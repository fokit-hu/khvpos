<?php

namespace KHTools\VPos;

class PaymentResult
{
    /**
     * @var int
     */
    private int $status;

    /**
     * @var string
     */
    private string $statusString;

    /**
     * @var int
     */
    private int $responseCode;

    /**
     * @var string|null
     */
    private ?string $responseMessage = null;

    /**
     * @var int
     */
    private int $bankLicenceNumber;

    /**
     * @var string|null
     */
    private ?string $emailAddress = null;

    public function __construct(string $bankStatusString, ?string $responseCode, ?string $responseMessage, ?string $bankLicenceNumber, ?string $emailAddress)
    {
        $this->status = self::bankStatusStringToTransactionStatus($bankStatusString);
        $this->statusString = $bankStatusString;
        $this->responseCode = (int) $responseCode;
        $this->responseMessage = $responseMessage;
        $this->bankLicenceNumber = (int) $bankLicenceNumber;
        $this->emailAddress = empty($emailAddress) ? null : $emailAddress;
    }
    
    public static function initWithResponseString(string $responseString): self
    {
        if (empty($responseString)) {
            throw new \LogicException('PaymentResult::initWithResponseString() Response string is empty.');
        }
        
        $resultArray = \explode("\n", $responseString);
        $resultArray = \array_map('trim', $resultArray);
        
        if ($resultArray[0] === 'ACK' || $resultArray[0] === 'VOI') {
            if (($numberOfLines = count($resultArray)) < 5) {
                throw new \LogicException(sprintf('PaymentResult::initWithResponseString() Number of lines (%d) in response string is wrong: "%s"', $numberOfLines, $responseString));
            }
            
            return new self($resultArray[0], $resultArray[1], $resultArray[2], $resultArray[3], $resultArray[4]);
        }

        return new self($resultArray[0], null, null, null, null);
    }
    
    public static function bankStatusStringToTransactionStatus($string): int
    {
        switch ($string) {
            case 'NAK':
                return TransactionInterface::TRANSACTION_STATUS_FAILED;
            case 'UTX':
                return TransactionInterface::TRANSACTION_STATUS_UNKNOWN_ID;
            case 'PEN':
                return TransactionInterface::TRANSACTION_STATUS_PENDING;
            case 'PE2':
                return TransactionInterface::TRANSACTION_STATUS_REFUND_PENDING;
            case 'ERR':
                return TransactionInterface::TRANSACTION_STATUS_ERROR;
            case 'CAN':
                return TransactionInterface::TRANSACTION_STATUS_CANCELED;
            case 'EXP':
                return TransactionInterface::TRANSACTION_STATUS_EXPIRED;
            case 'ACK':
                return TransactionInterface::TRANSACTION_STATUS_ACKNOWLEDGED;
            case 'VOI':
                return TransactionInterface::TRANSACTION_STATUS_REFUNDED;
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