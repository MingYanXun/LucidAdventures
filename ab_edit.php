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
}
// echo"abid: $current_ab_id<br>";


$get_focus = mysql_query("SELECT * FROM ".$slrp_prefix."focus WHERE focus_id = '2'") or die("failed to get posted focus.");
$getfoc = mysql_fetch_assoc($get_focus);

$get_focus_name = strip_tags(stripslashes($getfoc[focus]));

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	$col1_count = 9;
	$subcol1_count = 3;
	$subcol2_count = 5;
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			$col1_count = 3;
			$subcol1_count = 3;
			$subcol2_count = 5;
		}
	}
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] == 4)
	{
		$col1_count = 3;
		$subcol1_count = 3;
		$subcol2_count = 5;
	}
}

// echo"1cc: $col1_count, 1sc: $subcol1_count, 2sc: $subcol2_count<br>";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	$wide_align = "right";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			$wide_align = "right";
		}
	}
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] == 4)
	{
		$wide_align = "left";
	}
}

//the row that holds messages at the top

echo"
<tr>
	<td colspan = '$col1_count' align = 'left' valign = 'top'>
		<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
			<tr>
				<td colspan = '$col1_count' align = 'left' valign = 'top'>
					<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
";

// set new required effect types
if(isset($_POST['new_base_eff']))
{	
	$new_base_eff = $_POST['new_base_eff'];
	$new_base_eff_tier = $_POST['new_base_eff_tier'];
}

