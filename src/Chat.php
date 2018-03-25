<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $DB;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->DB = new DataBase();
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->checkDB();
        $this->clients->attach($conn);
        $this->DB->putQuery("INSERT INTO `users` (name,res_id,last) VALUES('anonim','".$conn->resourceId."','')");

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msg = json_decode($msg,true);
        $this->checkDB();
        if(empty($msg['hash'])){
            if(empty($msg['name'])){
                $from->send('error');
            }else{
                $tempStrQuery = "UPDATE `users` SET name = '".$msg['name']."',last='".md5($msg['name'].time())."' WHERE res_id = '".$from->resourceId."'";
               $this->DB->putQuery($tempStrQuery); 
            }
        }else{
            // проверка на адекватность хэша
            
            if(!empty($msg['to'])){
                $curId = $this->DB->getRow("SELECT res_id FROM `users` WHERE name = '".$msg['to']."'");
                var_export($curId['res_id']);
                foreach ($this->clients as $client) {
                    if($client->resourceId == $curId['res_id']){
                        $client->send($msg['text']);
                    }
                }                
            }
            if(!empty($msg['to_group'])){
                foreach ($this->clients as $client) {
                    if($client->resourceId != $from->resourceId){
                        $client->send($msg['text']);
                    }
                } 
            }
            if(!empty($msg['all'])){
                foreach ($this->clients as $client) {
                    if ($from !== $client) {             
                        $client->send($msg['text']);
                          }
                      }
            }
//            if(!empty($msg['hash'])){
//                
//            }
            
        }
//        $numRecv = count($this->clients) - 1;
//        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
//            , $from->resourceId, $msg['name'], $numRecv, $numRecv == 1 ? '' : 's');
//        echo $msg['to'];
//        foreach ($this->clients as $client) {
//            if ($from !== $client) {
//                // The sender is not the receiver, send to each client connected
//                $client->send($msg['name']);
//            }
//            if($client->resourceId == $msg['to']){
//                $client->send($msg['text']);
//            }
//        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        $this->checkDB();
        $this->DB->execQuery("DELETE FROM `users` WHERE `res_id`= ".$conn->resourceId);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
    
    public function checkDB(){
        if(!$this->DB->isConnect()){
            unset($this->DB);
            $this->DB = new DataBase();
            echo "reconnect\n";
        }
    }
}