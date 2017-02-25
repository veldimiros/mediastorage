<?php

namespace AppBundle\Service;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Rabbit
{

    private $connection;
    private $channel;

    public function connect()
    {
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    }

    public function getChannel()
    {
        if (is_null($this->connection)) {
            $this->connect();
        }
        if (is_null($this->channel)) {
            $this->channel = $this->connection->channel();
        }

        return $this->channel;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Publishing message
     */
    public function send($array)
    {
        $json = json_encode($array);
        $properties = [
            'content_type' => 'application/json', // data type
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT, //save the message to disk
        ];

        $message = new AMQPMessage($json, $properties);
        $this->getChannel()->basic_publish($message, 'email');
    }

    /**
     * Declare  bindings
     */
    public function declareChannel()
    {
        //create an exchange with name 'email'
        $this->getChannel()->exchange_declare('email', 'direct', false, true, false);
        // create an queue with name 'email'
        $this->getChannel()->queue_declare('email', false, true, false, false, false);
        // creating bindings. Queue 'email' will receive messages from exchange 'email' 
        $this->getChannel()->queue_bind('email', 'email');
    }

}
