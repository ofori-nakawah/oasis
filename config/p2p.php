<?php

return [
    /*
    |--------------------------------------------------------------------------
    | P2P Payment Configuration
    |--------------------------------------------------------------------------
    |
    | These values control the payment split for P2P (peer-to-peer) jobs.
    | The percentages should sum to 100.
    |
    */

    'initial_payment_percentage' => env('P2P_INITIAL_PAYMENT_PERCENTAGE', 10),
    'final_payment_percentage' => env('P2P_FINAL_PAYMENT_PERCENTAGE', 90),
];

