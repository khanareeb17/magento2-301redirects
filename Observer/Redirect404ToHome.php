<?php declare(strict_types=1);
/**
 * @author Areeb Khan
 * @copyright Copyright (c) 2025 Tekglide (https://www.tekglide.com)
 * @package Aakhan_Redirect404
 */

namespace Aakhan\Redirect404\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Redirect404ToHome implements ObserverInterface
{
    /**
    * @var \Magento\Framework\App\ResponseFactory
    */
    protected $_responseFactory;

    /**
    * @var \Magento\Framework\UrlInterface
    */
    protected $_url;

     /**
    * @var \Magento\Framework\StoreManagerInterface
    */
    protected $_storeManager;

    /**
     * Constructor for the class.
     *
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        StoreManagerInterface $storeManagerInterface
    )
    {
        $this->_responseFactory = $responseFactory;
        $this->_url = $url; 
        $this->_storeManager = $storeManagerInterface;
    }

    /**
     * Execute the observer.
     *
     * This method handles the observer logic when the specific event is triggered.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var RequestInterface $request */
        $request = $observer->getEvent()->getControllerAction()->getRequest();
        $actionFullName = strtolower($request->getFullActionName());
        if (strpos($actionFullName, '_noroute_') !== false) {
            $storeObj = $this->_storeManager->getStore(1);
            $storeId = $storeObj->getId();
            $baseURL = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
            $homepageRedirectUrl = $this->_url->getUrl($baseURL);
            $this->_responseFactory->create()->setRedirect($homepageRedirectUrl , 301)->sendResponse();
            exit;
        }
    }
}
