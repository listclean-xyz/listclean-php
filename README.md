# Listclean PHP SDK

A beginner-friendly PHP SDK for the [Listclean API](https://api.listclean.xyz). The SDK wraps the official endpoints so you can verify emails, manage uploads, and monitor account details with simple PHP method calls.

## Requirements

- PHP 7.2 or higher
- cURL extension enabled (default for most PHP installations)
- A Listclean API key (available from your Listclean dashboard)

## Installation

Use [Composer](https://getcomposer.org/) to add the SDK to your project:

```bash
composer require listclean/listclean-php
```

If you are installing from source, make sure to include the Composer autoloader:

```php
require __DIR__ . '/vendor/autoload.php';
```

## Quick start

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Listclean\Listclean;

$client = new Listclean('your-api-key-here');

// Verify a single email address
$result = $client->verifyEmail('user@example.com');

if ($result['success'] ?? false) {
    $status = $result['data'][0]['status'] ?? 'unknown';
    echo "Status: {$status}" . PHP_EOL;
}
```

## API coverage

The SDK currently supports the following endpoints from the OpenAPI specification:

| Feature | Method | Endpoint |
| ------- | ------ | -------- |
| Verify a single email | `verifyEmail(string $email)` | `GET /verify/email/{email}` |
| Verify a batch of emails | `verifyEmailBatch(array $emails)` | `POST /verify/email/batch` |
| Fetch verification logs | `getVerificationLogs()` | `GET /verify/email/logs` |
| List uploads | `listUploads()` | `GET /uploads/` |
| Start an upload | `startUpload(array $payload)` | `POST /uploads/` |
| Upload a chunk | `uploadChunk(int $uploadId, array $payload)` | `POST /uploads/{upload_id}` |
| Get upload status | `getUploadStatus(int $uploadId)` | `GET /uploads/{upload_id}` |
| List all lists | `listLists()` | `GET /lists/` |
| Get a list | `getList(int $listId)` | `GET /lists/{list_id}` |
| Delete a list | `deleteList(int $listId)` | `DELETE /lists/{list_id}` |
| Download list as CSV | `downloadListCsv(int $listId, string $type, ?string $tokenOverride = null)` | `GET /downloads/{list_id}/{type}/` |
| Download list as JSON | `downloadListJson(int $listId, string $type, ?string $tokenOverride = null)` | `GET /downloads/json/{list_id}/{type}/` |
| Get account profile | `getProfile()` | `GET /account/profile/` |
| Update account profile | `updateProfile(array $payload)` | `POST /account/profile/` |
| Check credits | `getCredits()` | `GET /credits` |

## Handling errors

All methods can throw a `Listclean\Exceptions\ApiException` if something goes wrong (for example, invalid credentials or a malformed request). Catch this exception to inspect the HTTP status code and response body:

```php
use Listclean\Exceptions\ApiException;

try {
    $client->verifyEmail('user@example.com');
} catch (ApiException $exception) {
    echo 'Status code: ' . $exception->getStatusCode();
    print_r($exception->getResponseBody());
}
```

## Examples

The `examples/` directory contains small scripts that show how to:

- Verify a single email (`examples/verify_single.php`)
- Verify a batch of emails (`examples/verify_batch.php`)
- Start an upload and track its progress (`examples/upload_list.php`)

Run an example by setting your API key and executing it with PHP:

```bash
API_KEY="your-api-key" php examples/verify_single.php
```

Each example reads the API key from the `API_KEY` environment variable to keep secrets out of source control.

## Contributing

1. Fork the repository and create a feature branch.
2. Run `composer dump-autoload` after adding new classes.
3. Submit a pull request with a clear description of the changes.

Feel free to open issues for questions or feature requests!
