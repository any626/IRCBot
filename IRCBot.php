<?php

error_reporting(E_ALL);
set_time_limit(0);


class IRCBot{

    private $stream = null; 

    private $info = array(
        "address" => "holmes.freenode.net",
        "port" => 6667, 
        "user" => "kazaamBot1111", 
        "hostname" => "www.github.com/any626", 
        "servername" => "any626", 
        "realname" => "tron", 
        "nick" => "kazaamBot1111",
        "channel" => "#ale213");

    private $voters = null;

    public function __construct(){

        $this->stream = stream_socket_client("tcp://". $this->info['address']. ":" . $this->info['port'], $errno, $errstr, 30);
        
        if(!$this->stream){
            echo "$errstr: $errno <br/>";
        } else {
            $this->login();
            $this->core();
        }

    }

    private function login(){

        $user = fwrite($this->stream, "USER " . $this->info['user'] . " " . $this->info['hostname'] . " " . $this->info['servername'] . " " .$this->info['realname']."\r\n");
        $nick = fwrite($this->stream, "NICK " . $this->info['nick'] . "\r\n");
        $channel = fwrite($this->stream, "JOIN " . $this->info['channel']. "\r\n");    }

    private function core(){
        //infinite loop
        while(true){

            //gets lines in chat
            $line = fgets($this->stream, 1024);

            //checks for commands
            if(strpos($line,"PING") !== false){
                fwrite($this->stream, "PONG\r\n");
            } else if(strpos($line, "!say") !== false){
                fwrite($this->stream, "PRIVMSG " . $this->info['channel'] . " :whats up\r\n");
            } else if(strpos($line, "!quit") !== false){
                fwrite($this->stream, "QUIT\r\n");
                exit;
            } else if(strpos($line, "!round") !== false){

            }

        }
    }

    private function voting(){
        

    }


}
$bot = new IRCBot();

?>