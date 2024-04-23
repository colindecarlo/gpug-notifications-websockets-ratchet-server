<?php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server;

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL & ~E_USER_DEPRECATED & ~E_DEPRECATED);

$loop   = React\EventLoop\Factory::create();
$pusher = new \MyApp\Pusher();

$context = new \React\ZMQ\Context($loop);
$pull = $context->getSocket(\ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555');
$pull->on('message', [$pusher, 'onMessage']);

$webSock = new Server('0.0.0.0:8080', $loop);
$webServer = new IoServer(
    new HttpServer(
        new WsServer(
            new WampServer(
                $pusher
            )
        )
    ),
    $webSock
);

$loop->run();
