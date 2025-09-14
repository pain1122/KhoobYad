<?php
/**
 * File for class NiksmsWebserviceServicePtp
 * @package NiksmsWebservice
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceServicePtp originally named Ptp
 * @package NiksmsWebservice
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceServicePtp extends NiksmsWebserviceWsdlClass
{
    /**
     * Method to call the operation originally named PtpSms
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructPtpSms $_niksmsWebserviceStructPtpSms
     * @return NiksmsWebserviceStructPtpSmsResponse
     */
    public function PtpSms(NiksmsWebserviceStructPtpSms $_niksmsWebserviceStructPtpSms)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->PtpSms($_niksmsWebserviceStructPtpSms));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NiksmsWebserviceWsdlClass::getResult()
     * @return NiksmsWebserviceStructPtpSmsResponse
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
