<?php
/**
 * File for class NiksmsWebserviceEnumSmsReturn
 * @package NiksmsWebservice
 * @subpackage Enumerations
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceEnumSmsReturn originally named SmsReturn
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Enumerations
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceEnumSmsReturn extends NiksmsWebserviceWsdlClass
{
    /**
     * Constant for value 'Successful'
     * @return string 'Successful'
     */
    const VALUE_SUCCESSFUL = 'Successful';
    /**
     * Constant for value 'UnknownError'
     * @return string 'UnknownError'
     */
    const VALUE_UNKNOWNERROR = 'UnknownError';
    /**
     * Constant for value 'InsufficientCredit'
     * @return string 'InsufficientCredit'
     */
    const VALUE_INSUFFICIENTCREDIT = 'InsufficientCredit';
    /**
     * Constant for value 'ForbiddenHours'
     * @return string 'ForbiddenHours'
     */
    const VALUE_FORBIDDENHOURS = 'ForbiddenHours';
    /**
     * Constant for value 'Filtered'
     * @return string 'Filtered'
     */
    const VALUE_FILTERED = 'Filtered';
    /**
     * Constant for value 'NoFilters'
     * @return string 'NoFilters'
     */
    const VALUE_NOFILTERS = 'NoFilters';
    /**
     * Constant for value 'PrivateNumberIsDisable'
     * @return string 'PrivateNumberIsDisable'
     */
    const VALUE_PRIVATENUMBERISDISABLE = 'PrivateNumberIsDisable';
    /**
     * Constant for value 'ArgumentIsNullOrIncorrect'
     * @return string 'ArgumentIsNullOrIncorrect'
     */
    const VALUE_ARGUMENTISNULLORINCORRECT = 'ArgumentIsNullOrIncorrect';
    /**
     * Constant for value 'MessageBodyIsNullOrEmpty'
     * @return string 'MessageBodyIsNullOrEmpty'
     */
    const VALUE_MESSAGEBODYISNULLOREMPTY = 'MessageBodyIsNullOrEmpty';
    /**
     * Constant for value 'PrivateNumberIsIncorrect'
     * @return string 'PrivateNumberIsIncorrect'
     */
    const VALUE_PRIVATENUMBERISINCORRECT = 'PrivateNumberIsIncorrect';
    /**
     * Constant for value 'ReceptionNumberIsIncorrect'
     * @return string 'ReceptionNumberIsIncorrect'
     */
    const VALUE_RECEPTIONNUMBERISINCORRECT = 'ReceptionNumberIsIncorrect';
    /**
     * Constant for value 'SentTypeIsIncorrect'
     * @return string 'SentTypeIsIncorrect'
     */
    const VALUE_SENTTYPEISINCORRECT = 'SentTypeIsIncorrect';
    /**
     * Constant for value 'Warning'
     * @return string 'Warning'
     */
    const VALUE_WARNING = 'Warning';
    /**
     * Constant for value 'PanelIsBlocked'
     * @return string 'PanelIsBlocked'
     */
    const VALUE_PANELISBLOCKED = 'PanelIsBlocked';
    /**
     * Constant for value 'SiteUpdating'
     * @return string 'SiteUpdating'
     */
    const VALUE_SITEUPDATING = 'SiteUpdating';
    /**
     * Return true if value is allowed
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_SUCCESSFUL
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_UNKNOWNERROR
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_INSUFFICIENTCREDIT
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_FORBIDDENHOURS
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_FILTERED
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_NOFILTERS
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_PRIVATENUMBERISDISABLE
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_ARGUMENTISNULLORINCORRECT
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_MESSAGEBODYISNULLOREMPTY
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_PRIVATENUMBERISINCORRECT
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_RECEPTIONNUMBERISINCORRECT
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_SENTTYPEISINCORRECT
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_WARNING
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_PANELISBLOCKED
     * @uses NiksmsWebserviceEnumSmsReturn::VALUE_SITEUPDATING
     * @param mixed $_value value
     * @return bool true|false
     */
    public static function valueIsValid($_value)
    {
        return in_array($_value,array(NiksmsWebserviceEnumSmsReturn::VALUE_SUCCESSFUL,NiksmsWebserviceEnumSmsReturn::VALUE_UNKNOWNERROR,NiksmsWebserviceEnumSmsReturn::VALUE_INSUFFICIENTCREDIT,NiksmsWebserviceEnumSmsReturn::VALUE_FORBIDDENHOURS,NiksmsWebserviceEnumSmsReturn::VALUE_FILTERED,NiksmsWebserviceEnumSmsReturn::VALUE_NOFILTERS,NiksmsWebserviceEnumSmsReturn::VALUE_PRIVATENUMBERISDISABLE,NiksmsWebserviceEnumSmsReturn::VALUE_ARGUMENTISNULLORINCORRECT,NiksmsWebserviceEnumSmsReturn::VALUE_MESSAGEBODYISNULLOREMPTY,NiksmsWebserviceEnumSmsReturn::VALUE_PRIVATENUMBERISINCORRECT,NiksmsWebserviceEnumSmsReturn::VALUE_RECEPTIONNUMBERISINCORRECT,NiksmsWebserviceEnumSmsReturn::VALUE_SENTTYPEISINCORRECT,NiksmsWebserviceEnumSmsReturn::VALUE_WARNING,NiksmsWebserviceEnumSmsReturn::VALUE_PANELISBLOCKED,NiksmsWebserviceEnumSmsReturn::VALUE_SITEUPDATING));
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
