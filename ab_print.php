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
$nav_title = "Print Abilities";
include("modules/$module_name/includes/slurp_header.php");

echo"
<form name = 'ab_print_page' method='post' action = 'modules.php?name=$module_name&file=ab_print_page'>
";

// default uses listcount 1, rowcount 0, colcount 0 and posts $rowcount$colcount. 
// Adjust the while(listcount =X) at top and the if(colcount = x) at the bottom to determine the rowXcol ration;
// For example: 
// 4 and 4 would make a new row after 4 cells and would stop altogether after 16.
// 2 and 5 would make a new row after 2 cells and would stop altogether after 10.
// 1 and 2 would make a new row after 1 cell and would stop altogether after 2.

$listcount = 0;
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
	
	$inverted_row = ($rowcount-1);
	$inverted_col = ($colcount+1);
	
	// echo"old: $rowcount$colcount, new: $inverted_col$inverted_row<br>";
	
	echo"
	<td width = '32%' align = 'left' valign = 'top'>
	<select class='engine' name = '$inverted_col$inverted_row'>
	<option value = '0'>Choose Ability</option>
	";
	
	$get_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1' AND ability_status_id = '4' OR ability_status_id = '5' ORDER BY ability");
	while($curab = mysql_fetch_assoc($get_abilities))
	{	
		echo"<option value = '$curab[ability_id]'>";
		if($curab[ability_status_id] == 5)
		{
			echo"{R} ";
		}
		
		echo"$curab[ability]</option>";
	}
	
	echo"
	</select>
	</td>
	";
	$listcount++;
	$colcount++;
	
	if($colcount == 1)
	{
		echo"
		</tr>
		";
		
		$colcount = 0;
	}
}

echo"
<tr>
<td colspan = '5' align = 'left' valign = 'middle'>
<br>
</td>
<tr>

<tr background='themes/RedShores/images/base1.gif' height='24'>
<td colspan = '5' align = 'left' valign = 'middle'>
<input class='submit3' type='submit' value='Print Abilities' name='ab_print_page'>
</td>
<tr>
</form>
";			

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>