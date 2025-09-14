<?php
/**
 * File for class NiksmsWebserviceEnumOperatorSmsSendType
 * @package NiksmsWebservice
 * @subpackage Enumerations
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceEnumOperatorSmsSendType originally named OperatorSmsSendType
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Enumerations
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceEnumOperatorSmsSendType extends NiksmsWebserviceWsdlClass
{
    /**
     * Constant for value 'Normal'
     * @return string 'Normal'
     */
    const VALUE_NORMAL = 'Normal';
    /**
     * Constant for value 'Flash'
     * @return string 'Flash'
     */
    const VALUE_FLASH = 'Flash';
    /**
     * Return true if value is allowed
     * @uses NiksmsWebserviceEnumOperatorSmsSendType::VALUE_NORMAL
     * @uses NiksmsWebserviceEnumOperatorSmsSendType::VALUE_FLASH
     * @param mixed $_value value
     * @return bool true|false
     */
    public static function valueIsValid($_value)
    {
        return in_array($_value,array(NiksmsWebserviceEnumOperatorSmsSendType::VALUE_NORMAL,NiksmsWebserviceEnumOperatorSmsSendType::VALUE_FLASH));
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