if(isset($_POST['newabname']))
{
	if(isset($_POST['ab_restr']))
	{
		$ab_restr = 1;
	}
	
	else
	{
		$ab_restr = 0;
	}
	
	$newabname = strip_tags(mysql_real_escape_string($_POST['newabname']));
	// echo"post: $ab_restr<br>$newabname<br>$new_base_eff<br>$new_base_eff_tier<br>";	

	$verify_existing_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]." = '$newabname'") or die ("failed to verify inserted subfocus.");
	$verexsbfoccnt = mysql_num_rows($verify_existing_subfocus);
	
	// if so, inform the user
	if($verexsbfoccnt >= 1)
	{	
		$verexsbfoc = mysql_fetch_assoc($verify_existing_subfocus);
		$serialized = $verexsbfoccnt+1;
		$existing_subfocus_name = strip_tags(stripslashes($verexsbfoc[$getfoc[focus_table]]));
		
		echo"
			<tr>
				<td align = 'left' valign = 'top'>
					<font color = 'orange' size = '2'>
					<li><i>$existing_subfocus_name</i> is already a(n) $get_focus_name, shown here.
				</td>
			</tr>
		";
		
		$current_ab_id = $verexsbfoc[$getfoc[focus_table].'_id'];
	}
	
	if($verexsbfoccnt == 0)
	{
		if(isset($_POST['copy_ab_id']))
		{
			$copy_ab_id = $_POST['copy_ab_id'];
			$copy_user_id = $_POST['copy_user_id'];
			$copy_ab_status = $_POST['copy_ab_status'];
			
			// echo"copy:id: $copy_ab_id<br>";
			
			$template_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$copy_ab_id'") or die("failed to get copied ab info");
			// $tmpltabnfo = mysql_fetch_assoc($template_ability_info);
			$tmpltabnfo = mysql_fetch_assoc($template_ability_info);
			$tmpltabnfocnt = mysql_num_rows($template_ability_info);
			
			$template_ability_name = stripslashes($tmpltabnfo[ability]);
			$template_ability_verbal = stripslashes($tmpltabnfo[ability_verbal]);
			$template_ability_desc = stripslashes($tmpltabnfo[ability_desc]);
			
			if($tmpltabnfo[ability_short] == "")
			{
				$template_ability_short = "None.";
			}
			else
			{
				$template_ability_short = stripslashes($tmpltabnfo[ability_short]);
			}
			
			if($tmpltabnfo[ability_desc] == "")
			{
				$template_ability_short_desc = "None.";
			}
			else
			{
				$template_ability_short_desc = stripslashes($tmpltabnfo[ability_desc]);
			}
			
			// echo"tmplt_ab_nm:$template_ability_name, new: $newabname<br>VERBAL: ".$tmpltabnfo[ability_verbal]."<br>DESC: $template_ability_desc<br>".$tmpltabnfo[ability_desc]."<br>";
			
			$copy_ability = mysql_query("INSERT INTO ".$slrp_prefix."ability (ability,ability_build_cost,ability_verbal,ability_desc,ability_modifier_id,ability_status_id,ability_slurp_id,ability_requires_ability_id,ability_restricted,ability_tier,ability_capacity,ability_min_rank,ability_unlimited_uses,ability_short,ability_short_desc,ability_set_id,ability_library_status,ability_owner_id) VALUES ('$newabname','$tmpltabnfo[ability_build_cost]','$template_ability_verbal','$template_ability_desc','$tmpltabnfo[ability_modifier_id]','2','$slrpnfo[slurp_id]','$tmpltabnfo[ability_requires_ability_id]','$tmpltabnfo[ability_restricted]','$tmpltabnfo[ability_tier]','$tmpltabnfo[ability_capacity]','5','$tmpltabnfo[ability_unlimited_uses]','$template_ability_short','$template_ability_short_desc','$tmpltabnfo[ability_set_id]','2','$usrnfo[user_id]')") or die("failed to copy ab template info.");
			
			$started_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability = '$newabname' AND ability_library_status = '2' AND ability_min_rank = '5' AND ability_owner_id = '$usrnfo[user_id]'") or die("failed to get started ab info.");
			// $strtabnfo = mysql_fetch_assoc($started_ability_info);
			$strtabnfo = mysql_fetch_assoc($started_ability_info);
			$strtabnfocnt = mysql_num_rows($started_ability_info);
			
			$started_ab_info = stripslashes($strtabnfo[ability]);
			
			if($strtabnfocnt == 1)
			{
				echo"
				<tr>
				
				<td colspan = '7' valign = 'top' align = 'left'>
				<font color = 'yellow' size = '2'>
				<li> <i>$started_ab_info</i> is now an Ability in the world of ".$slrpnfo[slurp_name].".
				</td>
				
				</tr>
				";
				
				$template_ability_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '".$tmpltabnfo[ability_id]."' AND attribute_type_id > '1' AND ability_cost > '0'") or die ("failed getting template ab cost.");
				$tmpltabcst = mysql_fetch_assoc($template_ability_cost);
				
				$copy_ab_cost = mysql_query("INSERT INTO ".$slrp_prefix."ability_cost (ability_cost,ability_id,attribute_type_id,ability_cost_tier) VALUES ('".$tmpltabcst[ability_cost_id]."','".$strtabnfo[ability_id]."','".$tmpltabcst[attribute_type_id]."','".$tmpltabcst[ability_cost_tier]."')") or die ("failed inserting new ab cost.");
				
				$verify_existing_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '".$strtabnfo[ability_id]."' AND attribute_type_id = '".$tmpltabcst[attribute_type_id]."' AND ability_cost = '".$tmpltabcst[ability_cost_tier]."'") or die ("failed verifying ability existing cost.");
				$verexcst = mysql_fetch_assoc($verify_existing_cost);
				$verexcstcnt = mysql_num_rows($verify_existing_cost);
				
				if($verexcstcnt == 1)
				{
					echo"
					<tr>
					
					<td colspan = '7' valign = 'top' align = 'left'>
					<font color = 'yellow' size = '2'>
					<li> <i>$template_ability_name</i>'s cost was copied.
					</td>
					
					</tr>
					";
				}
				
				if($verexcstcnt == 0)
				{
					echo"
					<tr>
					
					<td colspan = '7' valign = 'top' align = 'left'>
					<font color = 'yellow' size = '2'>
					<li> <i>$template_ability_name</i>'s cost was not copied.
					<br>
					<br>
					Please try again or contact an Admin.
					</td>
					
					</tr>
					";
				}
				
				$get_template_effect_types = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '".$tmpltabnfo[ability_id]."' ORDER BY effect_type_id");
				$tmpltefftypcnt = mysql_num_rows($get_template_effect_types);
				// while($tmpltefftyp = mysql_fetch_assoc($get_template_effect_types))
				while($tmpltefftyp = mysql_fetch_assoc($get_template_effect_types))
				{
					$copied_eff_typ_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id = '".$tmpltefftyp[effect_type_id]."'") or die("failed to get copied attr info");
					$cdefftypnfo = mysql_fetch_assoc($copied_eff_typ_info);
					$cdefftypnfocnt = mysql_num_rows($copied_eff_typ_info);
					
					$copied_eff_typ_name = stripslashes($cdefftypnfo[effect_type]);
					
					$copy_template_eff_types = mysql_query("INSERT INTO ".$slrp_prefix."ability_effect_type (ability_id,effect_type_id,effect_type_tier) VALUES ('".$strtabnfo[ability_id]."','".$tmpltefftyp[effect_type_id]."','".$tmpltefftyp[effect_type_tier]."')") or die ("failed copying template effect types.");
					
					$verify_copied_eff_types = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '".$strtabnfo[ability_id]."' AND effect_type_id = '".$tmpltefftyp[effect_type_id]."' AND effect_type_tier = '".$tmpltefftyp[effect_type_tier]."'") or die ("failed verifying copied ab chars.");
					$vrcpdefftypscnt = mysql_num_rows($verify_copied_eff_types);
					// $vrcpdefftyps = mysql_fetch_assoc($verify_copied_eff_types);
					$vrcpdefftyps = mysql_fetch_assoc($verify_copied_eff_types);
					
					if($vrcpdefftypscnt == 1)
					{
						echo"
						<tr>
						
						<td colspan = '7' valign = 'top' align = 'left'>
						<font color = 'yellow' size = '2'>
						<li> <i>$started_ab_info</i> now has $copied_eff_typ_name ".$vrcpdefftyps[effect_type_tier].".
						</td>
						
						</tr>
						";
					}
					
					if($vrcpdefftypscnt == 0)
					{
						echo"
						<tr>
						
						<td colspan = '7' valign = 'top' align = 'left'>
						<font color = 'yellow' size = '2'>
						<li> <i>$started_ab_info</i> did not copy $template_ability_name's $copied_eff_typ_name.
						<br>
						<br>
						Please try again or contact an Admin.
						</td>
						
						</tr>
						";
					}
				}
				
				$get_template_modifiers = mysql_query("SELECT * FROM ".$slrp_prefix."ability_ability_modifier WHERE ability_id = '".$tmpltabnfo[ability_id]."'");
				$tmpltmodcnt = mysql_num_rows($get_template_modifiers);
				// echo"templt mod cnt: $tmpltmodcnt<br>";
				
				while($tmpltmod = mysql_fetch_assoc($get_template_modifiers))
				{	
					// echo"copied mod id: $tmpltmod[0]<br>";
					$copied_modifier_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '".$tmpltmod[ability_modifier_id]."'") or die("failed to get copied mod info");
					$cdmodnfo = mysql_fetch_assoc($copied_modifier_info);
					$cdmodnfocnt = mysql_num_rows($copied_modifier_info);
					
					$copied_modifier_name = stripslashes($cdmodnfo[ability_modifier_short]);
					// echo"mod name: $copied_modifier_name<br>";
					
					$copy_template_modifiers = mysql_query("INSERT INTO ".$slrp_prefix."ability_ability_modifier (ability_id,ability_modifier_id,ability_effect_id) VALUES ('".$strtabnfo[ability_id]."','".$tmpltmod[ability_modifier_id]."','".$tmpltmod[ability_effect_id]."')") or die ("failed copying template modifiers.");
					
					$verify_copied_modifiers = mysql_query("SELECT * FROM ".$slrp_prefix."ability_ability_modifier WHERE ability_id = '".$strtabnfo[ability_id]."' AND ability_modifier_id = '".$tmpltmod[ability_modifier_id]."' AND ability_effect_id = '".$tmpltmod[ability_effect_id]."' ORDER BY ability_modifier_id") or die ("failed verifying copied mods.");
					$vrcpdmodcnt = mysql_num_rows($verify_copied_modifiers);
					$vrcpdmod = mysql_fetch_assoc($verify_copied_modifiers);
					
					// echo"verifed mod id: $vrcpdmod[0]<br>";
					
					if($vrcpdmodcnt >= 1)
					{
						echo"
						<tr>
						
						<td colspan = '7' valign = 'top' align = 'left'>
						<font color = 'yellow' size = '2'>
						<li> <i>$started_ab_info</i> is now modified by $copied_modifier_name.
						</td>
						
						</tr>
						";
					}
					
					if($vrcpdmodcnt == 0)
					{
						echo"
						<tr>
						
						<td colspan = '7' valign = 'top' align = 'left'>
						<font color = 'yellow' size = '2'>
						<li> <i>$started_ab_info</i> is not modified by $copied_modifier_name.
						<br>
						<br>
						Please try again or contact an Admin.
						</td>
						
						</tr>
						";
					}
				}
				
				$get_template_effects = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect WHERE ability_id = '".$tmpltabnfo[ability_id]."'");
				$tmplteffcnt = mysql_num_rows($get_template_effects);
				// echo"templt eff cnt: $tmplteffcnt<br>";
				
				while($tmplteff = mysql_fetch_assoc($get_template_effects))
				{	
					// echo"copied eff id: $tmplteff[0]<br>";
					$copied_effect_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '".$tmplteff[effect_id]."'") or die("failed to get copied eff info");
					$cdeffnfo = mysql_fetch_assoc($copied_effect_info);
					$cdeffnfocnt = mysql_num_rows($copied_effect_info);
					
					$copied_effect_name = stripslashes($cdeffnfo[effect]);
					// echo"eff name: $copied_effect_name<br>";
					
					$copy_template_effects = mysql_query("INSERT INTO ".$slrp_prefix."ability_effect (ability_id,effect_id,effect_type_id,effect_modifier_id,focus_id,slurp_id) VALUES ('".$strtabnfo[ability_id]."','".$tmplteff[effect_id]."','".$tmplteff[effect_type_id]."','".$tmplteff[effect_modifier_id]."','".$tmplteff[focus_id]."','".$tmplteff[slurp_id]."')") or die ("failed copying template effects.");
					
					$verify_copied_effects = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect WHERE ability_id = '".$strtabnfo[ability_id]."' AND effect_id = '".$tmplteff[effect_id]."' AND effect_type_id = '".$tmplteff[effect_type_id]."' AND effect_modifier_id = '".$tmplteff[effect_modifier_id]."' AND focus_id = '".$tmplteff[focus_id]."' AND slurp_id = '".$tmplteff[slurp_id]."' ORDER BY effect_id") or die ("failed verifying copied effs.");
					$vrcpdeffcnt = mysql_num_rows($verify_copied_effects);
					$vrcpdeff = mysql_fetch_assoc($verify_copied_effects);
					
					// echo"verifed eff id: $vrcpdeff[0]<br>";
					
					if($vrcpdeffcnt >= 1)
					{
						echo"
						<tr>
						
						<td colspan = '7' valign = 'top' align = 'left'>
						<font color = 'yellow' size = '2'>
						<li> <i>$started_ab_info</i> now uses $copied_effect_name.
						</td>
						
						</tr>
						";
					}
					
					if($vrcpdeffcnt == 0)
					{
						echo"
						<tr>
						
						<td colspan = '7' valign = 'top' align = 'left'>
						<font color = 'yellow' size = '2'>
						<li> <i>$started_ab_info</i> does not use $copied_effect_name.
						<br>
						<br>
						Please try again or contact an Admin.
						</td>
						
						</tr>
						";
					}
				}
				
				$current_ab_id = $strtabnfo[ability_id];
			}
			
			if($strtabnfocnt == 0)
			{
				echo"
				<tr>
				
				<td colspan = '7' valign = 'top' align = 'left'>
				<font color = 'yellow' size = '2'>
				<li> <i>$newabname</i> didn't make it to the world of ".$slrpnfo[slurp_name].".
				<li> Displaying <i>$template_ability_name</i> again instead.
				<br>
				<br>
				Please try again or contact an Admin.
				</td>
				
				</tr>
				";
				
				$current_ab_id = $tmpltabnfo[ability_id];
			}
		}
		
		if(empty($_POST['copy_ab_id']))
		{
			// insert and verify
			// for everything but creatures and object types, follow the default insert of just a name for now
			$insert_new_ability = mysql_query("INSERT INTO ".$slrp_prefix."ability (ability,ability_status_id,ability_restricted,ability_slurp_id,ability_owner_id,ability_library_status) VALUES ('$newabname','2','$ab_restr','".$slrpnfo[slurp_id]."','$usrnfo[user_id]','2')") or die ("failed inserting new req char.");
			
			$verify_new_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability = '$newabname' AND ability_library_status = '2' AND ability_restricted = '$ab_restr' AND ability_slurp_id = '".$slrpnfo[slurp_id]."'") or die ("failed verifying new ability.");
			// $vernewabnfo = mysql_fetch_assoc($verify_new_ability_info);
			$vernewabnfo = mysql_fetch_assoc($verify_new_ability_info);
			$vernewabnfocnt = mysql_num_rows($verify_new_ability_info);
			
			// echo"ver: $vernewabnfo[1]<br>";
			
			$current_ab_id = $vernewabnfo[ability_id];
		}
		
		// verify the record
		$verify_inserted_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_id = '$current_ab_id'") or die ("failed to verify inserted subfocus 43.");
		// $verinssbfoc = mysql_fetch_assoc($verify_inserted_subfocus);
		$verinssbfoc = mysql_fetch_assoc($verify_inserted_subfocus);
		$verinssbfoccnt = mysql_num_rows($verify_inserted_subfocus);
		$verify_inserted_sub = stripslashes($verinssbfoc[$getfoc[focus_table]]);
		
		// echo"<font size = '12'>$current_ab_id, $verinssbfoccnt<br></font>";
		
		//because the tier columns are not standard, get it specifically
		$verify_inserted_subfocus_tier = mysql_query("SELECT ".$getfoc[focus_table]."_tier FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_id = '".$verinssbfoc[$getfoc[focus_table].'_id']."'") or die ("failed to verify inserted subfocus tier again 1.");
		$verinssbfoctr = mysql_fetch_assoc($verify_inserted_subfocus_tier);
		
		// if it inserted correctly, offer a button to refresh the page for that object, since it split in X objects by rating
		if($verinssbfoccnt >= 1)
		{
			// get the verbage for the qualifier
			$get_focus_exclusion = mysql_query("SELECT * FROM ".$slrp_prefix."focus_exclusion WHERE focus_id = '".$getfoc[focus_id]."' ORDER BY focus_exclusion") or die ("failed to get focus exclusion 27.");
			$gtfcexcnt = mysql_num_rows($get_focus_exclusion);
			
			// while($gtfcex = mysql_fetch_assoc($get_focus_exclusion))
			while($gtfcex = mysql_fetch_assoc($get_focus_exclusion))
			{
				// start setting values. Based on the Inverted property, different numeric handlers.
				
				$thing_weight = $getfoc[focus_weight];
				$thing_level = $getfoc[focus_level];
				
				//echo" -WT<br>";
				$new_subfocus_value = -($thing_weight);
				
				// //echo"2 WT: $thing_weight ($getfoc[4])<br>LVL: $thing_level<br>TR: $thing_tier<br>MAX: $thing_max_tier<br>";
				// echo"INV: ".$gtfcex[focus_inverted]."<br>TOT:$new_subfocus_value<br>";
				
				// compile the text strings
				$old_modifier_short = mysql_real_escape_string($gtfcex[focus_exclusion]." ".$existing_sub);
				$new_subfocus_short = mysql_real_escape_string($gtfcex[focus_exclusion]." ".$verify_inserted_sub);
				$new_subfocus_modifier = ($new_subfocus_short.".");
				
				// insert or update the new modifier
				$verify_correct_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_type_id = '".$gtfcex[focus_ability_modifier_type_id]."' AND ability_modifier_value = '$new_subfocus_value' AND ability_modifier = '$new_subfocus_modifier' AND ability_modifier_short = '$new_subfocus_short'") or die ("failed verifying correct ".$getfoc[focus_table]." subtype 23bb.");
				$vercrctmodcnt = mysql_num_rows($verify_correct_modifier);
				
				// if already correct, leave it alone
				if($vercrctmodcnt == 1)
				{
					// sweet.
				}
				
				if($vercrctmodcnt == 0)
				{
					$verify_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_short = '$old_modifier_short'") or die ("failed verifying incorrect ".$getfoc[focus_table]." subtype 23bb.");
					// $vermod = mysql_fetch_assoc($verify_modifier);
					$vermod = mysql_fetch_assoc($verify_modifier);
					$vermodcnt = mysql_num_rows($verify_modifier);
					
					if($vermodcnt == 1)
					{
						$update_modifier_info = mysql_query("UPDATE ".$slrp_prefix."ability_modifier SET ability_modifier_type_id = '".$gtfcex[focus_ability_modifier_type_id]."', ability_modifier_value = '$new_subfocus_value', ability_modifier = '$new_subfocus_modifier', ability_modifier_short = '$new_subfocus_short' WHERE ability_modifier_id = '".$vermod[ability_modifier_id]."'") or die ("failed updating new ".$getfoc[focus_table]." mod value 23c.");
					}
					
					if($vermodcnt == 0)
					{
						$insert_modifier_info = mysql_query("INSERT INTO ".$slrp_prefix."ability_modifier (ability_modifier_type_id,ability_modifier_value,ability_modifier,ability_modifier_short) VALUES ('".$gtfcex[focus_ability_modifier_type_id]."','$new_subfocus_value','$new_subfocus_modifier','$new_subfocus_short')") or die ("failed inserting new ".$getfoc[focus_table]." subtype relation 23c.");
					}
				}
				
				// verify if made it in
				$verify_new_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_type_id = '".$gtfcex[focus_ability_modifier_type_id]."' AND ability_modifier_value = '$new_subfocus_value' AND ability_modifier = '$new_subfocus_modifier' AND ability_modifier_short = '$new_subfocus_short'") or die ("failed verifying new inserted ".$getfoc[focus_table]." subtype 23c.");
				$vernewmodcnt = mysql_num_rows($verify_new_modifier);
				// $vernewmod = mysql_fetch_assoc($verify_new_modifier);
				$vernewmod = mysql_fetch_assoc($verify_new_modifier);
				
				// if so, inform the user.
				if($vernewmodcnt >= 1)
				{
				//	if($verbose == 1)
				//	{
						echo"
						<tr>
						
						<td colspan = '7' valign = 'top' align = 'left'>
						<font color = 'yellow' size = '2'>
						<li> <i>$new_subfocus_short</i> added to modifiers.
						</td>
						
						</tr>
						";
				//	}
					
					$verify_modsub = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_id = '".$vernewmod[ability_modifier_id]."' AND subfocus_id = '".$verinssbfoc[$getfoc[focus_table].'_id']."' AND focus_id = '2' AND focus_exclusion_id = '".$gtfcex[focus_exclusion_id]."'") or die ("failed verifying inserted new mod subfocus 00.");
					$vermodsubcnt = mysql_num_rows($verify_modsub);
					// $vermodsub = mysql_fetch_assoc($verify_modsub);
					$vermodsub = mysql_fetch_assoc($verify_modsub);
					
					if($vermodsubcnt == 1)
					{
						$update_new_modifier_subfocus = mysql_query("UPDATE ".$slrp_prefix."ability_modifier_subfocus SET subfocus_id = '".$verinssbfoc[$getfoc[focus_table].'_id']."',focus_id = '2',focus_exclusion_id = '".$gtfcex[focus_exclusion_id]."' WHERE ability_modifier_subfocus_id = '".$vermodsub[ability_modifier_subfocus_id]."'") or die ("failed to insert new mod subfocus 00.");
					}
					
					if($vermodsubcnt == 0)
					{
						$insert_new_modifier_subfocus = mysql_query("INSERT INTO ".$slrp_prefix."ability_modifier_subfocus (ability_modifier_id,subfocus_id,focus_id,focus_exclusion_id) VALUES ('".$vernewmod[ability_modifier_id]."','".$verinssbfoc[$getfoc[focus_table].'_id']."','2','".$gtfcex[focus_exclusion_id]."')") or die ("failed to insert new mod subfocus 00.");
					}
					
					$verify_new_modsub = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_id = '".$vernewmod[ability_modifier_id]."' AND subfocus_id = '".$verinssbfoc[$getfoc[focus_table].'_id']."' AND focus_id = '2' AND focus_exclusion_id = '".$gtfcex[focus_exclusion_id]."'") or die ("failed verifying inserted new mod subfocus 00.");
					$vernewmodsubcnt = mysql_num_rows($verify_new_modsub);
					$vernewmodsub = mysql_fetch_assoc($verify_new_modsub);
					
					// let them know if it made it or not
					if($vernewmodsubcnt >= 1)
					{
					//	if($verbose == 1)
					//	{
							echo"
							<tr>
							
							<td colspan = '7' valign = 'top' align = 'left'>
							<font color = 'yellow' size = '2'>
							<li> Subfocus <i>$verify_inserted_sub</i> added to <i>$new_subfocus_short</i>.
							</td>
							
							</tr>
							";
					//	}
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
					<li> <i>".$vernewmod[ability_modifier_short]."</i> was not added to modifiers. Please try again or contact an admin if there is a problem.
					</td>
					
					</tr>
					";
				}
			}
		}
		
		if($verinssbfoccnt == 0)
		{
			echo"
			<tr>
			<td colspan = '7' align = 'left' valign = 'top'>
			<font color = 'red' size = '2'>
			<li> <i>$newabname</i> is not a(n) <i>".$getfoc[focus]."</i>. Please try again or contact an admin if there is a problem.
			</td>
			</tr>
			";
			
		}
		
		if(empty($_POST['copy_ab_id']))
		{
			$insert_base_effect_type = mysql_query("INSERT INTO ".$slrp_prefix."ability_effect_type (ability_id,effect_type_id,effect_type_tier) VALUES ('$current_ab_id','$new_base_eff','$new_base_eff_tier')") or die ("failed inserting base effect type.");
			
			$get_support = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_support = '1'") or die("failed getting insert ab support.");
			// while($getsprt = mysql_fetch_assoc($get_support))
			while($getsprt = mysql_fetch_assoc($get_support))
			{
				// echo"supp: $getsprt[1]<br>";
				$insert_support_defaults = mysql_query("INSERT INTO ".$slrp_prefix."ability_effect_type (ability_id,effect_type_id,effect_type_tier) VALUES ('$current_ab_id','".$getsprt[effect_type_id]."','".$slrpnfo[slurp_support_effect_type_min]."')") or die ("failed inserting support default.");
			}
		}
	}
}
if(isset($_POST['newabbook']))
{
	$newabbook = $_POST['newabbook'];
	$newbookab = $_POST['current_ab_id'];
	//  echo"posted: item+sub: $newabbook, ab:$newbookab<br>";
	
	$get_all_book_subtypes = mysql_query("SELECT * FROM ".$slrp_prefix."item_subtype WHERE item_subtype_id >= '89' AND item_subtype_id <= '93'") or die ("failed getting all book subtypes.");
	while($gtallbksubs = mysql_fetch_assoc($get_all_book_subtypes))
	{
		$get_all_books = mysql_query("SELECT * FROM ".$slrp_prefix."item INNER JOIN ".$slrp_prefix."item_item_subtype ON ".$slrp_prefix."item.item_id = ".$slrp_prefix."item_item_subtype.item_id WHERE ".$slrp_prefix."item.item_id > '1' AND ".$slrp_prefix."item_item_subtype.item_subtype_id = '$gtallbksubs[item_subtype_id]'") or die ("failed getting all books.");
		while($gtallbks = mysql_fetch_assoc($get_all_books))
		{
			$newabbk = $gtallbks[item_id]."_".$gtallbksubs[item_subtype_id];
			// echo"composite = posted: $newabbk = $newabbook<br>";
			if($newabbk == $_POST['newabbook'])
			{
				// $newbookrand = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$gtallbks[item_id]' AND object_focus_id = '$gtallbksubs[item_subtype_id]'") or die ("failed getting new book rand.");
				$newbookrand = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$newbookab' AND object_focus_id = '2' ORDER BY RAND() LIMIT 1") or die ("failed getting new book rand.");
				$nwbkrnd = mysql_fetch_assoc($newbookrand);
				$bkrnd_entry = $nwbkrnd[object_random_id];
				// echo"item:$gtallbks[item_id], ab: $current_ab_id, rnd: $bkrnd_entry<br>";
				
				$new_book_entry = mysql_query("INSERT INTO ".$slrp_prefix."item_book (item_id,ability_id,object_random_id) VALUES ('$gtallbks[item_id]','$current_ab_id','$bkrnd_entry')") or die ("failed inserting new book.");
			}
		}
	}
}

