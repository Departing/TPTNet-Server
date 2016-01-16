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

if($Logged){
	$Session_Check = "true";
}else{
	$Session_Check = "false";
}
//{\"Link\":\"\",\"Text\":\"\bgTest\"}

//-----CURRENT BUILD/MAJOR/MINOR NUMBERS RELEASE-----//
$Major = '90';
$Minor = '2';
$Build = '320';

//The user agent is found
$User_Agent_String = $headers['User-Agent'];

//Rips apart the secret agent trying to spy on the server
//We want their information. They WILL give it to us
$User_Agent_Specs = preg_split( "/(\(|\))/", $User_Agent_String );

//Tells us what operating system they're using
$User_Agent_OS_String = $User_Agent_Specs['1'];

$User_Agent_OS = explode('; ', $User_Agent_OS_String);

switch($User_Agent_OS[0]){
	
	case 'WIN32':
		$Release_Build = 'Update_Windows32_SSE2';
		
		break;
		
	case 'LIN32':
		$Release_Build = 'Update_Linux32_SSE2';
	
		break;
	
	case 'LIN64':
		$Release_Build = 'Update_Linux64_SSE2';
	
		break;
	
	case 'MACOSX':
		$Release_Build = 'Update_MacOSX_SSE2';
	
		break;
}

$Release_Build_Dir = "\/Downloads\/Updates\/$Release_Build";

echo "{\"Updates\":{\"Stable\":{\"Major\":$Major,\"Minor\":$Minor,\"Build\":$Build,\"File\":\"$Release_Build_Dir\"},\"Beta\":{\"Major\":90,\"Minor\":1,\"Build\":320,\"File\":\"\/Download\/Builds\/Build-320\/-.ptu\"},\"Snapshot\":{\"Major\":83,\"Minor\":3,\"Build\":208,\"Snapshot\":1346881831,\"File\":\"\/Download\/Builds\/TPTPP\/-.ptu\"}},\"Notifications\":[],\"Session\":$Session_Check,\"MessageOfTheDay\":\"\bt{a:http:\/\/ThePowderToy.Net\/Register.php|Register here!}\"}";

?>