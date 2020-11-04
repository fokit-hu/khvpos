<?php declare(strict_types=1);

namespace KHBankTools\Tests\PaymentGateway;

use KHBankTools\PaymentGateway\PaymentGateway;
use KHBankTools\PaymentGateway\PaymentRequestArguments;
use KHBankTools\PaymentGateway\SignatureProvider;
use KHBankTools\PaymentGateway\TransactionInterface;
use KHBankTools\Tests\PaymentGateway\Fixtures\TransactionImplementation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Psr18Client;

class PaymentGatewayTest extends TestCase
{
    private function callPrivateMethod($class, string $methodName, array $args = [])
    {
        $paymentGateway = new \ReflectionClass($class);
        $method = $paymentGateway->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($class, $args);
    }
    
    public function testHost(): void
    {
        $httpClient = new Psr18Client();
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');

        $paymentGateway = new PaymentGateway(1234, $signatureProvider, true);
        $this->assertSame($this->callPrivateMethod($paymentGateway, 'getEndpointBase'), 'https://pay.sandbox.khpos.hu/pay/v1');

        $paymentGateway = new PaymentGateway(1234, $signatureProvider, false);
        $this->assertSame($this->callPrivateMethod($paymentGateway, 'getEndpointBase'), 'https://pay.khpos.hu/pay/v1');
    }
    
    /**
     * @dataProvider buildQueryDataProvider
     */
    public function testBuildQuery(string $languageCode, string $expectedQueryString)
    {
        $httpClient = new Psr18Client();
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');

        $arguments = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_PURCHASE_TYPE, 1234, 12313, PaymentRequestArguments::CURRENCY_EUR);
        $arguments->setMerchantId(60000);
        
        $paymentGateway = new PaymentGateway(60000, $signatureProvider, false);
        
