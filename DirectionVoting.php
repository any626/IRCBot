<?php

error_reporting(E_ALL);

require_once('vote.php');

class DirectionVoting extends Vote{

	protected $stream = null;
	private $data = null;
	private $numberOfVotes = 0;
	private $maxVotes = 0;
	private $winner = 0;
	private $votes = array(
		"up" => 0,
		"down" => 0,
		"left" => 0,
		"right" => 0);

	public function __construct($stream, $data){

		$this->stream = $stream;
		$this->data = $data;
		$this->system();
		$this->maxVotes = count($this->voters);
		fwrite($this->stream, "PRIVMSG #ale " . $this->maxVotes . "\r\n");
		$this->system();

		while(true){
			$run = $this->commands();
			if($run === 0){
				break;
			}
		}
	}

	public function commands(){
		$line = fgets($this->stream, 1024);
		if($this->numberOfVotes == $this->maxVotes){
			$this->setWinner();
			fwrite($this->stream, "PRIVMSG " . $this->data['channel'] . " :Voting has ended, the winner is " . $this->winner . "\r\n");
			return 0;
		}

		if(strpos($line,"PING") !== false){
			fwrite($this->stream, "PONG\r\n");
		} else if(strpos($line, "!up") !== false){
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $ip);
			if($this->voters["$ip[0]"] == 0){
				$this->voters["$ip[0]"] = 1;
				$this->votes["up"]++;
				$this->numberOfVotes++;
			}

		} else if(strpos($line, "!down") !== false){
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $ip);
			if($this->voters["$ip[0]"] == 0){
				$this->voters["$ip[0]"] = 1;
				$this->votes["down"]++;
				$this->numberOfVotes++;
			}

		} else if(strpos($line, "!left") !== false){
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $ip);
			if($this->voters["$ip[0]"] == 0){
				$this->voters["$ip[0]"] = 1;
				$this->votes["left"]++;
				$this->numberOfVotes++;
				fwrite($this->stream, "PRIVMSG " . $this->data['channel'] . " :A Vote Left\r\n");
			}
		} else if(strpos($line, "!right") !== false){
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $ip);
			if($this->voters["$ip[0]"] == 0){
				$this->voters["$ip[0]"] = 1;
				$this->votes["right"]++;
				$this->numberOfVotes++;
			}

		} else if (strpos($line, "!early") !== false){
			$this->setWinner();
			fwrite($this->stream, "PRIVMSG " . $this->data['channel'] . " :Voting has ended, the winner is " . $this->winner . "\r\n");
			return 0;
		}
	}

	public function system(){
		fwrite($this->stream, "WHO " . $this->data['channel'] . "\r\n");
		$line = null;
		$pattern = "/:End of WHO list/";
		do{
			$line = fgets($this->stream, 1024);
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $ip);
			//$ip[0] is the first ip address, users typing ip addresses in chat should affect the voters
			$this->voters[$ip[0]]=0; 
		}while(strpos($line, $pattern) === false);
	}

	public function setWinner(){
		$max = 0;
		$winner = null;
		foreach ($this->votes as $key => $value) {
			if($value > $max){
				$max = $value;
				$winner = $key;
			}
		}
		$this->winner = $winner;
	}

	
}

?>