<?php
/**
 * File for class NiksmsWebserviceStructReturnSmsResult
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructReturnSmsResult originally named ReturnSmsResult
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructReturnSmsResult extends NiksmsWebserviceWsdlClass
{
    /**
     * The Status
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 1
     * @var NiksmsWebserviceEnumSmsReturn
     */
    public $Status;
    /**
     * The Id
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $Id;
    /**
     * The WarningMessage
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var string
     */
    public $WarningMessage;
    /**
     * The NikIds
     * Meta informations extracted from the WSDL
     * - maxOccurs : 1
     * - minOccurs : 0
     * @var NiksmsWebserviceStructArrayOfLong
     */
    public $NikIds;
    /**
     * Constructor method for ReturnSmsResult
     * @see parent::__construct()
     * @param NiksmsWebserviceEnumSmsReturn $_status
     * @param string $_id
     * @param string $_warningMessage
     * @param NiksmsWebserviceStructArrayOfLong $_nikIds
     * @return NiksmsWebserviceStructReturnSmsResult
     */
    public function __construct($_status,$_id = NULL,$_warningMessage = NULL,$_nikIds = NULL)
    {
        parent::__construct(array('Status'=>$_status,'Id'=>$_id,'WarningMessage'=>$_warningMessage,'NikIds'=>($_nikIds instanceof NiksmsWebserviceStructArrayOfLong)?$_nikIds:new NiksmsWebserviceStructArrayOfLong($_nikIds)),false);
    }
    /**
     * Get Status value
     * @return NiksmsWebserviceEnumSmsReturn
     */
    public function getStatus()
    {
        return $this->Status;
    }
    /**
     * Set Status value
     * @uses NiksmsWebserviceEnumSmsReturn::valueIsValid()
     * @param NiksmsWebserviceEnumSmsReturn $_status the Status
     * @return NiksmsWebserviceEnumSmsReturn
     */
    public function setStatus($_status)
    {
        if(!NiksmsWebserviceEnumSmsReturn::valueIsValid($_status))
        {
            return false;
        }
        return ($this->Status = $_status);
    }
    /**
     * Get Id value
     * @return string|null
     */
    public function getId()
    {
        return $this->Id;
    }
    /**
     * Set Id value
     * @param string $_id the Id
     * @return string
     */
    public function setId($_id)
    {
        return ($this->Id = $_id);
    }
    /**
     * Get WarningMessage value
     * @return string|null
     */
    public function getWarningMessage()
    {
        return $this->WarningMessage;
    }
    /**
     * Set WarningMessage value
     * @param string $_warningMessage the WarningMessage
     * @return string
     */
    public function setWarningMessage($_warningMessage)
    {
        return ($this->WarningMessage = $_warningMessage);
    }
    /**
     * Get NikIds value
     * @return NiksmsWebserviceStructArrayOfLong|null
     */
    public function getNikIds()
    {
        return $this->NikIds;
    }
    /**
     * Set NikIds value
     * @param NiksmsWebserviceStructArrayOfLong $_nikIds the NikIds
     * @return NiksmsWebserviceStructArrayOfLong
     */
    public function setNikIds($_nikIds)
    {
        return ($this->NikIds = $_nikIds);
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructReturnSmsResult
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
