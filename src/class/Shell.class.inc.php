<?php
namespace ishibashiTokyo;

class Shell extends Commands
{
    public function SessionInit()
    {
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

    public function Download($path)
    {
        // ファイルに読み込み権限がない場合
        if (!is_readable($path)) {
            die($path);
        }

        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        if (!preg_match('/\A\S+?\/\S+/', $mimeType)) {
            $mimeType = 'application/octet-stream';
        }

        header('Content-Type: ' . $mimeType);
        header('X-Content-Type-Options: nosniff');
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Connection: close');
        while (ob_get_level()) { ob_end_clean(); }
        readfile($path);
        exit;
    }
}