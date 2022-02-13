<?php
if (!eregi("modules.php", $PHP_SELF))
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

require("header.php");
$nav_title = "Print Characters";
include("modules/$module_name/includes/slurp_header.php");

echo"
<form name = 'pc_print_page' method='post' action = 'modules.php?name=$module_name&file=pc_inventory_print_page'>
";

// default uses listcount 1, rowcount 0, colcount 0 and posts $rowcount$colcount. 
// Adjust the while(listcount <=X) at top and the if(colcount = x) at the bottom to determine the rowXcol ration;
// For example: 
// 16 and 4 would make a new row after 4 cells and would stop altogether after 16.
// 10 and 2 would make a new row after 2 cells and would stop altogether after 10.
// 2 and 1 would make a new row after 1 cell and would stop altogether after 2.

$listcount = 1;
$rowcount = 0;
$colcount = 0;

// get abilities list

while($listcount <= 3)
{
	if($colcount == 0)
	{
		echo"
		<tr>
		";
		
		$rowcount++;
	}
	
	echo"
	<td width = '32%' align = 'left' valign = 'top'>
	$rowcount$colcount
	<select class='engine' name = '$rowcount$colcount'>
	<option value = '1'>Choose Character</option>
	";
	
	$get_characters = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id > '1' AND creature_status_id = '4' OR creature_status_id = '0' ORDER BY creature");
	while($gtchr = mysql_fetch_assoc($get_characters))
	{	
		echo"
		<option value = '$gtchr[creature_id]'>$gtchr[creature]</option>
		";
	}
	
	echo"
	</select>
	<br>
	<br>
	<br>
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

echo"
<tr>

<td colspan = '5' align = 'left' valign = 'top'>
<hr width = '100%'>
<input type='submit' value='Print Invntories' name='pc_inventory_print_page'>
</form>
</td>

<tr>
";			

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>