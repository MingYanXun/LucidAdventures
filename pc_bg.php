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
//$_POST['current_pc_id'] = 1;
$nav_title = "Edit Background";
$nav_page = "pc_bg";
include("modules/$module_name/includes/slurp_header.php");

if(empty($_POST['player_id']))
{
	$player_id = $usrnfo[user_id];
}
else
{
	$player_id = $_POST['player_id'];
}

// make sure the fields will handle empty and full sessions, as this will double as the edit screen

if(empty($_POST['pc_npc']))
{
	$pc_npc = 0;
}
if(isset($_POST['pc_npc']))
{
	$pc_npc = 1;
}

// if a newly minted template, blank the background fields 
if(isset($_POST['copy_template']))
{
	if($curusrslrprnk[slurp_rank_id] >= 5)
	{
		// echo"blanking out pc stuff.<br>";
		
		$blank_out_character_info = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_true_name = '', creature_desc = '', creature_nuke_user_id = '$usrnfo[user_id]', creature_socio = '', creature_parents = '', creature_shaped1 = '', creature_shaped2 = '', creature_shaped3 = '', creature_hunted1 = '', creature_hunted2 = '', creature_hunted3 = '', creature_org1 = '', creature_org2 = '', creature_org3 = '' WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed blanking template bg info.");
	}
}

if(isset($_POST['add_pc_bg_culture_default']))
{
	 $create_culture_new = mysql_query("INSERT INTO ".$slrp_prefix."creature_culture (creature_id,culture_id,culture_tolerance_id,culture_tolerance_notes) VALUES('$curpcnfo[creature_id]','1','1','None')") or die ("failed inserting default pc culture of origin.");
}

include("modules/$module_name/includes/fn_pc_race_reset.php");

if(isset($_POST['newpcregion']))
{
	$new_pc_region = $_POST['newpcregion'];
	// echo"region:$new_pc_region<br>";
	$upd_pc_region = mysql_query("UPDATE ".$slrp_prefix."creature_geography_subtype SET geography_subtype_id='$new_pc_region' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC geography_subtype.");
}

if(isset($_POST['newpclocation']))
{
	$new_pc_location = $_POST['newpclocation'];
	// echo"loc:$new_pc_location<br>";
	$upd_pc_location = mysql_query("UPDATE ".$slrp_prefix."creature_geography SET geography_id='$new_pc_location' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC geography.");
}