$del_item_book = mysql_query("SELECT * FROM ".$slrp_prefix."item_book WHERE item_book_id > '1'");
while($delitembk = mysql_fetch_assoc($del_item_book))
{
	if(isset($_POST['del_book_item_'.$delitembk[item_book_id]]))
	{
		$delbookitem = $_POST['del_book_item_'.$delitembk[item_book_id]];
		// echo"DEL_BOOK_ID: $delbookitem<br>";
		$delete_book_item = mysql_query("DELETE FROM ".$slrp_prefix."item_book WHERE item_book_id = '$delbookitem'") or die ("failed deleting book item.");
	}
}
// echo"curab: $current_ab_id<br>";
// get info on the ability
$get_pc_abilities2 = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$current_ab_id'");
// $curab2 = mysql_fetch_assoc($get_pc_abilities2);
$curab2 = mysql_fetch_assoc($get_pc_abilities2);

$get_ability_creator = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$curab2[ability_owner_id]'");
// $curab2 = mysql_fetch_assoc($get_pc_abilities2);
$curabcreator = mysql_fetch_assoc($get_ability_creator);

$current_ab_name = strip_tags(stripslashes($curab2[ability]));
$current_ab_verbal = strip_tags(stripslashes($curab2[ability_verbal]));
$current_ab_desc = strip_tags(stripslashes($curab2[ability_desc]));

$current_ability2_owned = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id='$current_ab_id' AND creature_id = '".$curpcnfo[creature_id]."'");
//$currab2own = mysql_fetch_assoc($current_ability2_owned);
$currab2owncnt = mysql_num_rows($current_ability2_owned);

$object_short = strip_tags(stripslashes($curab2[ability_short]));
$object_short_desc = strip_tags(stripslashes($curab2[ability_short_desc]));
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
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<form name = 'ab_info_edit' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"<form name = 'ab_info_edit' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>";
		}
	}
}

echo"
	<td align = 'left' valign = 'top' width = '83%'>
		<table cellpadding='0' cellspacing='0' border='0' bordercolor='red' width = '100%'>
			<tr>
				<td colspan = '$col1_count' align = '$left' valign = 'top' width='59%'>
					<table cellpadding='0' cellspacing='0' border='0' bordercolor='blue' width = '100%'>
						<tr>
							<td colspan = '$subcol1_count' align = '$wide_align' valign = 'top' width='100%'>";

// start the main info boxes and form

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($currab2owncnt >= 1)
	{
		echo"<font size = '4' color = 'yellow'>";
	}

	if($currab2owncnt == 0)
	{
		echo"<font size = '4' color = '#7fffd4'>";
	}
}

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	// OpenTable2();
	// graphic handler for all objects
	$dressed = 0;
	$current_focus_id = $getfoc[focus_id];
	$current_object_id = $current_ab_id;
	include("modules/$module_name/includes/fm_obj_graphic.php");
	
	// approved bilities for rank 5 and above are handled below: search on 'grphcsbfc'
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			// OpenTable2();
			// graphic handler for all objects
			$current_focus_id = $getfoc[focus_id];
			$current_object_id = $current_ab_id;
			include("modules/$module_name/includes/fm_obj_graphic.php");
		}
	}
}


echo"						</font>
							</td>
						</tr>
						<tr height='24'>							
							<td colspan = '$subcol1_count' align = '$wide_align' valign = 'middle' width='100%'>
								<font class='heading2'>";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<textarea class = 'textbox' name ='ab_name' rows = '5' cols = '50'>";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"<textarea class = 'textbox' name ='ab_name' rows = '5' cols = '50'>";
		}
	}
}

echo"$current_ab_name";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"</textarea>";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"</textarea>";
		}
	}
}

echo"</font>
							</td>
						</tr>
						<tr height='9'>
							<td colspan='3'>
							</td>
						</tr>
						<tr>
							<td colspan = '$subcol1_count' align = '$wide_align' valign = 'top' width='100%'>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<font class='heading2'>[Ability #$current_ab_id created by $curabcreator[username]]</font><br><br>";
	echo"<font class='heading1'>Verbal Component: </font> <i>";
	echo"<textarea class = 'textbox' name ='ab_verbal' rows = '5' cols = '50'>";
	echo"$current_ab_verbal";
	echo"</textarea>";
	echo"</i><br><br><font class='heading1'>Description: </font><br> ";
	echo"<textarea class = 'textbox' name ='ab_desc' rows = '5' cols = '50'>";
	echo"$current_ab_desc";
	echo"</textarea>";
	echo"<br><br><font color = 'red'>Restricted? <input type='checkbox' value='1' name='ab_restricted'";
	
	if($curab2[ability_restricted] == 1)
	{
		echo"checked";
	}

	echo"></font>";
	echo"<br><br>";
}

// unapproved abilities are visible to their creators
if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"<font class='heading2'>[Ability #$current_ab_id created by $curabcreator[username]]</font><br><br>";
			echo"<font class='heading1'>Verbal Component: </font> <i>";
			echo"<textarea class = 'textbox' name ='ab_verbal' rows = '5' cols = '50'>";
			echo"$current_ab_verbal";
			echo"</textarea>";
			echo"</i><br><br><font class='heading1'>Description: </font> ";
			echo"<textarea class = 'textbox' name ='ab_desc' rows = '5' cols = '50'>";
			echo"$current_ab_desc";
			echo"</textarea>";
			echo"<br><br><font color = 'red'>Restricted? <input type='checkbox' value='1' name='ab_restricted'";
			
			if($curab2[ability_restricted] == 1)
			{
				echo"checked";
			}
		
			echo"></font>";
			echo"<br><br>";
		}
	}
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] >= 4)
	{
		if($curab2[ability_library_status] <= 5)
		{
			// echo"<font class='heading2'>[Ability #$current_ab_id created by $curabcreator[username]]</font>";
			echo"<font class='heading1'>Verbal Component: <i>";
			echo"<font size = '2' color = 'orange'>$current_ab_verbal</font> </i>";
			echo"<br><br>Description:  ";
			echo"<font size = '2' color = 'orange'>$current_ab_desc</font> </i>";
		}
	
		echo"<br><br>";
	}
}

$get_current_ability_set = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id = '$curab2[ability_set_id]'") or die ("failed getting current ab set.");
$gtcurrabsetcnt = mysql_num_rows($get_current_ability_set);
$gtcurrabset = mysql_fetch_assoc($get_current_ability_set);

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	$get_ability_set = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id NOT IN (SELECT ability_set_id FROM ".$slrp_prefix."ability_set WHERE ability_set_id = '$gtcurrabset[ability_set_id]') ORDER BY ability_set") or die ("failed getting ability_set.");
	$gtabsetcnt = mysql_num_rows($get_ability_set);
	echo"<font class='heading1'>Choose Ability Set:</font> . . . <select class='engine' name = 'add_ab_set'>";
	echo"<option value = '$gtcurrabset[ability_set_id]'>$gtcurrabset[ability_set]</option>";
	echo"<option value = '1'>None</option>";
	
	while($gtabset = mysql_fetch_assoc($get_ability_set))
	{
		echo"<option value = '$gtabset[ability_set_id]'>$gtabset[ability_set]</option>";
	}
		
	echo"</select><br><br>";
	
	if($gtcurrabset[ability_set] == "Craft" OR $gtcurrabset[ability_set] == "Racial")
	{
		$get_current_ability_tokens = mysql_query("SELECT * FROM ".$slrp_prefix."ability_tokens WHERE ability_id = '$curab2[ability_id]'") or die ("failed getting current ab tokens.");
		$gtcurrabtkns = mysql_fetch_assoc($get_current_ability_tokens);
		
		echo"<font class='heading1'>Blue</font>: <select class='engine' name = 'blue_tokens'>";
		echo"<option value = '$gtcurrabtkns[ability_tokens_1]'>$gtcurrabtkns[ability_tokens_1]</option>";
		$base_token_count = (-10);
		while($base_token_count <= 10)
		{
			echo"<option value = '$base_token_count'>$base_token_count</option>";
			$base_token_count++;
		}
		echo"</select> &nbsp;&nbsp;&nbsp;";
		echo"<font class='heading1'>White</font>: <select class='engine' name = 'white_tokens'>";
		echo"<option value = '$gtcurrabtkns[ability_tokens_2]'>$gtcurrabtkns[ability_tokens_2]</option>";
		$base_token_count = (-10);
		while($base_token_count <= 10)
		{
			echo"<option value = '$base_token_count'>$base_token_count</option>";
			$base_token_count++;
		}
		echo"</select> &nbsp;&nbsp;&nbsp;";
		echo"<font class='heading1'>Red</font>: <select class='engine' name = 'red_tokens'>";
		echo"<option value = '$gtcurrabtkns[ability_tokens_3]'>$gtcurrabtkns[ability_tokens_3]</option>";
		$base_token_count = (-10);
		while($base_token_count <= 10)
		{
			echo"<option value = '$base_token_count'>$base_token_count</option>";
			$base_token_count++;
		}
		echo"</select> &nbsp;&nbsp;&nbsp;";
		echo"<font class='heading1'>Black</font>: <select class='engine' name = 'black_tokens'>";
		echo"<option value = '$gtcurrabtkns[ability_tokens_4]'>$gtcurrabtkns[ability_tokens_4]</option>";
		$base_token_count = (-10);
		while($base_token_count <= 10)
		{
			echo"<option value = '$base_token_count'>$base_token_count</option>";
			$base_token_count++;
		}
		echo"</select><br><br>";
	}
	
	echo"<font class='heading1'>Set Build Cost:</font> . . . <select class='engine' name = 'add_ab_cost'>";
	echo"<option value = '$curab2[ability_build_cost]'>$curab2[ability_build_cost]</option>";
	$build_cost = 0;
	while($build_cost <= 25)
	{
		echo"<option value = '$build_cost'>$build_cost</option>";
		$build_cost++;
	}
	echo"</select><br><br>";
	
	echo"<font class='heading1'>Purchase Limit:</font> . . . <select class='engine' name = 'add_ab_count_max'>";
	echo"<option value = '$curab2[ability_count_max]'>$curab2[ability_count_max]</option>";
	$purchase_limit = 0;
	while($purchase_limit <= 100)
	{
		echo"<option value = '$purchase_limit'>$purchase_limit</option>";
		
		if($purchase_limit >= 25)
		{
			$purchase_limit++;
			$purchase_limit++;
			$purchase_limit++;
			$purchase_limit++;
		}
		$purchase_limit++;
	}
	echo"</select><br><br>";
	
	
	$pc_level_option = floor(($curab2[ability_xp_min]-25)/10);
	if($pc_level_option <= 0)
	{
		$pc_level_option = 0;
	}
	echo"<font class='heading1'>Level/Build Minimum:</font> . . . <select class='engine' name = 'add_ab_xp_min'>";
	echo"<option value = '$curab2[ability_xp_min]'>$pc_level_option / $curab2[ability_xp_min]</option>";
	$ability_xp_min = 35;
	while($ability_xp_min <= 250)
	{
		$ab_level_option = floor(($ability_xp_min-25)/10);
		if($ab_level_option <= 0)
		{
			$ab_level_option = 0;
		}
		echo"<option value = '$ability_xp_min'>$ab_level_option / $ability_xp_min</option>";
		
		$ability_xp_min++;
		$ability_xp_min++;
		$ability_xp_min++;
		$ability_xp_min++;
		$ability_xp_min++;
	}
	echo"</select><br><br>";
	
	echo"<font class='heading1'>Non-Domain Build Minimum:</font> . . . <select class='engine' name = 'add_ab_sp_xp_min'>";
	echo"<option value = '$curab2[ability_special_xp_min]'>$curab2[ability_special_xp_min]</option>";
	$ability_sp_xp_min = 75;
	while($ability_sp_xp_min <= 250)
	{
		echo"<option value = '$ability_sp_xp_min'>$ability_sp_xp_min</option>";
		
		if($ability_sp_xp_min >= 75)
		{
			$ability_sp_xp_min++;
			$ability_sp_xp_min++;
			$ability_sp_xp_min++;
			$ability_sp_xp_min++;
		}
		$ability_sp_xp_min++;
	}
	echo"</select><br><br>";
	
	if($gtcurrabset[ability_set] == "Advanced Arts")
	{
		echo"<table cellpadding='0' cellspacing='0' border='0' width='100%' align='right'>";
		echo"<tr>";
		echo"<td width='100%' align = 'right' valign='middle' colspan='9'>";
		echo"Domain Ability Count Minimums";
		echo"</td>";
		echo"</tr>";
		echo"<tr>";
		echo"<td align = 'right' valign='middle'>";
		echo"<font class='heading1'>Burn</font>:<br><select class='engine' name = 'ability_set_min_1'>";
		echo"<option value = '$curab2[ability_set_min_1]'>$curab2[ability_set_min_1]</option>";
		$base_set_min_count = 1;
		while($base_set_min_count <= 10)
		{
			echo"<option value = '$base_set_min_count'>$base_set_min_count</option>";
			$base_set_min_count++;
		}
		echo"</select>";
		echo"</td>";
		echo"<td width = '2%'>";
		echo"&nbsp;";
		echo"</td>";
		echo"<td align = 'right' valign='middle'>";
		echo"<font class='heading1'>Combat</font>:<br><select class='engine' name = 'ability_set_min_2'>";
		echo"<option value = '$curab2[ability_set_min_2]'>$curab2[ability_set_min_2]</option>";
		$base_set_min_count2 = 1;
		while($base_set_min_count2 <= 10)
		{
			echo"<option value = '$base_set_min_count2'>$base_set_min_count2</option>";
			$base_set_min_count2++;
		}
		echo"</select>";
		echo"</td>";
		echo"<td width = '2%'>&nbsp;";
		echo"</td>";
		echo"<td align = 'right' valign='middle'>";
		echo"<font class='heading1'>Faith</font>:<br><select class='engine' name = 'ability_set_min_3'>";
		echo"<option value = '$curab2[ability_set_min_3]'>$curab2[ability_set_min_3]</option>";
		$base_set_min_count3 = 1;
		while($base_set_min_count3 <= 10)
		{
			echo"<option value = '$base_set_min_count3'>$base_set_min_count3</option>";
			$base_set_min_count3++;
		}
		echo"</select>";
		echo"</td>";
		echo"<td width = '2%'>&nbsp;";
		echo"</td>";
		echo"<td align = 'right' valign='middle'>";
		echo"<font class='heading1'>Insight</font>:<br><select class='engine' name = 'ability_set_min_4'>";
		echo"<option value = '$curab2[ability_set_min_4]'>$curab2[ability_set_min_4]</option>";
		$base_set_min_count4 = 1;
		while($base_set_min_count4 <= 10)
		{
			echo"<option value = '$base_set_min_count4'>$base_set_min_count4</option>";
			$base_set_min_count4++;
		}
		echo"</select>";
		echo"</td>";
		echo"<td width = '2%'>&nbsp;";
		echo"</td>";
		echo"<td align = 'right' valign='middle'>";
		echo"<font class='heading1'>Stealth</font>:<br><select class='engine' name = 'ability_set_min_5'>";
		echo"<option value = '$curab2[ability_set_min_5]'>$curab2[ability_set_min_5]</option>";
		$base_set_min_count5 = 1;
		while($base_set_min_count5 <= 10)
		{
			echo"<option value = '$base_set_min_count5'>$base_set_min_count5</option>";
			$base_set_min_count5++;
		}
		echo"</select>";
		echo"</td>";
		echo"</tr>";
		echo"</table>";
		echo"<br><br><br><br>";
	}
	
	echo"<font class='heading1'>Ability Tier:</font> . . . <select class='engine' name = 'add_ab_tier'>";
	echo"<option value = '$curab2[ability_tier]'>$curab2[ability_tier]</option>";
	$ability_tier = 1;
	while($ability_tier <= 4)
	{
		echo"<option value = '$ability_tier'>$ability_tier</option>";
		$ability_tier++;
	}
	echo"</select><br>";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			$get_ability_set = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id NOT IN (SELECT ability_set_id FROM ".$slrp_prefix."ability_set WHERE ability_set_id = '$gtcurrabset[ability_set_id]') ORDER BY ability_set") or die ("failed getting ability_set.");
			$gtabsetcnt = mysql_num_rows($get_ability_set);
			echo"<font class='heading1'>Choose Ability Set</font> . . . <select class='engine' name = 'add_ab_set'>";
			echo"<option value = '$gtcurrabset[ability_set_id]'>$gtcurrabset[ability_set]</option>";
			echo"<option value = '1'>None</option>";
			
			while($gtabset = mysql_fetch_assoc($get_ability_set))
			{
				echo"<option value = '$gtabset[ability_set_id]'>$gtabset[ability_set]</option>";
			}
				
			echo"</select><br><br>";
			
			echo"<font class='heading1'>Set Build Cost</font> . . . <select class='engine' name = 'add_ab_cost'>";
			echo"<option value = '$curab2[ability_build_cost]'>$curab2[ability_build_cost]</option>";
			$build_cost = 0;
			while($build_cost <= 25)
			{
				echo"<option value = '$build_cost'>$build_cost</option>";
				$build_cost++;
			}
			echo"</select><br><br>";
			
			echo"<font class='heading1'>Purchase Limit</font> . . . <select class='engine' name = 'add_ab_count_max'>";
			echo"<option value = '$curab2[ability_count_max]'>$curab2[ability_count_max]</option>";
			$purchase_limit = 0;
			while($purchase_limit <= 100)
			{
				echo"<option value = '$purchase_limit'>$purchase_limit</option>";
				
				if($purchase_limit >= 25)
				{
					$purchase_limit++;
					$purchase_limit++;
					$purchase_limit++;
					$purchase_limit++;
				}
				$purchase_limit++;
			}
			echo"</select><br><br>";
			
			
			$pc_level_option = floor(($curab2[ability_xp_min]-25)/10);
			if($pc_level_option <= 0)
			{
				$pc_level_option = 0;
			}
			echo"<font class='heading1'>Level/Build Minimum:</font> . . . <select class='engine' name = 'add_ab_xp_min'>";
			echo"<option value = '$curab2[ability_xp_min]'>$pc_level_option / $curab2[ability_xp_min]</option>";
			$ability_xp_min = 35;
			while($ability_xp_min <= 250)
			{
			
				$ab_level_option = floor(($ability_xp_min-25)/10);
				if($ab_level_option <= 0)
				{
					$ab_level_option = 0;
				}	
				echo"<option value = '$ability_xp_min'>$ab_level_option / $ability_xp_min</option>";
				
				if($ability_xp_min >= 25)
				{
					$ability_xp_min++;
					$ability_xp_min++;
					$ability_xp_min++;
					$ability_xp_min++;
				}
				$ability_xp_min++;
			}
			echo"</select><br><br>";			
			
			echo"<font class='heading1'>Non-Domain Build Minimum:</font> . . . <select class='engine' name = 'add_ab_sp_xp_min'>";
			echo"<option value = '$curab2[ability_special_xp_min]'>$curab2[ability_special_xp_min]</option>";
			$ability_sp_xp_min = 35;
			while($ability_sp_xp_min <= 250)
			{
				echo"<option value = '$ability_sp_xp_min'>$ability_sp_xp_min</option>";
				
				if($ability_sp_xp_min >= 25)
				{
					$ability_sp_xp_min++;
					$ability_sp_xp_min++;
					$ability_sp_xp_min++;
					$ability_sp_xp_min++;
				}
				$ability_sp_xp_min++;
			}
			echo"</select><br><br>";
			
			echo"<font class='heading1'>Ability Tier:</font> . . . <select class='engine' name = 'add_ab_tier'>";
			echo"<option value = '$curab2[ability_tier]'>$curab2[ability_tier]</option>";
			$ability_tier = 1;
			while($ability_tier <= 4)
			{
				echo"<option value = '$ability_tier'>$ability_tier</option>";
				$ability_tier++;
			}
			echo"</select><br>";
		}
	}
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] >= 4)
	{
		if($curab2[ability_library_status] <= 5)
		{
			$level_option = floor(($curab2[ability_xp_min]-25)/10);
			if($level_option <= 1)
			{
				$level_option = 1;
			}
			echo"<font class='heading1'>Ability Set: <font color = 'orange'>$gtcurrabset[ability_set]</font>";
					
			if($curab2[ability_library_status] == 5)
			{
				echo"<font color = 'red'>";
				echo" [Restricted]";
				echo"</font>";
			}
			
			echo"<br><br>";
		}
	}
}

