<?php

namespace App\Services;

use App\Contracts\ExchangeContract;
use App\Contracts\ExchangeSourceContract;
use App\FinderMethods\BaseFinder;
use App\FinderMethods\RandomLevel2Finder;
use App\FinderMethods\RandomLevel3Finder;
use App\Models\Currency;
use App\Support\OrderBookNormal;
use App\Support\OrderBookReverse;
use App\Support\ExchangePath;
use App\Support\ExchangeResult;
use Illuminate\Support\Collection;

class ExchangeService implements ExchangeContract
{

    /**
     * @var ExchangeSourceContract
     */
    protected $exchange;

    /**
     * @var Collection
     */
    protected $orderBooks;

    /**
     * @var string
     */
    protected $currencyIn;

    /**
     * @var string
     */
    protected $currencyOut;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $method;

    /**
     * @param string $source
     *
     * @return ExchangeContract
     */
    public function setSource(string $source): ExchangeContract
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @param string $method
     *
     * @return ExchangeContract
     */
    public function setMethod(string $method): ExchangeContract
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param string $currencyIn
     * @param string $currencyOut
     * @param float $amount
     *
     * @return ExchangeContract
     */
    public function setExchangeData(string $currencyIn, string $currencyOut, float $amount): ExchangeContract
    {
        $this->amount = $amount;
        $this->currencyIn = $currencyIn;
        $this->currencyOut = $currencyOut;

        return $this;
    }

    /**
     * @return Collection
     */
    public function findPaths(): Collection
    {
        $this->init();

        if(! ($this->orderBooks->has($this->currencyIn) && $this->orderBooks->has($this->currencyOut))) {
            throw new \Exception('Неверная валюта');
        }

        $result = collect();

        $classPathFinder = config('exchange.methods.' . $this->method . '.class');
        /** @var BaseFinder */
        $pathFinder = new $classPathFinder;

        while($this->amount > pow(10, -5)) {
            try {
                $bestPath = $pathFinder->setOrderBooks($this->orderBooks)
                    ->run($this->currencyIn, $this->currencyOut);

                if($bestPath->isEmpty()) {
                    break;
                }

                $result->add($this->createExchangeResult($bestPath));
            } catch (\Exception $e) {
                break;
            }
        }

        return $result;
    }

    /**
     * @return void
     */
    protected function init()
    {
        $sourceClass = config('exchange.sources.' . $this->source . '.class');
        $this->exchange = new $sourceClass(config('exchange.sources.' . $this->source . '.params'));

        $this->orderBooks = collect();

        $currencies = Currency::all()->keyBy('ticker');

        $markets = $this->exchange->load_markets();

        foreach($markets as $symbol => $market) {
            $tickers = explode(config('exchange.currencies_separator'), $symbol);

            if($currencies->has($tickers[0]) && $currencies->has($tickers[1])) {
                $prices = $this->exchange->fetch_order_book($symbol);

                $prices = [
                    'bids' => $prices['bids'],
                    'asks' => $prices['asks'],
                ];

                if(empty($this->orderBooks[$tickers[0]])) {
                    $this->orderBooks[$tickers[0]] = collect();
                }

                $this->orderBooks[$tickers[0]][$tickers[1]] = new OrderBookNormal($tickers[0], $tickers[1], [
                    'fee' => $market['taker'] ?? 0,
                    'info' => $market['info'] ?? []
                ]);
                $this->orderBooks[$tickers[0]][$tickers[1]]->loadPrices($prices);

                if(empty($this->orderBooks[$tickers[1]])) {
                    $this->orderBooks[$tickers[1]] = collect();
                }
                $this->orderBooks[$tickers[1]][$tickers[0]] = new OrderBookReverse($tickers[1], $tickers[0], [
                    'fee' => $market['taker'] ?? 0,
                    'info' => $market['info'] ?? []
                ]);

                $this->orderBooks[$tickers[1]][$tickers[0]]->loadPrices($prices);
            }
        }
    }

    /**
     * @param ExchangePath $exhangePath
     * @param bool $isReal
     *
     * @return ExchangeResult
     */
    protected function createExchangeResult(ExchangePath $exhangePath, bool $isReal = true): ExchangeResult
    {
        $result = new ExchangeResult;

        $finishAmount = $this->finishAmount($exhangePath);

        $decreasedAmounts = $this->decreasedAmounts($exhangePath, $finishAmount);

        $result = $this->fillExchangeResult($result, $decreasedAmounts);

        if($isReal) {
            $badPaths = [];
            for($i = 1; $i <= config('exchange.bad_paths'); $i++) {
                $badExchangePath = $this->getBadPath($exhangePath, $badPaths);
                $result->addBadResult($this->createBadExchangeResult($badExchangePath, $decreasedAmounts[$this->currencyIn]->amount));
                $badPaths[] = $badExchangePath;
            }


            $result->addDirectExchange($this->orderBooks[$this->currencyIn][$this->currencyOut] ?? null, $this->currencyIn, $this->currencyOut, $decreasedAmounts[$this->currencyIn]->amount);

            $this->decreaseFirstAmount($decreasedAmounts);

            $this->decreaseAmountsInOrderBooks($decreasedAmounts);
        }

        return $result;
    }

