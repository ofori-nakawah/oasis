<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linear API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Linear GraphQL API integration.
    | Get your API key from: https://linear.app/settings/api
    |
    */

    'api_key' => env('LINEAR_API_KEY'),
    
    'base_url' => env('LINEAR_API_URL', 'https://api.linear.app/graphql'),

    /*
    |--------------------------------------------------------------------------
    | OAuth Credentials (Optional - for future OAuth implementation)
    |--------------------------------------------------------------------------
    */
    'oauth' => [
        'client_id' => env('LINEAR_CLIENT_ID'),
        'client_secret' => env('LINEAR_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Team Configuration
    |--------------------------------------------------------------------------
    |
    | You can use either team_id (UUID) or team_key (string like 'vorktech')
    | If team_id is provided, it will be used directly.
    | Otherwise, team_key will be used to look up the team ID.
    |
    */
    'team_id' => env('LINEAR_TEAM_ID'),
    'team_key' => env('LINEAR_TEAM_KEY', 'vorktech'),
    
    /*
    |--------------------------------------------------------------------------
    | Project Configuration
    |--------------------------------------------------------------------------
    |
    | Project ID (UUID) for transaction issues
    | Use: php artisan linear:get-project-id to find project IDs
    |
    */
    'transaction_issues_project_id' => env('LINEAR_TRANSACTION_ISSUES_PROJECT_ID', 'f3da632f-c77c-40e3-8b15-7e8b54c9cb77'),
];

