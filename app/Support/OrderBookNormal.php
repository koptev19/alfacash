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
     * @return float
     */
    public function amount(): float
    {
        if(empty($this->bids[0]))
        {
            dd($this);
        }
        return $this->bids[0][1];
    }

    /**
     * @param float $minus
     */
    public function decreaseAmount(float $minus)
    {
        $this->bids[0][1] -= $minus / $this->cost();

        if($this->bids[0][1] < pow(10, -7)) {
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
}
