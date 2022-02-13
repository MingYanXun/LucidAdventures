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
$nav_title = "Verifying Character Background";
include("modules/$module_name/includes/slurp_header.php");

// echo"pc: $curpcnfo[creature_id]<br>";

$emptycount = 0;

echo"
<tr>
<td width = '100%'>

<font color = 'yellow' size = '2'>
<b>This is what we have so far:</b>
<hr>
</td>
</tr>
";

echo"<form name = 'pc_bg' method='post' action = 'modules.php?name=$module_name&file=pc_bg'>";


if(isset($_POST['newpcstory']))
{
	$pcstory = $_POST['newpcstory'];
	$upd_pc_name = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_story='$pcstory' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC story.");
}
else
{
	$pcstory = $curpcnfo[creature_story];
}

if(empty($_POST['newpcname']))
{
	if($curpcnfo[creature] == '')
	{
		echo"
		<tr>
		<td width = '100%'>
		
		<font color = 'red'>
		<li> The character must have a name.
		<hr>
		</td>
		</tr>
		";
	}
	else
	{
		$new_pc_name = $curpcnfo[creature];
	}
}
if(isset($_POST['newpcname']))
{
	$new_pc_name = strip_tags(mysql_real_escape_string($_POST['newpcname']));
	
	// echo"$new_pc_name, $slrp_prefix, $curpcnfo[creature_id]<br>";
	
	$upd_pc_name = mysql_query("UPDATE ".$slrp_prefix."creature SET creature='$new_pc_name' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC name.");
}

//if(empty($_POST['newpccreature']))
//{
//	echo"
//	<tr>
//	<td width = '100%'>
//	<font color = 'red'>
//	<li> The character must have a race/creature subtype.
//	<hr>
//	</td>
//	</tr>
//	";
//	
//	$emptycount++;
//}

if(isset($_POST['newpccreature']))
{
	$new_pc_creature = $_POST['newpccreature'];
	// echo"race id: $new_pc_creature<br>";
	$upd_pc_creature = mysql_query("UPDATE ".$slrp_prefix."creature_creature_subtype SET creature_subtype_id='$new_pc_creature' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC creature subtype.");
}

$verify_race = mysql_query("SELECT  * FROM ".$slrp_prefix."creature_subtype INNER JOIN ".$slrp_prefix."creature_creature_subtype ON ".$slrp_prefix."creature_creature_subtype.creature_subtype_id = ".$slrp_prefix."creature_subtype.creature_subtype_id WHERE ".$slrp_prefix."creature_creature_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc race");
$vrrc = mysql_fetch_assoc($verify_race);

echo"
<tr>
<td width = '100%'>
<font color = '#7fffd4'>
Race: <font color = 'white'>$vrrc[creature_subtype]
<hr>
</td>
</tr>
";	

//if(isset($_POST['newpcregion']))
//{
//	$new_pc_region = $_POST['newpcregion'];
//	 echo"region:$new_pc_region<br>";
//	$upd_pc_region = mysql_query("UPDATE ".$slrp_prefix."creature_geography_subtype SET geography_subtype_id='$new_pc_region' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC geography_subtype.");
//}
//$verify_creature_region = mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype INNER JOIN ".$slrp_prefix."creature_geography_subtype ON ".$slrp_prefix."creature_geography_subtype.geography_subtype_id = ".$slrp_prefix."geography_subtype.geography_subtype_id WHERE ".$slrp_prefix."creature_geography_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc geography_subtype");
//$vrcrrg = mysql_fetch_assoc($verify_creature_region);
//
//echo"
//<tr>
//<td width = '100%'>
//<font color = '#7fffd4'>
//Region of Origin: <font color = 'white'>$vrcrrg[geography_subtype]
//<hr>
//</td>
//</tr>
//";	

//if(isset($_POST['newpclocation']))
//{
//	$new_pc_location = $_POST['newpclocation'];
//	 echo"loc:$new_pc_location<br>";
//	$upd_pc_location = mysql_query("UPDATE ".$slrp_prefix."creature_geography SET geography_id='$new_pc_location' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC geography.");
//}
//
//$verify_creature_location = mysql_query("SELECT * FROM ".$slrp_prefix."geography INNER JOIN ".$slrp_prefix."creature_geography ON ".$slrp_prefix."creature_geography.geography_id = ".$slrp_prefix."geography.geography_id WHERE ".$slrp_prefix."creature_geography.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc geography");
//$vrcrlc = mysql_fetch_assoc($verify_creature_location);