$get_special_effects = mysql_query("SELECT * FROM ".$slrp_prefix."effect_special WHERE effect_special_id > '1' AND effect_special_id NOT IN (SELECT effect_special_id FROM ".$slrp_prefix."ability_effect_special WHERE ability_id = '".$curab2[ability_id]."') ORDER BY effect_special") or die ("failed getting sp effects list.");
$gtspcleffscnt = mysql_num_rows($get_special_effects);

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<br><br><font class='heading1'>Choose Special Effect:</font> . . . <select class='engine' name = 'add_spcl_eff'><option value = '1'>None</option>";

	// while($gtspcleffs = mysql_fetch_assoc($get_special_effects))
	while($gtspcleffs = mysql_fetch_assoc($get_special_effects))
	{
		echo"<option value = '".$gtspcleffs[effect_special_id]."'>".$gtspcleffs[effect_special]."</option>";
	}
		
	echo"</select><br><br>";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"<br><br><font class='heading1'>Choose Special Effect</font> . . . <select class='engine' name = 'add_spcl_eff'><option value = '1'>None</option>";
		
			// while($gtspcleffs = mysql_fetch_assoc($get_special_effects))
			while($gtspcleffs = mysql_fetch_assoc($get_special_effects))
			{
				echo"<option value = '".$gtspcleffs[effect_special_id]."'>".$gtspcleffs[effect_special]."</option>";
			}
				
			echo"</select><br><br>";
		}
	}
}

$get_current_special_effect = mysql_query("SELECT * FROM ".$slrp_prefix."effect_special INNER JOIN ".$slrp_prefix."ability_effect_special ON ".$slrp_prefix."effect_special.effect_special_id = ".$slrp_prefix."ability_effect_special.effect_special_id WHERE ".$slrp_prefix."ability_effect_special.ability_id = '".$curab2[ability_id]."' ORDER BY ".$slrp_prefix."ability_effect_special.effect_special_default,".$slrp_prefix."effect_special.effect_special") or die ("failed getting current special effects.");
$gtcurrspcleffcnt = mysql_num_rows($get_current_special_effect);
if($curusrslrprnk[slurp_rank_id] <= 4)
{
	if($gtcurrspcleffcnt == 1)
	{
		echo"<font size = '1' color = 'red'>DEL</font><br>";
	}
}
// while($gtcurrspcleff = mysql_fetch_assoc($get_current_special_effect))
while($gtcurrspcleff = mysql_fetch_assoc($get_current_special_effect))
{
	echo"<font class='heading1'>Special Effect/Tagline: </font><font color = 'orange'>".$gtcurrspcleff[effect_special]."" ;
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		echo" . . . <input type='checkbox' value='".$gtcurrspcleff[effect_special_id]."' name='del_".$gtcurrspcleff[effect_special]."_id'></font><br>";
	}
	
	if($curusrslrprnk[slurp_rank_id] >= 5)
	{
		if($curab2[ability_library_status] <= 3)
		{
			if($curab2[ability_owner_id] == $usrnfo[user_id])
			{
				echo" . . . <input type='checkbox' value='".$gtcurrspcleff[effect_special_id]."' name='del_".$gtcurrspcleff[effect_special]."_id'></font><br>";
			}
		}
	}
}

echo"<br><br>";

$get_current_library_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status INNER JOIN ".$slrp_prefix.$getfoc[focus_table]." ON ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_library_status = ".$slrp_prefix."slurp_status.slurp_status_id WHERE ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_id = '".$curab2[ability_id]."'") or die ("failed getting current library status.");
// $gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);
$gtcurrlibstat = mysql_fetch_assoc($get_current_library_status);

echo"<font class='heading1'>Library Status: </font>
	<font color = 'orange' size = '2'>";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
	<select class='engine' name = 'ab_library_status'>
	<option value = '".$gtcurrlibstat[slurp_status_id]."'>";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"
			<select class='engine' name = 'ab_library_status'>
			<option value = '".$gtcurrlibstat[slurp_status_id]."'>";
		}
	}
}

echo"$gtcurrlibstat[slurp_status]/$gtcurrlibstat[slurp_alt_status1]";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"</option>";
			
	$get_slurp_library_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_alt_status1 != 'None' ORDER BY slurp_status_id DESC") or die("failed to get rank list.");
	// while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
	while($gtslrplibstat = mysql_fetch_assoc($get_slurp_library_status))
	{
		echo"<option value = '".$gtslrplibstat[slurp_status_id]."'>".$gtslrplibstat[slurp_status]."/".$gtslrplibstat[slurp_alt_status1]."</option>";
	}
	
	echo"</select>
			 <font>";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"</option>";
					
			$get_slurp_library_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_status_id = '2' OR slurp_status_id = '3' ORDER BY slurp_status_id DESC") or die("failed to get rank list.");
			// while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
			while($gtslrplibstat = mysql_fetch_assoc($get_slurp_library_status))
			{
				echo"<option value = '".$gtslrplibstat[slurp_status_id]."'>".$gtslrplibstat[slurp_status]."/".$gtslrplibstat[slurp_alt_status1]."</option>";
			}
			
			echo"</select>";
		}
	}
}


$get_current_minimum_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix.$getfoc[focus_table]." ON ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_id = '".$curab2[ability_id]."'") or die ("failed getting current min rank to view.");
// $gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);
$gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<br><br><font class='heading1'>Minimum Rank to View:</font> . . . <select class='engine' name = 'min_rank'><option value = '".$gtcurrmnrnk[slurp_rank_id]."'>".$gtcurrmnrnk[slurp_rank]."</option>";
	
	$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id > '".$curusrslrprnk[slurp_rank_id]."' ORDER BY slurp_rank_id DESC") or die("failed to get rank list.");
	// while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
	while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
	{
		echo"<option value = '".$gtslrprnk[slurp_rank_id]."'>".$gtslrprnk[slurp_rank]."</option>";
	}
	
	echo"</select>";
}

echo"<br><br>";

$get_mimic_ability_list = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1' AND ability_id NOT IN (SELECT mimics_ability_id FROM ".$slrp_prefix."ability_mimics_ability WHERE ability_id = '".$curab2[ability_id]."') ORDER BY ability") or die ("failed getting mimicked ability list.");
$gtmmcablstcnt = mysql_num_rows($get_mimic_ability_list);

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<font class='heading1'>Choose Mimicked Ability</font> . . . <select class='engine' name = 'add_mimic_ab'><option value = '1'>None</option>";

	while($gtmmcablst = mysql_fetch_assoc($get_mimic_ability_list))
	{
		echo"<option value = '".$gtmmcablst[ability_id]."'>".$gtmmcablst[ability]."</option>";
	}
		
	echo"</select><br><br>";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"<font class='heading1'>Choose Mimicked Ability:</font> . . . <select class='engine' name = 'add_mimic_ab'><option value = '1'>None</option>";
		
			// while($gtspcleffs = mysql_fetch_assoc($get_special_effects))
			while($gtmmcablst = mysql_fetch_assoc($get_mimic_ability_list))
			{
				echo"<option value = '".$gtmmcablst[ability_id]."'>".$gtmmcablst[ability]."</option>";
			}
				
			echo"</select>";
		}
	}
}

$get_current_ability_mimic = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_mimics_ability ON ".$slrp_prefix."ability.ability_id = ".$slrp_prefix."ability_mimics_ability.mimics_ability_id WHERE ".$slrp_prefix."ability_mimics_ability.ability_id = '$curab2[ability_id]' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting current mimicked ability.");
$gtcurrabmmccnt = mysql_num_rows($get_current_ability_mimic);
if($gtcurrabmmccnt >= 1)
{
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		echo"<font size = '1' color = 'red'>DEL</font>";
	}
	
	echo"<font class='heading1'>Mimics Ability(-ies) for Prerequisites:<br>";
}

