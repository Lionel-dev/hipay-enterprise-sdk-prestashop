<?php
/**
 * 2017 HiPay
 *
 * NOTICE OF LICENSE
 *
 * @author    HiPay <support.wallet@hipay.com>
 * @copyright 2017 HiPay
 * @license   https://github.com/hipay/hipay-wallet-sdk-prestashop/blob/master/LICENSE.md
 */
require_once(dirname(__FILE__).'/../../../lib/vendor/autoload.php');

use HiPay\Fullservice\Enum\Transaction\TransactionStatus;

class HipayDBQuery
{
    const HIPAY_CAT_MAPPING_TABLE          = 'hipay_cat_mapping';
    const HIPAY_CARRIER_MAPPING_TABLE      = 'hipay_carrier_mapping';
    const HIPAY_ORDER_REFUND_CAPTURE_TABLE = 'hipay_order_refund_capture';
    const HIPAY_CC_TOKEN_TABLE             = 'hipay_cc_token';
    const HIPAY_TRANSACTION_TABLE          = 'hipay_transaction';
    const HIPAY_PAYMENT_ORDER_PREFIX       = 'HiPay Enterprise';

    public function __construct($moduleInstance)
    {
        $this->module = $moduleInstance;
        $this->logs   = $this->module->getLogs();
    }

    /**
     * Create categories mapping table
     * @return type
     */
    public function createCatMappingTable()
    {
        $this->logs->logsHipay('Create Hipay categories mapping table');

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.HipayDBQuery::HIPAY_CAT_MAPPING_TABLE.'`(
                `hp_ps_cat_id` INT(10) UNSIGNED NOT NULL,
                `hp_cat_id` INT(10) UNSIGNED NOT NULL,
                `shop_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`hp_ps_cat_id`, `shop_id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        return Db::getInstance()->execute($sql);
    }

