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
// include("modules/$module_name/includes/fn_prereq.php");

if(isset($_POST['core_ab_id']))
{
	$core_ab_id = $_POST['core_ab_id'];
}

// checkbox variables for the index
if(isset($_POST['current_expander']))
{
	$expander_abbr = $_POST['current_expander'];
	$expander = ($expander_abbr."_expander");
}

if(isset($_POST['ntro_expander']))
{
	$ntro_expander = $_POST['ntro_expander'];
}
else
{
	$ntro_expander = 0;
}

if(isset($_POST['race_desc_expander']))
{
	$race_desc_expander = $_POST['race_desc_expander'];
}
else
{
	$race_desc_expander = 0;
}

if(isset($_POST['library_id']))
{
	$library_id = $_POST['library_id'];
	// echo"$library_id<br>";
	$library_title = mysql_query("SELECT * FROM ".$slrp_prefix."library WHERE library_id = '$library_id'") or die ("failed getting library title.");
	$libttl = mysql_fetch_assoc($library_title);
}

// echo"exp: $expander_abbr, $expander<br>";

// uncomment the next line to check that variables are passing
// echo"PlayerID: $curpcplyr[0]<br>PC: $current_pc_id<br>$curpcnfo[creature]";

$rejection = 0;

// admin adding abilities, bypassing requirements
if(isset($_POST['admabcode']))
{
	$admabcode = 1;
}
else
{
	$admabcode = 0;
}

