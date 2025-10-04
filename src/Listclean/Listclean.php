<?php

namespace Listclean;

use InvalidArgumentException;
use Listclean\Exceptions\ApiException;

class Listclean
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(string $apiKey, string $baseUrl = 'https://api.listclean.xyz/v1/')
    {
        $this->httpClient = new HttpClient($apiKey, $baseUrl);
    }

    /**
     * Verify a single email address.
     *
     * @throws ApiException
     */
    public function verifyEmail(string $email): array
    {
        if ($email === '') {
            throw new InvalidArgumentException('Email address must not be empty.');
        }

        $encodedEmail = rawurlencode($email);

        return $this->httpClient->request('GET', "verify/email/{$encodedEmail}");
    }

    /**
     * Verify a batch of email addresses (max 3000 emails).
     *
     * @param string[] $emails
     *
     * @throws ApiException
     */
    public function verifyEmailBatch(array $emails): array
    {
        $emails = array_values(array_filter($emails, function ($email) {
            return $email !== '';
        }));

        if (empty($emails)) {
            throw new InvalidArgumentException('The emails array must contain at least one address.');
        }

        if (count($emails) > 3000) {
            throw new InvalidArgumentException('The emails array cannot contain more than 3000 addresses.');
        }

        return $this->httpClient->request('POST', 'verify/email/batch', [], [
            'emails' => $emails,
        ]);
    }

    /**
     * Fetch logs for previous single email verifications.
     *
     * @throws ApiException
     */
    public function getVerificationLogs(): array
    {
        return $this->httpClient->request('GET', 'verify/email/logs');
    }

    /**
     * List uploads.
     *
     * @throws ApiException
     */
    public function listUploads(): array
    {
        return $this->httpClient->request('GET', 'uploads/');
    }

    /**
     * Start a new CSV upload.
     *
     * @param array $payload
     *
     * @throws ApiException
     */
    public function startUpload(array $payload): array
    {
        return $this->httpClient->request('POST', 'uploads/', [], $payload);
    }

    /**
     * Upload a chunk for an existing upload.
     *
     * @param int   $uploadId
     * @param array $payload
     *
     * @throws ApiException
     */
    public function uploadChunk(int $uploadId, array $payload): array
    {
        return $this->httpClient->request('POST', "uploads/{$uploadId}", [], $payload);
    }

    /**
     * Get the status of an upload.
     *
     * @throws ApiException
     */
    public function getUploadStatus(int $uploadId): array
    {
        return $this->httpClient->request('GET', "uploads/{$uploadId}");
    }

    /**
     * Fetch all lists.
     *
     * @throws ApiException
     */
    public function listLists(): array
    {
        return $this->httpClient->request('GET', 'lists/');
    }

    /**
     * Fetch a single list.
     *
     * @throws ApiException
     */
    public function getList(int $listId): array
    {
        return $this->httpClient->request('GET', "lists/{$listId}");
    }

    /**
     * Delete a list.
     *
     * @throws ApiException
     */
    public function deleteList(int $listId): array
    {
        return $this->httpClient->request('DELETE', "lists/{$listId}");
    }

    /**
     * Download list results as CSV.
     *
     * @throws ApiException
     */
    public function downloadListCsv(int $listId, string $type, ?string $tokenOverride = null): string
    {
        $type = $this->normalizeResultType($type);

        $query = [];
        if ($tokenOverride !== null) {
            $query['X-Auth-Token'] = $tokenOverride;
        }

        return $this->httpClient->request('GET', "downloads/{$listId}/{$type}/", $query, null, false);
    }

    /**
     * Download list results as JSON.
     *
     * @throws ApiException
     */
    public function downloadListJson(int $listId, string $type, ?string $tokenOverride = null): array
    {
        $type = $this->normalizeResultType($type);

        $query = [];
        if ($tokenOverride !== null) {
            $query['X-Auth-Token'] = $tokenOverride;
        }

        return $this->httpClient->request('GET', "downloads/json/{$listId}/{$type}/", $query);
    }

    /**
     * Retrieve account profile details.
     *
     * @throws ApiException
     */
    public function getProfile(): array
    {
        return $this->httpClient->request('GET', 'account/profile/');
    }

    /**
     * Update account profile details.
     *
     * @throws ApiException
     */
    public function updateProfile(array $payload): array
    {
        return $this->httpClient->request('POST', 'account/profile/', [], $payload);
    }

    /**
     * Fetch credit balance.
     *
     * @throws ApiException
     */
    public function getCredits(): array
    {
        return $this->httpClient->request('GET', 'credits');
    }

    private function normalizeResultType(string $type): string
    {
        $normalized = strtolower($type);
        $allowed = ['clean', 'dirty', 'unknown'];

        if (!in_array($normalized, $allowed, true)) {
            throw new InvalidArgumentException('Type must be one of: ' . implode(', ', $allowed));
        }

        return $normalized;
    }
}
