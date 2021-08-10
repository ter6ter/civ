<?php
require_once("includes.php");
session_start();
if (isset($_REQUEST['method'])) {
	$page = $_REQUEST['method'];
} else {
	$page = 'map';
}

$page = preg_replace('/[^a-z0-9_]+/', '', $page);
MyDB::start_transaction();
if ($page == 'login' && isset($_REQUEST['gid']) && isset($_REQUEST['uid'])) {
    $game = Game::get((int)$_REQUEST['gid']);
    if (!$game) {
        die('game error');
    }
    $user = User::get((int)$_REQUEST['uid']);
    if (!$user || $user->game != $game->id) {
        die('user error');
    }
    $_SESSION['game_id'] = $game->id;
    $_SESSION['user_id'] = $user->id;
    $page = 'map';
}
if (!file_exists("pages/{$page}.php")) {
	die('404 Not found');
}
$page_no_login = ['selectgame', 'creategame', 'gameinfo', 'login'];
if (isset($_SESSION['game_id'])) {
    $game = Game::get($_SESSION['game_id']);
    $user = User::get($_SESSION['user_id']);
} elseif (!in_array($page, $page_no_login)) {
    $page = 'selectgame';
}
$error = false;
$data = [];
include "pages/{$page}.php";
MyDB::end_transaction();

if (isset($_REQUEST['json'])) {
	if ($error) {
		$response = ['status' => 'error',
			         'error' => $error];
	} else {
		$response = ['status' => 'ok',
			         'data' => $data];
	}
	print json_encode($response);
} else {
	include "templ/{$page}.php";
}