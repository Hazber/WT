<?php
require_once '../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('../templates/'); 
$twig = new Twig_Environment($loader);

/*function Check_User_Data($data,$regexp,&$matches,&$res){
    $addr=htmlspecialchars($data);
    if (preg_match($regexp,$addr,$matches)){
        $res="Принято";
        return 1;
    }
    else{ 
        $res="Перепроверьте данные";
        return 0;
    }
}

$flag=true;



$regexp_phone='/^(80(29|17)|\+375(29|33|44))[0-9]{7}$/u';
$matches_phone=array();
$res_phone=$_SESSION['phone'];
if (isset($_POST['client_phone'])){
	$st = preg_replace ("/[^0-9+]/","",$_POST['client_phone']);
	if (Check_User_Data($st,$regexp_phone,$matches_phone,$res_phone)){
		$_SESSION['phone']=$st;
	}
	else{ $flag=false;}	
}
else{$flag=false;}

$regexp_name='/^[(a-z)|(A-Z)|(а-яё)|(А-ЯЁ)]{1,20}$/u';
$matches_name=array();
$res_name=$_SESSION['name'];
if (isset($_POST['client_name'])){
	if (Check_User_Data($_POST['client_name'],$regexp_name,$matches_name,$res_name)){
		$_SESSION['name']=$_POST['client_name'];
	}
	else{ $flag=false;}	
}
else{$flag=false;}


#$regexp_email='/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9_\-.]+$/u';
$regexp_name='/.+@.+\..+/i';
$matches_email=array();
$res_email=$_SESSION['email'];
if (isset($_POST['client_email'])){
	if (Check_User_Data($_POST['client_email'],$regexp_email,$matches_email,$res_email)){
		$_SESSION['email']=$_POST['client_email'];
	}
	else{ $flag=false;}	
}

else{$flag=false;}

if ($flag){
	if (count($matches_adress)<11){$flat="no";}
	else{$flat=$matches_adress[11];}
	$data_adress=array('name' => $_SESSION['name'], 'phone' => $_SESSION['phone'],'city' =>$matches_adress[2] ,'street' => $matches_adress[4],'num' => $matches_adress[8],'flat' =>$flat);
	$customer=Selected_database_data_customer($db,"SELECT * FROM customers WHERE `nickname` =? AND `phone`=? AND `city`=? AND `street`=? AND `street_num`=? AND `flat`=?",array($data_adress['name'],$data_adress['phone'],$data_adress['city'],$data_adress['street'],$data_adress['num'],$data_adress['flat']));
	if (empty($customer)){
		$id_customer=Request_in_database($db,"INSERT INTO `customers` SET `nickname` = :name, `phone` = :phone, `city` = :city, `street` = :street, `street_num` = :num, `flat` = :flat",$data_adress);
		$data=FormOrder($_SESSION,$id_customer);
		$id_order=Request_in_database($db,"INSERT INTO `orders` SET `customerid` = :id, `capacity` = :capacity, `price` = :price, `date` = :date, `time` = :time",$data);
		Connect_Order_Menu($db,$id_order);
	}
	else{
		$data=FormOrder($_SESSION,$customer[0]['id']);
		Request_in_database($db,"UPDATE orders SET  capacity =?, price = ?, date = ?, time = ? WHERE customerid=?",array($data['capacity'],$data['price'],$data['date'],$data['time'],$data['id']));
		$id_order=GetOrderID($db,"SELECT * FROM orders WHERE `customerid` = ?",array($data['id']),'id');
		Request_in_database($db,"DELETE FROM product_in_order WHERE orderid=?",array($id_order));
		Connect_Order_Menu($db,$id_order);
	}
	SendMessage($_SESSION['email'],FormMailMessage($_SESSION,$data));
	$title="Ваш заказ успешно отправлен";
	
}
else{
	$title="Вы нам врете..Хавки не будет";
}
*/



$twig->addGlobal('link_index', "index.php");
$twig->addGlobal('link_shop', "shop.php");
$twig->addGlobal('link_contacts', "contacts.php");
$twig->addGlobal('link_service', " service.php");
$twig->addGlobal('cart_number', $_SESSION['total']);
$twig->addGlobal('res_adress',$res_adress);
$twig->addGlobal('res_phone',$res_phone);
$twig->addGlobal('res_name',$res_name);
$twig->addGlobal('res_email',$res_email);
echo $twig->render('service.html'); ?>