    /**
     * Create categories mapping table
     * @return type
     */
    public function createCarrierMappingTable()
    {
        $this->logs->logsHipay('Create Hipay carrier mapping table');

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.HipayDBQuery::HIPAY_CARRIER_MAPPING_TABLE.'`(
                `hp_ps_carrier_id` INT(10) UNSIGNED NOT NULL,
                `hp_carrier_mode` VARCHAR(255)  NOT NULL,
                `hp_carrier_shipping` VARCHAR(255) NOT NULL,
                `preparation_eta` FLOAT(10) UNSIGNED NOT NULL,
                `delivery_eta` FLOAT(10) UNSIGNED NOT NULL,
                `shop_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`hp_ps_carrier_id`, `shop_id` )
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        return Db::getInstance()->execute($sql);
    }

    /**
     *
     * @return type
     */
    public function createOrderRefundCaptureTable()
    {
        $this->logs->logsHipay('Create Hipay order refund capture table');

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.HipayDBQuery::HIPAY_ORDER_REFUND_CAPTURE_TABLE.'`(
                `hp_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `hp_ps_order_id` INT(10) UNSIGNED NOT NULL,
                `hp_ps_product_id` INT(10) UNSIGNED NOT NULL,
                `type` VARCHAR(255)  NOT NULL,
                `quantity` INT(10) UNSIGNED NOT NULL,
                `amount` DECIMAL(5,2) UNSIGNED NOT NULL,
                PRIMARY KEY (`hp_id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        return Db::getInstance()->execute($sql);
    }

    /**
     *
     * @return type
     */
    public function createCCTokenTable()
    {
        $this->logs->logsHipay('Create Hipay credit card token table');

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.HipayDBQuery::HIPAY_CC_TOKEN_TABLE.'`(
                `hp_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `customer_id` INT(10) UNSIGNED NOT NULL,
                `token` VARCHAR(45) NOT NULL,
                `brand` VARCHAR(255) NOT NULL,
                `pan` VARCHAR(20)  NOT NULL,
                `card_holder` VARCHAR(255) NOT NULL,
                `card_expiry_month` INT(2) UNSIGNED NOT NULL,
                `card_expiry_year` INT(4) UNSIGNED NOT NULL,
                `issuer` VARCHAR(255) NOT NULL,
                `country` VARCHAR(15) NOT NULL,
                PRIMARY KEY (`hp_id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        return Db::getInstance()->execute($sql);
    }

    /**
     *
     * @return type
     */
    public function createHipayTransactionTable()
    {
        $this->logs->logsHipay('Create Hipay transaction table');

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.HipayDBQuery::HIPAY_TRANSACTION_TABLE.'`(
                `hp_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT(10) UNSIGNED NOT NULL,
                `transaction_ref` VARCHAR(45) NOT NULL,
                `state` VARCHAR(255) NOT NULL,
                `status` INT(4) UNSIGNED NOT NULL,
                `message` VARCHAR(255) NOT NULL,
                `payment_product` VARCHAR(255) NOT NULL,
                `amount` FLOAT NOT NULL,
                `captured_amount` FLOAT ,
                `refunded_amount` FLOAT ,
                `payment_start` VARCHAR(255) ,
                `payment_authorized` VARCHAR(255) ,
                `authorization_code` VARCHAR(255) ,
                `basket` TEXT ,
                PRIMARY KEY (`hp_id`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        return Db::getInstance()->execute($sql);
    }

    /**
     * Delete Hipay mapping table
     * @return type
     */
    public function deleteCatMappingTable()
    {
        $this->logs->logsHipay('Delete Hipay mapping table');

        $sql = 'DROP TABLE `'._DB_PREFIX_.HipayDBQuery::HIPAY_CAT_MAPPING_TABLE.'`';
        return Db::getInstance()->execute($sql);
    }

    /**
     * Delete Hipay mapping table
     * @return type
     */
    public function deleteCarrierMappingTable()
    {
        $this->logs->logsHipay('Delete Hipay carrier mapping table');

        $sql = 'DROP TABLE `'._DB_PREFIX_.HipayDBQuery::HIPAY_CARRIER_MAPPING_TABLE.'`';
        return Db::getInstance()->execute($sql);
    }

    public function deleteOrderRefundCaptureTable()
    {
        $this->logs->logsHipay('Delete Hipay order refund capture table');

        $sql = 'DROP TABLE `'._DB_PREFIX_.HipayDBQuery::HIPAY_ORDER_REFUND_CAPTURE_TABLE.'`';
        return Db::getInstance()->execute($sql);
    }

    public function deleteCCTokenTable()
    {
        $this->logs->logsHipay('Delete credit card table');

        $sql = 'DROP TABLE `'._DB_PREFIX_.HipayDBQuery::HIPAY_CC_TOKEN_TABLE.'`';
        return Db::getInstance()->execute($sql);
    }

    /**
     * get last cart from user ID
     * @param int $userId
     * @return boolean / Cart
     */
    public function getLastCartFromUser($userId)
    {
        $sql = 'SELECT `id_cart`
                FROM `'._DB_PREFIX_.'cart`
                WHERE `id_customer` = '.pSQL($userId).'
                ORDER BY date_upd DESC';

        $result  = Db::getInstance()->getRow($sql);
        $cart_id = isset($result['id_cart']) ? $result['id_cart'] : false;

        if ($cart_id) {
            $objCart = new Cart((int) $cart_id);
        } else {
            $objCart = false;
        }

        return $objCart;
    }

    /**
     * start sql transaction
     * @param int $cartId
     */
    public function setSQLLockForCart($cartId)
    {
        $this->logs->callbackLogs('start LockSQL  for id_cart = '.$cartId);

        $sql = 'begin;';
        $sql .= 'SELECT id_cart FROM '._DB_PREFIX_.'cart WHERE id_cart = '.pSQL((int) $cartId).' FOR UPDATE;';

        if (!Db::getInstance()->execute($sql)) {
            $this->logs->logsHipay('Bad LockSQL initiated, Lock could not be initiated for id_cart = '.$cartId);
            die('Lock not initiated');
        }
    }

    /**
     * commit transaction and release sql lock
     */
    public function releaseSQLLock()
    {
        $this->logs->callbackLogs('release LockSQL');

        $sql = 'commit;';
        if (!Db::getInstance()->execute($sql)) {
            $this->logs->logsHipay('Bad LockSQL initiated ');
        }
    }

    /**
     * return transaction from Order Id
     * @return type
     */
    public function getTransactionFromOrder($orderId)
    {
        $sql = 'SELECT DISTINCT(op.transaction_id)
                FROM `'._DB_PREFIX_.'order_payment` op
                INNER JOIN `'._DB_PREFIX_.'orders` o ON o.reference = op.order_reference
                WHERE o.id_order = '.pSQL((int) $orderId);

        $result = Db::getInstance()->getRow($sql);

        return $result;
    }

    /**
     *
     * @param int $idShop
     * @return type
     */
    public function getHipayMappedCategories($idShop)
    {
        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_CAT_MAPPING_TABLE.'`
                WHERE `shop_id` = '.pSQL((int) $idShop);

        return Db::getInstance()->executeS($sql);
    }

    /**
     *
     * @param int $idShop
     * @return type
     */
    public function getHipayMappedCarriers($idShop)
    {
        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_CARRIER_MAPPING_TABLE.'`
                WHERE `shop_id` = '.pSQL((int) $idShop);

        return Db::getInstance()->executeS($sql);
    }

    /**
     * insert row in HIPAY_CAT_MAPPING_TABLE
     * @param type $values
     */
    public function setHipayCatMapping(
    $values, $shopId
    )
    {
        try {
            Db::getInstance()->insert(
                HipayDBQuery::HIPAY_CAT_MAPPING_TABLE,
                $values
            );
        } catch (Exception $exc) {
            $where = "`shop_id` = ".(int) $shopId;

            Db::getInstance()->delete(
                HipayDBQuery::HIPAY_CAT_MAPPING_TABLE,
                $where
            );

            Db::getInstance()->insert(
                HipayDBQuery::HIPAY_CAT_MAPPING_TABLE,
                $values
            );
        }
        return true;
    }

    /**
     * insert row in HIPAY_CARRIER_MAPPING_TABLE
     * @param type $values
     */
    public function setHipayCarrierMapping(
    $values, $shopId
    )
    {
        try {
            Db::getInstance()->insert(
                HipayDBQuery::HIPAY_CARRIER_MAPPING_TABLE,
                $values
            );
        } catch (Exception $exc) {
            $where = "`shop_id` = ".(int) $shopId;

            Db::getInstance()->delete(
                HipayDBQuery::HIPAY_CARRIER_MAPPING_TABLE,
                $where
            );

            Db::getInstance()->insert(
                HipayDBQuery::HIPAY_CARRIER_MAPPING_TABLE,
                $values
            );
        }
        return true;
    }

    /**
     *
     * @param type $PSId
     * @return int
     */
    public function getHipayCatFromPSId($PSId)
    {
        $sql = 'SELECT hp_cat_id
                FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_CAT_MAPPING_TABLE.'` 
                WHERE hp_ps_cat_id = '.pSQL((int) $PSId);

        $result = Db::getInstance()->getRow($sql);

        return $result;
    }

    /**
     *
     * @param type $PSId
     * @return int
     */
    public function getHipayCarrierFromPSId($PSId)
    {
        $sql = 'SELECT *
                FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_CARRIER_MAPPING_TABLE.'` 
                WHERE hp_ps_carrier_id = '.pSQL((int) $PSId);

        $result = Db::getInstance()->getRow($sql);

        return $result;
    }

    /**
     * check if specific order status exist in $idOrder order history
     * @param type $status
     * @param type $idOrder
     * @return boolean
     */
    public function checkOrderStatusExist(
    $status, $idOrder
    )
    {
        $sql = 'SELECT COUNT(id_order_history) as count
		FROM `'._DB_PREFIX_.'order_history`
		WHERE `id_order` = '.pSQL((int) $idOrder).' AND `id_order_state` = '.pSQL((int) $status);

        $this->logs->logsHipay('Check order status exist : '.$sql);

        $result = Db::getInstance()->getRow($sql);

        $this->logs->logsHipay(
            'Check order status exist : '.print_r(
                $result,
                true
            )
        );

        if (isset($result['count']) && $result['count'] > 0) {
            return true;
        }
        return false;
    }

    /**
     * update order payment line
     * @param type $paymentData
     * @return boolean
     */
    public function updateOrderPayment($paymentData)
    {
        $cardData = "";

        if ($paymentData['payment_method'] != null) {
            $cardData = " `card_number` = '".pSQL($paymentData['payment_method']['pan'])."',
                    `card_brand` = '".pSQL($paymentData['payment_method']['brand'])."',
                    `card_expiration` = '".pSQL($paymentData['payment_method']['card_expiry_month'])."/".pSQL(
                    $paymentData['payment_method']['card_expiry_year']
                )."',
                    `card_holder` = '".pSQL($paymentData['payment_method']['card_holder'])."' ,";
        }

        $sql = "
            UPDATE `"._DB_PREFIX_."order_payment`
            SET     ".$cardData."
                    `amount` = '".$paymentData['captured_amount']."'
                    
            WHERE 
                 `transaction_id` = '".$paymentData['transaction_id']."' AND
                `payment_method` = '".HipayDBQuery::HIPAY_PAYMENT_ORDER_PREFIX." ".$paymentData["name"]."'
            AND `order_reference`= '".$paymentData["order_reference"]."';";


        if (!Db::getInstance()->execute($sql)) {
            //LOG
            $this->logs->logsHipay("ERROR : updateOrderPayment");
            return false;
        }

        return true;
    }

