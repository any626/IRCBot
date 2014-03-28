<?php

abstract class Vote{

	protected $stream = null;
	private $voters = array();
	private $votes = null;

	abstract private function commands(){
	}

	abstract private function system(){
	}

	private function getLine(){
		return fgets($this->stream,1024);
	}
}
?>