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
$nav_title = "Ability Dependencies";
include("modules/$module_name/includes/slurp_header.php");

// echo"prefix: $slrp_prefix<br>";


if(isset($_POST['current_expander']))
{
	$expander_abbr = $_POST['current_expander'];
	$expander = ($expander_abbr."_expander");
}
// echo"exp: $expander_abbr, $expander<br>";

if(isset($_POST['current_ab_id']))
{
	$current_ab_id = $_POST['current_ab_id'];
	// get info on the ability
	$get_pc_abilities2 = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$current_ab_id'");
	// $curab2 = mysql_fetch_assoc($get_pc_abilities2);
	$curab2 = mysql_fetch_assoc($get_pc_abilities2);
}


//$col1_count = 9;
//$subcol1_count = 3;
//$subcol2_count = 5;


// echo"1cc: $col1_count, 1sc: $subcol1_count, 2sc: $subcol2_count<br>";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	$wide_align = "right";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	$wide_align = "left";
}

//the row that holds messages at the top

echo"
<tr>
	<td align = 'left' valign = 'top'>
		<table width = '100%'>
";

// if the current pc owns the ability, use their key
// end message table at top, and its row.

echo"
		</table>
	</td>
</tr>
";

// start a row to hold the main content, and a cell 5/6 of the screen wide, to leave the rest as a sidebar
// also start a table in the cell; it wil be  number of columns equal to the values set by rank at the beginning

echo"
<tr>
	<td align = 'left' valign = 'top'>
";

$condensed = '1';
// $condensed = '0';
include("modules/$module_name/includes/fn_ab_dep.php");

echo"
	</td>
</tr>
<tr>
	<td width='100%' colspan='9'>
		<table>
			<tr>
				<form name = 'go_to_ab_list' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
				<td colspan = '3'>
					<input type='hidden' value='ab' name='current_expander'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='submit' value='Abilities List' name='go_to_ab_list'>
				</td>
				</form>
				
				<td width = '2%'>
				</td>
				
				<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
				<td colspan = '3'>
					<input type='hidden' value='1' name='ab_expander'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='submit' value='Back to Main' name='go_home'>
				</td>
				</form>
				
				<td width = '2%'>
				</td>
			</tr>
		</table>
	</td>
";

// end main row
echo"
</tr>
";			

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>