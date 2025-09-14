<?php
/**
 * File for class NiksmsWebserviceServiceGet
 * @package NiksmsWebservice
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceServiceGet originally named Get
 * @package NiksmsWebservice
 * @subpackage Services
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceServiceGet extends NiksmsWebserviceWsdlClass
{
    /**
     * Method to call the operation originally named GetCredit
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructGetCredit $_niksmsWebserviceStructGetCredit
     * @return NiksmsWebserviceStructGetCreditResponse
     */
    public function GetCredit(NiksmsWebserviceStructGetCredit $_niksmsWebserviceStructGetCredit)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetCredit($_niksmsWebserviceStructGetCredit));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetDiscountCredit
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructGetDiscountCredit $_niksmsWebserviceStructGetDiscountCredit
     * @return NiksmsWebserviceStructGetDiscountCreditResponse
     */
    public function GetDiscountCredit(NiksmsWebserviceStructGetDiscountCredit $_niksmsWebserviceStructGetDiscountCredit)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetDiscountCredit($_niksmsWebserviceStructGetDiscountCredit));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetPanelExpireDate
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructGetPanelExpireDate $_niksmsWebserviceStructGetPanelExpireDate
     * @return NiksmsWebserviceStructGetPanelExpireDateResponse
     */
    public function GetPanelExpireDate(NiksmsWebserviceStructGetPanelExpireDate $_niksmsWebserviceStructGetPanelExpireDate)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetPanelExpireDate($_niksmsWebserviceStructGetPanelExpireDate));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetReceiveSms
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructGetReceiveSms $_niksmsWebserviceStructGetReceiveSms
     * @return NiksmsWebserviceStructGetReceiveSmsResponse
     */
    public function GetReceiveSms(NiksmsWebserviceStructGetReceiveSms $_niksmsWebserviceStructGetReceiveSms)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetReceiveSms($_niksmsWebserviceStructGetReceiveSms));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetSmsDelivery
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructGetSmsDelivery $_niksmsWebserviceStructGetSmsDelivery
     * @return NiksmsWebserviceStructGetSmsDeliveryResponse
     */
    public function GetSmsDelivery(NiksmsWebserviceStructGetSmsDelivery $_niksmsWebserviceStructGetSmsDelivery)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetSmsDelivery($_niksmsWebserviceStructGetSmsDelivery));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetSmsDeliveryWithClientId
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructGetSmsDeliveryWithClientId $_niksmsWebserviceStructGetSmsDeliveryWithClientId
     * @return NiksmsWebserviceStructGetSmsDeliveryWithClientIdResponse
     */
    public function GetSmsDeliveryWithClientId(NiksmsWebserviceStructGetSmsDeliveryWithClientId $_niksmsWebserviceStructGetSmsDeliveryWithClientId)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetSmsDeliveryWithClientId($_niksmsWebserviceStructGetSmsDeliveryWithClientId));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetServertime
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructGetServertime $_niksmsWebserviceStructGetServertime
     * @return NiksmsWebserviceStructGetServertimeResponse
     */
    public function GetServertime(NiksmsWebserviceStructGetServertime $_niksmsWebserviceStructGetServertime)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetServertime($_niksmsWebserviceStructGetServertime));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Method to call the operation originally named GetSenderNumbers
     * @uses NiksmsWebserviceWsdlClass::getSoapClient()
     * @uses NiksmsWebserviceWsdlClass::setResult()
     * @uses NiksmsWebserviceWsdlClass::saveLastError()
     * @param NiksmsWebserviceStructGetSenderNumbers $_niksmsWebserviceStructGetSenderNumbers
     * @return NiksmsWebserviceStructGetSenderNumbersResponse
     */
    public function GetSenderNumbers(NiksmsWebserviceStructGetSenderNumbers $_niksmsWebserviceStructGetSenderNumbers)
    {
        try
        {
            return $this->setResult(self::getSoapClient()->GetSenderNumbers($_niksmsWebserviceStructGetSenderNumbers));
        }
        catch(SoapFault $soapFault)
        {
            return !$this->saveLastError(__METHOD__,$soapFault);
        }
    }
    /**
     * Returns the result
     * @see NiksmsWebserviceWsdlClass::getResult()
     * @return NiksmsWebserviceStructGetCreditResponse|NiksmsWebserviceStructGetDiscountCreditResponse|NiksmsWebserviceStructGetPanelExpireDateResponse|NiksmsWebserviceStructGetReceiveSmsResponse|NiksmsWebserviceStructGetSenderNumbersResponse|NiksmsWebserviceStructGetServertimeResponse|NiksmsWebserviceStructGetSmsDeliveryResponse|NiksmsWebserviceStructGetSmsDeliveryWithClientIdResponse
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
