<?php
/**
 * File for class NiksmsWebserviceStructResetReceiveSmsVisitedStatusResponse
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructResetReceiveSmsVisitedStatusResponse originally named ResetReceiveSmsVisitedStatusResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructResetReceiveSmsVisitedStatusResponse extends NiksmsWebserviceWsdlClass
{
    /**
     * The ResetReceiveSmsVisitedStatusResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var boolean
     */
    public $ResetReceiveSmsVisitedStatusResult;
    /**
     * Constructor method for ResetReceiveSmsVisitedStatusResponse
     * @see parent::__construct()
     * @param boolean $_resetReceiveSmsVisitedStatusResult
     * @return NiksmsWebserviceStructResetReceiveSmsVisitedStatusResponse
     */
    public function __construct($_resetReceiveSmsVisitedStatusResult)
    {
        parent::__construct(array('ResetReceiveSmsVisitedStatusResult'=>$_resetReceiveSmsVisitedStatusResult),false);
    }
    /**
     * Get ResetReceiveSmsVisitedStatusResult value
     * @return boolean
     */
    public function getResetReceiveSmsVisitedStatusResult()
    {
        return $this->ResetReceiveSmsVisitedStatusResult;
    }
    /**
     * Set ResetReceiveSmsVisitedStatusResult value
     * @param boolean $_resetReceiveSmsVisitedStatusResult the ResetReceiveSmsVisitedStatusResult
     * @return boolean
     */
    public function setResetReceiveSmsVisitedStatusResult($_resetReceiveSmsVisitedStatusResult)
    {
        return ($this->ResetReceiveSmsVisitedStatusResult = $_resetReceiveSmsVisitedStatusResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructResetReceiveSmsVisitedStatusResponse
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
