<?php

declare(strict_types=1);

namespace App\Validator;

use App\DTO\Request;
use App\DTO\Transaction;
use App\Factory\MoneyFactory;

readonly class RequestMoneyValidator
{

    public function __construct(
        private float $deviationPercentage,
        private MoneyFactory $moneyFactory
    ) {
        if ($deviationPercentage < 0 || $deviationPercentage > 1) {
            throw new \InvalidArgumentException('Deviation percentage must be between 0 and 1.');
        }
    }

    public function validate(Request $request, Transaction $transaction): bool
    {
        $requestMoney = $this->moneyFactory->createMoney($request->amount, $request->currencyCode);
        $transactionMoney = $this->moneyFactory->createMoney($transaction->amount, $transaction->currencyCode);

        if (!$requestMoney->getCurrency()->equals($transactionMoney->getCurrency())) {
            return false;
        }

        $deviationPercent = (int)($this->deviationPercentage * 100);
        $deviationAmount = $requestMoney->multiply($deviationPercent)->divide(100);

        $lowerBound = $transactionMoney->subtract($deviationAmount);
        $upperBound = $transactionMoney->add($deviationAmount);

        return $requestMoney->greaterThanOrEqual($lowerBound) &&
            $requestMoney->lessThanOrEqual($upperBound);
    }

}