    /**
     * @param ExchangePath $exhangePath
     *
     * @return float
     */
    protected function finishAmount(ExchangePath $exhangePath): float
    {
        $amount = $this->amount;
        $ticker1 = $exhangePath->first();

        foreach($exhangePath->slice(1) as $ticker2) {
            $amount = $this->orderBooks[$ticker1][$ticker2]->getNewAmount($amount);
            $ticker1 = $ticker2;
        }

        return $amount;
    }

    /**
     * @param ExchangePath $exhangePath
     * @param float $finishAmount
     *
     * @return Collection
     */
    protected function decreasedAmounts(ExchangePath $exhangePath, float $finishAmount): Collection
    {
        $decreased = collect([]);
        $ticker1 = $exhangePath->last();
        $amount = $finishAmount;

        foreach($exhangePath->reverse() as $ticker2) {
            $amount = $ticker1 === $ticker2
                ? $amount
                : $this->orderBooks[$ticker2][$ticker1]->decreasedAmount($amount);

            $decreased[$ticker2] = (object)[
                'ticker_prev' => null,
                'amount' => !empty($this->orderBooks[$ticker2][$ticker1]) ? $this->orderBooks[$ticker2][$ticker1]->roundByLotStep($amount) : $amount,
                'comission' => 0,
                'price' => '',
            ];

            if($ticker1 !== $ticker2) {
                $decreased[$ticker1]->comission = $this->orderBooks[$ticker2][$ticker1]->comission($decreased[$ticker1]->amount);
            }

            $ticker1 = $ticker2;
        }

        $decreased = $decreased->reverse();

        $ticker1 = '';
        foreach ($decreased->keys() as $ticker)
        {
            $decreased[$ticker]->ticker_prev = $ticker1;
            $decreased[$ticker]->price = !empty($this->orderBooks[$ticker1][$ticker]) ? $this->orderBooks[$ticker1][$ticker]->costOriginalText() : '';
            $ticker1 = $ticker;
        }

        return $decreased;
    }

    /**
     * @param Collection $decreasedAmounts
     *
     * @return void
     */
    protected function decreaseFirstAmount(Collection $decreasedAmounts)
    {
        $this->amount -= $decreasedAmounts[$this->currencyIn]->amount;
    }

    /**
     * @param Collection $decreasedAmounts
     *
     * @return void
     */
    protected function decreaseAmountsInOrderBooks(Collection $decreasedAmounts)
    {
        foreach($decreasedAmounts as $ticker => $amountInfo) {
            if($ticker === $this->currencyIn) {
                continue;
            }

            $this->orderBooks[$amountInfo->ticker_prev][$ticker]->decreaseAmount($amountInfo->amount);
        }
    }

    /**
     * @param ExchangeResult $exchangeResult
     * @param Collection $decreasedAmounts
     *
     * @return ExchangeResult
     */
    protected function fillExchangeResult(ExchangeResult $exchangeResult, Collection $decreasedAmounts): ExchangeResult
    {
        foreach($decreasedAmounts as $ticker => $amountInfo) {
            if($ticker === $this->currencyIn) {
                continue;
            }

            $exchangeResult->addStep(
                $amountInfo->ticker_prev,
                $ticker,
                [
                    'amountFrom' => $decreasedAmounts[$amountInfo->ticker_prev]->amount,
                    'amountTo' => $amountInfo->amount,
                    'comission' => $amountInfo->comission,
                    'price' => $amountInfo->price,
                ]
            );
        }

        return $exchangeResult;
    }

    /**
     * @param ExchangePath $exchangePath
     * @param array $excludePaths
     *
     * @return ExchangePath
     */
    protected function getBadPath(ExchangePath $exchangePath, array $excludePaths): ExchangePath
    {
        $excludePaths[] = $exchangePath;

        $path = (new RandomLevel2Finder())
            ->setExcludeExhangePaths($excludePaths)
            ->setOrderBooks($this->orderBooks)
            ->run($this->currencyIn, $this->currencyOut);

        if(!$path) {
            $path = (new RandomLevel3Finder())
                ->setExcludeExhangePaths($excludePaths)
                ->setOrderBooks($this->orderBooks)
                ->run($this->currencyIn, $this->currencyOut);
        }

        return $path;
    }

    /**
     * @param ExchangePath $exhangePath
     * @param bool $isReal
     *
     * @return ExchangeResult
     */
    protected function createBadExchangeResult(ExchangePath $exhangePath, float $amount): ExchangeResult
    {
        $result = new ExchangeResult;

        $ticker1 = $this->currencyIn;

        foreach($exhangePath as $ticker) {
            if($ticker === $this->currencyIn) {
                continue;
            }

            $amountTo = $amount * $this->orderBooks[$ticker1][$ticker]->cost();

            $result->addStep(
                $ticker1,
                $ticker,
                [
                    'amountFrom' => $amount,
                    'amountTo' => $amountTo,
                    'comission' => $this->orderBooks[$ticker1][$ticker]->comission($amountTo),
                    'price' => $this->orderBooks[$ticker1][$ticker]->costOriginalText(),
                ]
            );

            $ticker1 = $ticker;
            $amount = $amountTo;
        }

        return $result;
    }

}