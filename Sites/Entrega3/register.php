<?php
// Include config file
require_once 'config.php';
try {
  $db = new PDO("pgsql:dbname=".DATABASE.";host=".HOST.";port=".PORT.";user=".USER.";password=".PASSWORD);
  }
  catch(PDOException $e) {
  echo $e->getMessage();
  }

//working insert into query
//$query= "INSERT INTO usuarios(id, username, name, phone, country, gender, password) VALUES (929,'test','holda',1,'cl','M','sdfg')";
//$result = $db -> prepare($query);
//$result -> execute();

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        //$sql = "SELECT id FROM users WHERE username = ?";


        //if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
        //    mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $_POST["username"];
            $query= "SELECT usuarios.username FROM usuarios WHERE usuarios.username = '$param_username';";
            $searchresult = $db->query($query);
            $row = $searchresult->fetch();
            // Attempt to execute the prepared statement
            //if(mysqli_stmt_execute($stmt)){
                /* store result */
            //    mysqli_stmt_store_result($stmt);

              if (!is_bool($row)){
                  $username_err = "This username is already taken. ONE";
              } else{
                  echo $username;
                  $username = trim($_POST["username"]);
              }
          }
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
      $param_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
      $param_username = $username;
        // Prepare an insert statement
        if ($searchresult !== false){
            $username_err = "This username is already taken.";

        $query= "INSERT INTO usuarios(id, username, name, phone, country, gender, password)
        VALUES (200,'$username','NONAME',0,'NOCOUTNRY','NOGENDER','$param_password')";
        $result = $db -> prepare($query);
        $result -> execute();
        header("location: login.php");
        }
        //$result = $db -> prepare($sql);
        //$result -> execute();
        //header("location: login.php")

      //  if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
        //    mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            // Set parameters
            //$param_username = $username;
            //$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
        }
      }
      //else {
      //  echo "testing";
//        $query= "SELECT usuarios.username FROM usuarios WHERE usuarios.username = 'test';";
//      $searchresult = $db->query($query);
//    $row = $searchresult->fetch();
//    echo "$row";
//    if(is_bool($row)){
//      echo "FALSE! NO ENTRIES";
//    }
//       else {echo "TRUE! ENTRIES!";
//       };

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Ya tienes una cuenta? <a href="login.php">Login</a>.</p>
        </form>
    </div>
</body>
</html>
