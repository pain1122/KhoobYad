<?php
/**
 * File for class NiksmsWebserviceServiceGroup
 * @package NiksmsWebservice
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceServiceGroup originally named Group
 * @package NiksmsWebservice
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceServiceGroup extends NiksmsWebserviceWsdlClass
{
    /**
     * Method to call the operation originally named GroupSms
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructGroupSms $_niksmsWebserviceStructGroupSms
     * @return NiksmsWebserviceStructGroupSmsResponse
     */
    public function GroupSms(NiksmsWebserviceStructGroupSms $_niksmsWebserviceStructGroupSms)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GroupSms($_niksmsWebserviceStructGroupSms));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NiksmsWebserviceWsdlClass::getResult()
     * @return NiksmsWebserviceStructGroupSmsResponse
     */
    public function getResult()
    {
        return parent::getResult();
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
