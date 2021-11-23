<?php

namespace App\FinderMethods;

use App\Support\ExchangePath;
use Illuminate\Support\Collection;

class RandomLevel2Finder extends BaseFinder
{

    /**
     * @var array
     */
    protected $excludeExhangePaths;

    /**
     * @param string $currencyIn
     * @param string $currencyOut
     *
     * @return ExchangePath
     */
    public function run(string $currencyIn, string $currencyOut): ?ExchangePath
    {
        foreach($this->orderBooks[$currencyIn] as $ticker => $orderBooks) {
            if($ticker === $currencyOut) {
                continue;
            }

            if($this->inExcludedPaths($ticker)) {
                continue;
            }

            if(empty($this->orderBooks[$ticker][$currencyOut])) {
                continue;
            }

            $path = new ExchangePath;

            $path->add($currencyIn);
            $path->add($ticker);
            $path->add($currencyOut);

            break;
        }

        return $path ?? null;
    }

    /**
     * @param array $excludeExhangePaths
     *
     * @return void
     */
    public function setExcludeExhangePaths(array $excludeExhangePaths)
    {
        $this->excludeExhangePaths = $excludeExhangePaths;

        return $this;
    }

    /**
     * @param string $ticker
     *
     * @return bool
     */
    protected function inExcludedPaths(string $ticker): bool
    {
        foreach($this->excludeExhangePaths ?: [] as $exchangePath) {
            if($exchangePath->get(1) === $ticker) {
                return true;
            }
        }

        return false;
    }

}