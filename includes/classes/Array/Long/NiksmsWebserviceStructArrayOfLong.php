<?php
/**
 * File for class NiksmsWebserviceStructArrayOfLong
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
/**
 * This class stands for NiksmsWebserviceStructArrayOfLong originally named ArrayOfLong
 * Meta informations extracted from the WSDL
 * - from schema : {@link http://niksms.com:1370/NiksmsWebservice.svc?xsd=xsd0}
 * @package NiksmsWebservice
 * @subpackage Structs
 * @author WsdlToPhp Team <contact@wsdltophp.com>
 * @version 20150429-01
 * @date 2016-01-03
 */
class NiksmsWebserviceStructArrayOfLong extends NiksmsWebserviceWsdlClass
{
    /**
     * The long
     * Meta informations extracted from the WSDL
     * - maxOccurs : unbounded
     * - minOccurs : 0
     * @var long
     */
    public $long;
    /**
     * Constructor method for ArrayOfLong
     * @see parent::__construct()
     * @param long $_long
     * @return NiksmsWebserviceStructArrayOfLong
     */
    public function __construct($_long = NULL)
    {
        parent::__construct(array('long'=>$_long),false);
    }
    /**
     * Get long value
     * @return long|null
     */
    public function getLong()
    {
        return $this->long;
    }
    /**
     * Set long value
     * @param long $_long the long
     * @return long
     */
    public function setLong($_long)
    {
        return ($this->long = $_long);
    }
    /**
     * Returns the current element
     * @see NiksmsWebserviceWsdlClass::current()
     * @return long
     */
    public function current()
    {
        return parent::current();
    }
    /**
     * Returns the indexed element
     * @see NiksmsWebserviceWsdlClass::item()
     * @param int $_index
     * @return long
     */
    public function item($_index)
    {
        return parent::item($_index);
    }
    /**
     * Returns the first element
     * @see NiksmsWebserviceWsdlClass::first()
     * @return long
     */
    public function first()
    {
        return parent::first();
    }
    /**
     * Returns the last element
     * @see NiksmsWebserviceWsdlClass::last()
     * @return long
     */
    public function last()
    {
        return parent::last();
    }
    /**
     * Returns the element at the offset
     * @see NiksmsWebserviceWsdlClass::last()
     * @param int $_offset
     * @return long
     */
    public function offsetGet($_offset)
    {
        return parent::offsetGet($_offset);
    }
    /**
     * Returns the attribute name
     * @see NiksmsWebserviceWsdlClass::getAttributeName()
     * @return string long
     */
    public function getAttributeName()
    {
        return 'long';
    }
    /**
     * Method called when an object has been exported with var_export() functions
     * It allows to return an object instantiated with the values
     * @see NiksmsWebserviceWsdlClass::__set_state()
     * @uses NiksmsWebserviceWsdlClass::__set_state()
     * @param array $_array the exported values
     * @return NiksmsWebserviceStructArrayOfLong
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