while($gtcurrabmmc = mysql_fetch_assoc($get_current_ability_mimic))
{
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		echo" . . . <input type='checkbox' value='".$gtcurrabmmc[mimics_ability_id]."' name='del_".$gtcurrabmmc[ability]."_id'></font>";
	}
	
	echo"<a href='modules.php?name=My_Vanguard&file=ab_edit&expander_abbr=$expander_abbr&current_focus_id=2&current_ab_id=$gtcurrabmmc[mimics_ability_id]' class='default'>$gtcurrabmmc[ability]</a><br>";
	
	if($curusrslrprnk[slurp_rank_id] >= 5)
	{
		if($curab2[ability_library_status] <= 3)
		{
			if($curab2[ability_owner_id] == $usrnfo[user_id])
			{
				echo" . . . <input type='checkbox' value='".$gtcurrabmmc[ability_id]."' name='del_".$gtcurrabmmc[ability]."_id'></font><br>";
			}
		}
	}
}
echo"<br>";
	
if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] >= 4)
	{
		if($curab2[ability_library_status] <= 5)
		{
			$level_option = floor(($curab2[ability_xp_min]-25)/10);
			if($level_option <= 1)
			{
				$level_option = 1;
			}
			
			echo"<font class='heading1'>Build Cost: <font color = 'orange'>$curab2[ability_build_cost]</font><br>";
			echo"<font class='heading1'>Level/Build Minimum: <font color = 'orange'>$level_option / $curab2[ability_xp_min]</font><br>";
			echo"<font class='heading1'>Non-Domain Build Minimum: <font  color = 'orange'>$curab2[ability_special_xp_min]</font><br>";
			
			if($curab2[ability_set_min_1] >= 1 OR $curab2[ability_set_min_2] >= 1 OR $curab2[ability_set_min_3] >= 1 OR $curab2[ability_set_min_4] >= 1 OR $curab2[ability_set_min_5] >= 1)
			{
				if($curab2[ability_set_min_1] >= 1)
				{
					echo"<font class='heading1'>Burn Domain Ability Count Minimum: <font color = 'orange'>$curab2[ability_set_min_1]</font></font><br>";
				}
				if($curab2[ability_set_min_2] >= 1)
				{
					echo"Combat Domain Ability Count Minimum: <font color = 'orange'>$curab2[ability_set_min_2]</font></font><br>";
				}
				if($curab2[ability_set_min_3] >= 1)
				{
					echo"Faith Domain Ability Count Minimum: <font color = 'orange'>$curab2[ability_set_min_3]</font></font><br>";
				}
				if($curab2[ability_set_min_4] >= 1)
				{
					echo"Insight Domain Ability Count Minimum: <font color = 'orange'>$curab2[ability_set_min_4]</font></font><br>";
				}
				if($curab2[ability_set_min_5] >= 1)
				{
					echo"Stealth Domain Ability Count Minimum: <font color = 'orange'>$curab2[ability_set_min_5]</font></font><br>";
				}
			}
	
			$grphc_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix."object_graphic WHERE object_focus_id = '2' AND object_id = '$current_ab_id' AND object_slurp_id = '$slrpnfo[slurp_id]'") or die("failed to get object focus.");
			$grphcsbfccnt = mysql_num_rows($grphc_subfocus);
			$grphcsbfc = mysql_fetch_assoc($grphc_subfocus);
			
			// echo"starting seeds: obj: $grphc_object_id foc: $grphc_focus_id, $grphcsbfc[graphic_id]<br>";
			$graphic_identifier = $grphcsbfc[graphic_id];
			if($graphic_identifier >= 2)
			{
				// get the object graphic, if any
				$get_object_graphic = mysql_query("SELECT * FROM ".$slrp_prefix."graphic WHERE graphic_id = '$graphic_identifier'") or die ("failed to get graphic info.");
				$gtobjgrphccnt = mysql_num_rows($get_object_graphic);
				$gtobjgrphc = mysql_fetch_assoc($get_object_graphic);
				$graphic_identifier = $gtobjgrphc[graphic_id];
				// echo"current graphic($gtobjgrphccnt): $gtpbjgrphc[graphic]<br>";
				echo"<br><br><img src = 'images/$gtobjgrphc[graphic]' height = '50' width = '50'>";
			}
		}
	}
}

echo"
						</td>
					</tr>
					<tr>
						<td colspan = '$subcol1_count' align = 'right' valign = 'top'>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
							<br>
							<br>
							Set Random Key? <input type='checkbox' value='1' name='new_object_random'> . . . 
							<input type='hidden' value='ab' name='current_expander'>
							<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
							<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
							<input class='submit3' type='submit' value='Submit' name='ab_info_edit'>
	";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
				echo"
							<br>
							<br>
							Set Random Key? <input type='checkbox' value='1' name='new_object_random'> . . . 
							<input type='hidden' value='ab' name='current_expander'>
							<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
							<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
							<input class='submit3' type='submit' value='Submit' name='ab_info_edit'>
				";
		}
	}
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_restricted] == 1)
	{
		echo"<p><font color = 'red'>This is a Restricted Ability.</font></p>";
	}
}

echo"
								</font>
								<br>
							</td>
							</form>
						</tr>
					</table>
";
// end table holding the main ability info


// echo"<br>";

// begin copy section
if($curusrslrprnk[slurp_rank_id] <= 4)
{
	if($curab2[ability_library_status] == 4 or $curab2[ability_library_status] == 5)
	{
		echo"
											<table cellpadding='0' cellspacing='0' border='0' width='100%'>
												<tr background='themes/$ThemeSel/images/back2a.gif' height='24'>
													<form name = 'copy_template' method='post' action = 'modules.php?name=$module_name&file=ab_edit'>
													<td align = 'left' valign = 'middle' colspan = '$subcol2_count'>
														<font class='heading2'>
														&nbsp;COPY $current_ab_name TO NEW ABILITY:
													</td>
												</tr>
												<tr>
													<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
														<font class='heading1'>
														<li> Choose a name, then click below.
														<br>
														<br>
														<input type='text' class='textbox3' class = 'textbox3' size='20%' name='newabname'></input>
														<br>
														<br>
														Restricted? <input type='checkbox' value='1' name='ab_restr'>
													</td>
												</tr>
												<tr>
													<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
														<input type='hidden' value='ab' name='current_expander'>
														<input type='hidden' value='2' name='copy_ab_status'>
														<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
														<input type='hidden' value='".$usrnfo[nuke_user_id]."' name='copy_user_id'>
														<input type='hidden' value='".$curab2[ability_id]."' name='copy_ab_id'>
														<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
														<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
														<input class='submit3' type='submit' value='Submit' name='copy_template'>
													</td>
													</form>
												</tr>
												
												<tr background='themes/$ThemeSel/images/back2a.gif' height='24'>
													<form name = 'add_ability_to_book' method='post' action = 'modules.php?name=$module_name&file=ab_edit'>
													<td align = 'left' valign = 'middle' colspan = '$subcol2_count'>
														<font class='heading2'>
														&nbsp;MAKE $current_ab_name AVAILABLE IN A BOOK:
													</td>
												</tr>
												<tr>
			";
					
			$get_book_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."item_item_subtype WHERE ".$slrp_prefix."item_item_subtype.item_subtype_id >= '89' AND ".$slrp_prefix."item_item_subtype.item_subtype_id <= '93'") or die ("failed getting new item parent.");
					
			echo"
													<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
														<font color = 'yellow' size = '2'>
														<li> Choose a book, then click below.
														<br>
														<br>
														<select class='engine' name='newabbook'>
			";
			
			while($gtbksub = mysql_fetch_assoc($get_book_subtype))
			{
				$get_books = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE ".$slrp_prefix."item.item_id = '$gtbksub[item_id]' AND ".$slrp_prefix."item.item_id > '1'") or die ("failed getting book item.");
				$gtbks = mysql_fetch_assoc($get_books);
				
				echo"<option value = '".$gtbks[item_id]."_".$gtbksub[item_subtype_id]."'>$gtbks[item]</option>";				
			}
							
			echo"
														</select>
														<br>
														<br>
													</td>
												</tr>
												<tr>
													<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
														<input type='hidden' value='ab' name='current_expander'>
														<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
														<input type='hidden' value='".$usrnfo[nuke_user_id]."' name='book_user_id'>
														<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
														<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
														<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
														<input class='submit3' type='submit' value='Submit' name='add_ability_to_book'>
													</td>
													</form>
												</tr>
			";
			
			echo"
			<form name = 'drop_ability_from_book' method='post' action = 'modules.php?name=$module_name&file=ab_edit'>
			";
			// for this ability get all book table info
			$get_book_item_del_info = mysql_query("SELECT * FROM ".$slrp_prefix."item_book WHERE ability_id = '$curab2[ability_id]' AND item_id > '1' ") or die ("failed to get book item info.");
			while($gtbkitmdlnfo = mysql_fetch_assoc($get_book_item_del_info))
			{
				// check the items
				$get_book_item_del = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtbkitmdlnfo[item_id]' ORDER BY item") or die ("failed getting book list.");
				$curbkitemdl = mysql_fetch_assoc($get_book_item_del);
				// get the random info assigned to that book
				$get_book_item_del_rand = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random_id = '$gtbkitmdlnfo[object_random_id]'") or die ("failed to get book ability random info.");
				$gtbkitmdlrnd = mysql_fetch_assoc($get_book_item_del_rand);
				
				echo"
												<tr>
													<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
													<font color = 'orange'>$curbkitemdl[item]  (<font color = 'blue'>$gtbkitmdlrnd[object_random]</font>)  <font color = 'red' size = '1'>[DROP]</font></font>
													<input type='hidden' value='$curbkitemdl[item_id]' name='del_book_$curbkitemdl[item_id]'><input type='checkbox' value='$gtbkitmdlnfo[item_book_id]' name='del_book_item_$gtbkitmdlnfo[item_book_id]'>
													</td>
												</tr>
				";
			}
			
			echo"
												<tr>
													<td colspan ='5'>
														<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
														<input type='hidden' value='$curab2[ability_id]' name='current_ab_id'>
														<input type='hidden' value='$expander_abbr' name='current_expander'>
														<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
														<input type='hidden' value='$component_expander' name = 'component_expander'>
														<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
														<input type='hidden' value='$materials_expander' name = 'materials_expander'>
														<input type='hidden' value='$items_expander' name = 'items_expander'>
														<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
														<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
														<input class='submit3' type='submit' value='Remove from Print' name='drop_ability_from_book'>
													</td>
													</form>
												</tr>												
											</table>
		";
	}
}
// end copy section


// end left pane

// this is the middle break of the main page panes
echo"
				<td width = '2%' valign = 'top' align = 'center'>
					<table width = '100%' cellspacing='0' cellpadding='0' border='0'>
						<tr background='themes/Vanguard/images/back2b.gif' height='24'>
							<td valign = 'middle' width = '100%' align = 'center'>
							&nbsp;
							</td>
						</tr>
					</table>
				</td>
";

// begin right main pane and containing table; this is the header row; there are not tables for each section going forward; just new rows in this pane
echo"
				<td align = 'left' valign = 'top' colspan = '3'>
";

echo"
					<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
						<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
							<td align = 'left' valign = 'middle' width = '10%'>
								<font class='heading2'>
								&nbsp;DEL
								</font>
							</td>
							
							<td width = '2%'>
							&nbsp;
							</td>
	";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"
							<td align = 'left' valign = 'middle' width = '10%'>
								<font class='heading2'>
								&nbsp;DEL
								</font>
							</td>
							
							<td width = '2%'>
							&nbsp;
							</td>
			";
		}
	}
}


if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
							<td class='heading2' align = 'center' valign = 'middle' width = '10%'>
								<font class='heading2'>
								&nbsp;WEIGHT
								</font>
							</td>
							<td width = '2%'>
							&nbsp;
							</td>
	";
}

echo"							
							<td align = 'left' valign = 'middle'>
								<font class='heading2'>
								EFFECTS (Hover)
							</td>
						</tr>
						<tr>
							<td colspan = '$subcol2_count'>
								<br>
							</td>
						</tr>
";

// start listing effects and their weights (support weighs nothing)
$get_effect = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect INNER JOIN ".$slrp_prefix."effect_type ON ".$slrp_prefix."effect_type.effect_type_id = ".$slrp_prefix."ability_effect.effect_type_id WHERE ".$slrp_prefix."ability_effect.ability_id = '".$curab2[ability_id]."' ORDER BY ".$slrp_prefix."effect_type.effect_type_support,".$slrp_prefix."effect_type.effect_type") or die("failed to get ablity effects.");
$geteffcnt = mysql_num_rows($get_effect);
$effect_counter = $geteffcnt;

// set the starting cost to zero
$cost_count = 0;


// echo"eff_cnt: $geteffcnt<br>";

if($geteffcnt >= 1)
{
	// set the starting subtotal to zero
	$cost_count_sub =  0;
	$highest_effect_tier = 1;
	
	while($geteff = mysql_fetch_assoc($get_effect))
	{
		$effect_modifier_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '".$geteff[effect_modifier_id]."'") or die ("failed getting effect modifier info for listing.");
		$effmodnfo = mysql_fetch_assoc($effect_modifier_info);
		
		$effect_type_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id = '".$geteff[effect_type_id]."'") or die ("failed getting effect type info for listing.");
		$efftypenfo = mysql_fetch_assoc($effect_type_info);
		
		$ability_mod_subfocus_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE focus_id = '9' AND subfocus_id = '$geteff[effect_id]' AND ability_modifier_id = '$effmodnfo[ability_modifier_id]'") or die ("failed getting effect mod subfocus info for listing.");
		$abmdsbfcsnfo = mysql_fetch_assoc($ability_mod_subfocus_info);
		
		$ability_effect_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$abmdsbfcsnfo[subfocus_id]'") or die ("failed getting effect info for listing.");
		$abeffnfo = mysql_fetch_assoc($ability_effect_info);
		
		// for base effects, add the tier to the subtotal
		if($efftypenfo[effect_type_support] == 0)
		{
			$effect_color = 'white';
			$cost_count_sub = $cost_count_sub + $effmodnfo[ability_modifier_value];
			$printed_weight = $effmodnfo[ability_modifier_value];
			if($highest_effect_tier < $printed_weight)
			{
				$highest_effect_tier = $printed_weight;
			}
		}
		// for support effects, add nothing to the subtotal			
		if($efftypenfo[effect_type_support] == 1)
		{
			$effect_color = 'orange';
			$printed_weight = 0;
		}
		
		// echo"chars: $effmodnfo[0] ($effmodnfo[3])<br>";
		
		echo"
					<tr>
		";
		
		// offer a remove option to admins
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"
						<form name = 'ab_del_efftyp_form' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>
						<td width = '10%' valign = 'top' align = 'left'>
							<input type='hidden' value='".$geteff[ability_effect_id]."' name='del_effect'>
							<input type='hidden' value='ab' name='current_expander'>
							<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
							<input class='submit3' type='submit' value='x' name='ab_del_efftyp_form'>
						</td>
						</form>
						
						<td width = '2%' align = 'left' valign = 'top'>
						&nbsp;
						</td>
			";
		}
		
		if($curusrslrprnk[slurp_rank_id] >= 5)
		{
			if($curab2[ability_library_status] <= 3)
			{
				if($curab2[ability_owner_id] == $usrnfo[user_id])
				{
					echo"
						<form name = 'ab_del_efftyp_form' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>
						<td width = '10%' valign = 'top' align = 'left'>
							<input type='hidden' value='".$geteff[ability_effect_id]."' name='del_effect'>
							<input type='hidden' value='ab' name='current_expander'>
							<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
							<input class='submit3' type='submit' value='x' name='ab_del_efftyp_form'>
						</td>
						</form>
						
						<td width = '2%' align = 'left' valign = 'top'>
						&nbsp;
						</td>
					";
				}
			}
		}
		
		
		// print the value and description
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"
						<td width = '10%' align = 'center' valign = 'top'>
							<font class='heading1'>
							$printed_weight
							</font>
						</td>
						
						<td width = '2%' align = 'left' valign = 'top'>
						&nbsp;
						</td>
			";
		}
		
		echo"				
						<td width = '76%' align = 'left' valign = 'top'>
							<font class='heading1'  title='[$efftypenfo[effect_type]] $abeffnfo[effect_desc]'>
							<li>$abeffnfo[effect]
							</font>
						</td>
					</tr>
		";
	}
	
 	// echo"<font color = 'blue'>before: $cost_count_sub</font><br>";
}

