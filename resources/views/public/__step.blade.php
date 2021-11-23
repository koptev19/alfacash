<div>
    {{ $step->amountFrom . ' ' . $step->currencyFrom->ticker }} =><br>
    {{ $step->amountTo . ' ' . $step->currencyTo->ticker }}
    <div class="text-xs text-gray-400">Курс: {{ $step->price }}</div>
    <div class="text-xs text-gray-400">Комиссия: {{ $step->comission }} {{ $step->currencyTo->ticker }}</div>
</div>
