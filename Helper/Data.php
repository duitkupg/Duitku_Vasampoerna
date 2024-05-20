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

namespace Duitku\Vasampoerna\Helper;
include_once('Duitku/ApiRequestor.php');
include_once('Duitku/DuitkuCore.php');
use Duitku\Vasampoerna\Helper\DuitkuConstants;
use Duitku_Vasampoerna_Core;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Duitku\Vasampoerna\Logger\DuitkuLogger
     */
    protected $_duitkuLogger;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * Duitku Helper
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Duitku\Vasampoerna\Logger\DuitkuLogger $duitkuLogger
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Duitku\Vasampoerna\Logger\DuitkuLogger $duitkuLogger,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        parent::__construct($context);
        $this->_duitkuLogger = $duitkuLogger;
        $this->_encryptor = $encryptor;
        $this->_moduleList = $moduleList;
    }

    /**
     * Gives back duitku_checkout configuration values
     *
     * @param $field
     * @param null|int $storeId
     * @return mixed
     */
    public function getDuitkuEpayConfigData($field, $storeId = null)
    {
        return $this->getConfigData($field, 'duitku_vasampoernaepay', $storeId);
    }
	
	public function getDuitkuCore(){
   		$DuitkuCore = new Duitku_Vasampoerna_Core();
		return $DuitkuCore;
	}
		
	
    /**
     * Gives back duitku_checkout configuration values
     *
     * @param $field
     * @param null|int $storeId
     * @return mixed
     */
    public function getDuitkuCheckoutConfigData($field, $storeId = null)
    {
        return $this->getConfigData($field, 'duitku_checkout', $storeId);
    }

    /**
     * Gives back duitku_checkout configuration values
     *
     * @param $field
     * @param null|int $storeId
     * @return mixed
     */
    public function getDuitkuAdvancedConfigData($field, $storeId = null)
    {
        return $this->getConfigData($field, 'duitku_advanced', $storeId);
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param $field
     * @param $paymentMethodCode
     * @param $storeId
     * @param bool|false $flag
     * @return bool|mixed
     */
    public function getConfigData($field, $paymentMethodCode, $storeId, $flag = false)
    {
        $path = 'payment/' . $paymentMethodCode . '/' . $field;

        if (!$flag) {
            return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            return $this->scopeConfig->isSetFlag($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        }
    }

 
 
    
    /**
     * Decrypt data
     *
     * @param mixed $data
     * @return string
     */
    public function decryptData($data)
    {
        return $this->_encryptor->decrypt(trim($data));
    }

    /**
     * Retrieve the shops local code
     *
     * @return string
     */
    public function getShopLocalCode()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resolver = $objectManager->get('Magento\Framework\Locale\Resolver');

        return $resolver->getLocale();
    }

    /**
     * Convert an amount from minorunits
     *
     * @param $amount
     * @param $minorunits
     * @return float
     */
    public function convertPriceFromMinorunits($amount, $minorunits)
    {
        if ($amount == "" || $amount == null) {
            return 0;
        }

        return ($amount / pow(10, $minorunits));
    }


    /**
     * Return minorunits based on Currency Code
     *
     * @param $currencyCode
     * @return int
     */
    public function getCurrencyMinorunits($currencyCode)
    {
        $currencyArray = array(
        'TTD' => 0, 'KMF' => 0, 'ADP' => 0, 'TPE' => 0, 'BIF' => 0,
        'DJF' => 0, 'MGF' => 0, 'XPF' => 0, 'GNF' => 0, 'BYR' => 0,
        'PYG' => 0, 'JPY' => 0, 'CLP' => 0, 'XAF' => 0, 'TRL' => 0,
        'VUV' => 0, 'CLF' => 0, 'KRW' => 0, 'XOF' => 0, 'RWF' => 0,
        'IQD' => 3, 'TND' => 3, 'BHD' => 3, 'JOD' => 3, 'OMR' => 3,
        'KWD' => 3, 'LYD' => 3);

        return array_key_exists($currencyCode, $currencyArray) ? $currencyArray[$currencyCode] : 2;
    }

    /**
     * Format currency
     *
     * @param float $amount
     * @return string
     */
    public function formatCurrency($amount)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');

        return $priceHelper->currency($amount, true, false);
    }

    /**
     * Generate Checkout Api key
     *
     * @param $storeId
     * @return string
     */
  
    /**
     * Returns the version of the module
     *
     * @return string
     */
    public function getModuleVersion()
    {
        return $this->_moduleList->getOne("Duitku_Vasampoerna")['setup_version'];
    }

    /**
     * Returns the module name and version
     *
     * @return string
     */
    public function getModuleHeaderInfo()
    {
        $duitkuVersion = $this->getModuleVersion();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $magentoVersion = $productMetadata->getVersion();
        $result = 'Magento/' . $magentoVersion. ' Module/' . $duitkuVersion . ' PHP/' . phpversion();
        return $result;
    }

   

    /**
     * Translate Payment status
     *
     * @param string $status
     * @return \Magento\Framework\Phrase
     */
    public function translatePaymentStatus($status)
    {
        if (strcmp($status, "PAYMENT_NEW") == 0) {
            return __("New");
        } elseif (strcmp($status, "PAYMENT_CAPTURED") == 0 || strcmp($status, "PAYMENT_EUROLINE_WAIT_CAPTURE") == 0 || strcmp($status, "PAYMENT_EUROLINE_WAIT_CREDIT") == 0) {
            return __("Captured");
        } elseif (strcmp($status, "PAYMENT_DELETED") == 0) {
            return __("Deleted");
        } else {
            return __("Unkown");
        }
    }

    /**
     * Return if Checkout Api Result is valid
     *
     * @param \Duitku\Vasampoerna\Model\Api\Checkout\Response\Base $request
     * @param mixed $id
     * @param bool $isBackoffice
     * @param string &$message
     * @return bool
     */
  


    /**
     * Convert country code to a number
     *
     * @param mixed $lan
     * @return string
     */
    public function calcLanguage($lan = null)
    {
        if (!isset($lan)) {
            $lan = $this->getShopLocalCode();
        }

        $languageArray = array(
            'da_DK' => '1',
            'de_CH' => '7',
            'de_DE' => '7',
            'en_AU' => '2',
            'en_GB' => '2',
            'en_NZ' => '2',
            'en_US' => '2',
            'sv_SE' => '3',
            'nn_NO' => '4',
            );

        return array_key_exists($lan, $languageArray) ? $languageArray[$lan] : '0';
    }

    /**
     * Convert card id to name
     *
     * @param mixed $cardid
     * @return string
     */
    public function calcCardtype($cardid)
    {
        $cardIdArray = array(
            '1' => 'Dankort / VISA/Dankort',
            '2' => 'eDankort',
            '3' => 'VISA / VISA Electron',
            '4' => 'MasterCard',
            '6' => 'JCB',
            '7' => 'Maestro',
            '8' => 'Diners Club',
            '9' => 'American Express',
            '10' => 'ewire',
            '12' => 'Nordea e-betaling',
            '13' => 'Danske Netbetalinger',
            '14' => 'PayPal',
            '16' => 'MobilPenge',
            '17' => 'Klarna',
            '18' => 'Svea',
            '19' => 'SEB Direktbetalning',
            '20' => 'Nordea E-payment',
            '21' => 'Handelsbanken Direktbetalningar',
            '22' => 'Swedbank Direktbetalningar',
            '23' => 'ViaBill',
            '24' => 'NemPay',
            '25' => 'iDeal');

        return array_key_exists($cardid, $cardIdArray) ? $cardIdArray[$cardid] : '';
    }

    /**
     * Convert Iso code
     *
     * @param string $code
     * @param bool $isKey
     * @return string
     */
    public function convertIsoCode($code, $isKey = true)
    {
        $isoCodeArray = array(
           'ADP' => '020', 'AED' => '784', 'AFA' => '004', 'ALL' => '008', 'AMD' => '051', 'ANG' => '532',
           'AOA' => '973', 'ARS' => '032', 'AUD' => '036', 'AWG' => '533', 'AZM' => '031', 'BAM' => '052',
           'BBD' => '004', 'BDT' => '050', 'BGL' => '100', 'BGN' => '975', 'BHD' => '048', 'BIF' => '108',
           'BMD' => '060', 'BND' => '096', 'BOB' => '068', 'BOV' => '984', 'BRL' => '986', 'BSD' => '044',
           'BTN' => '064', 'BWP' => '072', 'BYR' => '974', 'BZD' => '084', 'CAD' => '124', 'CDF' => '976',
           'CHF' => '756', 'CLF' => '990', 'CLP' => '152', 'CNY' => '156', 'COP' => '170', 'CRC' => '188',
           'CUP' => '192', 'CVE' => '132', 'CYP' => '196', 'CZK' => '203', 'DJF' => '262', 'DKK' => '208',
           'DOP' => '214', 'DZD' => '012', 'ECS' => '218', 'ECV' => '983', 'EEK' => '233', 'EGP' => '818',
           'ERN' => '232', 'ETB' => '230', 'EUR' => '978', 'FJD' => '242', 'FKP' => '238', 'GBP' => '826',
           'GEL' => '981', 'GHC' => '288', 'GIP' => '292', 'GMD' => '270', 'GNF' => '324', 'GTQ' => '320',
           'GWP' => '624', 'GYD' => '328', 'HKD' => '344', 'HNL' => '340', 'HRK' => '191', 'HTG' => '332',
           'HUF' => '348', 'IDR' => '360', 'ILS' => '376', 'INR' => '356', 'IQD' => '368', 'IRR' => '364',
           'ISK' => '352', 'JMD' => '388', 'JOD' => '400', 'JPY' => '392', 'KES' => '404', 'KGS' => '417',
           'KHR' => '116', 'KMF' => '174', 'KPW' => '408', 'KRW' => '410', 'KWD' => '414', 'KYD' => '136',
           'KZT' => '398', 'LAK' => '418', 'LBP' => '422', 'LKR' => '144', 'LRD' => '430', 'LSL' => '426',
           'LTL' => '440', 'LVL' => '428', 'LYD' => '434', 'MAD' => '504', 'MDL' => '498', 'MGF' => '450',
           'MKD' => '807', 'MMK' => '104', 'MNT' => '496', 'MOP' => '446', 'MRO' => '478', 'MTL' => '470',
           'MUR' => '480', 'MVR' => '462', 'MWK' => '454', 'MXN' => '484', 'MXV' => '979', 'MYR' => '458',
           'MZM' => '508', 'NAD' => '516', 'NGN' => '566', 'NIO' => '558', 'NOK' => '578', 'NPR' => '524',
           'NZD' => '554', 'OMR' => '512', 'PAB' => '590', 'PEN' => '604', 'PGK' => '598', 'PHP' => '608',
           'PKR' => '586', 'PLN' => '985', 'PYG' => '600', 'QAR' => '634', 'ROL' => '642', 'RUB' => '643',
           'RUR' => '810', 'RWF' => '646', 'SAR' => '682', 'SBD' => '090', 'SCR' => '690', 'SDD' => '736',
           'SEK' => '752', 'SGD' => '702', 'SHP' => '654', 'SIT' => '705', 'SKK' => '703', 'SLL' => '694',
           'SOS' => '706', 'SRG' => '740', 'STD' => '678', 'SVC' => '222', 'SYP' => '760', 'SZL' => '748',
           'THB' => '764', 'TJS' => '972', 'TMM' => '795', 'TND' => '788', 'TOP' => '776', 'TPE' => '626',
           'TRL' => '792', 'TRY' => '949', 'TTD' => '780', 'TWD' => '901', 'TZS' => '834', 'UAH' => '980',
           'UGX' => '800', 'USD' => '840', 'UYU' => '858', 'UZS' => '860', 'VEB' => '862', 'VND' => '704',
           'VUV' => '548', 'XAF' => '950', 'XCD' => '951', 'XOF' => '952', 'XPF' => '953', 'YER' => '886',
           'YUM' => '891', 'ZAR' => '710', 'ZMK' => '894', 'ZWD' => '716');

        if ($isKey) {
            return $isoCodeArray[$code];
        }

        return array_search($code, $isoCodeArray);
    }

    /**
     * Create an Surcharge fee item
     *
     * @param mixed $baseFeeAmount
     * @param mixed $feeAmount
     * @param mixed $storeId
     * @param mixed $orderId
     * @param mixed $text
     * @return \Magento\Sales\Model\Order\Item
     */
    public function createSurchargeItem($baseFeeAmount, $feeAmount, $storeId, $orderId, $text)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Sales\Model\Order\Item */
        $feeItem = $objectManager->create('\Magento\Sales\Model\Order\Item');
        $feeItem->setSku(DuitkuConstants::DUITKU_SURCHARGE);

        $feeItem->setName($text);
        $feeItem->setBaseCost($baseFeeAmount);
        $feeItem->setBasePrice($baseFeeAmount);
        $feeItem->setBasePriceInclTax($baseFeeAmount);
        $feeItem->setBaseOriginalPrice($baseFeeAmount);
        $feeItem->setBaseRowTotal($baseFeeAmount);
        $feeItem->setBaseRowTotalInclTax($baseFeeAmount);
        $feeItem->setCost($feeAmount);
        $feeItem->setPrice($feeAmount);
        $feeItem->setPriceInclTax($feeAmount);
        $feeItem->setOriginalPrice($feeAmount);
        $feeItem->setRowTotal($feeAmount);
        $feeItem->setRowTotalInclTax($feeAmount);
        $feeItem->setProductType(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL);
        $feeItem->setIsVirtual(1);
        $feeItem->setQtyOrdered(1);
        $feeItem->setStoreId($storeId);
        $feeItem->setOrderId($orderId);

        return $feeItem;
    }
}
