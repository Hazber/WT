<?php
require 'PHPmailer/PHPMailer.php';
require 'PHPmailer/SMTP.php';
require 'PHPmailer/Exception.php';

function SendMessage($customer,$msg){
	$mail = new PHPMailer\PHPMailer\PHPMailer();
	try {
		$mail->isSMTP();   
		$mail->CharSet = "UTF-8";                                          
		$mail->SMTPAuth   = true;
		$mail->Host       = 'ssl://smtp.mail.ru';
		$mail->Username   = 'hazarion@mail.ru'; 
		$mail->Password   = 'azsxdcqawsed123'; 
		$mail->SMTPSecure = 'ssl';
		$mail->Port       = 465;
		$mail->setFrom('hazarion@mail.ru', 'Алексей Никитко');
		$mail->addAddress($customer);  
		$mail->isHTML(true);
		$mail->Subject = "Бухарик";
		$mail->Body    = $msg;
		if ($mail->send()) {
			return 1;
		}
	}
	catch (Exception $e) {
		return 0;
	}
}

function FormMailMessage($_sess,$data_order){
	$order="";
	for($i=0;$i<$_sess['counter'];$i++){
			$si=$_sess['item_' . $i];
			$order=$order . "<br>" . $si['card_title'] . " : " . $si['card_price'] . "$ : X" . $si['card_num'] . " : " . $si['total_price'] . "$" . $si['card_gr']."%".$si['card_v']."L";		
	}
	$msg="<b>Уважаемый(ая) {$_sess['name']}</b>. Ваш заказ успешно оформлен<br>
		<br><b>Информация о доставке:</b>
		<br>Имя: {$_sess['name']}
		<br>Контактный телефон: {$_sess['phone']}
		<br><b>Информация о заказе:</b>
		<br>Стоимость: {$data_order['price']}$
		<br>Количество товаров {$data_order['capacity']}
		<br>Дата: {$data_order['date']}
		<br><b>Перечень товаров:</b>$order
		<br>Удачной попойки,черти<br>";
	return $msg;
}

function FormMailMessage1($_sess){

	$msg="<b>Уважаемый(ая) {$_sess['name']}</b>. Ваш заказ успешно оформлен<br>
		<br><b>Ожидайте звонка оператора для дальнейшего уточнения</b>
		<br><b>Мы позвоним вам в ближайшие 10-15 минут</b>";
	return $msg;
}
