<?php

namespace App\Support;

use Illuminate\Support\Collection;

class ExchangePath extends Collection
{
    /**
     * @param string $ticker
     *
     * @return void
     */
    public function addNode(string $ticker)
    {
        $this->add($ticker);
    }

}
