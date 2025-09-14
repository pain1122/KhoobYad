<?php
/**
 * File for class NiksmsWebserviceStructGetSmsDeliveryWithClientId
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructGetSmsDeliveryWithClientId originally named GetSmsDeliveryWithClientId
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructGetSmsDeliveryWithClientId extends NiksmsWebserviceWsdlClass
{
    /**
     * The yourId
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var long
     */
    public $yourId;
    /**
     * The security
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructAuthenticationModel
     */
    public $security;
    /**
     * Constructor method for GetSmsDeliveryWithClientId
     * @see parent::__construct()
     * @param long $_yourId
     * @param NiksmsWebserviceStructAuthenticationModel $_security
     * @return NiksmsWebserviceStructGetSmsDeliveryWithClientId
     */
    public function __construct($_yourId,$_security = NULL)
    {
        parent::__construct(array('yourId'=>$_yourId,'security'=>$_security),false);
    }
    /**
     * Get yourId value
     * @return long
     */
    public function getYourId()
    {
        return $this->yourId;
    }
    /**
     * Set yourId value
     * @param long $_yourId the yourId
     * @return long
     */
    public function setYourId($_yourId)
    {
        return ($this->yourId = $_yourId);
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
     * @return NiksmsWebserviceStructGetSmsDeliveryWithClientId
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
