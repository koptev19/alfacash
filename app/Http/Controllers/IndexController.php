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
        $exchangePaths = $changeService->setSource($request->source)
            ->setMethod($request->method)
            ->setExchangeData($request->currency_in, $request->currency_out, $request->amount)
            ->findPaths();

        $exchangePaths->each(function(ExchangeResult $exchangeResult) {
            $exchangeResult->calculateStartAndFinishAmounts();
        });

        $currencies = Currency::all();

        return view('public.exchange', compact('currencies', 'exchangePaths'));
    }
}
