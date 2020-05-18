<?php
require_once '../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('../templates/'); 
$twig = new Twig_Environment($loader);
$twig->addGlobal('link_index', "index.php");
$twig->addGlobal('link_shop', "shop.php");
$twig->addGlobal('link_contacts', "contacts.php");
$twig->addGlobal('link_service', " service.php");
$twig->addGlobal('cart_number', 0);
echo $twig->render('contacts.html'); ?>
