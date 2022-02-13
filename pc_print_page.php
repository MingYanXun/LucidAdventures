<?php
if (!eregi("modules.php", $PHP_SELF))
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("modules/$module_name/includes/slurp_min_header.php");

$listcount = 1;
$rowcount = 0;
$colcount = 0;

echo"
<tr>
	<td width = '100%' align = 'left' valign = 'top'>
";

if(isset($_POST['pc_to_print']))
{
	$pc_temp = $_POST['pc_to_print'];
}
if(isset($_GET['pc_to_print']))
{
	$pc_temp = $_GET['pc_to_print'];
}

if(isset($_POST['verbose']))
{
	$verbose = 1;
}
if(empty($_POST['verbose']))
{
	$verbose = 0;
}
if(isset($_GET['verbose']))
{
	$verbose = 1;
}

if($pc_temp > 1)
{
	include("modules/$module_name/includes/pcinfo.php");
	
	// start character sheet
	// this table (below) holds all the main content on top
	echo"
	<table cellpadding = '3' width = '100%' border='1' cellspacing='0'>
		<tr>
	";
	
	// this is the left pane first column
	$colcount++;
	echo"
			<td width = '33%' align='left' valign='top'>
				<table width = '100%' align = 'center' cellpadding = '0' border='0' cellspacing='0'>
	";
	
	// left third
	// split between three panes of this first third to make a header
	echo"
					<tr>
						<td width = '100%' align='left' valign='top'>
							<table width = '100%' cellpadding = '0' cellspacing='0' border='0'>
								<tr>
									<td align = 'left' valign = 'top' width = '20%'>
										<font color = 'black' size = '2'>
										Level $character_level
										<br>
										$curpcnfo[creature_xp_earned] Build Earned
										</font>
									</td>
									<form>
									<td align = 'center' valign = 'top' width = '60%'>
										<font color = 'black' size = '3'>
										<input type='button' onClick='window.print()' value = '$curpcnfo[creature]'>
										</form>
										<br>
	";
		
	$stone_count = 0;
	if($curpcnfo[creature_tokens_3] >= 1)
	{
		$black_stones = $curpcnfo[creature_tokens_3];
		while($black_stones >= 1)
		{
			echo"<img src = '../../images/black_stone.png' height = '10' width = '10'>";
			$black_stones--;
			$stone_count++;
			if($stone_count == 5)
			{
				echo".";
				$stone_count = 0;
			}
		}
		$red_stones = $curpcnfo[creature_tokens_2];
		while($red_stones >= 1)
		{
			echo"<img src = '../../images/red_stone.png' height = '10' width = '10'>";
			$red_stones--;
			$stone_count++;
			if($stone_count == 5)
			{
				echo".";
				$stone_count = 0;
			}
		}
		$blue_stones = $curpcnfo[creature_tokens_4];
		while($blue_stones >= 1)
		{
			echo"<img src = '../../images/blue_stone.png' height = '10' width = '10'>";
			$blue_stones--;
			$stone_count++;
			if($stone_count == 5)
			{
				echo".";
				$stone_count = 0;
			}
		}
		$white_stones = $curpcnfo[creature_tokens_1];
		while($white_stones >= 1)
		{
			echo"<img src = '../../images/white_stone.png' height = '10' width = '10'>";
			$white_stones--;
			$stone_count++;
			if($stone_count == 5)
			{
				echo".";
				$stone_count = 0;
			}
		}
		
		echo"			<br>";
	}
	
	if($curpcnfo[creature_npc] == 0)
	{
		echo"			<font size = '1'>cbn _____ pd ______ eat ______ wk ______ </font>";
	}
				
	echo"
									<td align = 'right' valign = 'top' width = '20%'>
										<font color = 'black' size = '2'>$total_hit_points HP
										<br>
										$total_recoveries Recoveries/day
										</font>
									</td>
								</tr>
							</table>
						</td>
					</tr>
	";
			
	// lines of racial abilities
	$ab_set_id = 12;
	include("modules/$module_name/includes/fn_pc_abset_print.php");
	
	// lines of racial pool abilities
	$ab_set_id = 18;
	include("modules/$module_name/includes/fn_pc_abset_print.php");
	
	// lines of advantages
	$ab_set_id = 15;
	include("modules/$module_name/includes/fn_pc_abset_print.php");
	
	echo"
				</table>
			</td>
	";
	// end first column
	
	$get_pc_domains = mysql_query("SELECT DISTINCT ability_set_id FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id = '13' OR ".$slrp_prefix."ability.ability_set_id = '17' OR (".$slrp_prefix."ability.ability_set_id >= '5' AND ".$slrp_prefix."ability.ability_set_id <= '9')") or die ("failed getting domain list");
	$pcdmnscnt = mysql_num_rows($get_pc_domains);
	if($pcdmnscnt >= 1)
	{
		$colcount++;
		echo"
		<td width='33%' align='left' valign='top'>
			<table cellpadding = '3' width = '100%' border='0' cellspacing='0'>
		";
	
		while($pcdmns = mysql_fetch_array($get_pc_domains, MYSQL_NUM))
		{
			// lines of domain abilities
			$ab_set_id = $pcdmns[0];
			include("modules/$module_name/includes/fn_pc_abset_print.php");
		}
		
		// lines of advanced arts abilities
		$ab_set_id = 14;
		include("modules/$module_name/includes/fn_pc_abset_print.php");
		
		echo"
			</table>
		</td>
		";			
	}
	
	$get_pc_constants = mysql_query("SELECT DISTINCT ability_set_id FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id = '2' OR ".$slrp_prefix."ability.ability_set_id = '11' ORDER BY ".$slrp_prefix."ability.ability_set_id") or die ("failed getting domain list");
	$pccnstntcnt = mysql_num_rows($get_pc_constants);
	if($pccnstntcnt >= 1)
	{
		if($colcount == 0)
		{
			echo"<tr>";
		}
		$colcount++;
		
		echo"
		<td width='33%' align='left' valign='top'>
			<table cellpadding = '3' width = '100%' border='0' cellspacing='0'>
		";
	
		while($pccnstnt = mysql_fetch_array($get_pc_constants, MYSQL_NUM))
		{
			// lines of constant abilities
			$ab_set_id = $pccnstnt[0];
			include("modules/$module_name/includes/fn_pc_abset_print.php");
		}
		
		echo"
			</table>
		</td>
		";
	
		if($colcount == 3)	
		{
			echo"</tr>";
			$colcount = 0;
		}
	}
}

$get_pc_pools = mysql_query("SELECT DISTINCT ability_set_id FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id = '3' OR ".$slrp_prefix."ability.ability_set_id = '4' OR ".$slrp_prefix."ability.ability_set_id = '10'") or die ("failed getting pool list");
$pcpoolscnt = mysql_num_rows($get_pc_pools);

while($pcpools = mysql_fetch_array($get_pc_pools, MYSQL_NUM))
{
	if($colcount == 0)
	{
		echo"<tr>";
	}
	
	$colcount++;
		
	echo"
	<td width='33%' align='left' valign='top'>
		<table cellpadding = '3' width = '100%' border='0' cellspacing='0'>
	";

	// lines of pool abilities
		$ab_set_id = $pcpools[0];
	include("modules/$module_name/includes/fn_pc_abset_print.php");
	
	echo"
		</table>
	</td>
	";
	
	if($colcount == 3)	
	{
		echo"</tr>";
		$colcount = 0;
	}
}

echo"	</form></td></tr>";
// include("modules/$module_name/includes/slurp_footer.php");
// require("footer.php");
?>