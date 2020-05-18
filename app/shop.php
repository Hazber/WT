<?php
require_once '../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('../templates/'); 
$twig = new Twig_Environment($loader);

if (!isset($_SESSION['counter'])) {
    $_SESSION['counter'] = 0;
	$_SESSION['total'] = 0;
	$_SESSION['sum']=0;
	$_SESSION['adress']="Введите ваш почтовый адрес";
	$_SESSION['phone']="Введите ваш телефон";
	$_SESSION['name']="Введите ваше Имя";
	$_SESSION['email']="Введите ваш email";
	
}
$twig->addGlobal('link_index', "index.php");
$twig->addGlobal('link_shop', "shop.php");
$twig->addGlobal('link_contacts', "contacts.php");
$twig->addGlobal('link_service', " service.php");
$twig->addGlobal('header_title', 'Водки и водки особые');
$twig->addGlobal('cart_number', $_SESSION['total']);
echo $twig->render('shop.html'); ?>