$post_creature_culture_listings = mysql_query("SELECT  * FROM ".$slrp_prefix."creature_culture WHERE creature_culture_id > '1'") or die ("failed getting pc culture post list.");
while($postcrcult = mysql_fetch_assoc($post_creature_culture_listings))
{
	// echo"cr cult id: $postcrcult[creature_culture_id]<br>";
	if(isset($_POST['newpcculture_'.$postcrcult[creature_culture_id].'_tol']))
	{
		// echo"creature_culture_id: $postcrcult[creature_culture_id]";
		$new_pc_culture = $_POST['newpcculture_'.$postcrcult[creature_culture_id].'_tol'];
		$new_pc_culttol = $_POST['new_culture_tolerance_'.$postcrcult[creature_culture_id].'_id'];
		$new_pc_cultnot = strip_tags(mysql_real_escape_string($_POST['new_culture_tolerance_'.$postcrcult[creature_culture_id].'_notes']));
		// echo"$new_pc_culture, $new_pc_culttol, $new_pc_cultnot<br>";
		
		if($new_pc_culttol == 0)
		{
			$del_pc_culture = mysql_query("DELETE FROM ".$slrp_prefix."creature_culture WHERE creature_culture_id='$postcrcult[creature_culture_id]'") or die("failed to delete PC culture.");
			
			if($curpcnfo[creature_status_id] == 4)
			{
				$culture_default_bbgroup = mysql_query("SELECT * FROM ".$slrp_prefix."culture_bbgroup WHERE culture_id = '$new_pc_culture'") or die ("failed getting default culture bbgroup for del.");
				while($cultdfbbgrp = mysql_fetch_assoc($culture_default_bbgroup))
				{
					 echo"$cultdfbbgrp[user_id], $cultdfbbgrp[group_id]<br>";
					$del_pc_culture_bbgroup = mysql_query("DELETE FROM nuke_bbuser_group WHERE group_id = '$cultdfbbgrp[group_id]' AND user_id = '$curpcnfo[creature_nuke_user_id]'") or die("failed to delete PC culture group.");
				}
			}
		}
		if($new_pc_culttol >= 1)
		{
			// echo"cult: $new_pc_culture, tol: $new_pc_culttol notes: $new_pc_cultno<br>";
			$upd_pc_culture = mysql_query("UPDATE ".$slrp_prefix."creature_culture SET culture_id='$new_pc_culture',culture_tolerance_id='$new_pc_culttol',culture_tolerance_notes='$new_pc_cultnot' WHERE creature_culture_id='$postcrcult[creature_culture_id]'") or die("failed to update PC culture.");
			
			if($curpcnfo[creature_status_id] == 4)
			{
				$culture_default_bbgroup = mysql_query("SELECT * FROM ".$slrp_prefix."culture_bbgroup WHERE culture_id = '$new_pc_culture'") or die ("failed getting default culture bbgroup for del.");
				while($cultdfbbgrp = mysql_fetch_assoc($culture_default_bbgroup))
				{
					$verify_character_race_bbgroups = mysql_query("SELECT * FROM nuke_bbuser_group WHERE group_id = '$cultdfbbgrp[group_id]' AND user_id = '$curpcnfo[creature_nuke_user_id]'") or die ("failed verifying pc culture bbgroups.");
					$vrchrrcbbgrpscnt = mysql_num_rows($verify_character_race_bbgroups);
					if($vrchrrcbbgrpscnt == 0)
					{
					 	echo"$cultdfbbgrp[user_id], $cultdfbbgrp[group_id]<br>";
						$add_pc_culture_bbgroup = mysql_query("INSERT INTO nuke_bbuser_group (group_id,user_id,user_pending) VALUES ('$cultdfbbgrp[group_id]','$curpcnfo[creature_nuke_user_id]','0')") or die("failed to add PC culture group.");
					}
				}
			}
		}
	}
}

// uncomment the line below to check the important stuf is coming through
// echo"$usrnfo[user_id], $usrnfo[name], $usrpccnt<br><br>";

// uncomment the next command to check if the variables are being passed
// echo"<br>PCNAME: $new_pc_name, USERID: $newpcuserid<br>$new_pc_creature, $new_pc_true_name, $new_pc_concept,  $pc_npc, $new_pc_socio, $new_pc_parents, $new_pc_shaped1, $new_pc_shaped2, $new_pc_shaped3, $new_pc_hunted1, $new_pc_hunted2, $new_pc_hunted3, $new_pc_org1, $new_pc_org2, $new_pc_org3<br>$slrpnfo[slurp_starting_attribute_points]<br>";

// update the record
// echo"updating creature.<br>Name: $<br>";
// select from the inserted records, then display them in txt fields for review

// echo"nw:$pc_temp<br>";
include("modules/$module_name/includes/pcinfo.php");

echo"
<tr>
	<form name = 'upd_pc_bg_origins' method = 'post' action = 'modules.php?name=$module_name&file=pc_bg'>
	<td colspan = '7' valign = 'top' align = 'center'>
		<font class='heading1'>
		This is what we now have on record. Make any needed corrections and continue.
		<br>
		<font color = 'red'>Required fields are in red.</font>
		<br>
		<br>
";

OpenTable3();

echo"
		<font class='heading1'><font color = 'red'>Race: </font></font>	<select class='engine' name = 'newpccreature'>
";

if($verpcrccnt == 1)
{
	echo"
		<option value = '$verpcrc[creature_subtype_id]'>$verpcrc[creature_subtype]</option>
	";
}

