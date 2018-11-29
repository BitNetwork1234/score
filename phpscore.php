<?php
// Include config file
require_once 'config.php';
 
// Define variables and initialize with empty values
$username = $score = $confirm_score = "";
$username_err = $score_err = $confirm_score_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST['score']))){
        $score_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST['score'])) < 6){
        $score_err = "Score must be valid.";
    } else{
        $score = trim($_POST['score']);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_score"]))){
        $confirm_score_err = 'Please confirm score.';     
    } else{
        $confirm_score = trim($_POST['confirm_score']);
        if($score != $confirm_score){
            $confirm_score_err = 'score did not match.';
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($score_err) && empty($confirm_score_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO score (username, score) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_score);
            
            // Set parameters
            $param_username = $username;
            $param_score = score_hash($score, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: score.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>score</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($score_err)) ? 'has-error' : ''; ?>">
                <label>Score</label>
                <input type="score" name="score" class="form-control" value="<?php echo $score; ?>">
                <span class="help-block"><?php echo $score_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_score_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Score</label>
                <input type="score" name="confirm_score" class="form-control" value="<?php echo $confirm_score; ?>">
                <span class="help-block"><?php echo $confirm_score_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an score? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>