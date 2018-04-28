<?php
/**
 * Обработка данных, полученных от системы BeGateway
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

if ($_GET['rewrite'] == 'begateway/result')
{
  $webhook = new \BeGateway\Webhook;

  $pay = $this->diafan->_payment->check_pay($webhook->getTrackingId(), 'begateway');

  if (!$pay)
    die('Unable to get payment');

  \BeGateway\Settings::$shopKey = $pay['params']['begateway_shop_key'];
  \BeGateway\Settings::$shopId = $pay['params']['begateway_shop_id'];

  $money = new \BeGateway\Money;
  $money->setAmount($pay['summ']);
  $money->setCurrency($pay['params']['begateway_currency']);

  if ($money->getCents() != $webhook->getResponse()->transaction->amount ||
      $pay['params']['begateway_currency'] != $webhook->getResponse()->transaction->currency)
    die('Wrong amount');

  if($webhook->isAuthorized())
  {
    if ($webhook->isSuccess())
      $this->diafan->_payment->success($pay, 'pay');
  } else {
    die('Unauthorized');
  }
  echo 'OK';
  exit;
}

$pay = $this->diafan->_payment->check_pay($_GET['pay_id'], 'begateway');

// оплата прошла успешно
if ($_GET["rewrite"] == "begateway/success")
{
	$this->diafan->_payment->success($pay, 'redirect');
}

$this->diafan->_payment->fail($pay);
