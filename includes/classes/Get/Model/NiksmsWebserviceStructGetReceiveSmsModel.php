<?php
/**
 * File for class NiksmsWebserviceStructGetReceiveSmsModel
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructGetReceiveSmsModel originally named GetReceiveSmsModel
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructGetReceiveSmsModel extends NiksmsWebserviceWsdlClass
{
    /**
     * The SenderNumber
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var long
     */
    public $SenderNumber;
    /**
     * The Id
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $Id;
    /**
     * The ReceiveDate
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var dateTime
     */
    public $ReceiveDate;
    /**
     * The IsRelayed
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $IsRelayed;
    /**
     * The Message
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Message;
    /**
     * The ReceiveNumber
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $ReceiveNumber;
    /**
     * Constructor method for GetReceiveSmsModel
     * @see parent::__construct()
     * @param long $_senderNumber
     * @param int $_id
     * @param dateTime $_receiveDate
     * @param boolean $_isRelayed
     * @param string $_message
     * @param string $_receiveNumber
     * @return NiksmsWebserviceStructGetReceiveSmsModel
     */
    public function __construct($_senderNumber,$_id,$_receiveDate,$_isRelayed,$_message = NULL,$_receiveNumber = NULL)
    {
        parent::__construct(array('SenderNumber'=>$_senderNumber,'Id'=>$_id,'ReceiveDate'=>$_receiveDate,'IsRelayed'=>$_isRelayed,'Message'=>$_message,'ReceiveNumber'=>$_receiveNumber),false);
    }
    /**
     * Get SenderNumber value
     * @return long
     */
    public function getSenderNumber()
    {
        return $this->SenderNumber;
    }
    /**
     * Set SenderNumber value
     * @param long $_senderNumber the SenderNumber
     * @return long
     */
    public function setSenderNumber($_senderNumber)
    {
        return ($this->SenderNumber = $_senderNumber);
    }
    /**
     * Get Id value
     * @return int
     */
    public function getId()
    {
        return $this->Id;
    }
    /**
     * Set Id value
     * @param int $_id the Id
     * @return int
     */
    public function setId($_id)
    {
        return ($this->Id = $_id);
    }
    /**
     * Get ReceiveDate value
     * @return dateTime
     */
    public function getReceiveDate()
    {
        return $this->ReceiveDate;
    }
    /**
     * Set ReceiveDate value
     * @param dateTime $_receiveDate the ReceiveDate
     * @return dateTime
     */
    public function setReceiveDate($_receiveDate)
    {
        return ($this->ReceiveDate = $_receiveDate);
    }
    /**
     * Get IsRelayed value
     * @return boolean
     */
    public function getIsRelayed()
    {
        return $this->IsRelayed;
    }
    /**
     * Set IsRelayed value
     * @param boolean $_isRelayed the IsRelayed
     * @return boolean
     */
    public function setIsRelayed($_isRelayed)
    {
        return ($this->IsRelayed = $_isRelayed);
    }
    /**
     * Get Message value
     * @return string|null
     */
    public function getMessage()
    {
        return $this->Message;
    }
    /**
     * Set Message value
     * @param string $_message the Message
     * @return string
     */
    public function setMessage($_message)
    {
        return ($this->Message = $_message);
    }
    /**
     * Get ReceiveNumber value
     * @return string|null
     */
    public function getReceiveNumber()
    {
        return $this->ReceiveNumber;
    }
    /**
     * Set ReceiveNumber value
     * @param string $_receiveNumber the ReceiveNumber
     * @return string
     */
    public function setReceiveNumber($_receiveNumber)
    {
        return ($this->ReceiveNumber = $_receiveNumber);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructGetReceiveSmsModel
     */
    public static function __set_state(array $_array,$_className = __CLASS__)
    {
        return parent::__set_state($_array,$_className);
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
