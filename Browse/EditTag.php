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

if($Logged){

	$Option = addslashes(htmlspecialchars($_GET['Op']));
	$SaveID = addslashes(htmlspecialchars($_GET['ID']));
	$Tag = addslashes(htmlspecialchars($_GET['Tag']));
		
	if(!preg_match('/[^a-zA-Z0-9\-]/', $Tag)){
	
		if(strlen($Tag) <= 16){
		
			//Date time
			$DateTime = time();
			
			//Gather information
			$QuerySave_Check = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID'") or die(mysql_error()); 
			//Number of Rows
			$CheckSave_Number = mysql_num_rows($QuerySave_Check);
			
			if($CheckSave_Number == 1){
				
				switch($Option)
				{

					case 'add':
					
						//Gather information
						$QueryTag_Check = mysql_query("SELECT * FROM `Tags` WHERE `Save_ID` = '$SaveID' AND `Tag` LIKE '$Tag' AND `Status` = '0'") or die(mysql_error()); 
						//Number of Rows
						$CheckTag_Number = mysql_num_rows($QueryTag_Check);
						
						//Gather information
						$QueryTag_NumCheck = mysql_query("SELECT * FROM `Tags` WHERE `Save_ID` = '$SaveID'") or die(mysql_error()); 
						//Number of Rows
						$CheckTag_NumberLimit = mysql_num_rows($QueryTag_NumCheck);
						
						if($CheckTag_NumberLimit < 10){
						
							if($CheckTag_Number == 0){
								
								$IP_Address = inet_pton($_SERVER['REMOTE_ADDR']);
							
								mysql_query("INSERT INTO `Tags`(`Save_ID`, `Tag`, `Author`, `Date`, `IP_Address`) VALUES ('$SaveID','$Tag','$UserID','$DateTime', '$IP_Address')");
								echo "{\"Status\":1,";
								
								echo "\"Tags\":[";

								//Tags Viewing MySQL Query
								$Tags_Query = mysql_query("SELECT * FROM `Tags` WHERE `Save_ID` = '$SaveID' AND `Status` = '0'") or die(mysql_error()); 

								//Tags viewing counting
								$Tags_Count = mysql_num_rows($Tags_Query);

								//Tags view count initialization
								$Tags_CountIni = 0;

								//Tags Viewing Fetch Array/While Loop
								while($Tags_Array = mysql_fetch_array($Tags_Query)){

									echo "\"$Tags_Array[Tag]\"";
									
									$Tags_CountIni++;
									
									if($Tags_CountIni != $Tags_Count){
										echo ",";
									}

								}

								echo "]}";
								
							}else{
							
								echo "{\"Status\":0,\"Error\":\"Tag already exists!\"}";
							
							}
						}else{
						
							echo "{\"Status\":0,\"Error\":\"Max # of tags already exists!\"}";
						
						}
						
						break;
					
					case 'delete':
					
						//Save Author ID Array to fetch
						$Author_ID_Fetch_Array = mysql_fetch_array($QuerySave_Check);

						//Author ID
						$Author_ID = $Author_ID_Fetch_Array['Author'];

						//Gather information
						$QueryTag_Check = mysql_query("SELECT * FROM `Tags` WHERE `Save_ID` = '$SaveID' AND `Tag` LIKE '$Tag' AND `Status` = '0'") or die(mysql_error()); 
						//Number of Rows
						$CheckTag_Number = mysql_num_rows($QueryTag_Check);

						//Check if the user deleting the tag is the author
						if($Author_ID == $UserID || $UIR['Elevation'] >= 5){
						//They are the author and can delete the tag	
							
							if($CheckTag_Number == 1){
								
								$IP_Address = inet_pton($_SERVER['REMOTE_ADDR']);
								
								//Delete the tag entry
								mysql_query("UPDATE `Tags` SET `Status` = '1' WHERE `Save_ID` = '$SaveID' AND `Tag` LIKE '$Tag' AND `Status` = '0'");
								
								//Output Status 1 for success!
								echo "{\"Status\":1,";
								
								//Output the opening tag statement
								echo "\"Tags\":[";

								//Tags Viewing MySQL Query
								$Tags_Query = mysql_query("SELECT * FROM `Tags` WHERE `Save_ID` = '$SaveID' AND `Status` = '0'") or die(mysql_error()); 

								//Tags viewing counting
								$Tags_Count = mysql_num_rows($Tags_Query);

								//Tags view count initialization
								$Tags_CountIni = 0;

								//Tags Viewing Fetch Array/While Loop
								while($Tags_Array = mysql_fetch_array($Tags_Query)){

									echo "\"$Tags_Array[Tag]\"";
									
									$Tags_CountIni++;
									
									if($Tags_CountIni != $Tags_Count){
										echo ",";
									}

								}

								echo "]}";
								
							}else{
							
								echo "{\"Status\":0,\"Error\":\"Tag does not exists!\"}";
							
							}

						//Otherwise they are not the author
						}else{

							//Output error message
							echo "{\"Status\":0,\"Error\":\"You are not authorized to delete this tag!\"}";

						}
					
						break;

				}
				
			}else{
			
				echo "Save no longer exists!";
			
			}
			
		}else{
		
			echo "{\"Status\":0,\"Error\":\"Tag must be less than or equal to 16 characters!\"}";
		
		}
	
	}else{
		echo "{\"Status\":0,\"Error\":Tag must be number and letter only!\"}";
	}

}else{
	echo "401";
}
?>