// adding abilities by entering the random code
if(isset($_POST['newabcode']))
{
	$newabcode = trim($_POST['newabcode']);
	// echo"code_id: $newabcode<br>";
//	$ability_code_check = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_focus_id = '2' AND object_random = '$newabcode' AND object_slurp_id = '$slrpnfo[slurp_id]'");
//	$abcodechkcnt = mysql_num_rows($ability_code_check);
//	// echo"ab code chk cnt: $abcodechkcnt<br>";
//	// if it is not a real code, let them know
//	if($abcodechkcnt == 0)
//	{
//		echo"
//		<tr>
//		
//		<td width = '100%'>
//		<font color = 'red'><b>
//		<li>$newabcode is not a working code.</b>
//		</form>
//		</td>
//		
//		</tr>
//		";
//		
//		$rejection++;
//	}
	
	// echo"l/c: $curpcnfo[creature_ability_learning_curve] < $slrpnfo[slurp_ability_learning_curve]<br>$abcodechkcnt...<br>";
	
	if($rejection == 0)
	{
		// if it is a real code, continue
	//	if($abcodechkcnt == 1)
	//	{
			// Safety, Staging, and Players are limited to the Learning Curve:
		if($curusrslrprnk[slurp_rank_id] >= 6)
		{
			// if they are at the max spending limit
			if($curpcnfo[creature_ability_learning_curve] >= $slrpnfo[slurp_ability_learning_curve])
			{
				echo"
				<tr>
				
				<td width = '100%'>
				<font color = 'red'><b>
				<li>$curpcnfo[creature] has learned as many Abilities as possible ($slrpnfo[slurp_ability_learning_curve]) at this time.</b>
				</td>
				
				</tr>
				";
				
				$rejection++;
			}
			
			if($rejection == 0)
			{
				// if they have not run over the max spending limit
//				if($curpcnfo[creature_ability_learning_curve] < $slrpnfo[slurp_ability_learning_curve])
//				{
//					while($abcodechk = mysql_fetch_assoc($ability_code_check))
//					{
						$abcode_ability = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$core_ab_id'");
						$abcodeab = mysql_fetch_assoc($abcode_ability);
						// echo"ab code ab: $abcodeab[ability]<br>";
						// if they already have the desired ability
						$get_pc_ability_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$abcodeab[ability_id]' AND creature_id = '$curpcnfo[creature_id]'");
						$getpcablstcnt = mysql_num_rows($get_pc_ability_list);
						$getpcablst = mysql_fetch_assoc($get_pc_ability_list);
						
						if($getpcablstcnt >= 1)
						{
							echo"
							<tr>
							
							<td width = '100%'>
							<font color = 'red'><b>
							<li>$curpcnfo[creature] already knows that version of $abcodeab[ability].</b>
							</td>
							
							</tr>
							";
						
						$rejection++;
						}
						
						// if they do not have the ability already
						if($getpcablstcnt == 0)
						{
							echo"
							<tr>
					
							<td width = '100%'>
							<font color = 'yellow'><b>
							<li>$curpcnfo[creature] does not already have $abcodeab[ability].</b>
							</td>
							
							</tr>
							";
						}
					}
				}
				
				// admins don't care about limits there
				if($curusrslrprnk[slurp_rank_id] <= 5)
				{
//					while($abcodechk = mysql_fetch_assoc($ability_code_check))
//					{
						$abcode_ability = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$core_ab_id'");
						$abcodeab = mysql_fetch_assoc($abcode_ability);
						// echo"ab code ab: $abcodeab[ability]<br>";
						// if they already have the desired ability
						$get_pc_ability_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$abcodeab[ability_id]' AND creature_id = '$curpcnfo[creature_id]'");
						$getpcablstcnt = mysql_num_rows($get_pc_ability_list);
						$getpcablst = mysql_fetch_assoc($get_pc_ability_list);
						
						if($getpcablstcnt >= 1)
						{
							echo"
							<tr>
							
							<td width = '100%'>
							<font color = 'red'><b>
							<li>$curpcnfo[creature] already knows that version of $abcodeab[ability].</b>
							</td>
							
							</tr>
							";
						
						$rejection++;
						}
						
						// if they do not have the ability already
						if($getpcablstcnt == 0)
						{
							echo"
							<tr>
					
							<td width = '100%'>
							<font color = 'yellow'><b>
							<li>$curpcnfo[creature] does not already have $abcodeab[ability].</b>
							</td>
							
							</tr>
							";
						}
//					}
				}
				
//				if($rejection == 0)
//				{
//					// get the attributes list
//					$all_attr = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id > '1'") or die ("failed getting attribute exclusion list.");
//					while($attrs = mysql_fetch_assoc($all_attr))
//					{
//						// echo"attr1: $attrs[attribute_type]<br>";
//						// get mods pointing at the attribute and modifying this ability
//						$required_attrs = mysql_query("SELECT * FROM ".$slrp_prefix."ability_ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id = ".$slrp_prefix."ability_ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_id = '4' AND ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$attrs[attribute_type_id]' AND ".$slrp_prefix."ability_ability_modifier.ability_id = '$abcodeab[ability_id]'") or die ("failed getting required attrs.");
//						$rqattrscnt = mysql_num_rows($required_attrs);
//						// echo"$rqattrscnt, ($abcodeab[ability]) attr1: $attrs[attribute_type]<br>";
//						if($rqattrscnt >= 1)
//						{
//							while($rqattrs = mysql_fetch_assoc($required_attrs))
//							{
//								// echo"$attrs[attribute_type]<br>";
//								// get mod info to compare to the pc attribute
//								$attr_dependent_mods = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '$rqattrs[ability_modifier_id]'") or die ("failed getting attr dependent mods.");
//								$attrdepmds = mysql_fetch_assoc($attr_dependent_mods);
//								// echo"attr2: $attrdepmds[ability_modifier_short]<br>";
//								
//								$attr_comparison_value = mysql_query("SELECT * FROM ".$slrp_prefix."focus_exclusion INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."focus_exclusion.focus_exclusion_id = ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id WHERE ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id = '$rqattrs[ability_modifier_id]'") or die ("failed getting abmod comparison value");  
//								$attrcompval = mysql_fetch_assoc($attr_comparison_value);
//								
//								// get the pc's instance of this attribute
//								$pc_attributes = mysql_query("SELECT * FROM ".$slrp_prefix."creature_attribute_type WHERE creature_id = '$curpcnfo[creature_id]' AND attribute_type_id = '$attrs[attribute_type_id]'") or die ("failed getting pc attrs for exclusion.");
//								$pcattrs = mysql_fetch_assoc($pc_attributes);
//								// echo"AB: $attrcompval[focus_comparison_value], PC: $pcattrs[creature_attribute_type_value]<br>";
//								
//								// get its tier
//								// $attr_tier = round(($pcattrs[creature_attribute_type_value]+(($slrpnfo[slurp_tier_width]-1)/2))/$slrpnfo[slurp_tier_width]);
//								
//								// mod value = negative (the tier value minus 1)
//								// $attr_tier_mod_value = -($attr_tier);
//								// echo"value: $attr_tier_mod_value<br>";
//								// if the tier is not high enough, reject them
//								// if($attr_tier_mod_value >= $attrdepmds[ability_modifier_value])
//								
//								
//								if($attrcompval[focus_comparison_value] > $pcattrs[creature_attribute_type_value])
//								{
//									echo"
//									<tr>
//									
//									<td width = '100%'>
//									<font color = 'red'>
//									<li><b>$curpcnfo[creature] does not have sufficient $attrs[attribute_type] to learn $newabcode.</b>
//									</td>
//									
//									</tr>
//									";
//								
//									$rejection++;
//									// echo"attr rejection: +1 = $rejection<br>";
//								}
//								
//								if($attrcompval[focus_comparison_value] <= $pcattrs[creature_attribute_type_value])
//								{
//									echo"
//									<tr>
//									
//									<td width = '100%'>
//									<font color = 'yellow'><b>
//									<li>$curpcnfo[creature] has the required $attrs[attribute_type] Tiers...</b>
//									</td>									
//									</tr>
//									";
//								}
//							}
//						}
//						if($rqattrscnt == 0)
//						{
//							echo"
//							<tr>
//							
//							<td width = '100%'>
//							<font color = 'yellow'><b>
//							<li>This Ability does not require $attrs[attribute_type] Tiers...</b>
//							</td>
//							
//							</tr>
//							";
//						}
//					}
//				}
				
				if($rejection == 0)
				{
					// see if they have the required effects
					$required_efftyps = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type INNER JOIN ".$slrp_prefix."ability_effect_type ON ".$slrp_prefix."ability_effect_type.effect_type_id = ".$slrp_prefix."effect_type.effect_type_id WHERE ".$slrp_prefix."ability_effect_type.ability_id = '$abcodeab[ability_id]'");
					$getrqefftypscnt = mysql_num_rows($required_efftyps);
					$rndstrsum = "";
					$rqtrcnt = $getrqefftypscnt;
					
					while($reqefftyp = mysql_fetch_assoc($required_efftyps))
					{
						$required_tiers = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE effect_type_id = '$reqefftyp[effect_type_id]' AND ability_id = '$abcodeab[ability_id]'");
						$reqtrscnt = mysql_num_rows($required_tiers);
						
						// echo"Tiers: $reqtrscnt<br>";
						while($reqtrs = mysql_fetch_assoc($required_tiers))
						{
							$pc_has_minimum = mysql_query("SELECT * FROM ".$slrp_prefix."creature_effect_type WHERE effect_type_id = '$reqefftyp[effect_type_id]' AND effect_type_tier >= '$reqtrs[effect_type_tier]' AND creature_id = '$curpcnfo[creature_id]'");
							$pchasmincnt = mysql_num_rows($pc_has_minimum);
							
							// if they lack the requisite effects, send them back
							$pchasmin = mysql_fetch_assoc($pc_has_minimum);
							
							if($pchasmincnt == 0)
							{
								$rqtrcnt++;
							}
							
							if($pchasmincnt >= 1)
							{
								$rqtrcnt--;
							}
						}
					}
					
//					if($rqtrcnt >= 1)
//					{
//						echo"
//						<tr>
//						
//						<td width = '100%'>
//						<font color = 'red'><b>
//						<li>$curpcnfo[creature] does not have high enough Effect Type Tiers.</b>
//						</td>
//						
//						</tr>
//						";
//						
//						$rejection++;
//					}
					
					// if they have the required effects
//					if($rqtrcnt == 0)
//					{
//						echo"
//						<tr>
//						
//						<td width = '100%'>
//						<font color = 'yellow'><b>
//						<li>$curpcnfo[creature] has all required Effect Type Tiers...</b>
//						</td>
//						
//						</tr>
//						";
//					}

					}

					if($rejection == 0)
					{
						if($admabcode = 0)
						{
							// verify they have all prerequisite abilities
							$abid = $abcodeab[ability_id];
							$prerequisites = 0;
							// echo"$abid<br>";
			
							// get all the modifiers on the ability
							$all_ability_mods = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_id = '$abid' ORDER BY ".$slrp_prefix."ability_modifier.ability_modifier_short") or die("failed to get all mods.");
							$allmodscnt = mysql_num_rows($all_ability_mods);
							while($allmods = mysql_fetch_assoc($all_ability_mods))
							{
								// for some reason this needed the table before the * and after the FROM, to calrify the 'ability_modifier_id' column, which the mySQL db declared as ambiguous.
								// this query sifts the modifiers that 'Must know' (16) an Ability (2) to know the desired Ability
								$get_subfocus_modifiers = mysql_query("SELECT ".$slrp_prefix."ability_modifier_subfocus.* FROM ".$slrp_prefix."ability_modifier_subfocus INNER JOIN ".$slrp_prefix."ability ON ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_id = '2' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id = '16' AND ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id = '$allmods[ability_modifier_id]' AND ".$slrp_prefix."ability.ability_id > '1'") or die("failed to get mods for subfoci relations.");
								$getsubmodcnt = mysql_num_rows($get_subfocus_modifiers);
								$submod_count = $getsubmodcnt;
								// echo "modcnt: $getsubmodcnt<br>ab_id: $abid <br>";	
								
								// get the list of Must Know
								while($getsubmod = mysql_fetch_assoc($get_subfocus_modifiers))
								{
									// echo"mod_id: $getsubmod[ability_modifier_id]<br>";
									// check to see if the character has the requisite abilities, and decrement the mod count for each one known
									$check_dependent_ability = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$getsubmod[subfocus_id]' and creature_id = '$curpcnfo[creature_id]'") or die ("failed getting creature ab fo mod dep.");
									$chkdependabcnt = mysql_num_rows($check_dependent_ability);
									while($chkdependab = mysql_fetch_assoc($check_dependent_ability))
									{
										$submod_count--;
										// echo"submod cnt: $submod_count<br>";
									}
								}
								$prerequisites = $prerequisites + $submod_count;
							}
							// echo"$prerequisites<br>";
							$req_ab_count = $prerequisites;			
							// echo"tot: $req_ab_count<br>";									
							
							// if they lack a prerequisite ability
							if($req_ab_count >= 1)
							{
								echo"
									<tr>
									
									<td width = '100%'>
									<font color = 'red'><b>
									<li>$curpcnfo[creature] is missing at least one prerequisite Ability.</b>
									</td>
									
									</tr>
								";
								
								$rejection++;
							}
							
							// if they have the prerequisites
							if($req_ab_count <= 0)
							{
								echo"
								<tr>
								
								<td width = '100%'>
								<font color = 'yellow'><b>
								<li>$curpcnfo[creature] knows all required Abilities...</b>
								</td>
								
								</tr>
								";
							}
						}
						if($admabcode = 1)
						{
							echo"
							<tr>
							
							<td width = '100%'>
							<font color = 'yellow'><b>
							<li>Admin override acknowledged.</b>
							</td>
							
							</tr>
							";
						}
					}
				}
		//}
		
//		if($abcodechkcnt >= 2)
//		{
//			echo"
//			<tr>
//				<td width = '100%'>
//					<font color = 'red'><b>
//					<li> This Ability has a duplicate random key with another Ability.
//						<font color = 'red'><li> Please alert Logistics, Rules, or Plot; we apologize for the inconvenience.
//					</font></b>
//				</td>
//			</tr>
//		";
//			
//			$rejection++;
//		}
}
	
