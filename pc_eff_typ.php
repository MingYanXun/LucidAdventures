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
$nav_title = "STEP 3 - ABILITIES";
include("modules/$module_name/includes/slurp_header.php");

// checkbox variables for the index
if(isset($_POST['current_expander']))
{
	$expander_abbr = $_POST['current_expander'];
	$expander = ($expander_abbr."_expander");
}
// echo"exp: $expander_abbr, $expander<br>";

if(isset($_POST['ab_poolblock_expander']))
{
	$ab_poolblock_expander = $_POST['ab_poolblock_expander'];
}
else
{
 $ab_poolblock_expander = 1;
}
if(isset($_POST['ab_racial_expander']))
{
	$ab_racial_expander = $_POST['ab_racial_expander'];
}
else
{
 $ab_racial_expander = 1;
}
if(isset($_POST['ab_learned_expander']))
{
	$ab_learned_expander = $_POST['ab_learned_expander'];
}
else
{
 $ab_learned_expander = 1;
}
if(isset($_POST['ab_studying_expander']))
{
	$ab_studying_expander = $_POST['ab_studying_expander'];
}
else
{
 $ab_studying_expander = 1;
}
if(isset($_POST['ab_unknown_expander']))
{
	$ab_unknown_expander = $_POST['ab_unknown_expander'];
}
else
{
 $ab_unknown_expander = 1;
}

//reset button handlet
if(isset($_POST['pc_reset']))
{
	$clear_pc_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id != '12'") or die ("failed getting PC abs for reset");
	while($clrpcabs = mysql_fetch_assoc($clear_pc_abilities))
	{
		// echo"$clrpcabs[creature_ability_id], $clrpcabs[ability_id]<br>";
		$delete_them_all = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE ".$slrp_prefix."creature_ability.creature_ability_id = '$clrpcabs[creature_ability_id]'") or die ("failed deleting cr abilities for reset");
	}
	
	include("modules/$module_name/includes/fn_pc_race_reset.php");
}

// abilities
// addition or subtraction from the efftype page
if(isset($_POST['pcabid']))
{
	$pcabid = $_POST['pcabid'];
	$pcabchg = $_POST['pcabchg'];
	$modified_build_cost = $_POST['final_build_cost'];
	// echo"chg: $pcabchg, pcabid: $pcabid, modcost: $modified_build_cost<br>";
	
// CRAFT DELAY REMOVED
//	if($pcabchg == '99')
//	{
//		$pcab = $_POST['pcab'];
//		$grad_pc_ability_name = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_ability_id = '$pcab'") or die("failed to get removed ability names.");
//		$grdpcabnm = mysql_fetch_assoc($grad_pc_ability_name);
//		$check_creature_ability_now = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_ability_id = '$pcab'") or die ("failed getting ownership.");
//		$chkpcabnow = mysql_fetch_assoc($check_creature_ability_now);
//		
//		$diploma_ability_studied = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET creature_ability_level = '0' WHERE creature_ability_id = '$pcab'") or die ("failed granting studied ability diploma.");		
//		
//		if($curpcnfo[creature_status_id] == 4)
//		{
//			$xp_change = 0;
//			$new_xp_after_diploma = $curpcnfo[creature_xp_current];
//			$reason = "Admin Grant of Diploma for $grdpcabnm[ability]. No XP Change. Total XP: $new_xp_after_diploma";
//			$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','+$xp_change','$usrnfo[user_id]','$reason')");	
//		}
//		
//		echo"
//		<tr>
//			<td colspan='9'>
//				<font color = 'yellow'><b>
//				<li>$curpcnfo[creature] was graduated with honors in $grdpcabnm[ability].
//			</td>
//		</tr>
//		";
//	}
	
	// deletion
	if($pcabchg == '0')
	{
		$reason = "";
		$pcab = $_POST['pcab'];
		if(isset($_POST['pcabcnt']))
		{
			$rem_pc_ability_name = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_ability_id = '$pcab'") or die("failed to get diploma ability names.");
			$rempcabnm = mysql_fetch_assoc($rem_pc_ability_name);			
			
			$pcabcnt = $_POST['pcabcnt'];
			if($pcabcnt >= 2)
			{
				// echo"<b><font color='purple'>$pcabcnt decrement</font></b><br>";
				$decrement_ability_count = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET creature_ability_count=creature_ability_count-1 WHERE creature_ability_id = '$pcab'") or die ("failed updating pc ab count.");
			}
			if($pcabcnt <= 1)
			{
				// echo"<b><font color='purple'>$pcabcnt delete</font></b><br>";
				$rem_pc_ab = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE creature_ability_id = '$pcab'") or die("remove pc ability failed.");
			}
		}
		if(empty($_POST['pcabcnt']))
		{
			$rem_pc_ab = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE creature_ability_id = '$pcab'") or die("remove pc ability failed.");
		}
		
		$del_requirement_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$pcabid' AND ".$slrp_prefix."ability_modifier_subfocus.focus_id = '2' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id = '16'") or die ("failed verifying required modifier.");
		$delrqabnfo = mysql_fetch_assoc($del_requirement_ability_info);
		$delrqabnfocnt = mysql_num_rows($del_requirement_ability_info);
		
		$get_del_required_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '$delrqabnfo[ability_modifier_id]'");
		while($gtdelrqabs = mysql_fetch_assoc($get_del_required_abilities))
		{
			$rem_req_pc_ab = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$gtdelrqabs[ability_id]'") or die("remove pc req ability failed.");
			$reason = $reason."Removed $gtdelrqabs[ability] due to dependence on $rempcabnm[ability]. ";
		}
		
		$check_ability_sow = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '21' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed getting SoW ownership.");
		$chkabsow = mysql_fetch_assoc($check_ability_sow);
		$chkabsowcnt = mysql_num_rows($check_ability_sow);
		if($chkabsowcnt == 0)
		{
			$check_ability_sow_sm = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_ability_level = '1' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed getting free SoW Source Mark ownership.");
			$chkabsowsm = mysql_fetch_assoc($check_ability_sow_sm);
			// echo"$chkabsowsm[ability_id]<br>";
			$chkabsowsmcnt = mysql_num_rows($check_ability_sow_sm);
			if($chkabsowsmcnt >= 1)
			{
				$rem_sow_dependent = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE creature_ability_id = '$chkabsowsm[creature_ability_id]'") or die("remove sow dep ability failed.");
			}
		}
		
		$xp_change = $modified_build_cost;
		$increment_experience = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_xp_current = (creature_xp_current+$xp_change) WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating experience for deleted ability.");
		
		if($curpcnfo[creature_status_id] == 4)
		{
			$new_xp_after_selling = $curpcnfo[creature_xp_current]+$xp_change;
			$reason = $reason."Removed Ability: ".$rempcabnm[ability]." for +$xp_change Build. New Total: $new_xp_after_selling";
			$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','+$xp_change','$usrnfo[user_id]','$reason')");	
		}	
		
		if($pcabcnt <= 1)
		{
			echo"
<tr>
	<td colspan='9'>
		<font color = 'yellow'><b>
		<li>$curpcnfo[creature] no longer knows $rempcabnm[ability] (<font color='#33F406'>+$modified_build_cost</font>).</b>
	</td>
</tr>
			";
		}
		if($pcabcnt >= 2)
		{
			$decabcnt = $pcabcnt-1;
			echo"
<tr>
	<td colspan='9'>
		<font color = 'yellow'><b>
		<li>$curpcnfo[creature] lowered $rempcabnm[ability] by one; $decabcnt left. (<font color='#33F406'>+$modified_build_cost</font>).</b>
	</td>
</tr>
			";
		}
	}
	//end deletion
	
	// addition
	if($pcabchg == 1)
	{
		$new_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$pcabid' AND object_random_current = '1' AND object_focus_id = '2' AND object_slurp_id = '$slrpnfo[slurp_id]'")or die ("failed getting new object random for PC insert.");
		$newabrnd = mysql_fetch_assoc($new_ability_random);
		
		if(isset($_POST['pcabcnt']))
		{
			$pcabcnt = $_POST['pcabcnt'];
			
			if(isset($_POST['pcab']))
			{
				$pcab = $_POST['pcab'];
			}
			
			// echo"<b><font color='purple'>pcab: $pcab, pcabcnt: $pcabcnt</font></b><br>";
			
			// Student of War Source Mark Choices
			if($pcabcnt <= (-1100))
			{
			 	if($pcabcnt >= (-1199))
				{
					$sow_ab_level = $_POST['this_sow_ab_level'];
					$query_sow_pc_ab = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$pcabid' AND creature_id = '$curpcnfo[creature_id]' AND creature_ability_level = '$sow_ab_level'") or die("failed to query pc ability.");
					$qrysowpcab = mysql_fetch_assoc($query_sow_pc_ab);
					$qrysowpcabcnt = mysql_num_rows($query_sow_pc_ab);
					
					if($qrysowpcabcnt == 0)
					{
						// echo"<b><font color='purple'>$pcabcnt</font></b><br>";
						$crabcnt = 1;
						$new_coded_ability = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count,creature_ability_level) VALUES ('$curpcnfo[creature_id]','$pcabid','$newabrnd[object_random_id]','0','$crabcnt','$sow_ab_level')");
					}
				}
			}
			
			//free domain abilities every 5 levels
			if($pcabcnt <= (-1000))
			{
			 	if($pcabcnt >= (-1099))
				{
					$free_ab_level = $_POST['this_free_ab_level'];
					// guard against the refresh
					$query_new_pc_ab = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$pcabid' AND creature_id = '$curpcnfo[creature_id]' AND creature_ability_level = '$free_ab_level'") or die("failed to query pc ability.");
					$qrypcab = mysql_fetch_assoc($query_new_pc_ab);
					$qrypcabcnt = mysql_num_rows($query_new_pc_ab);
					
					if($qrypcabcnt == 0)
					{
						// echo"<b><font color='purple'>$pcabcnt</font></b><br>";
						$crabcnt = 1;
						$new_coded_ability = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count,creature_ability_level) VALUES ('$curpcnfo[creature_id]','$pcabid','$newabrnd[object_random_id]','$modified_build_cost','$crabcnt','$free_ab_level')");
					}
				}
			}
			
			// Ability is being studied; XP will be spent, but the ability will not be available; 99 is for Elves
			if($pcabcnt <= (-900))
			{
				if($pcabcnt >= (-998))
				{
					// based on 2x the Tier of the Ability
					$pcabstudy = -($_POST['pcabstudy']);
					// echo"time left: $pcabstudy months<br>";
					// guard against the refresh
					// using the level negative to indicate an ability undr study; paid for, but not yet available
					$query_new_pc_ab = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$pcabid' AND creature_id = '$curpcnfo[creature_id]'") or die("failed to query pc ability.");
					$qrypcab = mysql_fetch_assoc($query_new_pc_ab);
					$qrypcabcnt = mysql_num_rows($query_new_pc_ab);
					// if they don't have the ability
					if($qrypcabcnt == 0)
					{
						// echo"<b><font color='purple'>$pcabcnt</font></b><br>";
						$crabcnt = 1;
						$new_coded_ability = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count,creature_ability_level) VALUES ('$curpcnfo[creature_id]','$pcabid','$newabrnd[object_random_id]','$modified_build_cost','$crabcnt','$pcabstudy')");
					}
				}
			}
			
			// pool addition 
			if($pcabcnt == 0)
			{
				// guard against the refresh
				$query_new_pc_ab = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_ability_id = '$pcab'") or die("failed to query pc ability.");
				$qrypcab = mysql_fetch_assoc($query_new_pc_ab);
				$qrypcabcnt = mysql_num_rows($query_new_pc_ab);
				if($qrypcabcnt == 0)
				{
					// echo"<b><font color='purple'>$pcabcnt</font></b><br>";
					$crabcnt = 1;
					$new_coded_ability = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count) VALUES ('$curpcnfo[creature_id]','$pcabid','$newabrnd[object_random_id]','$modified_build_cost','$crabcnt')");
				}
				if($qrypcabcnt == 1)
				{
					// echo"<b><font color='purple'>$pcabcnt</font></b><br>";
					$update_coded_ability = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET creature_ability_count = creature_ability_count+1 WHERE creature_ability_id = '$pcab'") or die ("failed updating pc ab count.");
					
				}
			}
			// multiple puchase abilities, including pool blocks
			if($pcabcnt >= 1)
			{
				$crabcnt = $pcabcnt+1;
				$increment_ability_count = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET creature_ability_count = '$crabcnt' WHERE creature_ability_id = '$pcab'") or die ("failed updating pc ab count.");
			}
			
			//	Racial Abilities		
			if($pcabcnt == (-999))
			{
				$get_new_race_ch_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id = '18'") or die("failed to get race pool abilities.");
				$gtnewrcchabscnt = mysql_num_rows($get_new_race_ch_abilities);
				
				$check_multiple_race_pools = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_ability.creature_ability_count = '-1'") or die("failed to check multiple race pool abilities.");	
				$chkmltrcplscnt = mysql_num_rows($check_multiple_race_pools);	
				
				while($gtnewrcchabs = mysql_fetch_assoc($get_new_race_ch_abilities))
				{
					// echo"limit: $total_race_abilities_limit vs current: $chkmltrcplscnt ($gtnewrcabscnt+$verpcrc[creature_subtype_race_pool_limit]); $gtnewrcchabs[ability] ($gtnewrcchabs[ability_id] = $pcabid)<br>";
					if($total_race_abilities_limit > $chkmltrcplscnt)
					{
						$pcab = $gtnewrcchabs[creature_ability_id];
						if($gtnewrcchabs[ability_id] == $pcabid)
						{
							$crabcnt = "-1";
							// echo"$crabcnt<br>";
							$increment_ability_count = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET creature_ability_count = '$crabcnt' WHERE creature_ability_id = '$pcab'") or die ("failed updating race pool ab count.");
						}
					}
					if($total_race_abilities_limit <= $chkmltrcplscnt)
					{					
						$delete_ability_count = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE creature_ability_id = '$pcab'") or die ("failed cleaning race pool ab count.");
					}															
				}				
			}		
		}
		else
		{
			// guard against the refresh
			$query_new_pc_ab = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$pcabid' AND creature_id = '$curpcnfo[creature_id]' AND creature_ability_level <= '0'") or die("failed to query pc ability.");
			$qrypcab = mysql_fetch_assoc($query_new_pc_ab);
			$qrypcabcnt = mysql_num_rows($query_new_pc_ab);
			if($qrypcabcnt >= 1)
			{
				if($qrypcab[creature_ability_count] >= 1)
				{
					$get_new_ability_limit = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability = '$qrypcab[ability_id]'") or die("failed to get ability limit.");
					$gtnewablmt = mysql_fetch_assoc($get_new_ability_limit);
					
					// echo"<b><font color='purple'>$pcabcnt 23</font></b><br>";
					if($gtnewablmt[ability_count_max] > $qrypcab[creature_ability_count])
					{
						$increment_ability_count = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET creature_ability_count=creature_ability_count+1 WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$pcabid'") or die ("failed refersh guard update of pc ab count.");
					}
				}
			}
			if($qrypcabcnt == 0)
			{
				// echo"ab cost: $modified_build_cost<br>";
				$crabcnt = 1;
				$new_coded_ability = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count) VALUES ('$curpcnfo[creature_id]','$pcabid','$newabrnd[object_random_id]','$modified_build_cost','$crabcnt')");
				// change out non-Racial Aptitudes for Student of War Aptitides
				if($pcabid == 21)
				{
					$sow_apt_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '160' AND object_random_current = '1' AND object_focus_id = '2' AND object_slurp_id = '$slrpnfo[slurp_id]'")or die ("failed getting new object random for SoW aptitude insert.");
					$sowaptabrnd = mysql_fetch_assoc($sow_apt_ability_random);
					
					$creature_aptitudes = 0;
					// count Aptitudes
					$get_pc_nonrace_aptitudes = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability LIKE '%Aptitude%' AND ".$slrp_prefix."ability.ability_set_id != '12'") or die ("failed getting aptitudes.");
					$curpcnnrcaptscnt = mysql_num_rows($get_pc_nonrace_aptitudes);
					while($curpcnnrcapts = mysql_fetch_assoc($get_pc_nonrace_aptitudes))
					{
						$creature_aptitudes_count = $creature_aptitudes_count+$curpcnnrcapts[creature_ability_count];
						
						$clear_aptitudes = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE creature_ability_id = '$curpcnnrcapts[creature_ability_id]'") or die ("failed clearing aptitudes for SoW.");	
						if($creature_aptitudes_count == 1)
						{
							$new_aptitudes = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count) VALUES ('$curpcnfo[creature_id]','160','$sowaptabrnd[object_random_id]','15','$creature_aptitudes_count')") or die ("failed converting aptitudes for SoW.");
						}
						if($creature_aptitudes_count >= 2)
						{
							$new_aptitudes = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET creature_ability_count = '$creature_aptitudes_count' WHERE creature_ability_id = '$curpcnnrcapts[creature_ability_id]'") or die ("failed converting aptitudes for SoW.");
						}
					}
					// echo"SoW aptitudes: $creature_aptitudes_count, rndID: $sowaptabrnd[object_random_id]<br>";
				
				}
			}
			// uncomment the next IF to check for duplicate entries if you think they're hiding
			if($qrypcabcnt >= 2)
			{
				echo"
<tr>
	<td colspan='9'>
		<font color = 'yellow'><b>
		<li>$curpcnfo[creature] has $qrypcabcnt versions of $newpcabnm[ability] <br><br><font color='red'>Inform an Admin, and so on...(!)</font>.</b>
	</td>
</tr>
				";
			}
		}
		
		// verify the new entry and print
		$get_new_pc_ab = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$pcabid' AND creature_id = '$curpcnfo[creature_id]'") or die("failed to get new pc ability.");
		$newpcabcnt = mysql_num_rows($get_new_pc_ab);
		
		while($newpcab = mysql_fetch_assoc($get_new_pc_ab))
		{
			$new_pc_ability_name = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$pcabid'") or die("failed to get ability names.");
			$newpcabnm = mysql_fetch_assoc($new_pc_ability_name);
			// Spend Build/XP
			$xp_change = $modified_build_cost;
			$decrement_experience = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_xp_current = (creature_xp_current-$xp_change) WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating experience for new ability.");
			// if the character is approved, log it by the current user.
			if($curpcnfo[creature_status_id] == 4)
			{
				if($pcabcnt >= (-899))
				{
					$new_xp_after_buying = $curpcnfo[creature_xp_current]-$xp_change;
					$reason = "[$curpcstts[slurp_status]] Added Ability: $newpcabnm[ability] for -$modified_build_cost. New Total: $new_xp_after_buying"; 							
					$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','-$xp_change','$usrnfo[user_id]','$reason')");	
					// echo"$xp_change xp: $reason<br>"; 
				}
//				if($pcabcnt >= (-998) AND $pcabcnt <= (-900))
//				{
//					$new_xp_after_buying = $curpcnfo[creature_xp_current]-$xp_change;
//					$reason = "[$curpcstts[slurp_status]] Studying Ability: $newpcabnm[ability] for -$modified_build_cost. New Total: $new_xp_after_buying"; 							
//					$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','-$xp_change','$usrnfo[user_id]','$reason')");	
//					// echo"$xp_change xp: $reason<br>"; 
				}
				if($pcabcnt <= (-1000))
				{
					$new_xp_after_buying = $curpcnfo[creature_xp_current]-$xp_change;
					$reason = "[$curpcstts[slurp_status]] Free Ability for reaching Level $free_ab_level: $newpcabnm[ability]. New Total: $new_xp_after_buying"; 							
					$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','-$xp_change','$usrnfo[user_id]','$reason')");	
					// echo"$xp_change xp: $reason<br>"; 
				}
			}
			
			echo"