//$verify_location_region = mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype INNER JOIN ".$slrp_prefix."geography_geography_subtype ON ".$slrp_prefix."geography_geography_subtype.geography_subtype_id = ".$slrp_prefix."geography_subtype.geography_subtype_id WHERE ".$slrp_prefix."geography_geography_subtype.geography_id = '$vrcrlc[geography_id]'") or die ("failed verifying region location.");
//$vrlcrg = mysql_fetch_assoc($verify_location_region);
//$upd_pc_location_region = mysql_query("UPDATE ".$slrp_prefix."creature_geography_subtype SET geography_subtype_id='$vrlcrg[geography_subtype_id]' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC geography_subtype.");

//echo"
//<tr>
//<td width = '100%'>
//<font color = '#7fffd4'>
//Location of Origin: <font color = 'white'>$vrcrlc[geography]
//<hr>
//</td>
//</tr>
//";	

//$post_creature_culture_listings = mysql_query("SELECT  * FROM ".$slrp_prefix."creature_culture WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed getting pc culture post list.");
//while($postcrcult = mysql_fetch_assoc($post_creature_culture_listings))
//{
//	// echo"cr cult id: $postcrcult[creature_culture_id]<br>";
//	if(isset($_POST['newpcculture_'.$postcrcult[creature_culture_id].'_tol']))
//	{
//		// echo"creature_culture_id: $postcrcult[creature_culture_id]";
//		$new_pc_culture = $_POST['newpcculture_'.$postcrcult[creature_culture_id].'_tol'];
//		$new_pc_culttol = $_POST['new_culture_tolerance_'.$postcrcult[creature_culture_id].'_id'];
//		$new_pc_cultnot = strip_tags(mysql_real_escape_string($_POST['new_culture_tolerance_'.$postcrcult[creature_culture_id].'_notes']));
//		
//		// echo"cult: $new_pc_culture, tol: $new_pc_culttol notes: $new_pc_cultno<br>";
//		$upd_pc_culture = mysql_query("UPDATE ".$slrp_prefix."creature_culture SET culture_id='$new_pc_culture',culture_tolerance_id='$new_pc_culttol',culture_tolerance_notes='$new_pc_cultnot' WHERE creature_culture_id='$postcrcult[creature_culture_id]'") or die("failed to update PC culture.");
//	}
//			
//	$verify_culture = mysql_query("SELECT  * FROM ".$slrp_prefix."culture INNER JOIN ".$slrp_prefix."creature_culture ON ".$slrp_prefix."creature_culture.culture_id = ".$slrp_prefix."culture.culture_id WHERE ".$slrp_prefix."creature_culture.creature_culture_id='$postcrcult[creature_culture_id]'") or die ("failed verifying pc culture.");
//	$vrcult = mysql_fetch_assoc($verify_culture);
//	$verify_culture_tolerance = mysql_query("SELECT  * FROM ".$slrp_prefix."culture_tolerance INNER JOIN ".$slrp_prefix."creature_culture ON ".$slrp_prefix."creature_culture.culture_tolerance_id = ".$slrp_prefix."culture_tolerance.culture_tolerance_id WHERE ".$slrp_prefix."creature_culture.creature_culture_id='$postcrcult[creature_culture_id]'") or die ("failed verifying pc culture_tolerance.");
//	$vrculttol = mysql_fetch_assoc($verify_culture_tolerance);
//	
//	echo"
//	<tr>
//	<td width = '100%'>
//	<font color = '#7fffd4'>
//	Group Association: <font color = 'white'>$vrcult[culture] [$vrculttol[culture_tolerance]] $vrculttol[culture_tolerance_notes]
//	<hr>
//	</td>
//	</tr>
//	";	
//}

