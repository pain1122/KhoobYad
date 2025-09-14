<?php
/**
 * File for the class which returns the class map definition
 * @package NiksmsWebservice
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * Class which returns the class map definition by the static method NiksmsWebserviceClassMap::classMap()
 * @package NiksmsWebservice
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceClassMap
{
    /**
     * This method returns the array containing the mapping between WSDL structs and generated classes
     * This array is sent to the SoapClient when calling the WS
     * @return array
     */
    final public static function classMap()
    {
        return array (
  'ArrayOfGetReceiveSmsModel' => 'NiksmsWebserviceStructArrayOfGetReceiveSmsModel',
  'ArrayOfLong' => 'NiksmsWebserviceStructArrayOfLong',
  'ArrayOfSmsStatus' => 'NiksmsWebserviceStructArrayOfSmsStatus',
  'ArrayOfString' => 'NiksmsWebserviceStructArrayOfString',
  'AuthenticationModel' => 'NiksmsWebserviceStructAuthenticationModel',
  'GetCredit' => 'NiksmsWebserviceStructGetCredit',
  'GetCreditResponse' => 'NiksmsWebserviceStructGetCreditResponse',
  'GetDiscountCredit' => 'NiksmsWebserviceStructGetDiscountCredit',
  'GetDiscountCreditResponse' => 'NiksmsWebserviceStructGetDiscountCreditResponse',
  'GetPanelExpireDate' => 'NiksmsWebserviceStructGetPanelExpireDate',
  'GetPanelExpireDateResponse' => 'NiksmsWebserviceStructGetPanelExpireDateResponse',
  'GetReceiveSms' => 'NiksmsWebserviceStructGetReceiveSms',
  'GetReceiveSmsModel' => 'NiksmsWebserviceStructGetReceiveSmsModel',
  'GetReceiveSmsResponse' => 'NiksmsWebserviceStructGetReceiveSmsResponse',
  'GetSenderNumbers' => 'NiksmsWebserviceStructGetSenderNumbers',
  'GetSenderNumbersResponse' => 'NiksmsWebserviceStructGetSenderNumbersResponse',
  'GetServertime' => 'NiksmsWebserviceStructGetServertime',
  'GetServertimeResponse' => 'NiksmsWebserviceStructGetServertimeResponse',
  'GetSmsDelivery' => 'NiksmsWebserviceStructGetSmsDelivery',
  'GetSmsDeliveryResponse' => 'NiksmsWebserviceStructGetSmsDeliveryResponse',
  'GetSmsDeliveryWithClientId' => 'NiksmsWebserviceStructGetSmsDeliveryWithClientId',
  'GetSmsDeliveryWithClientIdModel' => 'NiksmsWebserviceStructGetSmsDeliveryWithClientIdModel',
  'GetSmsDeliveryWithClientIdResponse' => 'NiksmsWebserviceStructGetSmsDeliveryWithClientIdResponse',
  'GroupSms' => 'NiksmsWebserviceStructGroupSms',
  'GroupSmsModel' => 'NiksmsWebserviceStructGroupSmsModel',
  'GroupSmsResponse' => 'NiksmsWebserviceStructGroupSmsResponse',
  'OperatorSmsSendType' => 'NiksmsWebserviceEnumOperatorSmsSendType',
  'PtpSms' => 'NiksmsWebserviceStructPtpSms',
  'PtpSmsModel' => 'NiksmsWebserviceStructPtpSmsModel',
  'PtpSmsResponse' => 'NiksmsWebserviceStructPtpSmsResponse',
  'ResetReceiveSmsVisitedStatus' => 'NiksmsWebserviceStructResetReceiveSmsVisitedStatus',
  'ResetReceiveSmsVisitedStatusResponse' => 'NiksmsWebserviceStructResetReceiveSmsVisitedStatusResponse',
  'ReturnSmsResult' => 'NiksmsWebserviceStructReturnSmsResult',
  'SmsReturn' => 'NiksmsWebserviceEnumSmsReturn',
  'SmsStatus' => 'NiksmsWebserviceEnumSmsStatus',
);
    }
}