<tr>
	<td colspan='9'>
		<font color = 'yellow'><b>
		<li>$curpcnfo[creature] has mastered $newpcabnm[ability] (<font color='red'>-$modified_build_cost</font>).</b>
	</td>
</tr>
			";
		}
	// end addition
	}
// end subtraction/addition

include("modules/$module_name/includes/pcinfo.php");
// begin top section and left panel
echo"
<tr>
	<td colspan = '5'>
		<br>
			<table border='0' cellpadding='0' cellspacing='0' width = '100%'>
				<tr>
					<td valign = 'middle' align = 'left' width='100%'>
";

// dressing for the controls at the top
OpenTable3();

echo"
						<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
							<tr>
								<td valign = 'middle' align = 'right'>
									<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
										<tr>
											<form name = 'ab_list' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
											<td valign = 'middle' align = 'right'>
												<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
												<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
												<input class='submit3' type='submit' value='Abilities List' name='ab_list'>
											</td>
											</form>
										</tr>
";
								
if($compab_expander == 1)
{
	echo"	
										<tr>
											<form name = 'show_hide_components' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
											<td valign = 'middle' align = 'right'>
												<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
												<input type='hidden' value='$expander_abbr' name='current_expander'>
												<input type='hidden' value='$compab_expander' name = 'compab_expander'>
												<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
												<input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'>
												<input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'>
												<input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'>
												<input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'>
												<input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'>
												<input type='hidden' value='$admin_expander' name = 'admin_expander'>
												<input type='hidden' value='0' name = 'compab_expander'>
												<input class='submit3' type='submit' value='Hide Components' name = 'show_hide_components'>
											</td>
											</form>
										</tr>
	";
}

if($compab_expander == 0)
{
	echo"
										<tr>
											<form name = 'show_hide_components' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
											<td valign = 'middle' align = 'right'>
												<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
												<input type='hidden' value='$expander_abbr' name='current_expander'>
												<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>	
												<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
												<input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'>
												<input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'>
												<input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'>
												<input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'>
												<input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'>
												<input type='hidden' value='$admin_expander' name = 'admin_expander'>
												<input type='hidden' value='1' name = 'compab_expander'>
												<input class='submit3' type='submit' value='Show Components' name = 'show_hide_components'>
											</td>
											</form>
										</tr>
	";
}

if($curpcnfo[creature_status_id] >= 2)
{
	if($curpcnfo[creature_status_id] <= 3)
	{
echo"
										<tr>
											<form name = 'pc_edit' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
											<td valign = 'middle' align = 'right'>							
												<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
												<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
												<input class='submit3' type='submit' value='Reset $curpcnfo[creature]' name='pc_reset'>
											</td>
											</form>
										</tr>
		";
	}
}

echo"
									</table>
								</td>
								<td width='2%'>
								&nbsp;
								</td>
								<td valign = 'middle' align = 'left'>
									<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
										<tr>
											<form name = 'show_hide_ntro' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
											<td valign = 'middle' align = 'left'>
";
		
if($ntro_expander == 0)
{
	echo"
									<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
									<input type='hidden' value='1' name = 'ntro_expander'>
									<input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'>
									<input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'>
									<input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'>
									<input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'>
									<input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'>
									<input class='submit3' type='submit' value='Show Instructions'>
		";
}
if($ntro_expander == 1)
{
	echo"
									<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
									<input type='hidden' value='0' name = 'ntro_expander'>
									<input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'>
									<input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'>
									<input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'>
									<input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'>
									<input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'>
									<input class='submit3' type='submit' value='Hide Instructions'>
	";
}

echo"
											</td>
											</form>
										</tr>
";
if($curpcnfo[creature_status_id] <= 3)
{
	echo"
										<tr>
											<form name = 'pc_edit' method='post' action = 'modules.php?name=$module_name&file=pc_bg'>
											<td valign = 'middle' align = 'left'>
												<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
												<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
												<input class='submit3' type='submit' value='Edit Background' name='pc_edit'>
												</font>
											</td>
											</form>
										</tr>
	";
}
echo"										<tr>
											<form name = 'pc_edit' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
											<td valign = 'middle' align = 'left'>
												<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
												<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
												<input class='submit3' type='submit' value='Back to $curpcnfo[creature]' name='pc_edit'>
												</font>
											</td>
											</form>
										</tr>
									</table>
								</td>
								<td width = '2%'>
								&nbsp;
								</td>
								
								<form name = 'eff_typ_dep' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
								<td align = 'center' valign = 'middle'>
									<select class='engine' name = 'core_ab_id'>
									<option value='1'>Reset Tree</option>
	";

$core_dependent_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability LIKE '%Apprentice%' OR ability LIKE '%Pool Block%' ORDER BY ability") or die ("failed getting apprentice-level abilities.");
while($coredepabs = mysql_fetch_assoc($core_dependent_abilities))
{
	echo"				<option value='$coredepabs[ability_id]'>$coredepabs[ability]</option>";
	// echo"<option value='$coredepabs[ability_id]'>$coredepabs[ability]</option>";
}

$condensed = 1;
echo"
										</select>
										<br>
										<input type='hidden' value='ab' name='current_expander'>
										<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
										<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
										<input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'>
										<input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'>
										<input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'>
										<input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'>
										<input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'>
										<font color = 'yellow' size = '2'>
										<input class='submit3' type='submit' value='Show Tree' name='eff_typ_dep'>
									</td>
									</form>
									<td width = '2%'>
									&nbsp;
									</td>
									<td valign = 'middle' align = 'right'>
