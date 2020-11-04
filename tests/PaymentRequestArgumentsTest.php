<?php declare(strict_types=1);

namespace PaymentGatewayTests;

use KHBankTools\PaymentGateway\PaymentRequestArguments;
use PHPUnit\Framework\TestCase;

class PaymentRequestArgumentsTest extends TestCase
{
    public function testPaymentResultType(): void
    {
        $args = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_RESULT_TYPE, 1234, 3000, PaymentRequestArguments::CURRENCY_HUF);
        
        $this->assertSame($args->getPaymentType(), PaymentRequestArguments::PAYMENT_RESULT_TYPE);
        $this->assertSame($args->getTransactionId(), 1234);
        $this->assertSame($args->getAmount(), null);
        $this->assertSame($args->getCurrency(), null);
    }
    
    /**
     * @dataProvider setterGetterDataProvider
     */
    public function testSettersGetters(string $property, $value): void
    {
        $args = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_RESULT_TYPE, 1234, 3000, PaymentRequestArguments::CURRENCY_HUF);

        $this->assertSame($args->{'set'.ucfirst($property)}($value), $args);
        $this->assertSame($args->{'get'.ucfirst($property)}(), $value);
    }
    
    public function setterGetterDataProvider(): array
    {
        return [
            ['merchantId', 1234],
            ['transactionId', 4321],
            ['paymentType', PaymentRequestArguments::PAYMENT_PURCHASE_TYPE],
            ['amount', 10000],
            ['currency', PaymentRequestArguments::CURRENCY_HUF],
        ];
    }
    
    /**
     * @dataProvider setterGetterExceptionDataProvider
     */
    public function testSetterExceptions(string $property, $value, $exception): void
    {
        $this->expectException($exception);
        
        $args = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_RESULT_TYPE, 1234, 3000, PaymentRequestArguments::CURRENCY_HUF);
        $args->{'set'.ucfirst($property)}($value);
    }
    
    public function setterGetterExceptionDataProvider(): array
    {
        return [
            ['transactionId', -1, \LogicException::class],
            ['transactionId', 10000000000, \LogicException::class],
            ['paymentType', 'nonexist', \LogicException::class],
            ['currency', 'nonexist', \LogicException::class],
            ['amount', 123.45, \TypeError::class],
        ];
    }
}