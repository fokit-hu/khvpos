<?php

namespace KHBankTools\PaymentGateway;

use Psr\Http\Client\ClientInterface;

class PaymentGatewayProvider implements PaymentGatewayProviderInterface
{
    private PaymentGateway $paymentGateway;

    public function __construct(string $version, int $merchantId, string $privateKeyPath, string $privateKeyPassphrase = '', bool $isTest = false, ClientInterface $httpClient = null)
    {
        $signatureProvider = new SignatureProvider($privateKeyPath, $privateKeyPassphrase, true);
        $this->paymentGateway = new PaymentGateway($version, $merchantId, $signatureProvider, $isTest, $httpClient);
    }

    public function getPaymentGateway(TransactionInterface $transaction): PaymentGateway
    {
        return $this->paymentGateway;
    }
}