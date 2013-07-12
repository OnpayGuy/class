<?php
/**
 * Пример реализации продаж с помощью класса Onpay.
 *
 * @author norgen
 * @version 1.0
 * @namespace Onpay
 */

/*
 * Если пришли данные формы, подключаем класс Onpay, создаем экземпляр класса
 * и инициализируем его.
 */

require_once('onpay.class/onpay.class.php');

//Данные авторизации в Onpay
$onpay_form = 'norgen';
$onpay_key = '1231233211';

//Работаем с внутренней базой класса.
$mode = 'internal_db';

// Создаем экземпляр класса Onpay и включаем отладку.
$opy = new Onpay($mode, $onpay_form, $onpay_key);
$opy->debug = true;


// Если запрос содержит параметр type - перехватываем его и отправляем в процессор.
// По всей вероятности это запрос от Onpay, дальнейшая валидация параметров производится классом.
if (isset($_REQUEST['type'])) {
	if ($opy->process_onpay($_REQUEST)) {
		//Заказ успешно оплачлен, Onpay передан корректный XML, статус заказа сменен на "оплачено" можно что-то сделать
		//в пользовательском скрипте, например отправить email администратору/пользователю и прервать выполнение скрипта.
		exit;
	} else {
		//Есть какие-то ошибки, которые в данном примере передаются в onpay. Лучшим вариантом было бы отправить их
		//администратору или записать в лог.
		echo $opy->error;
		exit;
	}
}

if (isset($_REQUEST['product']) && isset($_REQUEST['price'])) {

	// В идеале скрипт должен передавать еще и email покупателя, но для упрощения я использую свой.
	$email = 'vasily.norman@gmail.com';

	// Стоимость продукта
	$summ = $_REQUEST['price'];

	// В переменную $form записывается js-скрипт, при выводе которого в браузер происходит редирект на платежную форму
	// Onpay с необходимыми параметрами. Одновременно происходит запись заказа во внутреннюю БД класса.
	$form = $opy->get_form('redirect', $summ, $email);

	// Номер последнего заказа в этой сессии можно извлечь коммандой get_last_order(). Эта строчка не обязательна.
	echo "Redirect to pay for the order №" . $opy->get_last_order();

	// Выводим в браузер, который делает редирект на платежную форму Onpay.
	echo $form;
} else {
	?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>Тестовый магазин на базе класса Onpay</title>
	<style type="text/css">
		table {
			width: 100%;
		}

		td {
			text-align: center;
			width: 50%;
			padding-bottom: 20px;
			border-bottom: 1px dotted;
		}

		td img {
			height: 200px;
		}
	</style>
</head>
<body>
<h1>Тестовый магазин на базе класса Onpay</h1>
<table>
	<tr>
		<td>
			<h2>Молоко</h2>
			<img src="img/milk.jpg" alt="Молоко"/>

			<h3>200 руб.</h3><a href="?product=1&price=200">Купить через Onpay!</a>
		</td>
		<td>
			<h2>Кофе</h2>
			<img src="img/coffe.JPG" alt="Кофе"/>

			<h3>120 руб.</h3><a href="?product=2&price=120">Купить через Onpay!</a>
		</td>
	</tr>
	<tr>
		<td>
			<h2>Чай</h2>
			<img src="img/tea.png" alt="Чай"/>

			<h3>50 руб.</h3><a href="?product=3&price=50">Купить через Onpay!</a>
		</td>
		<td>
			<h2>Хлеб</h2>
			<img src="img/hleb.jpg" alt="Хлеб"/>

			<h3>15 руб. 50 коп.</h3><a href="?product=2&price=15.50">Купить через Onpay!</a>
		</td>
	</tr>
</table>
<small><a target="_blank" href="readme.html">Просмотреть исходный код с комментариями</a></small>
</body>
</html>

<? } ?>