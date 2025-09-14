<?php
/**
 * File for class NiksmsWebserviceEnumSmsStatus
 * @package NiksmsWebservice
 * @subpackage Enumerations
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceEnumSmsStatus originally named SmsStatus
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Enumerations
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceEnumSmsStatus extends NiksmsWebserviceWsdlClass
{
    /**
     * Constant for value 'NotFound'
     * @return string 'NotFound'
     */
    const VALUE_NOTFOUND = 'NotFound';
    /**
     * Constant for value 'DoNotSend'
     * @return string 'DoNotSend'
     */
    const VALUE_DONOTSEND = 'DoNotSend';
    /**
     * Constant for value 'InQueue'
     * @return string 'InQueue'
     */
    const VALUE_INQUEUE = 'InQueue';
    /**
     * Constant for value 'Sent'
     * @return string 'Sent'
     */
    const VALUE_SENT = 'Sent';
    /**
     * Constant for value 'InsufficientCredit'
     * @return string 'InsufficientCredit'
     */
    const VALUE_INSUFFICIENTCREDIT = 'InsufficientCredit';
    /**
     * Constant for value 'Block'
     * @return string 'Block'
     */
    const VALUE_BLOCK = 'Block';
    /**
     * Constant for value 'NotDeliverdSmsAdvertisingBlock'
     * @return string 'NotDeliverdSmsAdvertisingBlock'
     */
    const VALUE_NOTDELIVERDSMSADVERTISINGBLOCK = 'NotDeliverdSmsAdvertisingBlock';
    /**
     * Constant for value 'NotDeliverdBlackList'
     * @return string 'NotDeliverdBlackList'
     */
    const VALUE_NOTDELIVERDBLACKLIST = 'NotDeliverdBlackList';
    /**
     * Constant for value 'NotDeliverdDelay'
     * @return string 'NotDeliverdDelay'
     */
    const VALUE_NOTDELIVERDDELAY = 'NotDeliverdDelay';
    /**
     * Constant for value 'NotDeliverdCanceled'
     * @return string 'NotDeliverdCanceled'
     */
    const VALUE_NOTDELIVERDCANCELED = 'NotDeliverdCanceled';
    /**
     * Constant for value 'NotDeliverdNoViber'
     * @return string 'NotDeliverdNoViber'
     */
    const VALUE_NOTDELIVERDNOVIBER = 'NotDeliverdNoViber';
    /**
     * Constant for value 'NotDeliverdFiltering'
     * @return string 'NotDeliverdFiltering'
     */
    const VALUE_NOTDELIVERDFILTERING = 'NotDeliverdFiltering';
    /**
     * Constant for value 'WaitingForRecheckInOprator'
     * @return string 'WaitingForRecheckInOprator'
     */
    const VALUE_WAITINGFORRECHECKINOPRATOR = 'WaitingForRecheckInOprator';
    /**
     * Constant for value 'OpratorFault'
     * @return string 'OpratorFault'
     */
    const VALUE_OPRATORFAULT = 'OpratorFault';
    /**
     * Constant for value 'NotDeliveredBlocked'
     * @return string 'NotDeliveredBlocked'
     */
    const VALUE_NOTDELIVEREDBLOCKED = 'NotDeliveredBlocked';
    /**
     * Constant for value 'SendedButStatusNotUpdated'
     * @return string 'SendedButStatusNotUpdated'
     */
    const VALUE_SENDEDBUTSTATUSNOTUPDATED = 'SendedButStatusNotUpdated';
    /**
     * Constant for value 'NotDeliveredDuplicate'
     * @return string 'NotDeliveredDuplicate'
     */
    const VALUE_NOTDELIVEREDDUPLICATE = 'NotDeliveredDuplicate';
    /**
     * Constant for value 'NotDeliveredBlockPanel'
     * @return string 'NotDeliveredBlockPanel'
     */
    const VALUE_NOTDELIVEREDBLOCKPANEL = 'NotDeliveredBlockPanel';
    /**
     * Return true if value is allowed
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTFOUND
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_DONOTSEND
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_INQUEUE
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_SENT
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_INSUFFICIENTCREDIT
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_BLOCK
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDSMSADVERTISINGBLOCK
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDBLACKLIST
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDDELAY
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDCANCELED
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDNOVIBER
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDFILTERING
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_WAITINGFORRECHECKINOPRATOR
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_OPRATORFAULT
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVEREDBLOCKED
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_SENDEDBUTSTATUSNOTUPDATED
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVEREDDUPLICATE
     * @uses NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVEREDBLOCKPANEL
     * @param mixed $_value value
     * @return bool true|false
     */
    public static function valueIsValid($_value)
    {
        return in_array($_value,array(NiksmsWebserviceEnumSmsStatus::VALUE_NOTFOUND,NiksmsWebserviceEnumSmsStatus::VALUE_DONOTSEND,NiksmsWebserviceEnumSmsStatus::VALUE_INQUEUE,NiksmsWebserviceEnumSmsStatus::VALUE_SENT,NiksmsWebserviceEnumSmsStatus::VALUE_INSUFFICIENTCREDIT,NiksmsWebserviceEnumSmsStatus::VALUE_BLOCK,NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDSMSADVERTISINGBLOCK,NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDBLACKLIST,NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDDELAY,NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDCANCELED,NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDNOVIBER,NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVERDFILTERING,NiksmsWebserviceEnumSmsStatus::VALUE_WAITINGFORRECHECKINOPRATOR,NiksmsWebserviceEnumSmsStatus::VALUE_OPRATORFAULT,NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVEREDBLOCKED,NiksmsWebserviceEnumSmsStatus::VALUE_SENDEDBUTSTATUSNOTUPDATED,NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVEREDDUPLICATE,NiksmsWebserviceEnumSmsStatus::VALUE_NOTDELIVEREDBLOCKPANEL));
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
