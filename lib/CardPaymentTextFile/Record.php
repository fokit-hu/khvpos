<?php

namespace KHBankTools\CardPaymentTextFile;

class Record
{
    // kartya vagy tranzakcio azonosito
    protected $cardTransactionNumber;
    
    /**
     * setter for cardTransactionNumber
     *
     * @param mixed 
     * @return self
     */
    public function setCardTransactionNumber($value)
    {
        $this->cardTransactionNumber = $value;
        return $this;
    }
    
    /**
     * getter for cardTransactionNumber
     * 
     * @return mixed return value for 
     */
    public function getCardTransactionNumber()
    {
        return $this->cardTransactionNumber;
    }
    
    /**
     * @var \DateTime
     */
    protected $transactionDate;
    
    /**
     * setter for transactionDate
     *
     * @param \DateTime 
     * @return self
     */
    public function setTransactionDate(\DateTime $value = null)
    {
        $this->transactionDate = $value;
        return $this;
    }
    
    /**
     * getter for transactionDate
     * 
     * @return \DateTime return value for transactionDate
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }
    
    /**
     * Tr.Összeg (99999999V99, ha az összeg=0, akkor csak “0”)
     *
     * @var integer
     */
    protected $transactionAmount;
    
    /**
     * setter for transactionAmount
     *
     * @param mixed 
     * @return self
     */
    public function setTransactionAmount($value)
    {
        $this->transactionAmount = $value;
        return $this;
    }
    
    /**
     * getter for transactionAmount
     * 
     * @return mixed return value for 
     */
    public function getTransactionAmount()
    {
        return $this->transactionAmount;
    }
    
    /**
     * Jutalék összege (99999999V99, ha az összeg=0, akkor csak “0”)
     *
     * @var integer
     */
    protected $merchantServiceChargeAmount;
    
    /**
     * setter for merchantServiceChargeAmount
     *
     * @param mixed 
     * @return self
     */
    public function setMerchantServiceChargeAmount($value)
    {
        $this->merchantServiceChargeAmount = $value;
        return $this;
    }
    
    /**
     * getter for merchantServiceChargeAmount
     * 
     * @return mixed return value for 
     */
    public function getMerchantServiceChargeAmount()
    {
        return $this->merchantServiceChargeAmount;
    }
    
    /**
     * Visszatérítés összege (99999999V99, ha az összeg=0, akkor csak “0”)
     *
     * @var integer
     */
    protected $reimbursemetAmount;
    
    /**
     * setter for reimbursemetAmount
     *
     * @param mixed 
     * @return self
     */
    public function setReimbursemetAmount($value)
    {
        $this->reimbursemetAmount = $value;
        return $this;
    }
    
    /**
     * getter for reimbursemetAmount
     * 
     * @return mixed return value for 
     */
    public function getReimbursemetAmount()
    {
        return $this->reimbursemetAmount;
    }
    
    /**
     * Netto összeg (=Tr. összeg-Jutalék+Visszatérítés) (99999999V99, ha az összeg=0, akkor csak “0”)
     *
     * @var integer
     */
    protected $netAmount;
    
    /**
     * setter for netAmount
     *
     * @param mixed 
     * @return self
     */
    public function setNetAmount($value)
    {
        $this->netAmount = $value;
        return $this;
    }
    
    /**
     * getter for netAmount
     * 
     * @return mixed return value for 
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     * Esetunkben a null a nem kozolt.
     * Eredetileg: MIF (fizetett multilateral interchange fee) (99999999V99, ha az összeg=0, akkor csak “0”; “n.k.”=nem közölt)
     *
     * @var integer
     */
    protected $mifAmount = null;
    
    /**
     * setter for mifAmount
     *
     * @param mixed 
     * @return self
     */
    public function setMifAmount($value)
    {
        $this->mifAmount = $value;
        return $this;
    }
    
    /**
     * getter for mifAmount
     * 
     * @return mixed return value for 
     */
    public function getMifAmount()
    {
        return $this->mifAmount;
    }
    
    /**
     * Esetunkben a null a nem kozolt.
     * egyéb jutalék (99999999V99, ha az összeg=0, akkor csak “0”; “n.k.”=nem közölt)
     * angolban: MSC-MIF
     *
     * @var integer
     */
    protected $otherServiceChargeAmount;
    
