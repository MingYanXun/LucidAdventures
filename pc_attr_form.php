<?php
if (!eregi("modules.php", $PHP_SELF)) {
  die ("You can't access this file directly...");
}
$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("header.php");
$nav_title = "Character Attributes";
include("modules/$module_name/includes/slurp_header.php");

$pcattrid = $_POST['pcattrid'];
$pcattrval = $_POST['pcattrval'];

if(isset($_POST['admin_attr']))
{
	$admin_attr = $_POST['admin_attr'];
	
	// echo"attr_id: $pcattrid, attr_val: $pcattrval, $admin_attr<br>";
	$attr_val_subtotal == 0;
	$upd_admin_attr = mysql_query("UPDATE ".$slrp_prefix."creature_attribute_type SET creature_attribute_type_value = '$admin_attr' WHERE creature_attribute_type_id = '$pcattrid'") or die("update admin attributes failed.");
	$upd_adm_attr = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type INNER JOIN ".$slrp_prefix."creature_attribute_type ON ".$slrp_prefix."creature_attribute_type.attribute_type_id = ".$slrp_prefix."attribute_type.attribute_type_id WHERE ".$slrp_prefix."creature_attribute_type.creature_attribute_type_id = '$pcattrid'");
	while($updadmattr = mysql_fetch_assoc($upd_adm_attr))
	{
		while($admin_attr > $pcattrval)
		{
			$pcattrval++;
			$attr_tier = round(($pcattrval+(($slrpnfo[slurp_tier_width]-1)/2))/$slrpnfo[slurp_tier_width]);
			$attr_val_subtotal = ($attr_val_subtotal-$attr_tier);
		}
		
		while($admin_attr < $pcattrval)
		{		
			$attr_tier = round(($pcattrval+(($slrpnfo[slurp_tier_width]-1)/2))/$slrpnfo[slurp_tier_width]);
			$attr_val_subtotal = ($attr_val_subtotal+$attr_tier);
			$pcattrval--;
		}
		
//		$attr_tier = round(($admin_attr+(($slrpnfo[slurp_tier_width]-1)/2))/$slrpnfo[slurp_tier_width]);
		// if the character is approved, spend xp and log it by the current user.
		if($curpcnfo[creature_status_id] == 4)
		{
			$xp_change = $attr_val_subtotal;
			$new_xp_total = ($curpcnfo[creature_xp_current]+$xp_change);
			$reason = ("Changed ".$updadmattr[attribute_type_short]." to ".$admin_attr." for ".$xp_change." XP. . . New XP total: ".$new_xp_total);
			
			// uncomment to check variables
			// echo"<br><br>old: $pcattrval<br>new: $admin_attr<br>reason: $reason<br>new total: $new_xp_total<br>$xp_change<br>";
			
			$increment_experience = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_xp_current = '$new_xp_total' WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating experience for new abchar.");
			$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','$xp_change','$usrnfo[user_id]','$reason')") or die ("failed to insert xp log.");	
		}
		
		echo"
		<tr>
		
		<td width = '100%'>
		<font color = 'yellow'>
		<li> $curpcnfo[creature]'s $updadmattr[attribute_type] was changed to $admin_attr";

		if($curpcnfo[creature_status_id] == 4)
		{
			echo" for $xp_change XP (new total: $new_xp_total)";
		}

		echo".
		</font>
		<hr>
		<form name = 'back_to_ab_char' method='post' action = 'modules.php?name=$module_name&file=pc_attr'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='submit' value='Continue' name='back_to_attr'>
		</form>
		</td>
		</tr>
		";
	}
}

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>