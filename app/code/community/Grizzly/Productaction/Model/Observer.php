<?php
class Grizzly_Productaction_Model_Observer
{
    public function addAssignAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        
        $enable_module = Mage::getStoreConfig('grizzly_productaction/general/enable_module',Mage::app()->getStore());
        $select_action = Mage::getStoreConfig('grizzly_productaction/general/select_action',Mage::app()->getStore());
        //var_dump($select_action);
        //exit();
        /*if (strpos($select_action,'0') !== false) 
        {
        echo 'Assign to category enable';
        } */     
        if($enable_module)

         {   
                if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
                    && $block->getRequest()->getControllerName() == 'catalog_product')
                {
                    
                    //assign to category

                    if (strpos($select_action,'assigncat') !== false)
                    {  
                        $block->addItem('assigncat', array(
                            'label' => 'Assign To Category / Categories',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/assigncat'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'category',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Category IDs'),
                                 
                             )
                            )
                        ));

                    }

                    //remove from category
                    
                    if (strpos($select_action,'removecat') !== false)
                    { 
                        $block->addItem('removefromcat', array(
                            'label' => 'Remove From Category / Categories',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/removecat'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'removecat',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Category IDs'),
                                 
                             )
                            )
                        ));
                    }


                    
                    $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                    ->load()
                    ->toOptionHash();

                    //change attribute set
                    if (strpos($select_action,'chanattrset') !== false)
                    {

                        $block->addItem('attrset', array
                        (
                            'label' => 'Change Attribute Set',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/attrset'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'attributeset',
                                 'type'     => 'select',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Attribute Sets'),
                                 'values'   => $sets,
                                                             
                             )
                            )
                        ));
                    }
                    

                     //Increase/Decrease base price (flat)
                    if (strpos($select_action,'modpricef') !== false)
                    {

                        $block->addItem('incflatprice', array(
                            'label' => 'Modify Price (By Flat Number)',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/incpriceflat'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'incflat',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Enter value (Flat)'),
                                 
                             )
                            )
                        ));

                    }

                     //Increase/Decrease base price (percentage)
                    if (strpos($select_action,'modpriceper') !== false)
                    {

                        $block->addItem('percentprice', array(
                            'label' => 'Modify Price (By Percentage)',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/incpriceperc'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'incfperc',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Enter value in %'),
                                 
                             )
                            )
                        ));
                    }

                    //Increase/Decrease special price (flat)

                    if (strpos($select_action,'modspclpricef') !== false)
                    {

                        $block->addItem('incspclpriceflat', array(
                            'label' => 'Modify Special Price (By Flat Number)',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/incspclflat'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'incspclflat',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Enter value (Flat)'),
                                 
                             )
                            )
                        ));
                    }    

                     //Increase/Decrease special price (percentage)
                    
                    if (strpos($select_action,'modspclpricep') !== false)
                    {

                        $block->addItem('incspclpriceperct', array(
                            'label' => 'Modify Special Price (By Percentage)',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/incspclperc'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'incspclperc',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Enter value in %'),
                                 
                             )
                            )
                        ));

                    }


                    //Modify special price using price (flat)
                    
                    if (strpos($select_action,'modspclprcf') !== false)
                    {
                        $block->addItem('spclrelbaseflat', array(
                            'label' => 'Modify Special Price using price(By Flat Number)',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/spclrelbaseflat'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'spclrelbaseflat',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Enter value (Flat)'),
                                 
                             )
                            )
                        ));
                    }    

                    //Modify special price using price (percentage)
                    
                    if (strpos($select_action,'modspclprcp') !== false)
                    {
                        $block->addItem('spclrelbase', array(
                            'label' => 'Modify Special Price using price(By Percentage)',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/spclrelbase'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'spclrelbase',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Enter value (%)'),
                                 
                             )
                            )
                        ));
                    }

                    //Modify visibility starts

                    if (strpos($select_action,'modvis') !== false)
                    {
                        $visibilityarray = Mage::getModel('catalog/product_visibility')->getOptionArray();
                        $block->addItem('uptvisibility', array(
                            'label' => 'Modify Visibility',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/modvis'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'uptvisibility',
                                 'type'     => 'select',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Visibility'),
                                 'values'    => $visibilityarray,
                                 
                             )
                            )
                        ));
                    }

                    //Modify status starts
                    
                    if (strpos($select_action,'modstatus') !== false)
                    {
                     
                        $statuses = Mage::getModel('catalog/product_status')->getOptionArray();
                        $block->addItem('uptstatus', array(
                            'label' => 'Modify Status',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/modstatus'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'uptstatus',
                                 'type'     => 'select',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('Status'),
                                 'values'    => $statuses,
                                 
                             )
                            )
                        ));
                    }
                    //copy product options starts
                   
                    if (strpos($select_action,'cpyoptions') !== false)
                    {
                        $block->addItem('copyprodopt', array(
                            'label' => 'Copy Product Options',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/copyprodopt'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'copyprodopt',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('From Product ID'),
                                 
                                 
                             )
                            )
                        ));
                    }    
                    //copy and merge product images starts
                    
                    if (strpos($select_action,'cpymimages') !== false)
                    {

                        $block->addItem('copymergeprodimg', array(
                            'label' => 'Copy and Merge With Existing Product Images',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/copymergeprodimg'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'copymergeprodimg',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('From Product ID'),
                                 
                                 
                             )
                            )
                        ));
                    }

                    //copy product images starts
                    
                    if (strpos($select_action,'cpyrimages') !== false)
                    {
                        $block->addItem('copyprodimg', array(
                            'label' => 'Copy and Replace Existing Product Images',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/copyprodimg'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'copyprodimg',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('From Product ID'),
                                 
                                 
                             )
                            )
                        ));
                    }

                    if (strpos($select_action,'assignupprd') !== false)
                    {
                        $block->addItem('assignupprd', array(
                            'label' => 'Assign as Up-sells Product / Products',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/assignupprd'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'assignupprd',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('To Product ID'),
                                 
                                 
                             )
                            )
                        ));
                    }

                    if (strpos($select_action,'assigncrossprd') !== false)
                    {
                        $block->addItem('assigncrossprd', array(
                            'label' => 'Assign as Cross-sells Product / Products',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/assigncrossprd'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'assigncrossprd',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('To Product ID'),
                                 
                                 
                             )
                            )
                        ));
                    }

                    if (strpos($select_action,'assignrelprd') !== false)
                    {
                        $block->addItem('assignrelprd', array(
                            'label' => 'Assign as Related Product / Products',
                            'url' => Mage::app()->getStore()->getUrl('*/massassign/assignrelprd'),
                            'additional'   => array(
                            'visibility'    => array(
                                 'name'     => 'assignrelprd',
                                 'type'     => 'text',
                                 'class'    => 'required-entry',
                                 'label'    => Mage::helper('catalog')->__('To Product ID'),
                                 
                                 
                             )
                            )
                        ));
                    }



                    $block->addItem('divider', array(
                        'label' => '----- Custom Actions End -----',
                        'url' => '',
                    ));



                }
          }           
    }
}

?>