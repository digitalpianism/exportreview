<?php
/**
 * Controller to add the CSV and XML export to the review grid
 *
 * Class DigitalPianism_ExportReview_Adminhtml_ExportreviewController
 */
class DigitalPianism_ExportReview_Adminhtml_ExportreviewController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'pending':
                return Mage::getSingleton('admin/session')->isAllowed('catalog/reviews_ratings/reviews/pending');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('catalog/reviews_ratings/reviews/all');
                break;
        }
    }

    public function massCsvExportAction()
    {
        $reviewIds = $this->getRequest()->getParam('reviews');
        if (!is_array($reviewIds)) {
            $this->_getSession()->addError($this->__('Please select review(s).'));
            if( !Mage::registry('usePendingFilter') ) {
                $this->_redirect('catalog_product_review/index');
            }
            else $this->_redirect('catalog_product_review/pending');
        }
        else {
            //write headers to the csv file
            $content = "review_id,created_at,title,nickname,detail,type,name,sku";
            if( !Mage::registry('usePendingFilter') ) {
                $content .= ",status";
            }
            $content .= "\n";
            try {

                if( !Mage::registry('usePendingFilter') ) {
                    $status = true;
                }
                else $status = false;

                $attributesToSelect = array('review_id','created_at','title','nickname','detail','type','name','sku');

                if ($status)
                {
                    $attributesToSelect[] = 'status';
                }

                $model = Mage::getModel('review/review');
                $collection = $model->getProductCollection();

                $collection->addFieldToFilter('rt.review_id', array ('in' => array($reviewIds)))
                    ->addAttributeToSelect($attributesToSelect);

                foreach ($collection as $review) {
                    $content .= "\"{$review->getReviewId()}\",\"{$review->getCreatedAt()}\",\"{$review->getTitle()}\",\"{$review->getNickname()}\",\"{$review->getDetail()}\",\"{$review->getType()}\",\"{$review->getName()}\",\"{$review->getSku()}\"";
                    if ($status)
                    {
                        $content .= ",\"{$review->getStatus()}\"";
                    }
                    $content .= "\n";
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                if( !Mage::registry('usePendingFilter') ) {
                    $this->_redirect('catalog_product_review/index');
                }
                else $this->_redirect('catalog_product_review/pending');
            }
            $this->_prepareDownloadResponse('export.csv', $content, 'text/csv');
        }

    }

    public function massXmlExportAction()
    {
        $reviewIds = $this->getRequest()->getParam('reviews');
        if (!is_array($reviewIds)) {
            $this->_getSession()->addError($this->__('Please select review(s).'));
            if( !Mage::registry('usePendingFilter') ) {
                $this->_redirect('catalog_product_review/index');
            }
            else $this->_redirect('catalog_product_review/pending');
        }
        else {
            //write headers to the csv file
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml.= '<items>';
            try {

                if( !Mage::registry('usePendingFilter') ) {
                    $status = true;
                }
                else $status = false;

                $attributesToSelect = array('review_id','created_at','title','nickname','detail','type','name','sku');

                if ($status)
                {
                    $attributesToSelect[] = 'status';
                }

                $model = Mage::getModel('review/review');
                $collection = $model->getProductCollection();

                $collection->addFieldToFilter('rt.review_id', array ('in' => array($reviewIds)))
                    ->addAttributeToSelect($attributesToSelect);

                foreach ($collection as $review) {
                    $xml.= $review->toXml();
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                if( !Mage::registry('usePendingFilter') ) {
                    $this->_redirect('catalog_product_review/index');
                }
                else $this->_redirect('catalog_product_review/pending');
            }
            $xml.= '</items>';
            $this->_prepareDownloadResponse('export.xml', $xml, 'text/xml');
        }
    }
}