if($verpcrccnt == 0)
{
	echo"
		<option value = '1'>Choose a Race</option>
	";
}

$creature_type_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_type WHERE creature_type_id > '1' AND creature_type != 'Gender' AND creature_type_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY creature_type") or die ("failed getting creature type list.");

while($crtyplst = mysql_fetch_assoc($creature_type_list))
{
	echo"
		<optgroup label = '$crtyplst[creature_type]'>
	";
	
	$creature_subtype_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype INNER JOIN ".$slrp_prefix."creature_subtype_creature_type ON ".$slrp_prefix."creature_subtype_creature_type.creature_subtype_id = ".$slrp_prefix."creature_subtype.creature_subtype_id WHERE ".$slrp_prefix."creature_subtype.creature_subtype_id > '1' AND ".$slrp_prefix."creature_subtype_creature_type.creature_type_id = '$crtyplst[creature_type_id]' AND ".$slrp_prefix."creature_subtype.creature_subtype_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY ".$slrp_prefix."creature_subtype.creature_subtype") or die ("failed to get creature subtype list.");
	
	while($crsbtyplst = mysql_fetch_assoc($creature_subtype_list))
	{
		echo"
		<option value = '$crsbtyplst[creature_subtype_id]'>$crsbtyplst[creature_subtype]</option>
		";
	}
	echo"
		</optgroup>
		";
}

echo"
		</select>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input class='submit3' type='submit' value='Update' name='upd_pc_bg_origins'>
";

if($verpcrccnt == 1)
{
	echo"<br><br><font class='heading1'>$verpcrc[creature_subtype_desc]";
}

