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
$nav_title = "SLURP Meta";
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
	$col1_count = 3;
	$subcol1_count = 3;
	$subcol2_count = 3;
}

// echo"1cc: $col1_count, 1sc: $subcol1_count, 2sc: $subcol2_count<br>";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	$wide_align = "right";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	$wide_align = "left";
}

//the row that holds messages at the top

echo"
<tr>
	<td colspan = '$col1_count' align = 'left' valign = 'top'>
		<table width = '100%'>
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
					<li>$existing_subfocus_name is already a(n) $get_focus_name. Your entry will be named <i>$newabname (#$serialized)</i>.
					<hr>
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
			
			// echo"tmplt_ab_nm:$template_ability_name, new: $newabname<br> ".$tmpltabnfo[ability_cost_hth].", ".$tmpltabnfo[ability_cost_mna].", ".$tmpltabnfo[ability_cost_psy].", ".$tmpltabnfo[ability_verbal]."<br>desc: $template_ability_desc<br>".$tmpltabnfo[ability_desc]."<br> ".$tmpltabnfo[ability_modifier_id].", ".$tmpltabnfo[ability_status_id].", ".$slrpnfo[slurp_id].", 10req: ".$tmpltabnfo[ability_requires_ability_id].", ".$tmpltabnfo[ability_restricted].", ".$tmpltabnfo[ability_tier].", ".$tmpltabnfo[ability_capacity].", ".$tmpltabnfo[ability_min_rank].", 15unlim: ".$tmpltabnfo[ability_unlimited_uses].", $template_ability_short, $template_ability_short_desc, ".$tmpltabnfo[ability_set_id]."<br>";
			
			$copy_ability = mysql_query("INSERT INTO ".$slrp_prefix."ability (ability,ability_cost_hth,ability_cost_mna,ability_cost_psy,ability_verbal,ability_desc,ability_modifier_id,ability_status_id,ability_slurp_id,ability_requires_ability_id,ability_restricted,ability_tier,ability_capacity,ability_min_rank,ability_unlimited_uses,ability_short,ability_short_desc,ability_set_id,ability_library_status) VALUES ('$newabname','".$tmpltabnfo[ability_cost_hth]."','".$tmpltabnfo[ability_cost_mna]."','".$tmpltabnfo[ability_cost_psy]."','$template_ability_verbal','$template_ability_desc','".$tmpltabnfo[ability_modifier_id]."','".$tmpltabnfo[ability_status_id]."','".$slrpnfo[slurp_id]."','".$tmpltabnfo[ability_requires_ability_id]."','".$tmpltabnfo[ability_restricted]."','".$tmpltabnfo[ability_tier]."','".$tmpltabnfo[ability_capacity]."','".$tmpltabnfo[ability_min_rank]."','".$tmpltabnfo[ability_unlimited_uses]."','$template_ability_short','$template_ability_short_desc','".$tmpltabnfo[ability_set_id]."','".$tmpltabnfo[ability_library_status]."')") or die("failed to copy ab template info.");
			
			$started_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability = '$newabname' AND ability_status_id = '".$tmpltabnfo[ability_status_id]."' AND ability_cost_hth = '".$tmpltabnfo[ability_cost_hth]."' AND ability_cost_mna = '".$tmpltabnfo[ability_cost_mna]."' AND ability_cost_psy = '".$tmpltabnfo[ability_cost_psy]."'") or die("failed to get started ab info.");
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
			$insert_new_ability = mysql_query("INSERT INTO ".$slrp_prefix."ability (ability,ability_status_id,ability_restricted,ability_slurp_id) VALUES ('$newabname','4','$ab_restr','".$slrpnfo[slurp_id]."')") or die ("failed inserting new req char.");
			$insnewab = mysql_fetch_assoc($insert_new_ability);
			
			$verify_new_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability = '$newabname' AND ability_status_id = '4' AND ability_restricted = '$ab_restr' AND ability_slurp_id = '".$slrpnfo[slurp_id]."'") or die ("failed verifying new ability.");
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
				
				$new_book_entry = mysql_query("INSERT INTO ".$slrp_prefix."item_book (item_id,ability_id,ability_object_random_id) VALUES ('$gtallbks[item_id]','$current_ab_id','$bkrnd_entry')") or die ("failed inserting new book.");
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

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<form name = 'ab_info_edit' method='post' action = 'modules.php?name=$module_name&file=ab_edit_form'>";
}

echo"
<tr>
	<td align = 'left' valign = 'top' width = '83%'>
		<table width = '100%'>
			<tr>
				<td colspan = '$col1_count' align = '$left' valign = 'top'>
					<table>
						<tr>
							<td colspan = '$subcol1_count' align = '$wide_align' valign = 'top'>";

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

// graphic handler for all objects
$current_focus_id = $getfoc[focus_id];
$current_object_id = $current_ab_id;
include("modules/$module_name/includes/fm_obj_graphic.php");

echo"<font size = '3' color = 'white'><font size = '1' color = '#7fffd4'>Ability #$current_ab_id:</font><br>";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<font color = 'black'><textarea name ='ab_name' rows = '3' cols = '35'>";
}

echo"$current_ab_name";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"</textarea>";
}

echo"<br><font size = '1' color = '#7fffd4'>Verbal Component:</font><br>";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<textarea name ='ab_verbal' rows = '5' cols = '50'>";
}

echo"$current_ab_verbal";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"</textarea>";
}

echo"<br><font size = '1' color = '#7fffd4'>Description:</font><br>";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<textarea name ='ab_desc' rows = '5' cols = '50'>";
}

echo"$current_ab_desc";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"</textarea>";

	echo"<br><font color = 'red'>Restricted? <input type='checkbox' value='1' name='ab_restricted'";
	
	if($curab2[ability_restricted] == 1)
	{
		echo"checked";
	}

	echo"></font>";
}

echo"<br><br>";

$get_current_library_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status INNER JOIN ".$slrp_prefix.$getfoc[focus_table]." ON ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_library_status = ".$slrp_prefix."slurp_status.slurp_status_id WHERE ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_id = '".$curab2[ability_id]."'") or die ("failed getting current library status.");
// $gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);
$gtcurrlibstat = mysql_fetch_assoc($get_current_library_status);

echo"<font size = '1' color = '#7fffd4'>Library Status:</font> . . . ";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
	<select class='engine' name = 'ab_library_status'>
	<option value = '".$gtcurrlibstat[slurp_status_id]."'>";
}

echo"$gtcurrlibstat[slurp_alt_status1]";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"</option>";
			
	$get_slurp_library_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status ORDER BY slurp_status_id DESC") or die("failed to get rank list.");
	// while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
	while($gtslrplibstat = mysql_fetch_assoc($get_slurp_library_status))
	{
		echo"<option value = '".$gtslrplibstat[slurp_status_id]."'>".$gtslrplibstat[slurp_alt_status1]."</option>";
	}
	
	echo"</select>";
}

