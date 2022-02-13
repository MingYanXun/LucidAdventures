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
$nav_title = "Edit Ability";
include("modules/$module_name/includes/slurp_header.php");

$current_ab_id = $_POST['current_ab_id'];

if(isset($_POST['current_expander']))
{
	$expander_abbr = $_POST['current_expander'];
	$expander = ($expander_abbr."_expander");
}
//echo"exp: $expander_abbr, $expander<br>";

$get_focus = mysql_query("SELECT * FROM ".$slrp_prefix."focus WHERE focus_id = '2'") or die("failed to get posted focus.");
$getfoc = mysql_fetch_assoc($get_focus);

// get info on the ability
$get_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$current_ab_id'") or die("failed getting ab list.");
$curab = mysql_fetch_assoc($get_abilities);

$final_name = strip_tags(stripslashes($curab[ability]));

$get_current_item_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."ability_item_subtype WHERE ability_id = '$curab[ability_id]'") or die ("failed getting current item sub to view.");
$gtcurritmsbtp = mysql_fetch_assoc($get_current_item_subtype);
$gtcurritmsbtpcnt = mysql_num_rows($get_current_item_subtype);

// change core inf: name and description
if(isset($_POST['ab_name']))
{
	if(isset($_POST['ab_restricted']))
	{
		$ab_restricted = 1;
	}
	if(empty($_POST['ab_restricted']))
	{
		$ab_restricted = 0;
	}	
	
	$ab_restricted = $_POST['ab_restricted'];
	
	$ab_name = strip_tags(mysql_real_escape_string($_POST['ab_name']));
	// echo"abnm1: $ab_name<br>";
	
//	$ab_name = strip_tags($ab_name);
//	echo"abnm2: $ab_name<br>";
	
	$ab_desc = strip_tags(mysql_real_escape_string($_POST['ab_desc']));
	
	//$ab_desc = strip_tags($ab_desc);
	
	$ab_verbal = strip_tags(mysql_real_escape_string($_POST['ab_verbal']));
	
	//$ab_verbal = strip_tags($ab_verbal);
	
	// manage ability cost
	if(isset($_POST['add_ab_tier']))
	{
		$add_ab_tier = $_POST['add_ab_tier'];
				
		$update_ability_tier = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_tier = '$add_ab_tier' WHERE ability_id = '$curab[ability_id]'") or die ("failed updating ability tier.");
		
		$verify_ability_tier = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_tier = '$add_ab_tier' AND ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed verifying updated ability tier.");
		$vrabtierstatus = mysql_fetch_assoc($verify_ability_tier);
		$vrabtierstatuscnt = mysql_num_rows($verify_ability_tier);
		
		if($vrabtierstatuscnt >= 1)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size = '2'>
			<li> <i>$final_name</i> changed its tier to <i>$vrabtierstatus[ability_tier]</i>.
			</font>
			</td>
			</tr>
			";
		}
		
		if($vrabtierstatuscnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size = '2'>
			<li> <i>$final_name</i> did not change its tier to <i>$add_ab_tier</i>.
			</font>
			</td>
			</tr>
			";
		}
	}
	
	// manage ability cost
	if(isset($_POST['add_ab_cost']))
	{
		$add_ab_cost = $_POST['add_ab_cost'];
				
		$update_ability_cost = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_build_cost = '$add_ab_cost' WHERE ability_id = '$curab[ability_id]'") or die ("failed updating ability cost.");
		
		$verify_ability_cost = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_build_cost = '$add_ab_cost' AND ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed verifying updated ability cost.");
		$vrabcoststatus = mysql_fetch_assoc($verify_ability_cost);
		$vrabcoststatuscnt = mysql_num_rows($verify_ability_cost);
		
		if($vrabcoststatuscnt >= 1)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size = '2'>
			<li> <i>$final_name</i> changed its build cost to <i>$vrabcoststatus[ability_build_cost]</i>.
			</font>
			</td>
			</tr>
			";
		}
		
		if($vrabcoststatuscnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size = '2'>
			<li> <i>$final_name</i> did not change its build cost to <i>$add_ab_cost</i>.
			</font>
			</td>
			</tr>
			";
		}
	}
	
	// limit number of purchases
	if(isset($_POST['add_ab_count_max']))
	{
		$add_ab_count_max = $_POST['add_ab_count_max'];
				
		$update_ability_count_max = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_count_max = '$add_ab_count_max' WHERE ability_id = '$curab[ability_id]'") or die ("failed updating purchase limit.");
		
		$verify_ability_count_max = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_count_max = '$add_ab_count_max' AND ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed verifying updated ability purchase limit.");
		$vrabcntmx = mysql_fetch_assoc($verify_ability_count_max);
		$vrabcntmxcnt = mysql_num_rows($verify_ability_count_max);
		
		if($vrabcntmxcnt >= 1)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size = '2'>
			<li> <i>$final_name</i> changed its purchase limit to <i>$vrabcntmx[ability_count_max]</i>.
			</font>
			</td>
			</tr>
			";
		}
		
		if($vrabcntmxcnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size = '2'>
			<li> <i>$final_name</i> did not change its purchase limit to <i>$add_ab_count_max</i>.
			</font>
			</td>
			</tr>
			";
		}
	}
	
	// add a minimum XP
	if(isset($_POST['add_ab_xp_min']))
	{
		$add_ab_xp_min = $_POST['add_ab_xp_min'];
				
		$update_ability_xp_minimum = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_xp_min = '$add_ab_xp_min' WHERE ability_id = '$curab[ability_id]'") or die ("failed updating ab xp min.");
		
		$verify_ability_xp_minimum = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_xp_min = '$add_ab_xp_min' AND ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed verifying updated ability xp min.");
		$vrabxpmin = mysql_fetch_assoc($verify_ability_xp_minimum);
		$vrabxpmincnt = mysql_num_rows($verify_ability_xp_minimum);
		
		if($vrabxpmincnt >= 1)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size = '2'>
			<li> <i>$final_name</i> changed its Build Minimum to <i>$vrabxpmin[ability_xp_min]</i>.
			</font>
			</td>
			</tr>
			";
		}
		
		if($vrabxpmincnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size = '2'>
			<li> <i>$final_name</i> did not change its Build Minimum to <i>$add_ab_xp_min</i>.
			</font>
			</td>
			</tr>
			";
		}
	}
	
	// add a special minimum XP
	if(isset($_POST['add_ab_sp_xp_min']))
	{
		$add_ab_sp_xp_min = $_POST['add_ab_sp_xp_min'];
				
		$update_ability_special_xp_minimum = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_special_xp_min = '$add_ab_sp_xp_min' WHERE ability_id = '$curab[ability_id]'") or die ("failed updating ab special xp min.");
		
		$verify_ability_special_xp_minimum = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_special_xp_min = '$add_ab_sp_xp_min' AND ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed verifying updated ability special xp min.");
		$vrabspxpmin = mysql_fetch_assoc($verify_ability_special_xp_minimum);
		$vrabspxpmincnt = mysql_num_rows($verify_ability_special_xp_minimum);
		
		if($vrabspxpmincnt >= 1)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size = '2'>
			<li> <i>$final_name</i> changed its Non-Domain Build Minimum to <i>$vrabspxpmin[ability_special_xp_min]</i>.
			</font>
			</td>
			</tr>
			";
		}
		
		if($vrabspxpmincnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size = '2'>
			<li> <i>$final_name</i> did not change its Non-Domain Build Minimum to <i>$add_ab_sp_xp_min</i>.
			</font>
			</td>
			</tr>
			";
		}
	}
	
	//manage rank
	if(isset($_POST['min_rank']))
	{
		$min_rank = $_POST['min_rank'];
		
		$current_rank_information = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix.$getfoc[focus_table]." ON ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed getting rank info.");
		$currrnknfo = mysql_fetch_assoc($current_rank_information);
		
		$update_minimum_rank = mysql_query("UPDATE ".$slrp_prefix.$getfoc[focus_table]." SET ".$getfoc[focus_table]."_min_rank = '$min_rank' WHERE ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed updating min rank to view.");
		
		$verify_minimum_rank = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_min_rank = '$min_rank' AND ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed verifying updated min rank to view.");
		$vrmnrnk = mysql_fetch_assoc($verify_minimum_rank);
		$vrmnrnkcnt = mysql_num_rows($verify_minimum_rank);
		
		if($vrmnrnkcnt >= 1)
		{
			$get_rank_information = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix.$getfoc[focus_table]." ON ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed getting rank info.");
			$gtrnknfo = mysql_fetch_assoc($get_rank_information);
			
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size = '2'>
			<li> <i>$final_name</i> requires a minimum rank of <i>$gtrnknfo[slurp_rank]</i>.
			</font>
			</td>
			</tr>
			";
		}
		
		if($vrmnrnkcnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size = '2'>
			<li> <i>$final_name</i> did not change its required rank to <i>$currrnknfo[slurp_rank_id]</i>.
			</font>
			</td>
			</tr>
			";
		}
	}
	
	// manage library status
	if(isset($_POST['ab_library_status']))
	{
		$ab_lib_status = $_POST['ab_library_status'];
		
		$current_library_information = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status INNER JOIN ".$slrp_prefix.$getfoc[focus_table]." ON ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_library_status = ".$slrp_prefix."slurp_status.slurp_status_id WHERE ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed getting library status info.");
		$currlibnfo = mysql_fetch_assoc($current_library_information);
		
		$update_library_status = mysql_query("UPDATE ".$slrp_prefix.$getfoc[focus_table]." SET ".$getfoc[focus_table]."_library_status = '$ab_lib_status', ".$getfoc[focus_table]."_status_id = '$ab_lib_status' WHERE ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed updating library status.");
		
		$verify_library_status = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_library_status = '$ab_lib_status' AND ".$getfoc[focus_table]."_status_id = '$ab_lib_status' AND ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed verifying updated library status.");
		$vrlibstatus = mysql_fetch_assoc($verify_library_status);
		$vrlibstatuscnt = mysql_num_rows($verify_library_status);
		
		if($vrlibstatuscnt >= 1)
		{
			$get_library_status_information = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status INNER JOIN ".$slrp_prefix.$getfoc[focus_table]." ON ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_library_status = ".$slrp_prefix."slurp_status.slurp_status_id WHERE ".$slrp_prefix.$getfoc[focus_table].".".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed getting library status info after changes.");
			$gtlbsttsnfo = mysql_fetch_assoc($get_library_status_information);
			
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size = '2'>
			<li> <i>$final_name</i> is now has a status of <i>$gtlbsttsnfo[slurp_status]/$gtlbsttsnfo[slurp_alt_status1]</i>.
			</font>
			</td>
			</tr>
			";
		}
		
		if($vrlibstatuscnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size = '2'>
			<li> <i>$final_name</i> did not change its status to <i>$gtlbsttsnfo[slurp_status]/$currlibnfo[slurp_alt_status1]</i>.
			</font>
			</td>
			</tr>
			";
		}
	}
	
	// manage ability set
	if(isset($_POST['add_ab_set']))
	{
		$add_ab_set = $_POST['add_ab_set'];
		
		$current_abset_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id = '$curab[ability_set_id]'") or die ("failed getting ab_set info.");
		$currabsetnfo = mysql_fetch_assoc($current_abset_info);
		
		$update_ability_set = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_set_id = '$add_ab_set' WHERE ability_id = '$curab[ability_id]'") or die ("failed updating ability set.");
		
		$verify_ability_set = mysql_query("SELECT * FROM ".$slrp_prefix.$getfoc[focus_table]." WHERE ".$getfoc[focus_table]."_set_id = '$add_ab_set' AND ".$getfoc[focus_table]."_id = '$curab[ability_id]'") or die ("failed verifying updated ability set.");
		$vrabsetstatus = mysql_fetch_assoc($verify_ability_set);
		$vrabsetstatuscnt = mysql_num_rows($verify_ability_set);
		
		if($vrabsetstatuscnt >= 1)
		{
			if($vrabsetstatus[ability_set_id] >= 2)
			{
				$get_ability_set_information = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ".$getfoc[focus_table]."_set_id = '$vrabsetstatus[ability_set_id]'") or die ("failed getting ab set status info after changes.");
				$gtabsetnfo = mysql_fetch_assoc($get_ability_set_information);
				if($vrabsetstatus[ability_set_id] == 11 OR $vrabsetstatus[ability_set_id] == 12)
				{
					$check_existing_ability_tokens = mysql_query("SELECT * FROM ".$slrp_prefix."ability_tokens WHERE ability_id = '$curab[ability_id]'") or die ("failed checking existing ab tokens.");
					$chkexabtknscnt = mysql_num_rows($check_existing_ability_tokens);
					
					if($chkexabtknscnt == 0)
					{
						$create_new_ability_tokens = mysql_query("INSERT INTO ".$slrp_prefix."ability_tokens (ability_id,ability_tokens_1,ability_tokens_2,ability_tokens_3,ability_tokens_4) VALUES ('$curab[ability_id]','0','0','0','0')") or die ("Failed inserting new ab tokens.");
					}
					
					$blue_tokens = $_POST['blue_tokens'];
					$white_tokens = $_POST['white_tokens'];
					$red_tokens = $_POST['red_tokens'];
					$black_tokens = $_POST['black_tokens'];
						
					$update_ability_tokens = mysql_query("UPDATE ".$slrp_prefix."ability_tokens SET ability_tokens_1 = '$blue_tokens', ability_tokens_2 = '$white_tokens', ability_tokens_3 = '$red_tokens', ability_tokens_4 = '$black_tokens' WHERE ability_id = '$curab[ability_id]'") or die ("failed updating ability tokens.");
				}
				if($vrabsetstatus[ability_set_id] == 14)
				{
					$ab_set_min1 = $_POST['ability_set_min_1'];
					$ab_set_min2 = $_POST['ability_set_min_2'];
					$ab_set_min3 = $_POST['ability_set_min_3'];
					$ab_set_min4 = $_POST['ability_set_min_4'];
					$ab_set_min5 = $_POST['ability_set_min_5'];
				}
				if($vrabsetstatus[ability_set_id] != 14)
				{
					$ab_set_min1 = 0;
					$ab_set_min2 = 0;
					$ab_set_min3 = 0;
					$ab_set_min4 = 0;
					$ab_set_min5 = 0;
				}
				
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
				<font color = 'yellow' size = '2'>
				<li> <i>$final_name</i> is now part of the Ability Set: <i>$gtabsetnfo[ability_set]</i>.";
				
				if($vrabsetstatus[ability_set_id] == 11 OR $vrabsetstatus[ability_set_id] == 12)
				{
					echo" (<font color='blue'>$blue_tokens</font>, <font color='white'>$white_tokens</font>, <font color='red'>$red_tokens</font>, <font color='black'>$black_tokens</font>)";
				}
				
				if($vrabsetstatus[ability_set_id] == 14)
				{
					if($curab[ability_set_min_1] >= 1)
					{
						echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Burn Domain Ability Count Minimum: <i>$curab[ability_set_min_1]</i>";
					}
					if($curab[ability_set_min_2] >= 1)
					{
						echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Combat Domain Ability Count Minimum: <i>$curab[ability_set_min_2]</i>";
					}
					if($curab[ability_set_min_3] >= 1)
					{
						echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Faith Domain Ability Count Minimum: <i>$curab[ability_set_min_3]</i>";
					}
					if($curab[ability_set_min_4] >= 1)
					{
						echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Insight Domain Ability Count Minimum: <i>$curab[ability_set_min_4]</i>";
					}
					if($curab[ability_set_min_5] >= 1)
					{
						echo"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stealth Domain Ability Count Minimum: <i>$curab[ability_set_min_5]</i>";
					}
				}
				
				echo"
				</font>
				</td>
				</tr>
				";
			}
			if($vrabsetstatus[ability_set_id] == 1)
			{
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
				<font color = 'yellow' size = '2'>
				<li> <i>$final_name</i> is not part of any Ability Set.
				</font>
				</td>
				</tr>
				";
			}
		}
		
		if($vrabsetstatuscnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size = '2'>
			<li> <i>$final_name</i> did not change its Ability Set. It is still a(n) <i>$currabsetnfo[ability_set]</i>.
			</font>
			</td>
			</tr>
			";
		}
	}
	
	//clean up some text fields
	if(isset($_POST['edit_subfocus_short']))
	{
		$object_short = strip_tags(mysql_real_escape_string($_POST['edit_subfocus_short']));
	}
	if(empty($_POST['edit_subfocus_short']))
	{
		$object_short = $curab[ability_short];
	}

	if(isset($_POST['edit_subfocus_short_desc']))
	{	
		$object_short_desc = strip_tags(mysql_real_escape_string($_POST['edit_subfocus_short_desc']));
	}
	if(empty($_POST['edit_subfocus_short_desc']))
	{
		$object_short_desc = $curab[ability_short_desc];
	}
	
	// set the parent item subtype
	if(isset($_POST['item_subtype']))
	{	
		$item_subtype = strip_tags(mysql_real_escape_string($_POST['item_subtype']));
		
		if($item_subtype >= 2)
		{
			if($gtcurritmsbtpcnt >= 1)
			{
				$update_ability_item_subtype = mysql_query("UPDATE ".$slrp_prefix."ability_item_subtype SET item_subtype_id ='$item_subtype' WHERE ability_id = '$curab[ability_id]'") or die ("failed updating ab itm sub info.");
			}
			
			if($gtcurritmsbtpcnt == 0)
			{
				$insert_ability_item_subtype = mysql_query("INSERT INTO ".$slrp_prefix."ability_item_subtype (ability_id,item_subtype_id) VALUES ('$curab[ability_id]','$item_subtype')") or die ("failed inserting ab itm sub info.");
			}
		}
	}
	
	if(empty($_POST['item_subtype']))
	{
		$item_subtype = $gtcurritmsbtp[ability_item_subtype_id];
	}
	
	// echo"rank: $min_rank, ids: $current_ab_id, $curab[ability_id]<br>desc: $ab_desc<br>name: $ab_name<br>verbal: <i>$ab_verbal</i><br>";
	// update the core ab info and show confirmation
	$update_ability_core_info = mysql_query("UPDATE ".$slrp_prefix."ability SET ability='$ab_name', ability_desc = '$ab_desc', ability_verbal = '$ab_verbal', ability_restricted = '$ab_restricted', ability_short = '$object_short', ability_short_desc = '$object_short_desc', ability_set_min_1 = '$ab_set_min1', ability_set_min_2 = '$ab_set_min2', ability_set_min_3 = '$ab_set_min3', ability_set_min_4 = '$ab_set_min4', ability_set_min_5 = '$ab_set_min5' WHERE ability_id = '$curab[ability_id]'") or die ("failed updating ab core info.");
	
	$verify_ability_core_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$curab[ability_id]'") or die ("failed verifying core ability info.");
	$vrabcrnfo = mysql_fetch_assoc($verify_ability_core_info);
	$verabcornfocnt = mysql_num_rows($verify_ability_core_info);
	
	$new_name = strip_tags(stripslashes($vrabcrnfo[ability]));
	
	if($verabcornfocnt >= 1)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'yellow' size = '2'>
		<li> <i>$final_name</i> has been renamed <i>$new_name</i>.
		</font>
		</td>
		</tr>
		";
		
		$final_name = strip_tags(stripslashes($vrabcrnfo[ability]));
		
		// get ability limiters for sentences to make modifiers
		$get_object_focus_exclusion = mysql_query("SELECT * FROM ".$slrp_prefix."focus_exclusion WHERE focus_id = '2' ORDER BY focus_exclusion") or die ("failed to get object focus exclusion.");
		$gtobjfcexcnt = mysql_num_rows($get_object_focus_exclusion);
		while($gtobjfcex = mysql_fetch_assoc($get_object_focus_exclusion))
		{
			// echo"$gtobjfcex[focus_exclusion]<br>";
			// get the ones that should exist by default based on that; this will miss renamed ones.
			$get_existing_modifiers = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id = ".$slrp_prefix."ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_id = '2' AND subfocus_id = '$curab[ability_id]' AND focus_exclusion_id = '$gtobjfcex[focus_exclusion_id]' ORDER BY ".$slrp_prefix."ability_modifier.ability_modifier_short") or die ("failed getting existing modifiers for update.");
			$getexmodscnt =  mysql_num_rows($get_existing_modifiers);
			
			$new_subfocus_shrt = strip_tags(mysql_real_escape_string($gtobjfcex[focus_exclusion]." ".$final_name));
			$new_subfocus_mod = ($new_subfocus_shrt.".");
			
			if($getexmodscnt >= 1)
			{
				while($getexmods = mysql_fetch_assoc($get_existing_modifiers))
				{
					$old_mod_name = strip_tags(stripslashes($getexmods[ability_modifier_short]));
					// echo"ex_mod_nm: $getexmods[ability_modifier_short]<br>";
					
					// echo"compiled_mod_name: $gtobjfcex[1] $vrabcrnfo[1]<br>short: $new_subfocus_shrt<br>existing mod name: $getexmods[4]<br>long: $new_subfocus_mod<br>";
					
					$update_modifier_name = mysql_query("UPDATE ".$slrp_prefix."ability_modifier SET ability_modifier_short='$new_subfocus_shrt',ability_modifier='$new_subfocus_mod' WHERE ability_modifier_id = '$getexmods[ability_modifier_id]'") or die ("failed updating new subfocus subtype relation.");
					
					$verify_new_modifier_name = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '$getexmods[ability_modifier_id]' AND ability_modifier_short = '$new_subfocus_shrt' AND ability_modifier='$new_subfocus_mod'") or die ("failed verifying new updated mod name.");
					$vernewmodnmcnt = mysql_num_rows($verify_new_modifier_name);
					$vernewmodnm = mysql_fetch_assoc($verify_new_modifier_name);
					// echo"ver_mod_nm: $vernewmodnm[ability_modifier_short]<br>";
					
					$new_mod_name = strip_tags(stripslashes($vernewmodnm[ability_modifier_short]));
					
					if($vernewmodnmcnt >= 1)
					{
						echo"
						<tr>
						<td  colspan = '7' valign = 'top' align = 'left'>
						<font color = 'yellow' size = '2'>
						<li> $old_mod_name (Modifier #$vernewmodnm[ability_modifier_id]) has been renamed to <i>$new_mod_name</i>.
						</font>
						</td>
						</tr>
						";
					}
				}
				
				if($vernewmodnmcnt == 0)
				{
					echo"
					<tr>
					<td  colspan = '7' valign = 'top' align = 'left'>
					<font color = 'yellow' size = '2'>
					<li> <i>$old_mod_name</i> was not renamed to <i>$new_subfocus_shrt</i>. Please Try again or contact an admin.
					</font>
					</td>
					</tr>
					";
				}
			}
			if($getexmodscnt == 0)
			{
				$insert_modifier_info = mysql_query("INSERT INTO ".$slrp_prefix."ability_modifier (ability_modifier_type_id,ability_modifier_value,ability_modifier,ability_modifier_short) VALUES ('".$gtfcex[focus_ability_modifier_type_id]."','$new_subfocus_value','$new_subfocus_mod','$new_subfocus_shrt')") or die ("failed inserting new ".$getfoc[focus_table]." subtype relation 23c.");
			}
		}
	}
	
	if($verabcornfocnt == 0)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'red' size = '2'>
		<li>	<i>$final_name</i> has NOT been updated. Try again or contact an admin if you think there might be a problem.
		</font>
		</td>
		</tr>
		";
	}
	
	// set a hash code on it if checked
	if(isset($_POST['new_object_random']))
	{
		include("includes/fn_obj_rand.php");
		
		// echo"<font color = 'blue'>Sum: $$rndstrsum<br>";

		$random_object_exists = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_focus_id = '$getfoc[focus_id]' AND object_id = '$curab[ability_id]'") or die ("failed checking existing object randoms.");
		$rndobjexcnt = mysql_num_rows($random_object_exists);
		// echo "$rndobjexcnt<br>";
		if($rndobjexcnt >= 1)
		{
			$random_update = mysql_query("UPDATE ".$slrp_prefix."object_random SET object_random_current='0' WHERE object_focus_id = '$getfoc[focus_id]' AND object_id = '$curab[ability_id]'") or die ("failed updating object randoms.");	
		}
		
		$random_insert = mysql_query("INSERT INTO ".$slrp_prefix."object_random (object_id,object_random,object_random_current,object_focus_id,object_slurp_id) VALUES ('$curab[ability_id]','$rndtxtsum','1','$getfoc[focus_id]','$slrpnfo[slurp_id]')") or die ("failed inserting new object random.");
		
		//$get_new_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHEREWHERE object_focus_id = '$getfoc[focus_id]' AND object_id = '$curab[ability_id]' AND object_random = '$rndstrsum' AND object_ran".$slrp_prefix."current = '1'") or die ("failed getting new obj random.");
		// $gtnwrnd = mysql_fetch_assoc($get_new_random);
		
		// echo"rnd_id: $gtnwrnd[0]<br>";
		
		echo"
					<tr>
					<td  colspan = '7' valign = 'top' align = 'left'>
					<font color = 'yellow' size = '2'>
					<li> <i>$curab[ability]</i> now has a new random key of <font color='orange'><b>$rndtxtsum</b></font>.
					</font>
					</td>
					</tr>
		";
	}

}

