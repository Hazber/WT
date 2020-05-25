<?php
require_once '../vendor/autoload.php';
require_once 'PDO.php';
require_once 'SendMail.php';
$loader = new Twig_Loader_Filesystem('../views/'); 
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

	$data=array('customer_id'=>$id,'capacity'=>$arr['total'],'price'=>$arr['sum']);
	return $data;
}

function GetProductID($DBH,$request,$params,$field){
	$res=Selected_database_data($DBH,$request,$params);
	return $res[0][$field];
}

function GetOrderID($DBH,$request,$params,$field){
	$res=Select_database_data_order_shop($DBH,$request,$params);
	return $res[0][$field];
}

function Connect_Order_Menu($db,$order_id){
	for($i=0;$i<$_SESSION['counter'];$i++){
			$si=$_SESSION['item_' . $i];
			$product_id=GetProductID($db,"SELECT * FROM menuitem WHERE `card_type` = ? AND `card_price`=?",array($si['card_title'],$si['card_price']),'card_id');
			$data=array('product_id'=>$product_id,'order_id'=>$order_id,'capacity'=>$si['card_num']);
			Database_request($db,"INSERT INTO `product_in_order` SET `product_id` = :product_id, `order_id` = :order_id, `capacity` = :capacity",$data);
		}
}

function Find_Element($arr,$elem){
	for($i=0;$i<$arr['counter'];$i++){
		$si=$arr['item_' . $i];
		if ($si['card_title']==$elem['card_title']&&$si['card_price']==$elem['card_price']){
			return $i;
		}	
	}
	return -1;
}
function Delete_Element(&$arr,$ind){
	for($i=$ind+1;$i<$arr['counter'];$i++){
		$si=$arr['item_' . $i];
		$new_i=$i-1;
		$arr['item_' . $new_i]=$si;	
	}
}

    $flag=true;
	session_start();
	
	if (empty($_REQUEST)){
	}
	elseif($_REQUEST['flag']==1)
	{
		
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
		//$st = preg_replace ("/[0-9+]+/","",$_POST['tel']);
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
			$id_order=Database_request($DBH,"INSERT INTO `Shop_Orders` SET `customer_id` = :customer_id, `capacity` = :capacity, `price` = :price",$data);
			Connect_Order_Menu($DBH,$id_order);
		
		}
			else{
			$data=FormOrder($_SESSION,$customer[0]['id']);
			Database_request($DBH,"UPDATE Shop_Orders SET  capacity =?, price = ?, date =? WHERE customer_id=?",array($data['capacity'],$data['price'],$data['customer_id']));
			Request_in_database($DBH,"DELETE FROM product_in_order WHERE order_id=?",array($id_order));
			Connect_Order_Menu($DBH,$id_order);
		}
		SendMessage($_SESSION['email'],FormMailMessage($_SESSION,$data));
		$res="Ваш заказ успешно отправлен";
	
	}
	else{
		$res="Введите ваши данные";
	}
	}
		else{
		$item=array('card_title' =>$_REQUEST['name'],'card_price' =>$_REQUEST['price'],'card_image' =>$_REQUEST['image'],'card_gr' =>$_REQUEST['gr'],'card_v' =>$_REQUEST['v'],'card_num' =>1,'total_price' =>1);
		if ($_REQUEST['type']=="Add"){
			$ind=Find_Element($_SESSION,$item);
			if ($ind>-1){
				$_SESSION['item_' . $ind]['card_num']+=1;
				$_SESSION['item_' . $ind]['total_price']=$_SESSION['item_' . $ind]['card_price']*$_SESSION['item_' . $ind]['card_num'];
			}
			else{
				$k=$_SESSION['counter'];
				$_SESSION['item_' . $k]=$item;
				$_SESSION['item_' . $k]['total_price']=$_SESSION['item_' . $k]['card_price']*$_SESSION['item_' . $k]['card_num'];
				$_SESSION['counter']+=1;
			}
			$_SESSION['total']+=1;
		}
		else{
			$ind=Find_Element($_SESSION,$item);
			$_SESSION['item_' . $ind]['card_num']--;
			$_SESSION['item_' . $ind]['total_price']=$_SESSION['item_' . $ind]['card_price']*$_SESSION['item_' . $ind]['card_num'];
			$_SESSION['total']-=1;
			if ($_SESSION['item_' . $ind]['card_num']==0){
				Delete_Element($_SESSION,$ind);
				$_SESSION['counter']-=1;	
			}
		}
	}
	$_SESSION['sum']=0;
	for($i=0;$i<$_SESSION['counter'];$i++){
			$si=$_SESSION['item_' . $i];
			$mycards[$i]=array('card_num' =>$si['card_num'],'card_gr' =>$si['card_gr'],'card_v' =>$si['card_v'],'total_price' =>$si['total_price'],'card_type' =>$si['card_title'],'card_price' =>$si['card_price'],'card_image' =>$si['card_image']);
			$_SESSION['sum']+=$si['total_price'];
		}

		
$twig->addGlobal('link_index', "app.php");
$twig->addGlobal('link_shop', "shop.php");
$twig->addGlobal('link_contacts', "contacts.php");
$twig->addGlobal('link_service', " service.php");

$twig->addGlobal('cart_number', $_SESSION['total']);
$twig->addGlobal('sum', $_SESSION['sum']);
$twig->addGlobal('res_phone',$res_phone);
$twig->addGlobal('res_name',$res_name);
$twig->addGlobal('res_email',$res_email);
$twig->addGlobal('title_form',$res);
$twig->addGlobal('link_box', " box.php");
echo $twig->render('box.html',array('mycards' => $mycards)); ?>
