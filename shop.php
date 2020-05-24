<?php
require_once '../vendor/autoload.php';
require_once 'PDO.php';
$loader = new Twig_Loader_Filesystem('../templates/'); 
$twig = new Twig_Environment($loader);
session_start();
if (!isset($_SESSION['counter'])) {
    $_SESSION['counter'] = 0;
	$_SESSION['total'] = 0;
	$_SESSION['sum']=0;
	$_SESSION['adress']="Введите ваш почтовый адрес";
	$_SESSION['phone']="Введите ваш телефон";
	$_SESSION['name']="Введите ваше Имя";
	$_SESSION['email']="Введите ваш email";
	
}
$search_result ="Что вам нужно?";


if (!empty(($_POST['search_s']))&(($_POST['search_s'])!="Что вам нужно")) { 
	$search =($_POST['search_s']); 
	$mycards=Selected_database_data($DBH,"SELECT * FROM menuitem WHERE `card_type` = ?",array($search));
	if ($mycards==null){
		$name="Ничего не найдено";
	}	
	$name="Найденные товары";
    
}

else
{
	$search_result ="Что вам нужно?";
	$type=($_GET["types"]);
	if ($type==""){
		$name="Все товары";
		$mycards=All_database_data($DBH);
	}
	else{
		$name="$type";
		$mycards=Selected_database_data($DBH,"SELECT * FROM menuitem WHERE `card_title` = ?",array($type));	
	}
}





$twig->addGlobal('link_index', "app.php");
$twig->addGlobal('link_shop', "shop.php");
$twig->addGlobal('link_contacts', "contacts.php");
$twig->addGlobal('link_service', " service.php");
$twig->addGlobal('header_title', $name);
$twig->addGlobal('cart_number', $_SESSION['total']);
$twig->addGlobal('link_box', " box.php");
$twig->addGlobal('form_input', $search_result);
echo $twig->render('shop.html',array('mycards' => $mycards)); ?>
