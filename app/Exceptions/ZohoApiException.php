<?php

namespace App\Exceptions;

class ZohoApiException extends ZohoException
{
    protected function getStatusCode(): int
    {
        return 502;
    }
}
