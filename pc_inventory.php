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
	
	$abcnt = 0;
	// $abnms = mysql_fetch_assoc($abnames);
	
	$pc_temp = $_POST['current_pc_id'];
	// echo "$rowcount, $colcount<br>$pc_temp<br>";
	if($pc_temp > 1)
	{
		include("modules/$module_name/includes/pcinfo.php");
	
		echo"
		<font color = 'red' size = '2'>
		<li> <b>MATERIALS</b>
		</font>
		<font color = 'black'>
		";
	
		$get_pc_material = mysql_query("SELECT * FROM ".$slrp_prefix."material INNER JOIN ".$slrp_prefix."creature_material ON ".$slrp_prefix."creature_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."creature_material.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_material.creature_material_count >= '1' ORDER BY ".$slrp_prefix."material.material") or die ("failed getting pc material.");
		$curpcmatcnt = mysql_num_rows($get_pc_material);
		if($curpcmatcnt >= 1)
		{
			while($curpcmat = mysql_fetch_assoc($get_pc_material))
			{
				$get_pc_mat_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$curpcmat[material_id]'") or die ("failed to get pc item info.");
				$gtpcmatnfo = mysql_fetch_assoc($get_pc_mat_info);
				
				$get_pc_mat_unit = mysql_query("SELECT * FROM ".$slrp_prefix."unit INNER JOIN ".$slrp_prefix."material ON ".$slrp_prefix."unit.unit_id = ".$slrp_prefix."material.material_default_unit_id WHERE ".$slrp_prefix."material.material_id = '$curpcmat[material_id]'") or die ("failed to get pc mat info.");
				$gtpcmatunit = mysql_fetch_assoc($get_pc_mat_unit);
				
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
				
				echo"<br> <input type='checkbox' value='0' name='$mat_instance_display_name'>";
				
				if($gtpcmatnfo[creature_identified] == 0)
				{
					echo"* ";
				}
				
				echo"$mat_instance_display_name <b>($gtpcmatnfo[creature_material_count] ct.)</b></font>
				";
			}		
		}
		
		echo"
		<font color = 'red' size = '2'>
		<li> <b>ITEMS</b>
		</font>
		<font color = 'black'>
		";
		
		$get_pc_item = mysql_query("SELECT * FROM ".$slrp_prefix."item INNER JOIN ".$slrp_prefix."creature_item ON ".$slrp_prefix."creature_item.item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."creature_item.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_item.creature_item_count >= '1' ORDER BY ".$slrp_prefix."item.item") or die ("failed getting pc item.");
		$curpcitemcnt = mysql_num_rows($get_pc_item);
		if($curpcitemcnt >= 1)
		{
			while($curpcitem = mysql_fetch_assoc($get_pc_item))
			{
				$get_pc_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]' AND item_id = '$curpcitem[item_id]'") or die ("failed to get pc item info.");
				$gtpcitmnfo = mysql_fetch_assoc($get_pc_item_info);
				// echo"identified? $gtpcitmnfo[creature_identified]<br>";
				
				if($gtpcitmnfo[creature_identified] >= 1)
				{
					$instance = mysql_query("SELECT item FROM ".$slrp_prefix."item WHERE item_id = '$curpcitem[item_id]'") or die ("failed getting group instance desc.");
				}
				if($gtpcitmnfo[creature_identified] == 0)
				{
					$instance = mysql_query("SELECT item_short_name FROM ".$slrp_prefix."item WHERE item_id = '$curpcitem[item_id]'") or die ("failed getting group instance short_desc.");
				}					
				
				$inst = mysql_fetch_array($instance, MYSQL_NUM);
				
				$instance_display_name = stripslashes($inst[0]);
				
				echo"<br> <input type='checkbox' value='0' name='$instance_display_name'>";
				
				if($gtpcitmnfo[creature_identified] == 0)
				{
					echo"* ";
				}
				
				echo"$instance_display_name <b>($gtpcitmnfo[creature_item_count] ct.)</b></font>
				";
			}
		}
		
		$get_pc_recipes = mysql_query("SELECT * FROM ".$slrp_prefix."item INNER JOIN ".$slrp_prefix."creature_item ON ".$slrp_prefix."creature_item.item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."creature_item.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_item.creature_knows_recipe = '1' ORDER BY ".$slrp_prefix."item.item") or die ("failed getting pc item.");
		$curpcrcpcnt = mysql_num_rows($get_pc_recipes);
		if($curpcrcpcnt >= 1)
		{
			echo"
			<font color = 'red' size = '2'>
			<li> <b>RECIPES</b>
			<font color = 'black'>
			";

			
			while($curpcrcp = mysql_fetch_assoc($get_pc_recipes))
			{
				echo"<br> Recipe for $curpcrcp[item]";
			}
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