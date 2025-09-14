<?php
/**
 * File for class NiksmsWebserviceServiceReset
 * @package NiksmsWebservice
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceServiceReset originally named Reset
 * @package NiksmsWebservice
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceServiceReset extends NiksmsWebserviceWsdlClass
{
    /**
     * Method to call the operation originally named ResetReceiveSmsVisitedStatus
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructResetReceiveSmsVisitedStatus $_niksmsWebserviceStructResetReceiveSmsVisitedStatus
     * @return NiksmsWebserviceStructResetReceiveSmsVisitedStatusResponse
     */
    public function ResetReceiveSmsVisitedStatus(NiksmsWebserviceStructResetReceiveSmsVisitedStatus $_niksmsWebserviceStructResetReceiveSmsVisitedStatus)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->ResetReceiveSmsVisitedStatus($_niksmsWebserviceStructResetReceiveSmsVisitedStatus));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NiksmsWebserviceWsdlClass::getResult()
     * @return NiksmsWebserviceStructResetReceiveSmsVisitedStatusResponse
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
