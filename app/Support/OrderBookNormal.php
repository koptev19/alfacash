<?php

namespace App\Support;

class OrderBookNormal extends OrderBook
{
    /**
     * @return float
     */
    public function costOriginal(): float
    {
        return $this->bids[0][0];
    }

    /**
     * @return string
     */
    public function costOriginalText(): string
    {
        return $this->bids[0][0] . ' '. $this->tickerFrom . '/' . $this->tickerTo;
    }

    /**
     * @return float
     */
    public function amount(): float
    {
        return $this->bids[0][1];
    }

    /**
     * @param float $minus
     */
    public function decreaseAmount(float $minus)
    {
        $this->bids[0][1] -= $this->roundByLotStep($minus / $this->cost());

        if($this->bids[0][1] < 2*$this->lotStep) {
            array_shift($this->bids);
        }
    }

    /**
     * @param float $oldAmount
     *
     * @return float
     */
    public function getNewAmount(float $oldAmount): float
    {
        return min($this->amount(), $oldAmount) * $this->cost();
    }

    /**
     * @return bool
     */
    public function canExchange(): bool
    {
        return !empty($this->bids[0]);
    }

    /**
     * @param float $oldAmount
     *
     * @return float
     */
    public function decreasedAmount(float $amount): float
    {
        return $this->roundByLotStep($amount / $this->cost());
    }
}
