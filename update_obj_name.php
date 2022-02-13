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
$nav_title = "View Ability";
include("modules/$module_name/includes/slurp_header.php");

// assumes a $_POST  variable of 'edit_subfocus_name' is set
// assumes a $getsubfocnfoid variable is set; the object_id, typically.

$object_name = strip_tags(mysql_real_escape_string($_POST['edit_subfocus_name']));
$object_desc = strip_tags(mysql_real_escape_string($_POST['edit_subfocus_desc']));

if($getfoc[focus_id] == 10)
{
	$object_short = strip_tags(mysql_real_escape_string($_POST['edit_subfocus_short']));
	$object_short_desc = strip_tags(mysql_real_escape_string($_POST['edit_subfocus_short_desc']));
}
if($getfoc[focus_id] == 26)
{
	$object_short = strip_tags(mysql_real_escape_string($_POST['edit_subfocus_short']));
	$object_short_desc = strip_tags(mysql_real_escape_string($_POST['edit_subfocus_short_desc']));
}

// verify the object exists
$verify_existing_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_id = '$getsubfocnfoid'") or die ("failed to verify existing subfocus.");
$verexsbfoccnt = mysql_num_rows($verify_existing_subfocus);

// echo"$object_name, $object_desc, $object_short, $object_short_desc<br>$verexsbfoccnt<br>";

// if so, inform the user
if($verexsbfoccnt >= 1)
{
	$verexsbfoc = mysql_fetch_assoc($verify_existing_subfocus);
	$existing_sub = strip_tags(stripslashes($verexsbfoc[$getfoc[focus_table]]));
	
	if($verbose == 1)
	{
		echo"
		<tr>
		<td colspan = '7' align = 'left' valign = 'top'>
		<font color = 'white' size = '2'>
		<li> <i>$existing_sub</i> is already a(n) <i>$getfoc[focus]</i>.
		<hr>
		</td>
		</tr>
		";
	}
	
	if($verexsbfoc[$getfoc[focus_table].'_id'] == $getsubfocnfoid)
	{
		if($getfoc[focus_id] == 10)
		{
			 echo"grpnm: $group_name, grplvl: $group_level...objnm: $object_name<br>";
			//$update_object_name = mysql_query("UPDATE ".$slrp_prefix.$group_name.$group_level." SET ".$group_name.$group_level." = '$object_name', ".$group_name.$group_level."_desc = '$object_desc', ".$group_name.$group_level."_short_name = '$object_short', ".$group_name.$group_level."_short_desc = '$object_short_desc' WHERE ".$group_name.$group_level."_id = '$getsubfocnfoid'") or die ("failed updating $group_name"."$group_level name, desc.");
		}
		if($getfoc[focus_id] != 10)
		{
			if($getfoc[focus_id] == 26)
			{
			 echo"grpnm: $group_name, grplvl: $group_level...objnm: $object_name<br>gnnm: $object_short<br>gndsc: $object_short_desc<br>";
			//$update_object_name = mysql_query("UPDATE ".$slrp_prefix.$group_name.$group_level." SET ".$group_name.$group_level." = '$object_name', ".$group_name.$group_level."_desc = '$object_desc', ".$group_name.$group_level."_short_name = '$object_short', ".$group_name.$group_level."_short_desc = '$object_short_desc' WHERE ".$group_name.$group_level."_id = '$getsubfocnfoid'") or die ("failed updating $group_name"."$group_level name, desc.");
			}
			if($getfoc[focus_id] != 26)
			{
				 echo"grpnm: $group_name, grplvl: $group_level...objnm: $object_name<br>";
				//$update_object_name = mysql_query("UPDATE ".$slrp_prefix.$group_name.$group_level." SET ".$group_name.$group_level." = '$object_name', ".$group_name.$group_level."_desc = '$object_desc' WHERE ".$group_name.$group_level."_id = '$getsubfocnfoid'") or die ("failed updating object name, desc.");
			}
		}
	}
	
	if($verexsbfoc[$getfoc[focus_table].'_id'] != $getsubfocnfoid)
	{
		if($getfoc[focus_id] == 10)
		{
			 echo"grpnm: $group_name, grplvl: $group_level...objnm: $object_name<br>";
			//$update_object_not_name = mysql_query("UPDATE ".$slrp_prefix.$group_name.$group_level." SET ".$group_name.$group_level."_desc = '$object_desc', ".$group_name.$group_level."_short_name = '$object_short', ".$group_name.$group_level."_short_desc = '$object_short_desc' WHERE ".$group_name.$group_level."_id = '$getsubfocnfoid'") or die ("failed updating $group_name"."$group_level name, desc.");
		}
		if($getfoc[focus_id] != 10)
		{
			if($getfoc[focus_id] == 26)
			{
			  echo"grpnm: $group_name, grplvl: $group_level...objnm: $object_name<br>gnnm: $object_short<br>gndsc: $object_short_desc<br>";
			//$update_object_not_name = mysql_query("UPDATE ".$slrp_prefix.$group_name.$group_level." SET ".$group_name.$group_level."_desc = '$object_desc', ".$group_name.$group_level."_short_name = '$object_short', ".$group_name.$group_level."_short_desc = '$object_short_desc' WHERE ".$group_name.$group_level."_id = '$getsubfocnfoid'") or die ("failed updating $group_name"."$group_level name, desc.");
			}
			if($getfoc[focus_id] != 26)
			{
				 echo"grpnm: $group_name, grplvl: $group_level...objnm: $object_name<br>";
				// $update_object_not_name = mysql_query("UPDATE ".$slrp_prefix.$group_name.$group_level." SET ".$group_name.$group_level."_desc = '$object_desc' WHERE ".$group_name.$group_level."_id = '$getsubfocnfoid'") or die ("failed updating $group_name"."$group_level name, desc.");
			}
		}
	}
	
	$edit_subfocus_id = $getsubfocnfoid;
}

