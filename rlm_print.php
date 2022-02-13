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
$nav_title = "Print Realm Card Backs";
include("modules/$module_name/includes/slurp_header.php");

echo"
<form name = 'rlm_print_page' method='post' action = 'modules.php?name=$module_name&file=rlm_print_page'>
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
// ADJUST THIS TO CHNAGE TOTAL ENTRIES
while($listcount <= 9)
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
	
	echo"old: $rowcount$colcount, new: $inverted_col$inverted_row<br>";
	
	echo"
	<td width = '32%' align = 'left' valign = 'top'>
	<select class='engine' name = '$inverted_col$inverted_row'>
	<option value = '0'>Choose Realm</option>
	";
	
	$get_realm_subtypes = mysql_query("SELECT * FROM ".$slrp_prefix."realm_subtype WHERE realm_subtype_id > '1' ORDER BY realm_subtype") or die ("Failed getting realm subtypes.");
	while($currlmsbtp = mysql_fetch_assoc($get_realm_subtypes))
	{	
		echo"
		<option value = '$currlmsbtp[realm_subtype_graphic_id]'>$currlmsbtp[realm_subtype]</option>
		";
	}
	
	$get_realms = mysql_query("SELECT * FROM ".$slrp_prefix."realm WHERE realm_id > '1' ORDER BY realm") or die ("Failed getting realms");
	while($currlm = mysql_fetch_assoc($get_realms))
	{	
		echo"
		<option value = '$currlm[realm_graphic_id]'>$currlm[realm]</option>
		";
	}
	
		
	$get_graphics = mysql_query("SELECT * FROM ".$slrp_prefix."graphic WHERE graphic_id > '1' ORDER BY graphic") or die ("Failed getting graphics.");
	while($grphclst = mysql_fetch_assoc($get_graphics))
	{	
		echo"
		<option value = '$grphclst[graphic_id]'>$grphclst[graphic]</option>
		";
	}
	
	echo"
	</select>
	</td>
	";
	$listcount++;
	$colcount++;
	
	// ADJUST THIS TO CHANGE TOTAL COLUMNS
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
Border? <input type='checkbox' value='1' name='rlm_border'>
<br>
<br>
<input type='submit' value='Print Realms' name='rlm_print_page'>
</form>
</td>

<tr>
";			

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>