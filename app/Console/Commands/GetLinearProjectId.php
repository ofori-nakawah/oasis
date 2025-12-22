<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetLinearProjectId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'linear:get-project-id {--team-id= : Optional team ID to filter projects}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Linear project IDs from Linear API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $apiKey = config('linear.api_key');
        
        if (empty($apiKey)) {
            $this->error('LINEAR_API_KEY is not set in your .env file');
            return 1;
        }

        $teamId = $this->option('team-id') ?: config('linear.team_id');
        
        if (empty($teamId)) {
            $this->error('LINEAR_TEAM_ID is not set in your .env file. Use --team-id option or set LINEAR_TEAM_ID in .env');
            return 1;
        }

        $this->info('Fetching projects from Linear...');

        $query = '
            query Projects($teamId: String!) {
              team(id: $teamId) {
                projects {
                  nodes {
                    id
                    name
                    description
                  }
                }
              }
            }
        ';

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.linear.app/graphql', [
            'query' => $query,
            'variables' => [
                'teamId' => $teamId,
            ],
        ]);

        $responseData = $response->json();

        if (isset($responseData['errors'])) {
            $this->error('Error from Linear API:');
            $this->error(json_encode($responseData['errors'], JSON_PRETTY_PRINT));
            return 1;
        }

        if (!isset($responseData['data']['team']['projects']['nodes'])) {
            $this->error('Unexpected response structure');
            $this->error(json_encode($responseData, JSON_PRETTY_PRINT));
            return 1;
        }

        $projects = $responseData['data']['team']['projects']['nodes'];

        if (empty($projects)) {
            $this->warn('No projects found for this team');
            return 0;
        }

        $this->info('Found ' . count($projects) . ' project(s):');
        $this->newLine();

        $headers = ['Project Name', 'Project ID', 'Description'];
        $rows = [];

        foreach ($projects as $project) {
            $rows[] = [
                $project['name'],
                $project['id'],
                substr($project['description'] ?? 'N/A', 0, 50),
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();

        // Find user-transaction-issues project if it exists
        $transactionProject = collect($projects)->first(function ($project) {
            return str_contains(strtolower($project['name'] ?? ''), 'transaction') || 
                   str_contains(strtolower($project['description'] ?? ''), 'transaction');
        });
        
        if ($transactionProject) {
            $this->info('Found transaction-related project!');
            $this->info('Add this to your .env file:');
            $this->line('LINEAR_TRANSACTION_ISSUES_PROJECT_ID=' . $transactionProject['id']);
        } else {
            $this->warn('No transaction-related project found. Use one of the project IDs above.');
        }

        return 0;
    }
}
