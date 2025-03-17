<?php
// Save this as session.php and include it at the top of login.php and index.php
session_save_path('/var/lib/php/sessions');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Lax');
session_name('DEV_ANALYTICS_SESSION');
session_start();
?>