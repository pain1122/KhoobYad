<?php
/**
 * File for class NiksmsWebserviceStructGetSmsDeliveryWithClientIdResponse
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructGetSmsDeliveryWithClientIdResponse originally named GetSmsDeliveryWithClientIdResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructGetSmsDeliveryWithClientIdResponse extends NiksmsWebserviceWsdlClass
{
    /**
     * The GetSmsDeliveryWithClientIdResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel
     */
    public $GetSmsDeliveryWithClientIdResult;
    /**
     * Constructor method for GetSmsDeliveryWithClientIdResponse
     * @see parent::__construct()
     * @param NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel $_getSmsDeliveryWithClientIdResult
     * @return NiksmsWebserviceStructGetSmsDeliveryWithClientIdResponse
     */
    public function __construct($_getSmsDeliveryWithClientIdResult = NULL)
    {
        parent::__construct(array('GetSmsDeliveryWithClientIdResult'=>$_getSmsDeliveryWithClientIdResult),false);
    }
    /**
     * Get GetSmsDeliveryWithClientIdResult value
     * @return NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel|null
     */
    public function getGetSmsDeliveryWithClientIdResult()
    {
        return $this->GetSmsDeliveryWithClientIdResult;
    }
    /**
     * Set GetSmsDeliveryWithClientIdResult value
     * @param NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel $_getSmsDeliveryWithClientIdResult the GetSmsDeliveryWithClientIdResult
     * @return NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel
     */
    public function setGetSmsDeliveryWithClientIdResult($_getSmsDeliveryWithClientIdResult)
    {
        return ($this->GetSmsDeliveryWithClientIdResult = $_getSmsDeliveryWithClientIdResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructGetSmsDeliveryWithClientIdResponse
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
