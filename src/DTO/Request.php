<?php

declare(strict_types=1);

namespace App\DTO;

readonly class Request
{
    public function __construct(
        public string $amount,
        public string $currencyCode
    ) {}
}