//$post_creature_insanity_listings = mysql_query("SELECT * FROM ".$slrp_prefix."creature_effect WHERE ".$slrp_prefix."creature_effect.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting insanity post list.");
//while($postcrnsan = mysql_fetch_assoc($post_creature_insanity_listings))
//{
//	// echo"cr insanity id: $postcrnsan[creature_effect_id]<br>";
//	if(isset($_POST['newpcinsanity_'.$postcrnsan[creature_effect_id].'_eff']))
//	{
//		// echo"creature_culture_id: $postcrcult[creature_culture_id]";
//		$new_pc_insanity = $_POST['newpcinsanity_'.$postcrnsan[creature_effect_id].'_eff'];
//		$new_pc_nsantarg = $_POST['new_insanity_'.$postcrnsan[creature_effect_id].'_target'];
//		$new_pc_nsannot = strip_tags(mysql_real_escape_string($_POST['new_insanity_'.$postcrnsan[creature_effect_id].'_notes']));
//		// echo"$new_pc_insanity, $new_pc_nsantarg, $new_pc_nsannot<br>";
//		
//		if($new_pc_nsantarg == 0)
//		{
//			$del_pc_insanity = mysql_query("DELETE FROM ".$slrp_prefix."creature_effect WHERE creature_effect_id='$postcrnsan[creature_effect_id]'") or die("failed to delete PC insanity.");
//		}
//		if($new_pc_nsantarg >= 1)
//		{
//			// echo"cult: $new_pc_culture, tol: $new_pc_culttol notes: $new_pc_cultno<br>";
//			$upd_pc_insanity = mysql_query("UPDATE ".$slrp_prefix."creature_effect SET effect_id='$new_pc_insanity',effect_modifier_id='$new_pc_nsantarg',creature_effect_notes='$new_pc_nsannot' WHERE creature_effect_id='$postcrnsan[creature_effect_id]'") or die("failed to update PC insanity.");
//		}
//	}
//	
//	$verify_crnsannfo = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$postcrnsan[effect_id]'") or die ("failed verifying pc insanity effect info.");
//	$verpcnsan = mysql_fetch_assoc($verify_crnsannfo);
//	$get_insanity_target_modifier_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '$postcrnsan[effect_modifier_id]'") or die ("failed getting tolerances list.");
//	$getnsantargmodnfo = mysql_fetch_assoc($get_insanity_target_modifier_info);
//	
//	echo"
//	<tr>
//	<td width = '100%'>
//	<font color = '#7fffd4'>
//	Insanity: <font color = 'white'>$verpcnsan[effect] [$getnsantargmodnfo[ability_modifier_short]]:  $verpcnsan[effect_desc]
//	<hr>
//	</td>
//	</tr>
//	";
//}

if($curusrslrprnk[slurp_rank_id] <= 3)
{
	if(empty($_POST['newpctruename']))
	{
		if($curpcnfo[creature_true_name] == '')
		{
			echo"
			<tr>
			<td width = '100%'>
			<font color = 'red'>
			<li> The character must have a true name.
			<hr>
			</td>
			</tr>
			";
			
			$emptycount++;
		}
		else
		{
			$new_pc_true_name = $curpcnfo[creature_true_name];
		}
	}
	if(isset($_POST['newpctruename']))
	{
		$new_pc_true_name = strip_tags(mysql_real_escape_string($_POST['newpctruename']));
		
		$upd_pc_truename = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_true_name='$new_pc_true_name' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC true name.");
	}
	if(isset($new_pc_true_name))
	{
		echo"
		<tr>
		<td width = '100%'>
		<font color = '#7fffd4'>
		True Name: <font color = 'white'>$new_pc_true_name<br>
		<hr>
		</td>
		</tr>
		";
	}
}

if(empty($_POST['newpcconcept']))
{
	if($curpcnfo[creature_desc] == '')
	{
		echo"
		<tr>
		<td width = '100%'>
		<font color = 'red'>
		<li> The character must have a concept.
		<hr>
		</td>
		</tr>
		";
		
		$emptycount++;
	}
	else
	{
		$new_pc_concept = $curpcnfo[creature_desc];
	}
}
if(isset($_POST['newpcconcept']))
{
	$new_pc_concept = strip_tags(mysql_real_escape_string($_POST['newpcconcept']));
	
	$upd_pc_concept = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_desc='$new_pc_concept' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC concept.");
}
if(isset($new_pc_concept))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Concept Summary: <font color = 'white'>$new_pc_concept<br>
	<hr>
	</td>
	</tr>
	";
}

