<?php
require_once '../vendor/autoload.php';
require_once 'PDO.php';
$loader = new Twig_Loader_Filesystem('../templates/'); 
$twig = new Twig_Environment($loader);
session_start();

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

	session_start();
	if (empty($_REQUEST)){
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

$twig->addGlobal('link_box', " box.php");
echo $twig->render('box.html',array('mycards' => $mycards)); ?>
