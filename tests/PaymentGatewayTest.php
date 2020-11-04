<?php declare(strict_types=1);

namespace PaymentGatewayTests;

use KHBankTools\PaymentGateway\PaymentGateway;
use KHBankTools\PaymentGateway\SignatureProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Psr18Client;

class PaymentGatewayTest extends TestCase
{
    private function getAccessibleMethod(string $className, string $method)
    {
        $paymentGateway = new \ReflectionClass($className);
        $method = $paymentGateway->getMethod($method);
        $method->setAccessible(true);
        
        return $method;
    }
        
    public function testHost(): void
    {
        $httpClient = new Psr18Client();
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $publicGetHostMethod = $this->getAccessibleMethod(PaymentGateway::class, 'getEndpointBase');

        $paymentGateway = new PaymentGateway(1234, $signatureProvider, true);
        $this->assertSame($publicGetHostMethod->invokeArgs($paymentGateway, []), 'https://pay.sandbox.khpos.hu/pay/v1');

        $paymentGateway = new PaymentGateway(1234, $signatureProvider, false);
        $this->assertSame($publicGetHostMethod->invokeArgs($paymentGateway, []), 'https://pay.khpos.hu/pay/v1');
    }
}