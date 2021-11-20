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

    public function __construct(string $tickerFrom, string $tickerTo, float $amountFrom, float $amountTo, float $comission)
    {
        $this->currencyFrom = self::getCurrency($tickerFrom);
        $this->currencyTo = self::getCurrency($tickerTo);
        $this->amountFrom = $amountFrom;
        $this->amountTo = $amountTo;
        $this->comission = $comission;
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
