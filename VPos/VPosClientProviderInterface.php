<?php

namespace KHTools\VPos;

interface VPosClientProviderInterface
{
    public function getPaymentGateway(TransactionInterface $transaction): VPosClient;
}