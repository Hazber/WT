<?php
require_once '../vendor/autoload.php';
require_once 'PDO.php';
require_once 'SendMail.php';
$loader = new Twig_Loader_Filesystem('../templates/'); 

$twig = new Twig_Environment($loader);

function Check_User_Data($data,$regexp,&$matches,&$res){
    $addr=htmlspecialchars($data);
    if (preg_match($regexp,$addr,$matches)){
        $res="Принято";
        return 1;
    }
    else{ 
        $res="Некорректные данные";
        return 0;
    }
}


function FormOrder($arr,$id){
	$data=array('customer_id'=>$id,'text'=>$_POST['message'],'type'=>$_POST['list_service']);
	return $data;
}

function GetProductID($DBH,$request,$params,$field){
	$res=Selected_database_data($DBH,$request,$params);
	return $res[0][$field];
}

function GetOrderID($DBH,$request,$params,$field){
	$res=Selected_database_data_order_site($DBH,$request,$params);
	return $res[0][$field];
}

function Connect_Order_Menu($db,$order_id){
	for($i=0;$i<$_SESSION['counter'];$i++){
			$si=$_SESSION['item_' . $i];
			$product_id=GetProductID($db,"SELECT * FROM menuitems WHERE `name` = ? AND `price`=?",array($si['card_title'],$si['card_price']),'card_id');
			$data=array('productid'=>$product_id,'orderid'=>$order_id,'total'=>$si['card_num']);
			Database_request($db,"INSERT INTO `product_in_order` SET `productid` = :productid, `orderid` = :orderid, `total` = :total",$data);
		}
}

$flag=true;
$res="Введите ваши данные";
session_start();

$regexp_phone='/^(80(29|17)|\+375(29|33|44))[0-9]{7}$/u';
$matches_phone=array();
$res_phone=$_SESSION['phone'];
if (isset($_POST['tel'])){
	$st = preg_replace ("/[^0-9+]/","",$_POST['tel']);
	if (Check_User_Data($st,$regexp_phone,$matches_phone,$res_phone)){
		$_SESSION['phone']=$st;
	}
	else{ $flag=false;}	
}
else{$flag=false;}

$regexp_name='/^[(a-z)|(A-Z)|(а-яё)|(А-ЯЁ)]{1,20}$/u';
$matches_name=array();
$res_name=$_SESSION['name'];
if (isset($_POST['firstname'])){
	if (Check_User_Data($_POST['firstname'],$regexp_name,$matches_name,$res_name)){
		$_SESSION['name']=$_POST['firstname'];
	}
	else{ $flag=false;}	
}
else{$flag=false;}


$regexp_email='/.+@.+\..+/i';
$matches_email=array();
$res_email=$_SESSION['email'];
if (isset($_POST['e_mail'])){
	if (Check_User_Data($_POST['e_mail'],$regexp_email,$matches_email,$res_email)){
		$_SESSION['email']=$_POST['e_mail'];
	}
	else{ $flag=false;}	
}

else{$flag=false;}

if ($flag){
	$data_adress=array('name' => $_SESSION['name'], 'phone' => $_SESSION['phone'], 'email'=> $_SESSION['email']);
	$customer=Selected_database_customer($DBH,"SELECT * FROM Customer WHERE `name` =? AND `phone`=? AND `email`=?",array($data_adress['name'],$data_adress['phone'],$data_adress['email']));
	if (empty($customer)){
		$id_customer=Database_request($DBH,"INSERT INTO `Customer` SET `name` = :name, `phone` = :phone, `email` = :email",$data_adress);
		$data=FormOrder($_SESSION,$id_customer);
		$id_order=Database_request($DBH,"INSERT INTO `Site_Orders` SET `customer_id` = :customer_id, `text` = :text, `type` = :type",$data);
		
		
	}
	else{
		$data=FormOrder($_SESSION,$customer[0]['id']);
		Database_request($DBH,"UPDATE Site_Orders SET  text =?, type = ? WHERE customer_id=?",array($data['text'],$data['type'],$data['customer_id']));
		
	}
	SendMessage($_SESSION['email'],FormMailMessage1($_SESSION));
	$res="Ваш заказ успешно отправлен";
	
}
else{
	$res="Введите ваши данные";
}


$twig->addGlobal('link_index', "app.php");
$twig->addGlobal('link_shop', "shop.php");
$twig->addGlobal('link_contacts', "contacts.php");
$twig->addGlobal('link_service', " service.php");
$twig->addGlobal('cart_number', $_SESSION['total']);
$twig->addGlobal('res_adress',$res_adress);
$twig->addGlobal('res_phone',$res_phone);
$twig->addGlobal('res_name',$res_name);
$twig->addGlobal('res_email',$res_email);
$twig->addGlobal('title_form',$res);
$twig->addGlobal('link_box', " box.php");
echo $twig->render('service.html'); ?>
