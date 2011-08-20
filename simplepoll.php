<?php

// start the session
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

class SimplePoll {
	private $polls; // polls array

	private $admin_user = "admin"; // admin username change this
	private $admin_pass = "admin"; // admin password change this
	private $admin_secret = "e27be7eddc6b8a46e467a4425dbd161b"; // admin secret change this
	private $poll_file = "polls.json"; // where the poll file is located in relation to this file
	
	// the constructor is just simply initializing the polls array
	public function __construct() {
		$polls = array();
	}
	
	// loadPolls will load in the poll JSON file and store it in the polls array
	public function loadPolls($file = "") {
		// check to see if the file exists
		if ($file == '' || $file == null)
			$file = $this->poll_file;
		if (is_file($file)) {
			// read in the poll file
			$fh = fopen($file, 'r');
			$poll_json = fread($fh, filesize($file));
			fclose($fh);
	
			// if our poll JSON is null or blank we will create a new one
			if ($poll_json == null || $poll_json == "")
				$poll_json = json_encode(array());
			
			// if for some reason our poll JSON is false we will return false
			if ($poll_json == false)
				return false;
				
			// now store the polls array
			$this->polls = json_decode($poll_json, true);
			return true;
		}		
		return false;
	}
	
	// savePolls will save the poll back out when changes are made function is private as only functions should call it
	private function savePolls() {
		$fh = fopen($this->poll_file, 'w');
		fwrite($fh, json_encode($this->polls));
		fclose($fh);
	}
	
	// this will return the array ID of the poll based on the TSID
	// the TSID (timestamp ID) is used in case of ID overlap in which old polls
	// would be deleted and potential new polls would take their place causing updates
	// to occur on the wrong polls, and also getPoll(id) would pass back different polls depending
	// on the order of polls in the array
	// so by using a dynamic ID per poll that is stored as the timestamp for when it's created fixes this!
	private function getPollIDByTS($id) {
		foreach ($this->polls as $i => $p) {
			if ($p['id'] == $id) return $i;
		}
	}
	
	// createPoll passes in a name and a choices array
	public function createPoll($name, $choices) {
		// this function requires you to be an admin
		if ($this->checkAdminToken()) {
			// create a new array for choices
			$fchoices = array();
			
			// add a vote on the end
			foreach ($choices as $c) {
				$c = (array)$c;
				$c['votes'] = 0;
				$fchoices[] = $c;
			}
			
			// create the new poll array
			$new_poll = array(
				'id' => time(),
				'name' => $name,
				'total_votes' => 0,
				'choices' => $fchoices
			);
			
			// add the new poll
			$this->polls[] = $new_poll;
			
			// save the polls
			$this->savePolls();
			return 1;
		}
		return 0;
	}
	
	// this will simply return the entire polls array
	public function getPolls() {
		return $this->polls;
	}
	
	// this function will grab a specific poll based on its ID
	public function getPoll($id) {			
		return $this->polls[$this->getPollIDByTS($id)];
	}
	
	// updatePoll takes in an existing ID and the name and choices array again
	public function updatePoll($id, $name, $choices) {
		// this function requires you to be an admin
		if ($this->checkAdminToken()) {
			// grab the poll by ID
			$poll = $this->polls[$this->getPollIDByTS($id)];
	
			// change the name
			$poll['name'] = $name;
			
			// update the choices
			$new_choices = array();
			
			// this method of doing things is kind of annoying as it needs to be 
			// done recursively in order to update the options and ensure that none of their 
			// IDs interfere when removing/adding new options causing a vote count shift
			// basically we just loop through the choices and reset everything again
			// it's not that bad after all considering most polls wont have that many options anyways
			$i = 0;
			foreach ($choices as $c) {
				$nc = array();
				$nc['option'] = $c['option'];
				$nc['image'] = $c['image'];
				$nc['votes'] = $poll['choices'][$i]['votes'];
				if ($nc['votes'] == null) $nc['votes'] = 0;
				$new_choices[] = $nc;
				$i++;
			}
			
			// now we just pass in the new choices
			$poll['choices'] = $new_choices;		

			// and save etc
			$this->polls[$this->getPollIDByTS($id)] = $poll;
			$this->savePolls();
			return 1;
		}
		return 0;
	}
	
