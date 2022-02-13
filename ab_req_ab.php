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
$nav_title = "Characteristics List";
include("modules/$module_name/includes/slurp_header.php");

// get the abilities list for the REQUIRED BY entry
$required_all_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1'") or die ("failed getting ability list for mod reqs.");
while($rqallabs = mysql_fetch_array($required_all_abs, MYSQL_NUM))
{
	echo"<b><u>$rqallabs[1]:</u></b><br>";
	
	// get mods pointing at the required ability and modifying the edited ability
	$must_know = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE focus_id = '2' AND subfocus_id = '$rqallabs[0]' AND focus_exclusion_id = '16'") or die ("failed getting mstknw abs.");
	$mstknwcnt = mysql_num_rows($must_know);
	// this should be 1 for all abilities.
	// echo"($mstknwcnt) <br>";
	while($mstknw = mysql_fetch_array($must_know, MYSQL_NUM))
	{
		// get abilities REQUIRING other abs
		$required_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability_ability_modifier WHERE ability_modifier_id = '$mstknw[1]'") or die ("failed getting required abs.");
		$rqabscnt = mysql_num_rows($required_abs);
		while($rqabs = mysql_fetch_array($required_abs, MYSQL_NUM))
		{
			$required_ab_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$rqabs[1]'") or die ("failed getting ability list for mod reqs.");
			$rqabnfo = mysql_fetch_array($required_ab_info, MYSQL_NUM);
			echo"~ $rqabnfo[1] needs $rqallabs[1].<br>";
			// $insert_ability_requires_ability = mysql_query("INSERT INTO ".$slrp_prefix."ability_requires_ability(ability_id,requires_ability_id) VALUES ('$rqabnfo[0]','$rqallabs[0]')") or die("failed inserting ab req ab.");
			// $verify_ability_requires_ability = mysql_query("SELECT * FROM ".$slrp_prefix."ability_requires_ability WHERE ability_id = '$rqabnfo[0]' AND requires_ability_id = '$rqallabs[0]'") or die ("failed verifying ab req ab insert.");
		}
	}
}

?>