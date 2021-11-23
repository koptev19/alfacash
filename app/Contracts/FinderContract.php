<?php

namespace App\Contracts;

use App\Support\ExchangePath;
use Illuminate\Support\Collection;

interface FinderContract
{

    /**
     * @param Collection $orderBooks
     *
     * @return void
     */
    public function setOrderBooks(Collection $orderBooks);

    /**
     * @param string $currencyIn
     * @param string $currencyOut
     *
     * @return ExchangePath
     */
    public function run(string $currencyIn, string $currencyOut): ?ExchangePath;

}