	// this used to be just like how the updatePoll function was and done recursively which could have been avoided
	// now it is avoided!  because IDs operate independently from their array placement we can do this
	public function deletePoll($id) {
		if ($this->checkAdminToken()) {
			unset($this->polls[$this->getPollIDByTS($id)]);
			$this->savePolls();
			return 1;
		}		
		return 0;
	}
	
	// this function wont be called if you've already voted based on a cookie
	// however people will always tinker around and try to force the vote anyways, so we double check the cookie
	// of course anyone could easily bypass this by clearing the cookie set for this particular poll and vote over
	// and over again, but we hope people wouldn't abuse the system that much
	// we could bypass this by logging IPs per poll but that would be too much work and take too long to parse though
	// a list of IPs to make sure someone didn't already vote
	// i may however look back and decide to improve this in the future we will see for now just cookies ;)
	public function vote($id, $choice) {
		if (!isset($_COOKIE['p'.$id])) {
			// simple enough, we just increate total_votes by one and then the specific vote by one as well
			$this->polls[$this->getPollIDByTS($id)]['total_votes']++;
			$this->polls[$this->getPollIDByTS($id)]['choices'][$choice]['votes']++;
			$this->savePolls();
		}
	}
	
	// resetting votes is something you wont do too often, but it's added just in case, there may be a problem on the
	// site letting people revote or maybe some scriptkiddie decided to make a little JS loop that votes a bunch of times
	// and ruins your poll, or maybe something drastic has changed and you just want to update the choices and reset votes
	// not a problem!
	public function resetVotes($id)	{
		// this function requires you to be an admin
		if ($this->checkAdminToken()) {		
			$poll_id = $this->getPollIDByTS($id);
			$poll = $this->polls[$poll_id];
			for ($i=0;$i<count($poll['choices']);$i++) {
				$poll['choices'][$i]['votes'] = 0;
			}
			$this->polls[$poll_id] = $poll;
			$this->polls[$poll_id]['total_votes'] = 0;
			$this->savePolls();
			return 1;
		}
		return 0;
	}
	
	// please note that this isn't exactly the most secure thing in the world
	// but at the same time, there's no SQL to inject or anything but it would be nice
	// to try to get as much protection as possible.  i would consider improving security more
	// if there was something huge to breach, but this is just a poll application and it's likely no one
	// will try to break it anyways
	public function login($user, $pass)	{
		// tripple equals will ensure that it's equal along with type
		if ($user === $this->admin_user && $pass === $this->admin_pass)	{
			// this is a pretty secure token for the most part, I doubt anyone will be able to force this...
			$_SESSION['admin_token'] = sha1(md5($user).md5($pass).$this->admin_secret);
			// we will also store  what they entered for username and password to check later
			// i will md5 them already simply so that if someone stumbles upon them somehow in the session variables
			// they wont know what the actual details are
			$_SESSION['admin_user'] = md5($user);
			$_SESSION['admin_pass'] = md5($pass);
		}
	}
	
	// this function is called any time we try to call a function that requires you to be an admin
	// it double checks the session admin_token to make sure they haven't tried to pull a sneaky snake on us
	// and force their way in.  it's pretty basic but also pretty secure for the most part
	// we first check to see if their session data is even there
	// we also will check to see if the admin user and the admin pass match each other
	// lastly we will recreate the admin token and see if they match each other and return the boolean of it
	public function checkAdminToken() {
		if (isset($_SESSION['admin_token']) && isset($_SESSION['admin_user']) && isset($_SESSION['admin_pass']) && 
			$_SESSION['admin_user'] === md5($this->admin_user) && $_SESSION['admin_pass'] === md5($this->admin_pass)) {
			return $_SESSION['admin_token'] === sha1($_SESSION['admin_user'].$_SESSION['admin_pass'].$this->admin_secret);
		}
		return false;
	}
};

// we create an instance of the class here so you can just use $simple_poll when you want to use it 
$simple_poll = new SimplePoll();

?>
