<?php

// require our little poll class
require_once 'littlepoll.php';

// make sure we are getting the proper data 
if (isset($_GET['id']) && is_numeric($_GET['id'])) {	
	// load the existing polls
	if ($little_poll->loadPolls())
		echo json_encode(array('success' => $little_poll->deletePoll($_GET['id']);));
	else 
		echo json_encode(array('success' => 0));
}

?>
