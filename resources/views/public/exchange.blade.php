@extends('layouts.public')

@section('content')
    <div class="container">
        @include('public.__form')


        <div class="mt-10 text-2xl font-weight pb-5">Результат работы</div>
        <div class="mb-20 flex flex-col gap-3">
            <div>Потрачено: {{ $exchangePaths->sum('startAmount') }} {{ request('currency_in') }}</div>
            <div>Получено: {{ round($exchangePaths->sum('finishAmount'), 2) }} {{ request('currency_out') }}</div>
        </div>

        <div class="mt-20 text-2xl font-weight pb-10">Пути обмена</div>
        <table class="table mb-10">
            <thead>
                <tr>
                    <th>Номер</th>
                    <th>Путь обмена</th>
                    <th>Итого</th>
                </tr>
            </thead>
            <tbody>
            @foreach($exchangePaths as $path)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div class="flex flex-col gap-5">
                            @foreach($path as $step)
                                <div>
                                    {{ $step->amountFrom . ' ' . $step->currencyFrom->ticker }} =>
                                    {{ $step->amountTo . ' ' . $step->currencyTo->ticker }}
                                    <span class="text-xs text-gray-400">({{ number_format($step->comission, 10, '.', ' ') }} {{ $step->currencyTo->ticker }})</span>
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        {{ $path->first()->amountFrom . ' ' . $path->first()->currencyFrom->ticker }} =>
                        {{ $path->last()->amountTo . ' ' . $path->last()->currencyTo->ticker }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>


    </div>
@endsection