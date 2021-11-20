<?php

namespace App\Contracts;

interface ExchangeSourceContract
{

    /**
     * @return array
     */
    public function load_markets();

    /**
     * @param string $symbol
     * @param int|null $limit
     * @param array $params
     *
     * @return array
     */
    public function fetch_order_book(string $symbol, ?int $limit = null, array $params = []);

}