// if not, update and adjust dependent names
if($verexsbfoccnt == 0)
{	
	if($getfoc[focus_id] == 10)
	{
		 echo"grpnm: $group_name, grplvl: $group_level...objnm: $object_name<br>";
		// $update_object_name = mysql_query("UPDATE ".$slrp_prefix.$group_name.$group_level." SET ".$group_name.$group_level." = '$object_name', ".$group_name.$group_level."_desc = '$object_desc', ".$group_name.$group_level."_short_name = '$object_short', ".$group_name.$group_level."_short_desc = '$object_short_desc' WHERE ".$group_name.$group_level."_id = '$getsubfocnfoid'") or die ("failed updating object name, desc.");
	}
	if($getfoc[focus_id] != 10)
	{
		if($getfoc[focus_id] == 26)
		{
		 echo"grpnm: $group_name, grplvl: $group_level...objnm: $object_name<br>gnnm: $object_short<br>gndsc: $object_short_desc<br>";
		// $update_object_name = mysql_query("UPDATE ".$slrp_prefix.$group_name.$group_level." SET ".$group_name.$group_level." = '$object_name', ".$group_name.$group_level."_desc = '$object_desc', ".$group_name.$group_level."_short_name = '$object_short', ".$group_name.$group_level."_short_desc = '$object_short_desc' WHERE ".$group_name.$group_level."_id = '$getsubfocnfoid'") or die ("failed updating object name, desc.");
		}
		if($getfoc[focus_id] != 26)
		{
			 echo"grpnm: $group_name, grplvl: $group_level...objnm: $object_name<br>";
		//	$update_object_name = mysql_query("UPDATE ".$slrp_prefix.$group_name.$group_level." SET ".$group_name.$group_level." = '$object_name', ".$group_name.$group_level."_desc = '$object_desc' WHERE ".$group_name.$group_level."_id = '$getsubfocnfoid'") or die ("failed updating object name, desc.");
		}

	}
}

