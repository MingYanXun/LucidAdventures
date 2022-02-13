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

// get abilities list

while($listcount <= 3)
{
	if($colcount == 0)
	{
		echo"<tr>";
		
		$rowcount++;
	}
	
	echo"
	<td width = '33%' align = 'left' valign = 'top'>
	";
	
	// get pc table info
	$get_pc_bg_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id > '1' AND creature_status_id >= '2' AND creature_status_id <= '4' ORDER BY creature_status_id DESC, creature");
	// setting up to divide the list by four for columns
	$curpcbgcnt = mysql_num_rows($get_pc_bg_list);
	while($curpcbg = mysql_fetch_assoc($get_pc_bg_list))
	// $abnms = mysql_fetch_assoc($abnames);
	if(isset($_POST['pc_bg_'.$curpcbg[creature_id]]))
	{
		$pc_temp = $_POST['pc_bg_'.$curpcbg[creature_id]];
		if($pc_temp > 1)
		{
			include("modules/$module_name/includes/pcinfo.php");
			
			$get_pc_race = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype INNER JOIN ".$slrp_prefix."creature_creature_subtype ON ".$slrp_prefix."creature_subtype.creature_subtype_id = ".$slrp_prefix."creature_creature_subtype.creature_subtype_id WHERE ".$slrp_prefix."creature_creature_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting pc race.");
			$gtpcrc = mysql_fetch_assoc($get_pc_race);
			
			// start character sheet
			// this table (below) holds the main content column and the sidebar column; it is the character sheet
			echo"
			<table cellpadding = '3' width = '100%'>
			<tr>
			<td width = '100%'>
			<table width = '100%' align = 'center'>
			";
			// ^above^ start of the main content table before the sidebar of each pane
			
			if($curpcnfo[creature_id] >= 2)
			{
				echo"
				<tr>
				
				<td align = 'center' valign = 'top' width = '100%'>
				<font color = 'black' size = '2'>
				<b>$curpcnfo[creature]</b> ($curpcstts[status]) STONES: ";
				
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
				}

				if($curpcnfo[creature_npc] == 1)
				{
					echo"<font size = '1'>NPC</font>";
				}
							
				echo"
				</td>
				
				</tr>
				<tr>
				
				<td align = 'center' valign = 'top' colspan = '2' width = '100%'>
				<table width = '100%' align = 'center'>
				<tr>";
				
				while($curpcattr = mysql_fetch_assoc($get_pc_attributes))
				{
					$get_attributes_list = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id = '$curpcattr[attribute_type_id]'") or die("failed getting attr_type list");
					$attrlist = mysql_fetch_assoc($get_attributes_list);
					$attr_tier = round(($curpcattr[attribute_type_tier]+(($slrpnfo[slurp_tier_width]-1)/2))/$slrpnfo[slurp_tier_width]);
					
					if($attrlist[attribute_type_id] == 2)
					{
						$attr_print_color = "#4AC948";
						$attr_print_align = "right";
					}
					
					if($attrlist[attribute_type_id] == 3)
					{
						$attr_print_color = "#00B2EE";
						$attr_print_align = "center";
					}
					
					if($attrlist[attribute_type_id] == 4)
					{
						$attr_print_color = "#CC00FF";
						$attr_print_align = "left";
					}
					
					echo"<td valign = 'top' align = '$attr_print_align' width = '32%'><b><font color = '$attr_print_color'>$curpcattr[attribute_type_tier] $attrlist[attribute_type_short] <br><font color = 'black' size = '1'>TIER ".roman($attr_tier)."</font></b><br>";
					
					$attr_score = $curpcattr[creature_attribute_type_value];
					
					$end_of_the_line = 0;
					$bubble_breaker = 0;
					$bubble_breaker_count = 0;
					while($attr_score >= 1)
					{
						if($bubble_breaker == 0)
						{
							$bubble_breaker_count++;
							if($verbose == 1)
							{
								echo"<font size = '1' color = 'black'>".roman($bubble_breaker_count)." </font>";
							}
						}
					}
					
					echo"</b></font></td><td width = '2%'>";
				}
				
				echo"
				</tr>
				</table>
				</td>
				</tr>
				<tr>
				";
				
				// split between left and right panes of the main content				
				echo"<table width = '100%' align = 'center'>
				<tr>
				<td width = '23%' valign = 'top' align = 'left'>
				<font size = '2' color = '#993300'><i><b>
				";
				
				$get_pc_effect_support = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type INNER JOIN ".$slrp_prefix."creature_effect_type ON ".$slrp_prefix."creature_effect_type.effect_type_id = ".$slrp_prefix."effect_type.effect_type_id WHERE ".$slrp_prefix."creature_effect_type.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."effect_type.effect_type_id > '1' AND ".$slrp_prefix."effect_type.effect_type_support = '1' ORDER BY ".$slrp_prefix."effect_type.effect_type") or die ("failed getting support eff types.");
				$gtpceffsuppcnt = mysql_num_rows($get_pc_effect_support);
				// echo"$gtpceffsuppcnt<br>";
				while($gtpceffsupp = mysql_fetch_assoc($get_pc_effect_support))
				{
					$get_effect_support_core_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_effect_type WHERE creature_id = '$curpcnfo[creature_id]' AND effect_type_id = '$gtpceffsupp[effect_type_id]'");
					$gteffsuppcrnfo = mysql_fetch_assoc($get_effect_support_core_info);
					echo"$gtpceffsupp[effect_type] ".roman($gteffsuppcrnfo[effect_type_tier])."<br>";
				}	
				
				$get_pc_effect_types = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type INNER JOIN ".$slrp_prefix."creature_effect_type ON ".$slrp_prefix."creature_effect_type.effect_type_id = ".$slrp_prefix."effect_type.effect_type_id WHERE ".$slrp_prefix."creature_effect_type.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."effect_type.effect_type_id > '1' AND ".$slrp_prefix."effect_type.effect_type_support = '0' ORDER BY ".$slrp_prefix."effect_type.effect_type") or die ("failed getting all eff types.");
				$gtpcefftypscnt = mysql_num_rows($get_pc_effect_types);
				while($gtpcefftyps = mysql_fetch_assoc($get_pc_effect_types))
				{
					if($gtpcefftypscnt >= 1)
					{
						$get_effect_type_core_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_effect_type WHERE creature_id = '$curpcnfo[creature_id]' AND effect_type_id = '$gtpcefftyps[effect_type_id]'");
						$gtefftypcrnfo = mysql_fetch_assoc($get_effect_type_core_info);
						
						if($gtpcefftyps[effect_type_support] == 0)
						{
							$effect_type_color = "#993300";
						}
						if($gtpcefftyps[effect_type_support] == 1)
						{
							$effect_type_color = "black";
						}
	
						echo"<u><font color = '$effect_type_color' size = '3'><b><i>$gtpcefftyps[effect_type] ".roman($gtefftypcrnfo[effect_type_tier])."</b></i></u><br></font>";
					}
				}
				
				echo"
				</td>
				</table>
				
				</td>
				";
				
				// split between left and right panes of the main content
				echo"			
				</td>
				</tr>
				";
			}
			
			// end of the main content table; start the right sidebar if there's a record		
			echo"
			</table>
			
			</td>
			";		
		}
		
		echo"
		</tr>
		</table>
		";
		// end character sheet table (and cell)
	}
	
	echo"
	</td>
	";
}


// include("modules/$module_name/includes/slurp_footer.php");
// require("footer.php");
?>