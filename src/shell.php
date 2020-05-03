<?php
/**
 * Toy Shell
 *
 * A simple web shell made in PHP.
 *
 * @since 1.0.3
 * @link  https://saku.fun/
 */
ini_set('log_errors', '0');
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();

require_once 'class/AccessRestrictions.class.inc.php';

require_once 'class/Commands.class.ext.php';
require_once 'class/Shell.class.inc.php';
include_once 'config/config.inc.php';

// Access Restrictions
ishibashiTokyo\AccessRestrictions::Setting($conf);
ishibashiTokyo\AccessRestrictions::IpRestriction();
ishibashiTokyo\AccessRestrictions::SimpleAuth();

// Initialize
$Shell = new ishibashiTokyo\Shell;
$Shell->SessionInit();

// file download
if (isset($_GET['download'])) {
    $Shell->Download($_GET['download']);
}

if (isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);

    // Screen reset.
    if ($cmd === 'clear') {
        $Shell->Cmd_clear();
        exit();
    }

    // Update current directory.
    if (preg_match("/^cd\s/i", $cmd) || preg_match("/^cd$/i", $cmd)) {
        $Shell->Cmd_cd($cmd);
        exit();
    }

    // Update current directory.
    if (preg_match("/^ll\s/i", $cmd) || preg_match("/^ll$/i", $cmd)) {
        $Shell->Cmd_ll($cmd);
        exit();
    }

    // Command execution and execution result recording.
    $Shell->Cmd_exec($cmd);
}

// View
require 'template/window.tpl.php';