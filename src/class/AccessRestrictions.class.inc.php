<?php
namespace ishibashiTokyo;

class AccessRestrictions
{
    static $conf = array();

    function __construct($conf)
    {
        self::$conf = $conf;
    }

    static public function IpRestriction(): void
    {
        if (! self::$conf['IP_restriction']['valid']) {
            return;
        }

        if ( array_search($_SERVER['REMOTE_ADDR'], self::$conf['IP_restriction']['IPs']) === false) {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
    }

    // Authentication process
    static public function SimpleAuth(): void
    {
        if (! self::$conf['simple_auth']['valid']) {
            return;
        }

        if (isset($_POST['user']) && isset($_POST['passwd'])) {
            $_SESSION['webshell_auth'] = md5(trim($_POST['user'] . ':' . $_POST['passwd']));
        }

        if (empty($_SESSION['webshell_auth'])
            || $_SESSION['webshell_auth'] !== md5(self::$conf['simple_auth']['user'] . ':' . self::$conf['simple_auth']['password'])) {
            require BASE_PATH . '/template/login.tpl.php';
            exit();
        }
    }
}