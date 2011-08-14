<?php

// require our little poll class
require_once 'littlepoll.php';

// make sure we are getting the proper data 
if (isset($_POST['id']) 		&& is_numeric($_POST['id']) 	&&
	isset($_POST['options']) 	&& is_string($_POST['options']) && 
	isset($_POST['name']) 		&& is_string($_POST['name'])) 	{
	// get the poll stuff
	$poll_id = $_POST['id'];
	$poll_name = $_POST['name'];
	$poll_options = $_POST['options'];
	
	// load the existing polls
	if ($little_poll->loadPolls())
		echo json_encode(array('success' => $little_poll->updatePoll($poll_id, $poll_name, json_decode($poll_options))));
	else	
		echo json_encode(array('success' => 0));
}

?>
