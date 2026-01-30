<?php

namespace App\Exceptions;

class ZohoAuthenticationException extends ZohoException
{
    protected function getStatusCode(): int
    {
        return 401;
    }
}