// begin locations
//$verify_pc_reg = mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype INNER JOIN ".$slrp_prefix."creature_geography_subtype ON ".$slrp_prefix."creature_geography_subtype.geography_subtype_id = ".$slrp_prefix."geography_subtype.geography_subtype_id WHERE ".$slrp_prefix."creature_geography_subtype.geography_subtype_id > '1' AND ".$slrp_prefix."creature_geography_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc region info.");
//$verpcrg = mysql_fetch_assoc($verify_pc_reg);
//$verpcrgcnt = mysql_num_rows($verify_pc_reg);
//
//echo"
//		<br>
//		<br>
//		<font class='heading1'>
//		<font color = 'red'>Region of Origin:</font> <select class='engine' name = 'newpcregion'>
//";
//
//if($verpcrgcnt == 1)
//{
//	echo"
//		<option value = '$verpcrg[geography_subtype_id]'>$verpcrg[geography_subtype]</option>
//	";
//}
//
//if($verpcrgcnt == 0)
//{
//	echo"
//		<option value = '1'>Choose a Region</option>
//	";
//}
//
//$geography_type_list = mysql_query("SELECT * FROM ".$slrp_prefix."geography_type WHERE geography_type_id > '1' AND geography_type_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY geography_type") or die ("failed getting geography_type list.");
//
//while($geotyplst = mysql_fetch_assoc($geography_type_list))
//{
//	$geography_subtype_list = mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype INNER JOIN ".$slrp_prefix."geography_subtype_geography_type ON ".$slrp_prefix."geography_subtype_geography_type.geography_subtype_id = ".$slrp_prefix."geography_subtype.geography_subtype_id WHERE ".$slrp_prefix."geography_subtype.geography_subtype_id > '1' AND ".$slrp_prefix."geography_subtype_geography_type.geography_type_id = '$geotyplst[geography_type_id]' AND ".$slrp_prefix."geography_subtype.geography_subtype_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY ".$slrp_prefix."geography_subtype.geography_subtype") or die ("failed to get geography_subtype list.");
//	$geosbtplst = mysql_num_rows($geography_subtype_list);
//	if($geosbtplst >- 1)
//	{
//		echo"
//			<optgroup label = '$geotyplst[geography_type]'>
//		";
//		while($geosbtplst = mysql_fetch_assoc($geography_subtype_list))
//		{
//			echo"
//			<option value = '$geosbtplst[geography_subtype_id]'>$geosbtplst[geography_subtype]</option>
//			";
//		}
//		echo"
//			</optgroup>
//		";
//	}
//}
//
//echo"
//		</select> <input class='submit3' type='submit' value='Update' name='upd_pc_bg_origins'>
//";
//
//if($verpcrgcnt == 1)
//{
//	echo"<br><br><font class='heading1'>$verpcrg[geography_subtype_desc]";
//}
//
//// begin locations
//$verify_pc_loc = mysql_query("SELECT * FROM ".$slrp_prefix."geography INNER JOIN ".$slrp_prefix."creature_geography ON ".$slrp_prefix."creature_geography.geography_id = ".$slrp_prefix."geography.geography_id WHERE ".$slrp_prefix."creature_geography.geography_id > '1' AND ".$slrp_prefix."creature_geography.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc location info.");
//$verpclc = mysql_fetch_assoc($verify_pc_loc);
//$verpclccnt = mysql_num_rows($verify_pc_loc);
//
//echo"
//		</font>
//		<br>
//		<br>
//		<font class='heading3'>
//		Location of Origin:</font>	<select class='engine' name = 'newpclocation'>
//";
//
//if($verpclccnt == 1)
//{
//	echo"
//		<option value = '$verpclc[geography_id]'>$verpclc[geography]</option>
//	";
//}
//
//if($verpclccnt == 0)
//{
//	echo"
//		<option value = '1'>Choose a Location</option>
//	";
//}
//
//$geography_subtype_list = mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype WHERE geography_subtype_id > '1' AND geography_subtype_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY geography_subtype") or die ("failed getting geography_subtype list.");
//
//while($geosbtyplst = mysql_fetch_assoc($geography_subtype_list))
//{
//	$geography_list = mysql_query("SELECT * FROM ".$slrp_prefix."geography INNER JOIN ".$slrp_prefix."geography_geography_subtype ON ".$slrp_prefix."geography_geography_subtype.geography_id = ".$slrp_prefix."geography.geography_id WHERE ".$slrp_prefix."geography.geography_id > '1' AND ".$slrp_prefix."geography_geography_subtype.geography_subtype_id = '$geosbtyplst[geography_subtype_id]' AND ".$slrp_prefix."geography.geography_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY ".$slrp_prefix."geography.geography") or die ("failed to get geography list.");
//	$geolstcnt = mysql_num_rows($geography_list);
//	if($geolstcnt >- 1)
//	{
//		echo"
//			<optgroup label = '$geosbtyplst[geography_subtype]'>
//		";
//		while($geolst = mysql_fetch_assoc($geography_list))
//		{
//			echo"
//			<option value = '$geolst[geography_id]'>$geolst[geography]</option>
//			";
//		}
//		echo"
//			</optgroup>
//		";
//	}
//}
//
//echo"
//		</select> 				<input type='hidden' value='char' name='current_expander'>
//		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
//		<input class='submit3' type='submit' value='Update' name='upd_pc_bg_origins'>
//";
//
//if($verpclccnt == 1)
//{
//	echo"<br><br><font class='heading1'>$verpclc[geography_desc]";
//}
//
//echo"<br>
//<br>
//</form>
//";
//
//$verify_pc_cult = mysql_query("SELECT * FROM ".$slrp_prefix."culture INNER JOIN ".$slrp_prefix."creature_culture ON ".$slrp_prefix."creature_culture.culture_id = ".$slrp_prefix."culture.culture_id WHERE ".$slrp_prefix."creature_culture.creature_id = '$curpcnfo[creature_id]' ORDER BY ".$slrp_prefix."culture.culture") or die ("failed verifying pc culture origin info.");
//$verpccultcnt = mysql_num_rows($verify_pc_cult);
//$verify_pc_cult2 = mysql_query("SELECT * FROM ".$slrp_prefix."culture INNER JOIN ".$slrp_prefix."creature_culture ON ".$slrp_prefix."creature_culture.culture_id = ".$slrp_prefix."culture.culture_id WHERE ".$slrp_prefix."creature_culture.creature_id = '$curpcnfo[creature_id]' ORDER BY ".$slrp_prefix."culture.culture") or die ("failed verifying pc culture origin info.");
//$verpccult2cnt = mysql_num_rows($verify_pc_cult2);
//if($verpccult2cnt == 1)
//{
//	$verpccult2 = mysql_fetch_assoc($verify_pc_cult2);
//}
//echo"
//<form name = 'add_pc_bg_culture_default' method = 'post' action = 'modules.php?name=$module_name&file=pc_bg'>
//		<font class='heading1'>
//		<font color = 'red'>Guilds/Houses/Nations/Groups (choose at least one):</font>
//		<input type='hidden' value='char' name='current_expander'>
//		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
//";
//
//if($verpccultcnt >= 1)
//{
//	if($verpccult2[culture_id] >= 2)
//	{
//		echo"
//					<input class='submit3' type='submit' value='Add Group' name='add_pc_bg_culture_default'>
//		";
//	}
//	if($verpccultcnt >= 2)
//	{
//		echo"
//					<input class='submit3' type='submit' value='Add Group' name='add_pc_bg_culture_default'>
//		";
//	}
//}
//
//echo"
//</form>
//<form name = 'add_pc_bg_culture' method = 'post' action = 'modules.php?name=$module_name&file=pc_bg'>
//";
//
//
//while($verpccult = mysql_fetch_assoc($verify_pc_cult))
//{
//	$verify_crcultnfo = mysql_query("SELECT * FROM ".$slrp_prefix."creature_culture WHERE creature_id = '$curpcnfo[creature_id]' AND culture_id = '$verpccult[culture_id]'") or die ("failed verifying creature culture info.");
//	$vercrcultnfo = mysql_fetch_assoc($verify_crcultnfo);
//	
////	echo"
////	<tr>
////		<td width = '100%' valign = 'top'>
////	";
//	
//	
//	echo"<select class='engine' name = 'newpcculture_".$vercrcultnfo[creature_culture_id]."_tol'>
//			<option value = '$verpccult[culture_id]'>$verpccult[culture]</option>
//	";
//	
//	$culture_subtype_list = mysql_query("SELECT * FROM ".$slrp_prefix."culture_subtype WHERE culture_subtype_id > '1' AND culture_subtype_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY culture_subtype") or die ("failed getting culture_subtype list.");
//	
//	while($cultsbtyplst = mysql_fetch_assoc($culture_subtype_list))
//	{
//		$culture_list = mysql_query("SELECT * FROM ".$slrp_prefix."culture INNER JOIN ".$slrp_prefix."culture_culture_subtype ON ".$slrp_prefix."culture_culture_subtype.culture_id = ".$slrp_prefix."culture.culture_id WHERE ".$slrp_prefix."culture.culture_id > '1' AND ".$slrp_prefix."culture_culture_subtype.culture_subtype_id = '$cultsbtyplst[culture_subtype_id]' AND ".$slrp_prefix."culture.culture_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY ".$slrp_prefix."culture.culture") or die ("failed to get culture list.");
//		$cultlstcnt = mysql_num_rows($culture_list);
//		if($cultlstcnt >= 1)
//		{
//			echo"
//			<optgroup label = '$cultsbtyplst[culture_subtype]'>
//			";
//	
//			while($cultlst = mysql_fetch_assoc($culture_list))
//			{
//				echo"
//				<option value = '$cultlst[culture_id]'>$cultlst[culture]</option>
//				";
//			}
//			echo"
//				</optgroup>
//			";
//		}
//	}
//	
//	$verify_pc_cult_tol = mysql_query("SELECT * FROM ".$slrp_prefix."culture_tolerance INNER JOIN ".$slrp_prefix."creature_culture ON ".$slrp_prefix."creature_culture.culture_tolerance_id = ".$slrp_prefix."culture_tolerance.culture_tolerance_id WHERE ".$slrp_prefix."creature_culture.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_culture.culture_id = '$verpccult[culture_id]'") or die ("failed verifying pc culture tolerance info.");
//	$verpcculttol = mysql_fetch_assoc($verify_pc_cult_tol);
//	$verpcculttolcnt = mysql_num_rows($verify_pc_cult_tol);
//	
//	echo"
//			</select>. . . <select class='engine' name ='new_culture_tolerance_".$vercrcultnfo[creature_culture_id]."_id'>
//	";
//	
//	if($verpcculttolcnt == 0)
//	{
//		echo"	<option>Choose Tolerance Level</option>";
//	}
//	
//	if($verpcculttolcnt >= 1)
//	{
//		echo"<option value = '$verpcculttol[culture_tolerance_id]'>$verpcculttol[culture_tolerance]</option>";
//	}
//	
//	echo"<option value = '0'>Delete This Relation</option>";
//	
//	$get_tolerances_list = mysql_query("SELECT * FROM ".$slrp_prefix."culture_tolerance WHERE culture_tolerance_id > '1'") or die ("failed getting tolerances list.");
//	while($gettolslst = mysql_fetch_assoc($get_tolerances_list))
//	{
//		echo"<option value = '$gettolslst[culture_tolerance_id]'>$gettolslst[culture_tolerance]</option>";
//	}	
//	
//	echo"
//			</select>
//	";
//	
//	echo" . . . <input type = 'text' class='textbox3' name ='new_culture_tolerance_".$vercrcultnfo[creature_culture_id]."_notes' value = '$verpccult[culture_tolerance_notes]'></font>";	
//
//	$grphc_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix."object_graphic WHERE object_focus_id = '30' AND object_id = '$verpccult[culture_id]' AND object_slurp_id = '$slrpnfo[slurp_id]'") or die("failed to get culture graphic.");
//	$grphcsbfccnt = mysql_num_rows($grphc_subfocus);
//	$grphcsbfc = mysql_fetch_assoc($grphc_subfocus);
//	$graphic_identifier = $grphcsbfc[graphic_id];
//	if($graphic_identifier >= 2)
//	{
//		// get the object graphic, if any
//		$get_object_graphic = mysql_query("SELECT * FROM ".$slrp_prefix."graphic WHERE graphic_id = '$graphic_identifier'") or die ("failed to get graphic info.");
//		$gtobjgrphccnt = mysql_num_rows($get_object_graphic);
//		$gtobjgrphc = mysql_fetch_assoc($get_object_graphic);
//		$graphic_identifier = $gtobjgrphc[graphic_id];
//		// echo"current graphic($gtobjgrphccnt): $gtpbjgrphc[graphic]<br>";
//		echo"  <img src = 'images/$gtobjgrphc[graphic]' height = '50' width = '50'>";	
//	}
//	
//	echo"
//		<input type='hidden' value='char' name='current_expander'>
//		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
//		<input class='submit3' type='submit' value='Update' name='upd_pc_bg_culture'>
//	";
//	
//	if($verpccult[culture_id] >= 2)
//	{
//		echo"<br><br> <font class='heading1'>$verpccult[culture_desc]";
//	}
//	
////	echo"
////		</td>
////	</tr>
////	";
//}//