";

if($curpcnfo[creature_status_id] == 4)
{
	if($curpcnfo[creature_xp_current] >= 1)
	{
		echo"<font class='heading2'>BUILD LEFT<br><font color = '#33F406' size = '3'>$build_left</b></font></font>";
	}
	
	if($curpcnfo[creature_xp_current] <= 0)
	{
		echo"<font class='heading2'>BUILD LEFT<br>You have 0 left.</font>
		";
	}
}
if($curpcnfo[creature_status_id] <= 3)
{
	if($build_left >= 1)
	{
		echo"<font class='heading2'>BUILD LEFT<br><font color = '#33F406' size = '3'>$build_left</b></font></font>";
	}

	if($build_left <= 0)
	{
		if($curpcnfo[creature_xp_current] == 0)
		{
			echo"<font class='heading2'>BUILD LEFT<br>You have 0 left.</font>";
		}
	}
}

echo"
									</td>
								</tr>
";

if($ntro_expander == 1)
{
	echo"
								<tr height='9'>
									<td width = '100%' align = 'left' valign = 'middle' colspan = '11'>
									<td>
								</tr>
								<tr>
									<td width = '100%' align = 'left' valign = 'middle' colspan = '11'>
										<font class = 'heading1'>Above this description are buttons that take you to:
										<li> The <b>[Abilities list]</b>, where you can filter all the Abilities in different ways to help choose between them.
	";
	
	if($curpcnfo[creature_status_id] >= 2)
	{
		if($curpcnfo[creature_status_id] <= 3)
		{
			echo"<li> <b>[Reset $curpcnfo[creature]]</b>. This is available only at Creation, and removes all Build Points expenditures and Resets Racial Choice/Pool Abilities, making the Character new again. ";
		}
	}
	
	echo"
							<li> The button that opens these <b>[Instructions]</b>.
							<li> <b>[Return to $curpcnfo[creature]]</b> takes you back to $curpcnfo[creature]'s main page.
							<li> A <b>drop-down list</b> of entry-level skills shows to the right of the buttons. Choosing one and clicking <b>[Show Tree]</b> will show six levels deep of dependency, linked to the 'Must know [Ability Name]' modifier.
							<br>
							<br>
							<b>Pool Blocks</b> may be purchased on the left at a rate of 4 slots for 5 points; a list of the skills each Pool represents is available by hovering over the Pool Name or by using the Show Tree button above.
							<br>
							<br>
							$curpcnfo[creature] <b>Known Abilities</b> are to the left, with <b>Racial Abilities</b> in green. Build costs are listed under the <b>[Buy/Sell]</b> buttons, or if you have more than one of an Ability, under the <b>[+/-]</b> buttons.
							<b>Unknown Abilities</b> for which you qualify are to the right. If you want something and don't see it over there, click the Ability's name on the Abilities List to get the full page description, and make sure you qualify.
							Costs should already be adjusted for racial consideration, if they apply.
							<br>
							<br>
							Abilities have under their names the set they are in (<b>Constant, Craft, X Pool, Y Domain, or Advanced Arts</b>), their Special Effect (<b>Normal/Prime, Magic/Eldritch, or Elemental: Fire/Earth/Ice/Lightning</b>), the <b>Build Cost</b> including racial adjustments, and any normal <b>Level minimums</b> (non-Domain Build expenditure minimums are shown on the Ability info page). In the center of each Ability listing will be any <b>graphic</b> associated with the ability, plus the <b>Support Effects</b> every Ability needs to work, like <b>Delivery/Range, Size/Target, and Time (Duration, Frequency, etc.)</b>. The right pane of each Ability shows its <b>Verbal (mechanics only; Incantations are listed in the Description)</b> and a long <b>Description</b> of its function, followed by any <b>modifiers</b> like <i>Only usable in Moonlight</i> or <i>Requires Surprise</i>.
							</font>
						</td>
					</tr>
	";
}

echo"
				</table>
";

CloseTable3();
// end control panel

echo"
			</td>
		</tr>
		<tr>
			<td valign = 'top' align = 'left' width='100%'>
";

if(isset($_POST['core_ab_id']))
{
	$ab_dep_id = $_POST['core_ab_id'];
	if($ab_dep_id >= 2)
	{
		OpenTable3();
		
		$condensed = '1';
		if($ab_dep_id >= '2')
		{
			include("modules/$module_name/includes/fn_ab_dep.php");
		}
		CloseTable3();
	}
}

echo"
				</td>
			</tr>
		</table>
	</td>
</tr>
";
// end right panel and top section

// begin bottom section
echo"
<tr>
";

$melee_pool_list = "";
$abilities_melee_pool = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '3' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting melee block abs.");
$absmeleepoolcnt = mysql_num_rows($abilities_melee_pool);
while($absmeleepool = mysql_fetch_assoc($abilities_melee_pool))
{
	$melee_pool_list = $melee_pool_list."$absmeleepool[ability]&nbsp;&nbsp;&nbsp;";
}

$chemix_pool_list = "";
$abilities_chemix_pool = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '10' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting chemix block abs.");
$abschmxpoolcnt = mysql_num_rows($abilities_chemix_pool);
while($abschmxpool = mysql_fetch_assoc($abilities_chemix_pool))
{
	$chemix_pool_list = $chemix_pool_list."$abschmxpool[ability]&nbsp;&nbsp;&nbsp;";
}

$spell_pool_list = "";
$abilities_spell_pool = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '4' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting spell block abs.");
$absspellpoolcnt = mysql_num_rows($abilities_spell_pool);
while($absspellpool = mysql_fetch_assoc($abilities_spell_pool))
{  
	$spell_pool_list = $spell_pool_list."$absspellpool[ability]&nbsp;&nbsp;&nbsp;";
}

include("modules/$module_name/includes/pcinfo.php");

//begin left panel
echo"
	<td valign = 'top' width = '49%' align = 'left' colspan='3'>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td valign = 'top' align = 'center' width='100%' colspan = '5'>
					<table border='0' cellpadding='0' cellspacing='0' width='100%'>
						<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
							<td valign = 'middle' align = 'left' width=32%' colspan='3'>
								<font class='heading2'> POOL BLOCKS</font>
							</td>
							<td width='2%'>
							&nbsp;
							</td>
							<td valign = 'middle' align = 'left' colspan='5'>
								<font class='heading1'>Hover for Skills by Pool. </font>
							</td>	
							<td width='2%'>
							&nbsp;
							</td>
							<form name = 'show_hide_pool_blocks' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
							<td valign = 'middle' align = 'right'>
							";
							
							if($ab_poolblock_expander == 1)
							{
								echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'><input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'><input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'><input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='0' name = 'ab_poolblock_expander'><input class='submit3' type='submit' value='Hide'>";
							}
								
							if($ab_poolblock_expander == 0)
							{
								echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'><input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'><input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'><input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='1' name = 'ab_poolblock_expander'><input class='submit3' type='submit' value='Show'>";
							}
							
							echo"
							</td>
							</form>
						</tr>
						<tr height='9'>
							<td colspan = '11'>
							
							</td>
						</tr>
						";
						
						if($ab_poolblock_expander == 1)
						{
							echo"
						<tr>
							<td valign = 'middle' width = '32%' align = 'center' colspan='3'>
								<font class='label' title='$chemix_pool_list'>
								Chemix $curpcabchemixblock ($chemixslots Slots)
								</font>
							</td>
							<td width = '2%'>
							&nbsp;
							</td>
							<td valign = 'middle' align = 'center' width='32%' colspan='3'>
								<font class='label' title='$melee_pool_list'>
								Melee $curpcabmeleeblock ($meleeslots Slots)
								</font>
							</td>
							<td width = '2%'>
							&nbsp;
							</td>";
							
							$block_build_cost = 5;
							
							echo"
							<td valign = 'middle' width = '32%' align = 'center' colspan='3'>
								<font class='label' title='$spell_pool_list'>
								Spell $curpcabspellblock ($spellslots Slots)
								</font>
							</td>
						</tr>
						<tr>
							<form name = 'add_chemix' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
							<td valign = 'middle' align = 'center' width='15%'>
							";
							
							if($block_build_cost <= $build_left)
							{
								echo"
								<input type='hidden' value='$curpcabchemix[creature_ability_id]' name='pcab'>
								<input type='hidden' value='$curpcabchemix[creature_ability_count]' name='pcabcnt'>
								<input type='hidden' value='1' name='pcabchg'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='106' name='pcabid'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
								<input class='submit3' type='submit' value='+' name='add_chemix'>
								<br>
								<font color = '#33F406' size='2'><b>-$block_build_cost</b></font>
								";
							}
							
							echo"
							</td>
							</form>
							<td width = '2%'>
							&nbsp;
							</td>
							<form name = 'del_chemix' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
							<td valign = 'middle' align = 'center' width='15%'>";
							
							if($curpcabchemixcnt >= 1)
							{
								if($curpcnfo[creature_status_id] == 4)
								{
									if($curusrslrprnk[slurp_rank_id] <= 4)
									{
										echo"
								<input type='hidden' value='0' name='pcabchg'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='106' name='pcabid'>
								<input type='hidden' value='$curpcabchemix[creature_ability_id]' name='pcab'>
								<input type='hidden' value='$curpcabchemix[creature_ability_count]' name='pcabcnt'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
								<input class='submit3' type='submit' value='-' name='del_chemix'>
								<br>
								<font color = '#33F406' size='2'><b>+$block_build_cost</b></font>
										";
									}
								}
								if($curpcnfo[creature_status_id] <= 3)
								{
																												echo"
								<input type='hidden' value='0' name='pcabchg'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='106' name='pcabid'>
								<input type='hidden' value='$curpcabchemix[creature_ability_id]' name='pcab'>
								<input type='hidden' value='$curpcabchemix[creature_ability_count]' name='pcabcnt'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
								<input class='submit3' type='submit' value='-' name='del_chemix'>
								<br>
								<font color = '#33F406' size='2'><b>+$block_build_cost</b></font>
									";										
								}
							}
							
							echo"
							</td>
							</form>
							<td width = '2%'>
							&nbsp;
							</td>
							<form name = 'add_melee' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
							<td valign = 'middle' align = 'center' width='15%'>
							";
							
							if($block_build_cost <= $build_left)
							{
								echo"
								<input type='hidden' value='$curpcabmelee[creature_ability_id]' name='pcab'>
								<input type='hidden' value='$curpcabmelee[creature_ability_count]' name='pcabcnt'>
								<input type='hidden' value='1' name='pcabchg'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='105' name='pcabid'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
								<input class='submit3' type='submit' value='+' name='add_melee'>
								<br>
								<font color = '#33F406' size='2'><b>-$block_build_cost</b></font>
								";
							}
							
							echo"
							</td>
							</form>
							<td width = '2%'>
							&nbsp;
							</td>
							<form name = 'del_melee' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
							<td valign = 'middle' align = 'center' width='15%'>";
							
							if($curpcabmeleecnt >= 1)
							{
								if($curpcnfo[creature_status_id] == 4)
								{
									if($curusrslrprnk[slurp_rank_id] <= 4)
									{
										echo"
								<input type='hidden' value='0' name='pcabchg'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='105' name='pcabid'>
								<input type='hidden' value='$curpcabmelee[creature_ability_id]' name='pcab'>
								<input type='hidden' value='$curpcabmelee[creature_ability_count]' name='pcabcnt'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
								<input class='submit3' type='submit' value='-' name='del_melee'>
								<br>
								<font color = '#33F406' size='2'><b>+$block_build_cost</b></font>
										";
									}
								}
								if($curpcnfo[creature_status_id] <= 3)
								{
									echo"
								<input type='hidden' value='0' name='pcabchg'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='105' name='pcabid'>
								<input type='hidden' value='$curpcabmelee[creature_ability_id]' name='pcab'>								
								<input type='hidden' value='$curpcabmelee[creature_ability_count]' name='pcabcnt'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
								<input class='submit3' type='submit' value='-' name='del_melee'>
								<br>
								<font color = '#33F406' size='2'><b>+$block_build_cost</b></font>
									";
								}
							}
							
							echo"
							</td>
							</form>
							<td width = '2%'>
							&nbsp;
							</td>
							<form name = 'add_spell' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
							<td valign = 'middle' align = 'center' width='15%'>
							";
							
							// pause for racial costing adjustments
							$check_pc_races_again = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype INNER JOIN ".$slrp_prefix."creature_creature_subtype ON ".$slrp_prefix."creature_creature_subtype.creature_subtype_id = ".$slrp_prefix."creature_subtype.creature_subtype_id WHERE ".$slrp_prefix."creature_creature_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc cr subtype info.");
							$chkpcrcsagn = mysql_fetch_assoc($check_pc_races_again);
							
							if($chkpcrcsagn[creature_subtype] == "Elves")
							{
								$block_build_cost--;
							}
							$spell_block_creation_flag = 0;
							if($chkpcrcsagn[creature_subtype] == "Elves")
							{
								$spell_block_creation_flag = 1; 
							}
							if($chkpcrcsagn[creature_subtype] == "Gnomes")
							{
								$spell_block_creation_flag = 1; 
							}
							if($chkpcrcsagn[creature_subtype] == "Dwarves, Common")
							{
								$spell_block_creation_flag = 1; 
							}
							if($chkpcrcsagn[creature_subtype] == "Dwarves, Deep")
							{
								$spell_block_creation_flag = 1; 
							}
							
							if($curpcnfo[creature_status_id] == 4)
							{
								if($block_build_cost <= $build_left)
								{
									echo"
									<input type='hidden' value='$curpcabspell[creature_ability_id]' name='pcab'>
									<input type='hidden' value='$curpcabspell[creature_ability_count]' name='pcabcnt'>
									<input type='hidden' value='1' name='pcabchg'>
									<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
									<input type='hidden' value='104' name='pcabid'>
									<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
									<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
									<input class='submit3' type='submit' value='+' name='add_spell'>
									<br>
									<font color = '#33F406' size='2'><b>-$block_build_cost</b></font>
									";
								}
								
								// echo"flag: $spell_block_creation_flag<br>";
							}
								
							if($curpcnfo[creature_status_id] >= 2)
							{
								if($curpcnfo[creature_status_id] <= 3)
								{
									if($spell_block_creation_flag == 1)
									{
										echo"
											";
										
										if($block_build_cost <= $build_left)
										{
											echo"
											<input type='hidden' value='$curpcabspell[creature_ability_id]' name='pcab'>
											<input type='hidden' value='$curpcabspell[creature_ability_count]' name='pcabcnt'>
											<input type='hidden' value='1' name='pcabchg'>
											<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
											<input type='hidden' value='104' name='pcabid'>
											<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
											<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
											<input class='submit3' type='submit' value='+' name='add_spell'>
											<br>
											<font color = '#33F406' size='2'><b>-$block_build_cost</b></font>
											";
										}
									}
								}
							}
							echo"	
							</td>
							</form>
							<td width = '2%'>
							&nbsp;
							</td>
							<form name = 'del_spell' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
							<td valign = 'middle' align = 'center' width='15%'>";
							
							if($curpcabspellcnt >= 1)
							{
								if($curpcnfo[creature_status_id] == 4)
								{
									if($curusrslrprnk[slurp_rank_id] <= 4)
									{
										echo"
								<input type='hidden' value='0' name='pcabchg'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='104' name='pcabid'>
								<input type='hidden' value='$curpcabspell[creature_ability_id]' name='pcab'>
								<input type='hidden' value='$curpcabspell[creature_ability_count]' name='pcabcnt'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
								<input class='submit3' type='submit' value='-' name='del_spell'>
								<br>
								<font color = '#33F406' size='2'><b>+$block_build_cost</b></font>
										";
									}
								}
								if($curpcnfo[creature_status_id] <= 3)
								{
																	echo"
								<input type='hidden' value='0' name='pcabchg'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='104' name='pcabid'>
								<input type='hidden' value='$curpcabspell[creature_ability_id]' name='pcab'>
								<input type='hidden' value='$curpcabspell[creature_ability_count]' name='pcabcnt'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$block_build_cost' name = 'final_build_cost'>
								<input class='submit3' type='submit' value='-' name='del_spell'>
								<br>
								<font color = '#33F406' size='2'><b>+$block_build_cost</b></font>
									";
								}
							}
							
							echo"
							</td>
							</form>
						</tr>
						<tr height='9'>
							<td colspan = '11'>
							
							</td>
						</tr>
							";
						}				
													
						echo"
					</table>
				</td>
			</tr>
