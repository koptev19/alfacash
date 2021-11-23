@extends('layouts.public')

@section('content')
    <div class="px-10">
        @include('public.__form')


        <div class="mt-10 text-2xl font-weight pb-5">Результат работы</div>
        <div class="mb-20 flex flex-col gap-3">
            <div>Продано: {{ $exchangePaths->sum('startAmount') }} {{ $currencyIn }}</div>
            <div>Куплено: {{ round($exchangePaths->sum('finishAmount'), 2) }} {{ $currencyOut }}</div>
        </div>

        <div class="mt-20 text-2xl font-weight pb-10">Пути обмена</div>
        <table class="table mb-10">
            <thead>
                <tr>
                    <th>Номер</th>
                    <th>Лучший обмен</th>
                    <th>Прямой обмен</th>
                    @for($i = 1; $i <= config('exchange.bad_paths'); $i++)
                        <th>Плохой обмен {{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
            @foreach($exchangePaths as $path)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @include('public.__cell_path')
                    </td>

                    <td>
                        @if($path->directExchange)
                            @include('public.__step', ['step' => $path->directExchange])
                        @else
                            -
                        @endif
                    </td>
                    @foreach($path->badResults as $badPath)
                    <td>
                        @include('public.__cell_path', ['path' => $badPath])
                    </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>


    </div>
@endsection