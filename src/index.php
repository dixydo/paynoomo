<?php

require_once '../vendor/autoload.php';

use App\DTO\Request;
use App\DTO\Transaction;
use App\Factory\MoneyFactory;
use App\Validator\RequestMoneyValidator;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$deviation = isset($_ENV['MONEY_DEVIATION_PERCENTAGE']) ? (float)$_ENV['MONEY_DEVIATION_PERCENTAGE'] : 0.1;

$validator = new RequestMoneyValidator($deviation, new MoneyFactory());

$request1 = new Request('100.00', 'USD');
$transaction1 = new Transaction('100.00', 'USD');
$isValid1 = $validator->validate($request1, $transaction1);

$request2 = new Request('110.00', 'USD');
$transaction2 = new Transaction('100.00', 'USD');
$isValid2 = $validator->validate($request2, $transaction2);

$request3 = new Request('100.00', 'USD');
$transaction3 = new Transaction('100.00', 'EUR');
$isValid3 = $validator->validate($request3, $transaction3);

$request4 = new Request('50.00', 'USD');
$transaction4 = new Transaction('100.00', 'USD');
$isValid4 = $validator->validate($request4, $transaction4);

echo "Example 1: ".($isValid1 ? "Valid" : "Invalid").PHP_EOL;
echo "Example 2: ".($isValid2 ? "Valid" : "Invalid").PHP_EOL;
echo "Example 3: ".($isValid3 ? "Valid" : "Invalid").PHP_EOL;
echo "Example 4: ".($isValid4 ? "Valid" : "Invalid").PHP_EOL;