// if the Ability has no Effects, let them know...
if($geteffcnt == 0)
{
	echo"
						<tr>
							<td colspan = '5' align = 'left' valign = 'top'>
								<font color = 'red' size = '2'>
								<li>This Ability needs at least one Base Effect.
							</td>
						</tr>
	";
}
	
echo"
						<tr>
							<td colspan = '$subcol2_count' align = 'left' valign = 'top'>
								<br>
							</td>
						</tr>
";

// get non-effect modifiers and print to the screen
$all_ability_modifiers = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_id = '".$curab2[ability_id]."' ORDER BY ".$slrp_prefix."ability_modifier.ability_modifier_short") or die("failed to get modifiers.");
$allabmodcnt = mysql_num_rows($all_ability_modifiers);

// add the subtotal to the total and set the subtotal back to zero
$cost_count = $cost_count_sub;
$cost_count_sub = 0;
// echo"#mods: $allabmodcnt<br>Cost Ct.: $cost_count<br>";
// set the base multiplier to one
$cost_multiplier = 1;

if($allabmodcnt >= 1)
{

	echo"
						<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
	";
	
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"
							<td align = 'left' valign = 'middle' width = '10%'>
								<font class='heading2'>
								&nbsp;DEL
							</td>
							
							<td width = '2%'>
							&nbsp;
							</td>
			";
		}	
	
		if($curusrslrprnk[slurp_rank_id] >= 5)
		{
			if($curab2[ability_library_status] <= 3)
			{
				if($curab2[ability_owner_id] == $usrnfo[user_id])
				{
					echo"
							<td align = 'left' valign = 'middle' width = '10%'>
								<font class='heading2'>
								&nbsp;DEL
							</td>
							
							<td width = '2%'>
							&nbsp;
							</td>
					";
				}
			}
		}
		
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		echo"
							<td class='heading2' align = 'center' valign = 'middle' width = '10%'>
								<font class='heading2'>
								&nbsp;WEIGHT
								</font>
							</td>
							<td width = '2%'>
							&nbsp;
							</td>
		";
	}
	
	echo"
							<td width = '76%' align = 'left' valign = 'middle'>
								<font class='heading2'>
								MODIFIERS
							</td>
						</tr>
						<tr>
							<td colspan = '$subcol2_count'>
								<br>
							</td>
						</tr>
	";

	// while($allabmod = mysql_fetch_assoc($all_ability_modifiers))
	// track the subtotal
	$count_sub_weight = 0;
	while($allabmod = mysql_fetch_assoc($all_ability_modifiers))
	{
		// echo"mod_rel: $allabmod [0]<br>";
		echo"<tr>";
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"
						<form name = 'ab_del_mod_form' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>
						<td width = '10%' valign = 'top' align = 'left'>
							<input type='hidden' value='".$allabmod[ability_modifier_id]."' name='del_mod_id'>
							<input type='hidden' value='ab' name='current_expander'>
							<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
							<input class='submit3' type='submit' value='x' name='ab_del_mod_form'>
						</td>
						</form>
						
						<td width = '2%' align = 'left' valign = 'top'>
						&nbsp;
						</td>
			";
		}
		
		if($curusrslrprnk[slurp_rank_id] >= 5)
		{
			if($curab2[ability_library_status] <= 3)
			{
				if($curab2[ability_owner_id] == $usrnfo[user_id])
				{
					echo"
						<form name = 'ab_del_mod_form' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>
						<td width = '10%' valign = 'top' align = 'left'>
							<input type='hidden' value='".$allabmod[ability_modifier_id]."' name='del_mod_id'>
							<input type='hidden' value='ab' name='current_expander'>
							<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
							<input class='submit3' type='submit' value='x' name='ab_del_mod_form'>
						</td>
						</form>
						
						<td width = '2%' align = 'left' valign = 'top'>
						&nbsp;
						</td>
					";
				}
			}
		}
		
		// for extreme modifiers using multipliers, only change the multiplier

		if($allabmod[ability_modifier_type_id] == 9)
		{
			$cost_multiplier = ($cost_multiplier*$allabmod[ability_modifier_value]);
//			// echo"extreme mult: $cost_multiplier, $allabmod[ability_modifier_value]<br>";
			$printed_mod_cost = "x".$allabmod[ability_modifier_value];
			if($allabmod[ability_modifier_value] >= 2)
			{
				$count_sub_weight = $cost_multiplier * $count_sub_weight;
			}
		}
		
		// for non-extreme modifiers, change only the cost
		if($allabmod[ability_modifier_type_id] != 9)
		{
			// for charges
			if($allabmod[ability_modifier_type_id] == 3)
			{
				$cost_count_sub = $allabmod[ability_modifier_value];
				$printed_mod_cost = $allabmod[ability_modifier_value];
			}
			// all non-charges modifiers
			if($allabmod[ability_modifier_type_id] != 3)
			{
				$cost_count_sub = $allabmod[ability_modifier_value];
				$printed_mod_cost = $allabmod[ability_modifier_value];
				$count_sub_weight = $count_sub_weight + $cost_count_sub;
			}
			
			// Add the subtotal to the effects total	
			// echo"before: $cost_count + $cost_count_sub<br>";
			$cost_count = $cost_count + $cost_count_sub;

		}
		
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"			
			<td width= '10%' valign = 'top' align = 'center'>
			<font class='heading3'>
			$printed_mod_cost
			</font>
			</td>
			
			<td width = '2%'>
			&nbsp;
			</td>
			";
		}
		
		echo"			
			<td align = 'left' valign = 'top' width = '76%'>
			<font class='heading1'>
		";
				
		
		if($allabmod[ability_modifier_id] == 15689)
		{
			echo"<li> Must know Souce Mark";
		}
		if($allabmod[ability_modifier_id] != 15689)
		{
			// get things the modifier affects
			$ability_modifier_presub = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_id ='".$allabmod[ability_modifier_id]."'") or die ("failed to get presub.");
			// while($abmodpre = mysql_fetch_assoc($ability_modifier_presub))
			while($abmodpre = mysql_fetch_assoc($ability_modifier_presub))
			{
				$ability_modifier_focus = mysql_query("SELECT * FROM ".$slrp_prefix."focus WHERE focus_id ='".$abmodpre[focus_id]."' ORDER BY focus_priority DESC") or die("failed to get modifier focus.");
				$abmodfoccnt = mysql_num_rows($ability_modifier_focus);
				// $abmodfoc = mysql_fetch_assoc($ability_modifier_focus);
				$abmodfoc = mysql_fetch_assoc($ability_modifier_focus);
				
				echo"<li> ";
				
				// get the specific limiters
				$ability_modifier_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_subfocus_id = '".$abmodpre[ability_modifier_subfocus_id]."'");
				// while($abmodsub = mysql_fetch_assoc($ability_modifier_subfocus))
				while($abmodsub = mysql_fetch_assoc($ability_modifier_subfocus))
				{	
					// echo"sub: ".$abmodsub[subfocus_id];
					
					// if there are no exclusions, print the basic format
					if($abmodsub[focus_exclusion_id] <= 1)
					{
						$get_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix.$abmodfoc[focus_table]." WHERE ".$abmodfoc[focus_table]."_id = '".$abmodsub[subfocus_id]."'") or die ("failed getting no exclusion sub.");
						while($getsub= mysql_fetch_assoc($get_subfocus))
						{
							echo $abmodfoc[focus].": ".$getsub[$abmodfoc[focus_table]];
						}
					}
					
					// if there are exclusions, get them and print them also
					if($abmodsub[focus_exclusion_id] >= 2)
					{
						$get_exclusion = mysql_query("SELECT * FROM ".$slrp_prefix."focus_exclusion WHERE focus_exclusion_id = '".$abmodsub[focus_exclusion_id]."'")or die ("failed getting exclusion.");
						// $getexcl= mysql_fetch_assoc($get_exclusion);
						$getexcl= mysql_fetch_assoc($get_exclusion);
						// echo"$abmodsub[focus_id], ex: $getexcl[focus_exclusion]<br>";
						
						$get_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix.$abmodfoc[focus_table]." WHERE ".$abmodfoc[focus_table]."_id = '".$abmodsub[subfocus_id]."'") or die ("failed getting exclusion sub.");
						// while($getsub= mysql_fetch_assoc($get_subfocus))
						while($getsub= mysql_fetch_assoc($get_subfocus))
						{
							echo $getexcl[focus_exclusion]." ".$getsub[$abmodfoc[focus_table]];
						}
					}
				}
			}
		}
		
		echo"</td>";	
		echo"</tr>";

		// echo"= $cost_count, cm_final: $cost_multiplier<br>";
	}
}

// now that cost multipliers are finished cross multiplying, factor in the effects total
$cost_weight = $cost_count;
$cost_count = $cost_count*$cost_multiplier;
	
// echo"after...cc: $cost_count, cm: $cost_multiplier, cs: $cost_count_sub<br>";
echo"
						<tr>
								<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
									<br>
								</td>
							</tr>
							
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"					
							<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
								<td width = '10%' valign= 'middle' align = 'left'>			
								</td>
								
								<td width = '2%'>
								&nbsp;
								</td>
								<td align = 'center' valign = 'middle' width = '10%'>
									<font class='heading2'>
									&nbsp;WEIGHT 
									</font>
								</td>
								
								<td align = 'left' valign = 'middle' width = '2%'>
								&nbsp;
								</td>
								
								<td align = '$wide_align' valign = 'middle' width = '76%'>
									<font class='heading2'>
									
									</font>
								</td>
							</tr>
							<tr>
								<td width= '10%' valign = 'top' align = 'center'>
								</td>
								
								<td width = '2%'>
								&nbsp;
								</td>
								<td align = 'center' valign = 'top' width = '10%'>
									<font size = '3' color = 'white'>
									$cost_count
									<br>
								</td>
								
								<td width = '2%'>
								&nbsp;
								</td>
								
								<td width = '10%' valign= 'top' align = 'left'>
	";
}
if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"
							<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
								<td width = '10%' valign= 'middle' align = 'left'>			
								</td>
								
								<td width = '2%'>
								&nbsp;
								</td>
								<td align = 'center' valign = 'middle' width = '10%'>
									<font class='heading2'>
									&nbsp;WEIGHT 
									</font>
								</td>
								
								<td align = 'left' valign = 'middle' width = '2%'>
								&nbsp;
								</td>
								
								<td align = '$wide_align' valign = 'middle' width = '76%'>
									<font class='heading2'>
									
									</font>
								</td>
							</tr>
							<tr>
								<td align = 'center' valign = 'top' width = '10%'>
									<font size = '3' color = 'white'>
									$cost_count
									<br>
								</td>
								<td width = '2%'>
								&nbsp;
								</td>
								
								<td width = '10%' valign= 'top' align = 'left'>
			";
		}
	}
}

echo"
							</td>
						</tr>
";


