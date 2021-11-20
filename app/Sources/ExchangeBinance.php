<?php

namespace App\Sources;

use App\Contracts\ExchangeSourceContract;

class ExchangeBinance implements ExchangeSourceContract
{

    /**
     * @var \ccxt\binance
     */
    protected $binance;

    /**
     * ExchangeBinance constructor
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->binance = new \ccxt\binance($params);
    }

    /**
     * @return array
     */
    public function load_markets()
    {
        return $this->binance->load_markets();
    }

    /**
     * @param string $symbol
     * @param int|null $limit
     * @param array $params
     *
     * @return array
     */
    public function fetch_order_book(string $symbol, ?int $limit = null, array $params = [])
    {
        return $this->binance->fetch_order_book($symbol, $limit, $params);

    }
}