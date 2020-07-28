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
along with this program.  If not, see <http://www.gnu.org/licenses/>.

config.php
This file is the main file that contains most of the needed functions
in server operations. Things such as authentication and session chec-
king.


*/

//The database connection file
require('connector.php');

//Header content type. People kept complaining about it
header('Content-Type: application/json');

//Gather all the headers, name it as "headers" for ease
//of use.
$headers = apache_request_headers();

//Checking if the Session Key is set, but the request does not involve a secondary key in the URL GET.
if( isset($headers['X-Auth-Session-Key']) && !isset($_GET['Key']) ){
	
	//Grabs the Session Key.
	
	//Alright here's where things get confusing, the system was weirdly assigned where keys were actually
	//IDs and IDs where actually keys. I swear somewhere along the lines the Client had different assign-
	//ments from the headers. If  mention Session Key, I'm refering to the header variable, NOT the named
	//variable you're about to see!
	
	//Assigns the Session Key header to a variable
	$SessionID = addslashes(htmlspecialchars($headers['X-Auth-Session-Key']));
	
	//Assigns the User ID header to a variable
	$UserID = addslashes(htmlspecialchars($headers['X-Auth-User-Id']));
	
	//Hashes the Session Key to sha512. Probably overkill, but eh?
	$Hashed_SessionID = hash('sha512', $SessionID);

	//The MySQL query requesting where a hashed Session Key and User ID is located. Also checks if the Session in question is valid.
	//If type == 1, The session is a web session.
	//If type == 1, The session is a TPT session.
	
	//If status == 0, The session is valid.
	//If the session is == to anything else, assume it's invalid.
	//Future uses possible.
	$QuerySessionCheck = mysql_query("SELECT * FROM `Sessions` WHERE `SessionID` LIKE '$Hashed_SessionID' AND `User` = '$UserID' AND `Type` = '0' AND `Status` = '1'") or die(mysql_error()); 
	
	//Number of rows found
	$CheckSessionNumber = mysql_num_rows($QuerySessionCheck);

	//The MySQL query requesting where the user information is located, via User ID.
	//Confirms the user ID is valid
	$QueryRegistrationCheck = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$UserID' AND `Status` = '1'") or die(mysql_error()); 
	
	//Number of rows found
	$CheckRegistrationNumber = mysql_num_rows($QueryRegistrationCheck);

	//A quick and dirty way to determine if both are found
	$TotalCheck_Session_User = $CheckRegistrationNumber + $CheckSessionNumber;

	//Considering if both the User ID check and the Session Key check outputs one found on each, add them up, you should get 2.
	//If for some reason this returns something greater than two, well there's probably a duplicate in the database.
	if($TotalCheck_Session_User == 2){

		//Output true logged status to a variable for JSON later
		$Logged = true;
		
		//User Information Row
		//An easy way to gather user ifnormation on a row.
		$UIR = mysql_fetch_array($QueryRegistrationCheck);

	//Anything other than 2 is assumed to be invalid
	}else{

		//Output a false logged status to a variable for JSON later
		$Logged = false;

	}
	
//Checking if the Session Key is set, but the request does REQUIRE a secondary key in the URL GET.
}elseif( isset($headers['X-Auth-Session-Key']) && isset($_GET['Key']) ){
	
	//Grabs the Session Key.
	
	//Alright here's where things get confusing, the system was weirdly assigned where keys were actually
	//IDs and IDs where actually keys. I swear somewhere along the lines the Client had different assign-
	//ments from the headers. If  mention Session Key, I'm refering to the header variable, NOT the named
	//variable you're about to see!
	
	//Assigns the Session Key to a Session ID variable. 
	$SessionID = addslashes(htmlspecialchars($headers['X-Auth-Session-Key']));
	
	//Assigns the User ID to a User ID variable
	$UserID = addslashes(htmlspecialchars($headers['X-Auth-User-Id']));
	
	//Assigns the request GET URL key to a Key variable. For simplicity it's named Key
	$Key = addslashes(htmlspecialchars($_GET['Key']));
	
	//Hashing the Session Key with sha512. Probably overkill, but eh
	$Hashed_SessionID = hash('sha512', $SessionID);

	//Gather information
	$QuerySessionCheck = mysql_query("SELECT * FROM `Sessions` WHERE `SessionID` LIKE '$Hashed_SessionID' AND `User` = '$UserID' AND `SessionKey` = '$Key' AND `Type` = '0' AND `Status` = '1'") or die(mysql_error()); 
	//Number of Rows
	$CheckSessionNumber = mysql_num_rows($QuerySessionCheck);

	//Gather information
	$QueryRegistrationCheck = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$UserID'") or die(mysql_error()); 
	//Number of Rows
	$CheckRegistrationNumber = mysql_num_rows($QueryRegistrationCheck);
	
	$TotalCheck_Session_User = $CheckRegistrationNumber + $CheckSessionNumber;
	
	if($TotalCheck_Session_User == 2){

		$Logged = true;
		
		//User Information Row
		$UIR = mysql_fetch_array($QueryRegistrationCheck);

	}else{

		$Logged = false;

	}
	
}else{

	$Logged = false;

}
?>