// delete the modifier and everything tied to it in other tables, verifying each step
if(isset($_POST['ab_del']))
{
	$ab_del = $_POST['ab_del'];
	// echo"abdel: $ab_del<br>$curab[ability_id]<br>$current_ab_id<br>";
	
	echo"<tr>";
	
	$table_focus_id = $curab[ability_id];
	$slurp_object_list = mysql_query("SELECT * FROM ".$slrp_prefix."object WHERE object_id = '$getfoc[focus_is_object]'") or die ("failed getting slurp object list.");
	$slrpobjlstcnt = mysql_num_rows($slurp_object_list);
	if($slrpobjlstcnt >= 1)
	{
		//$slrpmetacols = 0;
		// echo"";
		while($slrpobjlst = mysql_fetch_assoc($slurp_object_list))
		{
			//$slrpmetacols++;
			// echo"<td valign = 'top'><li> <b>$slrpobjlst[object]</b> ($slrpobjlst[object_instance]) ($slrpobjlst[object_abbr]) ($slrpobjlst[object_levels])<br>";
			
			$slrpobjlvls = $slrpobjlst[object_levels];
			while($slrpobjlvls >= 1)
			{
				// echo"<hr>";
				$object_list_suffix = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_table_suffix WHERE slurp_table_suffix_level = '$slrpobjlvls'") or die ("failed getting object list suffixes.");
				while($objlstsffx = mysql_fetch_assoc($object_list_suffix))
				{
					$slrptblconcat = $slrp_prefix.$slrpobjlst[object_instance].$objlstsffx[slurp_table_suffix];
					$tblconcat = $slrpobjlst[object_instance].$objlstsffx[slurp_table_suffix];
					
					if($slurp_table_suffix_level >= 2)
					{
						$get_suffix_child = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_table_suffix WHERE slurp_table_suffix_id = '$objlstsffx[slurp_table_suffix_parent]'") or die ("failed getting suffix child.");
						$gtsffxchld = mysql_fetch_assoc($get_suffix_child);
						$childtblconcat = $slrp_prefix.$slrpobjlst[object_instance].$gtsffxchld[slurp_table_suffix];
						$chldconcat = $slrpobjlst[object_instance].$gtsffxchld[slurp_table_suffix];
						$joiner_concat = $slrp_prefix.$slrpobjlst[object_instance].$gtsffxchld[slurp_table_suffix]."_".$slrpobjlst[object_instance].$objlstsffx[slurp_table_suffix];				
					}
					
					// echo"$objtblconcat<br>";

					$object_table_groups = mysql_query("SHOW TABLES LIKE '%$tblconcat%'") or die ("failed getting $slrptblconcat table.");
					while($objtblgrps = mysql_fetch_array($object_table_groups, MYSQL_NUM))
					{						
						$slurp_table_temp = $objtblgrps[0];
						$object_table_columns = mysql_query("SHOW COLUMNS FROM ".$slurp_table_temp." LIKE '".$tblconcat."_id'") or die ("failed getting $slrptblconcat columns.");
						$objtblcolscnt = mysql_num_rows($object_table_columns);
						
						if($objtblcolscnt == 1)
						{
							// echo"DEL: $slurp_table_temp<br>";
							$delete_all_object_instances = mysql_query("DELETE FROM ".$slurp_table_temp." WHERE ".$tblconcat."_id = '$table_focus_id'") or die ("failed deleting $slrptblconcat columns.");					
							
							$verify_deleted_relations = mysql_query("SELECT * FROM ".$slurp_table_temp." WHERE ".$tblconcat."_id = '$table_focus_id'") or die ("failed getting ab mod relation.");
							$verdelrels = mysql_fetch_assoc($verify_deleted_relations);
							$verdelrelscnt = mysql_num_rows($verify_deleted_relations);
							
							if($verdelrelscnt == 0 )
							{
								echo"
								<tr>
								<td width = '100%' valign = 'top' align = 'left'>
								<font color = 'yellow' size = '2'>
								<li>	$final_name's relation(s) to the table [$slurp_table_temp] have been deleted.
								</font>
								</td>
								</tr>
								";
							}
							
							if($verdelrelscnt >=1 )
							{
								echo"
								<tr>
								<td width = '100%' valign = 'top' align = 'left'>
								<font color = 'red' size = '2'>
								<li>	$final_name's relation(s) to the table [$slurp_table_temp] have NOT been deleted.
								<br>
								<br>
								Try again or contact an admin if you think there might be a problem.
								</font>
								</td>
								</tr>
								";
							}
						}
												
						// echo"CASE $slurp_table_temp<br>";
						$get_counted_tables = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_table_temp WHERE slurp_table_temp = '$slurp_table_temp'") or die ("failed getting counted tables.");
						$gtcntdtblscnt = mysql_num_rows($get_counted_tables);
						if($gtcntdtblscnt == 0)
						{
							if($slurp_table_suffix_level >= 2)
							{
								// echo"<li> $joiner_concat<br>";
								$record_counted_tables = mysql_query("INSERT INTO ".$slrp_prefix."slurp_table_temp (slurp_table_temp) VALUES ('$joiner_concat')") or die ("failed inserting temp joiner listing.");
							}
							
							// echo"<li> $slurp_table_temp<br>";
							$record_counted_tables = mysql_query("INSERT INTO ".$slrp_prefix."slurp_table_temp (slurp_table_temp) VALUES ('$slurp_table_temp')") or die ("failed inserting temp table listing.");
						}
					}
				
					$slrpobjlvls--;
				}
			}
			
			$clean_up_counted_tables = mysql_query("DELETE FROM ".$slrp_prefix."slurp_table_temp") or die ("could not delete slurp table temp.");
									
			echo"</td>";
		}
	}
	
	$delete_object_graphic = mysql_query("DELETE FROM ".$slrp_prefix."object_graphic WHERE object_id = '$curab[ability_id]' AND object_focus_id = '$getfoc[focus_id]'") or die ("failed deleting object graphic relations.");
	$delete_object_random = mysql_query("DELETE FROM ".$slrp_prefix."object_random WHERE object_id = '$curab[ability_id]' AND object_focus_id = '$getfoc[focus_id]'") or die ("failed deleting object random relations.");
	
	$get_related_abmods = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE subfocus_id = '$curab[ability_id]' AND focus_id = '$getfoc[focus_id]'") or die ("failed getting abmod subfocus relations.");
	while($gtrelabmds = mysql_fetch_assoc($get_related_abmods))
	{
		$ability_mofidier_tables = mysql_query("SHOW TABLES LIKE '%ability_modifier%'") or die ("failed getting ability modifier tables.");
		while($abmodtables = mysql_fetch_array($ability_mofidier_tables, MYSQL_NUM))
		{
			$abmod_table_temp = $abmodtables[0];
			$abmod_table_columns = mysql_query("SHOW COLUMNS FROM ".$abmod_table_temp." LIKE 'ability_modifier_id'") or die ("failed getting $abmod_table_temp columns.");
			$abmdtblcolscnt = mysql_num_rows($abmod_table_columns);
			
			if($abmdtblcolscnt == 1)
			{
				$delete_object_ability_modifier_subfocus = mysql_query("DELETE FROM ".$abmod_table_temp." WHERE ability_modifier_id = '$gtrelabmds[ability_modifier_id]'") or die ("failed deleting abmod subfocus relations to $abmod_table_temp.");
				
				$verify_deleted_subfoc_relations = mysql_query("SELECT * FROM ".$abmod_table_temp." WHERE ability_modifier_id = '$gtrelabmds[ability_modifier_id]'") or die ("failed getting ab mod relation.");
				$verdelsbfcrels = mysql_fetch_assoc($verify_deleted_subfoc_relations);
				$verdelsbfcrelscnt = mysql_num_rows($verify_deleted_subfoc_relations);
				
				if($verdelsbfcrelscnt == 0)
				{
					echo"
					<tr>
					<td width = '100%' valign = 'top' align = 'left'>
					<font color = 'yellow' size = '2'>
					<li>	$final_name's relation(s) to the table [$abmod_table_temp] have been deleted.
					</font>
					</td>
					</tr>
					";
				}
				
				if($verdelsbfcrelscnt >= 1)
				{
					echo"
					<tr>
					<td width = '100%' valign = 'top' align = 'left'>
					<font color = 'red' size = '2'>
					<li>	$final_name's relation(s) to the table [$abmod_table_temp] have NOT been deleted.
					<br>
					<br>
					Try again or contact an admin if you think there might be a problem.
					</font>
					</td>
					</tr>
					";
				}
			}
		}
	}
		
	echo"
	</tr>
	";
}

