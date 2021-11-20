<?php
namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{

    /**
     * @var array
     */
    private $tickers = [
        'BTC',
        'ETH',
        'ETC',
        'XRP',
        'LTC',
        // 'BNB',
        // 'NEO',
        // 'EOS',
        // 'BNT',
        // 'BCH',
        'USDT',
        // 'TUSD',
        // 'USDC'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach($this->tickers as $ticker) {
            Currency::create([
                'ticker' => $ticker
            ]);
        }
    }
}