$get_current_minimum_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix.$getfoc[focus_table]." ON ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_id = '".$curab2[ability_id]."'") or die ("failed getting current min rank to view.");
// $gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);
$gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<br><br><font size = '1' color = '#7fffd4'>Miniminum Rank to View:</font> . . . <select class='engine' name = 'min_rank'><option value = '".$gtcurrmnrnk[slurp_rank_id]."'>".$gtcurrmnrnk[slurp_rank]."</option>";
	
	$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id > '".$curusrslrprnk[slurp_rank_id]."' ORDER BY slurp_rank_id DESC") or die("failed to get rank list.");
	// while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
	while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
	{
		echo"<option value = '".$gtslrprnk[slurp_rank_id]."'>".$gtslrprnk[slurp_rank]."</option>";
	}
	
	echo"</select>";
}

echo"<br><br>";

$get_special_effects = mysql_query("SELECT * FROM ".$slrp_prefix."effect_special WHERE effect_special_id > '1' AND effect_special_id NOT IN (SELECT effect_special_id FROM ".$slrp_prefix."ability_effect_special WHERE ability_id = '".$curab2[ability_id]."') ORDER BY effect_special") or die ("failed getting sp effects list.");
$gtspcleffscnt = mysql_num_rows($get_special_effects);

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<font size = '1' color = '#7fffd4'>Choose Special Effect</font> . . . <select class='engine' name = 'add_spcl_eff'><option value = '1'>None</option>";

	// while($gtspcleffs = mysql_fetch_assoc($get_special_effects))
	while($gtspcleffs = mysql_fetch_assoc($get_special_effects))
	{
		echo"<option value = '".$gtspcleffs[effect_special_id]."'>".$gtspcleffs[effect_special]."</option>";
	}
		
		echo"</select><br><br>";
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
	echo"<font color = 'yellow'>".$gtcurrspcleff[effect_special]."" ;
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		echo" . . . <input type='checkbox' value='".$gtcurrspcleff[effect_special_id]."' name='del_".$gtcurrspcleff[effect_special]."_id'></font><br>";
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
							<hr>Set Random Key? <input type='checkbox' value='1' name='new_object_random'> . . . 
							<input type='hidden' value='ab' name='current_expander'>
							<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
							<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
							<input type='submit' value='Submit' name='ab_info_edit'>
							<hr>
							</form>
	";
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
							</td>
						</tr>
					</table>
";
// end table holding the main ability info

echo"
					<br>
";

// begin table that allows req columns to play freely
echo"
					<table width = '100%'>
						<tr>
							<td align = 'left' valign = 'top'>
								<hr>
";

// begin table holding Requirements info
echo"
								<table width = '100%'>
									<tr>
										<td align = 'left' valign = 'top'>
											<font color = 'yellow' size = '2'>
											REQUIREMENTS
											</font>
										</td>
										
										<td width = '2%'>
										</td>
										
										<td align = 'left' valign = 'top'>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
											<font color = 'yellow' size = '2'>
											Add Effect Type
											</font>
	";
}

echo"
										</td>
									</tr>
									<tr>
										<td colspan = '$subcol1_count' align'= 'left' valign = 'top'>
											<hr>
										</td>
									</tr>
									<tr>
										<td>
											<table>
";
// this new tbale is to make a nicer arrangement of reqs and drop-down boxes

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
									<form name = 'ab_edit_form' method = 'post' action = 'modules.php?name=$module_name&file=ab_edit_form'>
	";
}

// print either a dropdown for staff or just the name for players
$eff_typ_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id > '1' ORDER BY effect_type_support, effect_type");
while($efftyplst = mysql_fetch_assoc($eff_typ_list))
{
//	echo"$efftyplst[effect_type] $efftyplst[effect_type_support]<br>";
//	$required_effects = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '".$curab2[ability_id]."' AND effect_type_id = '$efftyplst[effect_type_id]'");
	
	// staff will see drop down options
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		if($efftyplst[effect_type_support] == 1)
		{
			$check_ability_support = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '".$curab2[ability_id]."' AND effect_type_id = '".$efftyplst[effect_type_id]."'") or die ("failed verifying support relation.");
			$chkabsuppcnt = mysql_num_rows($check_ability_support);
//			echo"$chkabsuppcnt<br>";
			if($chkabsuppcnt == 0)
			{
				$new_support_effect = mysql_query("INSERT INTO ".$slrp_prefix."ability_effect_type (ability_id,effect_type_id,effect_type_tier) VALUES ('".$curab2[ability_id]."','".$efftyplst[effect_type_id]."','1')") or die ("failed inserting new support for unsupported ability.");
//				echo"inserted<br>";
				$check_ability_support_again = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type INNER JOIN ".$slrp_prefix."ability_effect_type ON ".$slrp_prefix."ability_effect_type.effect_type_id = ".$slrp_prefix."effect_type.effect_type_id WHERE ".$slrp_prefix."ability_effect_type.ability_id = '".$curab2[ability_id]."' AND ".$slrp_prefix."ability_effect_type.effect_type_id = '".$efftyplst[effect_type_id]."'") or die ("failed verifying support relation.");
				$chkabsuppagn = mysql_fetch_assoc($check_ability_support_again);
			}
			if($chkabsuppcnt == 1)
			{
				$check_ability_support_again = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type INNER JOIN ".$slrp_prefix."ability_effect_type ON ".$slrp_prefix."ability_effect_type.effect_type_id = ".$slrp_prefix."effect_type.effect_type_id WHERE ".$slrp_prefix."ability_effect_type.ability_id = '".$curab2[ability_id]."' AND ".$slrp_prefix."ability_effect_type.effect_type_id = '".$efftyplst[effect_type_id]."'") or die ("failed verifying support relation.");
				$chkabsuppagn = mysql_fetch_assoc($check_ability_support_again);
			}
		}
	}
	
	$required_effects_again = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '".$curab2[ability_id]."' AND effect_type_id = '".$efftyplst[effect_type_id]."'") or die ("failed getting effects again.");
	
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		$highest_effect_temp = 1;
		// list required base effects and verify starting support
		// while($rqeffagn = mysql_fetch_assoc($required_effects_again))
		while($rqeffagn = mysql_fetch_assoc($required_effects_again))
		{
			if($rqeffagn[effect_type_tier] >= $highest_effect_temp)
			{
				$highest_effect_temp = $rqeffagn[effect_type_tier];
			}

			// echo"eff: $rqeffagn[effect_tier] hi: $highest_effect_temp<br>";
			echo"
											<tr>
												<td align = 'left' valign = 'top'>
													<font size = '2' color = '";
	
			if($efftyplst[effect_type_support] == 0)
			{
			echo"white";
			}
		
			if($efftyplst[effect_type_support] == 1)
			{
			echo"orange";
			}
		
			echo"'>
													".$efftyplst[effect_type]." ".roman($rqeffagn[effect_type_tier])."</td>
													<td width = '2%'>
													</td>
													
													<td align = 'left' valign = 'top'>
													<select class='engine' name = '".$efftyplst[effect_type]."'>
													<option value = '".$rqeffagn[effect_type_tier]."'>".$rqeffagn[effect_type_tier]."</option>";
			
			if($efftyplst[effect_type_support] == 0)
			{
				$chrmin = $slrpnfo[slurp_effect_type_min];
				$chrmax = $slrpnfo[slurp_effect_type_max];
			}		
			
			if($efftyplst[effect_type_support] == 1)
			{
				$chrmin = $slrpnfo[slurp_effect_type_min];
				$chrmax = $slrpnfo[slurp_effect_type_max];
			}
		
			while($chrmin <= $chrmax)
			{
				echo"<option value = '$chrmin'>$chrmin</option>";
				
				$chrmin++;
			}
			
			echo"
			</select>
			<br>
			<br>
			<input type='hidden' value='".$efftyplst[effect_type_id]."' name='".$efftyplst[effect_type_id]."'>
			</td>
		</tr>
			";
		}
		
		
	}

	if($curusrslrprnk[slurp_rank_id] >= 5)
	{
		while($rqeffagn = mysql_fetch_assoc($required_effects_again))
		{
			$reqeffagncnt = mysql_num_rows($required_effects_again);
			
			echo"
												<tr>
													<td>
														<font size = '2' color = 'white'>
														".$efftyplst[effect_type]." ".roman($rqeffagn[effect_tier])."
														</font>
													</td>
												</tr>
			";
		}
	}

}

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
												<tr>
													<td colspan = '3' align = 'right' valign = 'top'>
														<hr>
														<input type='hidden' value='ab' name='current_expander'>
														<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
														<input type='submit' value='Change' name='ab_edit_form'>
														</form>
														<hr>
													</td>
												</tr>
	";
}

