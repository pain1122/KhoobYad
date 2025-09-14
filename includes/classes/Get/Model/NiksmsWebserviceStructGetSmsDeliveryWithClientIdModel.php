<?php
/**
 * File for class NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel originally named GetSmsDeliveryWithClientIdModel
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel extends NiksmsWebserviceWsdlClass
{
    /**
     * The SmsStatus
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var NiksmsWebserviceEnumSmsStatus
     */
    public $SmsStatus;
    /**
     * The NiksmsId
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var long
     */
    public $NiksmsId;
    /**
     * Constructor method for GetSmsDeliveryWithClientIdModel
     * @see parent::__construct()
     * @param NiksmsWebserviceEnumSmsStatus $_smsStatus
     * @param long $_niksmsId
     * @return NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel
     */
    public function __construct($_smsStatus,$_niksmsId)
    {
        parent::__construct(array('SmsStatus'=>$_smsStatus,'NiksmsId'=>$_niksmsId),false);
    }
    /**
     * Get SmsStatus value
     * @return NiksmsWebserviceEnumSmsStatus
     */
    public function getSmsStatus()
    {
        return $this->SmsStatus;
    }
    /**
     * Set SmsStatus value
     * @uses NiksmsWebserviceEnumSmsStatus::valueIsValid()
     * @param NiksmsWebserviceEnumSmsStatus $_smsStatus the SmsStatus
     * @return NiksmsWebserviceEnumSmsStatus
     */
    public function setSmsStatus($_smsStatus)
    {
        if(!NiksmsWebserviceEnumSmsStatus::valueIsValid($_smsStatus))
        {
            return false;
        }
        return ($this->SmsStatus = $_smsStatus);
    }
    /**
     * Get NiksmsId value
     * @return long
     */
    public function getNiksmsId()
    {
        return $this->NiksmsId;
    }
    /**
     * Set NiksmsId value
     * @param long $_niksmsId the NiksmsId
     * @return long
     */
    public function setNiksmsId($_niksmsId)
    {
        return ($this->NiksmsId = $_niksmsId);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel
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
