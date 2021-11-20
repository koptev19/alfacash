<?php

namespace App\Support;

use Illuminate\Support\Collection;

class TickerCollection
{
    /**
     * @var Collection
     */
    protected $items;

    /**
     * TickerCollection constructor
     */
    public function __construct()
    {
        $this->items = collect();
    }

    /**
     * @param string $ticker
     * @param array $params
     *
     * @return object
     */
    public function add(string $ticker, array $params)
    {
        if($this->items->has($ticker)) {
            return $this->items[$ticker];
        }

        $item = array_merge($params, [
            'ticker' => $ticker,
            'visited' => false,
            'finaly' => $this->calculateFinaly($params['amount'], $params['priceFinaly']),
        ]);

        $this->items[$ticker] = (object)$item;

        return $this->items[$ticker];
    }

    /**
     * @param string $ticker
     *
     * @return void
     */
    public function visit(string $ticker)
    {
        if($this->items->has($ticker)) {
            $this->items[$ticker]->visited = true;
        }
    }

    /**
     * @return object|null
     */
    public function current(): ?object
    {
        $tickers = $this->items->where('visited', false);

        if($tickers->isEmpty()) {
            return null;
        }

        $maxFinaly = $tickers->max('finaly');

        return $tickers->where('finaly', $maxFinaly)->first();
    }

    /**
     * @return bool
     */
    public function visited(string $ticker): bool
    {
        return $this->items->has($ticker) && $this->items[$ticker]->visited;
    }

    /**
     * @param string $ticker
     * @param string $parentTicker
     * @param float $amount
     * @param float $priceFinaly
     *
     * @return void
     */
    public function addOrUpdateIfNeed(string $ticker, string $parentTicker, float $amount, float $priceFinaly)
    {
        if($this->items->has($ticker)) {
            if($this->items[$ticker]->amount < $amount) {
                $this->items[$ticker]->amount = $amount;
                $this->items[$ticker]->priceFinaly = $priceFinaly;
                $this->items[$ticker]->finaly = $this->calculateFinaly($amount, $priceFinaly);
                $this->items[$ticker]->parentTicker = $parentTicker;
            }
        } else {
            $this->add($ticker, [
                'amount' => $amount,
                'priceFinaly' => $priceFinaly,
                'parentTicker' => $parentTicker
            ]);
        }
    }

    /**
     * @param float $amount
     * @param float $priceFinaly
     *
     * @return float
     */
    private function calculateFinaly(float $amount, float $priceFinaly): float
    {
        return $amount * $priceFinaly;
    }

    /**
     * @return Collection
     */
    public function items(): Collection
    {
        return $this->items;
    }

    /**
     * @param string $ticker
     *
     * @return object|null
     */
    public function get(string $ticker): ?object
    {
        return $this->items[$ticker] ?? null;
    }

    /**
     * @return object
     */
    public function getTickerByMaxFinaly(): ?object
    {
        return $this->items->where('finaly', $this->items->max('finaly'))->first();
    }

}