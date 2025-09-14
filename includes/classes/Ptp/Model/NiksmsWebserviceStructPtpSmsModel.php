<?php
/**
 * File for class NiksmsWebserviceStructPtpSmsModel
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructPtpSmsModel originally named PtpSmsModel
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructPtpSmsModel extends NiksmsWebserviceWsdlClass
{
    /**
     * The SendOn
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var dateTime
     */
    public $SendOn;
    /**
     * The SendType
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var NiksmsWebserviceEnumOperatorSmsSendType
     */
    public $SendType;
    /**
     * The SenderNumber
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $SenderNumber;
    /**
     * The Numbers
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructArrayOfString
     */
    public $Numbers;
    /**
     * The YourMessageId
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructArrayOfLong
     */
    public $YourMessageId;
    /**
     * The Message
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructArrayOfString
     */
    public $Message;
    /**
     * Constructor method for PtpSmsModel
     * @see parent::__construct()
     * @param dateTime $_sendOn
     * @param NiksmsWebserviceEnumOperatorSmsSendType $_sendType
     * @param string $_senderNumber
     * @param NiksmsWebserviceStructArrayOfString $_numbers
     * @param NiksmsWebserviceStructArrayOfLong $_yourMessageId
     * @param NiksmsWebserviceStructArrayOfString $_message
     * @return NiksmsWebserviceStructPtpSmsModel
     */
    public function __construct($_sendOn,$_sendType,$_senderNumber = NULL,$_numbers = NULL,$_yourMessageId = NULL,$_message = NULL)
    {
        parent::__construct(array('SendOn'=>$_sendOn,'SendType'=>$_sendType,'SenderNumber'=>$_senderNumber,'Numbers'=>($_numbers instanceof NiksmsWebserviceStructArrayOfString)?$_numbers:new NiksmsWebserviceStructArrayOfString($_numbers),'YourMessageId'=>($_yourMessageId instanceof NiksmsWebserviceStructArrayOfLong)?$_yourMessageId:new NiksmsWebserviceStructArrayOfLong($_yourMessageId),'Message'=>($_message instanceof NiksmsWebserviceStructArrayOfString)?$_message:new NiksmsWebserviceStructArrayOfString($_message)),false);
    }
    /**
     * Get SendOn value
     * @return dateTime
     */
    public function getSendOn()
    {
        return $this->SendOn;
    }
    /**
     * Set SendOn value
     * @param dateTime $_sendOn the SendOn
     * @return dateTime
     */
    public function setSendOn($_sendOn)
    {
        return ($this->SendOn = $_sendOn);
    }
    /**
     * Get SendType value
     * @return NiksmsWebserviceEnumOperatorSmsSendType
     */
    public function getSendType()
    {
        return $this->SendType;
    }
    /**
     * Set SendType value
     * @uses NiksmsWebserviceEnumOperatorSmsSendType::valueIsValid()
     * @param NiksmsWebserviceEnumOperatorSmsSendType $_sendType the SendType
     * @return NiksmsWebserviceEnumOperatorSmsSendType
     */
    public function setSendType($_sendType)
    {
        if(!NiksmsWebserviceEnumOperatorSmsSendType::valueIsValid($_sendType))
        {
            return false;
        }
        return ($this->SendType = $_sendType);
    }
    /**
     * Get SenderNumber value
     * @return string|null
     */
    public function getSenderNumber()
    {
        return $this->SenderNumber;
    }
    /**
     * Set SenderNumber value
     * @param string $_senderNumber the SenderNumber
     * @return string
     */
    public function setSenderNumber($_senderNumber)
    {
        return ($this->SenderNumber = $_senderNumber);
    }
    /**
     * Get Numbers value
     * @return NiksmsWebserviceStructArrayOfString|null
     */
    public function getNumbers()
    {
        return $this->Numbers;
    }
    /**
     * Set Numbers value
     * @param NiksmsWebserviceStructArrayOfString $_numbers the Numbers
     * @return NiksmsWebserviceStructArrayOfString
     */
    public function setNumbers($_numbers)
    {
        return ($this->Numbers = $_numbers);
    }
    /**
     * Get YourMessageId value
     * @return NiksmsWebserviceStructArrayOfLong|null
     */
    public function getYourMessageId()
    {
        return $this->YourMessageId;
    }
    /**
     * Set YourMessageId value
     * @param NiksmsWebserviceStructArrayOfLong $_yourMessageId the YourMessageId
     * @return NiksmsWebserviceStructArrayOfLong
     */
    public function setYourMessageId($_yourMessageId)
    {
        return ($this->YourMessageId = $_yourMessageId);
    }
    /**
     * Get Message value
     * @return NiksmsWebserviceStructArrayOfString|null
     */
    public function getMessage()
    {
        return $this->Message;
    }
    /**
     * Set Message value
     * @param NiksmsWebserviceStructArrayOfString $_message the Message
     * @return NiksmsWebserviceStructArrayOfString
     */
    public function setMessage($_message)
    {
        return ($this->Message = $_message);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructPtpSmsModel
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