if(empty($_POST['newpcparents']))
{
	if($curpcnfo[creature_parents] == '')
	{
		echo"
		<tr>
		<td width = '100%'>
		<font color = 'yellow'>
		<li> If the character has no parents, put a short origin description.
		<hr>
		</td>
		</tr>
		";
		
		$emptycount++;
	}
	else
	{
		$new_pc_parents = $curpcnfo[creature_parents];
	}
}
if(isset($_POST['newpcparents']))
{
	$new_pc_parents = strip_tags(mysql_real_escape_string($_POST['newpcparents']));
	
	$upd_pc_parents = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_parents='$new_pc_parents' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC parents.");
}
if(isset($new_pc_parents))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Parents: <font color = 'white'>$new_pc_parents<br>
	<hr>
	</td>
	</tr>
	";
}

if(empty($_POST['newpcsocio']))
{
	if($curpcnfo[creature_socio] == '')
	{
		echo"
		<tr>
		<td width = '100%'>
		<font color = 'yellow'>
		<li> If the character has no applicable social upbringing, explain their socialization.
		<hr>
		</td>
		</tr>
		";
		
		$emptycount++;
	}
	else
	{
		$new_pc_socio = $curpcnfo[creature_socio];
	}
}
if(isset($_POST['newpcsocio']))
{
	$new_pc_socio = strip_tags(mysql_real_escape_string($_POST['newpcsocio']));
	
	$upd_pc_socio = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_socio='$new_pc_socio' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC socio.");
}
if(isset($new_pc_socio))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Social Background: <font color = 'white'>$new_pc_socio<br>
	<hr>
	</td>
	</tr>
	";
}


if(empty($_POST['newpcshaped1']))
{
	if($curpcnfo[creature_shaped1] == '')
	{
		echo"
		<tr>
		<td width = '100%'>
		<font color = 'red'>
		<li> The character must have experienced at least three formative events to be played.
		<hr>
		</td>
		</tr>
		";
		
		$emptycount++;
	}
	else
	{
		$new_pc_shaped1 = $curpcnfo[creature_shaped1];
	}
}
if(isset($_POST['newpcshaped1']))
{
	$new_pc_shaped1 = strip_tags(mysql_real_escape_string($_POST['newpcshaped1']));
	
	$upd_pc_shaped1 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_shaped1='$new_pc_shaped1' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC shaped1.");
}
if(isset($new_pc_shaped1))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Formative Event: <font color = 'white'>$new_pc_shaped1<br>
	<hr>
	</td>
	</tr>
	";
}

if(empty($_POST['newpcshaped2']))
{
	if($curpcnfo[creature_shaped2] == '')
	{
		echo"
		<tr>
		<td width = '100%'>
		<font color = 'red'>
		<li> The character must have experienced at least three formative events to be played.
		<hr>
		</td>
		</tr>
		";
		
		$emptycount++;
	}
	else
	{
		$new_pc_shaped2 = $curpcnfo[creature_shaped2];
	}
}
if(isset($_POST['newpcshaped2']))
{
	$new_pc_shaped2 = strip_tags(mysql_real_escape_string($_POST['newpcshaped2']));
	
	$upd_pc_shaped2 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_shaped2='$new_pc_shaped2' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC shaped2.");
}
if(isset($new_pc_shaped2))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Formative Event: <font color = 'white'>$new_pc_shaped2<br>
	<hr>
	</td>
	</tr>
	";
}

