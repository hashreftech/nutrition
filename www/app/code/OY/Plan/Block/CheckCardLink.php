<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 06/06/19
 * Time: 11:13 PM
 */

namespace OY\Card\Block;

use Magento\Customer\Model\Context;

class CheckCardLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    protected $_customerRepository;

    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->httpContext = $httpContext;
        $this->_customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * Render block HTML.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isLoggedIn() && !$this->getLoggedInCustomerId()) {
            $this->_customerSession->start();
        }
        if (!$this->isLoggedIn() || !$this->getLoggedInCustomerId()) {
            return '';
        } else {
            if (false != $this->getTemplate()) {
                return parent::_toHtml();
            }

            $customer = $this->_customerRepository->getById($this->getLoggedInCustomerId());
            if ($customer->getCustomAttribute('associate_check_permit') == null ||
                $customer->getCustomAttribute('associate_check_permit')->getValue() == false) {
                return '';
            }

            return '<li class="request-card-link"><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
        }
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    public function getLoggedInCustomerId() {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomerId();
        }
        return false;
    }
}