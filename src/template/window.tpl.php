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
        // auto scroll
        var element = document.documentElement;
        var bottom = element.scrollHeight - element.clientHeight;
        window.scroll(0, bottom);

        // debuginfo
        <?php
        ob_start();
        echo '!POST' . PHP_EOL;
        var_dump($_POST);
        echo '!SESSION' . PHP_EOL;
        var_dump($_SESSION);
        $dump = ob_get_contents();
        ob_end_clean();
        $dump = str_replace('\'', '"', $dump);
        $dump = preg_replace("/\r\n|\r|\n/", '\n', $dump)
        ?>

        console.log('[debug]\n<?php echo $dump; ?>');
    </script>
</head>
<body>
<pre>
    ╔╦╗┌─┐┬ ┬  ╔═╗┬ ┬┌─┐┬  ┬
     ║ │ │└┬┘  ╚═╗├─┤├┤ │  │
     ╩ └─┘ ┴   ╚═╝┴ ┴└─┘┴─┘┴─┘
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