    /**
     * count order payment line
     * @param type $orderReference
     * @return boolean
     */
    public function countOrderPayment(
    $orderReference, $transactionId = null
    )
    {
        $transactWhere = "";

        if ($transactionId != null) {
            $transactWhere = " transaction_id='".pSQL($transactionId)."' AND ";
        }

        $sql = "SELECT COUNT(id_order_payment) as count "
            ."FROM `"._DB_PREFIX_."order_payment` "
            ."WHERE ".$transactWhere." `order_reference` = '".pSQL($orderReference)."' ;";


        $result = Db::getInstance()->getRow($sql);
        if (isset($result['count'])) {
            return $result['count'];
        }
        return 0;
    }

    /**
     * Check if there is a duplicated OrderPayment and remove duplicate from same order ref but with incomplete payment method name
     * @param type $orderReference
     */
    public function deleteOrderPaymentDuplicate($orderReference)
    {
        // delete
        $where = "payment_method='".HipayDBQuery::HIPAY_PAYMENT_ORDER_PREFIX."' AND transaction_id='' AND order_reference='".$orderReference."'";

        Db::getInstance()->delete(
            'order_payment',
            $where
        );
    }

    /**
     * set invoice order
     * @param Order $order
     */
    public function setInvoiceOrder($order)
    {
        $sql = 'SELECT `id_order_payment`
                FROM `'._DB_PREFIX_.'order_payment`
                WHERE order_reference="'.pSQL($order->reference).' LIMIT 1";';

        $result   = Db::getInstance()->getRow($sql);
        $idOrderP = isset($result['id_order_payment']) ? $result['id_order_payment']
                : false;

        if ($idOrderP) {
            $where = "`id_order` = ".(int) $order->id;
            $data  = array("id_order_payment" => (int) $idOrderP);
            Db::getInstance()->update(
                'order_invoice_payment',
                $data,
                $where
            );
        }
    }

