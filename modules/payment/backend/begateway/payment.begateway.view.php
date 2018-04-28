<?php
/**
 * Шаблон платежа через систему BeGateway
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

if (@$result['error']) {
  echo '<p>'.$this->diafan->_('Ошибка получения токена платежа. Сообщение:'). ' ' . $result['error_message'] . '</p>';
} else
{
	echo $result["text"];
	?>
	<form name="pay" method="POST" target="_self" action="<?php echo $result['url']; ?>">
		<input type="hidden" name="token" value="<?php echo $result["token"]; ?>">
		<p><input type="submit" value="<?php echo $this->diafan->_('Оплатить', false);?>"></p>
	</form>
	<?php
}
