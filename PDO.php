<?php
$mycards=array();
$DBH = new PDO('mysql:host=localhost;dbname=Alcohol_shop; charset=utf8;', 'root', 'ilovefAllout1290!');


function All_database_data($DBH){
	try {
		$index=0;
		$data = $DBH->query("SELECT * FROM menuitem")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($data as $value){
			$mycards[$index]=array('menuitem_id' =>$value['menuitem_id'],'card_title' =>$value['card_title'],'card_type' =>$value['card_type'],'card_price' =>$value['card_price'],'card_image' =>$value['card_image'],'card_gr' =>$value['card_gr'],'card_v' =>$value['card_v']);
			$index++;	
		}
		return $mycards;
	} 
	catch (PDOException $e) {
        echo $e->getMessage(); 
		exit();
	}
}
function Selected_database_data(){
	try {
		$index=0;
		$DBH=func_get_arg(0);
		$request=func_get_arg(1);
		$params=func_get_arg(2);
		$stmt = $DBH->prepare($request);
		$stmt->execute($params);
		while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
			$res[$index]=array('card_id' =>$row->id,'card_title' =>$row->card_title,'card_type' =>$row->card_type,'card_price' =>$row->card_price,'card_image' =>$row->card_image,'card_gr' =>$row->card_gr,'card_v' =>$row->card_v);
			$index++;}
		if (isset($res)){return $res;}	
		else {return null;}
	}
	catch (PDOException $e) {
		echo $e->getMessage(); 
		exit();
	}
}
function Selected_database_customer(){
	try {
		$index=0;
		$DBH=func_get_arg(0);
		$request=func_get_arg(1);
		$params=func_get_arg(2);
		$stmt = $DBH->prepare($request);
		$stmt->execute($params);
		while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
			$res[$index]=array('id' =>$row->customer_id,'name' =>$row->name,'phone' =>$row->phone,'email' =>$row->email);
			$index++;}
		if (isset($res)){return $res;}	
		else {return null;}
	}
	catch (PDOException $e) {
		print "Error!: " . $e->getMessage();
		exit();
	}
}

/*function Selected_database_data_order_shop(){
	try {
		$i=0;
		$DBH=func_get_arg(0);
		$request=func_get_arg(1);
		$params=func_get_arg(2);
		$stmt = $DBH->prepare($request);
		$stmt->execute($params);
		while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
			$res[$i]=array('id' =>$row->order_id,'capacity' =>$row->capacity,'price' =>$row->price,'customer_id' =>$row->customer_id,'date' =>$row->date,'order'=>$row->order_txt);
			$i++;}
		if (isset($res)){return $res;}	
		else {return null;}
	}
	catch (PDOException $e) {
		print "Error!: " . $e->getMessage();
		exit();
	}
}
*/
function Selected_database_data_order_site(){
	try {
		$i=0;
		$DBH=func_get_arg(0);
		$request=func_get_arg(1);
		$params=func_get_arg(2);
		$stmt = $DBH->prepare($request);
		$stmt->execute($params);
		while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
			$res[$i]=array('id' =>$row->Site_Order_id,'text' =>$row->text,'type' =>$row->type,'customerid' =>$row->customer_id,'date' =>$row->date,'time' =>$row->time);
			$i++;}
		if (isset($res)){return $res;}	
		else {return null;}
	}
	catch (PDOException $e) {
		print "Error!: " . $e->getMessage();
		exit();
	}
}


function Database_request(){
	try{
		$DBH=func_get_arg(0);
		$request=func_get_arg(1);
		$params=func_get_arg(2);
		$sth = $DBH->prepare($request);
		$sth->execute($params);
		$insert_id = $DBH->lastInsertId();
		return $insert_id;
	}
	catch (PDOException $e) {
		print "Error!: " . $e->getMessage();
		exit();
	}
}



