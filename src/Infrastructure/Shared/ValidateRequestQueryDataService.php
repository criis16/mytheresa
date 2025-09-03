<?php

namespace App\Infrastructure\Shared;

use App\Infrastructure\Shared\Exceptions\RequestException;

class ValidateRequestQueryDataService
{
    /**
     * Validates the request query input data
     *
     * @param array $request
     * @param array $requiredFields
     * @throws RequestException
     * @return void
     */
    public function validate(
        array $request,
        array $requiredFields
    ): void {
        $message = [];

        foreach ($requiredFields as $requiredField) {
            if (!\array_key_exists($requiredField, $request) || empty($request[$requiredField])) {
                $message[$requiredField] = \ucfirst($requiredField) . ' is a required field';
            }
        }

        if (!empty($message)) {
            throw new RequestException($message);
        }
    }
}