// graphic handler for all objects
$current_focus_id = 2;
$current_object_id = $current_ab_id;
include("modules/$module_name/includes/fm_obj_graphic.php");

// editng effects and tiers


// deleting effects and their attached modifiers
if(isset($_POST['del_effect']))
{
	$del_effect = $_POST['del_effect'];
	
	// echo"$del_base_effect, $del_base_ab_ab_mod<br>";
	
	$delete_base_effect = mysql_query("DELETE FROM ".$slrp_prefix."ability_effect WHERE ability_effect_id = '$del_effect'") or die("failed deleting base effects and stuff.");
	
	// verify the delete went through, and print accordingly
	$verify_delete = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect WHERE ability_effect_id = '$del_effect'") or die("failed getting delete verification");
	$verdelcnt = mysql_num_rows($verify_delete);
	
	if($verdelcnt >= 1)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'red' size ='2'>
		<li>	$final_name's base effects did not change. Please try again, or contact an admin if you believe something is wrong.
		</font>
		</td>
		</tr>
		";			
	}
	
	if($verdelcnt == 0)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'yellow' size ='2'>
		<li>	$final_name's base effect has been deleted.
		</font>
		</td>
		</tr>
		";			
	}
}

//adding special effects
if(isset($_POST['add_spcl_eff']))
{	
	$add_spcl_eff = $_POST['add_spcl_eff'];
	if($add_spcl_eff >= 2)
	{
		// echo"spcl: $add_spcl_eff<br>";
		$get_existing_special_effect = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_special WHERE ability_id = '$curab[ability_id]' AND effect_special_id = '$add_spcl_eff'") or die ("failed getting existing special effect.");
		$getexspcleffcnt = mysql_num_rows($get_existing_special_effect);
		
		// echo"cnt: $getexspcleffcnt<br>";
		
		if($getexspcleffcnt == 0)
		{	
			$insert_new_spcl_eff = mysql_query("INSERT INTO ".$slrp_prefix."ability_effect_special (ability_id,effect_special_id,effect_special_default) VALUES ('$curab[ability_id]','$add_spcl_eff','0')") or die ("failed inserting new special eff.");
			
			$verify_new_eff_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_special WHERE ability_id = '$curab[ability_id]' AND effect_special_id = '$add_spcl_eff'") or die ("failed verifying new spcl eff.");
			$verneweffnfo = mysql_fetch_assoc($verify_new_eff_info);
			$verneweffnfocnt = mysql_num_rows($verify_new_eff_info);
			
			if($verneweffnfocnt == 1)
			{
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
				<font color = 'yellow' size ='2'>
				<li>	$final_name's special effect(s) were successfully changed.
				</font>
				</td>
				</tr>
				";
			}
			
			if($verneweffnfocnt == 0)
			{
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
					<font color = 'red' size ='2'>
				<li>	$final_name did not update properly. Please try again, or contact an admin if you think something is wrong.
				</font>
				</td>
				</tr>
				";
			}
		}
	}	
	if($getexspcleffcnt == 1)
	{
		echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size ='2'>
			<li>	$final_name already has that Special Effect. No action was taken.
			</font>
			</td>
			</tr>
		";
	}
}

