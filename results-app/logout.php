<?php
// Clear auth cookies
setcookie("auth_user", "", time()-3600, "/");
setcookie("auth_name", "", time()-3600, "/");
setcookie("auth_status", "", time()-3600, "/");

// Redirect to login page
header('Location: http://localhost:8081/login.php');
exit;
?>