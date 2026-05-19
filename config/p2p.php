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

    /*
    | Vork platform fee charged to the employer on top of the worker's quote.
    | The worker still receives the full quote; the employer pays
    | quote * (1 + vork_fee_percentage/100). Initial and final payments are
    | split off the inflated total, not the raw quote.
    */
    'vork_fee_percentage' => env('P2P_VORK_FEE_PERCENTAGE', 1),
];

