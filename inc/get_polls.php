<?php

// require our little polls class
require_once 'littlepoll.php';

// load polls
$little_poll->loadPolls();

// return a JSON array of the polls
echo json_encode($little_poll->getPolls());

?>
