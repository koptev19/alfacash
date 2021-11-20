<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ExchangeContract
{
    /**
     * @param string $source
     *
     * @return ExchangeContract
     */
    public function setSource(string $source): ExchangeContract;

    /**
     * @param string $source
     *
     * @return ExchangeContract
     */
    public function setMethod(string $method): ExchangeContract;

    /**
     * @param string $currencyIn
     * @param string $currencyOut
     * @param float $amount
     *
     * @return ExchangeContract
     */
    public function setExchangeData(string $currencyIn, string $currencyOut, float $amount): ExchangeContract;

    /**
     * @return Collection
     */
    public function findPaths(): Collection;


}