";

// BEGIN KNOWN ABILITIES LISTING ************************//
//		ABILITY SET					creature_ability_count		creature_ability_level
//
//		2 	Constant					
//		3 	Melee Pool				Count			Block x4			1
//		4 	Spell Pool				Count			Block x4
//		5 	Combat Domain			Count			Count
//		6 	Stealth Domain		Count			Count
//		7 	Insight Domain		Count			Count
//		8 	Faith Domain			Count			Count
//		9 	Burn Domain				Count			Count
//		10 	Chemix Pool				Count			Block x4
//		11 	Craft							pcabcnt									-# means months left to know
//		12 	Racial						pcabcnt
//		13 	Generic Domain		Count			Count
//		14 	Advanced Arts			Count			Count
//		15 	Advantages				Count			Count
//		17 	Alchemy Domain		Count			Count
//		18	Racial Pool				-999			Default Add
//													-1				Chosen Pool
//												>=-100			Known
//
//*******************************************************//

// CHOSEN ABILITIES pcabcnt ******************************//
//		REASON													pcabcnt
//
//		Student of War Source Mark			-1100 to -1199
//		Free Domain every 5 levels			-1000 to -1099
//		Racial Known										-999
//		Studying but not Known Yet			-900 to -998
// 		Normal Purchase									0 to -899
//*******************************************************//


$get_choice_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id ='18' AND ".$slrp_prefix."creature_ability.creature_ability_count = '-999' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting choice ab list");
$curchcabcnt = mysql_num_rows($get_choice_abilities);

$get_chosen_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id ='18' AND ".$slrp_prefix."creature_ability.creature_ability_count = '-1' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting chosen ab list");
$curchsabcnt = mysql_num_rows($get_chosen_abilities);

$get_race_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id ='12' OR ".$slrp_prefix."ability.ability_set_id ='18' AND ".$slrp_prefix."creature_ability.creature_ability_count >= '-100' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting racial ab list");
$currcabcnt = mysql_num_rows($get_race_abilities);
if($currcabcnt >= 1)
{
	echo"
			<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
				<td valign = 'middle' align = 'left' width='32'%'>
					<font class='heading2'> RACIAL SKILLS</font>
				</td>
				<td width = '2%'>
				&nbsp;
				</td>
				";
	
				if($curchsabcnt < $verpcrc[creature_subtype_race_pool_limit])
				{
					echo"
				<form name = 'new_race_choice_ab' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
				<td valign = 'middle' width = '66%' align = 'left' colspan='3'>
					<font class='heading1'>
					<select class='engine' class='wow' name = 'pcabid'>
					<option value=''>Free Racial Ability</option>
					";
					
					while($curchcab = mysql_fetch_assoc($get_choice_abilities))
					{
						echo"<option value='$curchcab[ability_id]'>$curchcab[ability]</option>";
					}
					
					echo"
					</select>
					</font>
				</td>
			</tr>
			<tr>
				<td valign = 'middle' align = 'left' width=32%'>
				</td>
				<td width = '2%'>
				&nbsp;
				</td>
				<td align = 'right' valign = 'middle' width='66%' colspan='3'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$admin_expander' name = 'admin_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='-999' name = 'pcabcnt'>
					<input type='hidden' value='1' name = 'pcabchg'>
					<input type='hidden' value='0' name = 'final_build_cost'>
					<input type='submit' value='Choose Ability' name = 'new_race_choice_ab' class='submit2'>
				</td>
				</form>
			</tr>
					";
				}
				else
				{
					echo"
				<form name = 'show_hide_ab_racial' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
				<td valign = 'middle' align = 'right' width='66%' colspan='3'>
					";
					
					if($ab_racial_expander == 1)
					{
						echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'><input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'><input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'><input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='0' name = 'ab_racial_expander'><input class='submit3' type='submit' value='Hide'>";
					}
						
					if($ab_racial_expander == 0)
					{
						echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'><input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'><input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'><input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='1' name = 'ab_racial_expander'><input class='submit3' type='submit' value='Show'>";
					}
					
					echo"
				</td>
				</form>
			</tr>
					";
				}
				if($ab_racial_expander == 1)
				{
					echo"
			<tr>
				<td colspan = '5' width='100%' align='left' valign='top'>
					<table width='100%' cellspacing='0' cellpadding='0' border='0'>
					";
					
					while($currcab = mysql_fetch_assoc($get_race_abilities))
					{
						$ab_nfo_id = $currcab[ability_id];		
						$learned = 0;
						$dressed = 1;
						$ab_shop = 1;
						include("modules/$module_name/includes/fn_ab_nfo.php");
					}
					
					echo"
					</table>
				</td>
			</tr>
					";
				}
				
				echo"
			<tr height='9'>
				<td colspan = '11'>
				
				</td>
			</tr>
				";
}

//$get_studying_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_ability.creature_ability_level < '0' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting studying ab list");
//$curstdyabcnt = mysql_num_rows($get_studying_abilities);
//if($curstdyabcnt >= 1)
//{
//	echo"
//			<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
//				<td valign = 'middle' align = 'left' colspan='3'>
//					<font class='heading2'>CURRENTLY STUDYING</font>
//				</td>
//				<td width = '2%'>
//				&nbsp;
//				</td>
//				<form name = 'show_hide_ab_racial' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
//				<td valign = 'middle' align = 'right'>
//	";
//	
//	if($ab_studying_expander == 1)
//	{
//		echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'><input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'><input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'><input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='0' name = 'ab_studying_expander'><input class='submit3' type='submit' value='Hide'>";
//	}
//		
//	if($ab_studying_expander == 0)
//	{
//		echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'><input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'><input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'><input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='1' name = 'ab_studying_expander'><input class='submit3' type='submit' value='Show'>";
//	}
//	
//	echo"
//				</td>
//				</form>
//			</tr>
//	";
//}
//if($ab_studying_expander == 1)
//{
//	// get abilities being studied by the character
//	$studied_abilities_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_ability.creature_ability_level < '0' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting studying ab list");
//	$stdyablstcnt = mysql_num_rows($studied_abilities_list);
//	while($stdyablst = mysql_fetch_assoc($studied_abilities_list))
//	{
//	// echo"$curpcnfo[creature_id], $knwncraft[ability_id], abname: $knwncraft[ability]<br>";
//	// populate a temp table to use as the trimming list
//		$start_known_craft_list = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_learned_list(creature_ability_id,creature_id,ability_id,ability_build_cost_modified,ability_name,creature_ability_level) VALUES ('$stdyablst[creature_ability_id]','$curpcnfo[creature_id]','$stdyablst[ability_id]','$stdyablst[ability_build_cost]','$stdyablst[ability]','$stdyablst[creature_ability_level]')") or die("failed to start studying list.");
//	}
//	
//	echo"
//			<tr>
//				<td colspan = '5' width='100%' align='left' valign='top'>
//					<table width='100%' cellspacing='0' cellpadding='0' border='0'>
//	";
//	
//	while($curstdyab = mysql_fetch_assoc($get_studying_abilities))
//	{
//		$ab_nfo_id = $curstdyab[ability_id];		
//		$learned = 0;
//		$dressed = 1;
//		$ab_shop = 1;
//		include("modules/$module_name/includes/fn_ab_nfo.php");
//	}
//	
//	echo"
//					</table>
//				</td>
//			</tr>
//	";
//	
//	// delete the records in the temp table for that character and finish up
//	$clean_up_that_study = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_id = '$curpcnfo[creature_id]'");
//}