$effects_capacity = mysql_query("SELECT SUM(effect_type_tier) FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '".$curab2[ability_id]."'") or die ("failed getting ability capacity.");
// $effscap = mysql_fetch_assoc($effects_capacity);
$effscap = mysql_fetch_array($effects_capacity, MYSQL_NUM);

$update_capacity = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_capacity = '$effscap[0]' WHERE ability_id = '".$curab2[ability_id]."'") or die ("failed updating capacity.");

$verify_capacity = mysql_query("SELECT ability_capacity FROM ".$slrp_prefix."ability WHERE ability_id = '".$curab2[ability_id]."'") or die ("failed verifying capacity.");
// $vercap = mysql_fetch_assoc($verify_capacity);
$vercap = mysql_fetch_assoc($verify_capacity);

echo"
												<tr>
													<td>
														<font size = '3' color = '#00B2EE'>
														<b>Capacity: ".$vercap[ability_capacity]."</b>
														</font>
													</td>
												</tr>
											</table>
										</td>
";

// split the requirements pane, then make another table to space the 'add new reqs' list
echo"
										<td width = '2%'>
										</td>
										
										<td valign = 'top' align = 'left'>
											<table>
												<tr>
													<td valign = 'top' align = 'left'>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<form name = 'new_req' method = 'post' action = 'modules.php?name=$module_name&file=ab_edit_form'><select class='engine' name = 'new_req_eff'>";

	$requirements_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id > '1' AND effect_type_support = '0' AND effect_type_id NOT IN (SELECT effect_type_id FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '".$curab2[ability_id]."') ORDER BY effect_type") or die ("failed getting req list.");
	while($reqlist = mysql_fetch_assoc($requirements_list))
	{
		echo"<option value = '".$reqlist[effect_type_id]."'>".$reqlist[effect_type]."</option>";		
	}

	echo"</select> . . . <select class='engine' name = 'new_req_tier'>";

	$abcharmax = $slrpnfo[slurp_effect_type_max];
	$abcharmin = 1;

	while($abcharmax >= $abcharmin)
	{
		echo"<option value = '$abcharmin'>$abcharmin</option>";
		
		$abcharmin++;
	}

	echo"</select>
													</td>
												</tr>
												<tr>
													<td align = 'right' valign = 'top'>
														<hr>
														<input type='hidden' value='ab' name='current_expander'>
														<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
														<input type='submit' value='Add' name='new_req'>
														<hr>
														</form>
	";
}

// end the Add Reqs table and the Reqs pane container table, and make a rule inside th pane
echo"
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<hr>
							</td>
						</tr>
					</table>
";
// end of the requirements section altogether


//if ranked enough, show the Add Modifiers pane
if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
					<br>
					<table width = '100%'>
						<tr>
							<td colspan = '$subcol1_count' align = 'left' valign = 'top'>
								<font color = 'yellow' size = '2'>
								ADD MODIFIERS
								</font>
							</td>
						</tr>
						<tr>
							<td colspan = '$subcol1_count' align = 'left' valign = 'top'>
								<hr>
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
						<tr>
							<td align = 'left' valign = 'top'>
								<font color = '#7fffd4' size = '2'>
								".$getmodtyp[ability_modifier_type].":
							</td>
							
							<td width = '2%'>
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
								<hr>
							</td>
			";
		}
	}

	echo"
							<td width = '2%'>
							</td>
						</tr>
						<tr>
							<td align = 'right' valign = 'top' colspan = '$subcol1_count'>
								<hr>
								<input type='hidden' value='ab' name='current_expander'>
								<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
								<input type='submit' value='Add Modifier(s)' name='ab_edit_mod'>
								<hr>
							</td>
							</form>
						</tr>
					</table>
				</td>
	";
}

// end Add Modifiers table and left pane

// this is the middle break of the main page panes
echo"
				<td align = 'left' valign = 'top' width = '2%'>
				</td>
";

// begin right main pane and containing table; this is the header row; there are not tables for each section going forward; just new rows in this pane
echo"
				<td align = 'left' valign = 'top' colspan = '3'>
					<table width = '100%'>
						<tr>
							<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
								<hr>
							</td>
						</tr>
						<tr>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
							<td align = 'left' valign = 'top' width = '10%'>
								<font color = 'yellow' size = '2'>
								REMOVE
								</font>
							</td>
							
							<td width = '2%'>
							</td>
	";
}

echo"
							<td align = 'left' valign = 'top' width = '10%'>
								<font color = 'yellow' size = '2'>
								WEIGHT
								</font>
							</td>
							
							<td width = '2%'>
							</td>
							
							<td width = '76%' align = 'left' valign = 'top'>
								<font color = 'yellow' size = '2'>
								EFFECT
							</td>
						</tr>
						<tr>
							<td colspan = '$subcol2_count'>
								<hr>
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
	// while($geteff = mysql_fetch_assoc($get_effect))
	while($geteff = mysql_fetch_assoc($get_effect))
	{
		$effect_modifier_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '".$geteff[effect_modifier_id]."'") or die ("failed getting effect modifier info for listing.");
		// $effmodnfo = mysql_fetch_assoc($effect_modifier_info);
		$effmodnfo = mysql_fetch_assoc($effect_modifier_info);
		
		$effect_type_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id = '".$geteff[effect_type_id]."'") or die ("failed getting effect type info for listing.");
		// $efftypenfo = mysql_fetch_assoc($effect_type_info);
		$efftypenfo = mysql_fetch_assoc($effect_type_info);
		
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
							<input type='submit' value='-' name='ab_del_efftyp_form'>
						</td>
						</form>
						
						<td width = '2%' align = 'left' valign = 'top'>
						</td>
			";
		}
		
		
		// print the value and description
		echo"
						<td width = '10%' align = 'left' valign = 'top'>
							<font color = '$effect_color' size = ''>
							$printed_weight
							</font>
						</td>
						
						<td width = '2%' align = 'left' valign = 'top'>
						</td>
						
						<td width = '76%' align = 'left' valign = 'top'>
							<font color = '$effect_color' size = '2'>
							<li>$effmodnfo[ability_modifier_short]
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
							<td colspan = '$subcol2_count' align = 'left' valign = 'top'>
								<font color = 'red' size = '2'>
								<li>This Ability needs at least one Base Effect.
							</td>
						</tr>
	";
}
	
