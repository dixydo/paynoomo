<?php

declare(strict_types=1);

namespace App\Factory;

use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use Money\Currencies\ISOCurrencies;

class MoneyFactory
{

    private DecimalMoneyParser $moneyParser;

    public function __construct()
    {
        $this->moneyParser = new DecimalMoneyParser(new ISOCurrencies());
    }

    public function createMoney(string $amount, string $currency): Money
    {
        return $this->moneyParser->parse($amount, new Currency($currency));
    }

}