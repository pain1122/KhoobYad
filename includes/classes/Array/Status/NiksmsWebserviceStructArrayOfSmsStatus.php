<?php
/**
 * File for class NiksmsWebserviceStructArrayOfSmsStatus
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructArrayOfSmsStatus originally named ArrayOfSmsStatus
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructArrayOfSmsStatus extends NiksmsWebserviceWsdlClass
{
    /**
     * The SmsStatus
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var NiksmsWebserviceEnumSmsStatus
     */
    public $SmsStatus;
    /**
     * Constructor method for ArrayOfSmsStatus
     * @see parent::__construct()
     * @param NiksmsWebserviceEnumSmsStatus $_smsStatus
     * @return NiksmsWebserviceStructArrayOfSmsStatus
     */
    public function __construct($_smsStatus = NULL)
    {
        parent::__construct(array('SmsStatus'=>$_smsStatus),false);
    }
    /**
     * Get SmsStatus value
     * @return NiksmsWebserviceEnumSmsStatus|null
     */
    public function getSmsStatus()
    {
        return $this->SmsStatus;
    }
    /**
     * Set SmsStatus value
     * @param NiksmsWebserviceEnumSmsStatus $_smsStatus the SmsStatus
     * @return NiksmsWebserviceEnumSmsStatus
     */
    public function setSmsStatus($_smsStatus)
    {
        return ($this->SmsStatus = $_smsStatus);
    }
    /**
     * Returns the current element
     * @see NiksmsWebserviceWsdlClass::current()
     * @return NiksmsWebserviceEnumSmsStatus
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see NiksmsWebserviceWsdlClass::item()
     * @param int $_index
     * @return NiksmsWebserviceEnumSmsStatus
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see NiksmsWebserviceWsdlClass::first()
     * @return NiksmsWebserviceEnumSmsStatus
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see NiksmsWebserviceWsdlClass::last()
     * @return NiksmsWebserviceEnumSmsStatus
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see NiksmsWebserviceWsdlClass::last()
     * @param int $_offset
     * @return NiksmsWebserviceEnumSmsStatus
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Add element to array
     * @see NiksmsWebserviceWsdlClass::add()
     * @uses NiksmsWebserviceEnumSmsStatus::valueIsValid()
     * @param NiksmsWebserviceEnumSmsStatus $_item
     * @return NiksmsWebserviceEnumSmsStatus
     */
    public function add($_item)
    {
        return NiksmsWebserviceEnumSmsStatus::valueIsValid($_item)?parent::add($_item):false;
    }
    /**
     * Returns the attribute name
     * @see NiksmsWebserviceWsdlClass::getAttributeName()
     * @return string SmsStatus
     */
    public function getAttributeName()
    {
        return 'SmsStatus';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructArrayOfSmsStatus
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
