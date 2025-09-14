<?php
/**
 * File for class NiksmsWebserviceStructGetSenderNumbersResponse
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructGetSenderNumbersResponse originally named GetSenderNumbersResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructGetSenderNumbersResponse extends NiksmsWebserviceWsdlClass
{
    /**
     * The GetSenderNumbersResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructArrayOfString
     */
    public $GetSenderNumbersResult;
    /**
     * Constructor method for GetSenderNumbersResponse
     * @see parent::__construct()
     * @param NiksmsWebserviceStructArrayOfString $_getSenderNumbersResult
     * @return NiksmsWebserviceStructGetSenderNumbersResponse
     */
    public function __construct($_getSenderNumbersResult = NULL)
    {
        parent::__construct(array('GetSenderNumbersResult'=>($_getSenderNumbersResult instanceof NiksmsWebserviceStructArrayOfString)?$_getSenderNumbersResult:new NiksmsWebserviceStructArrayOfString($_getSenderNumbersResult)),false);
    }
    /**
     * Get GetSenderNumbersResult value
     * @return NiksmsWebserviceStructArrayOfString|null
     */
    public function getGetSenderNumbersResult()
    {
        return $this->GetSenderNumbersResult;
    }
    /**
     * Set GetSenderNumbersResult value
     * @param NiksmsWebserviceStructArrayOfString $_getSenderNumbersResult the GetSenderNumbersResult
     * @return NiksmsWebserviceStructArrayOfString
     */
    public function setGetSenderNumbersResult($_getSenderNumbersResult)
    {
        return ($this->GetSenderNumbersResult = $_getSenderNumbersResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructGetSenderNumbersResponse
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
