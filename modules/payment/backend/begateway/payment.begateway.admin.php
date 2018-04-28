<?php
/**
 * Настройки платежного решения BeGateway для административного интерфейса
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

class Payment_begateway_admin
{
	public $config;

	public function __construct(&$diafan)
	{
    $this->diafan = &$diafan;
		$this->config = array(
			"name" => 'BeGateway',
      "params" => array(
				'begateway_shop_id' => $this->diafan->_('ID магазина'),
				'begateway_shop_key' => $this->diafan->_('Секретный ключ магазина'),
				'begateway_checkout_domain' => $this->diafan->_('Домен страницы оплаты'),
				'begateway_transaction_type' => array('name' => $this->diafan->_('Тип операции'), 'type' => 'function'),
				'begateway_currency' => array('name' => $this->diafan->_('Валюта операции'), 'type' => 'function'),
				'begateway_types' => array('name' => $this->diafan->_('Способы оплаты'), 'type' => 'function'),
				'begateway_time' => array('name' => $this->diafan->_('Время на оплату'), 'type' => 'function'),
				'begateway_test' => array('name' => $this->diafan->_('Тестовый режим'), 'type' => 'checkbox'),
			)
		);
	}
  /**
 * Редактирвание поля "Способы оплаты"
 *
 * @return void
 */
  public function edit_variable_begateway_types($value)
  {
    if($value)
    {
      $vs = array_keys($value);
    }
    else
    {
      $vs = array();
    }
    $types = array(
      'credit_card' => $this->diafan->_('Банковская карта'),
      'credit_card_halva' => $this->diafan->_('Банковская карта Халва'),
      'erip' => $this->diafan->_('ЕРИП')
    );
    echo '<div class="unit tr_payment" payment="begateway" style="display:none">
      <div class="infofield">'.$this->diafan->_('Способы оплаты').'</div>';
      foreach($types as $k => $v)
      {
        echo '<input type="checkbox" name="begateway_types['.$k.']" id="input_begateway_types_'.$k.'" value="'.$v.'"'.(in_array($k, $vs) ? ' checked' : '').' class="label_full"> <label for="input_begateway_types_'.$k.'">'.$this->diafan->_($v).'</label>';
      }
      echo '
    </div>';
  }

  /**
   * Сохранение поля "Способы оплаты"
   *
   * @return string
   */
  public function save_variable_begateway_types()
  {
    if(empty($_POST["begateway_types"]))
    {
      $_POST["begateway_types"] = array();
    }
    return $_POST["begateway_types"];
  }

  /**
   * Редактирвание поля "Тип операции"
   *
   * @return void
   */
  public function edit_variable_begateway_transaction_type($value)
  {
    if($value)
    {
      $vs = $value;
    }
    else
    {
      $vs = 'payment';
    }
    $types = array(
      'payment' => 'Платёж',
      'authorization' => 'Авторизация'
    );
    echo '<div class="unit tr_payment" payment="begateway" style="display:none">
      <div class="infofield">'.$this->diafan->_('Тип операции').'</div>';
    echo '<select name="begateway_transaction_type" id="select_begateway_transaction_type">';
      foreach($types as $k => $v)
      {
        echo '<option value="'.$k.'"'.($k == $vs ? ' selected' : '').' >'.$this->diafan->_($v).'</option>';
      }
      echo '
    </select>
    </div>';
  }

  /**
   * Сохранение поля "Способы оплаты"
   *
   * @return string
   */
  public function save_variable_begateway_transaction_type()
  {
    if(empty($_POST["begateway_transaction_type"]))
    {
      $_POST["begateway_transaction_type"] = 'payment';
    }
    return $_POST["begateway_transaction_type"];
  }

  /**
   * Редактирвание поля "Валюта"
   *
   * @return void
   */
  public function edit_variable_begateway_currency($value)
  {
    if($value)
    {
      $vs = $value;
    }
    else
    {
      $vs = 'RUB';
    }
    $types = array(
      'RUB' => 'RUB',
      'USD' => 'USD',
      'EUR' => 'EUR',
      'BYN' => 'BYN'
    );
    echo '<div class="unit tr_payment" payment="begateway" style="display:none">
      <div class="infofield">'.$this->diafan->_('Валюта операции').'</div>';
    echo '<select name="begateway_currency" id="select_begateway_currency">';
      foreach($types as $k => $v)
      {
        echo '<option value="'.$k.'"'.($k == $vs ? ' selected' : '').' >'.$this->diafan->_($v).'</option>';
      }
      echo '
    </select>
    </div>';
  }

  /**
   * Сохранение поля "Валюта"
   *
   * @return string
   */
  public function save_variable_begateway_currency()
  {
    if(empty($_POST["begateway_currency"]))
    {
      $_POST["begateway_currency"] = 'RUB';
    }
    return $_POST["begateway_currency"];
  }

  /**
   * Редактирвание поля "Время на оплату"
   *
   * @return void
   */
  public function edit_variable_begateway_time($value)
  {
    if($value)
    {
      $vs = $value;
    }
    else
    {
      $vs = '15';
    }
    $types = array(
      '999999' => 'Бессрочно',
      '15' => '15 минут',
      '30' => '30 минут',
      '45' => '45 минут',
      '60' => '1 час',
      '360' => '6 часов',
      '720' => '12 часов',
      '1440' => '24 часа',
      '2880' => '48 часов',
      '4320' => '72 часа'
    );
    echo '<div class="unit tr_payment" payment="begateway" style="display:none">
      <div class="infofield">'.$this->diafan->_('Время на оплату').'</div>';
    echo '<select name="begateway_time" id="select_begateway_time">';
      foreach($types as $k => $v)
      {
        echo '<option value="'.$k.'"'.($k == $vs ? ' selected' : '').' >'.$this->diafan->_($v).'</option>';
      }
      echo '
    </select>
    </div>';
  }

  /**
   * Сохранение поля "Время на оплату"
   *
   * @return string
   */
  public function save_variable_begateway_time()
  {
    if(empty($_POST["begateway_time"]))
    {
      $_POST["begateway_time"] = '30';
    }
    return $_POST["begateway_time"];
  }
}
