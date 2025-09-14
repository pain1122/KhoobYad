<?php
/**
 * File for class NiksmsWebserviceStructGetDiscountCreditResponse
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructGetDiscountCreditResponse originally named GetDiscountCreditResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructGetDiscountCreditResponse extends NiksmsWebserviceWsdlClass
{
    /**
     * The GetDiscountCreditResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var int
     */
    public $GetDiscountCreditResult;
    /**
     * Constructor method for GetDiscountCreditResponse
     * @see parent::__construct()
     * @param int $_getDiscountCreditResult
     * @return NiksmsWebserviceStructGetDiscountCreditResponse
     */
    public function __construct($_getDiscountCreditResult)
    {
        parent::__construct(array('GetDiscountCreditResult'=>$_getDiscountCreditResult),false);
    }
    /**
     * Get GetDiscountCreditResult value
     * @return int
     */
    public function getGetDiscountCreditResult()
    {
        return $this->GetDiscountCreditResult;
    }
    /**
     * Set GetDiscountCreditResult value
     * @param int $_getDiscountCreditResult the GetDiscountCreditResult
     * @return int
     */
    public function setGetDiscountCreditResult($_getDiscountCreditResult)
    {
        return ($this->GetDiscountCreditResult = $_getDiscountCreditResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructGetDiscountCreditResponse
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
