<?php declare(strict_types=1);

namespace KHBankTools\Tests\PaymentGateway;

use KHBankTools\PaymentGateway\PaymentGateway;
use KHBankTools\PaymentGateway\PaymentRequestArguments;
use KHBankTools\PaymentGateway\SignatureProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Psr18Client;

class SignatureProviderTest extends TestCase
{
    private function callPrivateMethod($class, string $methodName, array $args = [])
    {
        $paymentGateway = new \ReflectionClass($class);
        $method = $paymentGateway->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($class, $args);
    }
    
    private function getPrivatePropertyValue($class, string $propery)
    {
        $reflection = new \ReflectionClass($class);
        $reflection_property = $reflection->getProperty($propery);
        $reflection_property->setAccessible(true);
        
        return $reflection_property->getValue($class);
    }
        
    /**
     * @dataProvider constructorDataProvider
     */
    public function testConstructor(string $key, string $passphase): void
    {
        $signatureProvider = new SignatureProvider($key, $passphase);
        
        $this->assertSame($this->getPrivatePropertyValue($signatureProvider, 'privateKeyPath'), $key);
        $this->assertSame($this->getPrivatePropertyValue($signatureProvider, 'privateKeyPassphrase'), $passphase);

        $this->callPrivateMethod($signatureProvider, 'loadKey');
    }
    
    public function constructorDataProvider(): array
    {
        return [
            [__DIR__.'/Fixtures/test1_private_key.pem', ''],
            [__DIR__.'/Fixtures/test2_private_key.pem', 'KHBankToolsTest'],
        ];
    }
    
    public function testNonExistedKey(): void
    {
        $this->expectException(\LogicException::class);

        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/not_exists.pem');
    }
    
    /**
     * @dataProvider wrongPassphraseDataProvider
     */
    public function testWrongPassphrase(string $key, string $pass): void
    {
        $this->expectException(\LogicException::class);

        $signatureProvider = new SignatureProvider($key, $pass);
        
        $this->callPrivateMethod($signatureProvider, 'loadKey');
    }
    
    public function wrongPassphraseDataProvider(): array
    {
        return [
            [__DIR__.'/Fixtures/test2_private_key.pem', 'wrongpassphrase'],
            [__DIR__.'/Fixtures/test2_private_key.pem', ''],
        ];
    }
    
    public function testBuildQueryToSign(): void
    {
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $arguments = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_PURCHASE_TYPE, 1234, (int) (123.13*100), PaymentRequestArguments::CURRENCY_EUR);
        $arguments->setMerchantId(60000);
        $this->assertSame($this->callPrivateMethod($signatureProvider, 'buildQueryToSign', [$arguments]), 'mid=60000&txid=1234&type=PU&amount=12313&ccy=EUR');
    }
    
    public function testBuildQueryToSignWithoutMerchantId(): void
    {
        $this->expectException(\TypeError::class);

        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $arguments = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_PURCHASE_TYPE, 1234, 12313, PaymentRequestArguments::CURRENCY_EUR);
        $this->assertSame($this->callPrivateMethod($signatureProvider, 'buildQueryToSign', [$arguments]), 'mid=60000&txid=1234&type=PU&amount=12313&ccy=EUR');
    }

    public function testSign(): void
    {
        $publicKey = file_get_contents(__DIR__.'/Fixtures/test1_public_key.pem');
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $arguments = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_PURCHASE_TYPE, 1234, 12313, PaymentRequestArguments::CURRENCY_EUR);
        $arguments->setMerchantId(60000);

        $signature = $signatureProvider->sign($arguments);

        $this->assertSame(openssl_verify('mid=60000&txid=1234&type=PU&amount=12313&ccy=EUR', hex2bin($signature), $publicKey, OPENSSL_ALGO_SHA1), 1);
    }
}