CloseTable3();
echo"
</form>
<form name = 'pc_bg_form' method = 'post' action = 'modules.php?name=$module_name&file=pc_bg_form'>
";

$pcname = stripslashes($curpcnfo[creature]);

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	$pctruename = stripslashes($curpcnfo[creature_true_name]);
}

$pcconcept = stripslashes($curpcnfo[creature_desc]);
$pcparents = stripslashes($curpcnfo[creature_parents]);
$pcsocio = stripslashes($curpcnfo[creature_socio]);
$pcshaped1 = stripslashes($curpcnfo[creature_shaped1]);
$pcshaped2 = stripslashes($curpcnfo[creature_shaped2]);
$pcshaped3 = stripslashes($curpcnfo[creature_shaped3]);
$pchunted1 = stripslashes($curpcnfo[creature_hunted1]);
$pchunted2 = stripslashes($curpcnfo[creature_hunted2]);
$pchunted3 = stripslashes($curpcnfo[creature_hunted3]);
$pcorg1 = stripslashes($curpcnfo[creature_org1]);
$pcorg2 = stripslashes($curpcnfo[creature_org2]);
$pcorg3 = stripslashes($curpcnfo[creature_org3]);

OpenTable3();
echo"<font class='heading1'>
			<center>
			<font color = 'orange'>To proceed without completing the background form, enter 'None' into the fields and come back later. 
			<br>
			You have 3 months (attending or not) to complete the form
			<br>
			before the character stops receiving individual attention and storylines.
			<br>
			<b>This feeds our development of your story.</b>
			</center>
			<br>
			<br>
			<font color = 'red'>What is the character's full name?</font>
			<br>
			<textarea name ='newpcname' rows = '4' cols = '100'>$pcname</textarea>";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
		<br>
		<font color = 'red'>What is the character's <i>True Name</i>? (Type <i>None</i> if you wish this to be provided for you). </font>
		<br>
		<textarea name ='newpctruename' rows = '4' cols = '100'>$pctruename</textarea>
	";
}