if(empty($_POST['newpcshaped3']))
{
	if($curpcnfo[creature_shaped3] == '')
	{
		echo"
		<tr>
		<td width = '100%'>
		<font color = 'red'>
		<li> The character must have experienced at least three formative events to be played.
		<hr>
		</td>
		</tr>
		";
		
		$emptycount++;
	}
	else
	{
		$new_pc_shaped3 = $curpcnfo[creature_shaped3];
	}
}
if(isset($_POST['newpcshaped3']))
{
	$new_pc_shaped3 = strip_tags(mysql_real_escape_string($_POST['newpcshaped3']));
	
	$upd_pc_shaped3 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_shaped3='$new_pc_shaped3' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC shaped3.");
}
if(isset($new_pc_shaped3))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Formative Event: <font color = 'white'>$new_pc_shaped3<br>
	<hr>
	</td>
	</tr>
	";
}

if(empty($_POST['newpchunted1'])) 
{
	if($curpcnfo[creature_hunted1] == '')
	{
		echo"
		<tr>
		<td width = '100%'>
		<font color = 'red'>
		<li> The character must have at least one flaw.
		<hr>
		</td>
		</tr>
		";
		
		$emptycount++;
	}
	else
	{
		$new_pc_hunted1 = $curpcnfo[creature_hunted1];
	}
}
if(isset($_POST['newpchunted1']))
{
	$new_pc_hunted1 = strip_tags(mysql_real_escape_string($_POST['newpchunted1']));
	
	$upd_pc_hunted1 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_hunted1='$new_pc_hunted1' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC hunted1.");
}
if(isset($new_pc_hunted1))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Hunted/Enemy: <font color = 'white'>$new_pc_hunted1<br>
	<hr>
	</td>
	</tr>
	";
}

if(isset($_POST['newpchunted2']))
{
	$new_pc_hunted2 = strip_tags(mysql_real_escape_string($_POST['newpchunted2']));
	
	$upd_pc_hunted2 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_hunted2='$new_pc_hunted2' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC hunted2.");
}
else
{
	$new_pc_hunted2 = $curpcnfo[creature_hunted2];
}
if(isset($new_pc_hunted2))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Hunted/Enemy: <font color = 'white'>$new_pc_hunted2<br>
	<hr>
	</td>
	</tr>
	";
}

if(isset($_POST['newpchunted3']))
{
	$new_pc_hunted3 = strip_tags(mysql_real_escape_string($_POST['newpchunted3']));
	
	$upd_pc_hunted3 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_hunted3='$new_pc_hunted3' WHERE creature_id='$curpcnfo[creature_id]'") or die("failed to update PC hunted3.");
}
else
{
	$new_pc_hunted3 = $curpcnfo[creature_hunted3];
}
if(isset($new_pc_hunted3))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Hunted/Enemy: <font color = 'white'>$new_pc_hunted3<br>
	<hr>
	</td>
	</tr>
	";
}

if(isset($pcstory))
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = '#7fffd4'>
	Long Story:
	<br>
	<font color = 'white'>$pcstory<br>
	<hr>
	</td>
	</tr>
	";
}


if($emptycount >= 1)
{
	echo"
	<tr>
	<td colspan= '9'>
	<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
	<input type='hidden' value='$curpcnfo[creature_nuke_user_id]' name='player_id'>
	<input type='hidden' value='1' name='char_expander'>
	<input class='submit3' type='submit' value='Back' name='pc_bg'>
	</td>
	</tr>
	";
}

// uncomment to check post variables
// echo"<br>$new_pc_name, $new_pc_true_name, $new_pc_concept, $new_pc_parents, $new_pc_socio, $new_pc_shaped1, $new_pc_shaped2, $new_pc_shaped3, $new_pc_hunted1, $new_pc_hunted2, $new_pc_hunted3, $new_pc_org1, $new_pc_org2, $new_pc_org3, $curpcnfo[creature_id], $curpcnfo[1]<br><br>";

echo"
</form>
";

