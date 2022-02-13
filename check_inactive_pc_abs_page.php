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
$nav_title = "Check for Inactive Abilities on PCs and Items";
include("modules/$module_name/includes/slurp_header.php");

// get all active items
$items = mysql_query("SELECT * FROM dom_item WHERE item_id > '1' AND item_status_id = '4'") or die ("failed getting existing items.");
$itmscnt = mysql_num_rows($items);
while($itms = mysql_fetch_assoc($items))
{
	$itm = $itms[item_id];
	$get_item_inactive_abilities = mysql_query("SELECT * FROM dom_ability INNER JOIN dom_item_ability ON dom_item_ability.ability_id = dom_ability.ability_id WHERE dom_item_ability.item_id = '$itms[item_id]' AND dom_ability.ability_status_id = '6'") or die ("failed getting inactive item_abilities.");
	$gtitminactabscnt = mysql_num_rows($get_item_inactive_abilities);
	echo"<bR># Items with inactive abs: $gtitminactabscnt<br>";
	if($gtitminactabscnt >= 1)
	{
		echo"<br><a href='modules.php?name=My_Dominion&file=obj_edit&expander_abbr=itm&current_focus_id=10&item=$itm'>$itms[item]:</a>";
		while($gtitminactabs = mysql_fetch_assoc($get_item_inactive_abilities))
		{
			$itminactabs = $gtitminactabs[ability_id];
		
			echo"<br>~ <a href='modules.php?name=My_Dominion&file=ab_edit&expander_abbr=ab&current_focus_id=2&current_ab_id=$itminactabs'>$gtitminactabs[ability]</a>
			";
		}
	}
}

// get all active items
$charcters = mysql_query("SELECT * FROM dom_creature WHERE creature_id > '1' AND creature_status_id = '4'") or die ("failed getting existing creatures.");
$charscnt = mysql_num_rows($charcters);
while($chars = mysql_fetch_assoc($charcters))
{
	$chr = $chars[creature_id];
	$get_creature_inactive_abilities = mysql_query("SELECT * FROM dom_ability INNER JOIN dom_creature_ability ON dom_creature_ability.ability_id = dom_ability.ability_id WHERE dom_creature_ability.creature_id = '$chr' AND dom_ability.ability_status_id = '6'") or die ("failed getting inactive cr abs.");
	$gtcrinactabscnt = mysql_num_rows($get_creature_inactive_abilities);
	echo"<bR># PCs with inactive abs: $gtcrinactabscnt<br>";
	if($gtcrinactabscnt >= 1)
	{
		echo"<br><a href='modules.php?name=My_Dominion&file=pc_edit_new&expander_abbr=cr&current_focus_id=7&current_creature_id=$chr'>$chars[creature]:</a>";
		while($gtcrinactabs = mysql_fetch_assoc($get_creature_inactive_abilities))
		{
			$crinactabs = $gtcrinactabs[ability_id];
		
			echo"<br>~ <a href='modules.php?name=My_Dominion&file=ab_edit&expander_abbr=ab&current_focus_id=2&current_ab_id=$crinactabs'>$gtcrinactabs[ability]</a>
			";
		}
	}
}


include("modules/$module_name/includes/slurp_footer.php");

?> 