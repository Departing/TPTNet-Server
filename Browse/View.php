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

$SaveID = addslashes(htmlspecialchars($_GET['ID']));

//Save Viewing MySQL Query
$View_Query = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID' AND `Published` <= '1'");

//Save existance check
$View_Count_Save = mysql_num_rows($View_Query);

if($View_Count_Save != 1){

	//Save Viewing MySQL Query
	$View_Query = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '11'");

	//Save Viewing Fetch Array
	$View_Array = mysql_fetch_array($View_Query);

}else{

	//Save Viewing Fetch Array
	$View_Array = mysql_fetch_array($View_Query);

}

$SaveID = $View_Array['ID'];


//////////---STR REGISTERED USERNAME SEARCH---//////////

	//Query for MySQL
	$Author_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$View_Array[Author]'");

	//Author information array
	$AuthorArray = mysql_fetch_array($Author_Query);

	//Grabs Author name out of array
	$AuthorUserName = $AuthorArray['Username'];

//////////---END REGISTERED USERNAME SEARCH---//////////

//////////---LOG USER'S VIEW---//////////

if($Logged && $View_Array['Author'] != $UserID){
//If they are logged in, then we shall count their view


	//Date & Time of the view
	$DateTime = time();

	//IP Address of user
	$IP_Address = inet_pton($_SERVER['REMOTE_ADDR']);

	//Query for MySQL
	$ViewUp_Query = mysql_query("SELECT * FROM `Views` WHERE `Save_ID` = '$SaveID' AND `Type` = '0'");

	//Author information array
	$TotalViewScore = mysql_num_rows($ViewUp_Query);
	
	//Query for MySQL
	$ViewUp_Query_Check_History = mysql_query("SELECT * FROM `Views` WHERE `Save_ID` = '$SaveID' AND `User` = '$UserID' AND `Type` = '0'");

	//Author information array
	$ViewUp_Count_Check_History = mysql_num_rows($ViewUp_Query_Check_History);
	
	if($ViewUp_Count_Check_History < 1){
	//If the number is less than zero
	
		//Insert new view into views table
		mysql_query("INSERT INTO `Views` (`Save_ID`, `User`, `Date`, `IP_Address`) VALUES('$SaveID', '$UserID', '$DateTime', '$IP_Address')");

		$TotalViewScore++;

		mysql_query("UPDATE `Saves` SET `Views` = '$TotalViewScore' WHERE `ID` = '$SaveID'");
	}else{
	//If they've viewed it before
	
		//Add the view to the table as background
		mysql_query("INSERT INTO `Views` (`Save_ID`, `User`, `Date`, `IP_Address`,`Type`) VALUES('$SaveID', '$UserID', '$DateTime', '$IP_Address','1')");
	
	
	}

}


//////////---END USER VIEW LOG---//////////

//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

	//Query for MySQL
	$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$SaveID'");

	//Author information array
	$TotalVoteScore = mysql_num_rows($VoteUp_Query);

//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

//////////---STR GLOBAL COMMENTS COUNT SEARCH---//////////

	//Query for MySQL
	$CommentsCount_Query = mysql_query("SELECT * FROM `Comments` WHERE `Save_ID` = '$SaveID'");

	//Author information array
	$CommentsCountScore = mysql_num_rows($CommentsCount_Query);

//////////---END GLOBAL COMMENTS COUNT SEARCH----//////////

//////////---STR VOTED HISTORY CHECK SEARCH---//////////

	//Query for MySQL
	$Vote_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$SaveID' AND `User` = '$View_Array[Author]'");

	//Author information array
	$VoteScore = mysql_num_rows($Vote_Query);
	
	if($Logged){
		if($View_Array['Author'] == $UserID){
		
			$VoteScore = 1;
		
		}else{
		
		//Query for MySQL
		$Vote_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$SaveID' AND `User` = '$UserID'");

		//Author information array
		$VoteScore = mysql_num_rows($Vote_Query);
		
		}
	}
		
//////////---END VOTED HISTORY CHECK SEARCH---//////////


//////////--START UPLOAD/UPDATED DATE TIMESTAMPS---/////
$DateTime = $View_Array['Date_Uploaded'];

$Date_Updated = $View_Array['Date_Updated'];
//////////---START UPLOAD/UPDATED DATE TIMESTAMPS---//////////


//////////---START SHORT NAME PORTION---//////////
if( strlen($View_Array['Name']) >= 24 ){
//Greater than 24 chars

	//Short Save Name SSN Trimmer
	$ShortSaveNamePartial = substr("$View_Array[Name]", 0, 21); 
	
	//Short Save Name
	$ShortSaveName = "$ShortSaveNamePartial ...";
}else{
//If the length is less than 24 chars

	$ShortSaveName = $View_Array['Name'];

}
//////////---END SHORT NAME PORTION---//////////



if($View_Array['Published'] == 1){
//If the save is published, return true

	$Published = "true";
	
}else{
//If the save is not published, return false

	$Published = "false";
	
}

if($Logged){
	
	//Gather information
	$QueryFavourite_Check = mysql_query("SELECT * FROM `Favourites` WHERE `Save_ID` = '$SaveID' AND `User` = '$UserID'") or die(mysql_error()); 
	//Number of Rows
	$CheckFavourite_Number = mysql_num_rows($QueryFavourite_Check);
	
}else{
	
	$CheckFavourite_Number = 0;
	
}

if($CheckFavourite_Number == 1){

	$Favourited = "true";

}else{

	$Favourited = "false";

}

echo "

	{\"ID\":$View_Array[ID],\"Favourite\":$Favourited,\"Score\":$TotalVoteScore,\"ScoreUp\":$TotalVoteScore,\"ScoreDown\":0,\"Views\":$View_Array[Views],\"ShortName\":\"$ShortSaveName\",\"Name\":\"$View_Array[Name]\",\"Description\":\"$View_Array[Description]\",\"DateCreated\":$DateTime,\"Date\":$Date_Updated,\"Username\":\"$AuthorUserName\",\"Comments\":$CommentsCountScore,\"Published\":$Published,\"Version\":0,
	";
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

	echo "]";
	echo ",\"ScoreMine\":$VoteScore}
";

?>