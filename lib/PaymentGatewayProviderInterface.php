<?php

namespace KHBankTools\PaymentGateway;

interface PaymentGatewayProviderInterface
{
    public function getPaymentGateway(TransactionInterface $transaction): PaymentGateway;
}