        $this->assertSame($this->callPrivateMethod($paymentGateway, 'buildQuery', [$arguments, $languageCode]), $expectedQueryString);
    }
    
    public function buildQueryDataProvider(): array
    {
        return [
            [PaymentGateway::LANGUAGE_CODE_HU, 'mid=60000&txid=1234&type=PU&amount=1231300&ccy=EUR&sign=10f97ff0d608a6cf396ff7d3bf86df859b48e701ed1990db5cc11922a5a80e6b8c906397de7319f1354d5b3a2973376716478ae0ee97e780331854c6d7dc0685f4a2efcb84737c00575ca8eca638ea355abac4226de984b9378d64121092a453a5d55b32a8b72dd729a2c339b08d73baaf498238ceb4d846f992ef02290ec0bc'],
            [PaymentGateway::LANGUAGE_CODE_EN, 'mid=60000&txid=1234&type=PU&amount=1231300&ccy=EUR&sign=10f97ff0d608a6cf396ff7d3bf86df859b48e701ed1990db5cc11922a5a80e6b8c906397de7319f1354d5b3a2973376716478ae0ee97e780331854c6d7dc0685f4a2efcb84737c00575ca8eca638ea355abac4226de984b9378d64121092a453a5d55b32a8b72dd729a2c339b08d73baaf498238ceb4d846f992ef02290ec0bc&lang=EN'],
        ];
    }
    
    /**
     * @dataProvider transactionToPaymentRequestArgumentsDataProvider
     */
    public function testTransactionToPaymentRequestArguments(TransactionInterface $transaction, string $paymentType, PaymentRequestArguments $requestArgument): void
    {
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $paymentGateway = new PaymentGateway(60000, $signatureProvider, false);

        $this->assertEquals($this->callPrivateMethod($paymentGateway, 'transactionToPaymentRequestArguments', [$transaction, $paymentType]), $requestArgument);
    }
    
    public function transactionToPaymentRequestArgumentsDataProvider(): array
    {
        $transaction = new TransactionImplementation();
        $transaction->id = 678;
        $transaction->amount = 123.45;
        $transaction->currency = TransactionInterface::TRANSACTION_EUR_CURRENCY;

        $argument1 = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_PURCHASE_TYPE, 678, 12345, 'EUR');
        $argument1->setMerchantId(60000);

        $argument2 = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_RESULT_TYPE, 678);
        $argument2->setMerchantId(60000);

        return [
            [$transaction, PaymentRequestArguments::PAYMENT_PURCHASE_TYPE, $argument1],
            [$transaction, PaymentRequestArguments::PAYMENT_RESULT_TYPE, $argument2],
        ];
    }
    
    /**
     * @dataProvider paymentUrlDataProvider
     */
    public function testPaymentUrl(TransactionInterface $transaction, string $language, string $url): void
    {
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $paymentGateway = new PaymentGateway(60000, $signatureProvider, false);
        
        $this->assertSame($paymentGateway->paymentUrl($transaction, $language), $url);
    }
    
    public function paymentUrlDataProvider(): array
    {
        $transaction = new TransactionImplementation();
        $transaction->id = 678;
        $transaction->amount = 123.45;
        $transaction->currency = TransactionInterface::TRANSACTION_EUR_CURRENCY;

        return [
            [$transaction, PaymentGateway::LANGUAGE_CODE_HU, 'https://pay.khpos.hu/pay/v1/PGPayment?mid=60000&txid=678&type=PU&amount=1234500&ccy=EUR&sign=7a6acb493e94f15d1b76e25a387f832b19fb548eb82f9464a62dc08ad7f47c3b1cc6ef5315e495b7e4890228a2599101f7aac8fb923e75e23c6d500e63c91a32556a6604c4c3c0320067f6a58366b91096f02ae60b000d93189edf18e9c3f7c3f3876c45a4283fb4771df6883a3826862e696bf5d5a14a38f860567aa2ea3e76'],
            [$transaction, PaymentGateway::LANGUAGE_CODE_EN, 'https://pay.khpos.hu/pay/v1/PGPayment?mid=60000&txid=678&type=PU&amount=1234500&ccy=EUR&sign=7a6acb493e94f15d1b76e25a387f832b19fb548eb82f9464a62dc08ad7f47c3b1cc6ef5315e495b7e4890228a2599101f7aac8fb923e75e23c6d500e63c91a32556a6604c4c3c0320067f6a58366b91096f02ae60b000d93189edf18e9c3f7c3f3876c45a4283fb4771df6883a3826862e696bf5d5a14a38f860567aa2ea3e76&lang=EN'],
        ];
    }
    
    /**
     * @dataProvider refundUrlDataProvider
     */
    public function testRefundUrl(TransactionInterface $transaction, string $language, string $url): void
    {
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $paymentGateway = new PaymentGateway(60000, $signatureProvider, false);
        
        $this->assertSame($paymentGateway->refundUrl($transaction, $language), $url);
    }
    
    public function refundUrlDataProvider(): array
    {
        $transaction = new TransactionImplementation();
        $transaction->id = 678;
        $transaction->amount = 123.45;
        $transaction->currency = TransactionInterface::TRANSACTION_EUR_CURRENCY;

        return [
            [$transaction, PaymentGateway::LANGUAGE_CODE_HU, 'https://pay.khpos.hu/pay/v1/PGPayment?mid=60000&txid=678&type=RE&amount=1234500&ccy=EUR&sign=240d33e15003a3412ee28c8fc6ede3cb9135f5698de442a6bea17df5141be6cac3332b1ce05d70d0d9554218a485dfcd5947acebe2c9cc68d5461b58ac148022cc09fc631b386e7abee645400709d4412ea18d1fd6ef53266673d8a8abfb91be25187590533dd1c61da858969a7e2e99ec6c62b9946bca0871469041cbea540f'],
            [$transaction, PaymentGateway::LANGUAGE_CODE_EN, 'https://pay.khpos.hu/pay/v1/PGPayment?mid=60000&txid=678&type=RE&amount=1234500&ccy=EUR&sign=240d33e15003a3412ee28c8fc6ede3cb9135f5698de442a6bea17df5141be6cac3332b1ce05d70d0d9554218a485dfcd5947acebe2c9cc68d5461b58ac148022cc09fc631b386e7abee645400709d4412ea18d1fd6ef53266673d8a8abfb91be25187590533dd1c61da858969a7e2e99ec6c62b9946bca0871469041cbea540f&lang=EN'],
        ];
    }
    
    public function testPaymentResultCheckUrl(): void
    {
        $transaction = new TransactionImplementation();
        $transaction->id = 678;
        $transaction->amount = 123.45;
        $transaction->currency = TransactionInterface::TRANSACTION_EUR_CURRENCY;

        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $paymentGateway = new PaymentGateway(60000, $signatureProvider, false);
        
        $this->assertSame($paymentGateway->paymentResultCheckUrl($transaction), 'https://pay.khpos.hu/pay/v1/PGResult?mid=60000&txid=678');
    }
}