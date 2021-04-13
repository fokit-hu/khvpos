<?php

namespace KHTools\VPos;

use Psr\Http\Client\ClientInterface;

class VPosClientProvider implements VPosClientProviderInterface
{
    private VPosClient $paymentGateway;

    public function __construct(string $version, int $merchantId, string $privateKeyPath, string $privateKeyPassphrase = '', bool $isTest = false, ClientInterface $httpClient = null)
    {
        $signatureProvider = new SignatureProvider($privateKeyPath, $privateKeyPassphrase, true);
        $this->paymentGateway = new VPosClient($version, $merchantId, $signatureProvider, $isTest, $httpClient);
    }

    public function getPaymentGateway(TransactionInterface $transaction): VPosClient
    {
        return $this->paymentGateway;
    }
}