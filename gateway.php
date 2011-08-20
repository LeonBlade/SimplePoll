<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// require in our class
require_once('simplepoll.php');

// if we have an action passing through
if (isset($_GET['action'])) {
	
	// load the polls JSON file
	if ($simple_poll->loadPolls()) {
		
		// which action did we pass in
		switch ($_GET['action']) {
			
			// creating a poll
			case 'create':
				if (isset($_POST['name']) && isset($_POST['options']) && isset($_POST['end']))
					echo json_encode(array('success' => $simple_poll->createPoll($_POST['name'], $_POST['end'], json_decode($_POST['options']))));
				break;
		
			// getting polls either we are getting just one or all
			case 'get':
				if (isset($_GET['id']))
					echo json_encode($simple_poll->getPoll($_GET['id'])); 	// we are getting a specific poll
				else
					echo json_encode($simple_poll->getPolls()); 			// we just want to return all the polls
				break;
			
			// updating an existing poll
			case 'update':
				if (isset($_POST['id']) 		&& is_numeric($_POST['id']) 	&&
					isset($_POST['options']) 	&& is_string($_POST['options']) && 
					isset($_POST['name']) 		&& is_string($_POST['name']) 	&&
					isset($_POST['end'])		&& is_string($_POST['end'])) 	{
					$poll_id = $_POST['id'];
					$poll_name = $_POST['name'];
					$poll_options = $_POST['options'];
					$poll_end = $_POST['end'];
					echo json_encode(array('success' => $simple_poll->updatePoll($poll_id, $poll_name, $poll_end, json_decode($poll_options, true))));
				}
				break;
			
			// vote on a poll
			case 'vote':
				// make sure we are getting the proper data 
				if (isset($_GET['id']) 		&& is_numeric($_GET['id']) &&
					isset($_GET['choice']) 	&& is_numeric($_GET['choice']))	{
					// get the poll stuff
					$poll_id = $_GET['id'];
					$poll_option = $_GET['choice'];
					if (!isset($_COOKIE['p'.$poll_id]) || $_COOKIE['p'.$poll_id] != '1') {
						$simple_poll->vote($poll_id, $poll_option);
						echo json_encode(array('success' => 1));
						setcookie('p'.$poll_id, '1', time()*90, '/');
					}
					else echo json_encode(array('success' => 0));
				}
				break;
				
			// deleting polls
			case 'delete':
				if (isset($_GET['id']))
					echo json_encode(array('success' => $simple_poll->deletePoll($_GET['id'])));
				break;
				
			// resetting the poll count for a certain poll
			case 'reset':
				if (isset($_GET['id']))
					echo json_encode(array('success' => $simple_poll->resetVotes($_GET['id'])));
		}
			
	}
	else
		echo json_encode(array('success' => 0));
}

?>
