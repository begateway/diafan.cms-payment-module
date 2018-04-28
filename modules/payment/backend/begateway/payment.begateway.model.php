<?php
/**
 * Формирует данные для формы платежной системы BeGateway
 *
 * @package    DIAFAN.CMS
 * @author     begateway.com
 * @version    1.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2018 eComCharge Ltd SIA
 */

if (! defined('DIAFAN'))
{
	$path = __FILE__; $i = 0;
	while(! file_exists($path.'/includes/404.php'))
	{
		if($i == 10) exit; $i++;
		$path = dirname($path);
	}
	include $path.'/includes/404.php';
}

require_once(dirname(__FILE__) . '/lib/begateway-api-php/lib/BeGateway.php');

class Payment_begateway_model extends Diafan
{
	/**
     * Формирует данные для формы платежного решения BeGateway
     *
     * @param array $params настройки платежной системы
     * @param array $pay данные о платеже
     * @return array
     */
	public function get($params, $pay)
	{
    $token = $this->_getToken($params, $pay);

		return array_merge($token, array('text' => $pay['text']));
	}

	/**
     * Получает токен на оплату для формы платежного решения BeGateway
     *
     * @param array $params настройки платежной системы
     * @param array $pay данные о платеже
     * @return array
     */
  public function _getToken($params, $pay)
  {
    \BeGateway\Settings::$shopKey = $params['begateway_shop_key'];
    \BeGateway\Settings::$shopId = $params['begateway_shop_id'];
    \BeGateway\Settings::$checkoutBase = 'https://' . $params['begateway_checkout_domain'];

    $transaction = new \BeGateway\GetPaymentToken;

    if ($params['begateway_transaction_type'] == 'authorization') {
      $transaction->setAuthorizationTransactionType();
    }
    else {
      $transaction->setPaymentTransactionType();
    }

    $transaction->money->setAmount($pay['summ']);

    $language = 'ru';

    foreach ($this->diafan->_languages->all as $row) {
      if ($row['id'] == _LANG) {
        $language = $row['shortname'];
        if ($language == 'eng')
          $language = 'en';
        break;
      }
    }

    $transaction->money->setCurrency($params['begateway_currency']);
    $transaction->setDescription($pay["desc"]);
    $transaction->setLanguage($language);

    $notification_url = BASE_PATH_HREF . 'payment/get/begateway/result/';
    $notification_url = str_replace('carts.local', 'webhook.begateway.com:8443', $notification_url);
    $notification_url = str_replace('app.docker.local:8080', 'webhook.begateway.com:8443', $notification_url);
    $transaction->setNotificationUrl($notification_url);

    $transaction->setSuccessUrl(BASE_PATH_HREF . 'payment/get/begateway/success/?pay_id='.$pay["id"]);
    $transaction->setDeclineUrl(BASE_PATH_HREF . 'payment/get/begateway/fail/?pay_id='.$pay["id"]);
    $transaction->setFailUrl(BASE_PATH_HREF . 'payment/get/begateway/fail/?pay_id='.$pay["id"]);

    $transaction->setTrackingId($pay["id"]);
    $transaction->setExpiryDate(date("c", $params['begateway_time']*60 + time()));

    $transaction->customer->setFirstName($pay["details"]["firstname"]);
    $transaction->customer->setLastName($pay["details"]["lastname"]);
    $transaction->customer->setCountry($pay["details"]["country"]);
    $transaction->customer->setAddress($pay["details"]["address"]);
    $transaction->customer->setCity($pay["details"]["city"]);
    $transaction->customer->setZip($pay["details"]["zip"]);
    $transaction->customer->setPhone($pay["details"]["phone"]);
    $transaction->customer->setEmail($pay["details"]["email"]);


    error_log(print_r($params['begateway_types'],true));
    if (!empty($params['begateway_types']['credit_card'])) {
      $cc = new \BeGateway\PaymentMethod\CreditCard;
      $transaction->addPaymentMethod($cc);
    }

    if (!empty($params['begateway_types']['credit_card_halva'])) {
      $halva = new \BeGateway\PaymentMethod\CreditCardHalva;
      $transaction->addPaymentMethod($halva);
    }

    if (!empty($params['begateway_types']['erip'])) {
      $erip = new \BeGateway\PaymentMethod\Erip(array(
        'order_id' => $pay["id"],
        'account_number' => strval($pay["id"])
      ));
      $transaction->addPaymentMethod($erip);
    }

    if ($params['begateway_test'] == 1) {
      $transaction->setTestMode();
    }

    $response = $transaction->submit();

    if ($response->isSuccess()) {
      $result = array(
        'token' => $response->getToken(),
        'url' => $response->getRedirectUrlScriptName()
      );
    } else {
      $result = array(
        'error_message' => $response->getMessage(),
        'error' => true
      );
    }

    return $result;
  }
}
