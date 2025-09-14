<?php
/**
 * File for class NiksmsWebserviceStructGetReceiveSmsResponse
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructGetReceiveSmsResponse originally named GetReceiveSmsResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructGetReceiveSmsResponse extends NiksmsWebserviceWsdlClass
{
    /**
     * The GetReceiveSmsResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructArrayOfGetReceiveSmsModel
     */
    public $GetReceiveSmsResult;
    /**
     * Constructor method for GetReceiveSmsResponse
     * @see parent::__construct()
     * @param NiksmsWebserviceStructArrayOfGetReceiveSmsModel $_getReceiveSmsResult
     * @return NiksmsWebserviceStructGetReceiveSmsResponse
     */
    public function __construct($_getReceiveSmsResult = NULL)
    {
        parent::__construct(array('GetReceiveSmsResult'=>($_getReceiveSmsResult instanceof NiksmsWebserviceStructArrayOfGetReceiveSmsModel)?$_getReceiveSmsResult:new NiksmsWebserviceStructArrayOfGetReceiveSmsModel($_getReceiveSmsResult)),false);
    }
    /**
     * Get GetReceiveSmsResult value
     * @return NiksmsWebserviceStructArrayOfGetReceiveSmsModel|null
     */
    public function getGetReceiveSmsResult()
    {
        return $this->GetReceiveSmsResult;
    }
    /**
     * Set GetReceiveSmsResult value
     * @param NiksmsWebserviceStructArrayOfGetReceiveSmsModel $_getReceiveSmsResult the GetReceiveSmsResult
     * @return NiksmsWebserviceStructArrayOfGetReceiveSmsModel
     */
    public function setGetReceiveSmsResult($_getReceiveSmsResult)
    {
        return ($this->GetReceiveSmsResult = $_getReceiveSmsResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructGetReceiveSmsResponse
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
