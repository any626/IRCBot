<?php

require_once('vote.php');

class DirectionVoting extends Vote{

	private $data = null;
	private $numberOfVotes = 0;
	private $maxVotes = 0;
	private $winner = null;
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

		while(true){
			$this->getLine();

		}
	}

	private function commands(){
		$line = $this->getLine();
		if($this->numberOfVotes == $this->maxVotes){
			fwrite($this->stream, "PRIVMSG " . $this->data['channel'] . " Voting has ended, the winner is $winner");
			break 2;
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
			}

		} else if(strpos($line, "!right") !== false){
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $ip);
			if($this->voters["$ip[0]"] == 0){
				$this->voters["$ip[0]"] = 1;
				$this->votes["right"]++;
				$this->numberOfVotes++;
			}

		} else if (strpos($line, "!early") !== false){
			fwrite($this->stream, "PRIVMSG " . $this->data['channel'] . " Voting has ended, the winner is $winner");
			break 2;
		}
	}

	private function system(){
		fwrite($this->stream, "WHO #ale");
		do{
			$line = fgets($this->stream, 1024);
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $ip);
			//$ip[0] is the first ip address, users typing ip addresses in chat should affect the voters
			$this->voters["$ip[0]"]=0; 
			$pattern = "/" . $this->data['channel'] . " :End of WHO list/";
			preg_match($pattern, $line, $end);
		}while($end[0] != false);
	}

	
}

?>