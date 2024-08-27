<?php
$page_name = basename(__FILE__, '.php');
$cookie_name = "page" . $page_name . "_visits";
if (isset($_COOKIE[$cookie_name]))
{
    $visits = $_COOKIE[$cookie_name] + 1;
}
else
{
    $visits = 1;
}
setcookie($cookie_name, $visits, time() + 3600);
?>
<a href="index.php">Повернутися на index.php</a>