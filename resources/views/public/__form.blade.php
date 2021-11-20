<div class="flex w-full">
    <form action="{{ route('index.exchange') }}" method="get" class="flex flex-col mx-auto gap-5 mt-5" x-data="{
        currencyIn: '{{ request('currency_in', $currencyIn ?? '') }}',
        currencyOut: '{{ request('currency_out', $currencyOut ?? '') }}'
    }">
        <div class="text-2xl font-weight text-center pb-6">Интерфейс для тестового задания компании Alfa.cash</div>
        <div class="flex gap-5">
            <div>
                <div class="pb-2">Отдаете:</div>
                <select name="currency_in" class="border border-gray-200 py-2 px-4 rounded w-40" x-model="currencyIn">
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->ticker }}">{{ $currency->ticker }}</option>
                    @endforeach
                </select>
                @error('currency_in')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex items-end pb-3">&rArr;</div>
            <div>
                <div class="pb-2">Получаете:</div>
                <select name="currency_out" class="border border-gray-200 py-2 px-4 rounded w-40" x-model="currencyOut">
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->ticker }}" >{{ $currency->ticker }}</option>
                    @endforeach
                </select>
                @error('currency_out')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="">
                <div class="pb-2">Сумма:</div>
                <div class="flex items-center gap-3">
                    <input type="number" name="amount" value="{{ request('amount') }}" class="w-64 border border-gray-200 rounded py-2 px-4" required min="0" step="0.0000000000000001">
                    <div x-text="currencyIn" class="w-20"></div>
                </div>
                @error('amount')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="flex gap-20">
            <div>
                <div class="pb-2">Источник данных:</div>
                <select name="source" class="border border-gray-200 py-2 px-4 rounded w-56">
                    @foreach(config('exchange.sources') as $key => $source)
                        <option value="{{ $key }}" @if(request('source', config('exchange.default_source')) === $key) selected @endif>{{ $source['name'] }}</option>
                    @endforeach
                </select>
                @error('source')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <div class="pb-2">Способ обмена:</div>
                <select name="method" class="border border-gray-200 py-2 px-4 rounded w-56">
                    @foreach(config('exchange.methods') as $key => $method)
                        <option value="{{ $key }}" @if(request('method', config('exchange.default_method')) === $key) selected @endif>{{ $method['name'] }}</option>
                    @endforeach
                </select>
                @error('method')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="">
            <button type="submit" class="border border-blue-500 font-weight text-blue-500 rounded-full py-2 px-10">Найти</button>
        </div>
    </form>
</div>
