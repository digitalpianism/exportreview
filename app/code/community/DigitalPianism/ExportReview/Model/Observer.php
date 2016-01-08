<?php

class DigitalPianism_ExportReview_Model_Observer
{
    public function addMassExport(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction
            && $block->getRequest()->getControllerName() == 'catalog_product_review')
        {
            $block->addItem('exportreviewcsv', array(
                'label' => 'Export to CSV',
                'url' => $block->getUrl('adminhtml/exportreview/massCsvExport')
            ));

            $block->addItem('exportreviewwml', array(
                'label' => 'Export to XML',
                'url' => $block->getUrl('adminhtml/exportreview/massXmlExport')
            ));
        }
    }
}