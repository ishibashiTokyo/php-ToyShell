<?php
namespace ishibashiTokyo;

class AccessRestrictions
{
    static $conf = array();

    static public function Setting($conf)
    {
        self::$conf = $conf;
    }

    static public function IpRestriction()
    {
        if (! self::$conf['IP_restriction']['valid']) {
            return;
        }

        if (! in_array($_SERVER['REMOTE_ADDR'], self::$conf['IP_restriction']['IPs'], true)) {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
    }

    // Authentication process
    static public function SimpleAuth()
    {
        if (! self::$conf['simple_auth']['valid']) {
            return;
        }

        if (isset($_POST['user']) && isset($_POST['passwd'])) {
            $_SESSION['webshell_auth'] = md5(trim($_POST['user'] . ':' . $_POST['passwd']));
        }

        $_hash = md5(self::$conf['simple_auth']['user'] . ':' . self::$conf['simple_auth']['password']);
        if (empty($_SESSION['webshell_auth']) || $_SESSION['webshell_auth'] !== $_hash) {
            include Path::file('template/login.tpl.php');
            exit();
        }
    }
}