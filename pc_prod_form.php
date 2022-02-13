<?php
if (!eregi("modules.php", $PHP_SELF)) {
  die ("You can't access this file directly...");
}
$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("header.php");
$nav_title = "View Character";
include("modules/$module_name/includes/slurp_header.php");

if(isset($_POST['race_desc_expander']))
{
	$race_desc_expander = $_POST['race_desc_expander'];
}
else
{
	$race_desc_expander = 1;
}

if(isset($_POST['ntro_expander']))
{
	$ntro_expander = $_POST['ntro_expander'];
}
else
{
	$ntro_expander = 1;
}

if(isset($_POST['component_expander']))
{
	$component_expander = $_POST['component_expander'];
}
else
{
	$component_expander = 1;
}

if(isset($_POST['items_expander']))
{
	$items_expander = $_POST['items_expander'];
}
else
{
	$items_expander = 1;
}

if(isset($_POST['materials_expander']))
{
	$materials_expander = $_POST['materials_expander'];
}
else
{
	$materials_expander = 1;
}

if(isset($_POST['recipe_expander']))
{
	$recipe_expander = $_POST['recipe_expander'];
}
else
{
	$recipe_expander = 0;
}	

if(isset($_POST['created_item']))
{
	$created_item = $_POST['created_item'];
	$create_count = $_POST['create_count'];
	$create_count_original = $create_count;
	
	// echo"$created_item, cnt: $create_count<br>";
	
	$created_item_info = mysql_query("SELECT * FROM dom_item WHERE item_id = '$created_item'") or die ("failed getting created item info.");
	$critmnfo = mysql_fetch_array($created_item_info, MYSQL_NUM);
	
	$created_item_name = stripslashes(strip_tags($critmnfo[1]));
	
	$created_item_materials = mysql_query("SELECT * FROM dom_material INNER JOIN dom_item_core_material ON dom_item_core_material.material_id = dom_material.material_id WHERE dom_item_core_material.item_id = '$created_item'") or die ("failed getting created item materials.");
	while($critmmats = mysql_fetch_array($created_item_materials, MYSQL_NUM))
	{
		$create_cost = ($critmmats[3]*$create_count_original);
		
		$verify_expenditure = mysql_query("SELECT * FROM dom_pc_material WHERE pc_id = '$curpcnfo[0]' AND material_id = '$critmmats[0]'") or die ("failed verifying spent materials.");
		$vrexp = mysql_fetch_array($verify_expenditure, MYSQL_NUM);
		
		if($vrexp[3] >= $create_cost)
		{
			$create_count--;
		}
	}
	
	if($create_count <= 0)
	{
		$enough_created_item_materials = mysql_query("SELECT * FROM dom_item_core_material WHERE dom_item_core_material.item_id = '$created_item'") or die ("failed getting created item materials.");
		$reason = "";
		while($enghcritmmats = mysql_fetch_array($enough_created_item_materials, MYSQL_NUM))
		{
			$recipe_create_cost = ($enghcritmmats[3]*$create_count_original);
			// echo"$recipe_create_cost = $enghcritmmats[3] * $create_count_original<br>";
			
			$subtract_materials_from_pc = mysql_query("UPDATE dom_pc_material SET pc_material_count=(pc_material_count-'$recipe_create_cost') WHERE material_id = '$enghcritmmats[2]' AND pc_id = '$curpcnfo[0]'") or die ("failed updating created material count.");
			$used_materials = mysql_query("SELECT * FROM dom_material WHERE material_id = '$enghcritmmats[2]'") or die ("failed getting used materials.");
			$usdmat = mysql_fetch_array($used_materials, MYSQL_NUM);
			
			$reason = $reason."-$recipe_create_cost $usdmat[1]. ";
		}
		
		echo"
		<tr>
		<td colspan = '3' align = 'left' valign = 'top'>
		<font color = 'yellow' size = '2'>
		<li> <i>$curpcnfo[1]</i> created $create_count_original $created_item_name(s)</i>.
		<hr>
		</font>
		</td>
		</tr>
		";
		
		$update_pc_items = mysql_query("UPDATE dom_pc_item SET pc_item_count=(pc_item_count+'$create_count_original') WHERE item_id = '$created_item' AND pc_id = '$curpcnfo[0]'") or die ("failed updating created item count.");
		$xp_change = 0;
		$reason = $reason."Created $create_count_original $created_item_name.";
		
		$record_xp_log = mysql_query("INSERT INTO dom_pc_xp_log (pc_id,xp_value,user_id,reason) VALUES ('$curpcnfo[0]','$xp_change','$usrnfo[0]','$reason')") or die ("failed adding character submission to xp log.");
	}
	
	if($create_count >= 1)
	{
		echo"
		<tr>
		<td colspan = '3' align = 'left' valign = 'top'>
		<font color = 'red' size = '2'>
		<li> <i>$curpcnfo[1]</i> failed to create $create_count_original $created_item_name(s)</i>.
		<hr>
		</font>
		</td>
		</tr>
		";
	}
	
	$clean_up_leftovers = mysql_query("DELETE FROM dom_pc_material WHERE pc_material_count = '0'") or die ("failed deleting material stragglers.");
}

echo"
<tr>
<td>
<form name = 'back_to_pc_edit' method='post' action = 'modules.php?name=$module_name&file=pc_edit'>
<input type='hidden' value='$curpcnfo[0]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
<input type='submit' value='Back to View/Edit' name='back_to_pc_edit'>
</form>
</td>

<td width = '2%'>
</td>

<td>
<form name = 'back_to_pc_prod' method='post' action = 'modules.php?name=$module_name&file=pc_prod'>
<input type='hidden' value='$curpcnfo[0]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
<input type='submit' value='Back to Production' name='back_to_pc_prod'>
</form>
</td>

</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
?>