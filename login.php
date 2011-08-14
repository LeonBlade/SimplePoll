<?php

require_once 'inc/littlepoll.php';

if (isset($_POST['login']) && isset($_POST['user']) && isset($_POST['pass'])) {
	$little_poll->login($_POST['user'], $_POST['pass']);
}

if ($little_poll->checkAdminToken()) {
	header('Location: admin.php');
}

require_once 'templates/header.php';

?>

<div class="round_box">
	<form action="login.php" method="post">
		<input type="text" id="user" name="user" placeholder="username" />
		<input type="password" id="pass" name="pass" placeholder="password" />
		<input type="submit" id="login" name="login" value="Login" />
	</form>
</div>

<? require_once 'templates/header.php'; ?>

