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

// get abilities list
echo"<tr>
	<td valign = 'top' width = '100%' align = 'center'>
		<table cellpadding = '3' width = '100%' border = '1'>";

$abcnt = 0;
$ab_print_names = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1' ORDER BY ability");
while($print_ab_id = mysql_fetch_assoc($ab_print_names))
{
	// echo "$rowcount$colcount<br>";
	// start ability pane proper
	$ab_nfo_id = $print_ab_id[ability_id];
	include("modules/$module_name/includes/fn_ab_nfo.php");
	// end ability sheet table
}
	
echo"
		</table>
	</td>
</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>