echo"
						<tr>
							<td colspan = '$subcol2_count' align = 'left' valign = 'top'>
								<hr>
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
						<tr>
	";

	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		echo"
							<td align = 'left' valign = 'top' width = '10%'>
								<font color = 'yellow' size = '2'>
								REMOVE
							</td>
							
							<td width = '2%'>
							</td>
		";
	}	

	echo"
							<td align = 'left' valign = 'top' width = '10%'>
								<font color = 'yellow' size = '2'>
								WEIGHT
							</td>
							
							<td width = '2%'>
							</td>
							
							<td width = '76%' align = 'left' valign = 'top'>
								<font color = 'yellow' size = '2'>
								MODIFIERS
							</td>
						</tr>
						<tr>
							<td colspan = '$subcol2_count'>
								<hr>
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
							<input type='submit' value='-' name='ab_del_mod_form'>
						</td>
						</form>
						
						<td width = '2%' align = 'left' valign = 'top'>
						</td>
			";
		}
		
		echo"<td width= '10%' valign = 'top' align = 'left'>";
		
		
		
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
		
		echo"
			<font color = 'white' size = '3'>
			$printed_mod_cost
			</font>
		
			</td>
			
			<td width = '2%'>
			</td>
			
			<td align = 'left' valign = 'top' width = '76%'>
			<font size = '2'>
		";
		
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
									<hr>
								</td>
							</tr>
							<tr>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
								<td width = '10%' valign= 'top' align = 'left'>			
								</td>
								
								<td width = '2%'>
								</td>
	";
}

echo"
								<td align = 'left' valign = 'top' width = '10%'>
									<font size = '1' color = 'yellow'>
									SUBTOTAL
									</font>
								</td>
								
								<td align = 'left' valign = 'top' width = '2%'>
								</td>
								
								<td align = '$wide_align' valign = 'top' width = '76%'>
									<font size = '2' color = 'yellow'>
									COST
									</font>
								</td>
							</tr>
							<tr>
								<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
									<hr>
								</td>
							</tr>
							<tr>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
								</td>
								
								<td width = '2%'>
								</td>
								
								<td width = '10%' valign= 'top' align = 'left'>
	";
}

echo"
								<td align = 'left' valign = 'top' width = '10%'>
									<font size = '3' color = 'white'>
									$cost_count
									<br>
									<br>
									<font size = '1'>
									Weight Mod:
									<br><font color = 'purple' size = '3'><b>$count_sub_weight</b></font></font>
";

echo"
							</td>
							
							<td align = 'left' valign = 'top' width = '2%'>
							</td>
";

$get_attributes_list = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id > '1'") or die ("failed getting attr_type list.");

$getattrbtlstcnt = mysql_num_rows($get_attributes_list);

