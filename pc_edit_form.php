<?php
if (!eregi("modules.php", $PHP_SELF)) {
  die ("You can't access this file directly...");
}
$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("header.php");
$nav_title = "View Character";
include("modules/$module_name/includes/slurp_header.php");

if(empty($_POST['created_item']))
{
	if(empty($_POST['min_rank']))
	{
		if(empty($_POST['stone_count']))
		{
			if(empty($_POST['change_pc_player']))
			{
				$pc_player_id = $curpcnfo[creature_nuke_user_id];
				
				if(empty($_POST['pc_status']))
				{
					$pc_status = $curpcnfo[creature_status_id];
				}
				
				if(isset($_POST['pc_status']))
				{
					$xp_change = 0;
					$pc_status = $_POST['pc_status'];
					// echo"status: $pc_status<br>";
					if($pc_status == 0)
					{
						$reason = ("Template approved for use.");
					}
					if($pc_status == 2)
					{
						$reason = ("Character returned for changes.");
					}
					if($pc_status == 3)
					{
						$reason = ("Character submission.");
					}
					if($pc_status == 4)
					{
						$reason = ("Character approved for use.");
					}
					
					$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','$xp_change','$usrnfo[user_id]','$reason')") or die ("failed adding character submission to xp log.");
				}
			}
			
			if(isset($_POST['change_pc_player']))
			{
				$pc_player_id = $_POST['change_pc_player'];
				
				$get_new_player = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$pc_player_id'") or die("failed to get new pc player.");
				$gtnwplyr = mysql_fetch_assoc($get_new_player);
				
				$xp_change = 0;
				$pc_status = $curpcnfo[creature_status_id];
				$reason = ("Player changed to $gtnwplyr[username].");
				$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','$xp_change','$usrnfo[user_id]','$reason')") or die ("failed adding character submission to xp log.");
			}
			
			if(isset($_POST['pc_npc']))
			{
				$pc_npc = 1;
			}
			else
			{
				$pc_npc = 0;
			}
			
			$submit_character = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_status_id = '$pc_status', creature_nuke_user_id = '$pc_player_id', creature_npc = '$pc_npc' WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating status for submitted PC.");
			
			$verify_submit = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id = '$curpcnfo[creature_id]' AND creature_nuke_user_id = '$pc_player_id' AND creature_status_id = '$pc_status'");
			$versub = mysql_fetch_assoc($verify_submit);
			$versubcnt = mysql_num_rows($verify_submit);
			$verify_inserted_sub = stripslashes($versub[creature]);
			
			$get_focus = mysql_query("SELECT * FROM ".$slrp_prefix."focus WHERE focus_id = '7'") or die("failed to get posted focus.");
			$getfoc = mysql_fetch_assoc($get_focus);
			
			echo"
			<tr>
			
			<td width = '100%'>
			<font color='green' size='2'>";
			
			if($pc_status == '0')
			{
				echo"<li> $versub[creature] has been created as a Template.</font>";
			}
			if($pc_status == '2')
			{
				echo"<li> $versub[creature] has been returned.</font>";
			}
			if($pc_status == '3')
			{
				echo"<li> $versub[creature] has been submitted.</font>";
			}
			if($pc_status == '4')
			{
				echo"<li> $versub[creature] has been approved.</font>";
				
				$group_name = "creature";
				$subtype_name = "creature";
				$group_level = "";
				$group_parent = "_subtype";
				$parent_joiner = "creature_creature_subtype";
				
				// $update_view_rank = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_min_rank = '8' WHERE creature_id = '$versub[creature_id]'") or die ("failed to update creature min rank.");
				
				//because the tier columns are not standard, get it specifically
				$verify_subfocus_tier = mysql_query("SELECT creature_tier FROM ".$slrp_prefix."creature WHERE creature_id = '$versub[creature_id]'") or die ("failed to verify inserted subfocus tier 4.");
				$verinssbfoctr = mysql_fetch_assoc($verify_subfocus_tier);
				
				// and then get the parent object for children
				$verify_parent_tier = mysql_query("SELECT ".$group_name.$group_parent."_tier FROM ".$slrp_prefix.$group_name.$group_parent." INNER JOIN ".$slrp_prefix.$parent_joiner." ON ".$slrp_prefix.$parent_joiner.".".$group_name.$group_parent."_id = ".$slrp_prefix.$group_name.$group_parent.".".$group_name.$group_parent."_id WHERE ".$slrp_prefix.$parent_joiner.".".$subtype_name.$group_level."_id = '$versub[creature_id]'") or die ("failed to verify parent tier 943.");
				$verprnttrcnt = mysql_num_rows($verify_parent_tier);
				
				if($verprnttrcnt >= 1)
				{
					$verprnttr = mysql_fetch_assoc($verify_parent_tier);
					$parent_tier = $verprnttr[$group_name.$group_parent.'_tier'];
					//echo"derived_";
				}
				if($verprnttrcnt == 0)
				{
					$parent_tier = $verinssbfoctr[creature_tier];
					//echo"preset_";
				}						
				
				//echo"prnttr: $parent_tier<br>";
				
				// if it inserted correctly, offer a button to refresh the page for that object, since it split in X objects by rating
				if($versubcnt >= 1)
				{
					// get the verbage for the qualifier
					$get_focus_exclusion = mysql_query("SELECT * FROM ".$slrp_prefix."focus_exclusion WHERE focus_id = '$getfoc[focus_id]' ORDER BY focus_exclusion") or die ("failed to get focus exclusion.");
					$gtfcexcnt = mysql_num_rows($get_focus_exclusion);
					
					while($gtfcex = mysql_fetch_assoc($get_focus_exclusion))
					{
						// start setting values. Based on the Inverted property, different numeric handlers.
						
						$thing_weight = $getfoc[focus_weight];
						$thing_level = $getfoc[focus_level];
						
						if($getfoc[focus_is_object] == 0)
						{
							$thing_tier = $verinssbfoctr[creature_tier];
							$thing_max_tier = $subtype_max_tier;
						}
						
						if($getfoc[focus_is_object] >= 1)
						{
							if($thing_level == 3)
							{
								$thing_max_tier = $subtype_max_tier;
								$thing_tier = $verinssbfoctr[creature_tier];
							}
							
							if($thing_level <= 2)
							{
								$thing_max_tier = $subtype_max_tier;
								$thing_tier = $parent_tier;
							}
							
							if($getfoc[focus_id] >= 27)
							{
								$thing_max_tier = $subtype_max_tier;
								$thing_tier = $verinssbfoctr[creature_tier];
							}
							
							if($group_name == "effect")
							{
								$thing_max_tier = $subtype_max_tier;
								$thing_tier = $verinssbfoctr[creature_tier];
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
						
						//echo"1 WT: $thing_weight ($getfoc[focus_weight])<br>LVL: $thing_level<br>TR: $thing_tier<br>MAX: $thing_max_tier<br>";
						//echo"INV: $gtfcex[focus_inverted]<br>TOT:$new_subfocus_value<br>";
						
						// compile the text strings
						$old_modifier_short = mysql_real_escape_string($gtfcex[focus_exclusion]." ".$curpcnfo[creature]);
						$new_subfocus_short = mysql_real_escape_string($gtfcex[focus_exclusion]." ".$verify_inserted_sub);
						$new_subfocus_modifier = ($new_subfocus_short.".");
						
						// echo"<font color = 'blue'>$new_subfocus_modifier<br></font>";
						
						// insert or update the new modifier
						$verify_correct_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_type_id = '$gtfcex[focus_ability_modifier_type_id]' AND ability_modifier_value = '$new_subfocus_value' AND ability_modifier = '$new_subfocus_modifier' AND ability_modifier_short = '$new_subfocus_short'") or die ("failed verifying correct pc subtype 23ab.");
						$vercrctmodcnt = mysql_num_rows($verify_correct_modifier);
						
						// if already correct, leave it alone
						if($vercrctmodcnt == 1)
						{
							// sweet.
						}
						
						if($vercrctmodcnt == 0)
						{
							$verify_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_short = '$old_modifier_short'") or die ("failed verifying incorrect pc subtype 230b.");
							$vermod = mysql_fetch_assoc($verify_modifier);
							$vermodcnt = mysql_num_rows($verify_modifier);
							
							if($vermodcnt == 1)
							{
								$update_modifier_info = mysql_query("UPDATE ".$slrp_prefix."ability_modifier SET ability_modifier_type_id = '$gtfcex[focus_ability_modifier_type_id]', ability_modifier_value = '$new_subfocus_value', ability_modifier = '$new_subfocus_modifier', ability_modifier_short = '$new_subfocus_short' WHERE ability_modifier_id = '$vermod[ability_modifier_id]'") or die ("failed updating new pc mod value 23c.");
							}
							
							if($vermodcnt == 0)
							{
								$insert_modifier_info = mysql_query("INSERT INTO ".$slrp_prefix."ability_modifier (ability_modifier_type_id,ability_modifier_value,ability_modifier,ability_modifier_short) VALUES ('$gtfcex[focus_ability_modifier_type_id]','$new_subfocus_value','$new_subfocus_modifier','$new_subfocus_short')") or die ("failed inserting new pc subtype relation 23c.");
							}
						}
						
						// verify if made it in
						$verify_new_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_type_id = '$gtfcex[focus_ability_modifier_type_id]' AND ability_modifier_value = '$new_subfocus_value' AND ability_modifier = '$new_subfocus_modifier' AND ability_modifier_short = '$new_subfocus_short'") or die ("failed verifying new inserted pc subtype 23c.");
						$vernewmodcnt = mysql_num_rows($verify_new_modifier);
						$vernewmod = mysql_fetch_assoc($verify_new_modifier);
						
						// if so, inform the user.
						if($vernewmodcnt >= 1)
						{
							echo"
							<tr>
							
							<td colspan = '7' valign = 'top' align = 'left'>
							<font color = 'yellow' size = '2'>
							<li> <i>$new_subfocus_short</i> added to modifiers.
							</td>
							
							</tr>
							<tr>
							
							<td colspan = '7' valign = 'top' align = 'left'>
							
							</td>
							
							</tr>
							";
							
							$verify_modsub = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_id = '$vernewmod[ability_modifier_id]' AND subfocus_id = '$versub[creature_id]' AND focus_id = '$getfoc[focus_id]' AND focus_exclusion_id = '$gtfcex[focus_exclusion_id]'") or die ("failed verifying inserted new mod subfocus.");
							$vermodsubcnt = mysql_num_rows($verify_modsub);
							$vermodsub = mysql_fetch_assoc($verify_modsub);
							
							// echo"subs: $vermodsubcnt<br>";
							
							if($vermodsubcnt == 1)
							{
								$update_new_modifier_subfocus = mysql_query("UPDATE ".$slrp_prefix."ability_modifier_subfocus  SET subfocus_id = '$versub[creature_id]',focus_id = '$getfoc[focus_id]',focus_exclusion_id = '$gtfcex[focus_exclusion_id]' WHERE ability_modifier_subfocus_id = '$vermodsub[ability_modifier_subfocus_id]'") or die ("failed to insert new mod subfocus.");
							}
							
							if($vermodsubcnt == 0)
							{
								$insert_new_modifier_subfocus = mysql_query("INSERT INTO ".$slrp_prefix."ability_modifier_subfocus (ability_modifier_id,subfocus_id,focus_id,focus_exclusion_id) VALUES ('$vernewmod[ability_modifier_id]','$versub[creature_id]','$getfoc[focus_id]','$gtfcex[focus_exclusion_id]')") or die ("failed to insert new mod subfocus.");
							}
							
							$verify_new_modsub = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_id = '$vernewmod[ability_modifier_id]' AND subfocus_id = '$versub[creature_id]' AND focus_id = '$getfoc[focus_id]' AND focus_exclusion_id = '$gtfcex[focus_exclusion_id]'") or die ("failed verifying inserted new mod subfocus.");
							$vernewmodsubcnt = mysql_num_rows($verify_new_modsub);
							$vernewmodsub = mysql_fetch_assoc($verify_new_modsub);
							
							// echo"newsubs: $vernewmodsubcnt<br>";
							
							// let them know if it made it or not
							if($vernewmodsubcnt >= 1)
							{
								echo"
								<tr>
								
								<td colspan = '7' valign = 'top' align = 'left'>
								<font color = 'yellow' size = '2'>
								<li> Subfocus <i>$verify_inserted_sub</i> added to <i>$new_subfocus_short</i>.
								</td>
								
								</tr>
								<tr>
								
								<td colspan = '7' valign = 'top' align = 'left'>
								
								</td>
								
								</tr>
								";
							}
							
							if($vernewmodsubcnt == 0)
							{
								echo"
								<tr>
								
								<td colspan = '7' valign = 'top' align = 'left'>
								<font color = 'red' size = '2'>
								<li> Modifier is not focused on <i>$new_subfocus_short</i>. Try again or check with an admin if there is a problem.
								</td>
								
								</tr>
								<tr>
								
								<td colspan = '7' valign = 'top' align = 'left'>
								
								</td>
								
								</tr>
								";
							}
						}
						
						// if not, tell them
						if($vernewmodcnt == 0)
						{
							echo"
							<tr>
							
							<td colspan = '7' valign = 'top' align = 'left'>
							<font color = 'red' size = '2'>
							<li> <i>$vernewmod[ability_modifier_short]</i> was not added to modifiers. Please try again or contact an admin if there is a problem.
							</td>
							
							</tr>
							<tr>
							
							<td colspan = '7' valign = 'top' align = 'left'>
							
							</td>
							
							</tr>
							";
						}
					}
				}
				
//				$verify_pc_cultures = mysql_query("SELECT * FROM ".$slrp_prefix."creature_culture WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc culture info.");
//				while($verpccults = mysql_fetch_assoc($verify_pc_cultures))
//				{
//					$culture_default_bbgroup = mysql_query("SELECT * FROM ".$slrp_prefix."culture_bbgroup WHERE culture_id = '$verpccults[culture_id]'") or die ("failed getting default culture bbgroup.");
//					$cultdfbbgrpcnt = mysql_num_rows($culture_default_bbgroup);
//					while($cultdfbbgrp = mysql_fetch_assoc($culture_default_bbgroup))
//					{
//						$verify_character_culture_bbgroups = mysql_query("SELECT * FROM nuke_bbuser_group WHERE group_id = '$cultdfbbgrp[group_id]' AND user_id = '$curpcnfo[creature_nuke_user_id]'") or die ("failed verifying pc culture bbgroups.");
//						$vrchrcultbbgrpscnt = mysql_num_rows($verify_character_culture_bbgroups);
//						// echo"<tr><td>culture $verpccults[culture_id] bbgroup $cultdfbbgrp[group_id] user $curpcnfo[creature_nuke_user_id]</td></tr>";
//						
//						if($vrchrcultbbgrpscnt == 0)
//						{
//							$new_culture_bbgroup = mysql_query("INSERT INTO nuke_bbuser_group (group_id,user_id,user_pending) VALUES ('$cultdfbbgrp[group_id]','$curpcnfo[creature_nuke_user_id]','0')") or die ("failed inserting new culture bbgroup.");
//						}
//						
//						$verify_character_culture_bbgroups_insert = mysql_query("SELECT * FROM nuke_bbuser_group WHERE group_id = '$cultdfbbgrp[group_id]' AND user_id = '$curpcnfo[creature_nuke_user_id]' AND user_pending = '0'") or die ("failed verifying pc culture bbgroups.");
//						$vrchrcultbbgrpsinstcnt = mysql_num_rows($verify_character_culture_bbgroups_insert);
//						if($vrchrcultbbgrpsinstcnt >= 1)
//						{
//							$default_bbgroup_cult_info = mysql_query("SELECT * FROM nuke_bbgroups WHERE group_id = '$cultdfbbgrp[group_id]'") or die ("failed getting default culture bbgroup info.");
//							$dfbbgrpcultnfo = mysql_fetch_assoc($default_bbgroup_cult_info);
//							
//							echo"
//						<tr>
//						<td colspan = '3' align = 'left' valign = 'top'>
//						<font color = 'yellow' size = '2'>
//						<li> <i>$curpcnfo[creature]</i>'s player, $curpcplyr[username], is now a member of the Forum Group <i>$dfbbgrpcultnfo[group_name]</i>.
//						
//						</font>
//						</td>
//						</tr>
//							";
//						}
//						if($vrchrcultbbgrpsinstcnt == 0)
//						{
//							$default_bbgroup_cult_info = mysql_query("SELECT * FROM nuke_bbgroups WHERE group_id = '$cultdfbbgrp[group_id]'") or die ("failed getting default culture bbgroup info.");
//							$dfbbgrpcultnfo = mysql_fetch_assoc($default_bbgroup_cult_info);
//							
//							echo"
//						<tr>
//						<td colspan = '3' align = 'left' valign = 'top'>
//						<font color = 'red' size = '2'>
//						<li> <i>$curpcnfo[creature]</i>'s player, $curpcplyr[username], failed to join the Forum Group <i>$dfbbgrpcultnfo[group_name]</i>.
//						
//						</font>
//						</td>
//						</tr>
//							";
//						}
//					}
//				}
				
				$verify_pc_races2 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_creature_subtype WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc race info.");
				while($verpcraces = mysql_fetch_assoc($verify_pc_races2))
				{
					// echo"<tr><td>race $verpcraces[creature_subtype_id]</td></tr>";
					
					$race_default_bbgroup = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype_bbgroup WHERE creature_subtype_id = '$verpcraces[creature_subtype_id]'") or die ("failed getting default race bbgroup.");
					$rcdfbbgrpcnt = mysql_num_rows($race_default_bbgroup);
					while($rcdfbbgrp = mysql_fetch_assoc($race_default_bbgroup))
					{
						$verify_character_race_bbgroups = mysql_query("SELECT * FROM nuke_bbuser_group WHERE group_id = '$rcdfbbgrp[group_id]' AND user_id = '$curpcnfo[creature_nuke_user_id]'") or die ("failed verifying pc race bbgroups.");
						$vrchrrcbbgrpscnt = mysql_num_rows($verify_character_race_bbgroups);
						// echo"<tr><td>bbgroup $rcdfbbgrp[group_id] user $curpcnfo[creature_nuke_user_id]</td></tr>";
						
						if($vrchrrcbbgrpscnt == 0)
						{
							$new_race_bbgroup = mysql_query("INSERT INTO nuke_bbuser_group (group_id,user_id,user_pending) VALUES ('$rcdfbbgrp[group_id]','$curpcnfo[creature_nuke_user_id]','0')") or die ("failed inserting new race bbgroup.");
						}
						
						$verify_character_race_bbgroups_insert = mysql_query("SELECT * FROM nuke_bbuser_group WHERE group_id = '$rcdfbbgrp[group_id]' AND user_id = '$curpcnfo[creature_nuke_user_id]' AND user_pending = '0'") or die ("failed verifying pc race bbgroups.");
						$vrchrracebbgrpsinstcnt = mysql_num_rows($verify_character_race_bbgroups_insert);
						if($vrchrracebbgrpsinstcnt >= 1)
						{
							$default_bbgroup_rc_info = mysql_query("SELECT * FROM nuke_bbgroups WHERE group_id = '$rcdfbbgrp[group_id]'") or die ("failed getting default race bbgroup info.");
							$dfbbgrprcnfo = mysql_fetch_assoc($default_bbgroup_rc_info);
							
							echo"
						<tr>
						<td colspan = '3' align = 'left' valign = 'top'>
						<font color = 'yellow' size = '2'>
						<li> <i>$curpcnfo[creature]</i>'s player, $curpcplyr[username], is now a member of the Forum Group <i>$dfbbgrprcnfo[group_name]</i>.
						
						</font>
						</td>
						</tr>
							";
						}
						if($vrchrracebbgrpsinstcnt == 0)
						{
							$default_bbgroup_rc_info = mysql_query("SELECT * FROM nuke_bbgroups WHERE group_id = '$rcdfbbgrp[group_id]'") or die ("failed getting default race bbgroup info.");
							$dfbbgrprcnfo = mysql_fetch_assoc($default_bbgroup_rc_info);
							
							echo"
						<tr>
						<td colspan = '3' align = 'left' valign = 'top'>
						<font color = 'red' size = '2'>
						<li> <i>$curpcnfo[creature]</i>'s player, $curpcplyr[username], failed to join the Forum Group <i>$dfbbgrprcnfo[group_name]</i>.
						
						</font>
						</td>
						</tr>
							";
						}
					}
				}
			}
		}
		
		if(isset($_POST['stone_count']))
		{
			$stone_count = $_POST['stone_count'];
			$stone_color = $_POST['stone_color'];
			
			if($stone_color == 'white')
			{
				$column_id = 1;
			}
			
			if($stone_color == 'red')
			{
				$column_id = 2;
			}
			
			if($stone_color == 'black')
			{
				$column_id = 3;
			}
			
			if($stone_color == 'blue')
			{
				$column_id = 4;
			}
			
			if($stone_color == 'green')
			{
				$column_id = 5;
			}
			
			$table_column_name = 'creature_tokens_'.$column_id;
			
			if($stone_count == 2)
			{
				$increase_stone_count = mysql_query("UPDATE ".$slrp_prefix."creature SET ".$table_column_name." = ".$table_column_name."+1 WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed to increase $stone_color stones.");
				
				$new_count = $curpcnfo[$table_column_name]+1;
				$reason = ("Added $stone_color stone; New Total: $new_count.");
				$xp_change = 0;
				$record_stone_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','$xp_change','$usrnfo[user_id]','$reason')") or die ("failed adding character submission to xp log.");
				
				echo"
				<tr>
				<td colspan = '3' align = 'left' valign = 'top'>
				<font color = 'yellow' size = '2'>
				<li> <i>$curpcnfo[creature]</i>'s $stone_color stone count is now <i>$new_count</i>.
				
				</font>
				</td>
				</tr>
				";
			}
			
			if($stone_count == 1)
			{
				$decrease_stone_count = mysql_query("UPDATE ".$slrp_prefix."creature SET ".$table_column_name." = ".$table_column_name."-1 WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed to decrease $stone_color stones.");
				$new_count = $curpcnfo[$column_id]-1;
				$reason = ("Subtracted $stone_color stone; New Total: $new_count.");
				$xp_change = 0;
				$record_stone_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','$xp_change','$usrnfo[user_id]','$reason')") or die ("failed adding character submission to xp log.");
				
				echo"
				<tr>
				<td colspan = '3' align = 'left' valign = 'top'>
				<font color = 'yellow' size = '2'>
				<li> <i>$curpcnfo[creature]</i>'s $stone_color stone count is now <i>$new_count</i>.
				
				</font>
				</td>
				</tr>
				";
			}
		}
	}
	if(isset($_POST['min_rank']))
	{
		$min_rank = $_POST['min_rank'];
		$new_focus = "creature";
		
		$current_rank_information = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix.$new_focus." ON ".$slrp_prefix.$new_focus.".".$new_focus."_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix.$new_focus.".".$new_focus."_id = '$curpcnfo[creature_id]'") or die ("failed getting rank info 1.");
		$currrnknfo = mysql_fetch_assoc($current_rank_information);
		
		$update_minimum_rank = mysql_query("UPDATE ".$slrp_prefix.$new_focus." SET ".$new_focus."_min_rank = '$min_rank' WHERE ".$new_focus."_id = '$curpcnfo[creature_id]'") or die ("failed updating current min rank to view.");
		
		$verify_minimum_rank = mysql_query("SELECT * FROM ".$slrp_prefix.$new_focus." WHERE ".$new_focus."_min_rank = '$min_rank' AND ".$new_focus."_id = '$curpcnfo[creature_id]'") or die ("failed verifying updated min rank to view.");
		$vrmnrnk = mysql_fetch_assoc($verify_minimum_rank);
		$vrmnrnkcnt = mysql_num_rows($verify_minimum_rank);
		
		if($vrmnrnkcnt >= 1)
		{
			$get_rank_information = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix.$new_focus." ON ".$slrp_prefix.$new_focus.".".$new_focus."_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix.$new_focus.".".$new_focus."_id = '".$vrmnrnk[$new_focus.'_id']."'") or die ("failed getting rank info 2.");
			$gtrnknfo = mysql_fetch_assoc($get_rank_information);
			
			echo"
			<tr>
			<td colspan = '3' align = 'left' valign = 'top'>
			<font color = 'yellow' size = '2'>
			<li> <i>$curpcnfo[creature]</i> requires a minimum rank of <i>$gtrnknfo[slurp_rank]</i>
			
			</font>
			</td>
			</tr>
			";
		}
		
		if($vrmnrnkcnt == 0)
		{
			echo"
			<tr>
			<td colspan = '3' align = 'left' valign = 'top'>
			<font color = 'red' size = '2'>
			<li> <i>$curpcnfo[creature]</i> did not change its required rank to <i>$currrnknfo[slurp_rank]</i>
			
			</font>
			</td>
			</tr>
			";
		}
	}
}	
if(isset($_POST['created_item']))
{
	$created_item = $_POST['created_item'];
	$create_count = $_POST['create_count'];
	$create_count_original = $create_count;
	
	// echo"$created_item, cnt: $create_count<br>";
	
	$created_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$created_item'") or die ("failed getting created item info.");
	$critmnfo = mysql_fetch_assoc($created_item_info);
	
	$created_item_name = stripslashes(strip_tags($critmnfo[1]));
	
	$created_item_materials = mysql_query("SELECT * FROM ".$slrp_prefix."material INNER JOIN ".$slrp_prefix."item_core_material ON ".$slrp_prefix."item_core_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."item_core_material.item_id = '$created_item'") or die ("failed getting created item materials.");
	while($critmmats = mysql_fetch_assoc($created_item_materials))
	{
		$create_cost = ($critmmats[material_tier]*$create_count_original);
		
		$verify_expenditure = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$critmmats[material_id]'") or die ("failed verifying spent materials.");
		$vrexp = mysql_fetch_assoc($verify_expenditure);
		
		if($vrexp[creature_material_count] >= $create_cost)
		{
			$create_count--;
		}
	}
	
	if($create_count <= 0)
	{
		$enough_created_item_materials = mysql_query("SELECT * FROM ".$slrp_prefix."item_core_material WHERE ".$slrp_prefix."item_core_material.item_id = '$created_item'") or die ("failed getting created item materials.");
		$reason = "";
		while($enghcritmmats = mysql_fetch_assoc($enough_created_item_materials))
		{
			$recipe_create_cost = ($enghcritmmats[material_tier]*$create_count_original);
			// echo"$recipe_create_cost = $enghcritmmats[material_tier] * $create_count_original<br>";
			
			$subtract_materials_from_pc = mysql_query("UPDATE ".$slrp_prefix."creature_material SET creature_material_count=(creature_material_count-'$recipe_create_cost') WHERE material_id = '$enghcritmmats[material_id]' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed updating created material count.");
			$used_materials = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id = '$enghcritmmats[material_id]'") or die ("failed getting used materials.");
			$usdmat = mysql_fetch_assoc($used_materials);
			
			$reason = $reason."-$recipe_create_cost $usdmat[material]. ";
		}
		
		echo"
		<tr>
		<td colspan = '3' align = 'left' valign = 'top'>
		<font color = 'yellow' size = '2'>
		<li> <i>$curpcnfo[creature]</i> created $create_count_original $created_item_name(s)</i>.
		
		</font>
		</td>
		</tr>
		";
		
		$update_pc_items = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_item_count=(creature_item_count+'$create_count_original') WHERE item_id = '$created_item' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed updating created item count.");
		$xp_change = 0;
		$reason = $reason."Created $create_count_original $created_item_name.";
		
		$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','$xp_change','$usrnfo[user_id]','$reason')") or die ("failed adding character submission to xp log.");
	}
	
	if($create_count >= 1)
	{
		echo"
		<tr>
		<td colspan = '3' align = 'left' valign = 'top'>
		<font color = 'red' size = '2'>
		<li> <i>$curpcnfo[creature]</i> failed to create $create_count_original $created_item_name(s)</i>.
		
		</font>
		</td>
		</tr>
		";
	}
	
	$clean_up_leftovers = mysql_query("DELETE FROM ".$slrp_prefix."creature_material WHERE creature_material_count = '0'") or die ("failed deleting material stragglers.");
}

echo"
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan='3'>
	</td>
</tr> 
<tr background='themes/RedShores/images/base1.gif' height='24'>
	<form name = 'back_to_pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
	<td colspan ='3'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input class='submit3' type='submit' value='Back to View/Edit' name='back_to_pc_edit_new'>
	</td>
	</form>
</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
?>