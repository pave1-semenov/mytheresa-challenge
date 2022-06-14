<?php

namespace Mytheresa\Challenge\Config;

class DefaultCurrencyConfig
{
    public function __construct(private readonly string $currency)
    {
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}