// for plot and rules and admins only
if($curusrslrprnk[slurp_rank_id] <= 4)
{
	// these numbers manage the dimensions of the table
	// Add one extra to $column_max between each text field for the 2% spacer
	// 3 will give 2 cols, 5 will give 3, 7 will give 4, etc.
	
	$column_count = 1;
	$column_max = 7;
	
	$button_count = 0;
	
	echo"<form name = 'ab_ab_attr' method = 'post' action = 'modules.php?name=$module_name&file=ab_edit_form'>";
	echo"<td align = 'right' valign = 'top'>";

	if($curab2[ability_set_id] == 22)
	{
		echo"<font color = 'orange' size = '1'><b>$highest_effect_tier / day</b></font><br>";
	}
	
	//  If there is nothing multiplying by zero
	if($cost_multiplier != 0)
	{
		// and the cost_weight is above zero
		if($cost_count <= 0)
		{
			// get the sum of the effect types to determine charges and unlimited thresholds
			$required_effect_types3 = mysql_query("SELECT SUM(".$slrp_prefix."ability_effect_type.effect_type_tier) FROM ".$slrp_prefix."ability_effect_type INNER JOIN ".$slrp_prefix."effect_type ON ".$slrp_prefix."effect_type.effect_type_id = ".$slrp_prefix."ability_effect_type.effect_type_id WHERE ".$slrp_prefix."ability_effect_type.ability_id = '$curab2[ability_id]' AND ".$slrp_prefix."effect_type.effect_type_support = '0'") or die ("failed getting reqchars 3a.");
			$rqeffs3cnt = mysql_num_rows($required_effect_types3);
			$reqeff3 = mysql_fetch_array($required_effect_types3, MYSQL_NUM);
			// echo"sum: $reqeff3[0]";
			
			// if negative double the chars sum, offer unlimited uses
			$unlimited_uses_threshold = -($reqeff3[0]);
			// if just negative, offer charges, starting at: 1 at 0; 2 at -1, etc.
			$charges_count = $cost_count-1;
			$invert_charges_count = -($charges_count);
			if($curab2[ability_unlimited_uses] == 0)
			{			
				echo"<font color = 'orange' size = '3'>Using $invert_charges_count Charges</font><br>";
			}
			if($curab2[ability_unlimited_uses] != 0)
			{
				echo"<font color = 'orange' size = '1'>Use $invert_charges_count Charges?</font> <input type='checkbox' value='0' name='ability_unlimited_uses'><input type='hidden' value='$invert_charges_count' name='charges_cost'<br>";
			}

			if($cost_count <= $unlimited_uses_threshold)
			{
				if($curab2[ability_unlimited_uses] != 1)
				{
					echo"<font color = 'red' size = '1'> Make Unlimited?</font> <input type='checkbox' value='1' name='ability_unlimited_uses'><br>";
				}
			}
			if($curab2[ability_unlimited_uses] == 1)
			{			
				echo"<font color = 'red' size = '3'>UNLIMITED</font><br>";
			}
			
			if($curab2[ability_unlimited_uses] != 2)
			{
				echo"<font color = 'yellow' size = '1'> Use Cost base <b>$cost_count</b> <input type='checkbox' value='2' name='ability_unlimited_uses'></font><br>";
			}
			if($curab2[ability_unlimited_uses] == 2)
			{			
				echo"<font color = 'yellow' size = '3'>Using Attribute Cost</font><br>";
			}
			
			if($curab2[ability_unlimited_uses] != 3)
			{
				echo"<font color = 'purple' size = '1'> Link to Attribute<b> $highest_effect_tier x/day</b> <input type='checkbox' value='3' name='ability_unlimited_uses'></font><br>";
			}
			if($curab2[ability_unlimited_uses] == 3)
			{			
				echo"<font color = 'purple' size = '3'>Linked w/Attribute</font><br>";
			}
			
			// echo"<font color = 'orange'>method: $curab2[ability_unlimited_uses]; unlim: $unlimited_uses_threshold, chg.cnt: $charges_count</font>";
		}
		
		if($cost_count >= 1)
		{
			$required_effect_types3 = mysql_query("SELECT SUM(".$slrp_prefix."ability_effect_type.effect_type_tier) FROM ".$slrp_prefix."ability_effect_type INNER JOIN ".$slrp_prefix."effect_type ON ".$slrp_prefix."effect_type.effect_type_id = ".$slrp_prefix."ability_effect_type.effect_type_id WHERE ".$slrp_prefix."ability_effect_type.ability_id = '$curab2[ability_id]' AND ".$slrp_prefix."effect_type.effect_type_support = '0'") or die ("failed getting reqchars 3.");
			$rqeffs3cnt = mysql_num_rows($required_effect_types3);
			$reqeff3 = mysql_fetch_array($required_effect_types3, MYSQL_NUM);
			// echo"sum: $reqeff3[0]";
			
			// if negative double the chars sum, offer unlimited uses
			$unlimited_uses_threshold = -($reqeff3[0]);
			$charges_count = $cost_count-1;
			$invert_charges_count = -($charges_count);
			
			if($curab2[ability_unlimited_uses] != 2)
			{
				echo"<font color = 'yellow' size = '1'> Use Cost base <b>$cost_count</b> <input type='checkbox' value='2' name='ability_unlimited_uses'></font><br>";
			}
			
			if($curab2[ability_unlimited_uses] == 2)
			{			
				echo"<font color = 'yellow' size = '3'>Using Attribute Cost</font><br>";
			}
			
			if($curab2[ability_unlimited_uses] == 3)
			{			
				echo"<font color = 'purple' size = '3'>Linked w/Attribute</font><br>";
			}
			if($curab2[ability_unlimited_uses] != 3)
			{
				echo"<font color = 'purple' size = '1'> Link to Attribute<b>[Tier]/day</b> <input type='checkbox' value='3' name='ability_unlimited_uses'></font><br>";
			}
			
			// echo"<font color = 'orange'>method: $curab2[ability_unlimited_uses]; unlim: $unlimited_uses_threshold, chg.cnt: $charges_count</font>";
		}
	}
	// echo"wt: $cost_count, divisor: $slrpnfo[3], ratio: $reqattrtemp<br>";
	
	$current_cost_value = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '".$curab2[ability_id]."'") or die ("failed to get current cost value.");
	$currcstvalcnt = mysql_num_rows($current_cost_value);

	if($currcstvalcnt == 0)
	{
		$new_cost = mysql_query("INSERT INTO ".$slrp_prefix."ability_cost (ability_cost,ability_id,attribute_type_id) VALUES ('1','".$curab2[ability_id]."','3')") or die ("failed inserting default ab cost.");
		
		$current_cost_value_2 = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '".$curab2[ability_id]."'") or die ("failed to get current cost value.");
		// $currcstval = mysql_fetch_assoc($current_cost_value_2);
		$currcstval = mysql_fetch_assoc($current_cost_value_2);
	}
	else

	{
		// $currcstval = mysql_fetch_assoc($current_cost_value);
		$currcstval = mysql_fetch_assoc($current_cost_value);
	}
	
	// $printed_cost_value is derived by the system ans proposed on screen; $cost_value is what it shows in the DB. $cost_count is the weight of the ability, equal to the basic MNA cost.

	if($currcstval[ability_cost] >= 1)
	{
		$printed_cost_value = $currcstval[ability_cost];
		$cost_value = $currcstval[ability_cost];
	}
	if($currcstval[ability_cost] <= 0)
	{
		if($cost_multiplier == 0)
		{
			$printed_cost_value = 0;
			$cost_count = 0;
		}
		else
		{
			if($cost_count <= 0)
			{
				$cost_count = 1;
			}
			
			$printed_cost_value = $cost_count;
		}
	}
	
	if($curab2[ability_unlimited_uses] == 2)
	{
		// echo"cc: $cost_count<br>";
		if($cost_multiplier == 0)
		{
			$final_ability_fuel = "UNLIM";
		}

		if($cost_multiplier >= 1)
		{
			// while($attrbtlst = mysql_fetch_assoc($get_attributes_list))
			while($attrbtlst = mysql_fetch_assoc($get_attributes_list))
			{			
				$required_attribute_ratio_a = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id = '".$attrbtlst[attribute_type_id]."'") or die("failed getting attribute_type for cost ratio.");
				// $reqattrbtrata = mysql_fetch_assoc($required_attribute_ratio_a);
				$reqattrbtrata = mysql_fetch_assoc($required_attribute_ratio_a);
				
				$subtotal_precost = (($reqattrbtrata[attribute_type_cost_ratio] / $slrpnfo[slurp_tier_width])*$cost_count);
				$subtotal_cost = ceil(($reqattrbtrata[attribute_type_cost_ratio] / $slrpnfo[slurp_tier_width])*$cost_count);				
				
				// echo"$subtotal_precost = (($reqattrbtrata[7] / $slrpnfo[3])*$cost_count)<br>";	
				
				// check the appropriate radio button for the attribute cost
				$required_attribute_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '".$curab2[ability_id]."'") or die ("failed getting requred attribute cost.");
				$reqattrcnt = mysql_num_rows($required_attribute_cost);
				// $reqattr = mysql_fetch_assoc($required_attribute_cost);
				$reqattr = mysql_fetch_assoc($required_attribute_cost);

				echo"<input type='radio' name='attr_id'";

				if($reqattr[attribute_type_id] == $attrbtlst[attribute_type_id])
				{
					// check the button if the attribute is used, and get the cost-to-tier ratio
					echo" checked ";				
				}
				
				// if fuel cost is above 0, use the ratios; if cost is below zero, convert to charges for fuel
				if($subtotal_cost <= 0)
				{
					if($reqattrbtrata[attribute_type_id] == 2)
					{
						$cost_color = "#4AC948";
					}
					
					if($reqattrbtrata[attribute_type_id] == 3)
					{
						$cost_color = "#00B2EE";
					}
						
					if($reqattrbtrata[attribute_type_id] == 4)
					{
						$cost_color = "#CC00FF";
					}

					$final_cost = 1;
					$final_ability_fuel = "<font color='$cost_color' size='2'><b>".$attrbtlst[attribute_type_short]." ($final_cost) </b></font>";
				}
				
				if($subtotal_cost >= 1)
				{					
					if($reqattrbtrata[attribute_type_id] == 2)
					{
						$cost_color = "#4AC948";
					}
					
					if($reqattrbtrata[attribute_type_id] == 3)
					{
						$cost_color = "#00B2EE";
					}
						
					if($reqattrbtrata[attribute_type_id] == 4)
					{
						$cost_color = "#CC00FF";
					}
					// echo"$subtotal_precost = (($reqattrbtrata[7] / $slrpnfo[3])*$cost_count)<br>";
					$final_ability_fuel = "<font color='$cost_color' size='2'><b>".$attrbtlst[attribute_type_short]." ($subtotal_cost) </b></font>";
				}

				echo"value='".$attrbtlst[attribute_type_id].$subtotal_cost."'> $final_ability_fuel<br>";	
			}
		}
	}
	
	if($curab2[ability_unlimited_uses] == 1)
	{
		echo"<font color = 'red' size = '2'><b>Currently UNLIM</b>";
	}
	
	if($curab2[ability_unlimited_uses] == 3)
	{
		echo"<font color = 'orange' size = '2'><b>$highest_effect_tier / day</b>";
	}
	
	if($curab2[ability_unlimited_uses] == 0)
	{
		$required_attribute_ratio_1 = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type INNER JOIN ".$slrp_prefix."ability_cost ON ".$slrp_prefix."ability_cost.attribute_type_id = ".$slrp_prefix."attribute_type.attribute_type_id WHERE ".$slrp_prefix."ability_cost.ability_id = '".$curab2[ability_id]."'") or die("failed getting attribute_type for cost ratio.");
		$reqattrbtrat1 = mysql_fetch_assoc($required_attribute_ratio_1);
		
		$subtotal_precost =$cost_weight;
		$charges_count = -($subtotal_precost-1);
		// echo"ch: $subtotal_cost, before: $subtotal_precost<br>";
		// $decimal_count = (($reqattrbtrat1[7] / $slrpnfo[3])*($cost_weight));
		// echo"($reqattrbtrat1[7] / $slrpnfo[3])*($cost_weight) = $decimal_count ";
		echo"<font color = 'orange' size = '2'><b>Current Uses: <font size = '4'> $charges_count</b>";
	}
	
	echo"
								</font>
							</td>
						</tr>
						<tr>
							<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
	";
	
	// staff will see options
	
	
	echo"</font>
							</td>
						</tr>
						<tr>
							<td colspan = '$subcol2_count' align = 'right' valign = 'top'>
								<hr>
								<input type='hidden' value='ab' name='current_expander'>
								<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
								<input type='submit' value='Change Cost' name='ab_ab_attr'>
								<hr>
							</td>
							</form>
	";
}

