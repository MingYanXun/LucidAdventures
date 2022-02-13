<?php
if (!eregi("modules.php", $PHP_SELF)) 
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("header.php");
//$_POST['current_pc_id'] = 1;
$nav_title = "My Vanguard Main";
$nav_page = "index";

include("modules/$module_name/includes/slurp_header.php");

echo"
<tr>
<td colspan = '9' align = 'left' valign = 'top'>
<font color = 'yellow'>
";

$ability_modifier_tables = mysql_query("SHOW TABLES LIKE '%ability_modifier%'") or die ("failed getting ability modifier tables.");
while($abmodtables = mysql_fetch_array($ability_modifier_tables, MYSQL_NUM))
{
	$abmod_table_temp = $abmodtables[0];
	echo"<br>[$abmod_table_temp]<br>";
	$abmod_table_columns = mysql_query("SHOW COLUMNS FROM ".$abmod_table_temp." LIKE 'ability_modifier_id'") or die ("failed getting $abmod_table_temp columns.");
	$abmdtblcolscnt = mysql_num_rows($abmod_table_columns);
	
	if($abmdtblcolscnt >= 1)
	{
		$check_object_ability_modifier_subfocus = mysql_query("SELECT ability_modifier_id FROM ".$abmod_table_temp." WHERE ability_modifier_id > '1' AND ability_modifier_id NOT IN (SELECT ability_modifier_id FROM ".$slrp_prefix."ability_modifier)") or die ("failed deleting abmod subfocus relations to $abmod_table_temp.");
		while($chkobjabmodsb = mysql_fetch_assoc($check_object_ability_modifier_subfocus))
		{
			echo"To be Deleted: (# $chkobjabmodsb[ability_modifier_id])";
		  $delete_object_ability_modifier_subfocus = mysql_query("DELETE FROM ".$abmod_table_temp." WHERE ability_modifier_id = '$chkobjabmodsb[ability_modifier_id]'") or die ("failed deleting abmod subfocus relations to $abmod_table_temp.");
		}	
	}
}

// start by going through foci
$get_all_foci = mysql_query("SELECT * FROM van_focus WHERE focus_id > '1'") or die ("failed getting all foci for purge.");
while($gtallfoci = mysql_fetch_assoc($get_all_foci))
{
	// with each one (creatre, culture, item, etc.) make sure the modifier's target subfocus exists
	$get_focus_details = mysql_query("SELECT * FROM van_ability_modifier_subfocus WHERE focus_id = '$gtallfoci[focus_id]'") or die ("failed getting focus details for orphan purge.");
	while($gtfcdetls = mysql_fetch_assoc($get_focus_details))
	{
		$get_modifier_details = mysql_query("SELECT * FROM van_ability_modifier WHERE ability_modifier_id = '$gtfcdetls[ability_modifier_id]'") or die ("failed getting modifier details for orphan purge 659.");
		$gtmoddetls = mysql_fetch_assoc($get_modifier_details);
		$get_subfocus_for_purge = mysql_query("SELECT * FROM van_".$gtallfoci[focus_table]." WHERE ".$gtallfoci[focus_table]."_id = '$gtfcdetls[subfocus_id]'") or ie ("failed getting subfocus info for purge");
		$gtsbfc4purge = mysql_fetch_assoc($get_subfocus_for_purge);
		$gtsbfc4purgecnt = mysql_num_rows($get_subfocus_for_purge);
		// if it does not exist, remove the modifier and its subfocus entry
		if($gtsbfc4purgecnt == 0)
		{
			// DELETE THE ORPHANS
			echo"$gtallfoci[focus_table] $gtfcdetls[subfocus_id] from modifier $gtmoddetls[ability_modifier_short].<br>";
			 $delete_orphan_mods_subfocus = mysql_query("DELETE FROM van_ability_modifier_subfocus WHERE ability_modifier_subfocus_id = '$gtfcdetls[ability_modifier_subfocus_id]'") or die ("failed purging orphan mod sbfc.");
			 $delete_orphan_mods = mysql_query("DELETE FROM van_ability_modifier WHERE ability_modifier_id = '$gtfcdetls[ability_modifier_id]'") or die ("failed purging orphan mod.");	 
		}
		// if existing, give the option to display
		if($gtsbfc4purgecnt == 1)
		{
			// DISPLAY THE MOD NAME
			// echo"OK: $gtmoddetls[ability_modifier_short]<br>";
		}
	}
}

$allabilitiesagain = mysql_query("SELECT * FROM van_ability WHERE ability_id > '1' AND ability LIKE '%Journeyman%'") or die ("failed getting crafts");
while($allabsagain = mysql_fetch_assoc($allabilitiesagain))
{
	$quick_craft_insert_check = mysql_query("SELECT * from van_ability_effect WHERE ability_id = '$allabsagain[ability_id]' AND effect_modifier_id = '14554'") or die ("failed getting craft titles");
	$quikcrinschkcnt = mysql_num_rows($quick_craft_insert_check);
	if($quikcrinschkcnt == 0)
	{
		echo"new self for $allabsagain[ability]! YAY!<br>";
		$new_self = mysql_query("INSERT INTO van_ability_effect (ability_id, effect_id, effect_type_id, effect_modifier_id, focus_id, slurp_id) VALUES ('$allabsagain[ability_id]','440','15','14554','1','1')") or die("failed making new self");
	}
	if($quikcrinschkcnt == 1)
	{
		echo"old self for $allabsagain[ability] ALSO YAY!<br>";
	}
	if($quikcrinschkcnt >= 2)
	{
		echo"two selves for $allabsagain[ability] <b>NOT YAY!</b><br>";
	}
}

	




require("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>