<?php
if (! isset($Shell)) {
    header("HTTP/1.0 404 Not Found");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Authentication</title>
</head>
<body>
<form method="POST">
    User: <input type="text" name="user"><br>
    Password: <input type="text" name="passwd"><br>
    <input type="submit" value="Login">
</form>
</body>
</html>