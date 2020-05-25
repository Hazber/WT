<?php
require_once '../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('../views/'); 
$twig = new Twig_Environment($loader);
session_start();
$twig->addGlobal('link_index', "app.php");
$twig->addGlobal('link_shop', "shop.php");
$twig->addGlobal('link_contacts', "contacts.php");
$twig->addGlobal('link_service', " service.php");
$twig->addGlobal('cart_number', $_SESSION['total']);
$twig->addGlobal('link_box', " box.php");
echo $twig->render('contacts.html'); ?>
