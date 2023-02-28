<?php
namespace Suraj\RestrictUserLogin\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    private $responseFactory;
    private $url;
    private $customerSession;
    private $messageManager;
    private $customerRepository;

    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->customerSession= $customerSession;
        $this->messageManager = $messageManager;
        $this->customerRepository = $customerRepository;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $customerGroup = $customer->getGroupId();
        $customer_activated = $this->getCustomerActiveStatus($customer->getId(), $attribute_code = "customer_activated");

        if($customer_activated != 1)
        {
            $this->customerSession->logout();
            $this->messageManager->addErrorMessage(__('Your account is not yet activated'));
            $redirectionUrl = $this->url->getUrl('customer/account/login');
            $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
            return $this;
        }
    }

    public function getCustomerActiveStatus($customerId, $attribute_code)
    {
        $customer = $this->customerRepository->getById($customerId);
        if($myCustomAttribute = $customer->getCustomAttribute($attribute_code)) {
            return $myCustomAttribute->getValue();
        }
    }
}