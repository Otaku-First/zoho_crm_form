<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ZohoException extends Exception implements Responsable
{
    public function toResponse($request): Response
    {
        $statusCode = $this->getStatusCode();

        if ($request->header('X-Inertia')) {
            return back()->with('error', $this->getMessage())->toResponse($request);
        }

        return response()->json([
            'message' => $this->getMessage(),
        ], $statusCode);
    }

    protected function getStatusCode(): int
    {
        return 500;
    }
}
