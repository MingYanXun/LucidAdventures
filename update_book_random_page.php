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
$nav_title = "Random Codes";
include("modules/$module_name/includes/slurp_header.php");

// from the contents of update_efftyp_random.php
$ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_focus_id = '2'");
$abrndcnt = mysql_num_rows($ability_random);
echo"$abrndcnt<br>";
while($abrnd = mysql_fetch_assoc($ability_random))
{
	$book_ability_random = mysql_query("UPDATE ".$slrp_prefix."item_book SET object_random_id = '$abrnd[object_random_id]' WHERE ability_id = '$abrnd[object_id]'");
	echo"<tr><td>$abrnd[object_id], $abrnd[object_random_id]</td></tr>";
}

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>