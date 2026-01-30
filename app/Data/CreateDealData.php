<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CreateDealData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $deal_name,

        #[Required, In([
            'Qualification',
            'Needs Analysis',
            'Value Proposition',
            'Identify Decision Makers',
            'Proposal/Price Quote',
            'Negotiation/Review',
            'Closed Won',
            'Closed Lost',
        ])]
        public string $stage,
    ) {}

    public function toZohoPayload(string $accountId): array
    {
        return [
            'data' => [
                [
                    'Deal_Name' => $this->deal_name,
                    'Stage' => $this->stage,
                    'Account_Name' => [
                        'id' => $accountId,
                    ],
                ],
            ],
        ];
    }
}
