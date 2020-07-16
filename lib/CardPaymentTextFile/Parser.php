<?php

namespace KHBankTools\CardPaymentTextFile;

class Parser
{
    public function __construct()
    {
    }
    
    public function parseString($string)
    {
        $this->contentString = $string;
        $this->position = 0;
        
        $nor = $this->parseHeader();
        $this->parseRecords($nor);
        
        return $this->header;
    }
    
    private function parseHeader()
    {
        $this->header = new Header();
        $createdAt = $this->parseDateTimeType();
        $this->header->setFileGeneratedAt($createdAt);
        
        $fileNumber = $this->parseStringType(8);
        $numberOfRecords = $this->parseIntegerType(8);

        $notificationDate = $this->parseDateType();
        $this->jump(2); // \n \r

        $this->header->setFileNumber($fileNumber);
        $this->header->setNotificationDate($notificationDate);
        
        return $numberOfRecords;
    }
    
    private function parseRecords($numberOfRecords)
    {
        for ($i = 0; $i < $numberOfRecords; $i++) {
            $transactionId = $this->parseStringType(19);
            $transactionDateTime = $this->parseDateTimeType();

            $transactionAmount = $this->parseIntegerType(10); // tr osszeg
            $merchantServiceChargeAmount = $this->parseIntegerType(10); // jutalek
            $reimbursemetAmount = $this->parseStringType(10); // visszaterites
            $netAmount = $this->parseIntegerType(10); // netto
            $mifAmount = trim($this->parseStringType(10)); // mif
            $this->jump(10); // placeholder
            $otherServiceChargeAmount = trim($this->parseIntegerType(10)); // egyeb jutalek

            $terminalId = trim($this->parseStringType(10)); // terminal azonosito
            $merchantId = $this->parseIntegerType(10); // kereskedo azonosito
            $numberOfTransaction = $this->parseIntegerType(12); // tranzakcio sorszam
            $authorisationCode = trim($this->parseStringType(9)); // auth kod
            $currencyIsoCode = $this->parseIntegerType(3); // curr code

            $statusCode = $this->parseIntegerType(2); // status code

            $batchNumber = trim($this->parseStringType(12)); // batch azon.
            $numberOfTransfer = trim($this->parseStringType(10)); // utalas azon.

            $cardBrandType = $this->parseIntegerType(1); // kartya tipus / brand
            $cardType = trim($this->parseStringType(1)); // kartya tipus
            $cardType2 = $this->parseIntegerType(1); // kartya tipus com / cons
            $cardRegionOfIssuance = $this->parseIntegerType(1); // kartya tipus com / cons

            $this->jump(4*20); // blank

            $paymentId = trim($this->parseStringType(20)); // kartya tipus com / cons
            
            $this->jump(2); // \n \r
            
            $record = new Record();
            $record->setCardTransactionNumber((int)$transactionId);
            $record->setTransactionDate($transactionDateTime);

            $record->setTransactionAmount((int)$transactionAmount);
            $record->setMerchantServiceChargeAmount((int)$merchantServiceChargeAmount);
            $record->setReimbursemetAmount((int)$reimbursemetAmount);
            $record->setNetAmount((int)$netAmount);
            if ($mifAmount !== 'n.k.') {
                $record->setMifAmount((int)$mifAmount);
            }
            if ($otherServiceChargeAmount !== 'n.k.') {
                $record->setOtherServiceChargeAmount((int)$otherServiceChargeAmount);
            }
            if ($terminalId !== '-') {
                $record->setTerminalId((int)$terminalId);
            }
            if ($terminalId !== '-') {
                $record->setTerminalId((int)$terminalId);
            }
            $record->setMerchantId($merchantId);
            $record->setNumberOfTransaction($numberOfTransaction);
            if ($authorisationCode !== '-') {
                $record->setAuthorisationCode($authorisationCode);
            }

            $record->setCurrencyIsoCode($currencyIsoCode);
            $record->setStatusCode($statusCode);

            $record->setBatchNumber($batchNumber);
            $record->setNumberOfTransfer($numberOfTransfer);

            $record->setCardBrandType($cardBrandType);

            switch ($cardType) {
                case 'B':
                    $record->setCardKind(Record::CARD_KIND_DEBIT);
                    break;
                case 'C':
                    $record->setCardKind(Record::CARD_KIND_CREDIT);
                    break;
                case 'P':
                    $record->setCardKind(Record::CARD_KIND_PREPAID);
                    break;
                case 'H':
                    $record->setCardKind(Record::CARD_KIND_CHARGE);
                    break;
                case 'R':
                    $record->setCardKind(Record::CARD_KIND_DEFERRED_DEBIT);
                    break;
                case '0':
                    $record->setCardKind(Record::CARD_KIND_OTHER);
                    break;
                
                default:
                    throw new \Exception('unknown card kind');
                    break;
            }

            $record->setCardUsageType($cardType2);
            $record->setCardRegionOfIssuance($cardRegionOfIssuance);
            $record->setPaymentId($paymentId);
            
            $this->header->addRecord($record);
        }
    }
    
    private function jump($size)
    {
        $this->position += $size;
    }
    
    private function dumpCurrent($size)
    {
        var_dump($this->parseStringType($size));
    }

    private function parseIntegerType($size)
    {
        return (int) $this->parseStringType($size);
    }
    
    private function parseStringType($size)
    {
        $string = substr($this->contentString, $this->position, $size);
        $this->position += $size;
        return $string;
    }
    
    private function parseDateType()
    {
        $dateString = substr($this->contentString, $this->position, 8);
        $this->position += 8;
        
        return new \DateTime(substr($dateString, 0, 4) .'-'. substr($dateString, 4, 2) .'-'. substr($dateString, 6, 2));
    }
    
    private function parseTimeType()
    {
        $timeString = substr($this->contentString, $this->position, 4);
        $this->position += 4;
        
        $timestamp = substr($timeString, 0, 2) * 3600 + substr($timeString, 2, 2) * 60;
        
        return new \DateTime('@'. $timestamp);
    }
    
    private function parseDateTimeType()
    {
        $size = 12;
        $dateString = substr($this->contentString, $this->position, $size);
        $this->position += $size;
        
        $date = substr($dateString, 0, 4) .'-'. substr($dateString, 4, 2) .'-'. substr($dateString, 6, 2);
        $time = substr($dateString, 8, 2) .':'. substr($dateString, 10, 2) .':00';
        
        return new \DateTime($date .' '. $time);
    }
    
    public function parseFile($filePath)
    {
        if (!is_file($filePath)) {
            throw new \Exception('file does not exists');
        }
        
        return $this->parseString(file_get_contents($filePath));
    }
}
