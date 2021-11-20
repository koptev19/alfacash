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
     * @param string $tickerFrom
     * @param string $tickerTo
     * @param float $amountFrom
     * @param float $amountTo
     * @param float $comission
     *
     * @return void
     */
    public function addStep(string $tickerFrom, string $tickerTo, float $amountFrom, float $amountTo, float $comission)
    {
        $this->add(new ExchangeStep($tickerFrom, $tickerTo, $amountFrom, $amountTo, $comission));
    }

    /**
     * @return void
     */
    public function calculateStartAndFinishAmounts()
    {
        $this->startAmount = $this->first()->amountFrom;
        $this->finishAmount = $this->last()->amountTo;
    }

}
