<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetLinearTeamId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'linear:get-team-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Linear team ID from Linear API';

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

        $this->info('Fetching teams from Linear...');

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.linear.app/graphql', [
            'query' => '
                query {
                  teams {
                    nodes {
                      id
                      key
                      name
                    }
                  }
                }
            ',
        ]);

        $responseData = $response->json();

        if (isset($responseData['errors'])) {
            $this->error('Error from Linear API:');
            $this->error(json_encode($responseData['errors'], JSON_PRETTY_PRINT));
            return 1;
        }

        if (!isset($responseData['data']['teams']['nodes'])) {
            $this->error('Unexpected response structure');
            $this->error(json_encode($responseData, JSON_PRETTY_PRINT));
            return 1;
        }

        $teams = $responseData['data']['teams']['nodes'];

        if (empty($teams)) {
            $this->warn('No teams found');
            return 0;
        }

        $this->info('Found ' . count($teams) . ' team(s):');
        $this->newLine();

        $headers = ['Team Key', 'Team Name', 'Team ID (UUID)'];
        $rows = [];

        foreach ($teams as $team) {
            $rows[] = [
                $team['key'],
                $team['name'],
                $team['id'],
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();

        // Find vorktech team if it exists
        $vorktechTeam = collect($teams)->firstWhere('key', 'vorktech');
        
        if ($vorktechTeam) {
            $this->info('Found vorktech team!');
            $this->info('Add this to your .env file:');
            $this->line('LINEAR_TEAM_ID=' . $vorktechTeam['id']);
        } else {
            $this->warn('vorktech team not found. Use one of the team IDs above.');
        }

        return 0;
    }
}
