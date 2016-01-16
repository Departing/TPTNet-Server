<?php
/*

TPTNet-Server is an open source The Powder Toy server. TPTNet-Net is mainly
scripted in PHP and intefaces with the renderer built into The Powder Toy.

Copyright (C) 2015  Lockheedmartin (Github)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>. */
require('../config.php');

if(!$Logged){

	echo "401";

}else{
	$SaveID = addslashes(htmlspecialchars($_GET['ID']));
	$Mode = addslashes(htmlspecialchars($_GET['Mode']));

	//Gather information
	$QuerySave_Check = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID'") or die(mysql_error());
	//Number of Rows
	$CheckSave_Number = mysql_num_rows($QuerySave_Check);
	
	if($CheckSave_Number == 1){
	
		//Gather information
		$QueryFavourite_Check = mysql_query("SELECT * FROM `Favourites` WHERE `Save_ID` = '$SaveID' AND `User` = '$UserID'") or die(mysql_error()); 
		//Number of Rows
		$CheckSave_Favourite = mysql_num_rows($QueryFavourite_Check);
		
		if($CheckSave_Favourite == 0){
		
			mysql_query("INSERT INTO `Favourites`(`User`,`Save_ID`) VALUES ('$UserID','$SaveID')");
			
			echo "{\"Status\":1}";
		
		}elseif($CheckSave_Favourite == 1 && $Mode == 'Remove'){
	
			mysql_query("DELETE FROM `Favourites` WHERE `Save_ID` = '$SaveID' AND `User` = '$UserID'");
			
			echo "{\"Status\":1}";
			
	
		}else{
	
			echo "{\"Status\":0,\"Error\":\"This save is already in your favourites!\"}";
			
		}
	
	
	}else{
	
		echo "{\"Status\":0,\"Error\":\"That save does not exist!\"}";
	
	}

}

?>