// deleting special effects
$special_effect_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect_special WHERE effect_special_id > '1'") or die ("failed to get special effects list for delete.");
$spclefflstcnt = mysql_num_rows($special_effect_list);
// echo"spcl eff ct.: $spclefflstcnt<br>";
while($spclefflst = mysql_fetch_assoc($special_effect_list))
{
	// echo"Sp.Eff.: $spclefflst[1], $spclefflst[0]<br>";
	if(isset($_POST['del_'.$spclefflst[effect_special].'_id']))
	{
		$del_spcl_effect = $_POST['del_'.$spclefflst[effect_special].'_id'];
		
		// echo"($spclefflst[1], $del_spcl_effect)<br>";
		
		$delete_special_effect = mysql_query("DELETE FROM ".$slrp_prefix."ability_effect_special WHERE effect_special_id = '$del_spcl_effect' AND ability_id = '$curab[ability_id]'") or die("failed deleting special effects.");
				
		// verify the delete went through, and print accordingly
		$verify_delete_spcl_eff = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_special WHERE ability_effect_special_id = '$del_spcl_effect'") or die("failed getting delete spec. effs verification");
		$verdelspcleffcnt = mysql_num_rows($verify_delete_spcl_eff);
		
		if($verdelspcleffcnt >= 1)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size ='2'>
			<li>	$final_name's base effects did not change. Please try again, or contact an admin if you believe something is wrong.
			</font>
			</td>
			</tr>
			";			
		}
		
		if($verdelspcleffcnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size ='2'>
			<li>	$final_name's special effect has been deleted.
			</font>
			</td>
			</tr>
			";			
		}
	}
}


