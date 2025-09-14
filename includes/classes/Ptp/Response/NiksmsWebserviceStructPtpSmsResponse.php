<?php
/**
 * File for class NiksmsWebserviceStructPtpSmsResponse
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructPtpSmsResponse originally named PtpSmsResponse
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructPtpSmsResponse extends NiksmsWebserviceWsdlClass
{
    /**
     * The PtpSmsResult
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructReturnSmsResult
     */
    public $PtpSmsResult;
    /**
     * Constructor method for PtpSmsResponse
     * @see parent::__construct()
     * @param NiksmsWebserviceStructReturnSmsResult $_ptpSmsResult
     * @return NiksmsWebserviceStructPtpSmsResponse
     */
    public function __construct($_ptpSmsResult = NULL)
    {
        parent::__construct(array('PtpSmsResult'=>$_ptpSmsResult),false);
    }
    /**
     * Get PtpSmsResult value
     * @return NiksmsWebserviceStructReturnSmsResult|null
     */
    public function getPtpSmsResult()
    {
        return $this->PtpSmsResult;
    }
    /**
     * Set PtpSmsResult value
     * @param NiksmsWebserviceStructReturnSmsResult $_ptpSmsResult the PtpSmsResult
     * @return NiksmsWebserviceStructReturnSmsResult
     */
    public function setPtpSmsResult($_ptpSmsResult)
    {
        return ($this->PtpSmsResult = $_ptpSmsResult);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructPtpSmsResponse
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
