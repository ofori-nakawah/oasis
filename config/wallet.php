<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Minimum Withdrawal Amount
    |--------------------------------------------------------------------------
    |
    | The minimum amount a user can withdraw from their wallet in GHS.
    |
    */
    'minimum_withdrawal' => env('WALLET_MINIMUM_WITHDRAWAL', 10),

    /*
    |--------------------------------------------------------------------------
    | Maximum Withdrawal Amount
    |--------------------------------------------------------------------------
    |
    | The maximum amount a user can withdraw per transaction in GHS.
    |
    */
    'maximum_withdrawal' => env('WALLET_MAXIMUM_WITHDRAWAL', 50000),
];