    /**
     * setter for otherServiceChargeAmount
     *
     * @param mixed 
     * @return self
     */
    public function setOtherServiceChargeAmount($value)
    {
        $this->otherServiceChargeAmount = $value;
        return $this;
    }
    
    /**
     * getter for otherServiceChargeAmount
     * 
     * @return mixed return value for 
     */
    public function getOtherServiceChargeAmount()
    {
        return $this->otherServiceChargeAmount;
    }
    
    /**
     * Esetunkben a null a -
     * Terminál azonosító / Terminal ID; Lehet “-“ is / It may be ‘-‘
     *
     * @var integer
     */
    protected $terminalId;
    
    /**
     * setter for terminalId
     *
     * @param mixed 
     * @return self
     */
    public function setTerminalId($value)
    {
        $this->terminalId = $value;
        return $this;
    }
    
    /**
     * getter for terminalId
     * 
     * @return mixed return value for 
     */
    public function getTerminalId()
    {
        return $this->terminalId;
    }
    
    /**
     * Kereskedő azonosító
     *
     * @var integer
     */
    protected $merchantId;
    
    /**
     * setter for merchantId
     *
     * @param mixed 
     * @return self
     */
    public function setMerchantId($value)
    {
        $this->merchantId = $value;
        return $this;
    }
    
    /**
     * getter for merchantId
     * 
     * @return mixed return value for 
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }
    
    /**
     * Tranzakció sorszám
     *
     * @var integer
     */
    protected $numberOfTransaction;
    
    /**
     * setter for numberOfTransaction
     *
     * @param mixed 
     * @return self
     */
    public function setNumberOfTransaction($value)
    {
        $this->numberOfTransaction = $value;
        return $this;
    }
    
    /**
     * getter for numberOfTransaction
     * 
     * @return mixed return value for 
     */
    public function getNumberOfTransaction()
    {
        return $this->numberOfTransaction;
    }
    
    /**
     * Esetunkben a null a -
     * Authorizációs kód / Authorisation code; Lehet “-“ is / It may be ‘-‘
     *
     * @var integer
     */
    protected $authorisationCode;
    
    /**
     * setter for authorisationCode
     *
     * @param mixed 
     * @return self
     */
    public function setAuthorisationCode($value)
    {
        $this->authorisationCode = $value;
        return $this;
    }
    
    /**
     * getter for authorisationCode
     * 
     * @return mixed return value for 
     */
    public function getAuthorisationCode()
    {
        return $this->authorisationCode;
    }
    
    const CURRENCY_HUF_CODE = 348;
    const CURRENCY_USD_CODE = 840;
    const CURRENCY_EUR_CODE = 978;
        
    /**
     * @var integer
     */
    protected $currencyIsoCode;
    
    /**
     * setter for currencyIsoCode
     *
     * @param mixed 
     * @return self
     */
    public function setCurrencyIsoCode($value)
    {
        $this->currencyIsoCode = $value;
        return $this;
    }
    
    /**
     * getter for currencyIsoCode
     * 
     * @return mixed return value for 
     */
    public function getCurrencyIsoCode()
    {
        return $this->currencyIsoCode;
    }
    
    const STATUS_CODE_PAYED = 10; // elszámolt / payed
    const STATUS_CODE_BLOCKED = 11; // visszatartott / blocked
    const STATUS_CODE_REFUND = 12; // áruvisszavét / refund
    const STATUS_CODE_ACCEPTED_BLOCKED = 20; // Visszatartott elszámolva / accepted after blocking
    const STATUS_CODE_REFUSED_BLOCKED = 30; // Visszatartott elutasitva / refused after blocking
    const STATUS_CODE_REDEBITED = 40; // Visszaterhelt / re-debited transactions (eg. chargeback)
    
    /**
     * @var integer
     */
    protected $statusCode;
    
    /**
     * setter for statusCode
     *
     * @param mixed 
     * @return self
     */
    public function setStatusCode($value)
    {
        $this->statusCode = $value;
        return $this;
    }
    
    /**
     * getter for statusCode
     * 
     * @return mixed return value for 
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    
    /**
     * BATCH azonosító 
     *
     * @var string
     */
    protected $batchNumber;
    
    /**
     * setter for batchNumber
     *
     * @param mixed 
     * @return self
     */
    public function setBatchNumber($value)
    {
        $this->batchNumber = $value;
        return $this;
    }
    
    /**
     * getter for batchNumber
     * 
     * @return mixed return value for 
     */
    public function getBatchNumber()
    {
        return $this->batchNumber;
    }
    
