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

if($Logged){
	
$SaveID = addslashes(htmlspecialchars($_GET['ID']));
$Mode = addslashes(htmlspecialchars($_GET['Mode']));


switch($Mode){

	case 'Unpublish':
		//Gather information
		$Query_CheckSave = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID' AND `Published` = '1'") or die(mysql_error()); 
		//Number of Rows
		$Count_SaveExist = mysql_num_rows($Query_CheckSave);
		
		if($Count_SaveExist == 1){
		
			//Gather information
			$Query_CheckAuthor = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID'") or die(mysql_error()); 
				
			//Checks if they're the author or staff member
			$Fetch_SaveAuthor = mysql_fetch_array($Query_CheckAuthor);
			
			//Assign variable to save author ID
			$Save_Author = $Fetch_SaveAuthor['Author'];
				
			if($Save_Author == $UserID || $UIR['Elevation'] >= 5){
			
					mysql_query("UPDATE `Saves` SET `Published` = '0' WHERE `ID` = '$SaveID'");
					echo "{\"Status\":1}";
			
			}else{
			
				echo "{\"Status\":0,\"Error\":\"You do not have permission to unpublish that save!\"}";
			
			}
			
		
		}else{
		
			echo "{\"Status\":0,\"Error\":\"Save does not exist or is already private!\"}";
		
		}
		break;
		
	case 'Delete':
	
		//Gather information
		$Query_CheckSave = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID' AND `Published` <= '1'") or die(mysql_error()); 
		//Number of Rows
		$Count_SaveExist = mysql_num_rows($Query_CheckSave);

		if($Count_SaveExist == 1){

			//Gather information
			$Query_CheckAuthor = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID' AND `Author` = '$UserID'") or die(mysql_error()); 
			//Number of Rows
			$Count_AuthorMatch = mysql_num_rows($Query_CheckAuthor);
			
			if($Count_AuthorMatch == 1){
			
				mysql_query("UPDATE `Saves` SET `Published` = '10' WHERE `ID` = '$SaveID'");
				
				
				$Deletion_Bin = "../Static/Deleted/";
				
				//List of extensions
				$File_Suffix = array('Published'=>'.cps','LargePNG'=>'_large.png','LargePTI'=>'_large.pti','SmallPNG'=>'_small.png','SmallPTI'=>'_small.pti');
				
				//Loop through the array
				foreach($File_Suffix as $Type => $File_Ext){
					
					//Deletion directory + save name
					$Save_Deleted = $Deletion_Bin . $SaveID . "$File_Ext";
					
					//Selected file's currect directory
					$Save_Selected = "../Static/$Type/".$SaveID."$File_Ext";
					
					//Move file
					rename("$Save_Selected","$Save_Deleted");
					
				}
				
/* 				$Save_CPS_Original = "../Static/Published/".$SaveID.".cps";
				$Save_CPS_Deleted = "../Static/Deleted/".$SaveID.".cps";
				
				$Save_LargePNG_Original = "../Static/LargePNG/".$SaveID."_large.png";
				$Save_LargePNG_Deleted = "../Static/Deleted/".$SaveID."_large.png";
				
				$Save_SmallPNG_Original = "../Static/SmallPNG/".$SaveID."_small.png";
				$Save_SmallPNG_Deleted = "../Static/Deleted/".$SaveID."_small.png";
				
				$Save_LargePTI_Original = "../Static/LargePTI/".$SaveID."_large.pti";
				$Save_LargePTI_Deleted = "../Static/Deleted/".$SaveID."_large.pti";
				
				$Save_SmallPTI_Original = "../Static/SmallPTI/".$SaveID."_small.pti";
				$Save_SmallPTI_Deleted = "../Static/Deleted/".$SaveID."_small.pti";
				
				rename("$Save_CPS_Original","$Save_CPS_Deleted");
				rename("$Save_LargePNG_Original","$Save_LargePNG_Deleted");
				rename("$Save_SmallPNG_Original","$Save_SmallPNG_Deleted");
				rename("$Save_LargePTI_Original","$Save_LargePTI_Deleted");
				rename("$Save_SmallPTI_Original","$Save_SmallPTI_Deleted"); */
				
				echo "{\"Status\":1}";
			
			}else{
			
				echo "{\"Status\":0,\"Error\":\"You do not have permission to delete that save!\"}";
			
			}
			

		}else{

			echo "{\"Status\":0,\"Error\":\"Save does not exist!\"}";

		}
	
		break;
}

}else{
	echo "401";
}
?>