// echo"rejection: $rejection<br>";

if($rejection == 0)
{
	// echo"$abcodeab[ability_id]<br>";
	$ability_code_check_2 = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_focus_id = '2' AND object_id = '$abcodeab[ability_id]' AND object_slurp_id = '$slrpnfo[slurp_id]' AND object_random_current='1'");
	$abcodechk2cnt = mysql_num_rows($ability_code_check_2);
	$abcodechk2 = mysql_fetch_assoc($ability_code_check_2);
	$abcode_ability_2 = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$abcodechk2[object_id]'");
	$abcodeab2 = mysql_fetch_assoc($abcode_ability_2);

	if($admabcode = 0)
	{
		$final_build_cost = $abcodeab2[ability_build_cost];
		
		if($verpcrc[creature_subtype] == "Ogres")
		{
			if($abcodeab2[ability_set_id] == 8)
			{
				$final_build_cost=$final_build_cost-5;
			}
		}
		if($verpcrc[creature_subtype] == "Goblins")
		{
			if($abcodeab2[ability_set_id] == 11)
			{
				$final_build_cost--;
				$final_build_cost--;
			}
		}
		if($verpcrc[creature_subtype] == "Elves")
		{
			if($abcodeab2[ability_set_id] == 4)
			{
				$final_build_cost--;
			}
			if($abcodeab2[ability_set_id] == 7)
			{
				$final_build_cost--;
			}
		}
		if($verpcrc[creature_subtype] == "Gnomes")
		{
			if($abcodeab2[ability_set_id] == 6)
			{
				$final_build_cost--;
				$final_build_cost--;
			}
		}
	}
	if($admabcode = 1)
	{
		$final_build_cost = 0;
	}
	//$new_coded_ability = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id) VALUES ('$curpcnfo[creature_id]','$abcodeab[ability_id]','$abcodechk2[object_random_id]')");
	$new_coded_ability = mysql_query("INSERT INTO ".$slrp_prefix."creature_ability (creature_id,ability_id,ability_random_id,ability_build_cost,creature_ability_count) VALUES ('$curpcnfo[creature_id]','$abcodeab2[ability_id]','$abcodechk2[object_random_id]','$final_build_cost','1')");
	// $decrement_learning_curve = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_ability_learning_curve = creature_ability_learning_curve+1 WHERE creature_id = '$curpcnfo[creature_id]'");

	// if the character is approved, log it by the current user.
	if($curpcnfo[creature_status_id] == 4)
	{
		$xp_change = -($final_build_cost);
		$decrement_experience = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_xp_current = (creature_xp_current-$xp_change) WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating experience for new ability.");
		if($admabcode = 0)
		{
			$reason = ("Added Ability: ".$abcodeab2[ability]." for $xp_change XP.");
		}
		if($admabcode = 1)
		{
			$reason = ("Admin-Granted Ability: ".$abcodeab2[ability]." for 0 XP.");
		}
		
		$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','$xp_change','$usrnfo[user_id]','$reason')");	
	}
		
	echo"
	<tr>
	
	<td width = '100%'>
	<font color = 'yellow'><b>
	<li>$curpcnfo[creature] has mastered $abcodeab2[ability].</b>
	</td>
	
	</tr>
	";
}

