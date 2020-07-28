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

//Just some very sketchy written code. Re-write ASAP
$Fetch_Tags = mysql_query("SELECT Tag, count(*) as frequency FROM Tags GROUP BY Tag ORDER BY count(*) DESC LIMIT 50");

echo "{\"TagTotal\":109735,\"Results\":50,\"Tags\":[";

$Tag_Array_Count = mysql_num_rows($Fetch_Tags);

$ini_tag_loop = 0;

while($Tag_Array_Fetched = mysql_fetch_array($Fetch_Tags)){
	
	echo "{\"Count\":$Tag_Array_Fetched[frequency],\"Tag\":\"$Tag_Array_Fetched[Tag]\",\"Restricted\":\"0\"}";
	
	$ini_tag_loop++;
	
	if($ini_tag_loop >= 50){
		
		
	}else{
		
		echo",";
	
	}
	
}

echo "]}";

?>



