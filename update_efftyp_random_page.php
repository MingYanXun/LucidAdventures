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
$nav_title = "Random Codes";
include("modules/$module_name/includes/slurp_header.php");

// from the contents of update_efftyp_random.php
$random_update_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1' ORDER BY ability") or die ("failed getting abilities fo random update.");
while($rndupdab = mysql_fetch_assoc($random_update_abilities))
{
	$rand_ab_id = $rndupdab[ability_id];
	//include("modules/$module_name/includes/fn_obj_rand.php");
	// echo"<font color='orange'>$rndupdab[ability]</font><br> ";
	$get_max_effect_tier = mysql_query("SELECT MAX(".$slrp_prefix."ability_effect_type.effect_type_tier) FROM ".$slrp_prefix."ability_effect_type WHERE ".$slrp_prefix."ability_effect_type.ability_id = '$rand_ab_id'") or die ("failed getting max effect tier.");
	$gtmxefftr = mysql_fetch_array($get_max_effect_tier, MYSQL_NUM);
	
	// set character count to determine how difficult the seed
	$character_count = $gtmxefftr[0];
	// dont' let it fall below 2 to ensure key strength
	if($character_count < 2)
	{
		$character_count = 2;
	}
	$rndstrsum  = "";
	while($character_count >= 1)
	{
		$rndtxt = chr(rand(255,999));
		$rndstrsum = $rndstrsum.$rndtxt;

		// echo"$rndtxt, $rndtxtsum<br>";
		// echo"<br>Start: $rndtxtsum<br>";
		
		$rndstrsum = urlsafe_b64encode($rndstrsum);
		// echo"<font color='white'>$character_count: $rndtxtsum</font><br>";

		if($character_count == 1)
		{
			// on the last pass, set the ability tier
			$update_ability_tier = mysql_query("UPDATE ".$slrp_prefix."ability SET ability_tier = '$gtmxefftr[0]' WHERE ability_id = '$rand_ab_id'") or die ("failed updating ab tier.");
		}
		
		// decrement the counter
		$character_count--;
	}
	
	//combine the text strings
	$rndstrsum = $rndstrsum.$string1;
	
	// verify duplicates and re-run the random generator
	// from the contents of update_efftyp_random.php
	$check_dupe_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random = '$rndstrsum' AND object_focus_id = '2' AND object_id != '$rndupdab[ability_id]' AND object_slurp_id = '$slrpnfo[slurp_id]' ORDER BY object_random_timestamp DESC") or die ("failed checking new random for dupes.");
	$chkduprndcnt = mysql_num_rows($check_dupe_random);
	if($chkduprndcnt >= 1)
	{
	 	// echo"<font coolor='red'>[DUPE] $rndstrsum . . . rerunning.</font><br>";
		$rndstrsum = urlsafe_b64encode($rndstrsum);
		echo"<font color='red'>[RERUN1] $rndstrsum</font><br>";
		$check_dupe_random2 = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random = '$rndstrsum' AND object_focus_id = '2' AND object_id != '$rndupdab[ability_id]' AND object_slurp_id = '$slrpnfo[slurp_id]' ORDER BY object_random_timestamp DESC") or die ("failed checking new random for dupes.");
		$chkduprnd2cnt = mysql_num_rows($check_dupe_random2);
		if($chkduprnd2cnt >= 1)
		{
			$rndstrsum = urlsafe_b64encode($rndstrsum);
			echo"<font color='red'>[RERUN2] $rndstrsum</font><br>";
			$check_dupe_random3 = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random = '$rndstrsum' AND object_focus_id = '2' AND object_id != '$rndupdab[ability_id]' AND object_slurp_id = '$slrpnfo[slurp_id]' ORDER BY object_random_timestamp DESC") or die ("failed checking new random for dupes.");
			$chkduprnd3cnt = mysql_num_rows($check_dupe_random3);
			if($chkduprnd3cnt >= 1)
			{
				$rndstrsum = urlsafe_b64encode($rndstrsum);
				echo"<font color='red'>[RERUN3] $rndstrsum</font><br>";
				$check_dupe_random4 = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random = '$rndstrsum' AND object_focus_id = '2' AND object_id != '$rndupdab[ability_id]' AND object_slurp_id = '$slrpnfo[slurp_id]' ORDER BY object_random_timestamp DESC") or die ("failed checking new random for dupes.");
				$chkduprnd4cnt = mysql_num_rows($check_dupe_random4);
				if($chkduprnd4cnt >= 1)
				{
					$rndstrsum = urlsafe_b64encode($rndstrsum);
				echo"<font color='red'>[RERUN4] $rndstrsum</font><br>";
				}
			}
		}
	}
	
	// echo"$rndupdab[ability]: $rndstrsum<br>";

	$random_update = mysql_query("UPDATE ".$slrp_prefix."object_random SET object_random_current='0' WHERE object_focus_id = '2' AND object_id = '$rndupdab[ability_id]' AND object_slurp_id = '$slrpnfo[slurp_id]' AND object_random_current = '1'");
	$random_tracker = mysql_query("INSERT INTO ".$slrp_prefix."object_random (object_id,object_random,object_random_current,object_focus_id,object_slurp_id) VALUES ('$rndupdab[ability_id]','$rndstrsum','1','2','$slrpnfo[slurp_id]')");
	
	// $get_new_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$rndupdab[ability_id]' AND object_random = '$rndstrsum' AND object_focus_id = '2' AND object_slurp_id = '$slrpnfo[slurp_id]'") or die ("failed getting new random.");
	$get_new_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_id = '$rndupdab[ability_id]' AND object_focus_id = '2' AND object_slurp_id = '$slrpnfo[slurp_id]' AND object_random_current = '1'") or die ("failed getting new random.");
	$gtnwrnd = mysql_fetch_assoc($get_new_random);
	 echo"$rndupdab[ability]: $gtnwrnd[object_random]<br>";
	$update_pc_ab_random = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET ability_random_id = '$gtnwrnd[object_random_id]' WHERE ability_id = '$rndupdab[ability_id]'") or die ("failed correcting PC ab random links");
	

	// end contents of update_efftyp_random.php	
	
	// FOR REPAIRS ONLY; KEEP COMMENTED OTHERWISE>
	//update the pc ability list with the most current random; 
	// $creature_ability_random_update = mysql_query("UPDATE ".$slrp_prefix."creature_ability SET ability_random_id='$gtnwrnd[object_random_id]' WHERE ability_id = '$rndupdab[ability_id]'") or die ("failed updating pc ability random.");
	//$check_creature_ability_random_update = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE ability_id = '$rndupdab[ability_id]'") or die ("failed updating pc ability random.");
	//while($chkcrabrndupd = mysql_fetch_assoc($check_creature_ability_random_update))
	//{
	//	$get_abrnd_creature = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id = '$chkcrabrndupd[creature_id]'") or die ("failed getting abrand creature info.");
	//	$gtabrndcr = mysql_fetch_assoc($get_abrnd_creature);
	//	echo"<li> $gtabrndcr[creature]";
//	}
		
	$check_book_ability_random_update = mysql_query("SELECT * FROM ".$slrp_prefix."item_book WHERE ability_id = '$rndupdab[ability_id]'") or die ("failed updating pc ability random.");
	while($chkbkabrndupd = mysql_fetch_assoc($check_book_ability_random_update))
	{
		$book_random_update = mysql_query("UPDATE ".$slrp_prefix."item_book SET object_random_id='$gtnwrnd[object_random_id]' WHERE ability_id = '$rndupdab[ability_id]'") or die ("failed updating book ability random.");
		$get_abrnd_book = mysql_query("SELECT * FROM ".$slrp_prefix."item_book WHERE ability_id = '$rndupdab[ability_id]'") or die ("failed getting abrand book info.");
		$gtabrndbk = mysql_fetch_assoc($get_abrnd_book);
		echo"<font color='purple'><li> book: $gtabrndbk[object_random_id]</font><br>";
	}
}

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>