// verify the record
$verify_inserted_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]." = '$object_name'") or die ("failed to verify inserted subfocus 43.");
$verinssbfoc = mysql_fetch_assoc($verify_inserted_subfocus);
$verinssbfoccnt = mysql_num_rows($verify_inserted_subfocus);
$verify_inserted_sub = strip_tags(stripslashes($verinssbfoc[$getfoc[focus_table]]));

//because the tier columns are not standard, get it specifically
$verify_inserted_subfocus_tier = mysql_query("SELECT ".$getfoc[focus_table]."_tier FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_id = '".$verinssbfoc[$getfoc[focus_table].'_id']."'") or die ("failed to verify inserted subfocus tier again 2.");
$verinssbfoctr = mysql_fetch_assoc($verify_inserted_subfocus_tier);
$vrinssbfctr = $verinssbfoctr[$getfoc[focus_table].'_tier'];

// and then get the parent object for children
if($getfoc[focus_level] <= 2)
{	
	$verify_parent_tier = mysql_query("SELECT ".$group_name.$group_parent."_tier FROM ".$slrp_prefix.$group_name.$group_parent." INNER JOIN ".$slrp_prefix.$parent_joiner." ON ".$slrp_prefix.$parent_joiner.".".$group_name.$group_parent."_id = ".$slrp_prefix.$group_name.$group_parent.".".$group_name.$group_parent."_id WHERE ".$slrp_prefix.$parent_joiner.".".$group_name.$group_level."_id = '".$verinssbfoc[$getfoc[focus_table].'_id']."'") or die ("failed to verify parent tier h6.");
	$verprnttrcnt = mysql_num_rows($verify_parent_tier);
	
	if($verprnttrcnt >= 1)
	{
		$verprnttr = mysql_fetch_assoc($verify_parent_tier);
		$parent_tier = $verprnttr[$group_name.$group_parent.'_tier'];
		//echo"derived_";
	}
	if($verprnttrcnt == 0)
	{
		$parent_tier = $vrinssbfctr;
		//echo"preset_";
	}						
	
	//echo"prnttr: $parent_tier<br>";
}

