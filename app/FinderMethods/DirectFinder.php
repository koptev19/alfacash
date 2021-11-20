<?php

namespace App\FinderMethods;

use App\Support\ExchangePath;

class DirectFinder extends BaseFinder
{

    /**
     * @param string $currencyIn
     * @param string $currencyOut
     *
     * @return ExchangePath
     */
    public function run(string $currencyIn, string $currencyOut): ExchangePath
    {
        $path = new ExchangePath;

        if(!empty($this->orderBooks[$currencyIn][$currencyOut]) && $this->orderBooks[$currencyIn][$currencyOut]->canExchange()) {
            $path->add($currencyIn);
            $path->add($currencyOut);
        }

        return $path;
    }

}