//adding mimic abilities
if(isset($_POST['add_mimic_ab']))
{	
	$add_mimic_ab = $_POST['add_mimic_ab'];
	if($add_mimic_ab >= 2)
	{
		// echo"spcl: $add_spcl_eff<br>";
		$check_existing_mimicked_ability = mysql_query("SELECT * FROM ".$slrp_prefix."ability_mimics_ability WHERE ability_id = '$curab[ability_id]' AND mimics_ability_id = '$add_mimic_ab'") or die ("failed getting existing mimic ability.");
		$chkexmmcabcnt = mysql_num_rows($check_existing_mimicked_ability);
		$mimicked_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$add_mimic_ab'") or die ("failed getting existing mimicked ability.");
		$mmckdabnfo = mysql_fetch_assoc($mimicked_ability_info);
		
		// echo"cnt: $getexspcleffcnt<br>";
		
		if($chkexmmcabcnt == 0)
		{	
			$insert_new_mimic_ability = mysql_query("INSERT INTO ".$slrp_prefix."ability_mimics_ability (ability_id,mimics_ability_id) VALUES ('$curab[ability_id]','$add_mimic_ab')") or die ("failed inserting new mimic ability.");
			
			$verify_new_mimic_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_mimics_ability WHERE ability_id = '$curab[ability_id]' AND mimics_ability_id = '$add_mimic_ab'") or die ("failed verifying new mimic ability.");
			$vernewmmcnfo = mysql_fetch_assoc($verify_new_mimic_info);
			$vernewmmcnfocnt = mysql_num_rows($verify_new_mimic_info);
			
			if($vernewmmcnfocnt == 1)
			{
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
				<font color = 'yellow' size ='2'>
				<li>	$final_name now mimics $mmckdabnfo[ability].
				</font>
				</td>
				</tr>
				";
			}
			
			if($vernewmmcnfocnt == 0)
			{
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
					<font color = 'red' size ='2'>
				<li>	$final_name did not update properly. Please try again, or contact an admin if you think something is wrong.
				</font>
				</td>
				</tr>
				";
			}
		}
	}	
	if($chkexmmcabcnt == 1)
	{
		echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size ='2'>
			<li>	$final_name already mimics that ability. No action was taken.
			</font>
			</td>
			</tr>
		";
	}
}

