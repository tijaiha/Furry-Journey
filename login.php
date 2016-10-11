<?php
require_once 'core/init.php';
error_reporting(0);

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000,
    $params["path"], $params["domain"],
    $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

session_start();
if($_POST['formSubmit'] == "Submit") {
      // username and password sent from form 

   $auth = new DB;
   $user = $auth->login($_POST['username'], $_POST['password']);

   if (!$user) {
      $error = "Invalid Username or Password.";
   }

}
?>

<html>

<head>
   <title>Login Page</title>

   <style type = "text/css">
      body {
         font-family:Arial, Helvetica, sans-serif;
         font-size:14px;
      }

      label {
         font-weight:bold;
         width:100px;
         font-size:14px;
      }

      .box {
         border:#666666 solid 1px;
      }
   </style>

</head>

<body bgcolor = "#FFFFFF">
	
   <div align = "center">
      <div style = "width:300px; border: solid 1px #333333; " align = "left">
         <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Login</b></div>

         <div style = "margin:30px">

            <form action = "login.php" method = "post">
               <label>UserName  :</label><input type = "text" name = "username" class = "box"/><br /><br />
               <label>Password  :</label><input type = "password" name = "password" class = "box" /><br/><br />
               <input type="submit" name="formSubmit"/><br />
            </form>

            <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error;;?></div>

         </div>

      </div>

   </div>

</body>
</html>