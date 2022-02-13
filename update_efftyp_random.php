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
$nav_title = "STEP 2 - ATTRIBUTES";
include("modules/$module_name/includes/slurp_header.php");

$random_update_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1' ORDER BY ability");
while($rndupdab = mysql_fetch_assoc($random_update_abilities))
{

	$rand_ab_id = $rndupdab[ability_id];
	//include("modules/$module_name/includes/fn_obj_rand.php");
	
	$get_effect_type_ties = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type INNER JOIN ".$slrp_prefix."effect_type ON ".$slrp_prefix."effect_type.effect_type_id = ".$slrp_prefix."ability_effect_type.effect_type_id WHERE ".$slrp_prefix."ability_effect_type.ability_id = '$rand_ab_id' AND ".$slrp_prefix."effect_type.effect_type_support = '0' ORDER BY ".$slrp_prefix."effect_type.effect_type");
	$getefftyptiescnt = mysql_num_rows($get_effect_type_ties);
	$abnfocnt = $getefftyptiescnt;
	$rndstrsum = "";
	
	while($getefftypties = mysql_fetch_assoc($get_effect_type_ties))
	{		
		// set character count to determine how difficult the seed
		$character_count = 2;
		$rndtxtsum  = "";
		while($character_count >= 1)
		{
			$rndtxt = chr(rand(255,999));
			$rndtxtsum = $rndtxtsum.$rndtxt;
			$character_count--;
			// echo"$rndtxt, $rndtxtsum<br>";
		}
		// echo"<br>Start: $rndtxtsum<br>";
		$string1 = $rndtxtsum;
		
		if($getefftypties[effect_type_tier] >= 1)
		{
			$string1 = urlsafe_b64encode($string1);
			$lastrnd = $string1;
			 echo"1: $string1, ";
			
			if($getefftypties[effect_type_tier] >= 2)
			{
				$string2 = urlsafe_b64encode($string1);
				$lastrnd = $string1;
				$string1 = $string2;
				 echo"2: $string1, ";
				
				if($getefftypties[effect_type_tier] >= 3)
				{
					$string3 = urlsafe_b64encode($string2);
					$lastrnd = $string2;
					$string1 = $string3;
					 echo"3: $string1, ";
					
					if($getefftypties[effect_type_tier] >= 4)
					{
						$string4 = urlsafe_b64encode($string3);
						$lastrnd = $string3;
						$string1 = $string4;
						 echo"4: $string1, ";
						
						if($getefftypties[effect_type_tier] >= 5)
						{
							$string5 = urlsafe_b64encode($string4);
							$lastrnd = $string4;
							$string1 = $string5;
							 echo"5: $string1, ";
						}
					}
				}
			}
		}	
		
		// to catch the last one and end the row
		$abnfocnt--;
		
		//combine the text strings
		$rndstrsum = $rndstrsum.$string1;
	}
	
	// echo"Sum: $rndstrsum<br>";
	$random_update = mysql_query("UPDATE ".$slrp_prefix."object_random SET object_random_current='0' WHERE object_focus_id = '2' AND object_id = '$rndupdab[ability_id]' AND object_slurp_id = '$slrpnfo[slurp_id]' AND object_random_current = '1'");
	$random_tracker = mysql_query("INSERT INTO ".$slrp_prefix."object_random (object_id,object_random,object_focus_id,object_slurp_id) VALUES ('$rndupdab[ability_id]','$rndstrsum','2','$slrpnfo[slurp_id]')");
	
	// $get_new_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$rndupdab[ability_id]' AND object_random = '$rndstrsum' AND object_focus_id = '2' AND object_slurp_id = '$slrpnfo[slurp_id]'") or die ("failed getting new random.");
	$get_new_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$rndupdab[ability_id]' AND object_focus_id = '2' AND object_slurp_id = '$slrpnfo[slurp_id]' AND object_random_current = '1'") or die ("failed getting new random.");
	$gtnwrnd = mysql_fetch_assoc($get_new_random);
	
//	$character_ability_random_repair = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$rndupdab[ability_id]' AND ability_random_id = '0'");
//	while($chrabrndrep = mysql_fetch_assoc($character_ability_random_repair))
//	{
//		echo"$rndupdab[ability]<br>obj_rnd_id: $gtnwrnd[object_random_id]: $gtnwrnd[object_focus_id] ($gtnwrnd[object_random])<br>";
//		echo" ab_rnd_id: $chrabrndrep[ability_random_id] <br>";
//	
//	//	$character_ability_random_repair = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET ability_random_id = '$gtnwrnd[object_random_id]' WHERE ability_id = '$chrabrndrep[ability_id]' AND ability_random_id = '0'");
//	}
}

?> 