<?php
//sistema de login basado en https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
// Include config file
require_once 'psql-config2.php';
try {
  $db30 = new PDO("pgsql:dbname=".DATABASE2.";host=".HOST2.";port=".PORT2.";user=".USER2.";password=".PASSWORD2);
  }
  catch(PDOException $e) {
  echo $e->getMessage();
  }
  require_once 'psql-config.php';
try {
  $db1 = new PDO("pgsql:dbname=".DATABASE.";host=".HOST.";port=".PORT.";user=".USER.";password=".PASSWORD);
  }
  catch(PDOException $e) {
  echo $e->getMessage();
  }

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = 'Please enter username.';
    } else{
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = trim($_POST['password']);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $param_username = $_POST["username"];
        $query= "SELECT usuarios.username, usuarios.id FROM usuarios WHERE usuarios.username = '$param_username';";
        $searchresult = $db30->query($query);
        $row = $searchresult->fetch();


        //$query= "SELECT usuario.nombre FROM usuario WHERE usuario.nombre = '$param_username';";
        $query1= "SELECT usuario.email, usuario.uid FROM usuario WHERE usuario.email= '$param_username@uc.cl';";
        $searchresult1 = $db1->query($query1);
        $row1 = $searchresult1->fetch();

        // si lo encuetra directamente, limipio en la tabla g30

        if (!is_bool($row)) {
          $param_username = $_POST["username"];
          $query= "SELECT usuarios.password FROM usuarios WHERE usuarios.username = '$param_username';";
          $searchresult = $db30->query($query);
          $roww = $searchresult->fetch(PDO::FETCH_ASSOC);

          if(password_verify($_POST['password'],$roww['password'])){
            session_start();
            $_SESSION['username'] = $param_username;
            $_SESSION['uid'] = $row['id'];
            header("location:welcome.php");
          }
        }elseif(!is_bool($row1)){
          $param_username = $_POST["username"];
          // chequear si está "sucio" en la tabla g30
          $uid = $row1['uid'];
          $concateanted_uid = $param_username._.$uid;

          $query= "SELECT usuarios.username, usuarios.id, usuarios.password FROM usuarios WHERE usuarios.username = '$concateanted_uid';";
          $searchresult = $db30->query($query);
          $row = $searchresult->fetch();

          // si existe en g30 de manera sucia, login
          if(!is_bool($row)){
            if(password_verify($_POST['password'],$row['password'])){
              session_start();
              $_SESSION['username'] = $concateanted_uid;
              $_SESSION['uid'] = $row['id'];
              header("location:welcome.php");
            }
          //si solo existe en g1
          }else{
            $param_username = $_POST["username"];
            session_start();
            $_SESSION['username'] = $param_username;
            $_SESSION['uid'] = $row1['uid'];
            // Si se encuentra en la tabla del grupo1, lo mandamos a que haga set su password.
            header("location:nopassword.php");

          }
          }

           else { echo "Usuario no encontrado o contraseña incorrecta"; }
        }
//        if($stmt = mysqli_prepare($link, $sql)){
//            // Bind variables to the prepared statement as parameters
//            mysqli_stmt_bind_param($stmt, "s", $param_username);
//
            // Set parameters
//            $param_username = $username;

            // Attempt to execute the prepared statement
//            if(mysqli_stmt_execute($stmt)){
                // Store result
//                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
//                if(mysqli_stmt_num_rows($stmt) == 1){
                    // Bind result variables
//                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
//                    if(mysqli_stmt_fetch($stmt)){
//                        if(password_verify($password, $hashed_password)){
                            /* Password is correct, so start a new session and
                            save the username to the session */
//                            session_start();
//                            $_SESSION['username'] = $username;
//                            header("location: welcome.php");
//                        } else{
//                            // Display an error message if password is not valid
//                            $password_err = 'The password you entered was not valid.';
//                        }
//                    }
//                } else{
//                    // Display an error message if username doesn't exist
//                    $username_err = 'No account found with that username.';
//                }
//            } else{
//                echo "Oops! Something went wrong. Please try again later.";
//            }
        }

        // Close statement
//        mysqli_stmt_close($stmt);

    // Close connection
//    mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Por favor ingresa tus credenciales. Si tienes una cuenta con servidor g1 anterior, ingresa cualquier contraseña</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>No tienes una cuenta? <a href="register.php">Registro</a>.</p>
        </form>
    </div>
</body>
</html>
