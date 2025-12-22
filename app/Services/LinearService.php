<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class LinearService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('linear.api_key');
        $this->baseUrl = config('linear.base_url', 'https://api.linear.app/graphql');
    }

    /**
     * Check if API key is configured
     *
     * @return bool
     */
    protected function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Create a Linear issue using GraphQL API
     *
     * @param string $teamId The Linear team ID (UUID)
     * @param string $title Issue title
     * @param string|null $description Issue description (optional)
     * @param array $additionalData Additional issue data (priority, labels, etc.)
     * @return array Returns issue data: id, identifier, url, title
     * @throws Exception
     *
     * Example response:
     * [
     *   'id' => 'uuid-here',
     *   'identifier' => 'VT-123',
     *   'url' => 'https://linear.app/vorktech/issue/VT-123/...',
     *   'title' => 'Issue Title'
     * ]
     */
    public function createIssue(
        string $teamId,
        string $title,
        ?string $description = null,
        array $additionalData = []
    ): array {
        if (!$this->isConfigured()) {
            throw new Exception('Linear API key is not configured. Please set LINEAR_API_KEY in your .env file.');
        }

        $mutation = <<<'GRAPHQL'
            mutation IssueCreate($input: IssueCreateInput!) {
                issueCreate(input: $input) {
                    success
                    issue {
                        id
                        identifier
                        url
                        title
                        description
                        state {
                            id
                            name
                        }
                        priority
                        createdAt
                    }
                }
            }
        GRAPHQL;

        // Validate teamId is a UUID before making the request
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $teamId)) {
            throw new Exception("Invalid team ID format. Expected UUID, got: {$teamId}");
        }

        // Get project ID from config if available
        $projectId = config('linear.transaction_issues_project_id');
        
        $inputData = [
            'teamId' => $teamId,
            'title' => $title,
        ];
        
        // Add project ID if configured
        if (!empty($projectId)) {
            $inputData['projectId'] = $projectId;
        }
        
        $variables = [
            'input' => array_merge($inputData, $additionalData),
        ];

        // Add description if provided
        if ($description !== null) {
            $variables['input']['description'] = $description;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => config('linear.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.linear.app/graphql', [
                'query' => $mutation,
                'variables' => $variables,
            ]);

            $responseData = $response->json();

            // Log full response for debugging
            Log::info('Linear issue creation response', [
                'status' => $response->status(),
                'response' => $responseData,
                'team_id' => $teamId,
            ]);

            // Check for HTTP errors
            if (!$response->successful()) {
                $errorMessage = $responseData['errors'][0]['message'] ?? 'Unknown error occurred';
                $errorDetails = $responseData['errors'][0] ?? [];
                Log::error('Linear API HTTP error', [
                    'status' => $response->status(),
                    'error' => $errorMessage,
                    'details' => $errorDetails,
                ]);
                throw new Exception("Linear API error: {$errorMessage}");
            }

            // Check for GraphQL errors
            if (isset($responseData['errors']) && !empty($responseData['errors'])) {
                $error = $responseData['errors'][0];
                $errorMessage = $error['message'] ?? 'GraphQL error occurred';
                $errorExtensions = $error['extensions'] ?? [];
                
                Log::error('Linear GraphQL error', [
                    'error' => $error,
                    'extensions' => $errorExtensions,
                ]);
                
                // Include more details if available
                if (isset($errorExtensions['userPresentableMessage'])) {
                    $errorMessage .= ': ' . $errorExtensions['userPresentableMessage'];
                }
                
                throw new Exception("Linear GraphQL error: {$errorMessage}");
            }

            // Check if issue creation was successful
            if (!isset($responseData['data']['issueCreate']['success']) || 
                !$responseData['data']['issueCreate']['success']) {
                throw new Exception('Failed to create Linear issue: Unknown error');
            }

            $issue = $responseData['data']['issueCreate']['issue'];

            Log::info('Linear issue created successfully', [
                'issue_id' => $issue['id'],
                'identifier' => $issue['identifier'],
                'title' => $issue['title'],
            ]);

            return [
                'id' => $issue['id'],
                'identifier' => $issue['identifier'],
                'url' => $issue['url'],
                'title' => $issue['title'],
                'description' => $issue['description'] ?? null,
                'state' => $issue['state']['name'] ?? null,
                'priority' => $issue['priority'] ?? null,
                'created_at' => $issue['createdAt'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Failed to create Linear issue', [
                'team_id' => $teamId,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get team ID by team key
     *
     * @param string $teamKey Team key (e.g., 'vorktech')
     * @return string|null Team UUID or null if not found
     * @throws Exception
     */
    public function getTeamId(string $teamKey): ?string
    {
        if (!$this->isConfigured()) {
            throw new Exception('Linear API key is not configured. Please set LINEAR_API_KEY in your .env file.');
        }

        $query = '
            query {
              teams {
                nodes {
                  id
                  key
                  name
                }
              }
            }
        ';

        try {
            $response = Http::withHeaders([
                'Authorization' => config('linear.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.linear.app/graphql', [
                'query' => $query,
            ]);

            $responseData = $response->json();

            // Log full response for debugging
            Log::info('Linear teams query response', [
                'team_key' => $teamKey,
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            if (isset($responseData['errors'])) {
                $errorMessage = $responseData['errors'][0]['message'] ?? 'Unknown error';
                Log::error('Linear GraphQL error', [
                    'errors' => $responseData['errors'],
                ]);
                throw new Exception('Failed to fetch teams: ' . $errorMessage);
            }

            if (!isset($responseData['data']['teams']['nodes'])) {
                Log::error('Unexpected Linear API response structure', [
                    'response' => $responseData,
                ]);
                throw new Exception('Unexpected response structure from Linear API');
            }

            $teams = $responseData['data']['teams']['nodes'] ?? [];
            
            Log::info('Linear teams found', [
                'team_key' => $teamKey,
                'teams_count' => count($teams),
                'team_keys' => array_column($teams, 'key'),
            ]);
            
            // Try exact match first (case-insensitive)
            foreach ($teams as $team) {
                if (strtolower($team['key']) === strtolower($teamKey)) {
                    $foundTeamId = $team['id'];
                    
                    // Validate it's a UUID
                    if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $foundTeamId)) {
                        Log::warning('Linear team ID is not a valid UUID', [
                            'team_key' => $teamKey,
                            'team_id' => $foundTeamId,
                        ]);
                        continue;
                    }
                    
                    Log::info('Linear team found', [
                        'team_key' => $teamKey,
                        'team_id' => $foundTeamId,
                        'team_name' => $team['name'],
                    ]);
                    return $foundTeamId;
                }
            }

            Log::warning('Linear team not found', [
                'team_key' => $teamKey,
                'available_teams' => array_map(function($team) {
                    return ['key' => $team['key'], 'name' => $team['name']];
                }, $teams),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Failed to get Linear team ID', [
                'team_key' => $teamKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}

