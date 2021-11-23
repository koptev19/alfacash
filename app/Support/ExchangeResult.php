<?php

namespace App\Support;

use Illuminate\Support\Collection;

class ExchangeResult extends Collection
{
    /**
     * @var float
     */
    public $startAmount;

    /**
     * @var float
     */
    public $finishAmount;

    /**
     * @var ExchangeStep
     */
    public $directExchange;

    /**
     * @var Collection
     */
    public $badResults;

    /**
     * @param string $tickerFrom
     * @param string $tickerTo
     * @param float $amountFrom
     * @param float $amountTo
     * @param float $comission
     *
     * @return void
     */
    public function addStep(string $tickerFrom, string $tickerTo, array $params)
    {
        $this->add(new ExchangeStep($tickerFrom, $tickerTo, $params));
    }

    /**
     * @return void
     */
    public function calculateStartAndFinishAmounts()
    {
        $this->startAmount = $this->first()->amountFrom;
        $this->finishAmount = $this->last()->amountTo;
    }

    /**
     * @param OrderBook|null $orderBook
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param float $amount
     *
     * @return void
     */
    public function addDirectExchange(?OrderBook $orderBook, string $currencyFrom, string $currencyTo, float $amount)
    {
        if($orderBook) {
            $amountTo = $orderBook->roundByLotStep($amount * $orderBook->cost());

            $this->directExchange = new ExchangeStep($currencyFrom, $currencyTo, [
                'amountFrom' => $amount,
                'amountTo' => $amountTo,
                'comission' => $orderBook->comission($amountTo),
                'price' => $orderBook->costOriginalText(),
            ]);
        }
    }

    /**
     * @param ExchangeResult $exchangeResult
     *
     * @return void
     */
    public function addBadResult(ExchangeResult $exchangeResult)
    {
        if(empty($this->badResults)) {
            $this->badResults = collect();
        }

        $this->badResults->add($exchangeResult);
    }

}
