<?php
/**
 * File for class NiksmsWebserviceStructGetPanelExpireDateResponse
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructGetPanelExpireDateResponse originally named GetPanelExpireDateResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructGetPanelExpireDateResponse extends NiksmsWebserviceWsdlClass
{
    /**
     * The GetPanelExpireDateResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * - nillable : true
     * @var dateTime
     */
    public $GetPanelExpireDateResult;
    /**
     * Constructor method for GetPanelExpireDateResponse
     * @see parent::__construct()
     * @param dateTime $_getPanelExpireDateResult
     * @return NiksmsWebserviceStructGetPanelExpireDateResponse
     */
    public function __construct($_getPanelExpireDateResult)
    {
        parent::__construct(array('GetPanelExpireDateResult'=>$_getPanelExpireDateResult),false);
    }
    /**
     * Get GetPanelExpireDateResult value
     * @return dateTime
     */
    public function getGetPanelExpireDateResult()
    {
        return $this->GetPanelExpireDateResult;
    }
    /**
     * Set GetPanelExpireDateResult value
     * @param dateTime $_getPanelExpireDateResult the GetPanelExpireDateResult
     * @return dateTime
     */
    public function setGetPanelExpireDateResult($_getPanelExpireDateResult)
    {
        return ($this->GetPanelExpireDateResult = $_getPanelExpireDateResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructGetPanelExpireDateResponse
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