if($ab_learned_expander == 1)
{
	// get craft abilities already known by the character
	$learned_abilities_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_ability.creature_ability_level >= '0' AND ".$slrp_prefix."ability.ability_set_id != '12' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting learned ab list");
	$lrndablstcnt = mysql_num_rows($learned_abilities_list);
	while($lrndablst = mysql_fetch_assoc($learned_abilities_list))
	{
	// echo"$curpcnfo[creature_id], $knwncraft[ability_id], abname: $knwncraft[ability]<br>";
	// populate a temp table to use as the trimming list
		$start_known_abs_list = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_learned_list(creature_ability_id,creature_id,ability_id,ability_build_cost_modified,ability_name,creature_ability_level) VALUES ('$lrndablst[creature_ability_id]','$curpcnfo[creature_id]','$lrndablst[ability_id]','$lrndablst[ability_build_cost]','$lrndablst[ability]','$lrndablst[creature_ability_level]')") or die("failed to start learned list.");
	}
	
	// if equal but not greater than the first block only
	if($curpcabmeleeblock == 1)
	{
		$all_melee_pool_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '3' AND ".$slrp_prefix."ability.ability != 'Melee Pool Block' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting melee pool ab list");
		$allmelpoolabscnt = mysql_num_rows($all_melee_pool_abilities);
		while($allmelpoolabs = mysql_fetch_assoc($all_melee_pool_abilities))
		{
			// echo"$allmelpoolabs[ability_set_id] $allmelpoolabs[ability]<br>";
			$melee_pool_check = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$allmelpoolabs[ability_id]'") or die ("failed checking existing melee pool abs for insert.");
			$melplchkcnt = mysql_num_rows($melee_pool_check);
			if($melplchkcnt == 0)
			{
				// echo" NEW!<br>";
				$melee_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$allmelpoolabs[ability_id]' AND object_random_current = '1' AND object_focus_id = '2' AND object_slurp_id = '$slrpnfo[slurp_id]'")or die ("failed getting new object random for melee pool insert.");
				$meleeabrnd = mysql_fetch_assoc($melee_ability_random);
			
				$add_melee_pool_to_character = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count) VALUES ('$curpcnfo[creature_id]','$allmelpoolabs[ability_id]','$meleeabrnd[object_random_id]','0','1')") or die ("failed inserting melee pool abs.");
			}
			
			$melee_pool_learned_check = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$allmelpoolabs[ability_id]'") or die ("failed checking existing melee pool abs for insert.");
			$melpllrndchkcnt = mysql_num_rows($melee_pool_learned_check);
			if($melpllrndchkcnt == 0)
			{
				$melee_pool_check2 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$allmelpoolabs[ability_id]'") or die ("failed checking existing melee pool abs for insert 2.");
				$melplchk = mysql_fetch_assoc($melee_pool_check2);
				// echo" listed<br>";
				$add_melee_pool_abilities = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_learned_list(creature_ability_id,creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$melplchk[creature_ability_id]','$curpcnfo[creature_id]','$allmelpoolabs[ability_id]','$allmelpoolabs[ability_build_cost]','$allmelpoolabs[ability]')") or die("failed to add melee pool list.");
			}
		}
	}
	if($curpcabmeleeblock == 0)
	{
		$melee_pool_abs_to_clear = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability.ability_set_id = '3' AND ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting melee pool abs for delete.");
		while($melplabs2clr = mysql_fetch_assoc($melee_pool_abs_to_clear))
		{
			// echo"$melplabs2clr[creature_ability_id]<br>";
			$clear_melee_pool_abs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE ".$slrp_prefix."creature_ability.creature_ability_id = '$melplabs2clr[creature_ability_id]'") or die ("failed clearing melee pool abs.");
			$del_melee_pool_abilities = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_ability_id = '$melplabs2clr[creature_ability_id]'") or die("failed to del melee pool list.");
		}
	}
	// if equal but not greater than the first block only
	if($curpcabspellblock == 1)
	{
		$all_spell_pool_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '4' AND ".$slrp_prefix."ability.ability != 'Spell Pool Block' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting spell pool ab list");
		$allsplpoolabscnt = mysql_num_rows($all_spell_pool_abilities);
		while($allsplpoolabs = mysql_fetch_assoc($all_spell_pool_abilities))
		{	
			// echo"$allsplpoolabs[ability_set_id] $allsplpoolabs[ability]<br>";
			$spell_pool_check = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$allsplpoolabs[ability_id]'") or die ("failed checking existing spell pool abs for insert.");
			$splplchkcnt = mysql_num_rows($spell_pool_check);
			
			if($splplchkcnt == 0)
			{
				// echo" NEW!<br>";
				$spell_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$allsplpoolabs[ability_id]' AND object_random_current = '1' AND object_focus_id = '2' AND object_slurp_id = '$slrpnfo[slurp_id]'")or die ("failed getting new object random for spell pool insert.");
				$splabrnd = mysql_fetch_assoc($spell_ability_random);
			
				$add_spell_pool_to_character = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count) VALUES ('$curpcnfo[creature_id]','$allsplpoolabs[ability_id]','$splabrnd[object_random_id]','0','1')") or die ("failed inserting spell pool abs.");
			}
			$spell_pool_learned_check = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$allsplpoolabs[ability_id]'") or die ("failed checking existing spell pool abs for insert.");
			$splpllrndchkcnt = mysql_num_rows($spell_pool_learned_check);
			
			if($splpllrndchkcnt == 0)
			{
				$spell_pool_check2 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$allsplpoolabs[ability_id]'") or die ("failed checking existing spell pool abs for insert 2.");
				$splplchk = mysql_fetch_assoc($spell_pool_check2);
				// echo" listed<br>";
				$add_spell_pool_abilities = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_learned_list(creature_ability_id,creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$splplchk[creature_ability_id]','$curpcnfo[creature_id]','$allsplpoolabs[ability_id]','$allsplpoolabs[ability_build_cost]','$allsplpoolabs[ability]')") or die("failed to add spell pool list.");
			}
			
		}
	}
	if($curpcabspellblock == 0)
	{
		$spell_pool_abs_to_clear = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability.ability_set_id = '4' AND ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting melee pool abs for delete.");
		while($splplabs2clr = mysql_fetch_assoc($spell_pool_abs_to_clear))
		{
			// echo"$splplabs2clr[creature_ability_id]<br>";
			$clear_spell_pool_abs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE ".$slrp_prefix."creature_ability.creature_ability_id = '$splplabs2clr[creature_ability_id]'") or die ("failed clearing spell pool abs.");
			$del_spell_pool_abilities = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_ability_id = '$splplabs2clr[creature_ability_id]'") or die("failed to del spell pool list.");
		}
	}
	// if equal but not greater than the first block only
	if($curpcabchemixblock == 1)
	{
		$all_chemix_pool_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '10' AND ".$slrp_prefix."ability.ability != 'Chemix Pool Block' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting chemix pool ab list");
		$allchmxpoolabscnt = mysql_num_rows($all_chemix_pool_abilities);
		while($allchmxpoolabs = mysql_fetch_assoc($all_chemix_pool_abilities))
		{
			// echo"$allchmxpoolabs[ability_set_id] $allchmxpoolabs[ability]<br>";
			$chemix_pool_check = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$allchmxpoolabs[ability_id]'") or die ("failed checking existing chemix pool abs for insert.");
			$chmxplchkcnt = mysql_num_rows($chemix_pool_check);
			if($chmxplchkcnt == 0)
			{
				// echo" NEW!<br>";
				$chemix_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$allchmxpoolabs[ability_id]' AND object_random_current = '1' AND object_focus_id = '2' AND object_slurp_id = '$slrpnfo[slurp_id]'")or die ("failed getting new object random for chemix pool insert.");
				$chmxabrnd = mysql_fetch_assoc($chemix_ability_random);
			
				$add_chemix_pool_to_character = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count) VALUES ('$curpcnfo[creature_id]','$allchmxpoolabs[ability_id]','$chmxabrnd[object_random_id]','0','1')") or die ("failed inserting chemix pool abs.");
			}
			
			$chemix_pool_learned_check = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$allchmxpoolabs[ability_id]'") or die ("failed checking existing chemix pool abs for insert.");
			$chmxpllrndchkcnt = mysql_num_rows($chemix_pool_learned_check);
			if($chmxpllrndchkcnt == 0)
			{
				$chemix_pool_check2 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$allchmxpoolabs[ability_id]'") or die ("failed checking existing chemix pool abs for insert 2.");
				$chmxplchk = mysql_fetch_assoc($chemix_pool_check2);
				// echo" listed<br>";
				$add_chemix_pool_abilities = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_learned_list(creature_ability_id,creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$chmxplchk[creature_ability_id]','$curpcnfo[creature_id]','$allchmxpoolabs[ability_id]','$allchmxpoolabs[ability_build_cost]','$allchmxpoolabs[ability]')") or die("failed to add chemix pool list.");
			}
		}
	}
	if($curpcabchemixblock == 0)
	{
		$chemix_pool_abs_to_clear = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability.ability_set_id = '10' AND ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting chemix pool abs for delete.");
		while($chmxplabs2clr = mysql_fetch_assoc($chemix_pool_abs_to_clear))
		{
			// echo"$chmxplabs2clr[creature_ability_id]<br>";
			$clear_chemix_pool_abs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ability WHERE ".$slrp_prefix."creature_ability.creature_ability_id = '$chmxplabs2clr[creature_ability_id]'") or die ("failed clearing chemix pool abs.");
			$del_chemix_pool_abilities = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_ability_id = '$chmxplabs2clr[creature_ability_id]'") or die("failed to del chemix pool list.");		
		}
	}
	
	$get_learned_pool_abilities3 = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability LIKE '%Pool Block%'") or die ("failed getting pool abilities");
	$lrndpcpoolabscnt3 = mysql_num_rows($get_learned_pool_abilities3);
	while($lrndpcpoolabs = mysql_fetch_assoc($get_learned_pool_abilities3))
	{
		// these are bought at the top of the list instead of on it, so they get removed
		$trim_learned_pool_blocks = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_learned_list WHERE ability_id = '$lrndpcpoolabs[ability_id]'");
		// echo"Pool Block ab# $curpcpoolab3[ability_id]<br>";
	}
	
	$get_craft_item_types = mysql_query("SELECT * FROM ".$slrp_prefix."item_type WHERE item_type_id > '1' ORDER BY item_type") or die ("failed getting item types to match craft names.");
	while($crftitemtypes = mysql_fetch_assoc($get_craft_item_types))
	{
		// create a sting of the first six characters to match to the abilities
		$first_six = substr($crftitemtypes[item_type],0, 6);
		// echo "$first_six: ";
		// get craft the highest tier craft by item type
		$highest_craft = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_ability.creature_ability_level >= '0' AND ".$slrp_prefix."ability.ability LIKE '%$first_six%' AND ability_set_id = '11'") or die ("failed getting hi craft list");
		$hicraftcnt = mysql_num_rows($highest_craft);
		$highest_craft_tier = 0;
		if($hicraftcnt >= 1)
		{
			$highest_craft_tier = 1;
			while($hicraft = mysql_fetch_assoc($highest_craft))
			{
				if($highest_craft_tier < $hicraft[ability_tier])
				{
					// echo"$highest_craft_tier < $hicraft[ability_tier]<br>";
					$highest_craft_tier = $hicraft[ability_tier];
				}
				
				// echo"highest tier $highest_craft_tier<br>";
			}
			
			$get_lower_crafts = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability LIKE '%$first_six%' AND ability_set_id = '11' AND ability_tier < '$highest_craft_tier'") or die ("failed getting lo craft list");
			$locraftcnt = mysql_num_rows($get_lower_crafts);
			while($locraft = mysql_fetch_assoc($get_lower_crafts))
			{
				// echo"Removing $locraft[ability] as a lower craft.<br>";
				$trim_lower_crafts = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_ability_id = '$locraft[creature_ability_id]'");
			}
		}
	}
	// get racial pool choice abilities not yet known by the PC
	$racial_choice_abilities_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id = '18' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting racial pool choice ab list");
	$rclchcablstcnt = mysql_num_rows($racial_choice_abilities_list);
	while($rclchcablst = mysql_fetch_assoc($racial_choice_abilities_list))
	{
	// echo"$curpcnfo[creature_id], $rclchcablst[ability_id], abname: $rclchcablst[ability]<br>";
	// populate a temp table to use as the trimming list
		$delete_choice_abs_list = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_ability_id = '$rclchcablst[creature_ability_id]'") or die("failed to clear pc choices from learned list.");
	}
}
// get the temp table results and print the matching list of abilities that are left
$final_learned_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_id = '$curpcnfo[creature_id]' ORDER BY ability_name ASC") or die ("failed to get final learned list IDs.");
$finlrndlstcnt = mysql_num_rows($final_learned_list);

echo"
		<tr height='9'>
			<td width = '100%' align = 'left' valign = 'middle' colspan = '11'>
			<td>
		</tr>
		<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
			<td valign = 'middle' align = 'left' width=32%'>
				<font class='heading2'>LEARNED SKILLS</font>
			</td>
			<td width = '2%'>
			&nbsp;
			</td>
";

$sorry_this_seat_is_taken = 0;

