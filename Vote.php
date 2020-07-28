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

if($Logged){

	//Save ID being voted on
	$SaveID = addslashes(htmlspecialchars($_POST['ID']));
	
	//Vote direction up/down
	$VoteType = addslashes(htmlspecialchars($_POST['Action']));
	
	//Gather information
	$QuerySave_Check = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID'") or die(mysql_error()); 
	//Number of Rows
	$CheckSave_Number = mysql_num_rows($QuerySave_Check);
	
	if($CheckSave_Number == 1){
	
		$Fetch_Save_Array = mysql_fetch_array($QuerySave_Check);
		
		if($Fetch_Save_Array['Author'] == $UserID){
		
			echo "You cannot vote for yourself!";
		
		}else{
	
			//Gather information
			$QueryVote_Check = mysql_query("SELECT * FROM `Votes` WHERE `User` = '$UserID' AND `Save_ID` = '$SaveID'") or die(mysql_error()); 
			//Number of Rows
			$CheckVote_Number = mysql_num_rows($QueryVote_Check);
			
			
			if($CheckVote_Number == 0){
				if($VoteType == 'Up'){
				
					$DateTime = time();
					$IP_Address = inet_pton($_SERVER['REMOTE_ADDR']);
					
					mysql_query("INSERT INTO `Votes` (`Save_ID`, `User`, `Date`, `IP_Address`) VALUES('$SaveID', '$UserID', '$DateTime', '$IP_Address')");
					
					//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

					//Query for MySQL
					$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$SaveID'");

					//Author information array
					$TotalVoteScore = mysql_num_rows($VoteUp_Query);
					
					$TotalVoteScore++;
					
					mysql_query("UPDATE `Saves` SET `Votes` = '$TotalVoteScore' WHERE `ID` = '$SaveID'");

					//////////---STR GLOBAL VOTE CHECK SEARCH---//////////
					
					
					
					
					echo "OK";
				
				}elseif($VoteType == 'Down'){
				
					echo "OK";
				
				}else{

					echo "Invalid vote option";
				
				}
			}else{
			
				echo "You have already voted!";
			
			}
		
		}
	
	}else{
		echo "Save no longer exists!";
	}
	
}else{

	echo "Auth error";

}