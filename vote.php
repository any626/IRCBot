<?php

abstract class Vote{

	protected $stream = null;
	private $voters = array();
	private $votes = null;

	abstract public function commands();

	abstract public function system();

	private function getLine(){
		return fgets($this->stream,1024);
	}
}
?>