// check for Student of War; Aptitudes are changed out later
$check_sow_ability = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '21'") or die ("failed checking SoW");
$chksowabcnt = mysql_num_rows($check_sow_ability);
if($chksowabcnt == 1)
{
	// check to see if they have chosen a free Source Mark yet
	$get_sow_choice_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability LIKE '%Source Mark%' AND ".$slrp_prefix."creature_ability.creature_ability_level = '1' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting SoW choice ab list");
	$cursowchcabcnt = mysql_num_rows($get_sow_choice_abilities);
	if($cursowchcabcnt == 0)
	{
		$sorry_this_seat_is_taken = 1;
		
		echo"<form name = 'new_sow_source_mark' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
			<td valign = 'middle' width = '66%' align = 'left' colspan = '3'>
				<font class='heading2'>
			";
	
		echo"<select class='engine' class='wow' name = 'pcabid'>";
		echo"<option value=''>Choose Source Mark</option>";
		$get_sow_source_mark_abilities =  mysql_query("SELECT ability_id FROM ".$slrp_prefix."ability WHERE ability LIKE '%Source Mark Flare%' AND ability NOT LIKE '%[Racial%' AND ability_id NOT IN (SELECT ability_id FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]')") or die("failed getting SoW free source mark sets.");
		while($gtsowsmabs = mysql_fetch_assoc($get_sow_source_mark_abilities))
		{	
			// get the list of unclaimed source mark abilities
			$get_sm_unclaimed_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$gtsowsmabs[ability_id]'") or die ("failed getting free SoW sm ab list");
			$gtsmunclabs = mysql_fetch_assoc($get_sm_unclaimed_abilities);		
			echo"<option value='$gtsmunclabs[ability_id]'>$gtsmunclabs[ability]</option>";
		}
		
		echo"</select>
				</font>
			</td>
		</tr>
		";
	}
}
if($sorry_this_seat_is_taken == 0)
{
	// free domain abilities every 5 levels
	$get_owned_level_based_domain_abilities1 = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_ability.creature_ability_level >= '5' ORDER BY ".$slrp_prefix."ability.ability_tier, ".$slrp_prefix."ability.ability") or die ("failed getting pwned free domain ab list");
	$curownlvlbsddmnabs1cnt = mysql_num_rows($get_owned_level_based_domain_abilities1);
	$domain_tier = $character_level/5;
	$free_ab_count = 	$domain_tier-$curownlvlbsddmnabs1cnt;
	
	$next_free_ab_level = (ceil(($character_level+1)/5))*5;
	$this_free_ab_level = (floor(($character_level)/5))*5;
	$pcabcnt = -(1000+$this_free_ab_level);
	// echo"$free_ab_count = ($character_level/5)-$curownlvlbsddmnabs1cnt<br> THIS FREEBIE: $this_free_ab_level (cnt: $pcabcnt)<br> NEXT FREEBIE: $next_free_ab_level<br>";
	
	if($free_ab_count >= 1)
	{
		echo"<form name = 'new_level_domain_ab' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
			<td valign = 'middle' width = '66%' align = 'left' colspan = '3'>
				<font class='heading2'>
			";
	
		echo"<select class='engine' class='wow' name = 'pcabid'>";
		echo"<option value=''>Free Domain Ability</option>";
		$get_domain_abilities =  mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set LIKE '%Domain%' ORDER BY ability_set") or die("failed getting domain ab sets.");
		while($gtdmnabs = mysql_fetch_assoc($get_domain_abilities))
		{	
			// get the list of domain abilities
			$get_level_based_domain_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_tier <= '$domain_tier' AND ".$slrp_prefix."ability.ability_set_id = '$gtdmnabs[ability_set_id]' ORDER BY ".$slrp_prefix."ability.ability_tier, ".$slrp_prefix."ability.ability") or die ("failed getting free domain ab list");
			$curlvlbsddmnabscnt = mysql_num_rows($get_level_based_domain_abilities);		
			
			echo"<optgroup label = '$gtdmnabs[ability_set]'>";
			while($curlvlbsddmnabs = mysql_fetch_assoc($get_level_based_domain_abilities))
			{
				// make sure they don't get the option to obtain again an ability limited to one purchase
				$get_owned_level_based_domain_abilities2 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."ability.ability_id = ".$slrp_prefix."creature_ability.ability_id WHERE ".$slrp_prefix."ability.ability_count_max > '$curownlvlbsddmnabs1cnt' AND ".$slrp_prefix."creature_ability.ability_id = '$curlvlbsddmnabs[ability_id]' AND ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting pwned free domain ab list 2");
				$curownlvlbsddmnabs2cnt = mysql_num_rows($get_owned_level_based_domain_abilities2);
				if($curownlvlbsddmnabs2cnt == 0)
				{
					echo"<option value='$curlvlbsddmnabs[ability_id]'>Tier $curlvlbsddmnabs[ability_tier]: $curlvlbsddmnabs[ability]</option>";
				}
			}
			echo"</optgroup>";
		}
		
		echo"</select>
				</font>
			</td>
		</tr>
		";
	}
	else
	{
		echo"
			<form name = 'show_hide_ab_learned' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
			<td valign = 'middle' align = 'left' width='32%'>
				<font class = 'heading1'>Next Free: <b>LVL $next_free_ab_level</b></font>
			</td>
			<td width='2%'>
			&nbsp;
			</td>
			<td valign = 'middle' align = 'right' width='32%'>
		";
		
		if($ab_learned_expander == 1)
		{
			echo"<input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'><input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'><input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'><input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='0' name = 'ab_learned_expander'><input class='submit3' type='submit' value='Hide'>";
		}
			
		if($ab_learned_expander == 0)
		{
			echo"<input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'><input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'><input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'><input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='1' name = 'ab_learned_expander'><input class='submit3' type='submit' value='Show'>";
		}
		
		echo"
				</td>
				</form>
			</tr>
		";
	}
	if($free_ab_count >= 1)
	{
		echo"
			<tr>
				<td valign = 'middle' align = 'left' width=32%'>
				</td>
				<td width = '2%'>
				&nbsp;
				</td>
				<td align = 'right' valign = 'middle' width='66%' colspan='3'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'>
					<input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'>
					<input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'>
					<input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'>
					<input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$admin_expander' name = 'admin_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='$this_free_ab_level' name = 'this_free_ab_level'>
					<input type='hidden' value='$pcabcnt' name = 'pcabcnt'>
					<input type='hidden' value='1' name = 'pcabchg'>
					<input type='hidden' value='0' name = 'final_build_cost'>
					<input type='submit' value='Choose Ability' name = 'new_level_domain_ab' class='submit2'>
				</td>
				</form>
			</tr>
		";
	}
}

if($sorry_this_seat_is_taken == 1)
{
	echo"
		<tr>
			<td valign = 'middle' align = 'left' width=32%'>
			</td>
			<td width = '2%'>
			&nbsp;
			</td>
			<td align = 'right' valign = 'middle' width='66%' colspan='3'>
				<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
				<input type='hidden' value='$expander_abbr' name='current_expander'>
				<input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'>
				<input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'>
				<input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'>
				<input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'>
				<input type='hidden' value='$ab_unknown_expander' name = 'ab_unknown_expander'>
				<input type='hidden' value='$compab_expander' name = 'compab_expander'>
				<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
				<input type='hidden' value='$admin_expander' name = 'admin_expander'>
				<input type='hidden' value='$compab_expander' name = 'compab_expander'>
				<input type='hidden' value='1' name = 'this_sow_ab_level'>
				<input type='hidden' value='-1100' name = 'pcabcnt'>
				<input type='hidden' value='1' name = 'pcabchg'>
				<input type='hidden' value='0' name = 'final_build_cost'>
				<input type='submit' value='Choose' name = 'new_sow_source_mark' class='submit2'>
			</td>
			</form>
		</tr>
	";
}

if($ab_learned_expander == 1)
{
	echo"
		<tr>
			<td colspan = '5' width='100%'>
				<table width='100%' cellspacing='0' cellpadding='0' border='0'>
	";

	while($finlrndlst = mysql_fetch_assoc($final_learned_list))
	{
		$final_known_list = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$finlrndlst[ability_id]'") or die ("failed to get final learned list");
		$knownlist = mysql_fetch_assoc($final_known_list);
	
		$ab_nfo_id = $finlrndlst[creature_ability_id];
		$learned = 1;
		$dressed = 0;
		if($finlrndlst[creature_ability_level] >= 1)
		{
			$ab_shop = 0;
		}
		if($finlrndlst[creature_ability_level] == 0)
		{
			$ab_shop = 1;
		}
		// echo"level: $finlrndlst[creature_ability_level]<br>";
		include("modules/$module_name/includes/fn_ab_nfo.php");
	}
	
	echo"
				</table>
			</td>
		</tr>
	";
}

$clean_up_that_knowledge = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_learned_list WHERE creature_id = '$curpcnfo[creature_id]'");

echo"
	</table>
</td>
";
// end left panel

// this is the 2% divider between right and left in the lower panel
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

// begin right panel 
echo"
	<td valign = 'top' width = '49%' align = 'left'>
		<table border='0' cellpadding='0' cellspacing='0' width = '100%'>
			<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
				<td valign = 'middle' width = '42%' align = 'left' colspan='3'>
						<font class='heading2'>UNKNOWN SKILLS</font>
				</td>
				<td width = '2%'>
				&nbsp;
				</td>
				<form name = 'show_hide_ab_unknown' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
				<td valign = 'middle' align = 'right' width='66%' colspan='5'>
					";
					
					if($ab_unknown_expander == 1)
					{
						echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'><input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'><input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'><input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='0' name = 'ab_unknown_expander'><input class='submit3' type='submit' value='Hide'>";
					}
						
					if($ab_unknown_expander == 0)
					{
						echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$ab_poolblock_expander' name = 'ab_poolblock_expander'><input type='hidden' value='$ab_learned_expander' name = 'ab_learned_expander'><input type='hidden' value='$ab_studying_expander' name = 'ab_studying_expander'><input type='hidden' value='$ab_racial_expander' name = 'ab_racial_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='1' name = 'ab_unknown_expander'><input class='submit3' type='submit' value='Show'>";
					}
					
					echo"
				</td>
				</form>
			</tr>
";

