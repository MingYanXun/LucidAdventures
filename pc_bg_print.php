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
$nav_title = "Print Background";
include("modules/$module_name/includes/slurp_header.php");

echo"
<form name = 'pc_bg_print_page' method='post' action = 'modules.php?name=$module_name&file=pc_bg_print_page'>
<tr>
";
	
echo"
<td width = '23%' align = 'left' valign = 'top'>
";

// get pc table info
$get_pc_bg_list = mysql_query("SELECT * FROM dom_creature WHERE creature_id > '1' AND creature_status_id >= '2' AND creature_status_id <= '4' ORDER BY creature_status_id DESC, creature");
// setting up to divide the list by four for columns
$curpcbgcnt = mysql_num_rows($get_pc_bg_list);
$colcount = ($curpcbgcnt/4);
// echo" col capacity: $colcount<br>";
$bg_count = 0;
while($curpcbg = mysql_fetch_assoc($get_pc_bg_list))
{
	$bg_count++;
	// color code by status
	if($curpcbg[creature_status_id] == 2)
	{
		echo"<font color = 'orange'>";
	}
	
	if($curpcbg[creature_status_id] == 3)
	{
		echo"<font color = 'yellow'>";
	}
	
	if($curpcbg[creature_status_id] == 4)
	{
		echo"<font color = 'green'>";
	}
	
	// print checkboxes with names
	echo"
		<input type='checkbox' value='$curpcbg[creature_id]' name='pc_bg_$curpcbg[creature_id]'> $curpcbg[creature]</font><br>
	";
	
	// manage the count to make four columns and reset the counter
	if($bg_count >= $colcount)
	{
		echo"
		</td>
		<td width = '2%'>
		</td>
		<td width = '23%' align = 'left' valign = 'top'>
		";
		$bg_count = 0;
	}
}

echo"
</td>
</tr>";

echo"
<tr>

<td colspan = '5' align = 'left' valign = 'top'>
<hr width = '100%'>
<input class='submit3' type='submit' value='Print Backgrounds' name='pc_bg_print_page'>
</form>
</td>

<tr>
";			

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>