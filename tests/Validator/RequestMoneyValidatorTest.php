<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\DTO\Request;
use App\DTO\Transaction;
use App\Factory\MoneyFactory;
use App\Validator\RequestMoneyValidator;
use Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\TestCase;

class RequestMoneyValidatorTest extends TestCase
{

    private MoneyFactory $moneyFactory;

    private RequestMoneyValidator $validator;

    protected function setUp(): void
    {
        $this->moneyFactory = new MoneyFactory();
        $this->validator = new RequestMoneyValidator(0.1, new MoneyFactory());
    }

    public function testValidateReturnsFalseWhenCurrenciesDontMatch(): void
    {
        $request = new Request('100.00', 'USD');
        $transaction = new Transaction('100.00', 'EUR');

        $this->assertFalse($this->validator->validate($request, $transaction));
    }

    public function testValidateReturnsTrueWhenAmountsAreEqual(): void
    {
        $request = new Request('100.00', 'USD');
        $transaction = new Transaction('100.00', 'USD');

        $this->assertTrue($this->validator->validate($request, $transaction));
    }

    public function testValidateReturnsTrueWhenAmountIsWithinLowerDeviation(): void
    {
        $request = new Request('109.99', 'USD');
        $transaction = new Transaction('100.00', 'USD');

        $this->assertTrue($this->validator->validate($request, $transaction));
    }

    public function testValidateReturnsFalseWhenAmountIsBelowAllowedDeviation(): void
    {
        $request = new Request('90.9', 'USD');
        $transaction = new Transaction('100.00', 'USD');

        $this->assertFalse($this->validator->validate($request, $transaction));
    }

    public function testValidateReturnsTrueWhenAmountIsWithinUpperDeviation(): void
    {
        $request = new Request('111.00', 'USD');
        $transaction = new Transaction('100.00', 'USD');

        $this->assertTrue($this->validator->validate($request, $transaction));
    }

    public function testValidateReturnsFalseWhenAmountIsAboveAllowedDeviation(): void
    {
        $request = new Request('90.00', 'USD');
        $transaction = new Transaction('100.00', 'USD');

        $this->assertFalse($this->validator->validate($request, $transaction));
    }

    public function testValidateReturnsTrueAtExactBoundaries(): void
    {
        $request = new Request('100.00', 'USD');
        $transaction = new Transaction('90.00', 'USD');

        $this->assertTrue($this->validator->validate($request, $transaction));

        $request = new Request('100.00', 'USD');
        $transaction = new Transaction('110.00', 'USD');

        $this->assertTrue($this->validator->validate($request, $transaction));
    }

    public function testValidateWithDifferentDeviationPercentage(): void
    {
        $validator = new RequestMoneyValidator(0.05, new MoneyFactory());

        $request1 = new Request('90.00', 'USD');
        $request2 = new Request('98.00', 'USD');
        $transaction = new Transaction('94.00', 'USD');

        $this->assertTrue($validator->validate($request1, $transaction));
        $this->assertTrue($validator->validate($request2, $transaction));
    }

    public function testValidateIsCaseInsensitiveForCurrencyCodes(): void
    {
        $request = new Request('110.00', 'USD');
        $transaction = new Transaction('100.00', 'usd');
        $this->assertTrue($this->validator->validate($request, $transaction));

        $request = new Request('95.00', 'usd');
        $transaction = new Transaction('100.00', 'USD');
        $this->assertTrue($this->validator->validate($request, $transaction));

        $request = new Request('100.00', 'UsD');
        $transaction = new Transaction('100.00', 'uSd');
        $this->assertTrue($this->validator->validate($request, $transaction));
    }

    public function testCannotCreateMoneyWithInvalidCurrencyCode(): void
    {
        $this->expectException(UnknownCurrencyException::class);

        $request = new Request('100.00', 'INVALID');
        $transaction = new Transaction('100.00', 'USD');

        $this->validator->validate($request, $transaction);
    }

    public function testDeviationValidation(): void
    {
        try {
            $validator1 = new RequestMoneyValidator(0, new MoneyFactory());
            $this->assertInstanceOf(RequestMoneyValidator::class, $validator1);

            $validator2 = new RequestMoneyValidator(0.5, new MoneyFactory());
            $this->assertInstanceOf(RequestMoneyValidator::class, $validator2);

            $validator3 = new RequestMoneyValidator(1, new MoneyFactory());
            $this->assertInstanceOf(RequestMoneyValidator::class, $validator3);
        } catch (\Exception $e) {
            $this->fail('Valid deviation values should not throw exceptions: '.$e->getMessage());
        }

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Deviation percentage must be between 0 and 1.');

        new RequestMoneyValidator(-0.1, new MoneyFactory());
    }

}