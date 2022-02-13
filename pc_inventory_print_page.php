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
	
	
	echo"
	<table>
	<tr>
	<td width = '100%' align = 'left' valign = 'top'>
	";
	// end character sheet table (and cell)
	
	if(isset($_POST['verbose_'.$rowcount.$colcount]))
	{
		$verbose = 1;
	}
	if(empty($_POST['verbose_'.$rowcount.$colcount]))
	{
		$verbose = 0;
	}
	
	// echo "$rowcount$colcount<br>";
	$pc_temp = $_POST[$rowcount.$colcount];
	if($pc_temp > 1)
	{
		include("modules/$module_name/includes/pcinfo.php");
	
		echo"
		<font color = 'blue' size = '2'>
		<b>Inventory for $curpcnfo[creature]</b>
		<font size = '1'>as of $today:</font>
		</font>
		<font color = 'red' size = '2'>
		";
	
		$get_pc_material = mysql_query("SELECT * FROM ".$slrp_prefix."material INNER JOIN ".$slrp_prefix."creature_material ON ".$slrp_prefix."creature_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."creature_material.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_material.creature_material_count >= '1' ORDER BY ".$slrp_prefix."material.material") or die ("failed getting pc material.");
		$curpcmatcnt = mysql_num_rows($get_pc_material);
		if($curpcmatcnt >= 1)
		{
			echo"
			<li> <b>MATERIALS</b>
			";
			while($curpcmat = mysql_fetch_assoc($get_pc_material))
			{
				$get_pc_mat_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$curpcmat[material_id]'") or die ("failed to get pc item info.");
				$gtpcmatnfo = mysql_fetch_assoc($get_pc_mat_info);
				
				$get_pc_mat_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."material ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."material.material_default_unit_id WHERE ".$slrp_prefix."material.material_id = '$curpcmat[material_id]'") or die ("failed to get pc mat info.");
				$gtpcmatunit = mysql_fetch_assoc($get_pc_mat_unit);
				
				$get_pc_mat_unit_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."effect_subtype INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = ".$slrp_prefix."effect_subtype.effect_subtype_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_id = '$gtpcmatunit[effect_id]'") or die ("failed to get pc mat unit subtype.");
				$gtpcmatunitsbtyp = mysql_fetch_assoc($get_pc_mat_unit_subtype);
				
				if($gtpcmatnfo[creature_identified] >= 1)
				{
					$mat_instance = mysql_query("SELECT material FROM ".$slrp_prefix."material WHERE material_id = '$curpcmat[material_id]'") or die ("failed getting mat group instance desc.");
				}
				if($gtpcmatnfo[creature_identified] == 0)
				{
					$mat_instance = mysql_query("SELECT material_short_name FROM ".$slrp_prefix."material WHERE material_id = '$curpcmat[material_id]'") or die ("failed getting material group instance short_desc.");
				}					
				
				$matinst = mysql_fetch_array($mat_instance, MYSQL_NUM);
				
				$mat_instance_display_name = stripslashes($matinst[0]);
				
				echo"<br> <font color = 'black' size = '1'><input type='checkbox' value='0' name='$mat_instance_display_name'>";
				
				if($gtpcmatnfo[creature_identified] == 0)
				{
					echo"* ";
				}
				
				echo"<b>($gtpcmatnfo[creature_material_count]";
				
				if($gtpcmatunitsbtyp[effect_subtype_id] == 37)
				{
					echo" ct.";
				}
				if($gtpcmatunitsbtyp[effect_subtype_id] != 37)
				{
					echo" $gtpcmatunit[effect_abbr].";
				}
				
				echo")</b> $mat_instance_display_name</font>
				";
			}		
		}
		
		$get_pc_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed to get pc item info.");
		$gtpcitmnfocnt = mysql_num_rows($get_pc_item_info);
		
		if($gtpcitmnfocnt >= 1)
		{
			echo"
			<li> <b>ITEMS</b>
			";
			
			while($gtpcitmnfo = mysql_fetch_assoc($get_pc_item_info))
			{
				if($gtpcitmnfo[creature_item_count] >= 1)
				{
					$get_pc_item = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtpcitmnfo[item_id]'") or die ("failed getting pc item.");
					$curpcitem = mysql_fetch_assoc($get_pc_item);
					// echo"identified? $gtpcitmnfo[creature_identified]<br>";
					
					if($gtpcitmnfo[creature_identified] >= 1)
					{
						$instance = mysql_query("SELECT item FROM ".$slrp_prefix."item WHERE item_id = '$gtpcitmnfo[item_id]'") or die ("failed getting group instance desc.");
					}
					if($gtpcitmnfo[creature_identified] == 0)
					{
						$instance = mysql_query("SELECT item_short_name FROM ".$slrp_prefix."item WHERE item_id = '$gtpcitmnfo[item_id]'") or die ("failed getting group instance short_desc.");
					}					
					
					$inst = mysql_fetch_array($instance, MYSQL_NUM);
					
					$instance_display_name = stripslashes($inst[0]);
					
					echo"<br> <font color = 'black' size = '1'><input type='checkbox' value='0' name='$instance_display_name'>";
					
					if($gtpcitmnfo[creature_identified] == 0)
					{
						echo"* ";
					}
					
					echo"<b>($gtpcitmnfo[creature_item_count] ct.)</b> $instance_display_name";
					
					if($gtpcitmnfo[creature_item_book_id] >= 2)
					{
						$get_ability_book_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."item_book ON ".$slrp_prefix."item_book.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."item_book.item_book_id = '$gtpcitmnfo[creature_item_book_id]'") or die ("failed to get book item info.");
						$gtabbknfocnt = mysql_num_rows($get_ability_book_info);
						$gtabbknfo = mysql_fetch_assoc($get_ability_book_info);
						
						echo"[$gtabbknfo[ability]]";
					}
					
					echo"
					</font>
					";
				}
			}
		}
		
		$get_pc_recipes = mysql_query("SELECT * FROM ".$slrp_prefix."item INNER JOIN ".$slrp_prefix."creature_item ON ".$slrp_prefix."creature_item.item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."creature_item.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_item.creature_knows_recipe = '1' ORDER BY ".$slrp_prefix."item.item") or die ("failed getting pc item.");
		$curpcrcpcnt = mysql_num_rows($get_pc_recipes);
		if($curpcrcpcnt >= 1)
		{
			echo"
			<li> <b>RECIPES</b>
			<font color = 'black' size = '1'>
			";
			
			while($curpcrcp = mysql_fetch_assoc($get_pc_recipes))
			{
				echo"<br> Recipe for $curpcrcp[item]";
			}
			
			echo"</font>";
		}
	}
	
	echo"
	</table>
	";
	// end character sheet table (and cell)
	
	echo"
	</td>
	";
	
	$listcount++;
	$colcount++;
	
	if($colcount == 3)
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