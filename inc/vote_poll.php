<?php

// require our little poll class
require_once 'littlepoll.php';

// make sure we are getting the proper data 
if (isset($_GET['id']) 		&& is_numeric($_GET['id']) &&
	isset($_GET['choice']) 	&& is_numeric($_GET['choice']))
{
	// get the poll stuff
	$poll_id = $_GET['id'];
	$poll_option = $_GET['choice'];

	// load the existing polls
	if ($little_poll->loadPolls())
	{
		if (!isset($_COOKIE['p'.$poll_id]) || $_COOKIE['p'.$poll_id] != '1')
		{
			$little_poll->vote($poll_id, $poll_option);
			echo json_encode(array('success' => 1));
			setcookie('p'.$poll_id, '1', time()*90, '/');
		}
		else echo json_encode(array('success' => 0));
	}
	else 
		echo json_encode(array('success' => 0));
}

?>
