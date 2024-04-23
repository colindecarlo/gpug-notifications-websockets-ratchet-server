<?php

namespace MyApp;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{

    public function __construct(protected array $subscribedTopics = [])
    {
    }

    /**
     * @inheritDoc
     */
    #[\Override] function onOpen(ConnectionInterface $conn)
    {
        // TODO: Implement onOpen() method.
    }

    /**
     * @inheritDoc
     */
    #[\Override] function onClose(ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
    }

    /**
     * @inheritDoc
     */
    #[\Override] function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    /**
     * @inheritDoc
     */
    #[\Override] function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    /**
     * @inheritDoc
     */
    #[\Override] function onSubscribe(ConnectionInterface $conn, $topic)
    {
        $this->subscribedTopics[$topic->getId()] = $topic;

        echo "Subscribed to topic: {$topic->getId()}\n";
    }

    /**
     * @inheritDoc
     */
    #[\Override] function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        // TODO: Implement onUnSubscribe() method.
    }

    /**
     * @inheritDoc
     */
    #[\Override] function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        $conn->close();
    }

    public function onMessage($payload)
    {
        echo "Received message: $payload\n";

        $message = json_decode($payload, true);

        if (!array_key_exists($message['channel'], $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$message['channel']];

        if (! $topic) {
            return;
        }

        unset($message['channel']);
        $topic->broadcast($message);
    }
}