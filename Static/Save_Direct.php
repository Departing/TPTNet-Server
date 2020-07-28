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

$SaveID = addslashes(htmlspecialchars($_GET['ID']));

$Operation = addslashes(htmlspecialchars($_GET['Op']));


switch($Operation){

	case 'Save':
	
		$Parse_History_Snapshot = explode('_',"$SaveID");
		
		if(isset($Parse_History_Snapshot[1])){
			
			$file = "Published/History/$SaveID.cps";
		
		}else{
		
			$file = "Published/$SaveID.cps";
		
		}

		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}else{
			$file = '11.cps';
			
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
			
		}
		break;
		
	case 'LargePTI':
	
		$Parse_History_Snapshot = explode('_',"$SaveID");
		
		if(isset($Parse_History_Snapshot[1])){
			
			$file = "LargePTI/History/".$SaveID."_large.pti";
		
		}else{
		
			$file = "LargePTI/".$SaveID."_large.pti";
		
		}

		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}else{
			$file = 'LargePTI/11_large.pti';
			
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
			
		}
		break;
		
	case 'SmallPTI':
	
		$Parse_History_Snapshot = explode('_',"$SaveID");
		
		if(isset($Parse_History_Snapshot[1])){
			
			$file = "SmallPTI/History/".$SaveID."_small.pti";
		
		}else{
		
			$file = "SmallPTI/".$SaveID."_small.pti";
		
		}

		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}else{
			$file = 'SmallPTI/11_small.pti';
			
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
			
		}
		break;
		
	case 'LargePNG':
	
		$Parse_History_Snapshot = explode('_',"$SaveID");
		
		if(isset($Parse_History_Snapshot[1])){
			
			$file = "LargePNG/History/".$SaveID."_large.png";
		
		}else{
		
			$file = "LargePNG/".$SaveID."_large.png";
		
		}

		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}else{
			$file = 'LargePNG/11_large.png';
			
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
			
		}
		break;
		
	case 'SmallPNG':
	
		$Parse_History_Snapshot = explode('_',"$SaveID");
		
		if(isset($Parse_History_Snapshot[1])){
			
			$file = "SmallPNG/History/".$SaveID."_small.png";;
		
		}else{
		
			$file = "SmallPNG/".$SaveID."_small.png";;
		
		}

		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}else{
			$file = 'SmallPNG/11_small.png';
			
			header('Content-Description: File Transfer');
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
			
		}
		break;
		
}
?>
