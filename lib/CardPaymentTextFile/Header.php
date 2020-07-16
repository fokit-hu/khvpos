<?php

namespace KHBankTools\CardPaymentTextFile;

class Header
{
    /**
     * @var \DateTime
     */
    protected $fileGeneratedAt;
    
    /**
     * setter for fileGeneratedAt
     *
     * @param \DateTime 
     * @return self
     */
    public function setFileGeneratedAt(\DateTime $value = null)
    {
        $this->fileGeneratedAt = $value;
        return $this;
    }
    
    /**
     * getter for fileGeneratedAt
     * 
     * @return \DateTime return value for fileGeneratedAt
     */
    public function getFileGeneratedAt()
    {
        return $this->fileGeneratedAt;
    }
    
    /**
     * @var \DateTime
     */
    protected $notificationDate;
    
    /**
     * setter for notificationDate
     *
     * @param \DateTime 
     * @return self
     */
    public function setNotificationDate(\DateTime $value = null)
    {
        $this->notificationDate = $value;
        return $this;
    }
    
    /**
     * getter for notificationDate
     * 
     * @return \DateTime return value for notificationDate
     */
    public function getNotificationDate()
    {
        return $this->notificationDate;
    }
    
    /**
     * @var integer
     */
    protected $fileNumber;
    
    /**
     * setter for fileNumber
     *
     * @param mixed 
     * @return self
     */
    public function setFileNumber($value)
    {
        $this->fileNumber = $value;
        return $this;
    }
    
    /**
     * getter for fileNumber
     * 
     * @return mixed return value for 
     */
    public function getFileNumber()
    {
        return $this->fileNumber;
    }
    
    /**
     * Collection of Record // Field side
     */
    protected $records = [];
    
    /**
     * Add record
     *
     * @param AppBundle\Document\record record
     */
    public function addRecord(Record $record)
    {
        $this->records[] = $record;
        return $this;
    }
    
    /**
     * Getter for records
     * 
     * @return mixed return value for Doctrine\Common\Collections\ArrayCollection|null
     */
    public function getRecords()
    {
        return $this->records;
    }
}