// for players, list only
if($curusrslrprnk[slurp_rank_id] >= 5)
{
	while($attrbtlst = mysql_fetch_assoc($get_attributes_list))
	{
		$required_attribute_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '".$curab2[ability_id]."' AND attribute_type_id = '$attrbtlst[attribute_type_id]'");
		$reqattrcnt = mysql_num_rows($required_attribute_cost);
		while($reqattr = mysql_fetch_assoc($required_attribute_cost))
		{
			$reqattrtemp = $reqattr[attribute_type_id];
			$reqattrcost = $reqattr[ability_cost];
		}
	}
	// flagged unlimited because costs were so low
	if($curab2[ability_unlimited_uses] == 1)
	{
		$display_cost = "<font color = 'red'><b>UNLIM</b></font>";
	}
	// based on the Effect Type Tier
	if($curab2[ability_unlimited_uses] == 3)
	{
		$display_cost = " <font color = 'orange'><b>Effect Type Tier/day</b></font>";
	}
	if($curab2[ability_unlimited_uses] == 2)
	{
		$check_for_zero_multiplier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_id = '".$curab2[ability_id]."' AND ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '1841'") or die("failed to get zero mulitplier modifier.");
		$chkzromltcnt = mysql_num_rows($check_for_zero_multiplier);
		// echo"# of Modifiers: $chkzromltcnt<br>";
		
		if($chkzromltcnt >= 1)
		{
			// $chkzromlt = mysql_fetch_assoc($check_for_zero_multiplier);
			$chkzromlt = mysql_fetch_assoc($check_for_zero_multiplier);
			
			$cost_multiplier = $chkzromlt[ability_modifier_value];
			// echo"extreme mult: $cost_multiplier, $chkzromlt[ability_modifier_value]<br>";
			$display_cost = " <font color = 'red'><b>UNLIM</b></font>";
		}
		if($chkzromltcnt == 0)
		{
			$required_attribute_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '".$curab2[ability_id]."'");
			$reqattrcstcnt = mysql_num_rows($required_attribute_cost);
			// while($reqattrcst = mysql_fetch_assoc($required_attribute_cost))
			while($reqattrcst = mysql_fetch_assoc($required_attribute_cost))
			{
				//echo"$reqattrcst[ability_cost], $reqattrcst[1], $reqattrcst[2], $reqattrcst[attribute_type_id]<br>";
				$required_attribute = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id = '".$reqattrcst[attribute_type_id]."'") or die ("failed to get attributes for cost.");
				// $reqattr =  mysql_fetch_assoc($required_attribute);
				$reqattr =  mysql_fetch_assoc($required_attribute);
				
				if($reqattrcstcnt >= 1)
				{
					// normal attribute costs
					if($reqattrcst[ability_cost] >= 1)
					{
						if($reqattr[attribute_type_id] == 2)
						{
							$display_cost = " <font color = '#4AC948'><b>".$reqattrcst[ability_cost]." ".$reqattr[attribute_type_short]."</b></font>";
						}
						
						if($reqattr[attribute_type_id] == 3)
						{
							$display_cost = " <font color = '#00B2EE'><b>".$reqattrcst[ability_cost]." ".$reqattr[attribute_type_short]."</b></font>";
						}
						
						if($reqattr[attribute_type_id] == 4)
						{
							$display_cost = " <font color = '#CC00FF'><b>".$reqattrcst[ability_cost]." ".$reqattr[attribute_type_short]."</b></font>";
						}
					}
				}
			}
		}
	}
	if($curab2[ability_unlimited_uses] == 0)
	{
		$required_attribute_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '".$curab2[ability_id]."'");
		$reqattrcstcnt = mysql_num_rows($required_attribute_cost);
		// while($reqattrcst = mysql_fetch_assoc($required_attribute_cost))
		while($reqattrcst = mysql_fetch_assoc($required_attribute_cost))
		{
			//echo"$reqattrcst[0], $reqattrcst[1], $reqattrcst[2], $reqattrcst[3]<br>";
			$required_attribute = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id = '".$reqattrcst[attribute_type_id]."'") or die ("failed to get attributes for cost.");
			// $reqattr =  mysql_fetch_assoc($required_attribute);
			$reqattr =  mysql_fetch_assoc($required_attribute);
			
			$summation_effect_type_tierz = mysql_query("SELECT SUM(effect_tier) FROM ".$slrp_prefix."ability_effect INNER JOIN ".$slrp_prefix."effect ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."ability_effect.effect_id WHERE ".$slrp_prefix."ability_effect.ability_id = '".$curab2[ability_id]."' AND ".$slrp_prefix."ability_effect.effect_type_id != '14' AND ".$slrp_prefix."ability_effect.effect_type_id != '15' AND ".$slrp_prefix."ability_effect.effect_type_id != '16'") or die ("failed to get effect types for cost.");
			$sumefftyptrz =  mysql_fetch_array($summation_effect_type_tierz, MYSQL_NUM);
			
			$summation_modifier_tierz = mysql_query("SELECT SUM(ability_modifier_value) FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_id = '".$curab2[ability_id]."' AND ".$slrp_prefix."ability_modifier.ability_modifier_id > '1'") or die ("failed to get mods for cost.");
			$summodtrz =  mysql_fetch_array($summation_modifier_tierz, MYSQL_NUM);
			
			$sum_tierz = $summodtrz[0] + $sumefftyptrz[0];
	// 		echo"$sum_tierz = $summodtrz[0] + $sumefftyptrz[0]<br>";
	//		if($reqattrcstcnt >= 1)
	//		{
				// charges
			if($reqattrcst[ability_cost] <= 0)
			{
				$charges_precount = ($sum_tierz -1);
				$charges_count = -($charges_precount);
				// echo"$sum_tierz -1: $charges_precount, end: $charges_count<br>";
				if($charges_count == 1)
				{
					$numbered_noun = "Use";
				}
				if($charges_count >= 2)
				{
					$numbered_noun = "Uses";
				}
				
				// echo"rqatcst: $reqattrcst[1]<br>";
				$display_cost = " <font color = 'orange'><b>$charges_count $numbered_noun</b></font>";
				
				// echo"<font color = 'orange'>unlim: $unlimited_uses_threshold, chg.cnt: $charges_count</font>";
			}
		//	}
		}
	}	
	echo"
							<td align = 'left' valign = 'top'><font size = '3' color = '$player_cost_color'>
								<b>$display_cost</b>
								</font>
							</td>
			";
	
	echo"
						</tr>
						<tr>
							<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
								<hr>
							</td>
						</tr>
	";
}


if($curusrslrprnk[slurp_rank_id] <= 5)
{
	echo"
						<tr>
							<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
								<font color = 'yellow' size = '2'>
								ADD EFFECTS
							</td>
						</tr>
						<tr>
							<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
								<hr>
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
						<tr>
							<td valign= 'top' align = 'left'";
			
			if($curusrslrprnk[slurp_rank_id] <= 4)
			{
				echo" colspan = '3'";
			}
			
			echo">
								<font size = '2' color = '$effect_color2'>
								".$abbsefflst[effect_type]." ".roman($rqeffs[effect_type_tier]).": 
							</td>
							
							<td width = '2%'>
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
									<hr>
				";
			}
		}
	}
	
	echo"
							</td>
							
							<td width = '2%'>
							</td>
						</tr>
						<tr>
							<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
								<hr>
								<input type='hidden' value='".$abbsefflst[effect_type_id]."' name='".$abbsefflst[effect_type]."_id'>
								<input type='hidden' value='".$rqeffs[effect_type_id]."' name='".$abbsefflst[effect]."_type_id'>
								<input type='hidden' value='".$abbsefflst[effect_tier]."' name='".$abbsefflst[effect]."_type_tier'>
								<input type='hidden' value='ab' name='current_expander'>
								<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
								<input type='submit' value='Add Base Effect(s)' name='ab_effect_form'>
								<hr>
							</td>
							</form>
						</tr>
	";
	
	if($curab2[ability_status_id] == 4)
	{
		echo"
				<tr>
					<form name = 'copy_template' method='post' action = 'modules.php?name=$module_name&file=ab_edit'>
					<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
						<font color = 'yellow' size = '2'>
						COPY $current_ab_name TO NEW ABILITY:
						<hr>
					</td>
				</tr>
				<tr>
					<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
						<font color = 'yellow' size = '2'>
						<li> Choose a name, then click below.
						<br>
						<br>
						<input type='text' class='textbox3' size='20%' name='newabname'></input>
						<br>
						<br>
						<font color = 'white'>
						Restricted? <input type='checkbox' value='1' name='ab_restr'>
					</td>
				</tr>
				<tr>
					<td align = 'right' valign = 'top' colspan = '$subcol2_count'>
						<hr>
						<input type='hidden' value='ab' name='current_expander'>
						<input type='hidden' value='2' name='copy_ab_status'>
						<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
						<input type='hidden' value='".$usrnfo[nuke_user_id]."' name='copy_user_id'>
						<input type='hidden' value='".$curab2[ability_id]."' name='copy_ab_id'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='submit' value='Submit' name='copy_template'>
						<hr>
					</td>
					</form>
				</tr>
		";
	}
	
	if($curab2[ability_status_id] == 4)
	{
		echo"
				<tr>
					<form name = 'add_ability_to_book' method='post' action = 'modules.php?name=$module_name&file=ab_edit'>
					<td align = 'left' valign = 'top' colspan = '$subcol2_count'>
						<font color = 'yellow' size = '2'>
						MAKE $current_ab_name AVAILABLE IN A BOOK:
						<hr>
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
						<hr>
						<input type='hidden' value='ab' name='current_expander'>
						<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
						<input type='hidden' value='".$usrnfo[nuke_user_id]."' name='book_user_id'>
						<input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='submit' value='Submit' name='add_ability_to_book'>
						<hr>
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
			$get_book_item_del_rand = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random_id = '$gtbkitmdlnfo[ability_object_random_id]'") or die ("failed to get book ability random info.");
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
				<td>
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
					<input type='submit' value='Remove from Print' name='drop_ability_from_book'>
				</td>
				</form>
			</tr>
		";
	}

	// drop a rule to separate the bottom nav buttons, and a table to make them not depend on the above columns
	echo"
					</table>
					</td>
				</tr>
				<tr>
					<td colspan = '$col1_count'>
						<hr>
						<table>
							<tr>
	";
}


