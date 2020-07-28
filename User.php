<?php
/*

TPTNet-Server is an open source The Powder Toy server. TPTNet-Net is mainly
scripted in PHP and intefaces with the renderer built into The Powder Toy.

Copyright (C) 2015  Departing (Github)

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

include('config.php');

if(isset($_GET['Name'])){
	//Searches by username
	
	//Username variable is defined
	$Username = addslashes(htmlspecialchars($_GET['Name']));

	//Perpare username statement
	$Prepare_Username_Check = $conn2 -> prepare("SELECT * FROM `Registered` WHERE `Username` LIKE :Username");

	//Execute the entry for finding a username similar or exactly to the client's chosen
	$Prepare_Username_Check ->execute(array(':Username' => "$Username"));

}elseif(isset($_GET['ID'])){
	//Searches by User ID number
	
	//Username variable is defined
	$Username_ByID = addslashes(htmlspecialchars($_GET['ID']));

	//Perpare username statement
	$Prepare_Username_Check = $conn2 -> prepare("SELECT * FROM `Registered` WHERE `ID` = :Username_ByID");

	//Execute the entry for finding a username similar or exactly to the client's chosen
	$Prepare_Username_Check ->execute(array(':Username_ByID' => "$Username_ByID"));
	
}

//Count the number of rows similar to the chosen username
$Username_Array = $Prepare_Username_Check->fetch();

//Count the number of rows for users similar to the chosen username
$Username_Count = $Prepare_Username_Check->rowCount();

//Perpare username statement
$Prepare_Saves_Count = $conn2 -> prepare("SELECT * FROM `Saves` WHERE `Author` = :AuthorID AND `Published` = '1'");

//Execute the entry for finding a username similar or exactly to the client's chosen
$Prepare_Saves_Count ->execute(array(':AuthorID' => "$Username_Array[ID]"));

//Count the number of rows similar to the chosen username
$Save_Count = $Prepare_Saves_Count->rowCount();

if($Username_Count == 1){
echo "{\"User\":{\"Username\":\"$Username_Array[Username]\",\"ID\":$Username_Array[ID],\"Avatar\":\"http:\/\/powdertoy.co.uk\/Design\/Images\/Avatar.png\",\"Age\":9001,\"Location\":\"null\",\"Biography\":\"I am a user on The Powder Toy Network\",\"Website\":null,\"Saves\":{\"Count\":$Save_Count,\"AverageScore\":0,\"HighestScore\":0},\"Forum\":{\"Topics\":0,\"Replies\":0,\"Reputation\":0}}}
";
}else{
	
	echo "404";
	
}
?>