if($rejection >= 1)
{					
	echo"
	<tr>
	
	<td width = '100%'>
	<font color = 'red'><b>
	<li>$curpcnfo[creature] has failed to master $newabcode.</b>
	</td>
	
	</tr>
	";
}

echo"
<tr background='themes/RedShores/images/row1.gif' height='9'>

<td colspan = '9' >
</td>

</tr>
<tr>
";

if($library_id > 1)
{
	echo"
	<td width = '2%'>
	</td>
	<form name = 'pc_ab_shop' method='post' action = 'modules.php?name=$module_name&file=ab_shop'>
	<td align = 'left' valign = 'top'>
	<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
	<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
	<input type='hidden' value='$library_id' name='library_id'>
	<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
	<input class='submit3' type='submit' value='Back to $libttl[library]' name='pc_ab_shop'>
	</td>
	</form>
	";
}

echo"
<form name = 'pc_eff_typ' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
<td align = 'left' valign = 'top'>
<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
<input type='hidden' value='$core_ab_id' name='core_ab_id'>
<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
<input class='submit3' type='submit' value='Back to Effect Types' name='pc_eff_typ'>
</td>
</form>
";

if(isset($_POST['back_to_ab_edit']))
{
	echo"
	<td width = '2%'>
	</td>

	<form name = 'back_to_ab_edit' method='post' action = 'modules.php?name=$module_name&file=ab_edit'>
	<td align = 'center' valign = 'top'>
	<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
	<input type='hidden' value='$abcodeab[ability_id]' name='current_ab_id'>
	<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
	<input type='hidden' value='2' name='current_focus_id'>
	<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
	<input class='submit3' type='submit' value='Back to $abcodeab[ability]' name='back_to_ab_edit'>
	</td>
	</form>
	";
}


echo"
<td width = '2%'>
</td>

<form name = 'back_to_edit' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
<td align = 'right' valign = 'top' width = '18%'>
<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
<input class='submit3' type='submit' value='Back to $curpcnfo[creature]' name='back_to_edit'>
</td>
</form>

</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>