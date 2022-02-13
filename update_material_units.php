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
$nav_title = "UPDATE MTL Units";
include("modules/$module_name/includes/slurp_header.php");

// clean out the material_harvest table
// $delete_old_harvest = mysql_query("DELETE FROM ".$slrp_prefix."material_harvest") or die ("failed deleting mat hrv table.");

// get all the regions associated with single location markets (i.e, harvtesing feeds that market)
$material = mysql_query("SELECT * FROM dom_item WHERE item LIKE '%Potion%'") or die ("failed getting existing mtl.");
$mtlsbtypcnt = mysql_num_rows($material);
while($mtlsbtyp = mysql_fetch_assoc($material))
{
	echo"<br>$mtlsbtyp[item]";
	//$get_unit_subs = mysql_query("SELECT * FROM dom_effect_subtype INNER JOIN dom_effect_subtype_type ON dom_effect_subtype_type.effect_subtype_id = dom_effect_subtype.effect_subtype_id WHERE dom_effect_subtype_type.effect_type_id = '16'") or die ("fauled getting effects.");
	//while($gtuntsbs = mysql_fetch_assoc($get_unit_subs))
	//{
		 $check_existing_entries = mysql_query("UPDATE dom_item SET item_default_unit_id = '635' WHERE item_id = '$mtlsbtyp[item_id]'") or die ("failed updating existing entries.");
		//$chkexentrycnt = mysql_num_rows($check_existing_entries);
		//echo"$chkexentrycnt, ";
		//if($chkexentrycnt == 0)
		//{
			// $make_new_entry = mysql_query("INSERT INTO dom_material_subtype_unit_type (material_subtype_id,unit_type_id,uses_unit_type) VALUES ($mtlsbtyp[material_subtype_id],$gtuntsbs[effect_subtype_id],'0')") or die ("failed inserting new entries.");
		//}
	//}
}

?> 