if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
						<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
							<td align = 'left' valign = 'middle' colspan = '$subcol2_count'>
								<font class='heading2'>
								&nbsp;ADD EFFECTS
							</td>
						</tr>
						<tr>
							<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
								<br>
							</td>
						</tr>
						<form name = 'ab_effect_form' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>
	";

	$ab_base_eff_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id > '1' ORDER BY effect_type_support DESC, effect_type");
	$abbsefflstcnt = mysql_num_rows($ab_base_eff_list);
	
	// while($abbsefflst = mysql_fetch_assoc($ab_base_eff_list))
	while($abbsefflst = mysql_fetch_assoc($ab_base_eff_list))
	{
		if($abbsefflst[effect_type_support] == 0)
		{
			$effect_color2 = "white";
		}
		
		if($abbsefflst[effect_type_support] == 1)
		{
			$effect_color2 = "orange";
		}
	
		echo"
					<tr height='24'>
						<td valign= 'middle' align = 'right' colspan = '3'>
							<font class='heading1'>
							".$abbsefflst[effect_type].": 
							</font>
						</td>
						
						<td width = '5%'>
						<br>
						</td>
						
						<td align = 'left' valign = 'top'>
						<select class='engine' name ='base_".$abbsefflst[effect_type]."_id'><option value = '1'>Choose Effect for $abbsefflst[effect_type]</option>";

		$list_effect_subs = mysql_query("SELECT * FROM ".$slrp_prefix."effect_subtype_effect_type WHERE effect_type_id = '".$abbsefflst[effect_type_id]."'") or die ("failed getting subtype realtion to base effect.");
		// while($lsteffsubs = mysql_fetch_assoc($list_effect_subs))
		while($lsteffsubs = mysql_fetch_assoc($list_effect_subs))
		{
			$effect_subtype_information = mysql_query("SELECT * FROM ".$slrp_prefix."effect_subtype WHERE effect_subtype_id = '".$lsteffsubs[effect_subtype_id]."'") or die ("failed getting effect sub info for ability.");
			// $effsubnfo = mysql_fetch_assoc($effect_subtype_information);
			$effsubnfo = mysql_fetch_assoc($effect_subtype_information);
			echo"<optgroup label= '".$effsubnfo[effect_subtype]."'>";
			
			$list_effects_by_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect_effect_subtype.effect_id = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '".$effsubnfo[effect_subtype_id]."' ORDER BY ".$slrp_prefix."effect.effect_tier, ".$slrp_prefix."effect.effect") or die ("failed getting effect realtion to subtype effect.");
			// while($lsteffbysub = mysql_fetch_assoc($list_effects_by_subtype))
			while($lsteffbysub = mysql_fetch_assoc($list_effects_by_subtype))
			{
				echo"<option value = '".$lsteffbysub[effect_id]."'>".roman($lsteffbysub[effect_tier])." - ".$lsteffbysub[effect]."</option>";
			}
		}
		echo"</select>";
	
		if($abbsefflstcnt >= 1)
		{
			echo"
								<br>
			";
		}
	}
	
	echo"
													</td>
													<td width = '2%'>
													&nbsp;
													</td>
												</tr>
												<tr>
													<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
														<font color='orange'>Orange = Required.&nbsp;&nbsp;
														<input type='hidden' value='".$abbsefflst[effect_type_id]."' name='".$abbsefflst[effect_type]."_id'>
														<input type='hidden' value='ab' name='current_expander'>
														<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
														<input class='submit3' type='submit' value='Add Base Effect(s)' name='ab_effect_form'>
													</td>
													</form>
												</tr>
	";
	// end add base effects
	
	//if ranked enough, show the Add Modifiers pane
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		echo"
			<tr>
				<td colspan = '7'>
					<br>
						<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
							<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
								<td colspan = '3' align = 'left' valign = 'middle'>
									<font class='heading2'>
									&nbsp;ADD MODIFIERS
									</font>
								</td>
							</tr>
							<tr>
								<td colspan = '2' align = 'left' valign = 'top'>
									<br>
								</td>
							</tr>
							<form name = 'ab_edit_mod' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>
		";
		
		$get_modifier_type = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_type WHERE ability_modifier_type_id > '1' ORDER BY ability_modifier_type") or die("failed to get mod types.");
		$getmodtypcnt = mysql_num_rows($get_modifier_type);
		$gtmdtypcnt = $getmodtypcnt;
		// while($getmodtyp = mysql_fetch_assoc($get_modifier_type))
		while($getmodtyp = mysql_fetch_assoc($get_modifier_type))
		{
			$gtmdtypcnt--;	
			
			echo"
							<tr height='24'>
								<td align = 'right' valign = 'middle'>
									<font class='heading1'>
									".$getmodtyp[ability_modifier_type].":
								</td>
								
								<td width = '5%'>
								</td>
								
								<td align = 'left' valign = 'top'><select class='engine' name = '".$getmodtyp[ability_modifier_type]."'><option value = '0'>Choose ".$getmodtyp[ability_modifier_type]."</option>";
			
			$get_modifiers_list = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_type_id = '".$getmodtyp[ability_modifier_type_id]."' ORDER BY ability_modifier_short") or die("failed to get ab mods.");
			// while($getmodlst = mysql_fetch_assoc($get_modifiers_list))
			while($getmodlst = mysql_fetch_assoc($get_modifiers_list))
			{
				echo"<option value = '".$getmodlst[ability_modifier_id]."'>".$getmodlst[ability_modifier_short]."</option>";
			}
			
			echo"</select>";
			
			if($gtmdtypcnt >= 1)
			{
				echo"
									<br>
								</td>
				";
			}
		}
	
		echo"
								<td width = '2%'>
								&nbsp;
								</td>
							</tr>
							<tr>
								<td align = 'right' valign = 'middle' colspan = '3'>
									<input type='hidden' value='ab' name='current_expander'>
									<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
									<input class='submit3' type='submit' value='Add Modifier(s)' name='ab_edit_mod'>
								</td>
								</form>
							</tr>
						</table>
					</td>
		";
	}
	
	if($curusrslrprnk[slurp_rank_id] >= 5)
	{
		if($curab2[ability_library_status] <= 3)
		{
			if($curab2[ability_owner_id] == $usrnfo[user_id])
			{
				echo"
								<br>
								<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
									<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
										<td colspan = '3' align = 'left' valign = 'middle'>
											<font class='heading2'>
											&nbsp;ADD MODIFIERS
											</font>
										</td>
									</tr>
									<tr>
										<td colspan = '2' align = 'left' valign = 'top'>
											<br>
										</td>
									</tr>
									<form name = 'ab_edit_mod' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>
				";
				
				$get_modifier_type = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_type WHERE ability_modifier_type_id > '1' ORDER BY ability_modifier_type") or die("failed to get mod types.");
				$getmodtypcnt = mysql_num_rows($get_modifier_type);
				$gtmdtypcnt = $getmodtypcnt;
				// while($getmodtyp = mysql_fetch_assoc($get_modifier_type))
				while($getmodtyp = mysql_fetch_assoc($get_modifier_type))
				{
					$gtmdtypcnt--;	
					
					echo"
									<tr height='24'>
										<td align = 'right' valign = 'middle'>
											<font class='heading1'>
											".$getmodtyp[ability_modifier_type].":
										</td>
										
										<td width = '5%'>
										</td>
										
										<td align = 'left' valign = 'top'><select class='engine' name = '".$getmodtyp[ability_modifier_type]."'><option value = '0'>Choose ".$getmodtyp[ability_modifier_type]."</option>";
					
					$get_modifiers_list = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_type_id = '".$getmodtyp[ability_modifier_type_id]."' ORDER BY ability_modifier_short") or die("failed to get ab mods.");
					// while($getmodlst = mysql_fetch_assoc($get_modifiers_list))
					while($getmodlst = mysql_fetch_assoc($get_modifiers_list))
					{
						echo"<option value = '".$getmodlst[ability_modifier_id]."'>".$getmodlst[ability_modifier_short]."</option>";
					}
					
					echo"</select>";
					
					if($gtmdtypcnt >= 1)
					{
						echo"
											<br>
										</td>
						";
					}
				}
			
				echo"
										<td width = '2%'>
										&nbsp;
										</td>
									</tr>
									<tr>
										<td align = 'right' valign = 'middle' colspan = '3'>
											<input type='hidden' value='ab' name='current_expander'>
											<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
											<input class='submit3' type='submit' value='Add Modifier(s)' name='ab_edit_mod'>
										</td>
										</form>
									</tr>
								</table>
							</td>
						</tr>
				";
			}
		}
	}
	// end Add Modifiers table
	
	// drop a rule to separate the bottom nav buttons, and a table to make them not depend on the above columns
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	if($curab2[ability_library_status] <= 3)
	{
		if($curab2[ability_owner_id] == $usrnfo[user_id])
		{
			echo"
						<tr background='themes/Vanguard/images/back2b.gif' height='24'>
							<td align = 'left' valign = 'middle' colspan = '$subcol2_count'>
								<font class='heading2'>
								&nbsp;ADD EFFECTS
							</td>
						</tr>
						<tr>
							<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
								<br>
							</td>
						</tr>
						<form name = 'ab_effect_form' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>
			";
		
			$ab_base_eff_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id > '1' ORDER BY effect_type");
			$abbsefflstcnt = mysql_num_rows($ab_base_eff_list);
			
			// while($abbsefflst = mysql_fetch_assoc($ab_base_eff_list))
			while($abbsefflst = mysql_fetch_assoc($ab_base_eff_list))
			{
				if($abbsefflst[effect_type_support] == 0)
				{
					$effect_color2 = "white";
				}
				
				if($abbsefflst[effect_type_support] == 1)
				{
					$effect_color2 = "orange";
				}
				
				$req_effects = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '$curab2[ability_id]' AND effect_type_id = '$abbsefflst[effect_type_id]'");
				
				// required support
				// while($rqeffs = mysql_fetch_assoc($req_effects))
				while($rqeffs = mysql_fetch_assoc($req_effects))
				{
					echo"
						<tr height='24'>
							<td valign= 'middle' align = 'right' colspan = '3'>
								<font size = '2' color = '$effect_color2'>
								".$abbsefflst[effect_type]." ".roman($rqeffs[effect_type_tier]).": 
							</td>
							
							<td width = '5%'>
							<br>
							</td>
							
							<td align = 'left' valign = 'top'>
							<select class='engine' name ='base_".$abbsefflst[effect_type]."_id'><option value = '1'>Choose Effect for ".$abbsefflst[effect_type]." ".roman($rqeffs[effect_type_tier])."</option>";
		
					$list_effect_subs = mysql_query("SELECT * FROM ".$slrp_prefix."effect_subtype_effect_type WHERE effect_type_id = '".$abbsefflst[effect_type_id]."'") or die ("failed getting subtype realtion to base effect.");
					// while($lsteffsubs = mysql_fetch_assoc($list_effect_subs))
					while($lsteffsubs = mysql_fetch_assoc($list_effect_subs))
					{
						$effect_subtype_information = mysql_query("SELECT * FROM ".$slrp_prefix."effect_subtype WHERE effect_subtype_id = '".$lsteffsubs[effect_subtype_id]."'") or die ("failed getting effect sub info for ability.");
						// $effsubnfo = mysql_fetch_assoc($effect_subtype_information);
						$effsubnfo = mysql_fetch_assoc($effect_subtype_information);
						echo"<optgroup label= '".$effsubnfo[effect_subtype]."'>";
						
						$list_effects_by_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect_effect_subtype.effect_id = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '".$effsubnfo[effect_subtype_id]."' AND ".$slrp_prefix."effect.effect_tier <= '".$rqeffs[effect_type_tier]."' ORDER BY ".$slrp_prefix."effect.effect_tier, ".$slrp_prefix."effect.effect") or die ("failed getting effect realtion to subtype effect.");
						// while($lsteffbysub = mysql_fetch_assoc($list_effects_by_subtype))
						while($lsteffbysub = mysql_fetch_assoc($list_effects_by_subtype))
						{
							echo"<option value = '".$lsteffbysub[effect_id]."'>".roman($lsteffbysub[effect_tier])." - ".$lsteffbysub[effect]."</option>";
						}
					}
		
					echo"</select>";
				
					if($abbsefflstcnt >= 1)
					{
						echo"
									<br>
						";
					}
				}
			}
			
			echo"
							</td>
							<td width = '2%'>
							&nbsp;
							</td>
						</tr>
						<tr>
							<td align = 'right' valign = 'top' colspan = '7'>
								<input type='hidden' value='".$abbsefflst[effect_type_id]."' name='".$abbsefflst[effect_type]."_id'>
								<input type='hidden' value='".$rqeffs[effect_type_id]."' name='".$abbsefflst[effect]."_type_id'>
								<input type='hidden' value='".$abbsefflst[effect_tier]."' name='".$abbsefflst[effect]."_type_tier'>
								<input type='hidden' value='ab' name='current_expander'>
								<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
								<input class='submit3' type='submit' value='Add Base Effect(s)' name='ab_effect_form'>
							</td>
							</form>
						</tr>
			";
		}
	}
}

if(empty($_POST['from_pc_bs_eff_ntro']))
{
	$footer_cols = 7;
}
if(isset($_POST['from_pc_bs_eff_ntro']))
{
	$footer_cols = 9;
}

echo"
					</table>
				</td>
			</tr>
			<tr height='9'>
				<td align='right' colspan='3' width='100%'>
				</td>
			</tr>
			<tr height='24' background='themes/$ThemeSel/images/back2b.gif'>
			<td colspan = '7' width='100%'>
				<table cellspacing='0' cellpadding='0' border='0'>
					<form name = 'go_to_ab_list' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
					<td valign = 'middle' align='center'>
						<input type='hidden' value='ab' name='current_expander'>
						<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
						<input class='submit3' type='submit' value='Abilities List' name='go_to_ab_list'>
					</td>
					</form>
					
					<td width = '2%'>
					&nbsp;
					</td>
					
					<form name = 'go_home' method='post' action = 'modules.php?name=$module_name'>
					<td valign = 'middle' align='center'>
						<input type='hidden' value='1' name='ab_expander'>
						<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
						<input class='submit3' type='submit' value='Back to Main' name='go_home'>
					</td>
					</form>
					<td width = '2%'>
					&nbsp;
					</td>
";

if(isset($_POST['from_pc_bs_eff_ntro']))
{
	echo"				
							<form name = 'back_to_eff_typ_ntro' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
							<td valign='middle' align='center'>
								<input type='hidden' value='ab_char' name='current_expander'>
								<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
								<input class='submit3' type='submit' value='Back to Effect Types' name='back_to_eff_typ_ntro'>
							</td>
							</form>
							
							<td width = '2%'>
							&nbsp;
							</td>
	";
}
//else
//{
//	echo"				<td>
//							</td>
//							
//							<td width = '2%'>
//							&nbsp;
//							</td>
//	";
//}

if($current_pc_id >= 2)
{
	echo"
							<form name = 'pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
							<td align = 'center' valign = 'middle'>
								<input type='hidden' value='$current_pc_id' name='current_pc_id'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='char' name='current_expander'>
								<input class='submit3' type='submit' value='Back to $curpcnfo[creature]' name='go_to_edit'>
							</td>
							</form>
														
							<td width = '2%'>
							&nbsp;
							</td>
	";
}
if($current_pc_id <= 1)
{
	echo"
							<td align = 'center' valign = 'middle'>
							</td>														
							<td width = '2%'>
							&nbsp;
							</td>
	";
}
								
if(isset($_POST['library_id']))
{
	$libid = $_POST['library_id'];
	echo"
							<form name = 'ab_shop' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
							<td align='center' valign='middle'>
								<input type='hidden' value='$libid' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
								<input class='submit3' type='submit' value='Back to Library' name='ab_shop'>
							</td>
							</form>
							<td width = '2%'>
							&nbsp;
							</td>
	";
}
else
{
	echo"			
							<td>
							</td>	
							<td width = '2%'>
							&nbsp;
							</td>
	";
}

echo"
						</tr>
						</table>
					</td>
				</tr>
				</table>
			</td>
