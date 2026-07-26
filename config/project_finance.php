<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fund transfer approval threshold
    |--------------------------------------------------------------------------
    | Transfers with amount greater than this value require workflow / approve
    | permission before balances are moved. Amounts at or below execute immediately.
    */
    'transfer_approval_threshold' => (float) env('FUND_TRANSFER_APPROVAL_THRESHOLD', 10000),
];
