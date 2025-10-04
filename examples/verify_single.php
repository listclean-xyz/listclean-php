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
$email = $argv[1] ?? 'user@example.com';

try {
    $response = $client->verifyEmail($email);
    print_r($response);
} catch (ApiException $exception) {
    fwrite(STDERR, 'Request failed: ' . $exception->getMessage() . PHP_EOL);
    if ($exception->getResponseBody()) {
        print_r($exception->getResponseBody());
    }
}
