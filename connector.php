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

Connector.php

REQUIRED

The file that connects to the actual database. Kept independent for security purposes
Supports  both the older MySQL PHP and the newer MySQL Prepared PHP functions. Use of 
the newer  Prepared Statements  is recommended  considering the security and  ease of
use. Some portions of the server still use the older MySQL  PHP functions. Conversion
of said portions will slowly take place over development.

Replace  the  following   details  with  your  own  respective  database  credentials:

DB_NAME
Database name

DB_USERNAME
Database username

DB_PASSWORD
Database password

*/



//Connection details for the server database
//Legacy MySQL support
$conn1 = mysql_connect('localhost','DB_USERNAME','DB_PASSWORD'); 
mysql_select_db('DB_NAME') or die(mysql_error());

//Connection details for the server database
//Newer MySQL prepared statement support (safer)
$conn2 = new PDO('mysql:host=localhost;dbname=DB_NAME', 'DB_USERNAME', 'DB_PASSWORD');

?>