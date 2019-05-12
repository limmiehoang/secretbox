<?php
/**
 * Created by PhpStorm.
 * User: limmie
 * Date: 08/05/2019
 * Time: 18:08
 */

namespace SecretBox\Http\Controllers;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\RFC6455\Messaging\Frame;

class WebSocketController extends Controller implements MessageComponentInterface
{
    private $connections;
    public function __construct()
    {
        $this->connections = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $from->send(new Frame($msg, true, Frame::OP_BINARY));
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}