<?php

session_start();

///*
ini_set('display_errors', 1);
error_reporting(E_ALL);
//*/

class LittlePoll {
	// the polls json
	private $polls;
		
	/* === <configuration> === */
	private $admin_user = "admin";
	private $admin_pass = "admin";
	private $admin_secret = "isuredolovelions";
	private $poll_file = "../polls.json";
	/* === </configuration> === */
	
	public function __construct() {
		$polls = array();
	}
	
	public function loadPolls() {
		// check to see if the file exists
		if (is_file($this->poll_file)) {
			$fh = fopen($this->poll_file, 'r');
			$poll_json = fread($fh, filesize($this->poll_file));
			fclose($fh);

			if ($poll_json == null || $poll_json == "")
				$poll_json = json_encode(array());
			
			if ($poll_json == false)
				return false;
				
			$this->polls = json_decode($poll_json);
			
			return true;
		}		
		return false;
	}
	
	private function savePolls() {
		$fh = fopen($this->poll_file, 'w');
		fwrite($fh, json_encode($this->polls));
		fclose($fh);
	}
	
	public function createPoll($name, $choices) {
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
	
	public function getPolls() {
		return $this->polls;
	}
	
	public function getPoll($id) {
		foreach ($this->polls as $poll)
			if ($poll->id == $id) return $p;
	}
	
	public function updatePoll($id, $name, $choices) {
		if ($this->checkAdminToken()) {
			// get a reference to the poll
			foreach ($this->polls as $p) {
				if ($p->id == $id) {
					$poll = $p; 
					break;
				}	
			}
			
			// change the name
			$poll->name = $name;
			
			// update the choices
			$new_choices = array();
			
			$i = 0;
			foreach ($choices as $c) {
				$nc = array();
				$nc['option'] = $c->option;
				$nc['image'] = $c->image;
				$nc['votes'] = $poll->choices[$i]->votes;
				if ($nc['votes'] == null) $nc['votes'] = 0;
				$new_choices[] = $nc;
				$i++;
			}
			
			$poll->choices = $new_choices;		
			
			$this->poll = $poll;
			$this->savePolls();
			return 1;
		}
		return 0;
	}
	
	public function deletePoll($id) {
		if ($this->checkAdminToken()) {
			// this isn't really the way to go about this, but it's okay for now
			$new_poll = array();			
			$i = 0;
			foreach ($this->polls as $poll)	{
				if ($poll->id != $id)
					$new_poll[] = $poll;
				$i++;
			}			
			$this->polls = $new_poll;			
			$this->savePolls();
			return 1;
		}		
		return 0;
	}
	
	public function vote($id, $choice) {
		if (!isset($_COOKIE['p'.$id])) {
			$i = 0;
			foreach ($this->polls as $p) {
				if ($p->id == $id) break;
				$i++;
			}
			
			$this->polls[$i]->total_votes++;
			$this->polls[$i]->choices[$choice]->votes++;
			$this->savePolls();
		}
	}
	
	public function resetVotes($id)	{
		if ($this->checkAdminToken()) {
			$i = 0;
			foreach ($this->polls as $p) {		
				if ($p->id == $id) {
					foreach ($this->polls[$i]->choices as $choice)
						$choice->votes = 0;
					break;
				}
				$i++;
			}			
			$this->savePolls();
			return 1;
		}
		return 0;
	}
	
	public function login($user, $pass)	{
		// please note that this isn't exactly the most secure thing in the world
		// but at the same time, there's no SQL to inject or anything…
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
	
	public function checkAdminToken() {
		if (isset($_SESSION['admin_token']) && isset($_SESSION['admin_user']) && isset($_SESSION['admin_pass']) && 
			$_SESSION['admin_user'] === md5($this->admin_user) && $_SESSION['admin_pass'] === md5($this->admin_pass)) {
			return $_SESSION['admin_token'] === sha1($_SESSION['admin_user'].$_SESSION['admin_pass'].$this->admin_secret);
		}
		return false;
	}
};

$little_poll = new LittlePoll();

?>
