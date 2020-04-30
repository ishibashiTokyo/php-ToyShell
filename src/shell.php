<?php
/**
 * Toy Shell
 *
 * A simple web shell made in PHP.
 *
 * @since 1.0.1
 * @link  https://saku.fun/
 */
ini_set('log_errors', '0');
ini_set('display_errors', '1');
error_reporting(E_ALL);
session_start();

require_once 'class/AccessRestrictions.class.inc.php';
require_once 'class/Shell.class.inc.php';
include_once 'config/config.inc.php';

$Shell = new ishibashiTokyo\Shell;
$AR = new ishibashiTokyo\AccessRestrictions($conf);

$AR::IpRestriction();
$AR::SimpleAuth();

// Initialize
$Shell->SessionInit();

if (isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);

    // Screen reset.
    if ($cmd === 'clear') {
        $Shell->Cmd_clear();
        exit();
    }

    // Update current directory.
    if (preg_match("/^cd\s/i", $cmd)) {
        $Shell->Cmd_cd($cmd);
        exit();
    }

    // Command execution and execution result recording.
    $Shell->Cmd_exec($cmd);
}

// View
require 'template/window.tpl.php';