if($emptycount == 0)
{
	echo"
	<tr>
	<td width = '100%'>
	<font color = 'yellow'>
	<li> All changes to $new_pc_name have been saved.
	<hr class='pipes'>
	</td>
	
	</tr>
	";
	
	if($curpcnfo[creature_status_id] >= 2)
	{
		if($curpcnfo[creature_status_id] <= 3)
		{
			if($curusrslrprnk[slurp_rank_id] >= 5)
			{
				echo"
				<tr>
					<form name='show_hide_story' method='post' action='modules.php?name=$module_name&file=pc_bg_form'>
					<td width = '100%'>
						<font color = 'yellow'>
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='1' name='char_expander'>
				";
				
				if($story_expander == 0)
				{
					echo"
						<input type='hidden' value='1' name='story_expander'>
						<input class='submit3' type='submit' value='Show Story Form' name='pc_bg_form'>
					";
				}
				if($story_expander == 1)
				{
					echo"
						<input type='hidden' value='0' name='story_expander'>
						<input class='submit3' type='submit' value='Hide Story Form' name='pc_bg_form'> &nbsp;&nbsp;&nbsp;<font class='heading1'><font color='orange'>Above entries are displayed to help in creating the story.</font>
						<br>
						<br>
					</td>
					<form>
				</tr>
				<tr>
					<form name='submit_story' method='post' action='modules.php?name=$module_name&file=pc_bg_form'>
					<td width = '100%'>
						<textarea name ='newpcstory' rows = '30' cols = '100'>$pcstory</textarea>
						<br>
						<br>
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='1' name='char_expander'>
						<input class='submit3' type='submit' value='Save Story' name='pc_bg_form'>
					";
				}
				
				echo"
						<hr class='pipes'>
					</td>
					<form>
				</tr>
				<tr>
				";
			}
		}
	}

	if($curusrslrprnk[slurp_rank_id] <= 4)
	{		
		if($story_expander == 0)
		{
			echo"
		<tr>
			<form name='show_hide_story' method='post' action='modules.php?name=$module_name&file=pc_bg_form'>
			<td width = '100%'>
				<font color = 'yellow'>
				<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
				<input type='hidden' value='1' name='char_expander'>
				<input type='hidden' value='1' name='story_expander'>
				<input class='submit3' type='submit' value='Show Story Form' name='pc_bg_form'>
				<hr class='pipes'>
			</td>
			<form>
		</tr>
			";
		}
		if($story_expander == 1)
		{
			echo"
		<tr>
			<form name='show_hide_story' method='post' action='modules.php?name=$module_name&file=pc_bg_form'>
			<td width = '100%'>
				<font color = 'yellow'>
				<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
				<input type='hidden' value='1' name='char_expander'>
				<input type='hidden' value='0' name='story_expander'>
				<input class='submit3' type='submit' value='Hide Story Form' name='pc_bg_form'> &nbsp;&nbsp;&nbsp;<font class='heading1'><font color='orange'>Above entries are displayed to help in creating the story.</font>
				<br>
				<br>
			</td>
			<form>
		</tr>
		<tr>
			<form name='submit_story' method='post' action='modules.php?name=$module_name&file=pc_bg_form'>
			<td width = '100%'>
				<textarea name ='newpcstory' rows = '30' cols = '100'>$pcstory</textarea>
				<br>
				<br>
				<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
				<input type='hidden' value='1' name='char_expander'>
				<input class='submit3' type='submit' value='Save Story' name='pc_bg_form'>
				<hr class='pipes'>
			</td>
			<form>
		</tr>
			";
		}
	}
	
	echo"
	</form>
	<tr>
	";
	
	if($curpcnfo[creature_status_id] != 2)
	{
		echo"
			<form name = 'pc_edit' method = 'post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
		";
	}
	
	if($curpcnfo[creature_status_id] == 2)
	{
		echo"
			<form name = 'pc_edit' method = 'post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
		";
	}	
	
	echo"
	<td width = '100%'>
		<table>
			<tr>
				<td width = '18%'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$curpcnfo[creature_nuke_user_id]' name='player_id'>
					<input type='hidden' value='1' name='char_expander'>
					<input class='submit3' type='submit' value='Continue' name='pc_edit'>
				</td>
				</form>
				<td width = '2%'>
				</td>
				<form name = 'pc_bg' method = 'post' action = 'modules.php?name=$module_name&file=pc_bg'>
				<td width = '18%'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$curpcnfo[creature_nuke_user_id]' name='player_id'>
					<input type='hidden' value='1' name='char_expander'>
					<input class='submit3' type='submit' value='Back' name='pc_bg'>
				</td>
				</form>
			</tr>
		</table>
	</td>
</tr>
	";
}

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>