echo"
		<br>
		<font color = 'red'>In a few words, give a concept summation of your character, like <i>Healer Gone Bad</i> or <i>Weaselly Confidence Man</i> or <i>Ruthless Knowledge Seeker</i>.<font color = 'black'>
		<br>
		<textarea name ='newpcconcept' rows = '4' cols = '100'>$pcconcept</textarea>
		<br>
		<font color = 'red'>Who were the character's parents?
		<br>
		<textarea name ='newpcparents' rows = '4' cols = '100'>$pcparents</textarea>
		<br>
		<font color = 'red'>What is the character's socio-economic background?
		<br>
		<textarea name ='newpcsocio' rows = '4' cols = '100'>$pcsocio</textarea>

		<hr class='pipes'>

		<font class='heading1'><font color = 'red'>List three things that have shaped the character importantly, and briefly describe why:
		<br>
		<textarea name ='newpcshaped1' rows = '4' cols = '100'>$pcshaped1</textarea>
		<br>
		<textarea name ='newpcshaped2' rows = '4' cols = '100'>$pcshaped2</textarea>
		<br>
		<textarea name ='newpcshaped3' rows = '4' cols = '100'>$pcshaped3</textarea>

		<hr class='pipes'>
		
		<font class='heading1'><font color = 'red'>List at least one: enemies, hatreds, prejudices, people the character is hunting or who are hunting the character, and why:
		<br>
		<textarea name ='newpchunted1' rows = '4' cols = '100'>$pchunted1</textarea>
		Enemy/Hunted/Prejudice/Obsession
		<br>
		<textarea name ='newpchunted2' rows = '4' cols = '100'>$pchunted2</textarea
		<br>
		Enemy/Hunted/Prejudice/Obsession
		<br>
		<textarea name ='newpchunted3' rows = '4' cols = '100'>$pchunted3</textarea>
";
CloseTable3();
echo"
		<font class='heading1'><font color='orange'>
			Optionally, a longer story detailing your character's life may be
			<br>
			entered by clicking the [Story Form] button on the next page.
			<br>
			You still must complete the main form above, and the story 
			<br>
			should include all of the items mentioned in the main form.
		</font>
	</td>
</tr>
<tr background='themes/Vanguard/images/back2b.gif' height='24'>
	<td width='100%' colspan='7' align='left' valign='middle'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='1' name='char_expander'>
		<input class='submit3' type='submit' value='Submit Background' name='pc_bg_form'>
	</td>
</tr>
</form>
<tr height='9'>
	<td width='100%' colspan='7' align='left' valign='middle'>
	</td>
</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>