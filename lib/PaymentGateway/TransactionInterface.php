<?php

namespace KHBankTools\PaymentGateway;

interface TransactionInterface
{
    const TRANSACTION_HUF_CURRENCY = 1;
    const TRANSACTION_EUR_CURRENCY = 2;
    const TRANSACTION_USD_CURRENCY = 3;
    
    const TRANSACTION_STATUS_INIT = 0; // not sent
    const TRANSACTION_STATUS_FAILED = 1; // NAK
    const TRANSACTION_STATUS_UNKNOWN_ID = 2; // UTX
    const TRANSACTION_STATUS_PENDING = 3; // PEN
    const TRANSACTION_STATUS_REFOUND_PENDING = 9; // PE2
    const TRANSACTION_STATUS_ERROR = 4; // ERR
    const TRANSACTION_STATUS_CANCELED = 5; // CAN
    const TRANSACTION_STATUS_EXPIRED = 6; // EXP
    const TRANSACTION_STATUS_ACKNOWLEDGED = 7; // ACK
    const TRANSACTION_STATUS_UNKNOWN = 8;
    const TRANSACTION_STATUS_REFOUNDED = 10; // VOI
    
    public function getId(): int;
    
    public function getAmount(): float;

    public function getCurrency(): int;
    
    public function getStatus(): int;
}