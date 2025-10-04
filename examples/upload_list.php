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

$filename = $argv[1] ?? 'my-list.csv';
$fileType = pathinfo($filename, PATHINFO_EXTENSION) ?: 'csv';

$payload = [
    'filename' => $filename,
    'file_type' => $fileType,
    'total_chunk_count' => 1,
    'max_chunk_size' => 64000,
];

try {
    $startResponse = $client->startUpload($payload);
    print_r($startResponse);

    $uploadId = $startResponse['data']['upload_id'] ?? null;

    if (!$uploadId) {
        fwrite(STDERR, "Upload ID not returned by API.\n");
        exit(1);
    }

    $status = $client->getUploadStatus((int) $uploadId);
    print_r($status);
} catch (ApiException $exception) {
    fwrite(STDERR, 'Request failed: ' . $exception->getMessage() . PHP_EOL);
    if ($exception->getResponseBody()) {
        print_r($exception->getResponseBody());
    }
}
