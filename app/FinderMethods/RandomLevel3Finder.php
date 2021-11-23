<?php

namespace App\FinderMethods;

use App\Support\ExchangePath;
use Illuminate\Support\Collection;

class RandomLevel3Finder extends BaseFinder
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
        $path = null;

        foreach($this->orderBooks[$currencyIn] as $ticker1 => $orderBooks1) {
            if($ticker1 === $currencyOut) {
                continue;
            }

            foreach($this->orderBooks[$ticker1] as $ticker2 => $orderBooks2) {
                if($ticker2 === $currencyIn) {
                    continue;
                }

                if($ticker2 === $currencyOut) {
                    continue;
                }

                if(empty($this->orderBooks[$ticker2][$currencyOut])) {
                    continue;
                }

                if($this->inExcludedPaths($ticker1, $ticker2)) {
                    continue;
                }

                $path = new ExchangePath;

                $path->add($currencyIn);
                $path->add($ticker1);
                $path->add($ticker2);
                $path->add($currencyOut);

                break;
            }

            if($path) {
                break;
            }
        }

        return $path;
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
    protected function inExcludedPaths(string $ticker1, string $ticker2): bool
    {
        foreach($this->excludeExhangePaths ?: [] as $exchangePath) {
            if($exchangePath->get(1) === $ticker1 && $exchangePath->get(2) && $exchangePath->get(2) === $ticker2) {
                return true;
            }
        }

        return false;
    }

}