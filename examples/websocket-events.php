<?php

/**
 * Get all updates from MadelineProto EventHandler running inside TelegramApiServer via websocket
 * @see \TelegramApiServer\Controllers\EventsController
 */

require 'vendor/autoload.php';

use Amp\Websocket\Client\Connection;
use Amp\Websocket\Message;
use function Amp\Websocket\Client\connect;

$shortopts = 'u::';
$longopts = [
    'url::',
];
$options = getopt($shortopts, $longopts);
$options = [
    'url' => $options['url'] ?? $options['u'] ?? 'ws://127.0.0.1:9503/events',
];

Amp\Loop::run(function () use($options) {
    echo "Connecting to: {$options['url']}" . PHP_EOL;

    /** @var Connection $connection */
    $connection = yield connect($options['url']);

    echo 'Waiting for events...' . PHP_EOL;
    while ($message = yield $connection->receive()) {
        /** @var Message $message */
        $payload = yield $message->buffer();
        printf("Received event: %s\n", $payload);
    }
});