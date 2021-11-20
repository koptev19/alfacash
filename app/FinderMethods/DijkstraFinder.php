<?php

namespace App\FinderMethods;

use App\Support\TickerCollection;
use App\Support\ExchangePath;
use Exception;

class DijkstraFinder extends BaseFinder
{

    /**
     * @var TickerCollection
     */
    protected $tickerCollection;

    /**
     * @param string $currencyIn
     * @param string $currencyOut
     *
     * @return ExchangePath
     */
    public function run(string $currencyIn, string $currencyOut): ExchangePath
    {
        if(empty($this->orderBooks)) {
            throw new Exception('Не заданы цены');
        }

        $this->tickerCollection = new TickerCollection;

        $this->tickerCollection->add($currencyIn, [
            'amount' => 1,
            'priceFinaly' => !empty($this->orderBooks[$currencyIn][$currencyOut]) && $this->orderBooks[$currencyIn][$currencyOut]->canExchange()
                ? $this->orderBooks[$currencyIn][$currencyOut]->cost()
                : 0
        ]);

        while($currentTicker = $this->tickerCollection->current()) {
            foreach ($this->orderBooks[$currentTicker->ticker] as $ticker => $orderBook) {
                if($ticker === $currencyOut) {
                    continue;
                }

                if($this->tickerCollection->visited($ticker)) {
                    continue;
                }

                if(!$orderBook->canExchange()) {
                    continue;
                }

                $this->tickerCollection->addOrUpdateIfNeed(
                    $ticker,
                    $currentTicker->ticker,
                    $currentTicker->amount * $orderBook->cost(),
                    !empty($this->orderBooks[$ticker][$currencyOut]) ? $this->orderBooks[$ticker][$currencyOut]->cost() : 0
                );
            }

            $this->tickerCollection->visit($currentTicker->ticker);
        }

        $path = $this->bestPath();
        $path->add($currencyOut);

        return $path->values();
    }

    /**
     * @return ExchangePath
     */
    protected function bestPath(): ExchangePath
    {
        $path = new ExchangePath;

        $ticker = $this->tickerCollection->getTickerByMaxFinaly();

        while($ticker) {
            $path->add($ticker->ticker);

            $ticker = !empty($ticker->parentTicker)
                ? $this->tickerCollection->get($ticker->parentTicker)
                : null;
        }

        return $path->reverse();
    }

}