if(isset($_POST['from_pc_bs_eff_ntro']))
{
	echo"					<form name = 'back_to_eff_typ_ntro' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ>
								<td width = '18%'>
									<input type='hidden' value='ab_char' name='current_expander'>
									<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
									<input type='submit' value='Back to Effect Types' name='back_to_eff_typ_ntro'>
								</td>
								</form>
								
								<td width = '2%'>
								</td>
	";
}

echo"
								<form name = 'go_to_ab_list' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
								<td width = '18%'>
									<input type='hidden' value='ab' name='current_expander'>
									<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
									<input type='submit' value='Abilities List' name='go_to_ab_list'>
								</td>
								</form>
								
								<td width = '2%'>
								</td>
								
								<form name = 'go_home' method='post' action = 'modules.php?name=$module_name'>
								<td width = '18%'>
									<input type='hidden' value='1' name='ab_expander'>
									<input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'>
									<input type='submit' value='Back to Main' name='go_home'>
								</td>
								</form>
								
								<td width = '2%'>
								</td>
";
								
if($curusrslrprnk[slurp_rank_id] <= 5)
{
	if($curpcnfo[creature_status_id] == 4)
	{
		echo"
								<form name = 'ab_shop' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
								<td width = '18%'>
									<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
									<input type='submit' value='Library' name='ab_shop'>
								</td>
								</form>
		";
	}
}

echo"
							</tr>
						</table>
					</td>
				</tr>
";
// end bottom buttons table and row

