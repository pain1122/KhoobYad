<?php
/**
 * File for class NiksmsWebserviceStructGetSmsDeliveryResponse
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructGetSmsDeliveryResponse originally named GetSmsDeliveryResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructGetSmsDeliveryResponse extends NiksmsWebserviceWsdlClass
{
    /**
     * The GetSmsDeliveryResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructArrayOfSmsStatus
     */
    public $GetSmsDeliveryResult;
    /**
     * Constructor method for GetSmsDeliveryResponse
     * @see parent::__construct()
     * @param NiksmsWebserviceStructArrayOfSmsStatus $_getSmsDeliveryResult
     * @return NiksmsWebserviceStructGetSmsDeliveryResponse
     */
    public function __construct($_getSmsDeliveryResult = NULL)
    {
        parent::__construct(array('GetSmsDeliveryResult'=>($_getSmsDeliveryResult instanceof NiksmsWebserviceStructArrayOfSmsStatus)?$_getSmsDeliveryResult:new NiksmsWebserviceStructArrayOfSmsStatus($_getSmsDeliveryResult)),false);
    }
    /**
     * Get GetSmsDeliveryResult value
     * @return NiksmsWebserviceStructArrayOfSmsStatus|null
     */
    public function getGetSmsDeliveryResult()
    {
        return $this->GetSmsDeliveryResult;
    }
    /**
     * Set GetSmsDeliveryResult value
     * @param NiksmsWebserviceStructArrayOfSmsStatus $_getSmsDeliveryResult the GetSmsDeliveryResult
     * @return NiksmsWebserviceStructArrayOfSmsStatus
     */
    public function setGetSmsDeliveryResult($_getSmsDeliveryResult)
    {
        return ($this->GetSmsDeliveryResult = $_getSmsDeliveryResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructGetSmsDeliveryResponse
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
