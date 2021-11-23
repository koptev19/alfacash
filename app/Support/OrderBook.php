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
     * @var float
     */
    protected $lotStep;

    /**
     * @var array
     */
    protected $bids;

    /**
     * @var array
     */
    protected $asks;

    public function __construct(string $tickerFrom, string $tickerTo, array $params)
    {
        $this->tickerFrom = $tickerFrom;
        $this->tickerTo = $tickerTo;
        $this->fee = $params['fee'] ?? 0;
        $this->lotStep = pow(10, -7);

        $info = $params['info'] ?? [];
        if(!empty($info['filters'])) {
            $filters = collect($info['filters']);
            $lotFilter = $filters->firstWhere('filterType', "LOT_SIZE");
            if(!empty($lotFilter)) {
                $this->lotStep = $lotFilter['stepSize'] ?? 0;
            }
        }
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
     * @param float $amount
     *
     * @return float
     */
    public function roundByLotStep(float $amount): float
    {
        return $amount;
        return floor($amount / $this->lotStep) * $this->lotStep;
    }

    /**
     * @return float
     */
    abstract public function costOriginal(): float;

    /**
     * @return string
     */
    abstract public function costOriginalText(): string;

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

    /**
     * @param float $oldAmount
     *
     * @return float
     */
    abstract public function decreasedAmount(float $amount): float;
}
