<?php
if (!eregi("modules.php", $PHP_SELF))
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);
$nav_page = "ab_print_page";

include("modules/$module_name/includes/slurp_min_header.php");

$listcount = 1;
$rowcount = 0;
$colcount = 0;

// get abilities list

while($listcount <= 4)
{
	if($colcount == 0)
	{
		echo"<tr>";
		
		$rowcount++;
	}
	
	echo"
		<td valign = 'top' width = '25%' align = 'left'>
	";
	
	$abcnt = 0;
	// $abnms = mysql_fetch_assoc($abnames);
	if(isset($_POST[$rowcount.$colcount]))
	{
		if(isset($_POST['verbose_'.$rowcount.$colcount]))
		{
			$verbose = 1;
		}
		if(empty($_POST['verbose_'.$rowcount.$colcount]))
		{
			$verbose = 0;
		}
		
		// echo "$rowcount$colcount<br>";
		$print_ab_id = $_POST[$rowcount.$colcount];
		
		// start ability pane proper
		echo"
			<table cellpadding = '2' width = '100%' cellborder = '0' border = '0' height = '375'>
				<tr>
					<td width = '100%' valign = 'top' align = 'right'>
						<table cellpadding = '0' width = '100%' cellborder = '0' border = '0'>
		";
		// ^above^ start of the main content table after the sidebar of each pane
		
		$abnames = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$print_ab_id'");
		$curpcab2 = mysql_fetch_assoc($abnames);

		$get_current_special_effect = mysql_query("SELECT * FROM ".$slrp_prefix."effect_special INNER JOIN ".$slrp_prefix."ability_effect_special ON ".$slrp_prefix."effect_special.effect_special_id = ".$slrp_prefix."ability_effect_special.effect_special_id WHERE ".$slrp_prefix."ability_effect_special.ability_id = '$curpcab2[ability_id]' ORDER BY ".$slrp_prefix."ability_effect_special.effect_special_default,".$slrp_prefix."effect_special.effect_special") or die ("failed getting current special effects.");
		$gtcurrspcleffcnt = mysql_num_rows($get_current_special_effect);

		$ability_weight = mysql_query("SELECT SUM(effect_type_tier) FROM ".$slrp_prefix."ability_effect_type WHERE ability_id = '$curpcab2[ability_id]'");
		$abwght = mysql_fetch_array($ability_weight, MYSQL_NUM);
		
		$unknown_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE ".$slrp_prefix."object_random.object_id = '$curpcab2[ability_id]' AND ".$slrp_prefix."object_random.object_focus_id = '2'  AND ".$slrp_prefix."object_random.object_random_current = '1' AND ".$slrp_prefix."object_random.object_slurp_id = '$slrpnfo[slurp_id]'");
		$unkabrnd = mysql_fetch_assoc($unknown_ability_random);
//		{
//			//$abrnd = $unkabrnd[3];
//			// echo"RNDu: $abrnd<br>";
//		}
		
		if($curpcab2[ability_id] >= 2)
		{
			echo"
							<tr>
								<td align = 'center' valign = 'top' colspan = '2' width = '100%'>
									<font color = 'black' size = '4'>
									<b>$curpcab2[ability]</b>
									</font>
									<hr>
								</td>
							</tr>
										<tr>
											<td valign = 'top' align = 'left' width = '45%'>
			";
			
			$get_all_effect_types1 = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id > '1' ORDER BY effect_type_support, effect_type") or die ("failed getting all ab chars.");
			while($gtallefftyps = mysql_fetch_assoc($get_all_effect_types1))
			{
				$get_ab_effect_type = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE effect_type_id = '$gtallefftyps[effect_type_id]' AND ability_id = '$curpcab2[ability_id]' ORDER BY effect_type_id");
				$gtabefftyp = mysql_fetch_assoc($get_ab_effect_type);
				$gtabefftypcnt = mysql_num_rows($get_ab_effect_type);
				
				if($gtabefftypcnt >= 1)
				{
					if($gtallefftyps[effect_type_support] == 0)
					{
						$effect_type_color = "black";
					}
					if($gtallefftyps[effect_type_support] == 1)
					{
						$effect_type_color = "#993300";
					}
					
					// echo"$gtallefftyps[0], $gtallefftyps[1], $gtallefftyps[2], $gtallefftyps[3]<br>";
					echo"<b><font color = '$effect_type_color' size = '3'>$gtallefftyps[effect_type] ".roman($gtabefftyp[effect_type_tier])."</font></b><br>";
				}
				
//				if($gtabefftypcnt == 0)
//				{
//					// echo"$gtallefftyps[0], $gtallefftyps[1], $gtallefftyps[2], $gtallefftyps[3]<br>";
//					echo"<font color = '#993300' size = '3'>$gtallefftyps[1]</font><br>";
//				}
			}
				
			echo"
											</td>		
											<td valign = 'top' align = 'right' width = '45%'>										
											";
											
											// flagged unlimited because costs were so low
											if($curpcab2[ability_unlimited_uses] == 1)
											{
												echo" <font color = 'red'><b>UNLIM</b></font>";
											}
											// based on the Effect Type Tier
											if($curpcab2[ability_unlimited_uses] == 3)
											{
												echo" <font color = 'orange'><b>Effect Type Tier/day</b></font>";
											}
											if($curpcab2[ability_unlimited_uses] == 2)
											{
												$check_for_zero_multiplier = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_id = '$curpcab2[ability_id]' AND ".$slrp_prefix."ability_modifier.ability_modifier_value = '0' AND ".$slrp_prefix."ability_modifier.ability_modifier_type_id = '9'") or die("failed to get zero mulitplier modifier.");
												$chkzromltcnt = mysql_num_rows($check_for_zero_multiplier);
												// echo"# of Modifiers: $chkzromltcnt<br>";
												
												if($chkzromltcnt >= 1)
												{
													$chkzromlt = mysql_fetch_assoc($check_for_zero_multiplier);
													
													$cost_multiplier = $chkzromlt[ability_modifier_value];
													// echo"extreme mult: $cost_multiplier, $chkzromlt[2]<br>";
													echo" <font color = 'red' size = '2'><b>UNLIM</b></font>";
												}
												if($chkzromltcnt == 0)
												{
													$required_attribute_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '$curpcab2[ability_id]'");
													$reqattrcstcnt = mysql_num_rows($required_attribute_cost);
													while($reqattrcst = mysql_fetch_assoc($required_attribute_cost))
													{
														//echo"$reqattrcst[0], $reqattrcst[1], $reqattrcst[2], $reqattrcst[3]<br>";
														$required_attribute = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id = '$reqattrcst[attribute_type_id]'") or die ("failed to get attributes for cost.");
														$reqattr =  mysql_fetch_assoc($required_attribute);
														
														if($reqattrcstcnt >= 1)
														{
															// normal attribute costs
															if($reqattrcst[ability_cost] >= 1)
															{
																if($reqattr[attribute_type_id] == 2)
																{
																	echo" <font color = '#4AC948'><b>$reqattrcst[ability_cost] $reqattr[attribute_type_short]</b></font>";
																}
																
																if($reqattr[attribute_type_id] == 3)
																{
																	echo" <font color = '#00B2EE'><b>$reqattrcst[ability_cost] $reqattr[attribute_type_short]</b></font>";
																}
																
																if($reqattr[attribute_type_id] == 4)
																{
																	echo" <font color = '#CC00FF'><b>$reqattrcst[ability_cost] $reqattr[attribute_type_short]</b></font>";
																}
															}
														}
													}
												}
											}
											if($curpcab2[ability_unlimited_uses] == 0)
											{
												$required_attribute_cost = mysql_query("SELECT * FROM ".$slrp_prefix."ability_cost WHERE ability_id = '$curpcab2[ability_id]'");
												$reqattrcstcnt = mysql_num_rows($required_attribute_cost);
												while($reqattrcst = mysql_fetch_assoc($required_attribute_cost))
												{
													//echo"$reqattrcst[0], $reqattrcst[1], $reqattrcst[2], $reqattrcst[3]<br>";
													$required_attribute = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id = '$reqattrcst[attribute_type_id]'") or die ("failed to get attributes for cost.");
													$reqattr =  mysql_fetch_assoc($required_attribute);
													
													if($reqattrcstcnt >= 1)
													{
														// charges
														if($reqattrcst[ability_cost] <= 0)
														{
															$charges_count = ($reqattrcst[ability_cost] -1);
															$invert_charges_count = -($charges_count);
															
															if($invert_charges_count == 1)
															{
																$numbered_noun = "Use";
															}
															if($invert_charges_count >= 2)
															{
																$numbered_noun = "Uses";
															}
															
															// echo"rqatcst: $reqattrcst[1]<br>";
															echo" <font color = 'orange'><b>$invert_charges_count $numbered_noun</b></font>";
															
															// echo"<font color = 'orange'>unlim: $unlimited_uses_threshold, chg.cnt: $charges_count</font>";
														}
													}
												}
											}
																			
											echo"
												<br>
												<font size = '3' color = 'blue'>
											";
											
											while($gtcurrspcleff = mysql_fetch_assoc($get_current_special_effect))
											{
												echo"$gtcurrspcleff[effect_special] ";
											}
								
											echo"
												</font>
												<br>
												(<font color= 'blue' size = '2'>$unkabrnd[object_random]</font>)
											</td>
										</tr>
									<tr>
										<td align = 'left' valign  = 'top'  width = '100%' colspan ='2'>
											<table width = '100%'>
			";

			echo"	
												<tr>
													<td align = 'left' valign  = 'top' width = '100%' colspan ='2'>
														<hr>
														<font size = '3'>*<i>$curpcab2[ability_verbal]</i>*</font>
														<br>
														<font color = 'black' size = '3'>$curpcab2[ability_desc]</font>
														<hr>
													</td>
												</tr>
			";	
			
			$get_effect = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect INNER JOIN ".$slrp_prefix."effect_type ON ".$slrp_prefix."effect_type.effect_type_id = ".$slrp_prefix."ability_effect.effect_type_id WHERE ".$slrp_prefix."ability_effect.ability_id = '$curpcab2[ability_id]' ORDER BY ".$slrp_prefix."effect_type.effect_type_support, ".$slrp_prefix."effect_type.effect_type") or die("failed to get ablity effects.");
			$geteffcnt = mysql_num_rows($get_effect);
			$effect_counter = $geteffcnt;
			
			// echo"eff_cnt: $geteffcnt<br>";
			
			if($geteffcnt >= 1)
			{
					echo"
												<tr>
													<td width = '50%' align = 'left' valign = 'top'>
					";
				$effect_tier = 0;
				while($geteff = mysql_fetch_assoc($get_effect))
				{
					$effect_modifier_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '$geteff[effect_modifier_id]'") or die ("failed getting effect modifier info for listing.");
					$effmodnfo = mysql_fetch_assoc($effect_modifier_info);
					
					$effect_type_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id = '$geteff[effect_type_id]'") or die ("failed getting effect type info for listing.");
					$efftypenfo = mysql_fetch_assoc($effect_type_info);
					
					// for base effects, add the tier to the subtotal
					if($efftypenfo[effect_type_support] == 0)
					{
						$effect_color = 'black';
						$cost_count_sub = $cost_count_sub + $effmodnfo[effect_type_support];
						$printed_weight = $effmodnfo[effect_type_support];
					}
					// for support effects, add nothing to the subtotal			
					if($efftypenfo[effect_type_support] == 1)
					{
						$effect_color = '#993300';
						$printed_weight = 0;
					}
					
					// echo"chars: $effmodnfo[0] ($effmodnfo[3])<br>";
					// print the value and description
					echo"
														<font color = '$effect_color' size = '2'>
														<li>$effmodnfo[ability_modifier_short]

					";
				}
			}
			
								echo"
													</td>
					";
			// get modifiers and print to the screen
			$current_ability_modifiers = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier INNER JOIN ".$slrp_prefix."ability_ability_modifier ON ".$slrp_prefix."ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_ability_modifier.ability_id = '$curpcab2[ability_id]' ORDER BY ".$slrp_prefix."ability_modifier.ability_modifier_type_id") or die("failed to get modifiers.");
			$curabmodcnt = mysql_num_rows($current_ability_modifiers);
			// echo"Modifiers: $curabmodcnt<br>";
			
			echo"						
									<td width = '50%' align = 'left' valign = 'top'>
			";
			
			while($curabmod = mysql_fetch_assoc($current_ability_modifiers))
			{
				// get the type of modifier
				$ability_modifier_type = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_type WHERE ability_modifier_type_id = '$curabmod[ability_modifier_type_id]'") or die("failed to get modifier type.");
				$abmodtyp = mysql_fetch_assoc($ability_modifier_type);
				
				if($abmodtyp[ability_modifier_type_id] == 3)
				{
					$mod_type_color = "#993300";
				}
				
				if($abmodtyp[ability_modifier_type_id] == 4)
				{
					$mod_type_color = "#993300";
				}
					
				if($abmodtyp[ability_modifier_type_id] == 5)
				{
					$mod_type_color = "black";
				}
				
				if($abmodtyp[ability_modifier_type_id] == 6)
				{
					$mod_type_color = "red";
				}
				
				if($abmodtyp[ability_modifier_type_id] == 7)
				{
					$mod_type_color = "blue";
				}
				
				if($abmodtyp[ability_modifier_type_id] == 8)
				{
					$mod_type_color = "red";
				}
				
				if($abmodtyp[ability_modifier_type_id] == 9)
				{
					$mod_type_color = "purple";
				}
				
				// get things the modifier affects
				$ability_modifier_presub = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_id ='$curabmod[ability_modifier_id]'") or die ("failed to get presub.");
				while($abmodpre = mysql_fetch_assoc($ability_modifier_presub))
				{
					$ability_modifier_focus = mysql_query("SELECT * FROM ".$slrp_prefix."focus WHERE focus_id ='$abmodpre[focus_id]' ORDER BY focus_priority") or die("failed to get modifier focus.");
					$abmodfoccnt = mysql_num_rows($ability_modifier_focus);
					$abmodfoc = mysql_fetch_assoc($ability_modifier_focus);
					
					// echo"$abmodfoc[1]: ";
					// get the specific limiters
					$ability_modifier_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier_subfocus WHERE ability_modifier_subfocus_id = '$abmodpre[ability_modifier_subfocus_id]'");
					$abmodsubcnt = mysql_num_rows($ability_modifier_subfocus);
					while($abmodsub = mysql_fetch_assoc($ability_modifier_subfocus))
					{	
						echo"<font color='$mod_type_color' size ='2'>";
						// echo"sub: $abmodsub[2] ";
						
						// if there are no exclusions, print the basic format
						if($abmodsub[focus_exclusion_id] <= 1)
						{
							$get_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix.$abmodfoc[focus_table]." WHERE ".$abmodfoc[focus_table]."_id = '$abmodsub[subfocus_id]'") or die ("failed getting no exclusion sub.");
							while($getsub= mysql_fetch_assoc($get_subfocus))
							{
								echo "<li>".$getsub[$abmodfoc[focus_table]];
							}
						}
						
						// if there are exclusions, get them and print them also
						if($abmodsub[focus_exclusion_id] >= 2)
						{
							$get_exclusion = mysql_query("SELECT * FROM ".$slrp_prefix."focus_exclusion WHERE focus_exclusion_id = '$abmodsub[focus_exclusion_id]'")or die ("failed getting exclusion.");
							$getexcl= mysql_fetch_assoc($get_exclusion);
							
							$get_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix.$abmodfoc[focus_table]." WHERE ".$abmodfoc[focus_table]."_id = '$abmodsub[subfocus_id]'") or die ("failed getting exclusion sub.");
							while($getsub= mysql_fetch_assoc($get_subfocus))
							{
								echo "<li>".$getexcl[focus_exclusion]." ".$getsub[$abmodfoc[focus_table]];
							}
						}
						
						echo"</font>";
					}
				}
			}
			
			// end second pane of main content, and the row holding both panes
			echo"
											</td>
										</tr>
									</table>
								</td>
							</tr>
			";
		}
		
		echo"
						</table>
					</td>
				</tr>
			</table>
		";
		// end character sheet table
		
		echo"<hr>";
		
		echo"
			<table cellpadding = '3' width = '100%' cellborder = '1' height = '375'>
				<tr>
					<td width = '2%' valign = 'top' align = 'left'>
						<font size = '1' color = '#993300'>
						<font color = 'black'>
						RAN
						</font>
						<br>
						tch
						<br>
						proj
						<br>
						pkt
						<br>
						2pkt
						<br>
						call
						<br>
						<font size = '1' color = 'black'>
						SIZ
						<br>
						(live)
						</font>
						<br>
						1pc
						<br>
						2pc
						<br>
						4pc
						<br>
						8pc
						<br>
						16pc
						<br>
						<font size = '1' color = 'black'>
						SIZ
						<br>
						(not)
						</font>
						<br>
						3' r.
						<br>
						5' r.
						<br>
						10' r.
						<br>
						20' r.
						<br>
						40' r.
						<br>
						<font size = '1' color = 'black'>
						TIM
						</font>
						<br>
						0-5s
						<br>
						15s
						<br>
						1m
						<br>
						3m
						<br>
						10m
					</td>
					
					<td width = '1%'>
					</td>
					
					<td width = '97%' valign = 'top' align = 'right'>
						<table width = '100%'>
							<tr>
								<td align = 'right' valign = 'top' colspan = '2' width = '100%'>
									<font color = 'black' size = '3'><b></b>
									<br>
									<font size = '1'></font>
									<br>
								</td>
							</tr>
							<tr>
								<td align = 'right' valign  = 'top'  width = '20%'>
								<font size = '2' color = 'black'>
								";
								
