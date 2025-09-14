<?php
/**
 * File for class NiksmsWebserviceStructArrayOfGetReceiveSmsModel
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructArrayOfGetReceiveSmsModel originally named ArrayOfGetReceiveSmsModel
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructArrayOfGetReceiveSmsModel extends NiksmsWebserviceWsdlClass
{
    /**
     * The GetReceiveSmsModel
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * - nillable : true
     * @var NiksmsWebserviceStructGetReceiveSmsModel
     */
    public $GetReceiveSmsModel;
    /**
     * Constructor method for ArrayOfGetReceiveSmsModel
     * @see parent::__construct()
     * @param NiksmsWebserviceStructGetReceiveSmsModel $_getReceiveSmsModel
     * @return NiksmsWebserviceStructArrayOfGetReceiveSmsModel
     */
    public function __construct($_getReceiveSmsModel = NULL)
    {
        parent::__construct(array('GetReceiveSmsModel'=>$_getReceiveSmsModel),false);
    }
    /**
     * Get GetReceiveSmsModel value
     * @return NiksmsWebserviceStructGetReceiveSmsModel|null
     */
    public function getGetReceiveSmsModel()
    {
        return $this->GetReceiveSmsModel;
    }
    /**
     * Set GetReceiveSmsModel value
     * @param NiksmsWebserviceStructGetReceiveSmsModel $_getReceiveSmsModel the GetReceiveSmsModel
     * @return NiksmsWebserviceStructGetReceiveSmsModel
     */
    public function setGetReceiveSmsModel($_getReceiveSmsModel)
    {
        return ($this->GetReceiveSmsModel = $_getReceiveSmsModel);
    }
    /**
     * Returns the current element
     * @see NiksmsWebserviceWsdlClass::current()
     * @return NiksmsWebserviceStructGetReceiveSmsModel
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see NiksmsWebserviceWsdlClass::item()
     * @param int $_index
     * @return NiksmsWebserviceStructGetReceiveSmsModel
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see NiksmsWebserviceWsdlClass::first()
     * @return NiksmsWebserviceStructGetReceiveSmsModel
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see NiksmsWebserviceWsdlClass::last()
     * @return NiksmsWebserviceStructGetReceiveSmsModel
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see NiksmsWebserviceWsdlClass::last()
     * @param int $_offset
     * @return NiksmsWebserviceStructGetReceiveSmsModel
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see NiksmsWebserviceWsdlClass::getAttributeName()
     * @return string GetReceiveSmsModel
     */
    public function getAttributeName()
    {
        return 'GetReceiveSmsModel';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructArrayOfGetReceiveSmsModel
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