if($ab_unknown_expander == 1)
{
	if($compab_expander == 1)
	{
		echo"
			<tr>
				<td valign = 'top' width = '20%' align = 'left'>
					<font class='heading7'>Tagline</font></font>
					<br>					
					<font class='heading6'>Ability Set</font>
					<br>					
					<font class='heading7'>Build Cost</font>
					
					
				</td>
				<td width = '2%'>
				&nbsp;
				</td>
				<td valign = 'top' align = 'left' width = '20%'>
					<font class='heading6'>
					Usage
					</font>
					<br>
					<font class='heading7'>
					Sigils, Icons, etc.
					</font>
				</td>
				<td width = '2%'>
				&nbsp;
				</td>
				<td valign = 'top' align = 'left' width = '44%'>
					<font class='heading7'>
					Verbal
					</font>
					<br>
					<font class='heading6'>
					Description
					</font>
				</td>
				<td width = '2%'>
				&nbsp;
				</td>
				<td valign = 'top' align = 'left' width = '10%'>
				<font class='heading3'>
				Buy/Sell
				</font<
				</td>
			</tr>
		";
	}
	
	// create shopping list (temp table) of all items,
	// then remove the ones the character doesn't qualify for
	
	$abnames = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1' AND ability_status_id = '4' AND ability_min_rank >= '$curusrslrprnk[slurp_rank_id]' AND ability_set_id != '15' ORDER BY ability") or die ("failed getting abilities for temp shoppong table creation");
	while($abnms = mysql_fetch_assoc($abnames))
	{	
	// echo"$curpcnfo[creature_id], $abnmefftyps[ability_id], $abnmefftyps[effect_type_id], $abnmefftyps[effect_type_tier], abname: $abnms[ability]<br>";
	// populate a temp table to use as the shopping list
		$start_shopping_list = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_shopping_list(creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$curpcnfo[creature_id]','$abnms[ability_id]','$abnms[ability_build_cost]','$abnms[ability]')") or die("failed to start shopping list.");
	}
	
	// Elves, Gnomes, Common Dwarves, and Deep Dwarves cannot start with Student of War at creation
	// they all also get Enchanting Apprentice at creation, where no one else does.
	if($curpcnfo[creature_status_id] <= 3)
	{
		$clear_enchanting_apprentice = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '36'") or die ("failed deleting Enchanting Apprentice for all.");
			
		if($verpcrc[creature_subtype] == "Dwarves, Common")
		{
			$illandar_cannot_study_war = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '21'") or die ("failed deleting Dwarf Student of War.");
			$illandar_are_enchanting = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_shopping_list(creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$curpcnfo[creature_id]','36','5','Enchanting Apprentice')") or die("failed to add Enchanting Apprentice.");
		}
		if($verpcrc[creature_subtype] == "Dwarves, Deep")
		{
			$illandar_cannot_study_war = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '21'") or die ("failed deleting Deep Dwarf Student of War.");
			$illandar_are_enchanting = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_shopping_list(creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$curpcnfo[creature_id]','36','5','Enchanting Apprentice')") or die("failed to add Enchanting Apprentice.");
		}
		if($verpcrc[creature_subtype] == "Elves")
		{
			$illandar_cannot_study_war = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '21'") or die ("failed deleting Elves Student of War.");
				$illandar_are_enchanting = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_shopping_list(creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$curpcnfo[creature_id]','36','5','Enchanting Apprentice')") or die("failed to add Enchanting Apprentice.");
		}
		if($verpcrc[creature_subtype] == "Gnomes")
		{
			$illandar_cannot_study_war = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '21'") or die ("failed deleting Gnome Student of War.");
			$illandar_are_enchanting = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_shopping_list(creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$curpcnfo[creature_id]','36','5','Enchanting Apprentice')") or die("failed to add Enchanting Apprentice.");
		}
		
		if($build_left <= 5)
		{
			$get_advanatages_set = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '15'") or die ("failed getting advantages.");
			while($curadvantageset = mysql_fetch_assoc($get_advanatages_set))
			{
				$cheap_advantages = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_shopping_list(creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$curpcnfo[creature_id]','$curadvantageset[ability_id]','$curadvantageset[ability_build_cost]','$curadvantageset[ability]')") or die("failed to add advantages.");
			}
		}							

		// non Goblins get only Two Crafts.
		if($verpcrc[creature_subtype] != "Goblins")
		{
			$get_craft_ability_sets = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."ability.ability_id = ".$slrp_prefix."creature_ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id = '11'  AND ".$slrp_prefix."ability.ability LIKE '%Apprentice%'") or die ("failed getting craft ability sets.");
			$curcrftabsetscnt = mysql_num_rows($get_craft_ability_sets);
			if($curcrftabsetscnt >= 2)
			{
				$get_craft_ability_to_remove = mysql_query("SELECT ".$slrp_prefix."ability.ability_id FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '11' AND ".$slrp_prefix."ability.ability_id NOT IN(SELECT ".$slrp_prefix."creature_ability.ability_id FROM ".$slrp_prefix."creature_ability WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]') AND ".$slrp_prefix."ability.ability LIKE '%Apprentice%'") or die ("failed getting craft ability sets.");
				while($curcrftabtormv = mysql_fetch_assoc($get_craft_ability_to_remove))
				{
					$non_goblin_craft_limiter = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$curcrftabtormv[ability_id]'") or die ("failed deleting excess crafts.");
				}	
			}	
		}
		
		// no one gets tier II crafts to start.		
		$get_craft_ability_to_remove = mysql_query("SELECT ".$slrp_prefix."ability.ability_id FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '11' AND ".$slrp_prefix."ability.ability_tier > '1'") or die ("failed getting advanced craft ability sets.");
		while($curcrftabtormv = mysql_fetch_assoc($get_craft_ability_to_remove))
		{
			$craft_limiter = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$curcrftabtormv[ability_id]'") or die ("failed deleting excess crafts.");
		}							
	}
	
	// active non-Goblins are limited to two crafts being studied at once.
	if($curpcnfo[creature_status_id] == 4)
	{
		// non Goblins can study only Two Crafts.
		if($verpcrc[creature_subtype] != "Goblins")
		{
			$get_craft_ability_studies = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND creature_ability_level < '0'") or die ("failed getting craft ability study sets.");
			$curcrftabstdscnt = mysql_num_rows($get_craft_ability_studies);
			if($curcrftabstdscnt >= 2)
			{
				$get_ability_study_to_remove = mysql_query("SELECT ".$slrp_prefix."ability.ability_id FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_set_id = '11'") or die ("failed getting craft ability sets for study delete.");
				while($curcrftabstdytormv = mysql_fetch_assoc($get_ability_study_to_remove))
				{
					$non_goblin_craft_study_limiter = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$curcrftabstdytormv[ability_id]'") or die ("failed deleting excess crafts for non goblin study.");
				}
			}
		}
	}
	
	$get_pc_pool_abilities3 = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability LIKE '%Pool Block%'") or die ("failed getting pool abilities");
	$curpcpoolabcnt3 = mysql_num_rows($get_pc_pool_abilities3);
	while($curpcpoolab3 = mysql_fetch_assoc($get_pc_pool_abilities3))
	{
		// these are bought at the top of the list instead of on it, so they get removed
		$trim_pool_blocks = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$curpcpoolab3[ability_id]'");
		// echo"Pool Block ab# $curpcpoolab3[ability_id]<br>";
	}
	
	$get_xp_min_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_xp_min > '$build_total'") or die ("failed getting xp min abilities.");
	$curxpminabscnt3 = mysql_num_rows($get_xp_min_abilities);
	while($curxpminabsc = mysql_fetch_assoc($get_xp_min_abilities))
	{
		// these have a higher level or XP requirement than the PC possesses just now
		$trim_xp_min_abs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$curxpminabsc[ability_id]'");
		// echo"Min XP: $curxpminabsc[ability_xp_min] ab# $curxpminabsc[ability_id]<br>";
	}
	
	$creature_non_dom_ab_cost_total = 0;
	$get_non_domain_ability_sets = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_set ON ".$slrp_prefix."ability.ability_set_id = ".$slrp_prefix."ability_set.ability_set_id WHERE ".$slrp_prefix."ability_set.ability_set NOT LIKE '%Racial%' AND ".$slrp_prefix."ability_set.ability_set NOT LIKE '%Domain%' AND ".$slrp_prefix."ability_set.ability_set NOT LIKE '%Advanced%' AND ".$slrp_prefix."ability_set.ability_set_id != '1'") or die ("failed getting non domain ability sets.");
	while($curnondmnabsets = mysql_fetch_assoc($get_non_domain_ability_sets))
	{
		// echo"Set # $curnondmnabsets[ability_set_id]<br>";
		$creature_non_dom_ab_cost = 0;
		$get_non_domain_abilities_sum = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_ability.ability_id = '$curnondmnabsets[ability_id]'") or die ("failed getting limited count abilities");
		while($curnondmnabsum = mysql_fetch_assoc($get_non_domain_abilities_sum))
		{
			$creature_non_dom_ab_cost = $creature_non_dom_ab_cost+($curnondmnabsum[ability_build_cost]*$curnondmnabsum[creature_ability_count]);
			// echo"cost:$curnondmnabsum[ability_build_cost], XP sub: $creature_non_dom_ab_cost<br>";
		}
		$creature_non_dom_ab_cost_total = $creature_non_dom_ab_cost_total+$creature_non_dom_ab_cost;
	}
	
	// echo"PC Non Domain, Non Adv, Non Racial XP spent: $creature_non_dom_ab_cost_total<br>";
	
	$get_special_xp_min_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_special_xp_min > '$creature_non_dom_ab_cost_total'") or die ("failed getting special xp min abilities");
	$curspclxpminabcnt = mysql_num_rows($get_special_xp_min_abilities);
	while($curspclxpminab = mysql_fetch_assoc($get_special_xp_min_abilities))
	{
		// these have a higher alternate XP requirement than the PC has just now
		// echo"$curspclxpminab[ability] deleted for alt xp min.<br>";
		$trim_sp_xp_min_abs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$curspclxpminab[ability_id]'");
	}
	
	$get_pc_low_abilities3 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."ability.ability_id = ".$slrp_prefix."creature_ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_count_max = '1'") or die ("failed getting limited count abilities");
	$curpcloabcnt3 = mysql_num_rows($get_pc_low_abilities3);
	while($curpcloab3 = mysql_fetch_assoc($get_pc_low_abilities3))
	{
		// the character already has these and cannot buy more, so they get removed
		$trim_already_known_low = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$curpcloab3[ability_id]'");
		// echo"already has lo ab# $curpcloab3[ability_id]<br>";
	}
	
	$get_pc_high_abilities3 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."ability.ability_id = ".$slrp_prefix."creature_ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_count_max > '1' AND ".$slrp_prefix."creature_ability.creature_ability_level = '0'") or die ("failed getting multiple count abilities");
	$curpchiabcnt3 = mysql_num_rows($get_pc_high_abilities3);
	while($curpchiab3 = mysql_fetch_assoc($get_pc_high_abilities3))
	{
		// the character already has these and will use +/- buttons on the owned ability listing, so they get removed
		$trim_already_known_high = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$curpchiab3[ability_id]'");
		//  echo"already has hi ab# $curpchiab3[ability_id]<br>";
	}
	
	$advanced_art_display = 0;
	$gtpcadvncdartssbttl = 0;
	$get_all_advanced_arts_set =  mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_set_id = '14'") or die("failed getting adv arts set for domain dependency.");
	while($gtalladvartsset = mysql_fetch_assoc($get_all_advanced_arts_set))
	{	
		$get_pc_domains_set =  mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set LIKE '%Domain%'") or die("failed getting adv arts domain dependency sets.");
		while($gtpcdmnsset = mysql_fetch_assoc($get_pc_domains_set))
		{
			$get_pc_advanced_arts_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id = '$gtpcdmnsset[ability_set_id]'") or die("failed getting pc adv arts domain count.");
			$gtpcadvncdartsabscnt = mysql_num_rows($get_pc_advanced_arts_abilities);
			while($gtpcadvncdartsabs = mysql_fetch_assoc($get_pc_advanced_arts_abilities))
			{
				$gtpcadvncdartssbttl = $gtpcadvncdartssbttl+($gtpcadvncdartsabs[creature_ability_count]*$gtpcadvncdartsabs[ability_build_cost]);
				if($gtpcdmnsset[ability_set] == "Burn Domain")
				{
					if($gtalladvartsset[ability_set_min_1] > $gtpcadvncdartssbttl)
					{
						$advanced_art_display++;
						// echo"Burn Min: $gtalladvartsset[ability_set_min_1]";
					}
				}
				if($gtpcdmnsset[ability_set] == "Combat Domain")
				{
					if($gtalladvartsset[ability_set_min_2] > $gtpcadvncdartssbttl)
					{
						$advanced_art_display++;
						// echo"Combat Min: $gtalladvartsset[ability_set_min_2]";
					}
				}
				if($gtpcdmnsset[ability_set] == "Faith Domain")
				{
					if($gtalladvartsset[ability_set_min_3] > $gtpcadvncdartssbttl)
					{
						$advanced_art_display++;
						// echo"Faith Min: $gtalladvartsset[ability_set_min_3]";
					}
				}
				if($gtpcdmnsset[ability_set] == "Insight Domain")
				{
					if($gtalladvartsset[ability_set_min_4] > $gtpcadvncdartssbttl)
					{
						$advanced_art_display++;
						// echo"Insight Min: $gtalladvartsset[ability_set_min_4]";
					}
				}
				if($gtpcdmnsset[ability_set] == "Stealth Domain")
				{
					if($gtalladvartsset[ability_set_min_5] > $gtpcadvncdartssbttl)
					{
						$advanced_art_display++;
						// echo"Stealth Min: $gtalladvartsset[ability_set_min_5]";
					}
				}
			}
		}
		
		if($advanced_art_display >= 1)
		{
			// echo"$gtalladvartsset[ability] trimmed for insufficient domains.<br>";
			$trim_advanced_arts = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$gtalladvartsset[ability_id]'");
			$advanced_art_display = 0;
		}
	}
	
	// the character does not have the prerequisite ability to see this as a purchase option
	$all_dependent_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id NOT IN (SELECT ability_id FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND creature_ability_level >= '0') ORDER BY ability_id");
	while($alldepabs = mysql_fetch_assoc($all_dependent_abilities))
	{
		// echo"alldep $alldepabs[ability_id]: $alldepabs[ability]<br>";
		// get the 'must know ability' modifier
		$requirement_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$alldepabs[ability_id]' AND ".$slrp_prefix."ability_modifier_subfocus.focus_id = '2' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id = '16'") or die ("failed verifying required modifier.");
		$rqabnfo = mysql_fetch_assoc($requirement_ability_info);
		$rqabnfocnt = mysql_num_rows($requirement_ability_info);
		
		// echo"modifier: $rqabnfo[ability_modifier_short].<br>";
		// then get any abilities using it; ergo, dependent on knowing it
		$get_required_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '$rqabnfo[ability_modifier_id]'");
		while($gtrqabs = mysql_fetch_assoc($get_required_abilities))
		{
			// check to make sure they do not have a mimic
			$all_dependent_mimics = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability_mimics_ability ON ".$slrp_prefix."ability_mimics_ability.ability_id = ".$slrp_prefix."creature_ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability_mimics_ability.mimics_ability_id = '$alldepabs[ability_id]'") or die ("failed getting dependent ab mimics.");
			$alldpemmcscnt = mysql_num_rows($all_dependent_mimics);
			// if they have a mimic, the ability stays
			if($alldpemmcscnt >= 1)
			{
				$alldpemmcs = mysql_fetch_assoc($all_dependent_mimics);
				// echo"$alldepabs[ability] mimicked by $alldpemmcs[ability_id]: NOT CUT!<br>";
			}
			// if they lack a mimic, the ability is removed
			if($alldpemmcscnt == 0)
			{
				// echo"ability_required: $gtrqabs[ability]<br>";
				$trim_alldepabs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$gtrqabs[ability_id]'");
				
				$removed_dependent_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$gtrqabs[ability_id]'");
				$remdepabs = mysql_fetch_assoc($removed_dependent_abs);
				
				// echo"trimmed for no prereq: $remdepabs[ability].<br>";
			}
			
			// or if they do have the equirement, but it is only under study, and not yet usable
//			$check_dependent_is_not_being_studied = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '$alldepabs[ability_id]' AND creature_ability_level < '0'") or die ("failed checking for required ab being studied");
//			$chkdepnotstdycnt = mysql_num_rows($check_dependent_is_not_being_studied);
//			echo"study count: $chkdepnotstdycnt<br>";
//			if($chkdepnotstdycnt >= 1)
//			{
//				$trim_studydepabs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$gtrqabs[ability_id]'");
//			}
		}
	}
	
	// remove mimicked Abilities
	$check_dependent_mimics = mysql_query("SELECT * FROM ".$slrp_prefix."ability_mimics_ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."ability_mimics_ability.ability_id = ".$slrp_prefix."creature_ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability_mimics_ability.mimics_ability_id != '18' AND ".$slrp_prefix."ability_mimics_ability.mimics_ability_id != '19' AND ".$slrp_prefix."ability_mimics_ability.mimics_ability_id != '20' AND ".$slrp_prefix."ability_mimics_ability.mimics_ability_id != '160'") or die ("failed getting cr dependent ab mimics.");
	$chkdpemmcscnt = mysql_num_rows($check_dependent_mimics);
	// echo"ttl mimics: $chkdpemmcscnt<br>";
	while($chkdpemmcs = mysql_fetch_assoc($check_dependent_mimics))
	{
		// echo"Deleted mimicked $chkdpemmcs[mimics_ability_id]<br>";
		$trim_dpemmcs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$chkdpemmcs[mimics_ability_id]'");
	}
	
	// the character has the forbidden ability and cannot see this as a purchase option
	$all_forbidden_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON  ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' ORDER BY ".$slrp_prefix."ability.ability") or die ("Failed getting creature abs for Cannot Know trim function.");
	while($allfbdnabs = mysql_fetch_assoc($all_forbidden_abilities))
	{
		// echo"allfrbdn $allfbdnabs[ability_id]: $allfbdnabs[ability]<br>";
		
		$forbidden_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$allfbdnabs[ability_id]' AND ".$slrp_prefix."ability_modifier_subfocus.focus_id = '2' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id = '53'") or die ("failed verifying forbidden modifier.");
		$fbdnabnfo = mysql_fetch_assoc($forbidden_ability_info);
		$fbdnabnfocnt = mysql_num_rows($forbidden_ability_info);
		
		// echo"modifier: $fbdnabnfo[ability_modifier_short].<br>";
		
		$get_forbidden_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '$fbdnabnfo[ability_modifier_id]'");
		while($gtfbdnabs = mysql_fetch_assoc($get_forbidden_abilities))
		{
			// echo"<font color = 'red'>ability_forbidden: $gtfbdnabs[ability]</font><br>";
			
			$trim_allfbdnabs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$gtfbdnabs[ability_id]'");
			
			$removed_forbidden_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$gtfbdnabs[ability_id]'");
			$remfbdnabs = mysql_fetch_assoc($removed_forbidden_abs);
			
			// echo"forbidden due to known ability: $remfbdnabs[ability].<br>";
		}
	}
	
	// the character does not have the prerequisite item to see this as a purchase option
	$all_dependent_items = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id NOT IN (SELECT item_id FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]') ORDER BY item_id");
	while($alldepitms = mysql_fetch_assoc($all_dependent_items))
	{
		$requirement_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$alldepitms[item_id]' AND ".$slrp_prefix."ability_modifier_subfocus.focus_id = '10' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id = '6'") or die ("failed verifying required modifier.");
		$rqitmnfo = mysql_fetch_assoc($requirement_item_info);
		$rqitmnfocnt = mysql_num_rows($requirement_item_info);
		
		// echo"modifier: $rqitmnfo[ability_modifier_short].<br>";
		
		$get_dependent_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '$rqitmnfo[ability_modifier_id]'");
		while($gtdepabs = mysql_fetch_assoc($get_dependent_abs))
		{
			// echo"<font color = 'red'>item_required: $gtdepabs[ability]</font><br>";
			
			$trim_alldepabs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$gtdepabs[ability_id]'");
			
			$removed_dependents = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$gtdepabs[ability_id]'");
			$remdep = mysql_fetch_assoc($removed_dependents);
			
			// echo"trimmed for no item: $remdep[ability].<br>";
		}
	}
	
	// the character has a forbidden item and cannot see these abilities.
	$all_forbidden_items = mysql_query("SELECT * FROM ".$slrp_prefix."item INNER JOIN ".$slrp_prefix."creature_item ON  ".$slrp_prefix."creature_item.item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."creature_item.creature_id = '$curpcnfo[creature_id]' ORDER BY ".$slrp_prefix."item.item") or die ("Failed getting creature items for Not with trim function.");
	while($allfbdnitms= mysql_fetch_assoc($all_forbidden_items))
	{
		// echo"allfrbdn $allfbdnitms[item_id]: $allfbdnitms[item]<br>";
		
		$forbidden_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$allfbdnitms[item_id]' AND ".$slrp_prefix."ability_modifier_subfocus.focus_id = '10' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id = '8'") or die ("failed verifying forbidden item modifier.");
		$fbdnitmnfo = mysql_fetch_assoc($forbidden_item_info);
		$fbdnitmnfocnt = mysql_num_rows($forbidden_item_info);
		
		// echo"modifier: $fbdnitmnfo[ability_modifier_short].<br>";
		
		$get_forbidden_items = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '$fbdnitmnfo[ability_modifier_id]'");
		while($gtfbdnitms = mysql_fetch_assoc($get_forbidden_items))
		{
			// echo"<font color = 'red'>ability_forbidden for item: $gtfbdnitms[ability]</font><br>";
			
			$trim_allfbdnitms = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$gtfbdnitms[ability_id]'");
			
			$removed_forbidden_itm_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$gtfbdnitms[ability_id]'");
			$remfbdnitmabs = mysql_fetch_assoc($removed_forbidden_itm_abs);
			
			// echo"trimmed for forbidden item: $remfbdnitmabs[ability].<br>";
		}
	}
	
	// the character cannot see other-race abilities
	$all_forbidden_races = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON  ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id = '25' AND ".$slrp_prefix."ability_modifier_subfocus.subfocus_id != '$verpcrc[creature_subtype_id]'") or die ("Failed getting Only Usable By creature_subtype/race trim function.");
	while($allfbdnrcs= mysql_fetch_assoc($all_forbidden_races))
	{
		// echo"allfrbdnrc $allfbdnrcs[ability_modifier_id]: $allfbdnrcs[ability_modifier]<br>";

		$get_forbidden_race_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '$allfbdnrcs[ability_modifier_id]'");
		while($gtfbdnrcabs = mysql_fetch_assoc($get_forbidden_race_abs))
		{
			// echo"<font color = 'red'>ability_forbidden for item: $gtfbdnrcabs[ability]</font><br>";
			
			$trim_allfbdnrcabs = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$gtfbdnrcabs[ability_id]'");
			
			$removed_forbidden_race_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$gtfbdnrcabs[ability_id]'");
			$remfbdnraceabs = mysql_fetch_assoc($removed_forbidden_race_abs);
			
			// echo"trimmed for forbidden race abilities: $remfbdnraceabs[ability].<br>";
		}
	}
	
	if($curusrslrprnk[slurp_rank_id] >= 6)
	{
		if($curpcnfo[creature_status_id] == 4)
		{
			// these are restricted to be chosen only by staff, so they go away sometimes
			$restricted_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_status_id = '5'");
			while($restrab = mysql_fetch_assoc($restricted_abs))
			{
				$trim_restr = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE ability_id = '$restrab[ability_id]'");
			}
		}
	}
	
	// specifically checking for Source Marks just for dependent abilitis, since there are multiple source marks.
	$all_source_mark_dependents = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON  ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability LIKE '%Source Mark%'") or die ("Failed getting creature abs for source mark dep function.");
	while($allsrcmrkdeps = mysql_fetch_assoc($all_source_mark_dependents))
	{
		// echo"alldep $alldepabs[ability_id]: $alldepabs[ability]<br>";
			
		$srcmrk_req_ability_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier LIKE '%Must know Source Mark%'") or die ("failed verifying required source mark modifier.");
		$srcmrkrqabnfo = mysql_fetch_assoc($srcmrk_req_ability_info);
		$srcmrkrqabnfocnt = mysql_num_rows($srcmrk_req_ability_info);
		
		// echo"modifier: $rqabnfo[ability_modifier_short].<br>";
		$srcmrk_required_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = '$srcmrkrqabnfo[ability_modifier_id]'") or die ("failed getting source mark dep abmods");
		while($srcmrkrqabs = mysql_fetch_assoc($srcmrk_required_abilities))
		{
			// check to make sure they do not have a mimic
			$all_srcmrk_mimics = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability INNER JOIN ".$slrp_prefix."ability_mimics_ability ON ".$slrp_prefix."ability_mimics_ability.ability_id = ".$slrp_prefix."creature_ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability_mimics_ability.mimics_ability_id = '$allsrcmrkdeps[ability_id]'") or die ("failed getting src mrk dependent ab mimics.");
			$allsrcmrkmmcscnt = mysql_num_rows($all_srcmrk_mimics);
			if($allsrcmrkmmcscnt == 0)
			{
				$allsrcmrkmmcs = mysql_fetch_assoc($all_srcmrk_mimics);
				// echo"$allsrcmrkdeps[ability] mimicked by $allsrcmrkmmcs[ability_id]: NOT CUT!<br>";
			}
			if($allsrcmrkmmcscnt >= 1)
			{
				// echo"ability_required: $gtrqabs[ability]<br>";
				$have_some_source_mark_stuff = mysql_query("INSERT INTO ".$slrp_prefix."creature_ab_shopping_list(creature_id,ability_id,ability_build_cost_modified,ability_name) VALUES ('$curpcnfo[creature_id]','$srcmrkrqabs[ability_id]','$srcmrkrqabs[ability_build_cost]','$srcmrkrqabs[ability]')") or die("failed to add source mark dep abs.");			
				// echo"trimmed for no prereq: $remdepabs[ability].<br>";				
			}
		}
	}
	
	// get the temp table results and print the matching list of abilities that are left
	$final_id_list = mysql_query("SELECT ability_id, ability_name FROM ".$slrp_prefix."creature_ab_shopping_list WHERE creature_id = '$curpcnfo[creature_id]' ORDER BY ability_name ASC") or die ("failed to get final list.");
	$finidlstcnt = mysql_num_rows($final_id_list);
	if($finidlstcnt >= 1)
	{
	//	echo"
	//	<tr>
	//		<td colspan = '7' width='100%'>
	//			<table width='100%'>
	//	";
		
		while($finidlst = mysql_fetch_assoc($final_id_list))
		{
			$final_shopping_list = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$finidlst[ability_id]'") or die ("failed to get final list");
			$shoplist = mysql_fetch_assoc($final_shopping_list);
		
			$ab_nfo_id = $shoplist[ability_id];
			$learned = 0;
			$dressed = 0;
			$ab_shop = 1;
			include("modules/$module_name/includes/fn_ab_nfo.php");
		}
			
		//	echo"
		//			</table>
		//		</td>
		//	</tr>
		//	";
	}
	
	// delete the records in the temp table for that character and finish up
	$clean_up_that_mess = mysql_query("DELETE FROM ".$slrp_prefix."creature_ab_shopping_list WHERE creature_id = '$curpcnfo[creature_id]'");
}

echo"
</table>

</td>

</tr>
<tr background='themes/$ThemeSel/images/row2.gif' height='9'>
<td colspan = '9'>

</td>
</tr>
<tr>
<td valign = 'top' colspan='4'>
";

// include("modules/$module_name/includes/fm_efftyp_combos.php");

echo"
</tr>
";

if($curpcnfo[creature_status_id] == 2)
{
	echo"	<form name = 'back_to_pc_edit' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
	<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
	<td colspan='9' valign='middle'>
	<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
	<input type='hidden' value='1' name='ab_expander'>
	<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
	<input type='hidden' value='1' name='char_expander'>
	<input class='submit3' type='submit' value='Back to $curpcnfo[creature]' name='back_to_pc_edit'></td>
	</form>
</tr>
	";
}

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>