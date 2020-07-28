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

require('config.php');

$Server_Shutdown_Protocol = false;

//Checks if the server is shutdown for saving
if(!$Server_Shutdown_Protocol){
	
	if($Logged){
		if($_FILES['Data']['tmp_name'] <= '16777216'){
		
			if($_FILES['Data']['name'] == 'save.bin'){
			//Check if the name being uploaded matches the required safe version
			
				//Name
				$Name = $_POST['Name'];
				// $Name = str_replace('\\','\\\\', $Name);
				// $Name = str_replace('"','\"', $Name);
				// $Name = preg_replace('/\s+/', ' ', $Name);
				
				if(!preg_match('/[^a-zA-Z0-9\-\\s!.,\(\)]/', $Name)){
				
					//Description
					$Description = $_POST['Description'];
					$Description = str_replace('"','\"', $Description);
					$Description = preg_replace('/\s+/', ' ', $Description);
					
					//Viewing status
					$Published = $_POST['Publish'];
					
					//Date time
					$DateTime = time();
					
					//Gather information
					$Query_Resave_Check = $conn2 -> prepare("SELECT * FROM `Saves` WHERE `Author` = :UserID AND `Name` = :Name AND `Published` <= '1'");
					//Execute the entry
					$Query_Resave_Check ->execute(array(':UserID' => "$UserID",':Name' => "$Name"));
					//Number of Rows
					$Count_Resave_Check = $Query_Resave_Check->rowCount();
					
						if( (strlen($Name) <= 50 ) && (strlen($Description) <= 254 ) && strlen(preg_replace('/\s+/', '', $Name)) >= 3 && strlen(preg_replace('/\s+/', '', $Description)) >= 5){
						//Check the save character mininum for name and description
						
							if($Published == 'Public'){
							//Check if it's public or private
							
								//Public save
								$SaveViewing = '1';
								
							}else{
							//Private Save
							
								$SaveViewing = '0';
								
							}
							
							//Checks for the project's existance
							if($Count_Resave_Check == 0){
								//They are not resaving the project	
								
								//Inserts it into the database
								//$UploadEntry_Save = mysql_query("INSERT INTO `Saves` (`Author`, `Name`, `Description`, `Published`, `Date_Uploaded`, `Date_Updated`) 
								//VALUES('$UserID', '$Name', '$Description', '$SaveViewing', '$DateTime', '$DateTime')");
								
								
								//$SaveID = mysql_insert_id();
								$stmt = $conn2->prepare('INSERT INTO Saves (Author, Name, Description, Published, Date_Uploaded, Date_Updated) 
								VALUES (:Author, :Name, :Description, :Published, :Date_Uploaded, :Date_Updated)');

								$stmt->execute(array(':Author'=> $UserID ,':Name'=> $Name ,':Description'=> $Description ,':Published'=> $SaveViewing ,':Date_Uploaded'=> $DateTime ,':Date_Updated'=> $DateTime));

								$SaveID = $conn2->lastInsertId("ID"); 
							
							}else{
								
								//They are resaving it
								
								//Fetch the array of the existing entry
								$Fetch_Resave_Array = $Query_Resave_Check->fetch();
								
								//Assign a varible to an ID
								$SaveID = $Fetch_Resave_Array['ID'];
								
								//Past time of upload
								$Save_Upload_History_Time = $Fetch_Resave_Array['Date_Updated'];
								
								$History_Name = $Fetch_Resave_Array['Name'];
								
								$History_Description = $Fetch_Resave_Array['Description'];
								
								//Preparing the statement for updating the times
								$stmt = $conn2->prepare("UPDATE `Saves` SET `Date_Updated` = :DateTime, `Description` = :Description, `Published` = :SaveViewing WHERE `ID` = :SaveID");
								
								//Execute the command for updating
								$stmt->execute(array(':DateTime'=> $DateTime, ':Description'=> $Description, ':SaveViewing'=>$SaveViewing, ':SaveID'=> $SaveID));
								
								
								//PAST TIME ENTRY INSERTION
								//USE PAST LAST SNAPSHOT DETAILS
								
								//Save the history snapshot into the database entry.
								$History_Entry = $conn2->prepare('INSERT INTO Saves__History (Save_ID, Author, Name, Description, Date_Updated) 
								VALUES (:Save_ID, :Author, :Name, :Description, :Date_Updated)');

								$History_Entry->execute(array(':Save_ID'=> $SaveID ,':Author'=> $UserID ,':Name'=> $History_Name ,':Description'=> $History_Description ,':Date_Updated'=> $Save_Upload_History_Time));

								
								//Moves the current save files to their respective history bins
								
								
								//List of extensions
								$File_History_Suffix = array('Published'=>'.cps','LargePNG'=>'_large.png','LargePTI'=>'_large.pti','SmallPNG'=>'_small.png','SmallPTI'=>'_small.pti');
								
								//Loop through the array
								foreach($File_History_Suffix as $Type => $File_Ext){
									
									//Sorted file's intial directive
									$Save_Current = "Static/$Type/".$SaveID."$File_Ext";
									
									//Sorted file's final directive
									$Save_History_Snapshot = "Static/$Type/History/".$SaveID."_".$Save_Upload_History_Time."$File_Ext";
									
									//Move file
									rename("$Save_Current","$Save_History_Snapshot");
									
								}
								
							}
							
							//Save file name
							$SaveIDName = "$SaveID.cps";
							
							//Defines the directory where we shal store saves.
							$SavesDirectory = "Static/";
							
							//The targeted path for movement
							$target_path = $SavesDirectory . basename($SaveIDName); 
							

							//Moving the actual file
							$Saved = move_uploaded_file($_FILES['Data']['tmp_name'], $target_path);
							
							//Combined final destination and file
							$Final_Render_File_Output = "$SavesDirectory"."$SaveID";
							
							//Execute a shell command to run the renderer
							
							//Runs the program
							// ./Static/render64 
							
							//The current file path of the bin file
							// $target_path
							
							//Final directory for saving the output
							// $SavesDirectory$SaveID
							
							//Execution
							exec("./Static/render64 $target_path $Final_Render_File_Output");
							
 							//List of extensions
							$File_Suffix = array('Published'=>'.cps','LargePNG'=>'_large.png','LargePTI'=>'_large.pti','SmallPNG'=>'_small.png','SmallPTI'=>'_small.pti');
							
							//Loop through the array
							foreach($File_Suffix as $Type => $File_Ext){
								
								//Orginal directory
								$Save_Original = $SavesDirectory . $SaveID . "$File_Ext";
								
								//Sorted file's final director
								$Save_Sorted = "Static/$Type/".$SaveID."$File_Ext";
								
								//Move file
								rename("$Save_Original","$Save_Sorted");
								
							}
							
							/* $Save_CPS_Original = $SavesDirectory . $SaveID . ".cps";
							$Save_CPS_Sorted = "Static/Published/".$SaveID.".cps";

							$Save_LargePNG_Original = $SavesDirectory . $SaveID . "_large.png";
							$Save_LargePNG_Sorted = "Static/LargePNG/".$SaveID."_large.png";

							$Save_SmallPNG_Original = $SavesDirectory . $SaveID . "_small.png";
							$Save_SmallPNG_Sorted = "Static/SmallPNG/".$SaveID."_small.png";

							$Save_LargePTI_Original = $SavesDirectory . $SaveID . "_large.pti";
							$Save_LargePTI_Sorted = "Static/LargePTI/".$SaveID."_large.pti";

							$Save_SmallPTI_Original = $SavesDirectory . $SaveID . "_small.pti";
							$Save_SmallPTI_Sorted = "Static/SmallPTI/".$SaveID."_small.pti";

							rename("$Save_CPS_Original","$Save_CPS_Sorted");
							rename("$Save_LargePNG_Original","$Save_LargePNG_Sorted");
							rename("$Save_SmallPNG_Original","$Save_SmallPNG_Sorted");
							rename("$Save_LargePTI_Original","$Save_LargePTI_Sorted");
							rename("$Save_SmallPTI_Original","$Save_SmallPTI_Sorted"); */
							

							//Checks if everything turned out okay.
							if($Saved) {
								//Everything is okay
								
								echo "OK, $SaveID";
								
							} else{
								//Not okay on the server's side
								
								echo "Server error upload";
								
							}
							
						//End of valid entries
						}elseif( (strlen($Name) <= 50 ) && (strlen($Description) > 254 ) && strlen(preg_replace('/\s+/', '', $Name)) >= 3 && strlen(preg_replace('/\s+/', '', $Description)) >= 5){
							//If the save has a description too long
							
							//Output error to user
							echo "Description too long!";
							
						}elseif( (strlen($Name) <= 50 ) && (strlen($Description) <= 254 ) && strlen(preg_replace('/\s+/', '', $Name)) < 3 && strlen(preg_replace('/\s+/', '', $Description)) >= 5){
							//Save title is too short
							
							//Output error to user
							echo "Save title too short!";
							
						}elseif( (strlen($Name) <= 50 ) && (strlen($Description) <= 254 ) && strlen(preg_replace('/\s+/', '', $Name)) >= 3 && strlen(preg_replace('/\s+/', '', $Description)) < 5){
							//Description is too vague
							
							//Output error to user
							echo "Tell us something about the save (Five (5) characters or more)!";
							
						}else{
							//Save title too long
							
							//Output error to user
							echo "Save title too long!";
							
						}
						
				}else{
					echo "Invalid save name!";
				}
				
				
			}else{
				echo "Invalid file format!";
			}
		}else{
			echo "File size too large!";
		}
		
	}else{
		echo "Not authenticated server-side!";
	}

}else{
	echo "Server is offline!";
}
?>