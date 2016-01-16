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

require('config.php');

function generateRandomString($length) {
	$chars = array_merge(range('A', 'Z'), range(0, 9));
	shuffle($chars);
	return implode(array_slice($chars, 0, $length));
}

$Username = addslashes(htmlspecialchars($_POST['Username']));
$Hash = addslashes(htmlspecialchars($_POST['Hash']));


$Login_Lockout = 0;

if( $Login_Lockout != 1){
	if(empty($Username) || empty($Hash)){

		echo '{"Status":0,"Error":"Empty Username or Password field."}';

	}else{
		//Gather information
		$QueryUsername = mysql_query("SELECT * FROM `Registered` WHERE `Username` LIKE '$Username' AND `Status` >= '1'") or die(mysql_error()); 
		//Number of Rows
		$CountUsername = mysql_num_rows($QueryUsername);

		if($CountUsername != 1 ){
		//If the User name if incorrect!
			echo '{"Status":0,"Error":"Username or Password incorrect."}';

		}else{

			$FetchUserInfo = mysql_fetch_array($QueryUsername);

			if($FetchUserInfo['Hash'] != $Hash){
			//If the Password Hash does NOT match the database
				echo '{"Status":0,"Error":"Username or Password incorrect."}';
			}else{
			//If the Password Hash does match the database
				
				//Initializes the While Loop Value for Checking
				$CheckSessionNumber = '1';
				
				while($CheckSessionNumber == 1){
				
				$SessionID = generateRandomString(30);
				$SessionKey = generateRandomString(10);
				
				$Hashed_SessionID = hash('sha512', $SessionID);
				
				//Gather information
				$QuerySessionCheck = mysql_query("SELECT * FROM `Sessions` WHERE `SessionID` LIKE '$Hashed_SessionID' OR `SessionKey` = '$SessionKey'") or die(mysql_error()); 
				
				//Number of Rows
				$CheckSessionNumber = mysql_num_rows($QuerySessionCheck);
				
				}
				
				$UserID = $FetchUserInfo['ID'];
				
				$Elevation = $FetchUserInfo['Elevation'];
				
				//Time of login in Unix
				$DateTime = time();
				
				//Condensed IP address
				$IP_Address = inet_pton($_SERVER['REMOTE_ADDR']);
				
				mysql_query("INSERT INTO `Sessions` (`User`, `SessionID`, `SessionKey`, `Date`, `IP_Address`) VALUES('$UserID', '$Hashed_SessionID', '$SessionKey', '$DateTime', '$IP_Address')");
				
				
				$_SESSION['SessionID'] = $SessionID;
				
				//Encryption Key
				$_SESSION['SessionKey'] = $SessionKey;
				
				echo '{"Status":1,"UserID":';
				
				//Prints User ID
				echo $UserID;
				
				echo ',"SessionID":"';
				
				//Prints Sessiond ID
				echo $SessionID;
				
				echo '","SessionKey":"';
				
				//Prints SessionKey
				echo $SessionKey;
				
				//Check if staff member
				if($Elevation >= 5){
					
					if($Elevation == 10){
						
						$Client_Elevation = "Admin";
						
					}else{
						
						$Client_Elevation = "Mod";
						
					}
				
				}else{
					
					$Client_Elevation = "None";
					
				}
				
				//Closes the lines off
				echo '","Elevation":"'.$Client_Elevation.'","Notifications":[]}';
				
				
			}

		}
	}
}else{
	
	echo '{"Status":0,"Error":"Login lockout enabled. Please try again later"}';
	
}
?>