<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitFormRequest;
use App\Models\Currency;
use App\Services\ExchangeService;
use App\Support\ExchangeResult;

class IndexController extends Controller
{
    /**
     * @return
     */
    public function index()
    {
        $currencies = Currency::all();

        $currencyIn = 'BTC';
        $currencyOut = 'USDT';

        return view('public.index', compact('currencies', 'currencyIn', 'currencyOut'));
    }

    /**
     * @return
     */
    public function exchange(SubmitFormRequest $request, ExchangeService $changeService)
    {
        $currencyIn = $request->deal === 'direct' ? $request->currency_in : $request->currency_out;
        $currencyOut = $request->deal === 'direct' ? $request->currency_out : $request->currency_in;

        $exchangePaths = $changeService->setSource(config('exchange.default_source'))
            ->setMethod(config('exchange.default_method'))
            ->setExchangeData($currencyIn, $currencyOut, $request->amount)
            ->findPaths();

        $exchangePaths->each(function(ExchangeResult $exchangeResult) {
            $exchangeResult->calculateStartAndFinishAmounts();
        });

        $currencies = Currency::all();

        return view('public.exchange', compact('currencies', 'exchangePaths', 'currencyIn', 'currencyOut'));
    }
}
