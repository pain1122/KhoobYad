<?php
/**
 * File for class NiksmsWebserviceStructPtpSms
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructPtpSms originally named PtpSms
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructPtpSms extends NiksmsWebserviceWsdlClass
{
    /**
     * The security
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructAuthenticationModel
     */
    public $security;
    /**
     * The model
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructPtpSmsModel
     */
    public $model;
    /**
     * Constructor method for PtpSms
     * @see parent::__construct()
     * @param NiksmsWebserviceStructAuthenticationModel $_security
     * @param NiksmsWebserviceStructPtpSmsModel $_model
     * @return NiksmsWebserviceStructPtpSms
     */
    public function __construct($_security = NULL,$_model = NULL)
    {
        parent::__construct(array('security'=>$_security,'model'=>$_model),false);
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
     * Get model value
     * @return NiksmsWebserviceStructPtpSmsModel|null
     */
    public function getModel()
    {
        return $this->model;
    }
    /**
     * Set model value
     * @param NiksmsWebserviceStructPtpSmsModel $_model the model
     * @return NiksmsWebserviceStructPtpSmsModel
     */
    public function setModel($_model)
    {
        return ($this->model = $_model);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructPtpSms
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