//								if($curpcab2[17] != "")
//								{
//									echo"
//									<hr>
//									$curpcab2[17]
//									<hr>
//									";
//								}
								
								echo"
								</font>
								</td>
								
								<td valign = 'top' align = 'right'  width = '80%'>
									<table width = '100%'>
										<tr>
											<td align = 'right' width = '100%' valign = 'top'>		
		";
		
		$get_attributes_list = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id > '1'") or die("failed getting attr list");
		while($attrlist = mysql_fetch_assoc($get_attributes_list))
		{
			if($attrlist[attribute_type_id] == 2)
			{
				$attr_font = "#4AC948";
			}
			
			if($attrlist[attribute_type_id] == 3)
			{
				$attr_font = "#00B2EE";
			}
			
			if($attrlist[attribute_type_id] == 4)
			{
				$attr_font = "#CC00FF";
			}
			echo"
												<font color = '#bbbbbb'> $attrlist[attribute_type_short]</font>
												<br>
			";
			
			$get_attribute_minimmum_requirements = mysql_query("SELECT * FROM ".$slrp_prefix."ability_ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_ability_modifier.ability_modifier_id = ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_id = '4' AND ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$attrlist[attribute_type_id]' AND ".$slrp_prefix."ability_ability_modifier.ability_id = '$curpcab2[ability_id]' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id >= '85' AND ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id <= '90'") or die ("failed getting min attr prereq modifiers.");
			while($gtattrminreq = mysql_fetch_assoc($get_attribute_minimmum_requirements))
			{
				$req_min_mod = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '$gtattrminreq[ability_modifier_id]'") or die ("failed getting min attr req mod nfo.");
				$rqmnmd = mysql_fetch_assoc($req_min_mod);
				
				echo"<font color = '$attr_font' size = '3'><b>$rqmnmd[ability_modifier_short]</b></font><br>";
			}
		}
		
		echo"
											</td>
										</tr>
										<tr>
											<td bgcolor = '#aaaaaa' align = 'right' width = '100%' valign = 'top'>
		";
		
		$get_all_effect_types = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type WHERE effect_type_id > '1' ORDER BY effect_type_support, effect_type") or die ("failed getting all ab chars.");
		while($gtallefftyp = mysql_fetch_assoc($get_all_effect_types))
		{
			$get_ab_effect_type = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE effect_type_id = '$gtallefftyp[effect_type_id]' AND ability_id = '$curpcab2[ability_id]' ORDER BY effect_type_id");
			$gtabefftyp = mysql_fetch_assoc($get_ab_effect_type);
			$gtabefftypcnt = mysql_num_rows($get_ab_effect_type);
			
			if($gtabefftypcnt >= 1)
			{
				if($gtallefftyp[effect_type_support] == 0)
				{
					$effect_type_color = "black";
				}
				if($gtallefftyp[effect_type_support] == 1)
				{
					$effect_type_color = "#993300";
				}
				
				// echo"$gtallefftyp[effect_type_id], $gtallefftyp[effect_type], $gtallefftyp[2], $gtallefftyp[3]<br>";
				echo"<b><font color = '$effect_type_color' size = '4'>$gtallefftyp[effect_type] ".roman($gtabefftyp[effect_type_tier])."</font></b><br>";
			}
			
//			if($gtabefftypcnt == 0)
//			{
//				// echo"$gtallefftyp[effect_type_id], $gtallefftyp[effect_type], $gtallefftyp[2], $gtallefftyp[3]<br>";
//				echo"<font color = '#993300' size = '3'>$gtallefftyp[effect_type]</font><br>";
//			}
		}
		
		echo"
											</td>
										</tr>
									</table>
									<br>";
				
				// graphic handler
				$dressed = 0;
				$current_object_id = $print_ab_id;
				$current_focus_id = 2;
				include("modules/$module_name/includes/fm_obj_graphic.php");
	
				
				echo"
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		";
		// end character sheet table (and cell)
	}
	
	echo"
		</td>
	";
	
	$listcount++;
	$colcount++;
	
	if($colcount == 4)
	{
		echo"
		</tr>
		";
		
		$colcount = 0;
	}
}

// include("modules/$module_name/includes/slurp_footer.php");
// require("footer.php");
?>