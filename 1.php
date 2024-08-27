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
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<a href="index.php">Повернутися на index.php</a>
</body>
</html>