// deleting mimic abilities
$mimicked_ability_list = mysql_query("SELECT * FROM ".$slrp_prefix."ability_mimics_ability WHERE ability_id = '$curab[ability_id]'") or die ("failed to get mimic list for delete.");
$mmckdablstcnt = mysql_num_rows($mimicked_ability_list);
// echo"mimic ct.: $mmckdablstcnt<br>";
while($mmckdablst = mysql_fetch_assoc($mimicked_ability_list))
{
	// echo"Mimicked: $spclefflst[mimics_ability_id]<br>";
	if(isset($_POST['del_'.$mmckdablst[mimics_ability_id].'_id']))
	{
		$del_mimic_ab = $_POST['del_'.$mmckdablst[mimics_ability_id].'_id'];
		
		// echo"($mmckdablst[ability_id], $del_mimic_ab)<br>";
		
		$delete_mimic_ability = mysql_query("DELETE FROM ".$slrp_prefix."ability_mimics_ability WHERE mimics_ability_id = '$del_mimic_ab' AND ability_id = '$curab[ability_id]'") or die("failed deleting mimic ability.");
				
		// verify the delete went through, and print accordingly
		$verify_delete_mimic_ability = mysql_query("SELECT * FROM ".$slrp_prefix."ability_mimics_ability WHERE mimics_ability_id = '$del_mimic_ab'") or die("failed getting deleted mimic ab verification");
		$verdelmmcabcnt = mysql_num_rows($verify_delete_mimic_ability);
		
		if($verdelmmcabcnt >= 1)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size ='2'>
			<li>	$final_name's mimic status did not change. Please try again, or contact an admin if you believe something is wrong.
			</font>
			</td>
			</tr>
			";			
		}
		
		if($verdelmmcabcnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size ='2'>
			<li>	$final_name's mimicking has been deleted.
			</font>
			</td>
			</tr>
			";			
		}
	}
}

// deleting modifiers other than base effects
if(isset($_POST['del_mod_id']))
{
	$del_mod_id = $_POST['del_mod_id'];
	
	$del_mod = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = 'del_mod_id'") or die ("failed getting del mod.");
	$dlmod = mysql_fetch_assoc($del_mod);
	//echo"$del_mod_id<br>";
	// get the abilities list to delete ability_requires_ability entry
	$all_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1'") or die ("failed getting ability list for mod reqs.");
	while($allabs = mysql_fetch_assoc($all_abs))
	{
		// get mods pointing at the required ability and modifying the edited ability
		$deleted_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_id = '$del_mod_id' AND focus_id = '2' AND subfocus_id = '$allabs[ability_id]' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id = '16'") or die ("failed getting deleted mod abs.");
		$dlabscnt = mysql_num_rows($deleted_abs);
		while($dlabs = mysql_fetch_assoc($deleted_abs))
		{					
			if($dlabs[ability_modifier_id] == $del_mod_id)
			{
				// echo"($curab[ability]) trying to lose $allabs[ability]?<br>";
				// echo"$dlabs[ability_modifier_id] should be $del_mod_id, $dlabscnt, ($curab[ability]) ab1: $allabs[ability]<br>";
				$delete_ability_requires_ability = mysql_query("DELETE FROM ".$slrp_prefix."ability_requires_ability WHERE ability_id = '$curab[ability_id]' AND requires_ability_id = '$allabs[ability_id]'") or die("failed deleting ab req ab.");
			}	
		}
	}
	
	$delete_modifier_from_ability = mysql_query("DELETE FROM ".$slrp_prefix."ability_ability_modifier WHERE ability_modifier_id = '$del_mod_id' AND ability_id = '$curab[ability_id]'") or die("failed deleting ability mod.");
	
	// change the unlimited flag to the default
	if($dlmod[ability_modifier_type_id] == '9')
	{
		$update_unlimited_uses_flag = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_unlimited_uses = '2' WHERE ability_id = '$curab[ability_id]'") or die ("Failed updating unlim flag to default.");
		
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'yellow' size ='2'>
		<li>	$final_name's Unlimited flag was successfully changed to 0.
		</font>
		</td>
		</tr>
		";
	}
	// verify the delete went through, and print accordingly
	$verify_delete37 = mysql_query("SELECT * FROM ".$slrp_prefix."ability_ability_modifier WHERE ability_modifier_id = '$del_mod_id' AND ability_id = '$curab[ability_id]'") or die("failed getting delete verification");
	$verdelcnt37 = mysql_num_rows($verify_delete37);
	
	if($verdelcnt37 >= 1)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'red' size ='2'>
		<li>	$final_name's modifers did not change. Please try again, or contact an admin if you believe something is wrong.
		</font>
		</hr>
		</td>
		</tr>
		";			
	}
	
	if($verdelcnt37 == 0)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'yellow' size ='2'>
		<li>	$final_name's modifier has been deleted.
		</font>
		</td>
		</tr>
		";			
	}
}

// adding modifiers other than base effects
$get_modifier_type = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_type WHERE ability_modifier_type_id > '1' ORDER BY ability_modifier_type") or die("failed to get mod types.");
while($getmodtyp = mysql_fetch_assoc($get_modifier_type))
{
	 // echo"$getmod[ability_modifier_type]<br>";
	if(isset($_POST[$getmodtyp[ability_modifier_type]]))
	{
		$current_ab_mod_id = $_POST[$getmodtyp[ability_modifier_type]];
		 // echo"mod_id: $current_ab_mod_id<br>";
		if($current_ab_mod_id >= 2)
		{
			// get the abilities list to add ability_requires_ability entry
			$all_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1'") or die ("failed getting ability list for mod reqs.");
			while($allabs = mysql_fetch_assoc($all_abs))
			{
				// echo"($curab[ability]) trying to lose $allabs[ability]?<br>";
				// get mods pointing at the required ability and modifying the edited ability
				$required_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE focus_id = '2' AND subfocus_id = '$allabs[ability_id]' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id = '16'") or die ("failed getting required abs.");
				$rqabscnt = mysql_num_rows($required_abs);
				while($rqabs = mysql_fetch_assoc($required_abs))
				{					
					if($rqabs[ability_modifier_id] == $current_ab_mod_id)
					{
						// echo"$rqabs[ability_modifier_id] should be $current_ab_mod_id, $rqabscnt, ($curab[ability]) ab1: $allabs[ability]<br>";
						$insert_ability_requires_ability = mysql_query("INSERT INTO ".$slrp_prefix."ability_requires_ability(ability_id,requires_ability_id) VALUES ('$curab[ability_id]','$allabs[ability_id]')") or die("failed inserting ab req ab.");
						$verify_ability_requires_ability = mysql_query("SELECT * FROM ".$slrp_prefix."ability_requires_ability WHERE ability_id = '$curab[ability_id]' AND requires_ability_id = '$allabs[ability_id]'") or die ("failed verifying ab req ab insert.");
					}	
				}
			}
			$get_ability_modifier37 = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '$current_ab_mod_id'") or die("failed getting current ab mod 37.");
			$getabmod37 = mysql_fetch_assoc($get_ability_modifier37);
			
			$get_ab_mod_now = strip_tags(stripslashes($getabmod37[ability_modifier_short])); 
			
			// echo"mod: $getabmod37[3]<br>";
			
			$insert_ability_modifier = mysql_query("INSERT INTO ".$slrp_prefix."ability_ability_modifier (ability_id,ability_modifier_id) VALUES ('$curab[ability_id]','$getabmod37[ability_modifier_id]')") or die("failed inserting ab ab mod.");
			
			$verify_ability_modifier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_ability_modifier WHERE ability_id = '$curab[ability_id]' AND ability_modifier_id = '$getabmod37[ability_modifier_id]'") or die ("failed verifying ab ab mod insert.");
			$verfyabmod = mysql_fetch_assoc($verify_ability_modifier);
			$verfyabmodcnt = mysql_num_rows($verify_ability_modifier);
			
				// change the unlimited flag to the default
			if($getabmod37[ability_modifier_type_id] == '9')
			{
				$update_unlimited_uses_flag2 = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_unlimited_uses = '1' WHERE ability_id = '$curab[ability_id]'") or die ("Failed updating unlim flag to default.");
				
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
				<font color = 'yellow' size ='2'>
				<li>	$final_name's Unlimited flag was successfully changed to 1.
				</font>
				</td>
				</tr>
				";
			}
			// echo"cnt: $verfyabmodcnt<br>";
			
			if($verfyabmodcnt >= 1)
			{
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
				<font color = 'yellow' size ='2'>
				<li>	$final_name is now modified by <i>$get_ab_mod_now</i>.
				</td>
				</tr>
				";
			}
			
			if($verfyabmodcnt == 0)
			{
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
				<font color = 'red' size ='2'>
				<li>	$final_name is not modified by <i>$get_ab_mod_now</i>.  Please try again, or contact an admin if you think there are problems.
				</font>
				</td>
				</tr>
				";
			}
		}
	}
}

