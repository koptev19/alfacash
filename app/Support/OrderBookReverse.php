<?php

namespace App\Support;

class OrderBookReverse extends OrderBook
{
    /**
     * @return float
     */
    public function costOriginal(): float
    {
        return 1 / $this->asks[0][0];
    }

    /**
     * @return float
     */
    public function amount(): float
    {
        return $this->asks[0][1];
    }

    /**
     * @param float $minus
     */
    public function decreaseAmount(float $minus)
    {
        $this->asks[0][1] -= $minus;

        if($this->asks[0][1] < pow(10, -7)) {
            array_shift($this->asks);
        }
    }

    /**
     * @param float $oldAmount
     *
     * @return float
     */
    public function getNewAmount(float $oldAmount): float
    {
        return min($this->amount(), $oldAmount * $this->cost());
    }

    /**
     * @return bool
     */
    public function canExchange(): bool
    {
        return !empty($this->asks[0]);
    }

}
