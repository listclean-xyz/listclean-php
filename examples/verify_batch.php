<?php

require __DIR__ . '/../vendor/autoload.php';

use Listclean\Exceptions\ApiException;
use Listclean\Listclean;

$apiKey = getenv('API_KEY');

if (!$apiKey) {
    fwrite(STDERR, "Please set the API_KEY environment variable.\n");
    exit(1);
}

$client = new Listclean($apiKey);
$emails = array_slice($argv, 1);

if (empty($emails)) {
    $emails = ['user1@example.com', 'user2@example.com'];
}

try {
    $response = $client->verifyEmailBatch($emails);
    print_r($response);
} catch (ApiException $exception) {
    fwrite(STDERR, 'Request failed: ' . $exception->getMessage() . PHP_EOL);
    if ($exception->getResponseBody()) {
        print_r($exception->getResponseBody());
    }
}
