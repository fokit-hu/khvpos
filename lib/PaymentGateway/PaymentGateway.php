<?php

namespace KHBankTools\PaymentGateway;

use Psr\Http\Client\ClientInterface;

class PaymentGateway
{
    const LANGUAGE_CODE_DE = 'DE';
    const LANGUAGE_CODE_EN = 'EN';
    const LANGUAGE_CODE_ES = 'ES';
    const LANGUAGE_CODE_FR = 'FR';
    const LANGUAGE_CODE_HU = 'HU';
    const LANGUAGE_CODE_IT = 'IT';
    const LANGUAGE_CODE_PL = 'PL';
    const LANGUAGE_CODE_PT = 'PT';
    const LANGUAGE_CODE_RO = 'RO';
    const LANGUAGE_CODE_SK = 'SK';
    
    const LANGUAGE_CODES = [
        self::LANGUAGE_CODE_DE,
        self::LANGUAGE_CODE_EN,
        self::LANGUAGE_CODE_ES,
        self::LANGUAGE_CODE_FR,
        self::LANGUAGE_CODE_HU,
        self::LANGUAGE_CODE_IT,
        self::LANGUAGE_CODE_PL,
        self::LANGUAGE_CODE_PT,
        self::LANGUAGE_CODE_RO,
        self::LANGUAGE_CODE_SK,
    ];
    
    const VERSION = 'v1';
    const PAYMENT_ENDPOINT_PATH = '/PGPayment';
    const RESULT_ENDPOINT_PATH = '/PGResult';
    
    private $signatureProvider;
    private $merchantId;
    private $isTest;
    private $httpClient;
    
    public function __construct(int $merchantId, SignatureProvider $signatureProvider, bool $isTest = false, ClientInterface $httpClient = null)
    {
        $this->merchantId = $merchantId;
        $this->signatureProvider = $signatureProvider;
        $this->isTest = $isTest;
        $this->httpClient = $httpClient;
    }

    protected function getEndpointBase(): string
    {
        return 'https://pay.'. ($this->isTest ? 'sandbox.' : '').'khpos.hu/pay/'.self::VERSION;
    }
    
    private function buildQuery(PaymentRequestArguments $arguments, ?string $languageCode = null): string
    {
        if ($arguments->getPaymentType() === PaymentRequestArguments::PAYMENT_RESULT_TYPE) {
            $queryArguments = [
                PaymentRequestArguments::MERCHANT_ID_REQUEST_ARGUMENT_NAME => $arguments->getMerchantId(),
                PaymentRequestArguments::TRANSACTION_ID_REQUEST_ARGUMENT_NAME => $arguments->getTransactionId(),
            ];
        }
        else {
            $queryArguments = [
                PaymentRequestArguments::MERCHANT_ID_REQUEST_ARGUMENT_NAME => $arguments->getMerchantId(),
                PaymentRequestArguments::TRANSACTION_ID_REQUEST_ARGUMENT_NAME => $arguments->getTransactionId(),
                PaymentRequestArguments::PAYMENT_TYPE_REQUEST_ARGUMENT_NAME => $arguments->getPaymentType(),
                PaymentRequestArguments::AMOUNT_REQUEST_ARGUMENT_NAME => $arguments->getAmount(),
                PaymentRequestArguments::CURRENCY_REQUEST_ARGUMENT_NAME => $arguments->getCurrency(),
            ];
        
            $queryArguments['sign'] = $this->signatureProvider->sign($arguments);
        
            if ($languageCode !== self::LANGUAGE_CODE_HU) {
                $queryArguments['lang'] = $languageCode;
            }
        }
        
        return \http_build_query($queryArguments);
    }
    
    private function transactionToPaymentRequestArguments(TransactionInterface $transaction, string $paymentType): PaymentRequestArguments
    {
        if ($paymentType === PaymentRequestArguments::PAYMENT_RESULT_TYPE) {
            $paymentArguments = new PaymentRequestArguments($paymentType, $transaction->getId());
        }
        else {
            $paymentArguments = new PaymentRequestArguments($paymentType, $transaction->getId(), (int) ($transaction->getAmount() * 100), PaymentRequestArguments::transactionCurrencyConverter($transaction));
        }
        
        $paymentArguments->setMerchantId($this->merchantId);
        
        return $paymentArguments;
    }
    
    public function paymentUrl(TransactionInterface $transaction, $languageCode = self::LANGUAGE_CODE_HU): string
    {
        $url = $this->getEndpointBase();
        $paymentArguments = $this->transactionToPaymentRequestArguments($transaction, PaymentRequestArguments::PAYMENT_PURCHASE_TYPE);
        $url .= self::PAYMENT_ENDPOINT_PATH.'?'.$this->buildQuery($paymentArguments, $languageCode);
        
        return $url;
    }

    public function refundUrl(TransactionInterface $transaction, $languageCode = self::LANGUAGE_CODE_HU): string
    {
        $url = $this->getEndpointBase();
        $paymentArguments = $this->transactionToPaymentRequestArguments($transaction, PaymentRequestArguments::PAYMENT_REFUND_TYPE);
        $url .= self::PAYMENT_ENDPOINT_PATH.'?'.$this->buildQuery($paymentArguments, $languageCode);
        
        return $url;
    }
    
    public function paymentResultCheckUrl(TransactionInterface $transaction): string
    {
        $url = $this->getEndpointBase();
        $paymentArguments = $this->transactionToPaymentRequestArguments($transaction, PaymentRequestArguments::PAYMENT_RESULT_TYPE);
        $url .= self::RESULT_ENDPOINT_PATH.'?'.$this->buildQuery($paymentArguments);
        
        return $url;
    }
    
    protected function fetchPaymentStatus(TransactionInterface $transaction): string
    {
        if ($this->httpClient === null) {
            throw new \LogicException('http client not initialized for payment result check');
        }
        
        $request = $this->httpClient->createRequest('GET', $this->paymentResultCheckUrl($transaction));
        $response = $this->httpClient->sendRequest($request);
        
        return $response->getBody()->getContents();
    }
    
    public function getPaymentResult(TransactionInterface $transaction): PaymentResult
    {
        return PaymentResult::initWithResponseString($this->fetchPaymentStatus($transaction));
    }
}