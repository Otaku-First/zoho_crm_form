<?php

namespace App\Exceptions;

class ZohoTokenRefreshException extends ZohoException
{
    protected function getStatusCode(): int
    {
        return 401;
    }
}
