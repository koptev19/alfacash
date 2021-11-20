<?php

namespace App\FinderMethods;

use App\Contracts\FinderContract;
use App\Support\ExchangePath;
use Illuminate\Support\Collection;

abstract class BaseFinder implements FinderContract
{
    /**
     * @var Collection
     */
    protected $orderBooks;

    /**
     * @param Collection $orderBooks
     *
     * @return BasePathFinder
     */
    public function setOrderBooks(Collection $orderBooks): BaseFinder
    {
        $this->orderBooks = $orderBooks;

        return $this;
    }

    /**
     * @param string $currencyIn
     * @param string $currencyOut
     *
     * @return ExchangePath
     */
    abstract public function run(string $currencyIn, string $currencyOut): ExchangePath;

}