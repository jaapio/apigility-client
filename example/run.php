<?php

require __DIR__ . '/../vendor/autoload.php';
$httpClient = new \GuzzleHttp\Client([
    'base_uri' => 'http://172.17.0.3',
    ]
);

$client = new \PolderKnowledge\ApigilityClient\Client($httpClient);

$client->send(new \GuzzleHttp\Psr7\Request('POST', '/foo'));