";
// end bottom buttons table and row

//end table in the main 5/6 pane and add space for the sidebar
echo"
		<tr>
	</table>
</td>
";

// start sidebar
if($curusrslrprnk[slurp_rank_id] <= 4)
{
	if($curab2[ability_id] >= 2)
	{
		echo"
<td width = '13%' align = 'right' valign = 'top'>
	<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
		<tr background='themes/Vanguard/images/back2b.gif' height='24'>
			<td valign = 'middle' width = '100%' align = 'center'>
			
			</td>
		</tr>
		<tr>
			<form name = 'subfocus_grp_del' method='post' action = 'modules.php?name=$module_name&file=obj_edit_form'>
			<td valign = 'top' width = '100%' align = 'right'>
				<font color = 'red' size = '2'>
				WARNING! Deleting $current_ab_name will remove all of its associations.
				<br>
				<input type='hidden' value='".$curab2[ability_id]."' name='delete_".$getfoc[focus_table]."_id'>
				<input type='hidden' value='$nav_title' name='nav_title'>
				<input type='hidden' value='ab_edit' name='nav_page'>
				<input type='hidden' value='$expander_abbr' name='current_expander'>
				<input type='hidden' value='1' name='confirm_delete'>
				<input class='submit3' type='submit' value='Delete $current_ab_name' name='subfocus_grp_del'>
				<br>
				<br>
			</td>
			</form>
		</tr>
		";
	}

	if(isset($_POST['keycode_expander']))
	{
		$keycode_expander = $_POST['keycode_expander'];
	}
	else
	{
		$keycode_expander = 1;
	}

	echo"
		<tr>
	";
	

	echo"<form name = 'show_hide_keycodes' method='post' action = 'modules.php?name=$module_name&file=ab_edit'><input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>";
	
	echo"
			<td width = '100%' align = 'right' valign='middle'>
	";
	
	if($keycode_expander == 1)
	{
		echo"<input type='hidden' value='0' name = 'keycode_expander'><input class='submit3' type='submit' value='Hide QR Code' name = 'show_hide_keycodes'> ";
	}
	
	if($keycode_expander == 0)
	{
		echo"<input type='hidden' value='1' name = 'keycode_expander'><input class='submit3' type='submit' value='Show QR Code' name = 'show_hide_keycodes'> ";
	}
	 
	echo"
			</td>
			</form>
		</tr>
	";

	// but only if the PC is already approved do they get the key
	if($curpcnfo[creature_status_id] == 4)
	{
		// if the current pc owns this ability
		if($currab2owncnt >= 1)
		{
			// get the known random key
			// while($currab2own = mysql_fetch_assoc($current_ability2_owned))
			while($currab2own = mysql_fetch_assoc($current_ability2_owned))
			{
				// echo"$currab2own[3], $currab2owncnt<br>";
				$approved_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random_id = '".$currab2own[ability_random_id]."'") or die("failed getting owned ab rand.");
				// while($apprvabrnd = mysql_fetch_assoc($approved_ability_random))
				while($apprvabrnd = mysql_fetch_assoc($approved_ability_random))
				{
					echo"
		<tr>
			<td width = '100%' align = 'right' valign = 'top'>
				<font color = '#7fffd4' size = '2'>
				".$apprvabrnd[object_random]."
				</font>
			</td>
		</tr>					
					";
				}
			}
		}
	}

	// get the known random key
	// while($currab2own = mysql_fetch_assoc($current_ability2_owned))
	while($currab2own = mysql_fetch_assoc($current_ability2_owned))
	{
		// echo"$currab2own[3], $currab2owncnt<br>";
		$known_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random_id = '".$currab2own[ability_random_id]."'") or die("failed getting owned ab rand.");
		//while($knwabrnd = mysql_fetch_assoc($known_ability_random))
		while($knwabrnd = mysql_fetch_assoc($known_ability_random))
		{
			$approved_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_random_id = '".$knwabrnd[object_random_id]."' AND ability_id = '".$currab2own[ability_id]."'") or die("failed getting owned ab rand.");
			//$apprvabrnd = mysql_fetch_assoc($approved_ability_random);
			$apprvabrnd = mysql_fetch_assoc($approved_ability_random);
			
			if($apprvabrnd[ability_random_id] = $knwabrnd[object_random_id])
			{
				$rnd_color = "orange";
			}
			
			if($apprvabrnd[ability_random_id] != $knwabrnd[object_random_id])
			{
				$rnd_color = "#7fffd4";
			}
			
			echo"
		<tr>
			<td width = '100%' align = 'right' valign = 'top'>
				<font size = '2' color='$rnd_color'>".$curpcnfo[creature]."'s key: ".$knwabrnd[object_random]."</font>
			</td>
		</tr>
			";
		}
	}
		
	$current_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$curab2[ability_id]' AND object_focus_id = '$getfoc[focus_id]' AND object_random_current = '1' ORDER BY object_random_timestamp DESC");
	// while($unkabrnd = mysql_fetch_assoc($unknown_ability_random))
	$curabrndcnt = mysql_num_rows($current_ability_random);
	if($curabrndcnt >= 1)
	{
		$random_counter = 0;
		while($curabrnd = mysql_fetch_assoc($current_ability_random))
		{
			if($keycode_expander == 1)
			{
				// echo"RNDu: $abrnd<br>";
//				echo"
//			<tr>
//				<td width = '100%' align = 'right' valign = 'top'>
//					<font color = '#7fffd4' size = '2'>
//					<font color='#7fffd4'>
//					".$curabrnd[object_random]."
//					</font>
//				</td>
//			</tr>";
			
	echo"
		<tr>
			<td width = '100%' align = 'right' valign = 'top'>
				<img src='https://chart.googleapis.com/chart?cht=qr&chl=$curabrnd[object_random]&chs=180x180&choe=UTF-8&chld=L|2' alt='qr code'>
			</td>
		</tr>
		<tr height = '9'>
			<td width = '100%' align = 'right' valign = 'top'>
				
			</td>
		</tr>
				";
			}
			
			$random_counter++;
			
			if($random_counter == 1)
			{
				$for_adding_to_pc = $unkabrnd[object_random];
			}
		}
	}

	if($curab2[ability_id] >= 2)
	{
		echo"
<td width = '13%' align = 'right' valign = 'top'>
	<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
		<tr background='themes/Vanguard/images/back2b.gif' height='24'>
			<td valign = 'middle' width = '100%' align = 'center'>
			
			</td>
		</tr>
		";
	}

	$get_subfocus_modifiers = mysql_query("SELECT ".$slrp_prefix."ability_modifier_subfocus.* FROM ".$slrp_prefix."ability_modifier_subfocus INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_id = '2' AND ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$curab2[ability_id]' AND ".$slrp_prefix."ability.ability_id > '1'") or die("failed to get mods for subfoci relations.");
	$getsubmodcnt = mysql_num_rows($get_subfocus_modifiers);
	// echo "modcnt: $getsubmodcnt<br>ab_id: $curab2[ability_id] <br>";
	if($getsubmodcnt >= 1)
	{	
	  while($getsubmod = mysql_fetch_assoc($get_subfocus_modifiers))
		{
	//		echo "abmod_id: $getsubmod[ability_modifier_subfocus_id], $getsubmod[ability_modifier_id], $getsubmod[subfocus_id], $getsubmod[focus_id], $getsubmod[focus_exclusion_id]<br>";
	//		// echo "abmod_id: $getsubmod[0], $getsubmod[1], $getsubmod[2], $getsubmod[3], $getsubmod[4]<br>";
				
			if($getsubmod[ability_modifier_id] >= 2)
			{
				$get_subfocus_modifier_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '".$getsubmod[ability_modifier_id]."'") or die ("failed getting submod info.");
				// $gtsbmdnfo = mysql_fetch_assoc($get_subfocus_modifier_info);
				$gtsbmdnfo = mysql_fetch_assoc($get_subfocus_modifier_info);
				
				$get_subfocus_modifier_type = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_type WHERE ability_modifier_type_id = '$gtsbmdnfo[ability_modifier_type_id]'") or die ("failed to get relation modifier type.");
				// $gtsbmdtyp = mysql_fetch_assoc($get_subfocus_modifier_type);
				$gtsbmdtyp = mysql_fetch_assoc($get_subfocus_modifier_type);
				// echo "abmod: $gtsbmdnfo[ability_modifier_short],  $gtsbmdnfo[ability_modifier_id], $getsbmdtyp[ability_modifier_type]<br>";
				echo"
			<tr>
				<td width = '100%' align = 'right' valign = 'top'>
				";
				
				echo"
					<font class='heading1'>";
				
				
				if($curusrslrprnk[slurp_rank_id] <= 5)
				{
					echo"
					<form name = 'mod_relations' method = 'post' action = 'modules.php?name=$module_name&file=mod_edit'>
					<input type='hidden' value='1' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$gtsbmdnfo[ability_modifier_id]' name='$gtsbmdtyp[ability_modifier_type]'>
					<input class='submit3' type='submit' value='";
					echo"($gtsbmdnfo[ability_modifier_value]) ";
				}
				
				echo"$gtsbmdnfo[ability_modifier_short]";
				
			
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"' name='mod_relations'></form>";
					$abilities_using_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '".$gtsbmdnfo[ability_modifier_id]."'") or die ("failed getting abilities using mods.");
				}
				if($curusrslrprnk[slurp_rank_id] >= 5)
				{
					$abilities_using_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '".$gtsbmdnfo[ability_modifier_id]."' AND ".$slrp_prefix."ability.ability_library_status = '4' ") or die ("failed getting abilities using mods.");
				}
				
				$abusmodcnt = mysql_num_rows($abilities_using_modifier);
				if($abusmodcnt >= 1)
				{
					echo"<br><br><font class='heading1'>Abilities Using <br><i>$gtsbmdnfo[ability_modifier_short]</i>:";
				}
				while($abusmod = mysql_fetch_assoc($abilities_using_modifier))
				{
					echo"
					<br><a href='modules.php?name=$module_name&file=ab_edit&current_pc_id=$curpcnfo[creature_id]&current_expander=$expander_abbr&race_desc_expander=$race_desc_expander&compeff_expander=$compeff_expander&compab_expander=$compab_expander&ntro_expander=$ntro_expander&recipe_expander=$recipe_expander&materials_expander=$materials_expander&items_expander=$items_expander&current_ab_id=$abusmod[ability_id]' class='default'>$abusmod[ability]</a>
					";
				}
		
				echo"
					<br>
					<br>
				</td>
			</tr>
				";
			}
		}
	}	

	$all_pcs_in_order = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id > '1' AND ".$slrp_prefix."creature.creature_status_id = '4' ORDER BY creature") or die ("Failed getting PC list for ability add, in order");
	$allpcsinordrcnt = mysql_num_rows($all_pcs_in_order);
	if($allpcsinordrcnt >= 1)
	{
		echo"
		<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
			<td width = '100%' align = 'right' valign = 'middle'>
				<font class='heading2'>
				&nbsp;ADMIN ADD to PC
			</td>
		</tr>
		<form name = 'add_ab_to_pc' method = 'post' action = 'modules.php?name=$module_name&file=pc_eff_typ_form'>
		<tr>
			<td width = '100%' align = 'right' valign = 'top'>				
				<select class='engine' name = 'current_pc_id'>";
	
		while($allpcsinordr = mysql_fetch_assoc($all_pcs_in_order))
		{
			$get_character_list = mysql_query("SELECT ".$slrp_prefix."creature.creature_id FROM ".$slrp_prefix."creature INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature.creature_id = ".$slrp_prefix."creature_ability.creature_id WHERE ".$slrp_prefix."creature.creature_id = '$allpcsinordr[creature_id]' AND ".$slrp_prefix."creature.creature_id NOT IN(SELECT creature_id FROM ".$slrp_prefix."creature_ability WHERE ".$slrp_prefix."creature_ability.ability_id = '$curab2[ability_id]')") or die("failed to get creature list for ability add.");
			$getcharlst = mysql_fetch_assoc($get_character_list);
			$getcharlstcnt = mysql_num_rows($get_character_list);				
			if($getcharlstcnt >= 1)
			{
				$list_character_instance = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id = '$getcharlst[creature_id]'") or die ("Failed getting PC list for ability add");
				$lstcharinst = mysql_fetch_assoc($list_character_instance);
				
				echo"<option value ='$lstcharinst[creature_id]'>$lstcharinst[creature]</option>";
			}
		}
		
		echo"
				</select>
				<br>
				<input type='hidden' value='7' name='current_focus_id'>
				<input type='hidden' value='$expander_abbr' name='current_expander'>
				<input type='hidden' value='$curab2[ability_id]' name='core_ab_id'>
				<input type='hidden' value='1' name='admabcode'>
				<input type='hidden' value='$for_adding_to_pc' name='newabcode'>
				<input type='hidden' value='1' name='back_to_ab_edit'>
				<input class='submit3' type='submit' value='Add to Character' name='creature_ownership'>
			</td>
			</form>
		</tr>
		";
	}
	
	$get_character_ownership = mysql_query("SELECT * FROM ".$slrp_prefix."creature INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature.creature_id = ".$slrp_prefix."creature_ability.creature_id WHERE ".$slrp_prefix."creature_ability.ability_id = '$curab2[ability_id]'") or die("failed to get creature ownership for ability relations.");
	$getcharownshpcnt = mysql_num_rows($get_character_ownership);
	if($getcharownshpcnt >= 1)
	{
		echo"
		<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
			<td width = '100%' align = 'right' valign = 'middle'>
				<font class='heading2'>
				&nbsp;KNOWN BY...
			</td>
		</tr>
		";
		
		while($getcharownshp = mysql_fetch_assoc($get_character_ownership))
		{
			echo"
		<tr>
			<form name = 'creature_ownership' method = 'post' action = 'modules.php?name=$module_name&file=pc_edit_new'>				
			<td width = '100%' align = 'right' valign = 'top'>
				<input type='hidden' value='$getcharownshp[creature_id]' name='current_pc_id'>
				<input type='hidden' value='7' name='current_focus_id'>
				<input type='hidden' value='$expander_abbr' name='current_expander'>
				<input class='submit3' type='submit' value='$getcharownshp[creature]' name='creature_ownership'>
			</td>
			</form>
		</tr>
			";	
		}			
	}
	
	// end sidebar table and cell
	echo"
	";
}

// end main row
echo"
			</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>