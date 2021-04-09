<?php

namespace KHBankTools\PaymentGateway;

class PaymentResult
{
    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $statusString;

    /**
     * @var int
     */
    private $responseCode;

    /**
     * @var string|null
     */
    private $responseMessage;

    /**
     * @var int
     */
    private $bankLicenceNumber;

    /**
     * @var string|null
     */
    private $emailAddress;

    public function __construct(string $bankStatusString, ?string $responseCode, ?string $responseMessage, ?string $bankLicenceNumber, ?string $emailAddress)
    {
        $this->status = self::bankStatusStringToTransactionStatus($bankStatusString);
        $this->statusString = $bankStatusString;
        $this->responseCode = (int) $responseCode;
        $this->responseMessage = $responseMessage;
        $this->bankLicenceNumber = (int) $bankLicenceNumber;
        $this->emailAddress = empty($emailAddress) ? null : $emailAddress;
    }
    
    public static function initWithResponeString(string $responseString): self
    {
        $resultArray = \explode("\n", $responseString);
        $resultArray = \array_map('trim', $resultArray);
        
        if ($resultArray[0] === 'ACK' || $resultArray[0] === 'VOI') {
            return new self($resultArray[0], $resultArray[1], $resultArray[2], $resultArray[3], $resultArray[4]);
        }

        return new self($resultArray[0], null, null, null, null);
    }
    
    public static function bankStatusStringToTransactionStatus($string)
    {
        switch ($string) {
            case 'NAK':
                return TransactionInterface::TRANSACTION_STATUS_FAILED;
            case 'UTX':
                return TransactionInterface::TRANSACTION_STATUS_UNKNOWN_ID;
            case 'PEN':
                return TransactionInterface::TRANSACTION_STATUS_PENDING;
            case 'PE2':
                return TransactionInterface::TRANSACTION_STATUS_REFOUND_PENDING;
            case 'ERR':
                return TransactionInterface::TRANSACTION_STATUS_ERROR;
            case 'CAN':
                return TransactionInterface::TRANSACTION_STATUS_CANCELED;
            case 'EXP':
                return TransactionInterface::TRANSACTION_STATUS_EXPIRED;
            case 'ACK':
                return TransactionInterface::TRANSACTION_STATUS_ACKNOWLEDGED;
            case 'VOI':
                return TransactionInterface::TRANSACTION_STATUS_REFOUNDED;
        }

        return TransactionInterface::TRANSACTION_STATUS_UNKNOWN;
    }
    
    public function getTransactionStatus(): int
    {
        return $this->status;
    }
    
    public function getStatusRawString(): string
    {
        return $this->statusString;
    }
    
    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }
    
    public function getResponseRawMessage(): ?string
    {
        return $this->responseMessage;
    }
    
    public function getBankLicenceNumber(): int
    {
        return $this->bankLicenceNumber;
    }
}