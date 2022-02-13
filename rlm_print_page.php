<?php
if (!eregi("modules.php", $PHP_SELF))
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("modules/$module_name/includes/slurp_min_header.php");


// default uses listcount 1, rowcount 0, colcount 0 and posts $rowcount$colcount. 
// Adjust the while(listcount <=X) at top and the if(colcount = x) at the bottom to determine the rowXcol ration;
// For example: 
// 16 and 4 would make a new row after 4 cells and would stop altogether after 16.
// 10 and 2 would make a new row after 2 cells and would stop altogether after 10.
// 2 and 1 would make a new row after 1 cell and would stop altogether after 2.

$listcount = 1;
$rowcount = 0;
$colcount = 0;

// get realm graphic list

while($listcount <= 9)
{
	if($colcount == 0)
	{
		echo"<tr>";
		
		$rowcount++;
	}
	
	echo"
		<td valign = 'middle' width = '33%' height = '375' align = 'center' bgcolor='#CCCCFF'>
	";
	
	$rlmcnt = 0;
	// $rlmnms = mysql_fetch_assoc($rlmnames);
	if(isset($_POST[$rowcount.$colcount]))
	{
		if(isset($_POST['verbose_'.$rowcount.$colcount]))
		{
			$verbose = 1;
		}
		if(empty($_POST['verbose_'.$rowcount.$colcount]))
		{
			$verbose = 0;
		}
		
		// echo "<font color = 'red' size = '3'>$rowcount.$colcount</font><br>";
		$print_rlm_id = $_POST[$rowcount.$colcount];
				
		if(isset($_POST['rlm_border']))
		{
			$print_rlm_border = " border = '10'";
		}
		if(empty($_POST['rlm_border']))
		{
			$print_rlm_border = $_POST['rlm_border'];
		}
		
		// $rlmnames = mysql_query("SELECT * FROM ".$slrp_prefix."realm WHERE realm_id = '$print_rlm_id'");
		// $currlm = mysql_fetch_assoc($rlmnames);

		$get_current_graphic = mysql_query("SELECT * FROM ".$slrp_prefix."graphic WHERE ".$slrp_prefix."graphic.graphic_id = '$print_rlm_id'") or die ("failed getting current realm graphic.");
		$gtcurrgraphic = mysql_fetch_assoc($get_current_graphic);

		// print the image for the realm or subtype in question:
		if($gtcurrgraphic[graphic_id] >= 2)
		{
			echo"<img src='images/$gtcurrgraphic[graphic]' width = '265'";
			if(isset($_POST['rlm_border']))
			{
				echo "$print_rlm_border";
			}
			
			echo">";
		}		
		// end graphic cell.
	}
	
	echo"
		</td>
	";
	
	$listcount++;
	$colcount++;
	
	if($colcount == 3)
	{
		echo"
		</tr>
		";
		
		$colcount = 0;
	}
}

// include("modules/$module_name/includes/slurp_footer.php");
// require("footer.php");
?>