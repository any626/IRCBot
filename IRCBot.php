<?php

error_reporting(E_ALL);
set_time_limit(0);


class IRCBot{

    private $loop = true;
    private $stream = null; 

    private $info = array(
        "address" => "irc.internetbrands.com",
        "port" => 6667, 
        "user" => "kazaamthemagicalbot", 
        "hostname" => "www.github.com/any626", 
        "servername" => "any626", 
        "realname" => "tron", 
        "nick" => "kazaam",
        "channel" => "#ale");

    private $voters = null;

    public function __construct(){
        $this->stream = stream_socket_client("tcp://". $this->info['address']. ":" . $this->info['port'], $errno, $errstr, 10);
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
        $channel = fwrite($this->stream, "JOIN " . $this->info['channel']. "\r\n");
    }

    private function core(){
        //infinite loop
        while($this->loop){

            //gets lines in chat
            $line = fgets($this->stream, 1024);

            //checks for commands
            if(strpos($line,"PING") !== false){
                fwrite($this->stream, "PONG\r\n");
            } else if(strpos($line, "!say") !== false){
                fwrite($this->stream, "PRIVMSG " . $this->info['channel'] . " :whats up\r\n");
            } else if(strpos($line, "!quit") !== false){
                fwrite($this->stream, "QUIT\r\n");
                fclose($this->stream);
                $this->loop = false;
            } else if(strpos($line, "!random") !== false){
                $number = rand();
                fwrite($this->stream, "PRIVMSG " . $this->info['channel'] . " " . $number . "\r\n");
            }
            // need to add voting in
        }
    }

    public function getInfo(){
        return $this->info;
    }

}
?>