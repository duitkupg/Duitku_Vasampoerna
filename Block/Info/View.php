<?php
/**
 * Copyright (c) 2017. All rights reserved Duitku Vasampoerna Host to Host.
 *
 * This program is free software. You are allowed to use the software but NOT allowed to modify the software.
 * It is also not legal to do any changes to the software and distribute it in your own name / brand.
 *
 * All use of the payment modules happens at your own risk. We offer a free test account that you can use to test the module.
 *
 * @author    Duitku Vasampoerna
 * @copyright Duitku Vasampoerna (http://duitku.com)
 * @license   Duitku Vasampoerna
 *
 */
namespace Duitku\Vasampoerna\Block\Info;
use \Duitku\Vasampoerna\Model\Method\Epay\Payment as EpayPayment;

class View extends \Magento\Payment\Block\Info
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/view/info.phtml');
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if ($this->_paymentSpecificInformation !== null) {
            return $this->_paymentSpecificInformation;
        }
        
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];

        if ($this->getInfo()->getLastTransId()) {
            $ccType = $this->getInfo()->getOrder()->getPayment()->getCcType();
            if (!empty($ccType)) {
                $data[(string)__("Payment type")] = $ccType;
            }
            $ccNumber = $this->getInfo()->getOrder()->getPayment()->getCcNumberEnc();
            if (!empty($ccNumber)) {
                $data[(string)__("Card number")] = $ccNumber;
            }

            $txnId = "";
            $payment = $this->getInfo()->getOrder()->getPayment();
            if ($payment->getMethod() === EpayPayment::METHOD_CODE) {
                $txnId = $payment->getAdditionalInformation(EpayPayment::METHOD_REFERENCE);
            }

            if (!empty($txnId)) {
                $data[(string)__("Transaction Id")] = $txnId;
            }
        }

        return $transport->setData(array_merge($data, $transport->getData()));
    }

    /**
     * Get translated payment information title
     * @return string
     */
    public function getPaymentInformationTitle()
    {
        return __("Payment Information");
    }
}
