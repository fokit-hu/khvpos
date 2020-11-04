<?php

namespace KHBankTools\PaymentGateway;

class PaymentRequestArguments
{
    const PAYMENT_PURCHASE_TYPE = 'PU';
    const PAYMENT_REFUND_TYPE = 'RE';
    const PAYMENT_RESULT_TYPE = '_RES'; // inernal type

    const PAYMENT_TYPES = [
        self::PAYMENT_PURCHASE_TYPE,
        self::PAYMENT_REFUND_TYPE,
        self::PAYMENT_RESULT_TYPE,
    ];

    const CURRENCY_HUF = 'HUF';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_USD = 'USD';
    
    const CURRENCIES = [
        self::CURRENCY_HUF,
        self::CURRENCY_EUR,
        self::CURRENCY_USD,
    ];
    
    const MERCHANT_ID_REQUEST_ARGUMENT_NAME = 'mid';
    const TRANSACTION_ID_REQUEST_ARGUMENT_NAME = 'txid';
    const PAYMENT_TYPE_REQUEST_ARGUMENT_NAME = 'type';
    const AMOUNT_REQUEST_ARGUMENT_NAME = 'amount';
    const CURRENCY_REQUEST_ARGUMENT_NAME = 'ccy';
    
    public function __construct(string $paymentType, int $transactionId, ?int $amount = null, ?string $currency = null)
    {
        if ($paymentType === self::PAYMENT_RESULT_TYPE) {
            $this->setPaymentType($paymentType);
            $this->setTransactionId($transactionId);
        }
        else {
            $this->setTransactionId($transactionId);
            $this->setPaymentType($paymentType);
            $this->setAmount($amount);
            $this->setCurrency($currency);
        }
    }
    
    public static function transactionCurrencyConverter(TransactionInterface $transaction): string
    {
        switch ($transaction->getCurrency()) {
            case TransactionInterface::TRANSACTION_HUF_CURRENCY:
                return self::CURRENCY_HUF;
                
            case TransactionInterface::TRANSACTION_EUR_CURRENCY:
                return self::CURRENCY_EUR;
                
            case TransactionInterface::TRANSACTION_USD_CURRENCY:
                return self::CURRENCY_USD;
        }
        
        throw new \LogicException(sprintf('Unknown currency: "%s".', $transaction->getCurrency()));
    }
    
    private $merchantId;
    
    public function setMerchantId(int $merchantId): self
    {
        $this->merchantId = $merchantId;
        
        return $this;
    }
    
    public function getMerchantId(): int
    {
        return $this->merchantId;
    }
    
    private $transactionId;
    
    public function setTransactionId(int $transactionId): self
    {
        if ($transactionId <= 0 || $transactionId > 9999999999) {
            throw new \LogicException(sprintf('Invalid transaction id: %d', $transactionId));
        }
        
        $this->transactionId = $transactionId;
        
        return $this;
    }
    
    public function getTransactionId(): int
    {
        return $this->transactionId;
    }
    
    private $paymentType;
    
    public function setPaymentType(string $paymentType): self
    {
        if (!in_array($paymentType, self::PAYMENT_TYPES)) {
            throw new \LogicException(sprintf('Invalid payment type: "%s".', $paymentType));
        }
        
        $this->paymentType = $paymentType;
        
        return $this;
    }
    
    public function getPaymentType(): string
    {
        return $this->paymentType;
    }
    
    private $amount;
    
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;
        
        return $this;
    }
    
    public function getAmount(): ?int
    {
        return $this->amount;
    }
    
    private $currency;
    
    public function setCurrency(string $currency): self
    {
        if (!\in_array($currency, self::CURRENCIES)) {
            throw new \LogicException(sprintf('Invalid Currency: "%s"', $currency));
        }
            
        $this->currency = $currency;
        
        return $this;
    }
    
    public function getCurrency(): ?string
    {
        return $this->currency;
    }
}