// adding base effects tied to effect types

$ab_effect_type_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id > '1' ORDER BY effect_type_support, effect_type") or die ("failed getting effect types list for ab edit form");
while($abefftyplst = mysql_fetch_assoc($ab_effect_type_list))
{
	if(isset($_POST['base_'.$abefftyplst[effect_type].'_id']))
	{
		$base_subfocus_id = $_POST['base_'.$abefftyplst[effect_type].'_id'];
		// echo"base $abefftyplst[1] id: $base_subfocus_id<br>";
		
		$ab_effect_instance = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$base_subfocus_id'") or die ("failed getting effect name.");
		$abeffinst = mysql_fetch_assoc($ab_effect_instance);
		// echo"$abeffinst[1]<br>";
		
		if($base_subfocus_id >= 2)
		{			
			// see if this effect mod is already on the ability
			$ability_existing_effects = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect WHERE ability_id = '$curab[ability_id]' AND effect_id = '$abeffinst[effect_id]'") or die ("failed to get existing ability effects.");
			$abexeffs = mysql_fetch_assoc($ability_existing_effects);
			$abexeffscnt = mysql_num_rows($ability_existing_effects);
			
			$ability_modifier_focus_for_addition = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id = ".$slrp_prefix."ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$abeffinst[effect_id]' AND ".$slrp_prefix."ability_modifier_subfocus.focus_id = '9' AND ".$slrp_prefix."ability_modifier.ability_modifier_type_id = '5'") or die ("failed getting ab mod focus for effect addition.");
			$abmodfocusforadd = mysql_fetch_assoc($ability_modifier_focus_for_addition);
			// echo"$abmodfocusforadd[4], $abmodfocusforadd[3]<br>";
						
			// if the effect is not already tied to that ability, create the relation.
			if($abexeffscnt == 0)
			{
				// echo"foo abid: $curab[ability_id], effid: $abeffinst[effect_id],eftypid: $abefftyplst[effect_type_id], modid: $abmodfocusforadd[ability_modifier_id]<br>";
				$insert_ability_effect = mysql_query("INSERT INTO ".$slrp_prefix."ability_effect (ability_id,effect_id,effect_type_id,effect_modifier_id) VALUES ('$curab[ability_id]','$abeffinst[effect_id]','$abefftyplst[effect_type_id]','$abmodfocusforadd[ability_modifier_id]')") or die("failed to insert ab effect and mod.");
				
				// see if the effect type tier is listed on this ability already for weighting purposes.
				// if larger, leave it alone, but if smaller, raise it to this tier.
							// see if this effect mod is already on the ability
				$ability_existing_effect_types = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '$curab[ability_id]' AND effect_type_id = '$abefftyplst[effect_type_id]'") or die ("failed to get existing ability effect types.");
				$abexefftyps = mysql_fetch_assoc($ability_existing_effect_types);
				$abexefftypscnt = mysql_num_rows($ability_existing_effect_types);
				if($abexefftypscnt >= 1)
				{
					if($abexefftyps[effect_type_tier] < $abeffinst[effect_tier])
					{
						$update_existing_ability_effect_types = mysql_query("UPDATE ".$slrp_prefix."ability_effect_type SET effect_type_tier = '$abeffinst[effect_tier]' WHERE ability_id = '$curab[ability_id]' AND effect_type_id = '$abefftyplst[effect_type_id]'") or die ("failed updating ability effect types for weight.");
					}
				}
				if($abexefftypscnt == 0)
				{
					$insert_ability_effect_types = mysql_query("INSERT INTO ".$slrp_prefix."ability_effect_type (ability_id, effect_type_id, effect_type_tier,slurp_id) VALUES('$curab[ability_id]','$abefftyplst[effect_type_id]','$abeffinst[effect_tier]','2')") or die ("failed updating ability effect types for weight.");
				}

				$verify_ability_effect_relation = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect WHERE ability_id = '$curab[ability_id]' AND effect_id = '$abeffinst[effect_id]' AND effect_modifier_id = '$abmodfocusforadd[ability_modifier_id]'") or die ("failed getting new ab eff relation.");
				$verabeffrelcnt = mysql_num_rows($verify_ability_effect_relation);				
				
				if($verabeffrelcnt >= 1)
				{
					echo"
					<tr>
					<td width = '100%' valign = 'top' align = 'left'>
					<font color = 'yellow' size ='2'>
					<li>	$abeffinst[effect] added to $final_name.
					</font>
					</td>
					</tr>
					";
				}
				
				if($verabeffrelcnt == 0)
				{
					echo"
					<tr>
					<td width = '100%' valign = 'top' align = 'left'>
					<font color = 'yellow' size ='2'>
					<li>	$abeffinst[effect] was NOT added to $final_name.
					</font>
					</td>
					</tr>
					";
				}
			}
			
			// if the effect is already tied to that ability, continue...
			if($abexeffscnt >= 1)
			{
				echo"
				<tr>
				<td width = '100%' valign = 'top' align = 'left'>
				<font color = 'red' size ='2'>
				<li> $aeffinst[effect] is already attached to $final_name.
				</font>
				</td>
				</tr>
				";
			}
		}
	}
}


// deleting base effects tied to effect types
if(isset($_POST['del_ability_effect']))
{
	$del_ability_effect = $_POST['del_ability_effect'];
	
	$delete_ability_effect = mysql_query("DELETE FROM ".$slrp_prefix."ability_effect WHERE ability_effect_id = '$del_ability_effect'") or die ("failed deleting ability effect.");
	
	$verify_deleted_ability_effect = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect WHERE ability_effect_id = '$del_ability_effect'") or die ("failed verifying deleted ability effect.");
	$vrdelabeffcnt = mysql_num_rows($verify_deleted_ability_effect);
	
	if($vrdelabeffcnt == 0)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'yellow' size ='2'>
		<li> $final_name's effect has been deleted.
		</font>
		</td>
		</tr>
		";
	}
	
	if($vrdelabeffcnt >= 1)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'red' size ='2'>
		<li> $final_name's effect has not been deleted. Try again or contact an admin if there is a problem.
		</font>
		</td>
		</tr>
		";
	}
}

