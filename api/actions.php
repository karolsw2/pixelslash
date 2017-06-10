<?php
	// All most important in-game actions such as buying, selling, equipping and unequipping items, opening chests and more
	session_start();
	include("../config.php");

	$action = $_POST["action"];
	$item_id = $_POST["item_id"];

	$query = mysqli_query($a,"select * from items where id='$item_id'");
	$item_info = mysqli_fetch_array($query);

	$percent_of_real_price_in_shop = 0.25; // Set selling price of items according to the prices in shop

	// Users information
	$user_login = $_SESSION['login'];
	$query = mysqli_query($a, "select * from `users` where `user`='$user_login'");
	$user_data = mysqli_fetch_array($query);
	$player_weared_equipment = explode(";", $user_data["eq_weared"]); 
	$player_equipment = explode(";", $user_data["eq"]);
	$item_index_in_player_eq = $_POST["item_index_in_player_eq"];
	//
	$alert = "";

	switch ($action){
		case "equip":
			for($i=0;$i<count($player_weared_equipment);$i++){	
				$query = mysqli_query($a,"select * from items where id='$player_weared_equipment[$i]'");
				$item_info = mysqli_fetch_array($query);
				$types_of_eq_acctually_weared_by_user[$item_info['type']] = true;
			}

			if($types_of_eq_acctually_weared_by_user[$item_info['type']] == true){ // User already have this type of item weared!
				$alert = "You are already wearing this type of equipment";
				exit();
			}

			if($user_data["lvl"]<$item_info["lvl"]){
				$alert = "Your lvl is too low";
				exit();
			}

			unset($player_equipment[$item_index_in_player_eq]);
			$updated_player_equipment = implode(";",$player_equipment);

			$item_id .= ";";

			mysqli_query($a, "UPDATE `p505207_db`.`users` SET `eq`='$updated_player_equipment' WHERE `users`.`user`='$user_login' ");
			mysqli_query($a, "UPDATE `p505207_db`.`users` SET `eq_weared`=CONCAT(`eq_weared`,'$item_id') WHERE `users`.`user`='$user_login' ");	
			$alert = "Item equipped successfully";		
			break;
		case "unequip":
			$item_id = $player_weared_equipment[$item_index_in_player_eq];
			unset($player_weared_equipment[$item_index_in_player_eq]);
			$updated_player_weared_equipment = implode(";",$player_weared_equipment);
			$item_id .= ";";
			mysqli_query($a, "UPDATE `p505207_db`.`users` SET `eq_weared`='$updated_player_weared_equipment' WHERE `users`.`user`='$user_login' ");
			mysqli_query($a, "UPDATE `p505207_db`.`users` SET `eq`=CONCAT(`eq`,'$item_id') WHERE `users`.`user`='$user_login' ");
			$alert = "Item unequipped successfully";
			break;
		case "buy": // !important - For first check if player has got enough money to buy the item!
			$query = mysqli_query($a,"select * from items where id='$item_id'");
			$item_id .= ";";
			mysqli_query($a, "UPDATE `p505207_db`.`users` SET `eq`=CONCAT(`eq`,'$item_id'),`silver_coins`=`silver_coins`-'$item_price' WHERE `users`.`user`='$user_login' ");
			$alert = "Item bought successfully";
			break;
		case "sell":
			$item_price = $item_info['price']*$percent_of_real_price_in_shop;
			unset($player_equipment[$item_index_in_player_eq]);
			$updated_player_equipment = implode(";",$player_equipment);
			mysqli_query($a, "UPDATE `p505207_db`.`users` SET `eq`='$updated_player_equipment',`silver_coins`=`silver_coins`+'$item_price' WHERE `users`.`user`='$user_login' ");
			$alert = "Item sold successfully";
			break;
		case "open_chest":
					
	}
	echo json_encode(array('alert' => $alert));
?>

