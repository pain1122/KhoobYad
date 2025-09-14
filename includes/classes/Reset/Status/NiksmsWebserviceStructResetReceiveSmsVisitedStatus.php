<?php
/**
 * File for class NiksmsWebserviceStructResetReceiveSmsVisitedStatus
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructResetReceiveSmsVisitedStatus originally named ResetReceiveSmsVisitedStatus
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructResetReceiveSmsVisitedStatus extends NiksmsWebserviceWsdlClass
{
    /**
     * The startDate
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * - nillable : true
     * @var dateTime
     */
    public $startDate;
    /**
     * The endDate
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * - nillable : true
     * @var dateTime
     */
    public $endDate;
    /**
     * The security
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructAuthenticationModel
     */
    public $security;
    /**
     * Constructor method for ResetReceiveSmsVisitedStatus
     * @see parent::__construct()
     * @param dateTime $_startDate
     * @param dateTime $_endDate
     * @param NiksmsWebserviceStructAuthenticationModel $_security
     * @return NiksmsWebserviceStructResetReceiveSmsVisitedStatus
     */
    public function __construct($_startDate,$_endDate,$_security = NULL)
    {
        parent::__construct(array('startDate'=>$_startDate,'endDate'=>$_endDate,'security'=>$_security),false);
    }
    /**
     * Get startDate value
     * @return dateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
    /**
     * Set startDate value
     * @param dateTime $_startDate the startDate
     * @return dateTime
     */
    public function setStartDate($_startDate)
    {
        return ($this->startDate = $_startDate);
    }
    /**
     * Get endDate value
     * @return dateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
    /**
     * Set endDate value
     * @param dateTime $_endDate the endDate
     * @return dateTime
     */
    public function setEndDate($_endDate)
    {
        return ($this->endDate = $_endDate);
    }
    /**
     * Get security value
     * @return NiksmsWebserviceStructAuthenticationModel|null
     */
    public function getSecurity()
    {
        return $this->security;
    }
    /**
     * Set security value
     * @param NiksmsWebserviceStructAuthenticationModel $_security the security
     * @return NiksmsWebserviceStructAuthenticationModel
     */
    public function setSecurity($_security)
    {
        return ($this->security = $_security);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructResetReceiveSmsVisitedStatus
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
