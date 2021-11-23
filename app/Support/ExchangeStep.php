<?php

namespace App\Support;

use App\Models\Currency;
use Illuminate\Support\Collection;

class ExchangeStep
{
    /**
     * @var Collection
     */
    private static $currencies;

    /**
     * @var Currency
     */
    public $currencyFrom;

    /**
     * @var Currency
     */
    public $currencyTo;

    /**
     * @var float
     */
    public $amountFrom;

    /**
     * @var float
     */
    public $amountTo;

    /**
     * @var float
     */
    public $comission;

    /**
     * @var float
     */
    public $price;

    public function __construct(string $tickerFrom, string $tickerTo, array $params)
    {
        foreach($params as $key => $val) {
            $this->$key = $val;
        }
        $this->currencyFrom = self::getCurrency($tickerFrom);
        $this->currencyTo = self::getCurrency($tickerTo);
    }

    /**
     * @param string $ticker
     *
     * @return Currency
     */
    protected static function getCurrency(string $ticker): Currency
    {
        if(empty(self::$currencies)) {
            $currencies = Currency::all()->keyBy('ticker');
        }

        return $currencies[$ticker];
    }


}