//end table in the main 5/6 pane and add space for the sidebar
echo"
			</table>
		</td>
			
		<td width = '2%'>
		</td>
";

// start sidebar
echo"
		<td width = '13%' align = 'right' valign = 'top'>
			<table width = '100%'>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	if($curab2[ability_id] >= 2)
	{
		echo"
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
						<input type='submit' value='Delete $current_ab_name' name='subfocus_grp_del'>
						<hr>
					</td>
					</form>
				</tr>
		";
	}
}

echo"
				<tr>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"<form name = 'show_hide_keycodes' method='post' action = 'modules.php?name=$module_name&file=ab_edit'><input type='hidden' value='".$curpcnfo[creature_id]."' name='current_pc_id'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='".$curab2[ability_id]."' name='current_ab_id'>";
}

echo"
					<td width = '100%' align = 'right'>
";
	
if($curusrslrprnk[slurp_rank_id] <= 4)
{
	if($keycode_expander == 1)
	{
		echo"<input type='hidden' value='0' name = 'keycode_expander'><input type='submit' value='Hide'> ";
	}

	if($keycode_expander == 0)
	{
		echo"<input type='hidden' value='1' name = 'keycode_expander'><input type='submit' value='Show' name = 'show_hide_keycodes'> ";
	}
	 
	echo" 
						<font color = 'yellow' size = '2'>KEYS</font>
					</td>
					</form>
				</tr>
	";
}

// show only owned keys to players
if($curusrslrprnk[slurp_rank_id] >= 5)
{
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
}

// show the all keys to staff
if($curusrslrprnk[slurp_rank_id] <= 4)
{
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
		
	if($keycode_expander == 1)
	{
		$unknown_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '".$curab2[ability_id]."' AND object_focus_id = '".$getfoc[focus_id]."' ORDER BY object_ran".$slrp_prefix."timestamp DESC");
		// while($unkabrnd = mysql_fetch_assoc($unknown_ability_random))
		$unkabrndcnt = mysql_num_rows($unknown_ability_random);
		if($unkabrndcnt >= 1)
		{
			while($unkabrnd = mysql_fetch_assoc($unknown_ability_random))
			{
				// echo"RNDu: $abrnd<br>";
				echo"
					<tr>
						<td width = '100%' align = 'right' valign = 'top'>
							<font color = '#7fffd4' size = '2'>
							<font color='#7fffd4'>
							".$unkabrnd[object_random]."
							</font>
						</td>
					</tr>
				";
			}
		}
	}
}

echo"
					<form name = 'ab_dep' method='post' action = 'modules.php?name=$module_name&file=ab_dep'>
					<tr>
						<td width = '100%' align = 'right' valign = 'top'>
							<hr>
							<font color = 'yellow' size = '2'>
							<input type='hidden' value='".$curab2[ability_id]."' name='ab_dep_id'>
							<input type='submit' value='Dependency Tree' name='ab_dep'>
							<hr>
						</td>
					</tr>
					</form>
";
	
$get_subfocus_modifiers = mysql_query("SELECT ".$slrp_prefix."ability_modifier_subfocus.* FROM ".$slrp_prefix."ability_modifier_subfocus INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_id = '2' AND ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$curab2[ability_id]' AND ".$slrp_prefix."ability.ability_id > '1'") or die("failed to get mods for subfoci relations.");
$getsubmodcnt = mysql_num_rows($get_subfocus_modifiers);
// echo "modcnt: $getsubmodcnt<br>ab_id: $curab2[ability_id] <br>";
 if($getsubmodcnt >= 1)
 {

// 	// show the dressing to staff
//	if($curusrslrprnk[slurp_rank_id] <= 5)
//	{
//		echo"
//		<tr>
//		<td width = '100%' align = 'right' valign = 'top'>
//		<font color = 'yellow' size = '2'>
//		CHILD MODIFIERS ($getsubmodcnt)
//		<hr>
//		</td>
//		
//		</tr>
//		";
//	}
	
  while($getsubmod = mysql_fetch_assoc($get_subfocus_modifiers))
	{
	
//		echo "abmod_id: $getsubmod[ability_modifier_subfocus_id], $getsubmod[ability_modifier_id], $getsubmod[subfocus_id], $getsubmod[focus_id], $getsubmod[focus_exclusion_id]<br>";
//		// echo "abmod_id: $getsubmod[0], $getsubmod[1], $getsubmod[2], $getsubmod[3], $getsubmod[4]<br>";
//		
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
			
			if($curusrslrprnk[slurp_rank_id] <= 5)
			{
				echo"
							<font color = 'orange' size = '2'>

							<form name = 'mod_relations' method = 'post' action = 'modules.php?name=$module_name&file=mod_edit'>
							<input type='hidden' value='1' name='current_pc_id'>
							<input type='hidden' value='$expander_abbr' name='current_expander'>
							<input type='hidden' value='$gtsbmdnfo[ability_modifier_id]' name='$gtsbmdtyp[ability_modifier_type]'>
							<input type='submit' value='($gtsbmdnfo[ability_modifier_value]) $gtsbmdnfo[ability_modifier_short]' name='mod_relations'>
							</form>
				";
			}
			
			$abilities_using_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '".$gtsbmdnfo[ability_modifier_id]."'") or die ("failed getting abilities using mods.");
			// while($abusmod = mysql_fetch_assoc($abilities_using_modifier))
			while($abusmod = mysql_fetch_assoc($abilities_using_modifier))
			{
				echo"
							<br>
							<font color = '#00B2EE'' size = '1'>
							(Ability: ".$abusmod[ability].")
							<form name = 'ab_uses_mod' method = 'post' action = 'modules.php?name=$module_name&file=ab_edit'>
							<input type='hidden' value='1' name='current_pc_id'>
							<input type='hidden' value='$expander_abbr' name='current_expander'>
							<input type='hidden' value='".$abusmod[ability_id]."' name='current_ab_id'>
							<input type='submit' value='".$abusmod[ability]."' name='ab_uses_mod'>
							</form>
				";
			}
	
			echo"
							<hr>
						</td>
					</tr>
			";
		}
	}
	
	if($curusrslrprnk[slurp_rank_id] <= 5)
	{
		$get_character_ownership = mysql_query("SELECT * FROM ".$slrp_prefix."creature INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature.creature_id = ".$slrp_prefix."creature_ability.creature_id WHERE ".$slrp_prefix."creature_ability.ability_id = '$curab2[ability_id]'") or die("failed to get creature ownership for ability relations.");
		$getcharownshpcnt = mysql_num_rows($get_character_ownership);
		if($getcharownshpcnt >= 1)
		{
			echo"
			<tr>
			
			<td width = '100%' align = 'right' valign = 'top'>
			<font color = 'yellow' size = '2'>
			KNOWN BY...
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
				<input type='submit' value='$getcharownshp[creature]' name='creature_ownership'>
				</td>
				</form>
				</tr>
				";	
			}			
		}
	}
}

// end sidebar table and cell
echo"
		</table>
	</td>
";

// end main row
echo"
	</tr>
";			

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>