<?php
class Grizzly_Productaction_Model_System_Config_Source_View
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'assigncat', 'label'=>Mage::helper('adminhtml')->__('Assign To Category / Categories')),
            array('value' => 'removecat', 'label'=>Mage::helper('adminhtml')->__('Remove From Category / Categories')),
            array('value' => 'chanattrset', 'label'=>Mage::helper('adminhtml')->__('Change Attribute Set')),
            array('value' => 'modpricef', 'label'=>Mage::helper('adminhtml')->__('Modify Price (By Flat Number)')),
            array('value' => 'modpriceper', 'label'=>Mage::helper('adminhtml')->__('Modify Price (By Percentage)')),
            array('value' => 'modspclpricef', 'label'=>Mage::helper('adminhtml')->__('Modify Special Price (By Flat Number)')),
            array('value' => 'modspclpricep', 'label'=>Mage::helper('adminhtml')->__('Modify Special Price (By Percentage)')),
            array('value' => 'modspclprcf', 'label'=>Mage::helper('adminhtml')->__('Modify Special Price using price(By Flat Number)')),
            array('value' => 'modspclprcp', 'label'=>Mage::helper('adminhtml')->__('Modify Special Price using price(By Percentage)')),
            array('value' => 'modvis', 'label'=>Mage::helper('adminhtml')->__('Modify Visibility')),
            array('value' => 'modstatus', 'label'=>Mage::helper('adminhtml')->__('Modify Status')),
            array('value' => 'cpyoptions', 'label'=>Mage::helper('adminhtml')->__('Copy Product Options')),
            array('value' => 'cpymimages', 'label'=>Mage::helper('adminhtml')->__('Copy and Merge With Existing Product Images')),
            array('value' => 'cpyrimages', 'label'=>Mage::helper('adminhtml')->__('Copy and Replace Existing Product Images')),
            array('value' => 'assignupprd', 'label'=>Mage::helper('adminhtml')->__('Assign as Up-sells Product / Products')),
            array('value' => 'assigncrossprd', 'label'=>Mage::helper('adminhtml')->__('Assign as Cross-sells Product / Products')),
            array('value' => 'assignrelprd', 'label'=>Mage::helper('adminhtml')->__('Assign as Related Product / Products')),
        );
    }

    public function toArray()
    {
        return array(
            "assigncat" => Mage::helper('adminhtml')->__('Assign To Category / Categories'),
            "removecat" => Mage::helper('adminhtml')->__('Remove From Category / Categories'),
            "chanattrset" => Mage::helper('adminhtml')->__('Change Attribute Set'),
            "modpricef" => Mage::helper('adminhtml')->__('Modify Price - By Flat Number'),
            "modpriceper" => Mage::helper('adminhtml')->__('Modify Price - By Percentage'),
            "modspclpricef" => Mage::helper('adminhtml')->__('Modify Special Price - By Flat Number'),
            "modspclpricep" => Mage::helper('adminhtml')->__('Modify Special Price - By Percentage'),
            "modspclprcf" => Mage::helper('adminhtml')->__('Modify Special Price using price - By Flat Number'),
            "modspclprcp" => Mage::helper('adminhtml')->__('Modify Special Price using price - By Percentage'),
            "modvis" => Mage::helper('adminhtml')->__('Modify Visibility'),
            "modstatus" => Mage::helper('adminhtml')->__('Modify Status'),
            "cpyoptions" => Mage::helper('adminhtml')->__('Copy Product Options'),
            "cpymimages" => Mage::helper('adminhtml')->__('Copy and Merge With Existing Product Images'),
            "cpyrimages" => Mage::helper('adminhtml')->__('Copy and Replace Existing Product Images'),
            "assignupprd" => Mage::helper('adminhtml')->__('Assign as Up-sells Product / Products'),
            "assigncrossprd" => Mage::helper('adminhtml')->__('Assign as Cross-sells Product / Products'),
            "assignrelprd" => Mage::helper('adminhtml')->__('Assign as Related Product / Products'),
        );
    }
    
}

?>