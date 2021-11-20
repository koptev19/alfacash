<?php

namespace App\Support;

abstract class OrderBook
{
    /**
     * @var string
     */
    protected $tickerFrom;

    /**
     * @var string
     */
    protected $tickerTo;

    /**
     * @var float
     */
    protected $fee;

    /**
     * @var array
     */
    protected $bids;

    /**
     * @var array
     */
    protected $asks;

    public function __construct(string $tickerFrom, string $tickerTo, float $fee)
    {
        $this->tickerFrom = $tickerFrom;
        $this->tickerTo = $tickerTo;
        $this->fee = $fee;
    }

    /**
     * @param array $prices
     *
     * @return void
     */
    public function loadPrices(array $prices)
    {
        $this->bids = $prices['bids'];
        $this->asks = $prices['asks'];
    }

    /**
     * @return float
     */
    public function cost(): float
    {
        return $this->costOriginal() * (1 - $this->fee);
    }

    /**
     * @return float
     */
    public function costReverse(): float
    {
        return $this->costOriginal() * (1 + $this->fee);
    }

    /**
     * @param float $amount
     *
     * @return float
     */
    public function comission(float $amount): float
    {
        return $amount * $this->fee;
    }

    /**
     * @return float
     */
    abstract public function costOriginal(): float;

    /**
     * @return float
     */
    abstract public function amount(): float;

    /**
     * @param float $minus
     */
    abstract public function decreaseAmount(float $minus);

    /**
     * @param float $oldAmount
     *
     * @return float
     */
    abstract public function getNewAmount(float $oldAmount): float;

    /**
     * @return bool
     */
    abstract public function canExchange(): bool;
}
