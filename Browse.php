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

//Gets the save starting point they want
$Save_Start = addslashes(htmlspecialchars($_GET['Start']));

//Gets the save count they want
$Save_Count = addslashes(htmlspecialchars($_GET['Count']));

//Gets the save search query
$Save_Search = addslashes(htmlspecialchars($_GET['Search_Query']));

if(isset($_GET['Category'])){
	//Gets the save category
	$Save_Category = addslashes(htmlspecialchars($_GET['Category']));
}

// $myfile = fopen("OwnerTest.txt", "w") or die("Unable to open file!");
// foreach ($_REQUEST as $key => $value){
	// $FileTXT = "$key,$value\n";
	// fwrite($myfile, $FileTXT);
// }
// fclose($myfile);

if(isset($_GET['Category'])){

if($Logged == true){

	$Parse_AuthorBy = explode(':', $Save_Category);
	
	if($Save_Category == 'Favourites'){
		
		$Favourite_QueryCount = mysql_query("SELECT * FROM `Favourites` WHERE `User` = '$UserID'");
		
		while($Favourite_FetchCount = mysql_fetch_array($Favourite_QueryCount)){

			//Save MySQL Query
			$Save_QueryC = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$Favourite_FetchCount[Save_ID]' AND `Published` <= '1'");
			$Save_CountC = mysql_num_rows($Save_QueryC);
			
			if($Save_CountC == 0){
			
				mysql_query("DELETE FROM `Favourites` WHERE `Save_ID` = '$Favourite_FetchCount[Save_ID]'");
			
			}
			
		}
		
		if($Save_Start >= 20){
		
			$Favourite_Query = mysql_query("SELECT * FROM `Favourites` WHERE `User` = '$UserID' ORDER BY `ID` ASC LIMIT 20,$Save_Start") or die(mysql_error());
		
		}else{
		
			$Favourite_Query = mysql_query("SELECT * FROM `Favourites` WHERE `User` = '$UserID' ORDER BY `ID` ASC") or die(mysql_error());
		
		}
		
		$Count_FavouritedSaves = mysql_num_rows($Favourite_Query);
		
		$Count_FavouritedSaves_Slave = mysql_num_rows(mysql_query("SELECT * FROM `Favourites` WHERE `User` = '$UserID'"));
		
		$Count_Favourites_Chain = 0;

		echo "{\"Count\":$Count_FavouritedSaves_Slave,\"Saves\":[";
		
		while($Favourite_Fetch = mysql_fetch_array($Favourite_Query)){
		
			//Save MySQL Query
			$Save_Query = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$Favourite_Fetch[Save_ID]' AND `Published` <= '1'");
			
			//$Save_Count = mysql_num_rows($Save_Query);
			
			$Save_Fetch = mysql_fetch_array($Save_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Query for MySQL
			$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$Save_Fetch[ID]'");

			//Author information array
			$TotalVoteScore = mysql_num_rows($VoteUp_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Author ID from Save Information Array
			$AuthorID = $Save_Fetch['Author'];

			if( strlen($Save_Fetch['Name']) >= 24 ){
			//Greater than 24 chars

				//Short Save Name SSN Trimmer
				$ShortSaveNamePartial = substr("$Save_Fetch[Name]", 0, 21); 
				
				//Short Save Name
				$ShortSaveName = "$ShortSaveNamePartial ...";
			}else{
			//If the length is less than 24 chars

				$ShortSaveName = $Save_Fetch['Name'];

			}

			//Query for MySQL
			$Author_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$AuthorID'");

			//Author information array
			$AuthorArray = mysql_fetch_array($Author_Query);

			//Grabs Author name out of array
			$AuthorUserName = $AuthorArray['Username'];

			if($Save_Fetch['Published'] == 0){
			//If the save is not published

				$PublishedStatus = "false";
				
			}else{
			//If the save is published

				$PublishedStatus = "true";
				
			}
		
		
			echo "
			{
			\"ID\":$Save_Fetch[ID],
			\"Created\":$Save_Fetch[Date_Uploaded],
			\"Updated\":$Save_Fetch[Date_Updated],
			\"Version\":0,
			\"Score\":$TotalVoteScore,
			\"ScoreUp\":$TotalVoteScore,
			\"ScoreDown\":0,
			\"Name\":\"$Save_Fetch[Name]\",
			\"ShortName\":\"ShortSaveName\",
			\"Username\":\"$AuthorUserName\",
			\"Comments\":0,
			\"Published\":$PublishedStatus
			}
			";
			
			$Count_Favourites_Chain = $Count_Favourites_Chain + 1;
			
			if($Count_Favourites_Chain != $Count_FavouritedSaves){
			
				echo ",";
			
			}
			
			
		
		}
		
		echo "]}";
	
	}elseif($Parse_AuthorBy[0] == 'by'){
	
	
		if($Save_Search == 'sort:date'){

			if($Save_Start >= 20){

			//Save MySQL Query
				$Owner_Query = mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$UserID' AND `Published` <= '1' ORDER BY `Date_Uploaded` DESC LIMIT $Save_Start,$Save_Start") or die(mysql_error());

			}else{

				$Owner_Query = mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$UserID' AND `Published` <= '1' ORDER BY `Date_Uploaded` DESC LIMIT 20") or die(mysql_error());

			}
			
		}else{

			if($Save_Start >= 20){

			//Save MySQL Query
				$Owner_Query = mysql_query("SELECT Saves.*, COUNT(Votes.ID) AS votes FROM Saves LEFT JOIN Votes ON Saves.ID=Votes.Save_ID WHERE Saves.Published<=1 AND `Author` = '$UserID' GROUP BY Saves.ID ORDER BY votes DESC LIMIT $Save_Start,$Save_Start");
				//$Owner_Query = mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$UserID' AND `Published` <= '1' ORDER BY `Votes` DESC LIMIT $Save_Start,$Save_Start") or die(mysql_error());

			}else{
			
				$Owner_Query = mysql_query("SELECT Saves.*, COUNT(Votes.ID) AS votes FROM Saves LEFT JOIN Votes ON Saves.ID=Votes.Save_ID WHERE Saves.Published<=1 AND `Author` = '$UserID' GROUP BY Saves.ID ORDER BY votes DESC LIMIT 20");

				//$Owner_Query = mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$UserID' AND `Published` <= '1' ORDER BY `Votes` DESC LIMIT 20") or die(mysql_error());

			}

		}

		$Count_OwnerSaves = mysql_num_rows($Owner_Query);
		
		$Count_OwnerSaves_Slave = mysql_num_rows(mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$UserID' AND `Published` <= '1'"));

		$Count_Owner_Chain = 0;

		echo "{\"Count\":$Count_OwnerSaves_Slave,\"Saves\":[";

		while($Owner_Fetch = mysql_fetch_array($Owner_Query)){

			//Save MySQL Query
			$Save_Query = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$Owner_Fetch[ID]'");
			$Save_Fetch = mysql_fetch_array($Save_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Query for MySQL
			$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$Save_Fetch[ID]'");

			//Author information array
			$TotalVoteScore = mysql_num_rows($VoteUp_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Author ID from Save Information Array
			$AuthorID = $Save_Fetch['Author'];

			if( strlen($Save_Fetch['Name']) >= 24 ){
			//Greater than 24 chars

				//Short Save Name SSN Trimmer
				$ShortSaveNamePartial = substr("$Save_Fetch[Name]", 0, 21); 
				
				//Short Save Name
				$ShortSaveName = "$ShortSaveNamePartial ...";
			}else{
			//If the length is less than 24 chars

				$ShortSaveName = $Save_Fetch['Name'];

			}

			//Query for MySQL
			$Author_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$AuthorID'");

			//Author information array
			$AuthorArray = mysql_fetch_array($Author_Query);

			//Grabs Author name out of array
			$AuthorUserName = $AuthorArray['Username'];

			if($Save_Fetch['Published'] == 0){
			//If the save is not published

				$PublishedStatus = "false";
				
			}else{
			//If the save is published

				$PublishedStatus = "true";
				
			}


			echo "
			{
			\"ID\":$Save_Fetch[ID],
			\"Created\":$Save_Fetch[Date_Uploaded],
			\"Updated\":$Save_Fetch[Date_Updated],
			\"Version\":0,
			\"Score\":$TotalVoteScore,
			\"ScoreUp\":$TotalVoteScore,
			\"ScoreDown\":0,
			\"Name\":\"$Save_Fetch[Name]\",
			\"ShortName\":\"$ShortSaveName\",
			\"Username\":\"$AuthorUserName\",
			\"Comments\":0,
			\"Published\":$PublishedStatus
			}
			";
			
			$Count_Owner_Chain = $Count_Owner_Chain + 1;
			
			if($Count_Owner_Chain != $Count_OwnerSaves){
			
				echo ",";
			
			}

		}

		echo "]}";

			
	}

}


}elseif(strlen($Save_Search) <= 3){


	if( ($Save_Start == 0 && $Save_Count == 20) ){
	//Front Page Determination Page Number
	
		//Save MySQL Query for Front Page Saves.
		
		
		//Current Time minus 48 hours.
		$Last_48hr_Votes = time()-172800;
		
		//$Save_Query = mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1' AND `Status` = '1' ORDER BY `ID` ASC LIMIT 15");
		$Save_Query = mysql_query("SELECT Saves.*, COUNT(Views.ID) AS watched, COUNT(Votes.ID) AS voted FROM Saves LEFT JOIN Views ON Saves.ID=Views.Save_ID LEFT JOIN Votes ON Saves.ID=Votes.Save_ID WHERE Saves.Published=1 AND Saves.Status=0 AND Views.Type = 0 GROUP BY Saves.ID, Votes.Save_ID, Views.Save_ID ORDER BY watched DESC, voted ASC LIMIT 15");
		
		$Count_FrontpageSaves = mysql_num_rows($Save_Query);

		$CountSaves = mysql_num_rows(mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1'"));

		$LoopSave_Count = '0';

		//Count the number of saves
		echo "{\"Count\":$CountSaves,\"Saves\":[";

		//The while loop spits out the save jsons
		while($SaveInfo = mysql_fetch_array($Save_Query))
		{
		
			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Query for MySQL
			$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$SaveInfo[ID]'");

			//Author information array
			$TotalVoteScore = mysql_num_rows($VoteUp_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////
			
			//Author ID from Save Information Array
			$AuthorID = $SaveInfo['Author'];
			
			if( strlen($SaveInfo['Name']) >= 24 ){
			//Greater than 24 chars
			
				//Short Save Name SSN Trimmer
				$ShortSaveNamePartial = substr("$SaveInfo[Name]", 0, 21); 
				
				//Short Save Name
				$ShortSaveName = "$ShortSaveNamePartial ...";
			}else{
			//If the length is less than 24 chars
			
				$ShortSaveName = $SaveInfo['Name'];
			
			}
			
			//Query for MySQL
			$Author_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$AuthorID'");
			
			//Author information array
			$AuthorArray = mysql_fetch_array($Author_Query);
			
			//Grabs Author name out of array
			$AuthorUserName = $AuthorArray['Username'];
			
			if($SaveInfo['Published'] == 0){
			//If the save is not published
			
				$PublishedStatus = "false";
				
			}else{
			//If the save is published
			
				$PublishedStatus = "true";
				
			}
			
			//The actual output
			echo "{\"ID\":$SaveInfo[ID],\"Version\":0,\"Score\":$TotalVoteScore,\"ScoreUp\":$TotalVoteScore,\"ScoreDown\":0,\"Name\":\"$SaveInfo[Name]\",\"ShortName\":\"$ShortSaveName\",\"Username\":\"$AuthorUserName\",\"Comments\":42,\"Published\":true}";
			
			$LoopSave_Count++;
			
			if( $LoopSave_Count < $Count_FrontpageSaves ){
				echo ",";
			}

		//End
		}

		//Close output json array
		echo "]}";
		
	}elseif($Save_Start >= 20){
	//If the Save Start is greater than 20
	
		$Save_Start_Offset = $Save_Start - 20;
		
		$Count_Saves = mysql_num_rows(mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1'"));
		
		if(($Count_Saves % 20) == 0){
		
			$Count_Saves = mysql_num_rows(mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1'"));
		
		}else{
		
			$Count_Saves = mysql_num_rows(mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1'"))+20;
		
		}
		
		
		if($Save_Start == 20){
		
			//$Save_Query = mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1' ORDER BY `Votes` DESC LIMIT 20");
			//$Save_Query = mysql_query("SELECT Saves.*, COUNT(Votes.Save_ID) AS Vote_Ups FROM Saves, Votes WHERE Saves.Published=1 AND Saves.ID = Votes.Save_ID GROUP BY Saves.ID ORDER BY Vote_Ups DESC LIMIT 20");
			// SELECT COUNT(*) AS votes FROM `Saves` INNER JOIN Votes ON Saves.id=Votes.Save_ID GROUP BY Save_ID
			//$Save_Query = mysql_query("SELECT Saves.*, COUNT(*) AS votes FROM `Saves` INNER JOIN Votes ON Saves.ID=Votes.Save_ID GROUP BY Save_ID ORDER BY votes DESC LIMIT 20");
			
			$Save_Query = mysql_query("SELECT Saves.*, COUNT(Votes.ID) AS votes FROM Saves LEFT JOIN Votes ON Saves.ID=Votes.Save_ID WHERE Saves.Published=1 GROUP BY Saves.ID ORDER BY votes DESC LIMIT 20");
		}elseif($Save_Start > 20){
		
			//$Save_Query = mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1' ORDER BY `Votes` DESC LIMIT $Save_Start_Offset,$Save_Start_Offset");
			//$Save_Query = mysql_query("SELECT Saves.*, COUNT(*) AS votes FROM `Saves` INNER JOIN Votes ON Saves.Published='1' AND Saves.ID=Votes.Save_ID GROUP BY Save_ID ORDER BY votes DESC LIMIT $Save_Start_Offset,$Save_Start_Offset");
			$Save_Query = mysql_query("SELECT Saves.*, COUNT(Votes.ID) AS votes FROM Saves LEFT JOIN Votes ON Saves.ID=Votes.Save_ID WHERE Saves.Published=1 GROUP BY Saves.ID ORDER BY votes DESC LIMIT $Save_Start_Offset,$Save_Start_Offset");
			//$Save_Query = mysql_query("SELECT Saves.*, COUNT(*) AS Vote_Ups FROM Saves, Votes WHERE Saves.Published=1 AND Saves.ID = Votes.Save_ID GROUP BY Saves.ID ORDER BY Vote_Ups DESC LIMIT $Save_Start_Offset,$Save_Start_Offset");
		
		}
		// elseif($Save_Start > 20 && $Save_Start_Offset >= $Estimated_Pages){
		
			// $Save_Query = mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1' ORDER BY `Votes` DESC LIMIT $Save_Start_Offset,$Save_Start_Offset");
		
		// }

		
		$Count_FrontpageSaves = mysql_num_rows($Save_Query);

		$LoopSave_Count = '0';

		//Count the number of saves
		echo "{\"Count\":$Count_Saves,\"Saves\":[";

		//The while loop spits out the save jsons
		while($SaveInfo = mysql_fetch_array($Save_Query))
		{
			
			
			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Query for MySQL
			$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$SaveInfo[ID]'");

			//Author information array
			$TotalVoteScore = mysql_num_rows($VoteUp_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////
			
			//Author ID from Save Information Array
			$AuthorID = $SaveInfo['Author'];
			
			if( strlen($SaveInfo['Name']) >= 24 ){
			//Greater than 24 chars
			
				//Short Save Name SSN Trimmer
				$ShortSaveNamePartial = substr("$SaveInfo[Name]", 0, 21); 
				
				//Short Save Name
				$ShortSaveName = "$ShortSaveNamePartial ...";
			}else{
			//If the length is less than 24 chars
			
				$ShortSaveName = $SaveInfo['Name'];
			
			}
			
			//Query for MySQL
			$Author_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$AuthorID'");
			
			//Author information array
			$AuthorArray = mysql_fetch_array($Author_Query);
			
			//Grabs Author name out of array
			$AuthorUserName = $AuthorArray['Username'];
			
			if($SaveInfo['Published'] == 0){
			//If the save is not published
			
				$PublishedStatus = "false";
				
			}else{
			//If the save is published
			
				$PublishedStatus = "true";
				
			}
			
			//The actual output
			echo "{\"ID\":$SaveInfo[ID],\"Version\":0,\"Score\":$TotalVoteScore,\"ScoreUp\":$TotalVoteScore,\"ScoreDown\":0,\"Name\":\"$SaveInfo[Name]\",\"ShortName\":\"$ShortSaveName\",\"Username\":\"$AuthorUserName\",\"Comments\":42,\"Published\":true}";
			
			$LoopSave_Count++;
			
			if( $LoopSave_Count < $Count_FrontpageSaves ){
				echo ",";
			}

		//End
		}

		//Close output json array
		echo "]}";

	}else{

		echo "Error";

	}

}elseif($Save_Search == 'sort:date'){
//Sorting by date

	//Save MySQL Query
	$Save_Query = mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1' ORDER BY `Date_Updated` DESC LIMIT $Save_Count OFFSET $Save_Start");

	$Count_FrontpageSaves = mysql_num_rows($Save_Query);

	$CountSaves = mysql_num_rows(mysql_query("SELECT * FROM `Saves` WHERE `Published` = '1'"));

	$LoopSave_Count = '0';

	//Count the number of saves
	echo "{\"Count\":$CountSaves,\"Saves\":[";

	//The while loop spits out the save jsons
	while($SaveInfo = mysql_fetch_array($Save_Query))
	{
	
		//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

		//Query for MySQL
		$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$SaveInfo[ID]'");

		//Author information array
		$TotalVoteScore = mysql_num_rows($VoteUp_Query);

		//////////---STR GLOBAL VOTE CHECK SEARCH---//////////
		
		//Author ID from Save Information Array
		$AuthorID = $SaveInfo['Author'];
		
		if( strlen($SaveInfo['Name']) >= 24 ){
		//Greater than 24 chars
		
			//Short Save Name SSN Trimmer
			$ShortSaveNamePartial = substr("$SaveInfo[Name]", 0, 21); 
			
			//Short Save Name
			$ShortSaveName = "$ShortSaveNamePartial ...";
		}else{
		//If the length is less than 24 chars
		
			$ShortSaveName = $SaveInfo['Name'];
		
		}
		
		//Query for MySQL
		$Author_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$AuthorID'");
		
		//Author information array
		$AuthorArray = mysql_fetch_array($Author_Query);
		
		//Grabs Author name out of array
		$AuthorUserName = $AuthorArray['Username'];
		
		if($SaveInfo['Published'] == 0){
		//If the save is not published
		
			$PublishedStatus = "false";
			
		}else{
		//If the save is published
		
			$PublishedStatus = "true";
			
		}
		
		//The actual output
		echo "
		{\"ID\":$SaveInfo[ID],\"Version\":0,\"Score\":$TotalVoteScore,\"ScoreUp\":$TotalVoteScore,\"ScoreDown\":0,\"Name\":\"$SaveInfo[Name]\",\"ShortName\":\"$ShortSaveName\",\"Username\":\"$AuthorUserName\",\"Comments\":42,\"Published\":true}
		";
		
		$LoopSave_Count++;
		
		if( $LoopSave_Count < $Count_FrontpageSaves ){
			echo ",";
		}

	//End
	}

	//Close output json array
	echo "]}";	

}elseif(stripos($Save_Search, 'user:') !== false){
//If the user is finding a bunch of saves by a particular author

	//Parse whether they are alos sorting by date
	$Parse_Sort = explode(' ', $Save_Search);
	
	//Author side of the parse
	$Author_Side = $Parse_Sort[0];
	
	//Date side of the parse
	$Date_Side = $Parse_Sort[1];
	
	//Parse the save search query to get the author name
	$Parse_AuthorBy = explode(":", "$Author_Side");
	
	//Author's username
	$AuthorBy_Username = $Parse_AuthorBy[1];
	
	//Prepare the database query to search the author's user 
	$Prepare_Username_Check = $conn2 -> prepare("SELECT * FROM `Registered` WHERE `Username` LIKE :Username");

	//Execute the entry for finding a username similar or exactly to the client's chosen
	$Prepare_Username_Check ->execute(array(':Username' => "$AuthorBy_Username"));
	
	//Counts how many entries actually exist
	$Count_Username_Check = $Prepare_Username_Check->rowCount();
	
	//Determine if the number of users found is one
	if($Count_Username_Check != 1){
	//Usernames found does not equal one
	
		//Output error message
		echo "{\"Count\":0,\"Saves\":[]}";
		
	}else{
	//Only one username found
	
		//Turn the found row into an array
		$AuthorBy_FetchArray = $Prepare_Username_Check->fetch();
		
		//Pull out the user ID
		$AuthorBy_User_ID = $AuthorBy_FetchArray['ID'];
		
		if(strpos($Date_Side, 'sort:date') !== false){
			if($Save_Start >= 20){

			//Save MySQL Query
				$Owner_Query = mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$AuthorBy_User_ID' AND `Published` = '1' ORDER BY `Date_Uploaded` DESC LIMIT $Save_Start,$Save_Start") or die(mysql_error());

			}else{

				$Owner_Query = mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$AuthorBy_User_ID' AND `Published` = '1' ORDER BY `Date_Uploaded` DESC LIMIT 20") or die(mysql_error());

			}
		}else{
			if($Save_Start >= 20){

			//Save MySQL Query
				$Owner_Query = mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$AuthorBy_User_ID' AND `Published` = '1' ORDER BY `Votes` DESC LIMIT $Save_Start,$Save_Start") or die(mysql_error());

			}else{

				$Owner_Query = mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$AuthorBy_User_ID' AND `Published` = '1' ORDER BY `Votes` DESC LIMIT 20") or die(mysql_error());

			}
		}

		$Count_OwnerSaves = mysql_num_rows($Owner_Query);

		$Count_OwnerSaves_Slave = mysql_num_rows(mysql_query("SELECT * FROM `Saves` WHERE `Author` = '$AuthorBy_User_ID' AND `Published` = '1'"));

		$Count_Owner_Chain = 0;

		echo "{\"Count\":$Count_OwnerSaves_Slave,\"Saves\":[";

		while($Owner_Fetch = mysql_fetch_array($Owner_Query)){

			//Save MySQL Query
			$Save_Query = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$Owner_Fetch[ID]'");
			$Save_Fetch = mysql_fetch_array($Save_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Query for MySQL
			$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$Save_Fetch[ID]'");

			//Author information array
			$TotalVoteScore = mysql_num_rows($VoteUp_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Author ID from Save Information Array
			$AuthorID = $Save_Fetch['Author'];

			if( strlen($Save_Fetch['Name']) >= 24 ){
			//Greater than 24 chars

				//Short Save Name SSN Trimmer
				$ShortSaveNamePartial = substr("$Save_Fetch[Name]", 0, 21); 
				
				//Short Save Name
				$ShortSaveName = "$ShortSaveNamePartial ...";
			}else{
			//If the length is less than 24 chars

				$ShortSaveName = $Save_Fetch['Name'];

			}

			//Grabs Author name out of array
			$AuthorUserName = $AuthorBy_FetchArray['Username'];

			if($Save_Fetch['Published'] == 0){
			//If the save is not published

				$PublishedStatus = "false";
				
			}else{
			//If the save is published

				$PublishedStatus = "true";
				
			}


			echo "
			{
			\"ID\":$Save_Fetch[ID],
			\"Created\":$Save_Fetch[Date_Uploaded],
			\"Updated\":$Save_Fetch[Date_Updated],
			\"Version\":0,
			\"Score\":$TotalVoteScore,
			\"ScoreUp\":$TotalVoteScore,
			\"ScoreDown\":0,
			\"Name\":\"$Save_Fetch[Name]\",
			\"ShortName\":\"$ShortSaveName\",
			\"Username\":\"$AuthorUserName\",
			\"Comments\":0,
			\"Published\":$PublishedStatus
			}
			";
			
			$Count_Owner_Chain = $Count_Owner_Chain + 1;
			
			if($Count_Owner_Chain != $Count_OwnerSaves){
			
				echo ",";
			
			}

		}

		echo "]}";



	}

}elseif(stripos($Save_Search, 'id:') !== false){
//If the user is finding a bunch of saves for a certain ID
	
	//Parse the save search query to get the save ID
	$Parse_ID_Search = explode(":", "$Save_Search");
	
	//Save ID from parsed area
	$Save_ID_Parsed = $Parse_ID_Search[1];
	
	//Prepare the database query to search the saves by ID
	$Prepare_ID_SaveSearch = $conn2 -> prepare("SELECT * FROM `Saves` WHERE `ID` = :Save_ID AND `Published` <= :Save_Status");

	//Execute the entry for finding a save ID exactly to the client's chosen
	$Prepare_ID_SaveSearch ->execute(array(':Save_ID' => "$Save_ID_Parsed", ':Save_Status' => "1"));
	
	//Counts how many entries actually exist
	$Count_SaveID_Check = $Prepare_ID_SaveSearch->rowCount();
	
	//Determine if the number of ID found is one
	if($Count_SaveID_Check != 1){
	//Saves found does not equal one
	
		//Output error message
		echo "{\"Count\":0,\"Saves\":[]}";
		
	}else{
	//Only one ID found
	
		//Turn the found save row into an array
		$SaveID_FetchArray = $Prepare_ID_SaveSearch->fetch();

		//$Count_OwnerSaves = mysql_num_rows($Owner_Query);

		$Count_OwnerSaves_Slave = mysql_num_rows(mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$Save_ID_Parsed' AND `Published` = '1'"));

		$Count_Owner_Chain = 0;

		echo "{\"Count\":$Count_OwnerSaves_Slave,\"Saves\":[";

			//Save MySQL Query
			$Save_Query = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveID_FetchArray[ID]'");
			$Save_Fetch = mysql_fetch_array($Save_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Query for MySQL
			$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$Save_ID_Parsed'");

			//Author information array
			$TotalVoteScore = mysql_num_rows($VoteUp_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Author ID from Save Information Array
			$AuthorID = $Save_Fetch['Author'];

			if( strlen($Save_Fetch['Name']) >= 24 ){
			//Greater than 24 chars

				//Short Save Name SSN Trimmer
				$ShortSaveNamePartial = substr("$Save_Fetch[Name]", 0, 21); 
				
				//Short Save Name
				$ShortSaveName = "$ShortSaveNamePartial ...";
			}else{
			//If the length is less than 24 chars

				$ShortSaveName = $Save_Fetch['Name'];

			}

			//Grabs Author name out of array
			$AuthorUser_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$AuthorID' AND `Status` = '1'");
			
			//Fetch the data into an array
			$AuthorUser_Array = mysql_fetch_array($AuthorUser_Query);
			
			//Author's username split from the fetched array
			$Author_Username = $AuthorUser_Array['Username'];

			if($Save_Fetch['Published'] == 0){
			//If the save is not published

				$PublishedStatus = "false";
				
			}else{
			//If the save is published

				$PublishedStatus = "true";
				
			}


			echo "
			{
			\"ID\":$Save_Fetch[ID],
			\"Created\":$Save_Fetch[Date_Uploaded],
			\"Updated\":$Save_Fetch[Date_Updated],
			\"Version\":0,
			\"Score\":$TotalVoteScore,
			\"ScoreUp\":$TotalVoteScore,
			\"ScoreDown\":0,
			\"Name\":\"$Save_Fetch[Name]\",
			\"ShortName\":\"$ShortSaveName\",
			\"Username\":\"$Author_Username\",
			\"Comments\":0,
			\"Published\":$PublishedStatus
			}
			";
			
			$Count_Owner_Chain = $Count_Owner_Chain + 1;
			
			if($Count_Owner_Chain != $Count_SaveID_Check){
			
				echo ",";
			
			}


		echo "]}";



	}
}elseif(stripos($Save_Search, 'history:') !== false){
//If the user is finding a bunch of saves by a particular author

	//Parse whether they are alos sorting by date
	$Parse_Sort = explode(' ', $Save_Search);
	
	//Author side of the parse
	$History_Side = $Parse_Sort[0];
	
	//Date side of the parse
	$Date_Side = $Parse_Sort[1];
	
	//Parse the save search query to get the save ID
	$Parse_HistoryID = explode(":", "$History_Side");
	
	//Save ID
	$History_SaveID = $Parse_HistoryID[1];
	
	//Prepare the database query to search the author's user 
	$Prepare_SaveID_Check = $conn2 -> prepare("SELECT * FROM `Saves` WHERE `ID` = :SaveID");

	//Execute the entry for finding a username similar or exactly to the client's chosen
	$Prepare_SaveID_Check ->execute(array(':SaveID' => "$History_SaveID"));
	
	//Counts how many entries actually exist
	$Count_ID_Check = $Prepare_SaveID_Check->rowCount();
	
	//Determine if the number of saves found is one
	if($Count_ID_Check != 1){
	//Saves found does not equal one
	
		//Output error message
		echo "{\"Count\":0,\"Saves\":[]}";
		
	}else{
	//Only one saves found
	
		//Turn the found row into an array
		$History_FetchArray = $Prepare_SaveID_Check->fetch();
		
		//Pull out the save ID
		$History_Save_ID_Verified = $History_FetchArray['ID'];
		
		if(strpos($Date_Side, 'sort:date') !== false){
			//Sort by DATE
			
			//Check the page number
			if($Save_Start >= 20){
				//We are on page two or higher
				
				//The history of the save. Excluding current.
				$History_Query = mysql_query("SELECT * FROM `Saves__History` WHERE `Save_ID` = '$History_Save_ID_Verified' ORDER BY `Date_Updated` DESC LIMIT $Save_Start,$Save_Start") or die(mysql_error());

			}else{
				//We are on page one

				//The history of the save. Excluding current.
				$History_Query = mysql_query("SELECT * FROM `Saves__History` WHERE `Save_ID` = '$History_Save_ID_Verified' ORDER BY `Date_Updated` DESC LIMIT 20") or die(mysql_error());

			}
			
		}else{
			//Sort by VOTES
			
			//Determine our page number
			if($Save_Start >= 20){
				//We are on page two or higher

				//Save MySQL Query
				$History_Query = mysql_query("SELECT * FROM `Saves__History` WHERE `Save_ID` = '$History_Save_ID_Verified' LIMIT $Save_Start,$Save_Start") or die(mysql_error());

			}else{

				$History_Query = mysql_query("SELECT * FROM `Saves__History` WHERE `Save_ID` = '$History_Save_ID_Verified' LIMIT 20") or die(mysql_error());

			}
		}

		$Count_SnapshotSaves = mysql_num_rows($History_Query);

		$Count_SnapshotSaves_Slave = mysql_num_rows(mysql_query("SELECT * FROM `Saves__History` WHERE `Save_ID` = '$History_Save_ID_Verified'"));

		$Count_Snapshots_Chain = 0;

		echo "{\"Count\":$Count_SnapshotSaves_Slave,\"Saves\":[";

		while($History_Fetch = mysql_fetch_array($History_Query)){

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Query for MySQL
			$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$History_Save_ID_Verified'");

			//Author information array
			$TotalVoteScore = mysql_num_rows($VoteUp_Query);

			//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

			//Author ID from Save Information Array
			$AuthorID = $History_FetchArray['Author'];

			if( strlen($History_Fetch['Name']) >= 24 ){
			//Greater than 24 chars

				//Short Save Name SSN Trimmer
				$ShortSaveNamePartial = substr("$History_Fetch[Name]", 0, 21); 
				
				//Short Save Name
				$ShortSaveName = "$ShortSaveNamePartial ...";
			}else{
			//If the length is less than 24 chars

				$ShortSaveName = $History_Fetch['Name'];

			}

			//Prepared query for the registered username search via ID
			$Prepare_Author_Fetch = $conn2 -> prepare("SELECT * FROM `Registered` WHERE `ID` LIKE :AuthorID");

			//Execute and assign the :AuthorID preparation
			$Prepare_Author_Fetch ->execute(array(':AuthorID' => "$AuthorID"));
			
			//Fetches the array for the user
			$Author_UsernameFetch = $Prepare_Author_Fetch->fetch();
			
			//Grabs Author name out of array
			$AuthorUserName = $Author_UsernameFetc['Username'];

			if($Save_Fetch['Published'] == 0){
			//If the save is not published

				$PublishedStatus = "false";
				
			}else{
			//If the save is published

				$PublishedStatus = "true";
				
			}
			
			if($History_Fetch['Date_Updated'] == $History_FetchArray['Date_Uploaded']){
				
				$Version_Number = 0;
				
			}else{
				
				$Version_Number = $History_Fetch['Date_Updated'];
				
			}


			echo "
			{
			\"ID\":$History_Save_ID_Verified,
			\"Created\":$History_FetchArray[Date_Uploaded],
			\"Updated\":$History_Fetch[Date_Updated],
			\"Version\":$Version_Number,
			\"Score\":$TotalVoteScore,
			\"ScoreUp\":$TotalVoteScore,
			\"ScoreDown\":0,
			\"Name\":\"$History_Fetch[Name]\",
			\"ShortName\":\"$ShortSaveName\",
			\"Username\":\"$AuthorUserName\",
			\"Comments\":0,
			\"Published\":$PublishedStatus
			}
			";
			
			$Count_Snapshots_Chain = $Count_Snapshots_Chain + 1;
			
			if($Count_Snapshots_Chain != $Count_SnapshotSaves){
			
				echo ",";
			
			}

		}

		echo "]}";



	}

}elseif(strlen($Save_Search) > 3){

	$Broken_Term = explode(" ", "$Save_Search");

	$Main_Search_Array = array();

	$Counter_Chain = "0";

	foreach($Broken_Term as $Search_Term){
		
		if(strlen($Search_Term) > 3){
		
			//Search the table via query. Using REGEXP to find the term between special characters and such.
			//INSTR(`column`, '{$needle}') > 0
			$Search_Table_Query = mysql_query("SELECT * FROM `Saves` WHERE `Name` LIKE '%$Search_Term%' AND `Published` = '1' ORDER BY `ID` ASC LIMIT 20");
			
			//Searched results are placed into an array.
			//$Search_Table_RR = mysql_fetch_array($Search_Table_Query);
			//$Temp_Search_Array = mysql_fetch_assoc($Search_Table_Query);
			
			while($Temp_Search_Array = mysql_fetch_assoc($Search_Table_Query)){
			
			$Main_Search_Array = array_merge_recursive($Main_Search_Array, $Temp_Search_Array);
			//$Main_Search_Array = array_unique($Main_Search_Array, SORT_REGULAR);
			
			}
		}
		
	}

	//$Main_Search_Array = array_unique($Main_Search_Array, SORT_STRING);

	//$Test_Array = var_export($Main_Search_Array['ID'], true);

	//echo $Test_Array;

	$Save_Count_Total = sizeof($Main_Search_Array['ID']);

	//echo $Main_Search_Array['ID'];

	echo "{\"Count\":$Save_Count_Total,\"Saves\":[";

	if($Save_Count_Total > 1){
		foreach($Main_Search_Array['ID'] as $Search_ID){

		$SaveInfo_ID = $Search_ID;

		$Search_Specific_Query = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveInfo_ID' AND `Published` = '1'");

		$Search_Specific_Check = mysql_num_rows($Search_Specific_Query);

		if($Search_Specific_Check == 1){


		$Search_Specific_Fetch = mysql_fetch_array($Search_Specific_Query);

		$SaveInfo_Author = $Search_Specific_Fetch['Author'];
		$SaveInfo_Name = $Search_Specific_Fetch['Name'];

		//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

		//Query for MySQL
		$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$SaveInfo_ID'");

		//Author information array
		$TotalVoteScore = mysql_num_rows($VoteUp_Query);

		//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

		//Author ID from Save Information Array
		$AuthorID = $SaveInfo_Author;

		if( strlen($SaveInfo_Name) >= 24 ){
		//Greater than 24 chars

			//Short Save Name SSN Trimmer
			$ShortSaveNamePartial = substr("$SaveInfo[Name]", 0, 21); 
			
			//Short Save Name
			$ShortSaveName = "$ShortSaveNamePartial ...";
		}else{
		//If the length is less than 24 chars

			$ShortSaveName = $SaveInfo_Name;

		}

		//Query for MySQL
		$Author_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$AuthorID'");

		//Author information array
		$AuthorArray = mysql_fetch_array($Author_Query);

		//Grabs Author name out of array
		$AuthorUserName = $AuthorArray['Username'];

		if($SaveInfo['Published'] == 0){
		//If the save is not published

			$PublishedStatus = "false";
			
		}else{
		//If the save is published

			$PublishedStatus = "true";
			
		}

		//The actual output
		echo "{\"ID\":$SaveInfo_ID,\"Version\":0,\"Score\":$TotalVoteScore,\"ScoreUp\":$TotalVoteScore,\"ScoreDown\":0,\"Name\":\"$SaveInfo_Name\",\"ShortName\":\"$ShortSaveName\",\"Username\":\"$AuthorUserName\",\"Comments\":42,\"Published\":true}";

		$Counter_Chain++;

		//Checks if a comma is needed
		if($Save_Count_Total != $Counter_Chain){

			echo ",";
			

		}

		}
		}
	}else{

		$SaveInfo_ID = $Main_Search_Array['ID'];

		$Search_Specific_Query = mysql_query("SELECT * FROM `Saves` WHERE `ID` = '$SaveInfo_ID' AND `Published` = '1'");

		$Search_Specific_Check = mysql_num_rows($Search_Specific_Query);

		if($Search_Specific_Check == 1){


		$Search_Specific_Fetch = mysql_fetch_array($Search_Specific_Query);

		$SaveInfo_Author = $Search_Specific_Fetch['Author'];
		$SaveInfo_Name = $Search_Specific_Fetch['Name'];

		//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

		//Query for MySQL
		$VoteUp_Query = mysql_query("SELECT * FROM `Votes` WHERE `Save_ID` = '$SaveInfo_ID'");

		//Author information array
		$TotalVoteScore = mysql_num_rows($VoteUp_Query);

		//////////---STR GLOBAL VOTE CHECK SEARCH---//////////

		//Author ID from Save Information Array
		$AuthorID = $SaveInfo_Author;

		if( strlen($SaveInfo_Name) >= 24 ){
		//Greater than 24 chars

			//Short Save Name SSN Trimmer
			$ShortSaveNamePartial = substr("$SaveInfo[Name]", 0, 21); 
			
			//Short Save Name
			$ShortSaveName = "$ShortSaveNamePartial ...";
		}else{
		//If the length is less than 24 chars

			$ShortSaveName = $SaveInfo_Name;

		}

		//Query for MySQL
		$Author_Query = mysql_query("SELECT * FROM `Registered` WHERE `ID` = '$AuthorID'");

		//Author information array
		$AuthorArray = mysql_fetch_array($Author_Query);

		//Grabs Author name out of array
		$AuthorUserName = $AuthorArray['Username'];

		if($SaveInfo['Published'] == 0){
		//If the save is not published

			$PublishedStatus = "false";
			
		}else{
		//If the save is published

			$PublishedStatus = "true";
			
		}

		//The actual output
		echo "{\"ID\":$SaveInfo_ID,\"Version\":0,\"Score\":$TotalVoteScore,\"ScoreUp\":$TotalVoteScore,\"ScoreDown\":0,\"Name\":\"$SaveInfo_Name\",\"ShortName\":\"$ShortSaveName\",\"Username\":\"$AuthorUserName\",\"Comments\":42,\"Published\":true}";


		}
	}
	echo "]}";

}else{

echo "{\"Count\":0,\"Saves\":[]}";

}
?>