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

require('../config.php');

//The save ID for the comments to be streamed from
$SaveID = addslashes(htmlspecialchars($_GET['ID']));

if(isset($_GET['Start']) && isset($_GET['Count']) && !isset($_POST['Comment'])){

//Start at comment number
$Start = addslashes(htmlspecialchars($_GET['Start']));

//Number of comments they want
$Count = addslashes(htmlspecialchars($_GET['Count']));

//Gather information
$QuerySave_Check = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID' AND `Published` <= '1'") or die(mysql_error()); 
//Number of Rows
$CheckSave_Number = mysql_num_rows($QuerySave_Check);

if($CheckSave_Number == 1){

	//Prepare the database query to search the author's user 
	$Comment_Query = $conn2 -> prepare("SELECT * FROM `Comments` WHERE `Save_ID` = :Save_ID ORDER BY Date DESC LIMIT :Count OFFSET :Start");

	$Comment_Query ->bindValue(':Save_ID', (int) $SaveID, PDO::PARAM_INT);
	
	$Comment_Query ->bindValue(':Count', (int) $Count, PDO::PARAM_INT);
	
	$Comment_Query ->bindValue(':Start', (int) $Start, PDO::PARAM_INT);
	
	//Execute the entry for finding a username similar or exactly to the client's chosen
	$Comment_Query ->execute();
	
	$Comment_Count = $Comment_Query->rowCount();
	
	$Comment_CountIni = 0;
	
	echo "[";
	
	//While Loop out the comments
	while($Comment_Fetch = $Comment_Query->fetch()){
	
		//Query for mysql
		$Author_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$Comment_Fetch[Author]'");

		//Author information array
		$AuthorArray = mysql_fetch_array($Author_Query);
		

		//Grabs Author name out of array
		$AuthorUserName = $AuthorArray['Username'];
		
		//Save Comments mysql Query
		$Comment_SaveAuthor_Query = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID'");
		$Comment_SaveAuthor_Array = mysql_fetch_array($Comment_SaveAuthor_Query);
		
		
		
		if($Comment_Fetch['Author'] == $Comment_SaveAuthor_Array['Author']){
		
			$Formatted_AuthorUserName = "$AuthorUserName";
		
		}elseif($AuthorArray['Elevation'] == 5){
		
			$Formatted_AuthorUserName = "\bt$AuthorUserName";
			
		}elseif($AuthorArray['Elevation'] == 10){
		
			$Formatted_AuthorUserName = "\br$AuthorUserName";
		
		}else{
		
			$Formatted_AuthorUserName = "$AuthorUserName";
		
		}
		
		$DateTime = $Comment_Fetch['Date'];
		
		//$Comment = str_replace("\\'","'", $Comment_Fetch['Comment']);
		$Comment = $Comment_Fetch['Comment'];
				
		
		echo "{\"Username\":\"$AuthorUserName\",\"UserID\":\"$Comment_Fetch[Author]\",\"Gravatar\":\"\/Avatars\/1_001.png\",\"Text\":\"$Comment\",\"Timestamp\":\"$DateTime\",\"FormattedUsername\":\"$Formatted_AuthorUserName\"}";
		
		$Comment_CountIni++;
		
		if($Comment_Count > $Comment_CountIni){
		
			echo ",";
		
		}
	}
	echo "]";
	
}else{

	echo "[]";

}
	
}else{
	$SaveID = addslashes(htmlspecialchars($_GET['ID']));
	$Comment = $_POST['Comment'];
	$IP_Address = inet_pton($_SERVER['REMOTE_ADDR']);
	
	$Comment = str_replace('\\',"\\\\", $Comment);
	$Comment = str_replace('"','\"', $Comment);
	$Comment = preg_replace('/\s+/', ' ', $Comment);

	if($Logged){
	
	
		//Gather information
		$QuerySave_Check = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID' AND `Published` <= '1'") or die(mysql_error()); 
		//Number of Rows
		$CheckSave_Number = mysql_num_rows($QuerySave_Check);
		
		if($CheckSave_Number == 1){
		
			if(!empty($Comment)){
			
				if(strlen($Comment) <= 1024){
					
					$DateTime = time();
					
					$Spam_Time_Limit = $DateTime - 15;
					
					//Gather information
					$QueryComment_Check = mysql_query("SELECT * FROM `Comments` WHERE `Author` = '$UserID' AND `Date` >= '$Spam_Time_Limit'") or die(mysql_error()); 
					//Number of Rows
					$CheckComment_Space = mysql_num_rows($QueryComment_Check);
					
					if($CheckComment_Space < 1){
					
						//mysql_query("INSERT INTO `Comments`(`Author`, `Comment`, `Date`, `Save_ID`, `IP_Address`) VALUES ('$UserID','$Comment','$DateTime','$SaveID', '$IP_Address')");
						
						$stmt = $conn2->prepare('INSERT INTO `Comments`(`Author`, `Comment`, `Date`, `Save_ID`, `IP_Address`) 
						VALUES (:UserID,:Comment,:DateTime,:SaveID, :IP_Address)');

						$stmt->execute(array(':UserID'=> $UserID ,':Comment'=> $Comment ,':DateTime'=> $DateTime ,':SaveID'=> $SaveID ,':IP_Address'=> $IP_Address));
						
						//Grabs the comment's ID
						$Comment_ID = $conn2->lastInsertId("ID"); 
						
						//Fetch array for save
						$Save_Information = mysql_fetch_array($QuerySave_Check);
						
						//Save Author
						$Save_AuthorID = $Save_Information['Author'];
						
						//Adds comment to notification system if enabled
						
						//Gather settings information
						$Setting_Check = mysql_query("SELECT * FROM `Notifications__Settings__User` WHERE `User` = '$Save_AuthorID' AND `Setting` = '1' AND `Status` = '1'") or die(mysql_error()); 
						//Number of Rows
						$Setting_Check = mysql_num_rows($Setting_Check);
						
						if($Setting_Check == 1 && $UserID != $Save_Information['Author']){
							
							//The string added to the content of the index entry for notifications
							$Comment_String = "@$UserID has commented on one of your saves!";
							
							//Adds the comment notification to the index (the content)
							$Comment_Notify_Add = $conn2->prepare('INSERT INTO `Notifications__Index`(`Type`, `Content`) 
							VALUES (:Type,:Content)');

							//Executes comment insertion
							$Comment_Notify_Add->execute(array(':Type'=> '3' ,':Content'=> $Comment_String));
						
							//Notification ID
							$Notification_ID = $conn2->lastInsertId("ID"); 
							
							//Group ID for user (individual)
							$User_Group_ID = "U-$Save_AuthorID";
						
							//Add comment notification entry
							$Comment_Notify = $conn2->prepare('INSERT INTO `Notifications__Sent`(`Notification`, `Group_ID`, `Time`) 
							VALUES (:Notification,:Group_ID,:Time)');
							
							//Execute entry
							$Comment_Notify->execute(array(':Notification'=> $Notification_ID ,':Group_ID'=> $User_Group_ID ,':Time'=> $DateTime));
							
							//Creates the prepared statement for inserting it into the Comments Notification Merge table
							$Comment_Specific_Notify = $conn2->prepare('INSERT INTO `Notifications__Comments`(`Save_ID`, `Comment_ID`, `Date`) 
							VALUES (:SaveID,:CommentID,:Date)');
							
							//Execute the entry
							$Comment_Specific_Notify->execute(array(':SaveID'=> $SaveID ,':CommentID'=> $Comment_ID ,':Date'=> $DateTime));
							
						}
						
						echo "{\"Status\":1}";
						
					}else{
						
						echo "{\"Status\":0,\"Error\":\"Whoa, you've been posting comments to quickly!\"}";
						
					}
				
				}else{
				
					echo "{\"Status\":0,\"Error\":\"Comment must be 1024 characters or less!\"}";
				
				}
			
			}else{
			
			}
		
		}else{
		
			echo "{\"Status\":0,\"Error\":\"Save no longer exists!\"}";
		
		}
	
	}else{
	
		echo "{\"Status\":0,\"Error\":\"Not authorized!\"}";
	
	}

}


?>