    /**
     * @var string
     */
    protected $numberOfTransfer;
    
    /**
     * setter for numberOfTransfer
     *
     * @param mixed 
     * @return self
     */
    public function setNumberOfTransfer($value)
    {
        $this->numberOfTransfer = $value;
        return $this;
    }
    
    /**
     * getter for numberOfTransfer
     * 
     * @return mixed return value for 
     */
    public function getNumberOfTransfer()
    {
        return $this->numberOfTransfer;
    }
    
    const CARD_BRAND_TYPE_MAESTRO = 1;
    const CARD_BRAND_TYPE_MASTERCARD = 2;
    const CARD_BRAND_TYPE_JCB = 3;
    const CARD_BRAND_TYPE_VISA = 4;
    const CARD_BRAND_TYPE_VISAELECTRON = 5;
    const CARD_BRAND_TYPE_VPAY = 6;
    const CARD_BRAND_TYPE_OTHER = 0;
    
    /**
     * @var integer
     */
    protected $cardBrandType;
    
    /**
     * setter for cardBrandType
     *
     * @param mixed 
     * @return self
     */
    public function setCardBrandType($value)
    {
        $this->cardBrandType = $value;
        return $this;
    }
    
    /**
     * getter for cardBrandType
     * 
     * @return mixed return value for 
     */
    public function getCardBrandType()
    {
        return $this->cardBrandType;
    }
    
    const CARD_KIND_DEBIT = 1; // Betéti kártya
    const CARD_KIND_CREDIT = 2; // Hitelkártya
    const CARD_KIND_PREPAID = 3; // Előre fizetett kártya
    const CARD_KIND_CHARGE = 4; // Terhelési kártya
    const CARD_KIND_DEFERRED_DEBIT = 5; // Halasztott fizetésű kártya
    const CARD_KIND_OTHER = 0;

    /**
     * card kind
     *
     * @var integer
     */
    protected $cardKind;
    
    /**
     * setter for cardKind
     *
     * @param mixed 
     * @return self
     */
    public function setCardKind($value)
    {
        $this->cardKind = $value;
        return $this;
    }
    
    /**
     * getter for cardKind
     * 
     * @return mixed return value for 
     */
    public function getCardKind()
    {
        return $this->cardKind;
    }
    
    const CARD_USAGE_TYPE_CONSUMER = 1;
    const CARD_USAGE_TYPE_CORPORATE = 2;
    const CARD_USAGE_TYPE_OTHER = 1;
    
    /**
     * @var integer
     */
    protected $cardUsageType;
    
    /**
     * setter for cardUsageType
     *
     * @param mixed 
     * @return self
     */
    public function setCardUsageType($value)
    {
        $this->cardUsageType = $value;
        return $this;
    }
    
    /**
     * getter for cardUsageType
     * 
     * @return mixed return value for 
     */
    public function getCardUsageType()
    {
        return $this->cardUsageType;
    }
    
    const CARD_ISSUANCE_REGION_KH = 1; // K&H Bank
    const CARD_ISSUANCE_REGION_DOMESTIC = 2; // Hazai
    const CARD_ISSUANCE_REGION_IEEA = 3; // Nemzetözi, Európai Uniós tagország
    const CARD_ISSUANCE_REGION_IEU = 4; // Nemzetözi, európai régiós, de nem EU tagállam
    const CARD_ISSUANCE_REGION_INT = 5; // Nemzetközi, európai régión kívüli
    const CARD_ISSUANCE_REGION_OTHER = 0; // egyeb
    
    /**
     * @var integer
     */
    protected $cardRegionOfIssuance;
    
    /**
     * setter for cardRegionOfIssuance
     *
     * @param mixed 
     * @return self
     */
    public function setCardRegionOfIssuance($value)
    {
        $this->cardRegionOfIssuance = $value;
        return $this;
    }
    
    /**
     * getter for cardRegionOfIssuance
     * 
     * @return mixed return value for 
     */
    public function getCardRegionOfIssuance()
    {
        return $this->cardRegionOfIssuance;
    }
    
    /**
     * @var string
     */
    protected $paymentId;
    
    /**
     * setter for paymentId
     *
     * @param mixed 
     * @return self
     */
    public function setPaymentId($value)
    {
        $this->paymentId = $value;
        return $this;
    }
    
    /**
     * getter for paymentId
     * 
     * @return mixed return value for 
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }
    
}