// if it inserted correctly, offer a button to refresh the page for that object, since it split in X objects by rating
if($verinssbfoccnt >= 1)
{
	// get the verbage for the qualifier
	$get_focus_exclusion = mysql_query("SELECT * FROM ".$slrp_prefix."focus_exclusion WHERE focus_id = '$getfoc[focus_id]' ORDER BY focus_exclusion") or die ("failed to get focus exclusion 27.");
	$gtfcexcnt = mysql_num_rows($get_focus_exclusion);
	
	while($gtfcex = mysql_fetch_assoc($get_focus_exclusion))
	{
		// start setting values. Based on the Inverted property, different numeric handlers.
		$thing_type = $gtfcex[focus_ability_modifier_type_id];
		$thing_weight = $getfoc[focus_weight];
		$thing_level = $getfoc[focus_level];
		// echo"THING: $thing_type, $thing_weight, $thing_level<br>";
		
		
		if($getfoc[focus_is_object] == 0)
		{
			$thing_tier = $vrinssbfctr;
			$thing_max_tier = $subtype_max_tier;
		}
		
		if($getfoc[focus_is_object] >= 1)
		{
			if($thing_level == 3)
			{
				$thing_max_tier = $subtype_max_tier;
				$thing_tier = $vrinssbfctr;
			}
			
			if($thing_level <= 2)
			{
				if($getfoc[focus_id] == 9)
				{
					if(isset($_POST['update_object_tier']))
					{
						$thing_tier = $_POST['update_object_tier'];
					}
					if(empty($_POST['update_object_tier']))
					{
						$thing_max_tier = $subtype_max_tier;
						$thing_tier = $verexsbfctr;
					}
					
					// echo"Thing Tier Effect3: $thing_tier<br>";
				}
				if($getfoc[focus_id] != 9)
				{
					$thing_max_tier = $subtype_max_tier;
					$thing_tier = $parent_tier;
					// echo"Thing Tier not Effect: $thing_tier<br>";
				}
			}						
			if($getfoc[focus_id] >= 27)
			{
				$thing_max_tier = $subtype_max_tier;
				$thing_tier = $vrinssbfctr;
			}
		}
		
		if($gtfcex[focus_inverted] == 1)
		{
			//echo" (WT - T); weight of the object minus the tier in the table, with an upper limit = 0<br>";
			$new_subfocus_value = ($thing_weight - $thing_tier);
			if($new_subfocus_value >= 0)
			{
				$new_subfocus_value = 0;
			}
		}
		
		if($gtfcex[focus_inverted] == 2)
		{
			//echo"-(5+WT - T); negative (5 plus weight minus Tier)<br>";
			$new_subfocus_value = -(5 + $thing_weight - $thing_tier);
		}
		
		if($gtfcex[focus_inverted] == 3)
		{
			//echo"(WT+T); Object tier plus the table listed weight<br>";
			$new_subfocus_value = ($thing_weight + $thing_tier);
		}
		
		if($gtfcex[focus_inverted] == 4)
		{
			//echo"LVL; The level of the group (type = 3, subtype = 2, instance = 1)<br>";
			$new_subfocus_value = $thing_level;
		}
		
		if($gtfcex[focus_inverted] == 5)
		{
			//echo" - LVL; negative the Level of the group<br>";
			$new_subfocus_value = -($thing_level);
		}
		
		if($gtfcex[focus_inverted] == 6)
		{
			//echo" T; The Tier value<br>";
			$new_subfocus_value = $thing_tier;
		}
		
		if($gtfcex[focus_inverted] == 7)
		{
			//echo" -T; negative the Tier value<br>";
			$new_subfocus_value = -($thing_tier);
		}
		
		if($gtfcex[focus_inverted] == 8)
		{
			//echo" -(5 + WT -  LVL) you should be getting the idea by now.<br>";
			$new_subfocus_value = -(5 + $thing_weight - $thing_level);
		}
		
		if($gtfcex[focus_inverted] == 9)
		{
			//echo" -WT<br>";
			$new_subfocus_value = -($thing_weight);
		}
		
		if($gtfcex[focus_inverted] == 10)
		{
			//echo" WT<br>";
			$new_subfocus_value = $thing_weight;
		}
		
		if($gtfcex[focus_inverted] == 11)
		{
			//echo" -(6 - LVL) -(6 minus the object level).<br>";
			$new_subfocus_value = -(6 - $thing_level);
		}
		
		//echo"3 WT: $thing_weight ($getfoc[focus_weight])<br>LVL: $thing_level<br>TR: $thing_tier<br>MAX: $thing_max_tier<br>";
		// echo"INV3: $gtfcex[focus_inverted]<br>TOT:$new_subfocus_value<br>TYPE: $thing_type<br>";
		
		// compile the text strings
		$old_modifier_short = strip_tags(mysql_real_escape_string($gtfcex[focus_exclusion]." ".$verexsbfoc[$getfoc[focus_table]]));
		$new_subfocus_short = strip_tags(mysql_real_escape_string($gtfcex[focus_exclusion]." ".$verify_inserted_sub));
		$new_subfocus_modifier = ($new_subfocus_short.".");
		
		// echo"old A (".$verexsbfoc[$getfoc[focus_table]]."): ($old_modifier_short), new A: ($new_subfocus_short)<br>";
		
		// insert or update the new modifier
		$verify_correct_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_type_id = '$thing_type' AND ability_modifier_value = '$new_subfocus_value' AND ability_modifier = '$new_subfocus_modifier' AND ability_modifier_short = '$new_subfocus_short'") or die ("failed verifying correct ".$getfoc[focus_table]." subtype 23cb.");
		$vercrctmodcnt = mysql_num_rows($verify_correct_modifier);
		
		// if already correct, leave it alone
		if($vercrctmodcnt == 1)
		{
			// sweet.
		}
		
		if($vercrctmodcnt == 0)
		{
			$verify_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_short = '$old_modifier_short'") or die ("failed verifying incorrect ".$getfoc[focus_table]." subtype 23cb.");
			$vermod = mysql_fetch_assoc($verify_modifier);
			$vermodcnt = mysql_num_rows($verify_modifier);
			
			if($vermodcnt == 1)
			{
				// $update_modifier_info = mysql_query("UPDATE ".$slrp_prefix."ability_modifier SET ability_modifier_type_id = '$thing_type', ability_modifier_value = '$new_subfocus_value', ability_modifier = '$new_subfocus_modifier', ability_modifier_short = '$new_subfocus_short' WHERE ability_modifier_id = '$vermod[ability_modifier_id]'") or die ("failed updating new ".$getfoc[focus_table]." mod value 23c.");
			}
			
			if($vermodcnt == 0)
			{
				// $insert_modifier_info = mysql_query("INSERT INTO ".$slrp_prefix."ability_modifier (ability_modifier_type_id,ability_modifier_value,ability_modifier,ability_modifier_short) VALUES ('$thing_type','$new_subfocus_value','$new_subfocus_modifier','$new_subfocus_short')") or die ("failed inserting new ".$getfoc[focus_table]." subtype relation 23c.");
			}
		}
		
		// verify if made it in
		$verify_new_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_type_id = '$thing_type' AND ability_modifier_value = '$new_subfocus_value' AND ability_modifier = '$new_subfocus_modifier' AND ability_modifier_short = '$new_subfocus_short'") or die ("failed verifying new inserted ".$getfoc[focus_table]." subtype 23c.");
		$vernewmodcnt = mysql_num_rows($verify_new_modifier);
		$vernewmod = mysql_fetch_assoc($verify_new_modifier);
		
		// if so, inform the user.
		if($vernewmodcnt >= 1)
		{
			if($verbose == 1)
			{
				$new_subfocus_short = strip_tags(stripslashes($new_subfocus_short));
				echo"
				<tr>
				
				<td colspan = '7' valign = 'top' align = 'left'>
				<font color = 'yellow' size = '2'>
				<li> <i>$new_subfocus_short</i> is confirmed at a value of $vernewmod[ability_modifier_value].
				</td>
				
				</tr>
				<tr>
				
				<td colspan = '7' valign = 'top' align = 'left'>
				<hr width = '100%'>
				</td>
				
				</tr>
				";
			}
			
			$verify_modsub = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_id = '$vernewmod[ability_modifier_id]' AND subfocus_id = '".$verinssbfoc[$getfoc[focus_table].'_id']."' AND focus_id = '$current_focus_id' AND focus_exclusion_id = '$gtfcex[focus_exclusion_id]'") or die ("failed verifying inserted new mod subfocus 00.");
			$vermodsubcnt = mysql_num_rows($verify_modsub);
			$vermodsub = mysql_fetch_assoc($verify_modsub);
			
			if($vermodsubcnt == 1)
			{
				echo" Updating...<br>";
				//$update_new_modifier_subfocus = mysql_query("UPDATE ".$slrp_prefix."ability_modifier_subfocus  SET subfocus_id = '".$verinssbfoc[$getfoc[focus_table].'_id']."',focus_id = '$current_focus_id',focus_exclusion_id = '$gtfcex[focus_exclusion_id]' WHERE ability_modifier_subfocus_id = '$vermodsub[ability_modifier_subfocus_id]'") or die ("failed to insert new mod subfocus 00.");
			}
			
			if($vermodsubcnt == 0)
			{
			echo"<b><font color='purple'>INSERTING...</font></b><br>";
				// $insert_new_modifier_subfocus = mysql_query("INSERT INTO ".$slrp_prefix."ability_modifier_subfocus (ability_modifier_id,subfocus_id,focus_id,focus_exclusion_id) VALUES ('$vernewmod[ability_modifier_id]','".$verinssbfoc[$getfoc[focus_table].'_id']."','$current_focus_id','$gtfcex[focus_exclusion_id]')") or die ("failed to insert new mod subfocus 00.");
			}
			
			$verify_new_modsub = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_id = '$vernewmod[ability_modifier_id]' AND subfocus_id = '".$verinssbfoc[$getfoc[focus_table].'_id']."' AND focus_id = '$current_focus_id' AND focus_exclusion_id = '$gtfcex[focus_exclusion_id]'") or die ("failed verifying inserted new mod subfocus 00.");
			$vernewmodsubcnt = mysql_num_rows($verify_new_modsub);
			$vernewmodsub = mysql_fetch_assoc($verify_new_modsub);
			
			// let them know if it made it or not
			if($vernewmodsubcnt >= 1)
			{
				if($verbose == 1)
				{
					$new_subfocus_short = strip_tags(stripslashes($new_subfocus_short));
					$verify_inserted_sub = strip_tags(stripslashes($verify_inserted_sub));
					
					echo"
					<tr>
					
					<td colspan = '7' valign = 'top' align = 'left'>
					<font color = 'yellow' size = '2'>
					<li> <i>$new_subfocus_short</i> has a subfocus of <i>$verify_inserted_sub</i>.
					</td>
					
					</tr>
					<tr>
					
					<td colspan = '7' valign = 'top' align = 'left'>
					<hr width = '100%'>
					</td>
					
					</tr>
					";
				}
			}
			
			if($vernewmodsubcnt == 0)
			{
				$new_subfocus_short = strip_tags(stripslashes($new_subfocus_short));
				$verify_inserted_sub = strip_tags(stripslashes($verify_inserted_sub));
				
				echo"
				<tr>
				
				<td colspan = '7' valign = 'top' align = 'left'>
				<font color = 'red' size = '2'>
				<li> <i>$new_subfocus_short</i> DOES NOT have a subfocus of <i>$verify_inserted_sub</i>. Try again or check with an admin if there is a problem.
				</td>
				
				</tr>
				<tr>
				
				<td colspan = '7' valign = 'top' align = 'left'>
				<hr width = '100%'>
				</td>
				
				</tr>
				";
			}
		}
		
		// if not, tell them
		if($vernewmodcnt == 0)
		{
			$new_subfocus_short = strip_tags(stripslashes($new_subfocus_short));
			$verify_inserted_sub = strip_tags(stripslashes($verify_inserted_sub));
			
			echo"
			<tr>
			
			<td colspan = '7' valign = 'top' align = 'left'>
			<font color = 'red' size = '2'>
			<li> <i>$new_subfocus_short</i> was not confirmed as one of <i>$verify_inserted_sub</i>'s modifiers. Please try again or contact an admin if there is a problem.
			</td>
			
			</tr>
			<tr>
			
			<td colspan = '7' valign = 'top' align = 'left'>
			<hr width = '100%'>
			</td>
			
			</tr>
			";
		}
	}
	
	$edit_subfocus_id = $verinssbfoc[$getfoc[focus_table].'_id'];
}

if($verinssbfoccnt == 0)
{
	$new_subfocus_short = stripslashes($new_subfocus_short);
	
	echo"
	<tr>
	<td colspan = '7' align = 'left' valign = 'top'>
	<font color = 'red' size = '2'>
	<li> <i>$new_subfocus_short</i> is NOT a(n) <i>$getfoc[focus]</i>. Please try again or contact an admin if there is a problem.
	<hr>
	</td>
	</tr>
	";
}

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>