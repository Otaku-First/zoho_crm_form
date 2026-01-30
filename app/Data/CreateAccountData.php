<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class CreateAccountData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $account_name,

        #[Required, Url]
        public string $website,

        #[Required, StringType]
        public string $phone,
    ) {}

    public function toZohoPayload(): array
    {
        return [
            'data' => [
                [
                    'Account_Name' => $this->account_name,
                    'Website' => $this->website,
                    'Phone' => $this->phone,
                ],
            ],
        ];
    }
}
