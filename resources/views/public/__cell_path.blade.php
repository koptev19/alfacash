<div class="flex flex-col gap-5">
    @each('public.__step', $path, 'step')
</div>
<div class="mt-8 font-bold">Итого:</div>
{{ $path->first()->amountFrom . ' ' . $path->first()->currencyFrom->ticker }} =><br>
{{ $path->last()->amountTo . ' ' . $path->last()->currencyTo->ticker }}
