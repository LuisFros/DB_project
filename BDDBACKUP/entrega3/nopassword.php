<?php
//sistema de login basado en https://www.tutorialrepublic.com/php-tutorial/php-mysql-login-system.php
require_once 'psql-config2.php';
try {
  $db = new PDO("pgsql:dbname=".DATABASE2.";host=".HOST2.";port=".PORT2.";user=".USER2.";password=".PASSWORD2);
  }
  catch(PDOException $e) {
  echo $e->getMessage();
  }


$password_err = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
  session_start();
  if(empty(trim($_POST["password"]))){
      $password_err = "Please enter a password.";
  } else{
    $idquery = "SELECT MAX(usuarios.id) FROM usuarios";
    $searchresult2 = $db->query($idquery);
    $row3 = $searchresult2->fetch();
    $assigned_id = $row3['max'] + 1;

    $userid = $_SESSION['uid'];
    $password = $_POST["password"];
    $param_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user = $_SESSION['username'];

    $concatenated = $user._.$userid;
    $query= "INSERT INTO usuarios(id, username, name, phone, country, gender, password)
    VALUES ($assigned_id,'$concatenated','NONAME',0,'NOCOUTNRY','NOGENDER','$param_password')";
    $result = $db -> prepare($query);
    $result -> execute();
    header("location:login.php");
  }
}
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
        <h2>Password setting</h2>
        <p>Hemos detectado que tu usuario no cuenta con una contraseña. Por favor ingrésala aquí</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Cambiar contraseña">
            </div>
        </form>
    </div>
</body>
</html>