    public function setHipayTransaction($values)
    {
        foreach ($values as $key => $value) {
            $values[$key] = pSQL($value);
        }

        return Db::getInstance()->insert(
                HipayDBQuery::HIPAY_TRANSACTION_TABLE,
                $values
        );
    }

    public function alreadyCaptured($orderId)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_TRANSACTION_TABLE.'` WHERE order_id='.pSQL(
                (int) $orderId
            ).' AND status ='.TransactionStatus::CAPTURED.' ;';

        $result = Db::getInstance()->executeS($sql);
        if (empty($result)) {
            return false;
        }
        return true;
    }

    public function getTransactionReference($orderId)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_TRANSACTION_TABLE.'` WHERE order_id='.pSQL(
                (int) $orderId
            ).' AND status ='.TransactionStatus::AUTHORIZED.' LIMIT 1 ;';

        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            
            return $result[0]["transaction_ref"];
        }
        return false;
    }

    public function getPaymentProductFromMessage($orderId)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_TRANSACTION_TABLE.'` WHERE order_id='.pSQL(
                (int) $orderId
            ).' AND status ='.TransactionStatus::AUTHORIZED.' LIMIT 1;';

        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            return $result[0]["payment_product"];
        }
        return false;
    }

    public function getOrderBasket($orderId)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_TRANSACTION_TABLE.'` WHERE order_id='.pSQL(
                (int) $orderId
            ).' AND status ='.TransactionStatus::AUTHORIZED.' LIMIT 1;';

        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {

            return Tools::jsonDecode(
                    $result[0]["basket"],
                    true
            );
        }
        return false;
    }

    /**
     * save order capture data (basket)
     * @param type $values
     * @return type
     */
    public function setCaptureOrRefundOrder($values)
    {
        foreach ($values as $key => $value) {
            $values[$key] = pSQL($value);
        }

        return Db::getInstance()->insert(
                HipayDBQuery::HIPAY_ORDER_REFUND_CAPTURE_TABLE,
                $values
        );
    }

    /**
     * get order capture saved data (basket)
     * @param type $orderId
     * @return type
     */
    public function getCapturedItems($orderId)
    {
        return $this->getCapturedOrRefundedItems(
                $orderId,
                "capture"
        );
    }

    /**
     * get order refund saved data (basket)
     * @param type $orderId
     * @return type
     */
    public function getRefundedItems($orderId)
    {
        return $this->getCapturedOrRefundedItems(
                $orderId,
                "refund"
        );
    }

    /**
     * get capture or refund saved data (basket)
     * @param type $orderId
     * @param type $type
     * @return type
     */
    private function getCapturedOrRefundedItems(
    $orderId, $type
    )
    {
        $sql = 'SELECT `hp_ps_product_id`, `type`, SUM(`quantity`) as quantity, SUM(`amount`) as amount
                FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_ORDER_REFUND_CAPTURE_TABLE.'`
                WHERE `hp_ps_order_id` = '.pSQL((int) $orderId).' AND `type` = "'.pSQL($type).'"'.
            ' GROUP BY `hp_ps_product_id`';

        $result          = Db::getInstance()->executeS($sql);
        $formattedResult = array();
        foreach ($result as $item) {
            $formattedResult[$item["hp_ps_product_id"]] = $item;
        }
        return $formattedResult;
    }

    /**
     * get number of capture or refund request and message id containing this information
     * @param type $type
     * @param type $orderId
     * @return type
     */
    public function getCaptureOrRefundAttempt(
    $type, $orderId
    )
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'message` WHERE id_order='.pSQL(
                (int) $orderId
            ).' AND message LIKE \'%"'.pSQL($type).'_attempt":%\' LIMIT 1;';

        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            $message = Tools::jsonDecode(
                    $result[0]["message"],
                    true
            );
            return array("message_id" => $result[0]["id_message"], "attempt" => (int) $message[$type."_attempt"]);
        }
        return array("message_id" => false, "attempt" => 0);
    }

    /**
     * get if is set fees captured message
     * @param type $orderId
     * @return boolean
     */
    public function feesAreCaptured($orderId)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'message` WHERE id_order='.pSQL(
                (int) $orderId
            ).' AND message LIKE \'%"fees_capture":1%\' LIMIT 1;';

        $result = Db::getInstance()->executeS($sql);

        if (!empty($result)) {
            return true;
        }

        return false;
    }

    /**
     * get if is set fees refunded message
     * @param type $orderId
     * @return boolean
     */
    public function feesAreRefunded($orderId)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'message` WHERE id_order='.pSQL(
                (int) $orderId
            ).' AND message LIKE \'%"fees_refund":1%\' LIMIT 1;';

        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            return true;
        }

        return false;
    }

    /**
     * check if token exist for this customer
     * @param type $customerId
     * @param type $token
     * @return boolean
     */
    public function ccTokenExist(
    $customerId, $token
    )
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_CC_TOKEN_TABLE.'` WHERE customer_id='.pSQL(
                (int) $customerId
            ).' AND token LIKE "'.pSQL($token).'" LIMIT 1;';

        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            return true;
        }

        return false;
    }

    /**
     * save credit card token and other informations
     * @param type $values
     * @return type
     */
    public function setCCToken($values)
    {
        foreach ($values as $key => $value) {
            $values[$key] = pSQL($value);
        }

        return Db::getInstance()->insert(
                HipayDBQuery::HIPAY_CC_TOKEN_TABLE,
                $values
        );
    }

    /**
     * get all credit card saved for this customer
     * @param type $customerId
     * @return boolean
     */
    public function getSavedCC($customerId)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_CC_TOKEN_TABLE.'` WHERE customer_id='.pSQL(
                (int) $customerId
            ).' ;';

        try {
            $result = Db::getInstance()->executeS($sql);
        } catch (Exception $exc) {
            $this->logs->errorLogsHipay($exc->getMessage());
            $this->logs->errorLogsHipay($exc->getTraceAsString());
            return false;
        }

        if (!empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * get token informations
     * @param type $customerId
     * @param type $token
     * @return boolean
     */
    public function getToken(
    $customerId, $token
    )
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_CC_TOKEN_TABLE.'` WHERE customer_id='.pSQL(
                (int) $customerId
            ).' AND token LIKE "'.pSQL($token).'" LIMIT 1;';

        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            return $result[0];
        }

        return false;
    }

    /**
     * delete credit card token
     * @param type $customerId
     * @param type $tokenId
     * @return boolean
     */
    public function deleteToken(
    $customerId, $tokenId
    )
    {
        // check if tokenID exist for this user
        $sqlExist = 'SELECT * FROM `'._DB_PREFIX_.HipayDBQuery::HIPAY_CC_TOKEN_TABLE.'` WHERE customer_id='.pSQL(
                (int) $customerId
            ).' AND hp_id = '.pSQL((int) $tokenId).';';

        $result = Db::getInstance()->executeS($sqlExist);

        if (!empty($result)) {
            // delete
            $where = 'customer_id='.pSQL((int) $customerId).' AND hp_id='.pSQL((int) $tokenId);
            Db::getInstance()->delete(
                HipayDBQuery::HIPAY_CC_TOKEN_TABLE,
                $where
            );

            return true;
        }
        return false;
    }
}