if(isset($_POST['ab_ab_attr']))
{
	if(isset($_POST['ability_unlimited_uses']))
	{
		$ability_unlimited_uses = $_POST['ability_unlimited_uses'];
		$ability_charges_cost = $_POST['charges_cost'];
		// echo"UNLIM: $ability_unlimited_uses,charges: $ability_charges_cost<br>";
		
		$update_ability_unlimited_uses = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_unlimited_uses = '$ability_unlimited_uses' WHERE ability_id = '$curab[ability_id]'") or die ("failed to update unlimited flag.");
		$update_ability_charges_cost = mysql_query("UPDATE ".$slrp_prefix."ability_cost SET ability_cost = '$ability_charges_cost' WHERE ability_id = '$curab[ability_id]'") or die ("failed to update unlimited flag.");
	
		$verify_new_unlim_status = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$curab[ability_id]' AND ability_unlimited_uses = '$ability_unlimited_uses'") or die ("failed verifying changed unlim status.");
		$vernewunlmnfo = mysql_fetch_assoc($verify_new_unlim_status);
		$vernewunlmnfocnt = mysql_num_rows($verify_new_unlim_status);
		
		if($vernewunlmnfocnt == 1)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'yellow' size ='2'>
			<li>	$final_name's Unlimited flag was successfully changed to $vernewunlmnfo[ability_unlimited_uses].
			</font>
			</td>
			</tr>
			";
		}
		
		if($vernewunlmnfocnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' valign = 'top' align = 'left'>
			<font color = 'red' size ='2'>
			<li>	$final_name did not update properly. Please try again, or contact an admin if you think something is wrong.
			</font>
			</td>
			</tr>
			";
		}
	
	}
	
	// changing cost type and value
		
	if(isset($_POST['attr_id']))
	{
		$attribute_id_cost = $_POST['attr_id'];
		// echo"attr: $attrbtlst[0], j/c: $attribute_id_cost<br>";
		
		$get_attributes_list = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id > '1'") or die ("failed getting attr_type list.");
		$getattrbtlstcnt = mysql_num_rows($get_attributes_list);
		while($attrbtlst = mysql_fetch_assoc($get_attributes_list))
		{
			// clean up the tags
			$attrib_list_name = strip_tags(stripslashes($attrbtlst[attribute_type_id]));
			// echo"attr: $attrbtlst[0], j/c: $attribute_id_cost<br>";
			$cost_increment = 0;
			while($cost_increment <= 99)
			{
				$attribute_increment_cost = $attrbtlst[attribute_type_id].$cost_increment;
				if($attribute_increment_cost == $attribute_id_cost)
				{
					$req_attribute_id = $attrbtlst[attribute_type_id];
					$current_ab_cost = $cost_increment;
					$cost_increment = 99;
					// echo"attr: $req_attribute_id, cost: $current_ab_cost, j/c: $attribute_id_cost<br>";
				}
				
				$cost_increment++;
			}
		}
	}
	
	if(empty($_POST['attr_id']))
	{
		$get_current_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '$curab[ability_id]'") or die ("failed getting existing required attr cost.");
		$getcrrcost = mysql_fetch_assoc($get_current_cost);
		$req_attribute_id = $getcrrcost[attribute_type_id];
		$current_ab_cost = $getcrrcost[ability_cost];
	}
	// echo"$current_ab_cost, $req_attribute_id<br>";
	$get_existing_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '$curab[ability_id]'") or die ("failed getting existing required attr cost.");
	$getexcostcnt = mysql_num_rows($get_existing_cost);
		
	if($getexcostcnt == 0)
	{	
		$insert_new_cost = mysql_query("INSERT INTO ".$slrp_prefix."ability_cost (ability_cost,ability_id,attribute_type_id) VALUES ('$current_ab_cost','$curab[ability_id]','$req_attribute_id')") or die ("failed inserting new cost.");
		$get_existing_cost_2 = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '$curab[ability_id]'") or die ("failed getting existing required attr cost.");
		$getexcost = mysql_fetch_assoc($get_existing_cost_2);
	}
		
	if($getexcostcnt >= 1)
	{
		$getexcost = mysql_fetch_assoc($get_existing_cost);
		
		$update_existing_cost = mysql_query("UPDATE ".$slrp_prefix."ability_cost SET ability_cost='$current_ab_cost',attribute_type_id='$req_attribute_id' WHERE ability_cost_id = '$getexcost[ability_cost_id]'") or die ("failed updating new cost.");
	}
		
	$verify_new_cost_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '$curab[ability_id]' AND attribute_type_id = '$req_attribute_id' AND ability_cost = '$current_ab_cost'") or die ("failed verifying changed attr cost.");
	$vernewcstnfo = mysql_fetch_assoc($verify_new_cost_info);
	$vernewcstnfocnt = mysql_num_rows($verify_new_cost_info);
		
	if($vernewcstnfocnt == 1)
	{		
		$get_attribute_name = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id = '$vernewcstnfo[attribute_type_id]'") or die ("failed getting attr_type info.");
		$getattrbtnmcnt = mysql_num_rows($get_attribute_name);
		$getattrbtnm = mysql_fetch_assoc($get_attribute_name);

		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'yellow' size ='2'>
		<li>	$final_name's cost was successfully changed to $vernewcstnfo[ability_cost] $getattrbtnm[attribute_type].
		</font>
		</td>
		</tr>
		";
	}

	if($vernewcstnfocnt == 0)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'red' size ='2'>
		<li>	$final_name did not update properly. Please try again, or contact an admin if you think something is wrong.
		</font>
		</td>
		</tr>
	";
	}
}

// adding new base effect types

if(isset($_POST['new_req_eff']))
{	
	$new_req_eff = $_POST['new_req_eff'];
	$new_req_tier = $_POST['new_req_tier'];
	
	$get_existing_effect_type = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '$curab[ability_id]' AND effect_type_id = '$new_req_eff'") or die ("failed getting existing required effect.");
	$getexeffcnt = mysql_num_rows($get_existing_effect_type);
	
	// echo"cnt: $getexcostcnt, new_tier: $new_req_tier<br>";
	
	if($getexeffcnt == 0)
	{	
		$insert_new_eff = mysql_query("INSERT INTO ".$slrp_prefix."ability_effect_type (ability_id,effect_type_id,effect_type_tier) VALUES ('$curab[ability_id]','$new_req_eff','$new_req_tier')") or die ("failed inserting new req eff.");
	}
	
	if($getexeffcnt == 1)
	{
		while($getexeff = mysql_fetch_assoc($get_existing_effect_type))
		{
			$update_existing_eff = mysql_query("UPDATE ".$slrp_prefix."ability_effect_type SET effect_type_tier = '$new_req_tier' WHERE ability_id = '$curab[ability_id]' AND effect_type_id = '$new_req_eff'") or die ("failed updating new cost.");
		}
	}
	
	$verify_new_eff_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '$curab[ability_id]' AND effect_type_id = '$new_req_eff' AND effect_type_tier = '$new_req_tier'") or die ("failed verifying new req eff.");
	$verneweffnfo = mysql_fetch_assoc($verify_new_eff_info);
	$verneweffnfocnt = mysql_num_rows($verify_new_eff_info);
	
	if($verneweffnfocnt == 1)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'yellow' size ='2'>
		<li>	$final_name's requirements were successfully changed.
		</font>
		</td>
		</tr>
		";
	}
	
	if($verneweffnfocnt == 0)
	{
		echo"
		<tr>
		<td width = '100%' valign = 'top' align = 'left'>
		<font color = 'red' size ='2'>
		<li>	$final_name did not update properly. Please try again, or contact an admin if you think something is wrong.
		</font>
		</td>
		</tr>
		";
	}
}

echo"
		<tr height='9' background='themes/RedShores/images/row2.gif' height='9'>
			<td>
			
			</td>
		</tr>
		<tr>
			<td width='100%' align='left' valign='top' colspan = '3'>
				<table border='0' width='100%' cellpadding='0' cellspacing='0'>
					<tr height='9' background='themes/RedShores/images/base1.gif' height='24'>
						<form name = 'back_to_mod_edit' method='post' action = 'modules.php?name=$module_name&file=ab_edit'>
						<td width='33%' align='left' valign='middle'>
";

if(empty($_POST['ab_del']))
{
	echo"
							<input type='hidden' value='ab' name='current_expander'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='$curab[ability_id]' name='current_ab_id'>
							<input class='submit3' type='submit' value='Back to $final_name' name='back_to_ab_edit'>
	";
}

echo"
						</td>
						</form>
						<form name = 'go_to_ab_list' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
						<td width='33%' align='center' valign='middle'>
							<input type='hidden' value='ab' name='current_expander'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input class='submit3' type='submit' value='Abilities List' name='go_to_ab_list'>
						</td>
						</form>
						<form name = 'back_to_ab_edit' method='post' action = 'modules.php?name=$module_name'>
						<td width='33%' align='right' valign='middle'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='1' name='ab_expander'>
							<input class='submit3' type='submit' value='Back to Main' name='back_to_main'>
						</td>
					</tr>
				</form>
				</table>
			</td>
		</tr>
";


include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>