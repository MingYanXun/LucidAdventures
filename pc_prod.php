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
$nav_title = "Manage Production";
$nav_page = "pc_prod";
include("modules/$module_name/includes/slurp_header.php");
include("modules/$module_name/includes/fn_game_nfo.php");

// checkbox variables for the index
$expander_abbr = $_POST['current_expander'];
$expander = $expander_abbr."_expander";
//echo"exp: $expander_abbr, $expander<br>";
// see how many attribute points they have

$attribute_total = mysql_query("SELECT SUM(creature_attribute_type_value) FROM ".$slrp_prefix."creature_attribute_type WHERE creature_id = '$curpcnfo[creature_id]'") or die("failed getting attr_type total");
$attr_total = mysql_fetch_array($attribute_total, MYSQL_NUM);

$attr_pts_left = ($slrpnfo[slurp_starting_attribute_points] - $attr_total[0]);

$abwt_total = 0;
$get_pc_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]'");
while($curpcabs = mysql_fetch_assoc($get_pc_abs))
{
	$ability_weight = mysql_query("SELECT SUM(".$slrp_prefix."ability_effect_type.effect_type_tier) FROM ".$slrp_prefix."ability_effect_type INNER JOIN ".$slrp_prefix."effect_type ON ".$slrp_prefix."effect_type.effect_type_id = ".$slrp_prefix."ability_effect_type.effect_type_id WHERE ".$slrp_prefix."ability_effect_type.ability_id = '$curpcabs[ability_id]' AND ".$slrp_prefix."effect_type.effect_type_support = '0'") or die ("failed getting ability weight.");
	$abwt = mysql_fetch_array($ability_weight, MYSQL_NUM);
	$abwt_total = ($abwt_total + $abwt[0]);
	
	// echo"Ab subTotal: (+$abwt[0] = ) $abwt_total<br>";
}


// echo"Prod Points: $prod_points<br>";
// adding materials manually
if(isset($_POST['mat_list']))
{
	$mat_list = $_POST['mat_list'];
	$pc_mat_count = $_POST['pc_mat_count'];
	// $pc_mat_unit = $_POST['pc_mat_unit'];
	
	if($mat_list >= 2)
	{
		$ex_pc_mat_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."material ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."material.material_default_unit_id WHERE ".$slrp_prefix."material.material_id = '$mat_list'") or die ("failed to get pc mat unit info.");
		$expcmatunit = mysql_fetch_assoc($ex_pc_mat_unit);
		// $active_count_size = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$pc_mat_count'") or die ("failed getting current mtl count nfo.");
		// $actcntsz = mysql_fetch_assoc($active_count_size);
		
		$verify_existing_ownership = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$mat_list' AND creature_material_unit_id = '$expcmatunit[effect_id]' ORDER BY material_id") or die ("failed getting owned materials.");
		$vrexownshp = mysql_fetch_assoc($verify_existing_ownership);
		$vrexownshpcnt = mysql_num_rows($verify_existing_ownership);
		
		if(isset($_POST['id_pc_mat']))
		{
			$id_pc_mat = 1;
		}
		if(empty($_POST['id_pc_mat']))
		{
			if($vrexownshpcnt >= 1)
			{
				$id_pc_mat = $vrexownshp[creature_identified];
			}
			if($vrexownshpcnt == 0)
			{
				$id_pc_mat = 0;
			}
		}
		
		if($vrexownshpcnt == 0)
		{
			$active_count_size_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$pc_mat_count'") or die ("failed getting current harvest count nfo.");
			$actcntsznfo = mysql_fetch_assoc($active_count_size_info);
			$new_pc_material = mysql_query("INSERT INTO ".$slrp_prefix."creature_material (creature_id,material_id,creature_material_count_id,creature_material_count,creature_material_unit_id,creature_identified) VALUES ('$curpcnfo[creature_id]','$mat_list','$actcntsznfo[effect_id]','$actcntsznfo[effect_abbr]','$expcmatunit[effect_id]','$id_pc_mat')") or die ("failed inserting new pc material.");
		}
		if($vrexownshpcnt >= 1)
		{
			$active_count_size_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$pc_mat_count'") or die ("failed getting owned current count nfo.");
			$actcntsznfo = mysql_fetch_assoc($active_count_size_info);
			// echo"abbr: $actcntsznfo[effect_abbr]<br>";
			
			$pc_mat_count_size = $vrexownshp[creature_material_count] + $actcntsznfo[effect_abbr];
			// echo "$pc_mat_count_size = $vrexownshp[creature_material_count] + $actcntsznfo[effect_abbr]<br>";
			
			$update_pc_material = mysql_query("UPDATE ".$slrp_prefix."creature_material SET creature_material_count = '$pc_mat_count_size',creature_identified = '$id_pc_mat' WHERE creature_material_id = '$vrexownshp[creature_material_id]' AND creature_material_unit_id = '$expcmatunit[effect_id]'") or die ("failed updating materials count.");
		}
	}
}

// adding, subtracting, and deleting (at 0) materials from a PC
$del_mat_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_material_id > '1'");
while($delmatlst = mysql_fetch_assoc($del_mat_list))
{
	$del_mat_cnt = 0;
	if(isset($_POST['del_pc_mat_'.$delmatlst[creature_material_id]]))
	{
		$del_mat_cnt++;
		$del_mat = $_POST['del_pc_mat_'.$delmatlst[creature_material_id]];
		// echo"del mtl: $del_mat<br>";
		$delete_pc_material = mysql_query("DELETE FROM ".$slrp_prefix."creature_material WHERE creature_material_id = '$del_mat'") or die ("failed deleting pc material.");
	}
	
	if(isset($_POST['add_pc_mat']))
	{
		$add_mat_id = $_POST['add_pc_mat_'.$delmatlst[creature_material_id]];
		$add_pc_mat = $_POST['add_pc_mat'];
		$add_pc_material_count = mysql_query("UPDATE ".$slrp_prefix."creature_material SET creature_material_count = creature_material_count+'$add_pc_mat' WHERE creature_material_id = '$add_mat_id'") or die ("failed adding materials count.");
	}
	
	if(isset($_POST['sub_pc_mat']))
	{
		$sub_mat_id = $_POST['sub_pc_mat_'.$delmatlst[creature_material_id]];
		$sub_pc_mat = $_POST['sub_pc_mat'];
		$sub_pc_material_count = mysql_query("UPDATE ".$slrp_prefix."creature_material SET creature_material_count = creature_material_count+'$sub_pc_mat' WHERE creature_material_id = '$sub_mat_id'") or die ("failed subtractng materials count.");
		//delete records at zero not known as recipes
		$delete_empty_pc_materials = mysql_query("DELETE FROM ".$slrp_prefix."creature_material WHERE creature_material_count = '0' AND creature_identified = '0' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed deleting pc material.");
	}
}

// adding, subtracting, and deleting (at 0) items from a PC
$del_item_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]' AND item_id > '1'");
while($delitemlst = mysql_fetch_assoc($del_item_list))
{
	if(isset($_POST['del_pc_itm_'.$delitemlst[creature_item_id]]))
	{
		$delpcitm = $_POST['del_pc_itm_'.$delitemlst[creature_item_id]];
		// echo"CR_ITM_ID: $delpcitm<br>";
		// changed to an update to preserve the exposure of a crafter to a recipe that can be understood, and thus learned.
		$delete_pc_item = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_item_count = '0', creature_item_count_id = '1' WHERE creature_item_id = '$delpcitm'") or die ("failed deleting pc item.");
		$delete_empty_pc_items = mysql_query("DELETE FROM ".$slrp_prefix."creature_item WHERE creature_item_count <= '0' AND creature_identified = '0' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed deleting pc item.");
	}
	
	if(isset($_POST['add_pc_itm']))
	{
		// echo"adding itm count $add_pc_itm";
		$add_itm_id = $_POST['add_pc_itm_'.$delitemlst[creature_item_id]];
		$add_pc_itm = $_POST['add_pc_itm'];
		$add_pc_item_count = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_item_count = creature_item_count+'$add_pc_itm' WHERE creature_item_id = '$add_itm_id'") or die ("failed adding items count.");
	}
	
	if(isset($_POST['sub_pc_itm']))
	{
	// echo"subtracting itm count $sub_pc_itm";
		$sub_itm_id = $_POST['sub_pc_itm_'.$delitemlst[creature_item_id]];
		$sub_pc_itm = $_POST['sub_pc_itm'];
		$sub_pc_item_count = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_item_count = creature_item_count+'$sub_pc_itm' WHERE creature_item_id = '$sub_itm_id'") or die ("failed subtractng items count.");
		//delete records at zero not known as recipes
		$delete_empty_pc_items = mysql_query("DELETE FROM ".$slrp_prefix."creature_item WHERE creature_item_count <= '0' AND creature_identified = '0' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed deleting pc item.");
	}
}

// delete recipe from PC (forget), and if the PC has no actual items, delete the entry
$del_recipe_list = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id > '1'");
while($delrcplst = mysql_fetch_assoc($del_recipe_list))
{
	if(isset($_POST['del_pc_rcp_'.$delrcplst[item_id]]))
	{
		$delpcrcp = $_POST['del_pc_rcp_'.$delrcplst[item_id]];
		// changed to an update to preserve the exposure of a crafter to a recipe that can be understood, and thus learned.
		$delete_pc_recipe = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_knows_recipe = '0' WHERE creature_id = '$curpcnfo[creature_id]' AND item_id = '$delpcrcp'") or die ("failed deleting pc item.");
		$delete_empty_pc_items = mysql_query("DELETE FROM ".$slrp_prefix."creature_item WHERE creature_item_count <= '0' AND creature_identified = '0' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed deleting pc item for recipes.");
	}
}

// items whose recipes did not transfer upon being added will need deleting since they are only 0 count records now
$clean_up_dropped_items = mysql_query("DELETE FROM ".$slrp_prefix."creature_item WHERE creature_item_count = '0' AND creature_knows_recipe = '0'") or die ("failed cleaning up dropped items");

// manually adding items to a PC, including the book flag
if(isset($_POST['item_listing']))
{
	if($item_listing >= 2)
	{
		// set the book ID to default (none) of 1
		$item_book = 1;
		$item_listing = $_POST['item_listing'];
		
		include("modules/$module_name/includes/fn_itm_book.php");
	  // echo"item_book: $item_book<br>";
	  if(isset($_POST['pc_item_count']))
		{
			$pc_item_count = $_POST['pc_item_count'];
					
			$get_item_count_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$pc_item_count'") or die ("failed getting item count info.");
			$gtitmcntnfo = mysql_fetch_assoc($get_item_count_info);
			$pc_item_count = $gtitmcntnfo[effect_abbr];
			// echo "count: $gtitmcntnfo[effect], $gtitmcntnfo[effect_abbr]<br>";
		}
		if(empty($_POST['pc_item_count']))
		{
			$pc_item_count = 0;
		}
		
		if(isset($_POST['pc_item_quality']))
		{
			$pc_item_quality = $_POST['pc_item_quality'];
		}
		// echo"QTY id: $pc_item_quality COUNT id: $pc_item_count<br>";

		$verify_items_owned = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]' AND item_id = '$item_listing' AND creature_item_quality_id = '$pc_item_quality' AND creature_item_book_id = '$item_book'") or die ("failed getting owned items.");
		$vritmownd = mysql_fetch_assoc($verify_items_owned);
		$vritmowndcnt = mysql_num_rows($verify_items_owned);
		
		// if PC qualifies, insert or update as needed
		if($vritmowndcnt == 0)
		{
			// echo"<font color='purple'>insert count ($gtitmcntnfo[effect_id] $gtitmcntnfo[effect]): $gtitmcntnfo[effect_abbr], QTY: $pc_item_quality</font><br>";
			// echo"inserting item_book $item_book<br>";
			$add_pc_item = mysql_query("INSERT INTO ".$slrp_prefix."creature_item (creature_id,item_id,creature_item_count_id,creature_item_count,creature_item_quality_id,creature_item_book_id) VALUES ('$curpcnfo[creature_id]','$item_listing','$gtitmcntnfo[effect_id]','$gtitmcntnfo[effect_abbr]','$pc_item_quality','$item_book')") or die ("failed inserting new pc item.");
		}
		
		// if pc has it. just update the count
		if($vritmowndcnt >= 1)
		{
			$owned_count = $vritmownd[creature_item_count] + $pc_item_count;
			// echo"$critmscnt";

			$get_count_subs = mysql_query("SELECT * FROM dom_effect INNER JOIN dom_effect_effect_subtype ON dom_effect.effect_id = dom_effect_effect_subtype.effect_id WHERE dom_effect_effect_subtype.effect_subtype_id = '37' AND dom_effect.effect_abbr >= '$vritmownd[creature_item_count]' AND dom_effect.effect_min_value <= '$vritmownd[creature_item_count]' ORDER BY dom_effect.effect") or die ("fauled getting effects count.");
			$gtcntsbs = mysql_fetch_assoc($get_count_subs);
			// echo"$critms[creature_item_id] $critms[creature_item_count] ($gtcntsbs[effect_id]): Tier ".roman($gtcntsbs[effect_tier]).", $gtcntsbs[effect]<br>";
			// echo"<font color='purple'>update count ($gtitmcntnfo[effect_id] $gtitmcntnfo[effect]): $gtitmcntnfo[effect_abbr], QTY: $vritmownd[creature_item_quality_id]</font><br>";
			
			$update_pc_item = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_item_count_id = '$gtcntsbs[effect_id]', creature_item_count='$owned_count' WHERE creature_item_id = '$vritmownd[creature_item_id]'") or die ("failed updating item count.");
		}
		
		$verify_items_owned2 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]' AND item_id = '$item_listing' AND creature_item_quality_id = '$pc_item_quality' AND creature_item_book_id = '$item_book'") or die ("failed getting owned items 2.");
		$vritmownd2 = mysql_fetch_assoc($verify_items_owned2);
		$vritmownd2cnt = mysql_num_rows($verify_items_owned2);
		
		$item_listing = $vritmownd2[item_id];

		// include prod_id fucntions?
		if(isset($_POST['id_pc_item']))
		{
			// echo"ID'd $vritmownd[item_id]<br>";
			$id_pc_item = 1;
			$update_pc_item_identified = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_identified = '1' WHERE creature_id = '$vritmownd[creature_id]' AND item_id = '$vritmownd[item_id]'") or die ("failed updating item recipe, identified status.");
			include("modules/$module_name/includes/fn_prod_id.php");
		}
		if(empty($_POST['id_pc_item']))
		{
			if($vritmownd2cnt >= 1)
			{
				$id_pc_item = $vritmownd2[creature_identified];
			}
			if($vritmownd2cnt == 0)
			{
				$id_pc_item = 0;
			}
		}
	}
}

// get leftover orphans and get rid of them
$collect_unfinished_batches = mysql_query("SELECT * FROM ".$slrp_prefix."creature_batch WHERE batch_status = '2' AND creature_id = '$curpcnfo[creature_id]'") or die ("Failed collecting unfinished batches.");
while($cllctunfbtch = mysql_fetch_assoc($collect_unfinished_batches))
{
	$delete_unfinished_batches_ingredients = mysql_query("DELETE FROM ".$slrp_prefix."creature_batch_ingredients WHERE creature_batch_id = '$cllctunfbtch[creature_batch_id]'") or die ("failed cleaning out batch ingredients.");
	$delete_unfinished_batches = mysql_query("DELETE FROM ".$slrp_prefix."creature_batch WHERE creature_batch_id = '$cllctunfbtch[creature_batch_id]'") or die ("Failed deleting unfinished batches.");
}


echo"
<tr>
	<td valign = 'top' align = 'left'  colspan = '5'>
		<table width = '100%'>
			<tr>
				<form name = 'show_hide_instructions' method='post' action = 'modules.php?name=$module_name&file=pc_prod'>
				<td valign = 'top' align = 'right'>
";

if($ntro_expander == 1)
{
	echo"
					<input type='hidden' value='$admin_expander' name = 'admin_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='0' name = 'ntro_expander'>
					<input type='submit' value='Hide Instructions' name='show_hide_instructions'>
	";
}

if($ntro_expander == 0)
{
	echo"
					<input type='hidden' value='$admin_expander' name = 'admin_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='1' name = 'ntro_expander'>
					<input type='submit' value='Show Instructions' name='show_hide_instructions'>
	";
}

echo"			</form>
					<br><br>
					<form name = 'pc_edit' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$admin_expander' name = 'admin_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='submit' value='Back to $curpcnfo[creature]' name='to_pc_edit'>
					</font>
				</td>
				</form>
				<td width = '2%'>
				</td>
				<form name = 'mat_list' method='post' action = 'modules.php?name=$module_name&file=obj_list'>
				<td valign = 'top' align = 'left'>
					<font size = '2'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='material' name='group_name'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='submit' value='Materials List' name='to_mat_list'>
					</form>
					<br>
					<br>
					<form name = 'item_list' method='post' action = 'modules.php?name=$module_name&file=obj_list'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='item' name='group_name'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='submit' value='Items List' name='to_item_list'>
				</td>
				</form>

				<td valign = 'top' align = 'left'>
					<table>
";

$pcid = $curpcnfo[creature_id];
$boxed = 1;
$prp_hidden = 0;
include("modules/$module_name/includes/fn_prod_pts.php");

$prod_pts = $prod_points;

// Echo"P3: $prod_pts<br>";

echo"			</table>
				</td>
				<td valign = 'top' align = 'left'>
				
				</td>
			</tr>
		</table>
	</td>
	
</tr>
<tr>
	
	<td valign = 'top' align = 'left' colspan = '5'>
";

if($ntro_expander == 1)
{
	echo"
		<table width = '100%'>
			<tr>
				<td valign = 'top' align = 'center' colspan = '5'>
					<hr width = '100%'>
					<font size = '2' color = 'yellow'>
					<b>USES FOR PROD POINTS</b> [(4x Production)+SIZ+TIM]<br>
					<font size = '1'>And other Production Modifiers</font>
					</font>
					<hr width = '100%'>
				</td>
			</tr>
			<tr>
				<td valign = 'top' align = 'left' colspan = '3'>
					<font size = '2' color = '#7fffd4'>
					<br><b> Free Trade Harvesting: <font color = 'white'>(by Terrain)</b>
					<li> Tier I and II Free Trade Items only
					<li> +1 Prod Point per Material Tier (+1 TC) 
					<li> Subtract 1 Tier from Batch Time (-1 TC)
					<li> +1 Tier (Double) Batch Size (+1 TC)</font>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left'>
					<font size = '2' color = '#7fffd4'>
					<br><b> Harvesting: <font color = 'white'>(non-Free Trade Items, by Terrain)</b>
					<li> Up to Harvester's Production Tier
					<li> +1 Prod Point per Material Tier (+1 TC)
					<li> +1 Tier (Double) Batch Size (+1 TC)
					<li> Harvesting Tools (-1 TC)</font>
				</td>
			</tr>
			<tr>
				<td valign = 'top' align = 'left' colspan = '5'>
					<font size = '2' color = '#7fffd4'>
					<br><b> Crafting <font color = 'white'>Default is Crude (QTY I) using same-Tier Materials as the Crafter Prod Tier</b>
					<li> +1 Item Durability (DRB) above the default (up to Crafter Craft Tier) (+1 TC)
					<li> +1 Item Capacity above the default (up to Crafter Craft Tier) (+1 TC)
					<li> +1 Item Quality above the default (affects Durability) (costs 2 Prod Pts.) (up to Crafter Craft Tier) (+1 TC)
					<li> Double Batch Count (up to Crafter SIZ) (+1 TC)
					<li> Lower Batch Time (up to Crafter TIM) (-1 TC)
					<li> Crafting Tools (by Craft) (-1 TC)
					<li> Lab/Workshop (-(Tier of Facility) TC)
					<li> Repair Only (-2 TC; 0 Prod Points)
					<li> Adjust Only (-3 TC; 0 Prod Points)</font>							
					</font></b>
				</td>
			</tr>
		</table>
	";
}

echo"
	</td>
</tr>
<tr>
	<td colspan = '9'>
		<hr>
	</td>
</tr>
<tr>
	<form name = 'show_hide_harvest' method='post' action = 'modules.php?name=$module_name&file=pc_prod'>
	<td valign = 'top' align = 'left' width = '100%'>
		<table width = '100%'>
			<tr>
				<td valign = 'top' align = 'left' width = '33%'>
					<font color = 'yellow' size = '2'>
					&nbsp;&nbsp;&nbsp;<b>Harvest</b>
					</font>
";

if($harvest_expander == 1)
{
	echo"
					<br>
					<font color = 'orange' size = '1'>A Default Batch will harvest 1 default unit of Material
	in 24 hours +1 Time Chart Tier for each Size Harvested.</font>
	";
}

echo"
				</td>				
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left'>
";

if($harvest_expander == 0)
{
	echo"
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='1' name = 'harvest_expander'>
					<input type='submit' value='Show Harvest' name = 'show_hide_harvest'>
				</td>
				</form>
				<td width = '2%'> 
				</td>
				<td> 
				</td>
			</tr>
		</table>
	</td>
	</tr>
	";
}
if($harvest_expander == 1)
{
	echo"
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='0' name = 'harvest_expander'>
					<input type='submit' value='Hide Harvest' name = 'show_hide_harvest'>
				</td>
				</form>
				<td width = '2%'>
				</td>
			</tr>
		</table>
	</td>
	<tr>
	<tr>
	<td valign = 'top' align = 'left'>
		<table width = '100%'>
			<tr>
				<td>
	";
	
	// if the db is locked
	if($ngame[event_database_lock_date] <= $now)
	{
		echo"			
							<font color = '#CC00FF'>$ngame[event_database_lock_date] <= $now; Database Locked.</font>
		";
	}
	// if the db is unlocked
	if($ngame[event_database_lock_date] > $now)
	{
		if($prod_points_left >= 1)
		{
			// get all the regions associated with single location markets (i.e, harvtesing feeds that market)
			$harvest_regions = mysql_query("SELECT * FROM ".$slrp_prefix."geography_region INNER JOIN ".$slrp_prefix."geography ON ".$slrp_prefix."geography.geography_id = ".$slrp_prefix."geography_region.geography_id WHERE ".$slrp_prefix."geography.geography_market = '1' AND ".$slrp_prefix."geography.geography_id = '$curpcnfo[creature_market]'") or die ("failed getting creature harvest regions.");
			$hrvregcnt = mysql_num_rows($harvest_regions);
			while($hrvreg = mysql_fetch_assoc($harvest_regions))
			{			
				// echo"$hrvreg[geography_id], $hrvreg[region_id]<br>";
				// get region info for display
				$get_harvest_region_info = mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype WHERE geography_subtype_id = '$hrvreg[region_id]' ORDER BY geography_subtype") or die ("failed getting current harvest region(s) info.");
				$gethrvregnfocnt = mysql_num_rows($get_harvest_region_info);
				$gethrvregnfo = mysql_fetch_assoc($get_harvest_region_info);
				
				echo"
						<form name = 'harvest_prod_form' method='post' action = 'modules.php?name=$module_name&file=pc_prod_batch'>
						<hr>
						<font color = 'white' size = '1'>
						<b><u>Region: $gethrvregnfo[geography_subtype]
				";
				
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo" ($gethrvregnfo[geography_subtype_id])";
				}
				
				// get terrain info for display
				$get_harvest_geotype_info = mysql_query("SELECT * FROM ".$slrp_prefix."geography_type INNER JOIN ".$slrp_prefix."geography_subtype_geography_type ON ".$slrp_prefix."geography_subtype_geography_type.geography_type_id = ".$slrp_prefix."geography_type.geography_type_id WHERE ".$slrp_prefix."geography_subtype_geography_type.geography_subtype_id = '$hrvreg[region_id]' ORDER BY ".$slrp_prefix."geography_type.geography_type") or die ("failed getting current harvest region(s) info.");
				$gethrvgtnfocnt = mysql_num_rows($get_harvest_geotype_info);
				$gethrvgtnfo = mysql_fetch_assoc($get_harvest_geotype_info);
				
				echo" [ $gethrvgtnfo[geography_type] ]</b></u>";
				
				// the first one is free trade materials only, so joins with geo subtype material to get the freq = '99' entries
				// $ability_set_harvest = mysql_query("SELECT MAX(ability_tier) FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id = '11'")or die ("failed getting ability sets.");
				// $absethrv = mysql_fetch_array($ability_set_harvest, MYSQL_NUM);
				//  $absethrvcnt = mysql_num_rows($ability_set_harvest);
				
				$effect_type_prod = mysql_query("SELECT * FROM ".$slrp_prefix."creature_effect_type WHERE creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_effect_type.effect_type_id = '7'")or die ("failed getting production for pc.");
				$efftypprd = mysql_fetch_assoc($effect_type_prod);
				$efftypprdcnt = mysql_num_rows($effect_type_prod);
				
				if($efftypprdcnt == 0)
				{
					$pc_hrv_tier = 0;
				}
				if($efftypprdcnt >= 1)
				{
					$pc_hrv_tier = $efftypprd[effect_type_tier];
				}
				// echo"hrv_tier: $efftypprd[effect_type_tier]<br>";
				
				// no harvesting skill, so free trade materials only
				if($pc_hrv_tier == 0)
				{
					$get_gt_harvest_mtl_info = mysql_query("SELECT * FROM ".$slrp_prefix."material INNER JOIN ".$slrp_prefix."geography_type_material ON ".$slrp_prefix."geography_type_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."geography_type_material.rarity_id = '686' AND ".$slrp_prefix."geography_type_material.geography_type_id = '$gethrvgtnfo[geography_type_id]' ORDER BY ".$slrp_prefix."material.material_tier, ".$slrp_prefix."material.material") or die ("failed getting current free trade only material info.");
				}
				// shows harvesting skill; this one limits the available harvest tiers by the Harvesting skill tier.
				if($pc_hrv_tier >= 1)
				{
					$get_gt_harvest_mtl_info = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id > '1' AND material_tier <= '$pc_hrv_tier' ORDER BY material_tier, material") or die ("failed getting current skilled harvest material info.");
				}
				
				$getgthrvmtlnfocnt = mysql_num_rows($get_gt_harvest_mtl_info);
				if($getgthrvmtlnfocnt == 0)
				{
					echo"
						<br>
						<font color = 'red' size = '2'><b>There are no free trade materials in $gethrvregnfo[geography_subtype].</b></font>
					";
				}
				if($getgthrvmtlnfocnt >= 1)
				{
					echo"
						<br>
						<br>
						<select class='engine' name = 'create_batch'>
					";
					
					while($getgthrvmtlnfo = mysql_fetch_assoc($get_gt_harvest_mtl_info))
					{
						// get materials and frequency by region
						$get_geotype_harvest_info = mysql_query("SELECT * FROM ".$slrp_prefix."geography_type_material INNER JOIN ".$slrp_prefix."geography_subtype_geography_type ON ".$slrp_prefix."geography_subtype_geography_type.geography_type_id = ".$slrp_prefix."geography_type_material.geography_type_id WHERE ".$slrp_prefix."geography_subtype_geography_type.geography_subtype_id = '$gethrvregnfo[geography_subtype_id]' AND ".$slrp_prefix."geography_type_material.material_id = '$getgthrvmtlnfo[material_id]' ORDER BY ".$slrp_prefix."geography_type_material.rarity_id") or die ("failed getting current harvest region(s) info.");
						$getgthrvnfocnt = mysql_num_rows($get_geotype_harvest_info);
						while($getgthrvnfo = mysql_fetch_assoc($get_geotype_harvest_info))
						{
							$avail_hrv_size_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."material ON ".$slrp_prefix."material.material_default_unit_id = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."material.material_id = '$getgthrvmtlnfo[material_id]'") or die ("failed getting current harvest size nfo.");
							$avlhrvsznfo = mysql_fetch_assoc($avail_hrv_size_info);
							$avail_hrv_freq_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$getgthrvnfo[rarity_id]'") or die ("failed getting current frequency nfo.");
							$avlhrvfrqnfo = mysql_fetch_assoc($avail_hrv_freq_info);
							// get material info for display and units
							// $get_gt_harvest_mtl_info = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id = '$getgthrvnfo[material_id]'") or die ("failed getting current harvest material info.");
							// $getgthrvmtlnfo = mysql_fetch_assoc($get_gt_harvest_mtl_info);
							if($getgthrvmtlnfo[material_tier] <= $prod_points_left)
							{
								echo"
								<option value='$getgthrvmtlnfo[material_id]'>($avlhrvfrqnfo[effect_abbr]/$getgthrvmtlnfo[material_tier] PrP) $getgthrvmtlnfo[material] ($avlhrvsznfo[effect_abbr])</option>
								";
							}
						}
					}
					
					echo"
						</select>
						<br>
						<br>
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='$expander_abbr' name='current_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='hidden' value='$admin_expander' name = 'admin_expander'>
						<input type='hidden' value='$component_expander' name = 'component_expander'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$materials_expander' name = 'materials_expander'>
						<input type='hidden' value='$items_expander' name = 'items_expander'>
						<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
						<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
						<input type='hidden' value='$gethrvregnfo[geography_subtype_id]' name = 'batch_geo_subtype'>
						<input type='hidden' value='16' name='create_limit'>
						<input type='hidden' value='mtl' name='create_type'>
						<input type='submit' value='Harvest from $gethrvregnfo[geography_subtype]' name = 'harvest_prod_$gethrvgtnfo[geography_subtype_id]'>
						</form>
					";	
				}
			}
			echo"
						</font>
			";
		}
		
		if($prod_points_left <= 0)
		{
			echo"<br><br><font color = 'red' size = '2'><li><b> You have no Prod Points left.</b></font>";
		}
	}
	echo"
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left'>
	";
	
	$get_active_hrv_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_batch WHERE creature_id = '$curpcnfo[creature_id]' AND material_id >= '2' AND item_id = '1' AND (batch_event_id = '$pgame[event_id]' OR batch_event_id = '$slrpnfo[slurp_next_game_id]') ORDER BY batch_time_start") or die ("failed getting active batch PrP.");
	$gtactvhrvlstcnt = mysql_num_rows($get_active_hrv_list);
	if($gtactvhrvlstcnt >= 1)
	{
		while($gtactvhrvlst = mysql_fetch_assoc($get_active_hrv_list))
		{
			// echo"$gtactvhrvlst[batch_size], $gtactvhrvlst[batch_count], $gtactvhrvlst[batch_quality_id], $gtactvhrvlst[batch_prp]<br>";
			$active_hrv_mtl_info = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id = '$gtactvhrvlst[material_id]'") or die ("failed getting current batch material info.");
			$acthrvnfo = mysql_fetch_assoc($active_hrv_mtl_info);
			
			$active_hrv_geosub_info = mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype WHERE geography_subtype_id = '$gtactvhrvlst[batch_geography_subtype_id]'") or die ("failed getting current batch geography_subtype info.");
			$acthrvgeosbnfo = mysql_fetch_assoc($active_hrv_geosub_info);
			
			$active_hrv_count_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_abbr >= '$gtactvhrvlst[batch_count_value]' AND effect_min_value <= '$gtactvhrvlst[batch_count_value]' AND effect LIKE '%Entit%'  AND effect_abbr != '0'") or die ("failed getting current harvest count nfo.");
			$acthrvcntnfo = mysql_fetch_assoc($active_hrv_count_info);
			
			$active_hrv_unit_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$acthrvnfo[material_default_unit_id]'") or die ("failed getting current harvest size nfo.");
			$acthrvunitnfo = mysql_fetch_assoc($active_hrv_unit_info);
			$active_hrv_size_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."effect_subtype INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect_subtype.effect_subtype_id = ".$slrp_prefix."effect_effect_subtype.effect_subtype_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_id = '$acthrvunitnfo[effect_id]'") or die ("failed getting current harvest size subtype nfo.");
			$acthrvszsbtyp = mysql_fetch_assoc($active_hrv_size_subtype);
			
			$mtl_count_from_hrv = $gtactvhrvlst[batch_count_value];
			
			// echo"item_add_Count: $item_count_from_batch<br>";
			
			$active_hrv_qty_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtactvhrvlst[batch_quality_id]'") or die ("failed getting current batch quality nfo.");
			$acthrvqtynfo = mysql_fetch_assoc($active_hrv_qty_info);
			$improved_active_hrv_quality_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtactvhrvlst[batch_quality_id]'") or die ("failed getting improved recipe quality.");
			$imprvdacthrvqtynfo = mysql_fetch_assoc($improved_active_hrv_quality_info);
			
			$active_hrv_status_info = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_status_id = '$gtactvhrvlst[batch_status]'") or die ("failed getting current batch status nfo.");
			$acthrvstatnfo = mysql_fetch_assoc($active_hrv_status_info);
			
			// $active_hrv_time_info = mysql_query("SELECT * FROM ".$slrp_prefix."time_chart WHERE time_chart_id = '$gtactvhrvlst[batch_time]'") or die ("failed getting current batch time nfo.");
			// $acthrvtmnfo = mysql_fetch_assoc($active_hrv_time_info);
			// $improved_active_hrv_time_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtactvhrvlst[batch_time]'") or die ("failed getting improved recipe time.");
			// $imprvdacthrvtmnfo = mysql_fetch_assoc($improved_active_hrv_time_info);
			
			$start_hrv_name = stripslashes(strip_tags($acthrvnfo[material]));
			$hrv_time_end_vis = $gtactvhrvlst[batch_time_end];
			// $batch_time_start_vis = date_diff($gtactvhrvlst[batch_time_start]);
			$saved_hrv_time_ending = date_create($hrv_time_end_vis);
			$saved_hrv_end_vis = date_format($saved_hrv_time_ending, '(Y) D, j M g:i:s A');
			$now_date = date_create($now);
			$now_hrv_vis = date_format($now_date, 'Y-m-d H:i:s');
			// echo"$now_hrv_vis, $hrv_time_end_vis<br>";

			// not ordered this downtime
			if($gtactvhrvlst[batch_event_id] != $slrpnfo[slurp_next_game_id])
			{
				// if the time has not elapsed, show active batches.
				if($hrv_time_end_vis >= $now)
				{
					echo"<font size = '2' color = 'orange'><li><b>[Previous] $gtactvhrvlst[batch_prp] PrP</b> used for <font color = 'yellow'>$mtl_count_from_hrv ";
					if($acthrvszsbtyp[effect_subtype_id] != '37')
					{
						echo"($acthrvunitnfo[effect_abbr]) of";
					}
					echo" $acthrvnfo[material]</font>; Due: <font color = '#33F406'>$saved_hrv_end_vis</font></font><br>";
				}
				// if the time has elapsed...
				if($hrv_time_end_vis < $now)
				{			
					if($gtactvhrvlst[batch_status] >= 60)
					{
						if($gtactvhrvlst[batch_status] <= 69)
						{
							// Check if the material is even available for harvest
							$up_for_harvest = mysql_query("SELECT * FROM ".$slrp_prefix."material_harvest WHERE material_id = '$gtactvhrvlst[material_id]' AND geography_subtype_id = '$gtactvhrvlst[batch_geography_subtype_id]'") or die ("failed checking available harvest.");
							$upforhrvcnt = mysql_num_rows($up_for_harvest);
							$upforhrv = mysql_fetch_assoc($up_for_harvest);

							if($upforhrvcnt == 0)
							{
								echo"<font size = '2' color = 'red'><li><b>You were unable to find any $acthrvnfo[material] in $acthrvgeosbnfo[geography_subtype].</font></b>";
								
								echo"<font size = '2' color = 'green'><li><b>You retain the Production cost";
								if($gtactvhrvlst[batch_prp] >= 1)
								{
									echo" ($gtactvhrvlst[batch_prp] PrP)";
								}
								
								echo".</font></b><br>";
							}
		
							if($upforhrvcnt >= 1)
							{
								echo"<font size = '2' color = '#33F406'><li><b>($gtnwvhrvlst[batch_prp] PrP) A batch of $mtl_count_from_hrv ";
								
								if($acthrvszsbtyp[effect_subtype_id] != '37')
								{
									echo"($acthrvunitnfo[effect_abbr]) of ";
								}
								
								echo"$acthrvnfo[material] was completed $saved_hrv_end_vis.</b></font><br>";
							}
						}
					}
					
					echo"<font size = '2' color = '$33F406'><li><b>[Previous] $gtactvhrvlst[batch_prp] PrP</b> used for <font color = 'white'>$mtl_count_from_hrv ";
					if($acthrvszsbtyp[effect_subtype_id] != '37')
					{
						echo"($acthrvunitnfo[effect_abbr]) of";
					}
					echo" $acthrvnfo[material]</font>; Due: <font color = 'white'>$saved_hrv_end_vis</font></font><br>";
				}	
			}
			
			// if ordered this downtime, display normally:
			if($gtactvhrvlst[batch_event_id] == $slrpnfo[slurp_next_game_id])
			{
				// if the time has not elapsed, show active batches.
				if($hrv_time_end_vis >= $now)
				{					
					echo"<font size = '2' color = 'orange'><li><b><font color = '#33F406'>$gtactvhrvlst[batch_prp] PrP</font></b> used for <font color = 'yellow'>$gtactvhrvlst[batch_count_value] ";
					if($acthrvszsbtyp[effect_subtype_id] != '37')
					{
						echo"($acthrvunitnfo[effect_abbr]) of";
					}
					echo" $acthrvnfo[material]</font>; Due: <font color = '#33F406'>$saved_hrv_end_vis</font></font><br>";
				}
			
				// completion.
				if($hrv_time_end_vis < $now)
				{
					$get_new_hrv_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_batch WHERE creature_batch_id = '$gtactvhrvlst[creature_batch_id]'") or die ("failed getting new batch info.");
					$gtnwvhrvlst = mysql_fetch_assoc($get_new_hrv_list);
					$new_hrv_count_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtnwvhrvlst[batch_count]'") or die ("failed getting new harvest count nfo.");
					$nwhrvcntnfo = mysql_fetch_assoc($new_hrv_count_info);
					// report current successful attempts
					if($gtactvhrvlst[batch_status] >= 70)
					{
						if($gtactvhrvlst[batch_status] <= 79)
						{			
							echo"<font size = '2' color = '#33F406'><li><b>($gtnwvhrvlst[batch_prp] PrP) A batch of $mtl_count_from_hrv ";
							
							if($acthrvszsbtyp[effect_subtype_id] != '37')
							{
								echo"($acthrvunitnfo[effect_abbr]) of ";
							}
							
							echo"$acthrvnfo[material] was completed $saved_hrv_end_vis.</b></font><br>";
						}
					}
				// report past successful attempts
					if($gtactvhrvlst[batch_status] >= 50)
					{
						if($gtactvhrvlst[batch_status] <= 59)
						{			
							echo"<font size = '2' color = 'white'><li><b>[Previous] ($gtnwvhrvlst[batch_prp] PrP) A batch of $mtl_count_from_hrv ";
							
							if($acthrvszsbtyp[effect_subtype_id] != '37')
							{
								echo"($acthrvunitnfo[effect_abbr]) of ";
							}
							
							echo"$acthrvnfo[material] was completed $saved_hrv_end_vis.</b></font><br>";
						}
					}
					// report current failed attempts so people don't repeat
					if($gtactvhrvlst[batch_status] >= 90)
					{
						if($gtactvhrvlst[batch_status] <= 99)
						{			
							echo"<font size = '2' color = 'red'><li><b>You were unable to find any $acthrvnfo[material] in $acthrvgeosbnfo[geography_subtype].</font></b>";
							
							echo"<font size = '2' color = 'green'><li><b>You retain the Production cost";
							if($gtactvhrvlst[batch_prp] >= 1)
							{
								echo" ($gtactvhrvlst[batch_prp] PrP)";
							}
							
							echo".</font></b><br>";
						}
					}
					// report past failed attempts so people don't repeat
					if($gtactvhrvlst[batch_status] >= 40)
					{
						if($gtactvhrvlst[batch_status] <= 49)
						{			
							echo"<font size = '2' color = 'red'><li><b>You were unable to find any $acthrvnfo[material] in $acthrvgeosbnfo[geography_subtype].</font></b>";
							
							echo"<font size = '2' color = 'white'><li><b>You retain the Production cost";
							if($gtactvhrvlst[batch_prp] >= 1)
							{
								echo" ($gtactvhrvlst[batch_prp] PrP)";
							}
							
							echo".</font></b><br>";
						}
					}
					
					if($gtactvhrvlst[batch_status] >= 80)
					{
						if($gtactvhrvlst[batch_status] <= 89)
						{
							// Check if the material is even available for harvest
							$up_for_harvest = mysql_query("SELECT * FROM ".$slrp_prefix."material_harvest WHERE material_id = '$gtactvhrvlst[material_id]' AND geography_subtype_id = '$gtactvhrvlst[batch_geography_subtype_id]'") or die ("failed checking available harvest.");
							$upforhrvcnt = mysql_num_rows($up_for_harvest);
							$upforhrv = mysql_fetch_assoc($up_for_harvest);
							
							// if no available resource, resulting in failure, return their points

							if($upforhrvcnt == 0)
							{
								echo"<font size = '2' color = 'red'><li><b>You were unable to find any $acthrvnfo[material] in $acthrvgeosbnfo[geography_subtype].</font></b>";
								
								echo"<font size = '2' color = 'green'><li><b>You retain the Production cost";
								if($gtactvhrvlst[batch_prp] >= 1)
								{
									echo" ($gtactvhrvlst[batch_prp] PrP)";
								}
								
								echo".</font></b><br>";
							}
	
							if($upforhrvcnt >= 1)
							{
								echo"<font size = '2' color = '#33F406'><li><b>($gtnwvhrvlst[batch_prp] PrP) A batch of $mtl_count_from_hrv ";
								
								if($acthrvszsbtyp[effect_subtype_id] != '37')
								{
									echo"($acthrvunitnfo[effect_abbr]) of ";
								}
								
								echo"$acthrvnfo[material] was completed $saved_hrv_end_vis.</b></font><br>";
							}
						}
					}
				}
			}
		}
		
		echo"
			</td>
		";
	}
	
	echo"
		</tr>
	</table>
</td>
</tr>
	";
}

echo"
<tr>
	<td colspan = '9'>
		<hr width = '100%'>
	</td>
</tr>
<tr>
	<form name = 'show_hide_materials' method='post' action = 'modules.php?name=$module_name&file=pc_prod'>
	<td valign = 'top' align = 'left' width = '100%'>
		<table width = '100%'>
			<tr>
				<td valign = 'top' align = 'left' width = '33%'>
					<font color = 'yellow' size = '2'>
					<b>MATERIALS</b>
					</font>
					";
					
					if($materials_expander == 1)
					{
						echo"
					<br>
					<font color = 'orange' size = '1'>* Buttons will not work for unidentified Materials.</font>
						";
					}
					
					echo"
				</td>				
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left'>
					";
					
					if($materials_expander == 0)
					{
						echo"
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='1' name = 'materials_expander'>
					<input type='submit' value='Show Materials' name = 'show_hide_materials'>
				</td>
				</form>
				<td width = '2%'> 
				</td>
				<td valign = 'top' align = 'right' colspan = '3'> 
				</td>
			</tr>
		</table>
	</td>
</tr>
	";
}
if($materials_expander == 1)
{
	echo"
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='0' name = 'materials_expander'>
					<input type='submit' value='Hide Materials' name = 'show_hide_materials'>
				</td>
				</form>
				<td width = '2%'>
				</td>
				<form name = 'add_pc_mat' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
				<td valign = 'top' align = 'left' colspan = '3'>
	";

	if($curusrslrprnk[slurp_rank_id] <= 5)
	{
		echo"
					<font color = 'yellow' size = '2'><b>ADD MATERIAL to $curpcnfo[creature]:</b></font>
					<br>
					<select class='engine' name = 'mat_list'><option value = '1'>Choose One</option>";
		
		$get_mat_list = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id > '1' ORDER BY material");
		while($matlst = mysql_fetch_assoc($get_mat_list))
		{
			echo"<option value = '$matlst[material_id]'>$matlst[material]</option>";
		}

		echo"</select>
		<br>
		<br>
		<font color = '#7fffd4' size = '1'>Identified? <input type='checkbox' value='1' name='id_pc_mat'> . . . Count:  </font>";
		
		echo"<select class='engine' name = 'pc_mat_count'>";
		
		$get_batch_count_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."effect_effect_subtype.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '37' AND ".$slrp_prefix."effect.effect_abbr != '0' AND ".$slrp_prefix."effect.effect_desc LIKE '%Entit%' ORDER BY ".$slrp_prefix."effect.effect_tier ASC") or die ("failed getting units list for batches.");
		while($gtbtchcntlst = mysql_fetch_assoc($get_batch_count_list))
		{
			echo"<option value = '$gtbtchcntlst[effect_id]'>(".roman($gtbtchcntlst[effect_tier]).") $gtbtchcntlst[effect_abbr]</option>";
		}
		
		echo"</select>";
		
		// Add Material button
		echo"
				</td>
				<td width = '2%'>
				<td align = 'left'>
					<font color ='black'> . . . </font><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$harvest_expander' name = 'harvest_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='submit' value = 'Add Material' name='add_pc_mat'>";
	}

	echo"
				</td>
				</form>
			</tr>
		</table>
	</td>
</tr>
</tr>
<tr>
	<td colspan = '9'>
		<hr width = '100%'>
	</td>
</tr>
	";

	$get_pc_material = mysql_query("SELECT * FROM ".$slrp_prefix."material INNER JOIN ".$slrp_prefix."creature_material ON ".$slrp_prefix."creature_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."creature_material.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_material.creature_material_count >= '1' ORDER BY ".$slrp_prefix."material.material") or die ("failed getting pc material.");
	$curpcmatcnt = mysql_num_rows($get_pc_material);
	if($curpcmatcnt >= 1)
	{
		echo"
<tr>
	<td align = 'left' colspan = '9'>
		<table border = '0' bordercolor = 'red'>
			<tr>
					<td align = 'center'>
						<font color = 'orange' size = '1'>#</font>
					</td>
					<td width = '2%'>
					</td>
					<td align = 'left'>
						<font color = 'orange' size = '1'>UNIT</font>
					</td>
					<td width = '2%'>
					</td>
					<td align = 'left'>
					<font color = 'yellow' size = '2'>MATERIAL</font>
					</td>
		";
					
		// for staff only
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{	
			echo"
					<td width = '2%'>
					</td>
					<td>
						<font color = 'red' size = '1'>ADD</font>
					</td>
					<td width = '2%'>
					</td>
					<td>
						<font color = 'red' size = '1'>SUBTRACT</font>
					</td>
					<td width = '2%'>
					</td>
					<td>
						<font color = 'red' size = '1'>DROP</font>
					</td>
			";
		}
					
		echo"
				</tr>
		";

		while($curpcmat = mysql_fetch_assoc($get_pc_material))
		{
			$get_pc_mat_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$curpcmat[material_id]'") or die ("failed to get pc mat info.");
			$gtpcmatnfo = mysql_fetch_assoc($get_pc_mat_info);
			
			$get_pc_mat_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtpcmatnfo[creature_material_unit_id]'") or die ("failed to get pc mat unit info.");
			$gtpcmatunit = mysql_fetch_assoc($get_pc_mat_unit);
			$get_pc_mat_count = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtpcmatnfo[creature_material_count_id]'") or die ("failed to get pc mat count info.");
			$gtpcmatcount = mysql_fetch_assoc($get_pc_mat_count);
			
			if($gtpcmatnfo[creature_identified] >= 1)
			{
				$mat_instance = mysql_query("SELECT material FROM ".$slrp_prefix."material WHERE material_id = '$curpcmat[material_id]'") or die ("failed getting mat group instance desc.");
				$matinst = mysql_fetch_assoc($mat_instance);
				$mat_instance_display_name = stripslashes($matinst[material]);
				echo"
			<form name = 'edit_active_material' method = 'post' action = 'modules.php?name=$module_name&file=obj_edit'>
				";
			}
			if($gtpcmatnfo[creature_identified] == 0)
			{
				$mat_instance = mysql_query("SELECT material_short_name FROM ".$slrp_prefix."material WHERE material_id = '$curpcmat[material_id]'") or die ("failed getting material group instance short_desc.");
				$matinst = mysql_fetch_assoc($mat_instance);
				$mat_instance_display_name = stripslashes($matinst[material_short_name]);
			}					
			
			echo"
			<tr>
				<td align = 'right'>
					<font color = 'orange' size = '2'><b>$gtpcmatnfo[creature_material_count]</b></font>
				</td>
				<td width = '2%'>
				</td>
				<td align = 'right'>
					<font color = 'orange' size = '2'><b>$gtpcmatunit[effect_abbr] &nbsp; of</b></font>
				</td>
				<td width = '2%'>
				</td>
				<td align = 'left' valign = 'middle'>
					<input type='hidden' value='26' name='current_focus_id'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$component_expander' name = 'component_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$recipe_expander' name = 'harvest_expander'>
					<input type='hidden' value='$curpcmat[material_id]' name='material'>
					<input type='submit' value='";
			
			if($gtpcmatnfo[creature_identified] == 0)
			{
				echo"* ";
			}
			
			echo"$mat_instance_display_name' name='edit_active_material'>
				</td>
				</form>
			";
			
			if($curusrslrprnk[slurp_rank_id] <= 5)
			{
				echo"			
					<td width = '2%'>
					</td>
						<form name = 'add_pc_mat_value' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='$expander_abbr' name='current_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='hidden' value='$component_expander' name = 'component_expander'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$materials_expander' name = 'materials_expander'>
						<input type='hidden' value='$items_expander' name = 'items_expander'>
						<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
						<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
						<input type='hidden' value='$gtpcmatnfo[creature_material_id]' name='add_pc_mat_$gtpcmatnfo[creature_material_id]'>
					<td>
						<select class='engine' name = 'add_pc_mat'>
							<option value = '1'>1</option>
							<option value = '2'>2</option>
							<option value = '3'>3</option>
							<option value = '4'>4</option>
							<option value = '5'>5</option>
							<option value = '10'>10</option>
							<option value = '15'>15</option>
							<option value = '20'>20</option>
							<option value = '25'>25</option>
							<option value = '50'>50</option>
							<option value = '75'>75</option>
							<option value = '100'>100</option>
							<option value = '125'>125</option>
							<option value = '150'>150</option>
							<option value = '200'>200</option>
							<option value = '250'>250</option>
							<option value = '500'>500</option>
						</select>	<input type='submit' value='+' name='add_pc_mat_value'>
					</td>
					</form>
					<td width = '2%'>
					</td>
						<form name = 'sub_pc_mat_value' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='$expander_abbr' name='current_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='hidden' value='$component_expander' name = 'component_expander'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$materials_expander' name = 'materials_expander'>
						<input type='hidden' value='$items_expander' name = 'items_expander'>
						<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
						<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
						<input type='hidden' value='$gtpcmatnfo[creature_material_id]' name='sub_pc_mat_$gtpcmatnfo[creature_material_id]'>
					<td>
						<select class='engine' name = 'sub_pc_mat'>
					";
					
					$crmtlcnt = $gtpcmatnfo[creature_material_count];
					while($crmtlcnt >= 1)
					{
						echo"
							<option value = '-$crmtlcnt'>-$crmtlcnt</option>
						";
						
						$crmtlcnt--;
					}
					
					echo"
						</select> <input type='submit' value='-' name='sub_pc_mat_value'>
					</td>
					</form>
					<td width = '2%'>
					</td>
					<form name = 'del_pc_mat' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
					<td align = 'left'>
				";
				
				$get_pc_mat_del_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$curpcmat[material_id]'") or die ("failed to get pc material info.");
				$gtpcmatdlnfo = mysql_fetch_assoc($get_pc_mat_del_info);
				
				
				echo"
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='$expander_abbr' name='current_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='hidden' value='$component_expander' name = 'component_expander'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$materials_expander' name = 'materials_expander'>
						<input type='hidden' value='$items_expander' name = 'items_expander'>
						<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
						<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
						<input type='hidden' value='$gtpcmatdlnfo[creature_material_id]' name='del_pc_mat_$gtpcmatdlnfo[creature_material_id]'>
						<input type='submit' value='Drop $mat_instance_display_name' name='del_pc_mat'>
					</td>
					</form>
				";
			}
			
			echo"
				</tr>
			";
		}

		echo"
		</table>
	</td>
</tr>
		";
	}
}

echo"
			<tr>
				<td colspan = '9'>
					<hr>
				</td>
			</tr>
			
			<tr>
				<form name = 'show_hide_recipes' method='post' action = 'modules.php?name=$module_name&file=pc_prod'>
				<td valign = 'top' align = 'left' width = '100%'>
					<table width = '100%'>
						<tr>
							<td valign = 'top' align = 'left' width = '33%'>
								<font color = 'yellow' size = '2'>
								&nbsp;&nbsp;&nbsp;<b>Recipes</b>
								</font>
";
		
if($recipe_expander == 1)
{
	echo"
								<br>
								<font size = '1' color = 'orange'>A Default Batch will make <i>1 Standard Quality Item in 24 hours +1 Time Chart Tier per Durability (DRB)</i>.</font>
	";
}
		
echo"	
							</td>
							<td width = '2%'>
							</td>
							<td valign ='top' align = 'left'>
";

if($recipe_expander == 0)
{
	echo"
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$harvest_expander' name = 'harvest_expander'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'>
								<input type='hidden' value='1' name = 'recipe_expander'>
								<input type='submit' value='Show Recipes' name = 'show_hide_recipes'>
							</td>
							</form>
							<td width = '2%'>
							</td>
							<td>
							</td>
						</tr>
					</table>
				</td>
			</tr>
	";
}
if($recipe_expander == 1)
{
	echo"
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$harvest_expander' name = 'harvest_expander'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'>
								<input type='hidden' value='0' name = 'recipe_expander'>
								<input type='submit' value='Hide Recipes' name = 'show_hide_recipes'>
							</td>
							</form>
							<td width = '2%'>
							</td>
							<td>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign = 'top' align = 'left'>
					<table>
	";

	// get the list of known recipes
	$get_distinct_recipes = mysql_query("SELECT DISTINCT item FROM ".$slrp_prefix."item INNER JOIN ".$slrp_prefix."creature_item ON ".$slrp_prefix."creature_item.item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."creature_item.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_item.creature_knows_recipe = '1' ORDER BY ".$slrp_prefix."item.item") or die ("failed getting distinct recipes.");
	$dstnctpcrcpcnt = mysql_num_rows($get_distinct_recipes);
	while($dstnctpcrcp = mysql_fetch_assoc($get_distinct_recipes))
	{
		// get the list of known recipes
		$get_pc_recipes = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item = '$dstnctpcrcp[item]'") or die ("failed getting pc item.");
		$curpcrcpcnt = mysql_num_rows($get_pc_recipes);
		$curpcrcp = mysql_fetch_assoc($get_pc_recipes);

		$rcpmtl = $curpcrcp[item_tier];
		$current_pc_recipe_siz = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$curpcrcp[item_size_chart_id]'") or die ("failed getting recipe MTL.");
		$curpcrcpsz = mysql_fetch_assoc($current_pc_recipe_siz);
		$rcpsz = $curpcrcpsz[effect_tier];

		$rcpqty = 0;
		$rcpdur = ($rcpqty+$rcpsz+$rcpmtl);
		// echo"$rcpqty+$rcpsz+$rcpmtl =$rcpdur<br>";
		
		echo"
					<tr>
						<td colspan = '11'>
							<hr>
						</td>
					</tr>
					<tr>
						<form name = 'edit_active_item' method = 'post' action = 'modules.php?name=$module_name&file=obj_edit'>
						<td valign = 'top' align = 'left'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
							<input type='hidden' value='10' name='current_focus_id'>
							<input type='hidden' value='$curpcrcp[item_id]' name='item'>
							<input type='submit' value='$curpcrcp[item]' name='edit_active_item'>
						</td>
						</form>
		";
				
		// for staff only
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{
			echo"
						<td width = '2%'>
						</td>
						<form name = 'delete_active_recipe' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
						<td valign = 'top' align = 'left'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='$expander_abbr' name='current_expander'>
							<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
							<input type='hidden' value='$component_expander' name = 'component_expander'>
							<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
							<input type='hidden' value='$materials_expander' name = 'materials_expander'>
							<input type='hidden' value='$items_expander' name = 'items_expander'>
							<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
							<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
							<input type='hidden' value='$curpcrcp[item_id]' name='del_pc_rcp_$curpcrcp[item_id]'>
							<input type='submit' value='Forget' name='del_active_rcp'>
						</td>
						</form>
			";
		}
				
		if($curusrslrprnk[slurp_rank_id] >= 6)
		{
			echo"
						<td valign = 'top' align = 'left'>
					
						</td>
				";
		}
		
		echo"
						<td width = '2%'>
						</td>
						<td valign = 'top' align = 'left' colspan = '3'>";
		
		$get_active_batch_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_batch WHERE creature_id = '$curpcnfo[creature_id]' AND (batch_event_id = '$pgame[event_id]' OR batch_event_id = '$slrpnfo[slurp_next_game_id]') AND item_id = '$curpcrcp[item_id]'") or die ("failed getting active batch list.");
		$gtactvbtchlstcnt = mysql_num_rows($get_active_batch_list);
		if($gtactvbtchlstcnt >= 1)
		{
			while($gtactvbtchlst = mysql_fetch_assoc($get_active_batch_list))
			{
				// echo"$gtactvbtchlst[batch_size], $gtactvbtchlst[batch_count], $gtactvbtchlst[batch_quality_id], $gtactvbtchlst[batch_prp]<br>";
				$active_batch_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtactvbtchlst[item_id]'") or die ("failed getting current batch item info.");
				$actbtchnfo = mysql_fetch_assoc($active_batch_item_info);
				
				$active_batch_count_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_abbr >= '$gtactvbtchlst[batch_count_value]' AND effect_min_value <= '$gtactvbtchlst[batch_count_value]' AND effect LIKE '%Entit%'") or die ("failed getting current batch count nfo.");
				$actbtchcntnfo = mysql_fetch_assoc($active_batch_count_info);
				
				$item_count_from_batch = $gtactvbtchlst[batch_count_value];
				
				// echo"item_add_Count: $item_count_from_batch<br>";
				
				$improved_active_batch_quality_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtactvbtchlst[batch_quality_id]'") or die ("failed getting improved recipe quality.");
				$imprvdactbtchqtynfo = mysql_fetch_assoc($improved_active_batch_quality_info);
				
				$active_batch_status_info = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_status_id = '$gtactvbtchlst[batch_status]'") or die ("failed getting current batch status nfo.");
				$actbtchstatnfo = mysql_fetch_assoc($active_batch_status_info);
				
				$start_batch_name = stripslashes(strip_tags($actbtchnfo[item]));
				$batch_time_end_vis = $gtactvbtchlst[batch_time_end];
				$saved_batch_time_ending = date_create($batch_time_end_vis);
				$saved_batch_end_vis = date_format($saved_batch_time_ending, '(Y) D, j M g:i:s A');
				$now_date = date_create($now);
				$now_vis = date_format($now_date, 'Y-m-d H:i:s');
				// echo"$now_vis, $batch_time_end_vis<br>";
				
				// if it was ordered last downtime:
				if($gtactvbtchlst[batch_event_id] != $slrpnfo[slurp_next_game_id])
				{
					// if the due date is after the previous reset, show it
					if($gtactvbtchlst[batch_time_end] > $pgame[event_reset])
					{							
						echo"<font size = '2' color = 'white'><li><b> [Previous] $gtactvbtchlst[batch_prp] PrP</b> used for <font color = 'white'>$actbtchcntnfo[effect]</font> in #; QTY <font color = 'white'>$imprvdactbtchqtynfo[effect]</font>; Due: <font color = 'white'>$saved_batch_end_vis</font></font>";
					}
				}
				
				// if it was ordered this downtime
				if($gtactvbtchlst[batch_event_id] == $slrpnfo[slurp_next_game_id])
				{
					// if time has elapsed
					if($batch_time_end_vis <= $now)
					{
						echo"<font size = '2' color = '#33F406'><li><b>[ $gtactvbtchlst[batch_prp] PrP ] A batch of $gtactvbtchlst[batch_count_value] [$imprvdactbtchqtynfo[effect]] was completed $saved_batch_end_vis.</b></font>";
					}
				
					// if time has not elapsed
					if($batch_time_end_vis > $now)
					{
						echo"<font size = '2' color = 'orange'><li><b><font color = '#33F406'>$gtactvbtchlst[batch_prp] PrP</b></font> used for <font color = 'yellow'> $gtactvbtchlst[batch_count_value]</font> in #; QTY <font color = 'yellow'>$imprvdactbtchqtynfo[effect]</font>; Due: <font color = '#33F406'>$saved_batch_end_vis</font></font>";
					}
				}
			}
		}
						
		echo"			</td>";
		
		$final_create_limit = 1000;
		
		$get_pc_recipe_mat_info = mysql_query("SELECT * FROM ".$slrp_prefix."item_core_material INNER JOIN ".$slrp_prefix."material ON ".$slrp_prefix."item_core_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."item_core_material.item_id = '$curpcrcp[item_id]' ORDER BY ".$slrp_prefix."material.material") or die ("failed to get pc recipe mat item info.");
		$gtpcrcpmatnfocnt = mysql_num_rows($get_pc_recipe_mat_info);
		// $mtl_create_limit = 0;
		
		while($gtpcrcpmatnfo = mysql_fetch_assoc($get_pc_recipe_mat_info))
		{
			$mtl_create_limit = 0;
			$get_pc_recipe_materials = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material INNER JOIN ".$slrp_prefix."material ON ".$slrp_prefix."creature_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."creature_material.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."material.material_id = '$gtpcrcpmatnfo[material_id]' AND ".$slrp_prefix."creature_material.creature_material_count >= '$gtpcrcpmatnfo[item_core_material_count]' ORDER BY ".$slrp_prefix."material.material") or die ("failed getting pc recipe materials");
			$gtpcrcpmatcnt = mysql_num_rows($get_pc_recipe_materials);
						
			if($gtpcrcpmatcnt == 0)
			{
				$final_create_limit = 0;
			}
			if($gtpcrcpmatcnt >= 1)
			{
				$gtpcrcpmat = mysql_fetch_assoc($get_pc_recipe_materials);
				// echo"<font color= 'blue'>required mtl: $gtpcrcpmatnfo[material_id] ($gtpcrcpmatnfo[item_core_material_count]), owned: $gtpcrcpmat[material_id] ($gtpcrcpmat[creature_material_count])</font><br>";
				
				// set a temp variable to match the count of units, and set a counter
				$gtpcrcpmattmp = $gtpcrcpmat[creature_material_count];
				
				// increment the create_limit for each multiple it has
				while($gtpcrcpmattmp >= $gtpcrcpmatnfo[item_core_material_count])
				{
					$gtpcrcpmattmp = $gtpcrcpmattmp - $gtpcrcpmatnfo[item_core_material_count];
					$mtl_create_limit++;
					// echo"mtl limit+temp: $mtl_create_limit x, $gtpcrcpmattmp left<br>";
				}
	
				// take the smallest creation denominator for the final number of makeable items
				if($mtl_create_limit < $final_create_limit)
				{
					$final_create_limit = $mtl_create_limit;
					$mtl_create_limit = 0;
				}
			}
		}
		
		// echo"limit after mtl: $final_create_limit<br>";
		if($final_create_limit >= 1)
		{
			$get_pc_recipe_itm_info = mysql_query("SELECT * FROM ".$slrp_prefix."item_core_item INNER JOIN ".$slrp_prefix."item ON ".$slrp_prefix."item_core_item.ingredient_item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."item_core_item.item_id = '$curpcrcp[item_id]' ORDER BY ".$slrp_prefix."item.item") or die ("failed to get pc recipe item info.");
			$gtpcrcpitmnfocnt = mysql_num_rows($get_pc_recipe_itm_info);
			// $itm_create_limit = 0;
			
			while($gtpcrcpitmnfo = mysql_fetch_assoc($get_pc_recipe_itm_info))
			{
				$itm_create_limit = 0;
				$get_pc_recipe_items = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item INNER JOIN ".$slrp_prefix."item ON ".$slrp_prefix."creature_item.item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."creature_item.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."item.item_id = '$gtpcrcpitmnfo[item_id]' AND ".$slrp_prefix."creature_item.creature_item_count >= '$gtpcrcpitmnfo[item_core_item_count]' ORDER BY ".$slrp_prefix."item.item") or die ("failed getting pc recipe items");
				$gtpcrcpitmcnt = mysql_num_rows($get_pc_recipe_items);
							
				if($gtpcrcpitmcnt == 0)
				{
					$final_create_limit = 0;
				}
				if($gtpcrcpitmcnt >= 1)
				{
					$gtpcrcpitm = mysql_fetch_assoc($get_pc_recipe_items);
					// echo"<font color= 'blue'>required itm: $gtpcrcpitmnfo[item_id] ($gtpcrcpitmnfo[item_core_item_count]), owned: $gtpcrcpitm[item_id] ($gtpcrcpitm[creature_item_count])</font><br>";
					
					// set a temp variable to match the count of units, and set a counter
					$gtpcrcpitmtmp = $gtpcrcpitm[creature_item_count];
					
					// increment the create_limit for each multiple it has
					while($gtpcrcpitmtmp >= $gtpcrcpitmnfo[item_core_item_count])
					{
						$gtpcrcpitmtmp = $gtpcrcpitmtmp - $gtpcrcpitmnfo[item_core_item_count];
						$itm_create_limit++;
						// echo"itm limit+temp: $itm_create_limit x, $gtpcrcpitmtmp left<br>";
					}
		
					// take the smallest creation denominator for the final number of makeable items
					if($itm_create_limit < $final_create_limit)
					{
						$final_create_limit = $itm_create_limit;
						$itm_create_limit = 0;
					}
				}
			}
		}
		
		// echo"limit after itm: $final_create_limit<br>";
		
		// $rcpcorecompcnt = $rcpcorematcnt + $rcpcoreitmcnt;
		// echo"<td valign = 'top' align = 'right'><font size = '1' color = 'purple'>$rcpcorecompcnt = $rcpcorematcnt + $rcpcoreitmcnt</font></td>";
		
		// if the database is still unlocked
		if($ngame[event_database_lock_date] > $now)
		{
			if($final_create_limit >= 1)
			{
				echo"
								<td width = '2%'>
								</td>
								<td valign = 'top' align = 'right'>
									<font size = '1' color = 'yellow'>
									<b><font color = '#33F406' size = '2'>$rcpdur PrP</font></b>
									<br>
									Limit: $final_create_limit</font>
								</td>
				";
				
				if($prod_points_left >= $rcpdur)
				{
					echo"
								<td width = '2%'>
								</td>
								<form name = 'make_active_item' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod_batch'>
								<td valign = 'top' align = 'left'>
									<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
									<input type='hidden' value='$expander_abbr' name='current_expander'>
									<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
									<input type='hidden' value='$admin_expander' name = 'admin_expander'>
									<input type='hidden' value='$component_expander' name = 'component_expander'>
									<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
									<input type='hidden' value='$materials_expander' name = 'materials_expander'>
									<input type='hidden' value='$items_expander' name = 'items_expander'>
									<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
									<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
									<input type='hidden' value='$curpcrcp[item_tier]' name='batch_tier'>
									<input type='hidden' value='$rcpdur' name='batch_cost'>
									<input type='hidden' value='$final_create_limit' name='create_limit'>
									<input type='hidden' value='item' name='create_type'>
									<input type='hidden' value='$curpcrcp[item_id]' name='create_batch'>
									<input type='submit' value='Create Batch' name='make_active_item'>
								</td>
								</form>
					";
				}
			}
		}
		
		// if the db is locked
		if($ngame[event_database_lock_date] <= $now)
		{
			echo"			<td width = '2%'>
								</td>
								<td valign = 'top' align = 'left'>
								<font color = '#CC00FF'>Database Locked.</font>
								</td>
			";
		}
		
		echo"
						</tr>
		";
	}
	
	echo"
			</table>
	</td>
</tr>
	";
}


echo"
<tr>
	<td colspan = '9'>
		<hr width = '100%'>
	</td>
</tr>
<tr>
	<form name = 'show_hide_items' method='post' action = 'modules.php?name=$module_name&file=pc_prod'>
	<td valign = 'top' align = 'left' width = '100%'>
		<table width = '100%'>
			<tr>
					<td valign = 'top' align = 'left' width = '33%'>
					<font color = 'yellow' size = '2'>
					<b>ITEMS</b>
					</font>
";

if($items_expander == 1)
{
	echo"
					<br>
					<font color = 'orange' size = '1'>* Buttons will not work for unidentified Items.</font>
	";
}

echo"
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left'>
";

if($items_expander == 0)
{
	echo"
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='1' name = 'items_expander'>
					<input type='submit' value='Show Items' name = 'show_hide_items'>
				</td>
				</form>
				<td width = '2%'> 
				</td>
				<td valign = 'top' align = 'right' colspan = '3'> 
				</td>
			</tr>
		</table>
	</td>
</tr>
	";
}
if($items_expander == 1)
{
	echo"
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='0' name = 'items_expander'>
					<input type='submit' value='Hide Items' name = 'show_hide_items'>
				</td>
				</form>
				<td width = '2%'>
				</td>
				<form name = 'add_pc_itm' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
				<td valign = 'top' align = 'left' colspan = '3'>
	";
	
	if($curusrslrprnk[slurp_rank_id] <= 5)
	{
		echo"
					<font color = 'yellow' size = '2'><b>Add Item to $curpcnfo[creature]:</b></font>
					<br>
					<select class='engine' name = 'item_listing'><option value = '1'>Choose One</option>";
		
		$get_item_list = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id > '1' ORDER BY item");
		while($itemlst = mysql_fetch_assoc($get_item_list))
		{
			echo"<option value = '$itemlst[item_id]'>$itemlst[item]</option>";
			
			// for books containing Abilities
			$check_book_abilities = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."item_book ON ".$slrp_prefix."item_book.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."item_book.item_id = '$itemlst[item_id]' AND ".$slrp_prefix."item_book.item_id > '1' AND ".$slrp_prefix."item_book.ability_id > '1'ORDER BY ".$slrp_prefix."ability.ability") or die ("Failed checking book abilities");
			$chkbksabcnt = mysql_num_rows($check_book_abilities);
			while($chkbksab = mysql_fetch_assoc($check_book_abilities))
			{
			 	$book_ability_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random_id = '$chkbksab[object_random_id]'") or die ("failed getting book ability random.");
			 	$bkabrand = mysql_fetch_assoc($book_ability_random);
			 	
			 	echo"<option value = '".$itemlst[item_id]."_".$bkabrand[object_random]."'>~ IN PRINT: $chkbksab[ability] ($bkabrand[object_random])</option>";
			}
			// for books containing Recipes
			$check_book_recipes = mysql_query("SELECT * FROM ".$slrp_prefix."item INNER JOIN ".$slrp_prefix."item_book ON ".$slrp_prefix."item_book.recipe_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."item_book.item_id = '$itemlst[item_id]' AND ".$slrp_prefix."item_book.item_id > '1' AND ".$slrp_prefix."item_book.recipe_id > '1'ORDER BY ".$slrp_prefix."item.item") or die ("Failed checking book recipes");
			$chkbksrcpcnt = mysql_num_rows($check_book_recipes);
			while($chkbksrcp = mysql_fetch_assoc($check_book_recipes))
			{
			 	$book_recipe_random = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random_id = '$chkbksrcp[object_random_id]'") or die ("failed getting book recipe random.");
			 	$bkrcprand = mysql_fetch_assoc($book_recipe_random);
			 	
			 	echo"<option value = '".$itemlst[item_id]."_".$bkrcprand[object_random]."'>~ RECIPE: $chkbksrcp[item] ($bkrcprand[object_random])</option>";
			}
		}
		
		echo"</select>
		<br>
		<br>
		<font color = '#7fffd4' size = '1'>Quality? <select class='engine' name = 'pc_item_quality'><option value = '3'>Choose Quality</option>";
		
		$get_quality_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."effect_effect_subtype.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '39' AND ".$slrp_prefix."effect.effect_desc NOT LIKE '%+%' ORDER BY ".$slrp_prefix."effect.effect_tier ASC") or die ("failed getting qty list for batches.");
		while($qtylst = mysql_fetch_assoc($get_quality_list))
		{
			echo"<option value = '$qtylst[effect_id]'>$qtylst[effect]</option>";
		}
	
		echo"</select>	<font color = '#7fffd4' size = '1'>Identified? <input type='checkbox' value='1' name='id_pc_item'>  Count:  <select class='engine' name = 'pc_item_count'>";
					
		$get_item_units_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."effect_effect_subtype.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '37' AND ".$slrp_prefix."effect.effect_abbr != '0' AND ".$slrp_prefix."effect.effect_desc LIKE '%Entit%' ORDER BY ".$slrp_prefix."effect.effect_tier ASC") or die ("failed getting units list for batches.");
		while($gtitmuntslst = mysql_fetch_assoc($get_item_units_list))
		{
			echo"<option value = '$gtitmuntslst[effect_id]'>(".roman($gtitmuntslst[effect_tier]).") $gtitmuntslst[effect_abbr]</option>";
		}
		
		// end drop-down; start Add Item Button:
		echo"</select>
		</td>
		<td width = '2%'>
		</td>
		<td align = 'left'>
		<input type='hidden' value='0' name='refresh_token'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$harvest_expander' name = 'harvest_expander'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$component_expander' name = 'component_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='submit' value = 'Add Item' name='add_pc_itm'>
		";
	}
		// add forms here as needed
	echo"
				</td>
				</form>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan = '9'>
		<hr width = '100%'>
	</td>
</tr>
	";

	
	$get_pc_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item INNER JOIN ".$slrp_prefix."item ON ".$slrp_prefix."creature_item.item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."creature_item.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_item.creature_item_count >= '1' ORDER BY ".$slrp_prefix."item.item, ".$slrp_prefix."creature_item.creature_item_quality_id DESC") or die ("failed to get pc item info.");
	$gtpcitmnfocnt = mysql_num_rows($get_pc_item_info);
	if($gtpcitmnfocnt >= 1)
	{
		echo"
			<tr>
				<td align = 'left' colspan = '9'>
					<table width = '100%'>
						<tr>
							<td align = 'right'>
								<font color = 'orange' size = '1'>#</font>
							</td>
							<td width = '2%'>
							</td>
							<td align = 'left'>
								<font color = 'yellow' size = '2'>ITEM</font>
							</td>
							<td width = '2%'>
							</td>
							<td align = 'left'>
							</td>
			";
					
			// for staff only
			if($curusrslrprnk[slurp_rank_id] <= 5)
			{	
				echo"
							<td width = '2%'>
							</td>
							<td>
								<font color = 'red' size = '1'>ADD</font>
							</td>
							<td width = '2%'>
							</td>
							<td>
								<font color = 'red' size = '1'>SUBTRACT</font>
							</td>
							<td width = '2%'>
							</td>
							<td>
								<font color = 'red' size = '1'>DROP</font>
							</td>
				";
			}
			
			echo"	</tr>";
			
			while($gtpcitmnfo = mysql_fetch_assoc($get_pc_item_info))
			{
				// echo"ID'd $gtpcitmnfo[creature_identified]<br>";
				$dressed = 0;
				$verbose = 0;
				$item_listing = $gtpcitmnfo[item_id];
				include("modules/$module_name/includes/fn_prod_id.php");
				
				if($gtpcitmnfo[creature_identified] >= 1)
				{
					$instance = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtpcitmnfo[item_id]'") or die ("failed getting group instance desc.");
					$inst = mysql_fetch_assoc($instance);
					$instance_quality = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtpcitmnfo[creature_item_quality_id]'") or die ("failed getting instance quality desc.");
					$instqty = mysql_fetch_assoc($instance_quality);
					$instance_count = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE (effect_min_value <= '$gtpcitmnfo[creature_item_count]' AND effect_abbr >= '$gtpcitmnfo[creature_item_count]') AND effect LIKE '%Entit%'") or die ("failed getting instance count.");
					$instcount = mysql_fetch_assoc($instance_count);
					$instance_display_name = stripslashes($inst[item]);
					echo"<form name = 'edit_active_item' method = 'post' action = 'modules.php?name=$module_name&file=obj_edit'>";
				}
				if($gtpcitmnfo[creature_identified] == 0)
				{
					$instance = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtpcitmnfo[item_id]'") or die ("failed getting group instance short_desc.");
					$inst = mysql_fetch_assoc($instance);
					$instance_quality = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtpcitmnfo[creature_item_quality_id]'") or die ("failed getting instance quality desc.");
					$instqty = mysql_fetch_assoc($instance_quality);
					$instance_count = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE (effect_min_value <= '$gtpcitmnfo[creature_item_count]' AND effect_abbr >= '$gtpcitmnfo[creature_item_count]') AND effect LIKE '%Entit%'") or die ("failed getting instance count.");
					$instcount = mysql_fetch_assoc($instance_count);
					$instance_display_name = stripslashes($inst[item_short_name]);
				}
				
				echo"<tr>
							<td align = 'right' width = '3%'>
								<font color = 'orange' size = '2'><b>$gtpcitmnfo[creature_item_count] &nbsp; of</b></font>
							</td>
							<td width = '2%'>
							</td>
							<td align = 'left' valign = 'middle'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$expander_abbr' name='current_expander'>
								<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
								<input type='hidden' value='$component_expander' name = 'component_expander'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='$materials_expander' name = 'materials_expander'>
								<input type='hidden' value='$items_expander' name = 'items_expander'>
								<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
								<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
								<input type='hidden' value='10' name='current_focus_id'>
								<input type='hidden' value='$inst[item_id]' name='item'>
								<input type='submit' value='";
					
				if($gtpcitmnfo[creature_identified] == 0)
				{
					echo"* ";
				}
				
				echo"$instance_display_name' name='edit_active_item'><br>";
				
				if($gtpcitmnfo[creature_identified] == 1)
				{
					echo"<font color = 'orange' size = '1'>$instqty[effect] Quality</font>";
				}
				if($gtpcitmnfo[creature_identified] == 0)
				{
					echo"<font color = 'red' size = '1'>Unknown Quality</font>";
				}
					
				$listed_item_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."item_subtype INNER JOIN ".$slrp_prefix."item_item_subtype ON ".$slrp_prefix."item_subtype.item_subtype_id = ".$slrp_prefix."item_item_subtype.item_subtype_id WHERE ".$slrp_prefix."item_item_subtype.item_id = '$inst[item_id]'") or die ("failed getting listed item_subtype.");
				$listitmsub = mysql_fetch_assoc($listed_item_subtype);
				// echo"list item sub: $listitmsub[item_subtype_id], book? $gtpcitmnfo[creature_item_book_id]<br>";
				
				if($listitmsub[item_subtype_id] >= 89)
				{
					if($listitmsub[item_subtype_id] <= 93)
					{
						if($gtpcitmnfo[creature_item_book_id] == 1)
						{
							echo"<font color = 'orange'><b><i><li>Tabula Rasa</i></b>";
						}
						if($gtpcitmnfo[creature_item_book_id] >= 2)
						{
							$item_book_info = mysql_query("SELECT * FROM ".$slrp_prefix."item_book WHERE item_book_id = '$gtpcitmnfo[creature_item_book_id]'") or die ("failed getting item_book_info.");
							$itmbknfo = mysql_fetch_assoc($item_book_info);
							//{
							if($itmbknfo[ability_id] >= 2)
							{
								$item_book_ability = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$itmbknfo[ability_id]'") or die("failed getting item book ability.");
								$itmbkobj = mysql_fetch_assoc($item_book_ability);
							}
							if($itmbknfo[recipe_id] >= 2)
							{
								$item_book_recipe = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$itmbknfo[recipe_id]'") or die("failed getting item book recipe.");
								$itmbkobj = mysql_fetch_assoc($item_book_recipe);
							}
							
							$item_book_rand = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random_id = '$itmbknfo[object_random_id]'") or die("failed getting item book ability_random.");
							$itmbkrnd = mysql_fetch_assoc($item_book_rand);
							// echo"<font color = 'green'>$itmbkab[ability]</font><br>";
							$rejection = 0;
							// get the attributes list
							$all_attr = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id > '1'") or die ("failed getting attribute exclusion list.");
							while($attrs = mysql_fetch_assoc($all_attr))
							{
								// echo"attr1: $attrs[attribute_type]<br>";
								// get mods pointing at the attribute and modifying this object
								if($itmbknfo[ability_id] >= 2)
								{
									$required_attrs = mysql_query("SELECT * FROM ".$slrp_prefix."ability_ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id = ".$slrp_prefix."ability_ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_id = '4' AND ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$attrs[attribute_type_id]' AND ".$slrp_prefix."ability_ability_modifier.ability_id = '$itmbkobj[ability_id]'") or die ("failed getting required ab attrs.");
								}
								if($itmbknfo[recipe_id] >= 2)
								{
									$required_attrs = mysql_query("SELECT * FROM ".$slrp_prefix."item_ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id = ".$slrp_prefix."item_ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_id = '10' AND ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$attrs[attribute_type_id]' AND ".$slrp_prefix."item_ability_modifier.item_id = '$itmbkobj[item_id]'") or die ("failed getting required itm attrs.");
								}
								
								$rqattrscnt = mysql_num_rows($required_attrs);
								// echo"$rqattrscnt, ($itmbkab[ability]) attr1: $attrs[attribute_type]<br>";
								if($rqattrscnt >= 1)
								{
									while($rqattrs = mysql_fetch_assoc($required_attrs))
									{
										// echo"$attrs[attribute_type]<br>";
										// get mod info to compare to the pc attribute
										$attr_dependent_mods = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '$rqattrs[ability_modifier_id]'") or die ("failed getting attr dependent mods.");
										$attrdepmds = mysql_fetch_assoc($attr_dependent_mods);
										// echo"attr2: $attrdepmds[ability_modifier_short]<br>";
										
										$attr_comparison_value = mysql_query("SELECT * FROM ".$slrp_prefix."focus_exclusion INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."focus_exclusion.focus_exclusion_id = ".$slrp_prefix."ability_modifier_subfocus.focus_exclusion_id WHERE ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id = '$rqattrs[ability_modifier_id]'") or die ("failed getting abmod comparison value");  
										$attrcompval = mysql_fetch_assoc($attr_comparison_value);
										
										// get the pc's instance of this attribute
										$pc_attributes = mysql_query("SELECT * FROM ".$slrp_prefix."creature_attribute_type WHERE creature_id = '$curpcnfo[creature_id]' AND attribute_type_id = '$attrs[attribute_type_id]'") or die ("failed getting pc attrs for exclusion.");
										$pcattrs = mysql_fetch_assoc($pc_attributes);
										
										// get its tier
										// $attr_tier = round(($pcattrs[creature_attribute_type_value]+(($slrpnfo[slurp_tier_width]-1)/2))/$slrpnfo[slurp_tier_width]);
										
										// mod value = negative (the tier value minus 1)
										// $attr_tier_mod_value = -($attr_tier);
										// echo"value: $attr_tier_mod_value<br>";
										// if the tier is not high enough, reject them
										// if($attr_tier_mod_value >= $attrdepmds[ability_modifier_value])
										
										if($attrcompval[focus_comparison_value] > $pcattrs[creature_attribute_type_value])
										{
											$rejection++;
											// echo"attr rejection: +1 = $rejection<br>";
										}
										
										if($attrcompval[focus_comparison_value] <= $pcattrs[creature_attribute_type_value])
										{
			
										}
									}
								}
								// echo"rejection: $rejection<br>";
							}
							
							if($itmbknfo[ability_id] >= 2)
							{
								// see if they have the required effects
								$required_efftyps = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type INNER JOIN ".$slrp_prefix."ability_effect_type ON ".$slrp_prefix."ability_effect_type.effect_type_id = ".$slrp_prefix."effect_type.effect_type_id WHERE ".$slrp_prefix."ability_effect_type.ability_id = '$itmbkobj[ability_id]'");
								$getrqefftypscnt = mysql_num_rows($required_efftyps);
								$rndstrsum = "";
								$rqtrcnt = $getrqefftypscnt;
								
								while($reqefftyp = mysql_fetch_assoc($required_efftyps))
								{
									$required_tiers = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE effect_type_id = '$reqefftyp[effect_type_id]' AND ability_id = '$itmbkobj[ability_id]'");
									$reqtrscnt = mysql_num_rows($required_tiers);
									
									// echo"Tiers: $reqtrscnt<br>";
									while($reqtrs = mysql_fetch_assoc($required_tiers))
									{
										//echo"efftype: $reqtrs[effect_type_id], $reqtrs[effect_type_tier]<br>";
										$pc_has_minimum = mysql_query("SELECT * FROM ".$slrp_prefix."creature_effect_type WHERE effect_type_id = '$reqefftyp[effect_type_id]' AND effect_type_tier >= '$reqtrs[effect_type_tier]' AND creature_id = '$curpcnfo[creature_id]'");
										$pchasmincnt = mysql_num_rows($pc_has_minimum);
										
										// if they lack the requisite effects, send them back
										$pchasmin = mysql_fetch_assoc($pc_has_minimum);
										
										if($pchasmincnt == 0)
										{
											// for every effect type the character doesn't exceed, add to the counter
											$rqtrcnt++;
											// echo"$reqtrs[effect_type_id] fail<br>";
										}
										
										if($pchasmincnt >= 1)
										{
											// for every effect type the character exceeds, subtract the counter
											// if successful, the counter will drop to zero
										  $rqtrcnt--;
										}
									}
								}
								if($rqtrcnt >= 1)
								{
									$rejection++;
								}
							}
							
							if($itmbknfo[recipe_id] >= 2)
							{
								// echo"<b>$itmbknfo[recipe_id]<br></b>";
								$item_listing_temp = $item_listing;
								$item_listing = $itmbknfo[recipe_id];
								$learned_random = $itmbkrnd[object_random_id];
								include("modules/$module_name/includes/fn_prod_id.php");
								
								if($recipe_reject >= 1)
								{
									$rejection++;
								}
								// save the item listing as it was set.
								$item_listing = $item_listing_temp;
							}
							
							if($rejection == 0)
							{
								if($itmbknfo[ability_id] >= 2)
								{
									echo"
							<font color = 'orange'><b>
							<li>ABILITY: $itmbkobj[ability]: <font color = 'blue'>$itmbkrnd[object_random]</font></b>
									";
								}
								if($itmbknfo[recipe_id] >= 2)
								{
									echo"
							<font color = 'orange'><b>
							<li>RECIPE: $itmbkobj[item]: <font color = 'blue'>$itmbkrnd[object_random]</font></b>
									";
								}
							}
							if($rejection >= 1)
							{
								echo"<font color = 'blue' size = '2'><b>$itmbkrnd[object_random]</b><br>";
							}
							
							// reset the reject counter
							$rejection = 0;
						}
					}
				}
			
			echo"
							</td>
							</form>
			";
				
			if($curusrslrprnk[slurp_rank_id] <= 5)
			{
				echo"
									<td width = '2%'>
									</td>
									<form name = 'id_pc_item' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
									<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
									<input type='hidden' value='$expander_abbr' name='current_expander'>
									<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
									<input type='hidden' value='$component_expander' name = 'component_expander'>
									<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
									<input type='hidden' value='$materials_expander' name = 'materials_expander'>
									<input type='hidden' value='$items_expander' name = 'items_expander'>
									<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
									<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
									<input type='hidden' value='$item_listing' name = 'item_listing'>
									<input type='hidden' value='$gtpcitmnfo[creature_item_quality_id]' name = 'pc_item_quality'>
									<input type='hidden' value='$item_book' name = 'item_book'>
									<td align = 'left'>
									<input type='submit' value='ID' name='id_pc_item'>
									</td>
									</form>
									<td width = '2%'>
									</td>
										<form name = 'add_pc_itm_value' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
										<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
										<input type='hidden' value='$expander_abbr' name='current_expander'>
										<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
										<input type='hidden' value='$component_expander' name = 'component_expander'>
										<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
										<input type='hidden' value='$materials_expander' name = 'materials_expander'>
										<input type='hidden' value='$items_expander' name = 'items_expander'>
										<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
										<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
										<input type='hidden' value='$gtpcitmnfo[creature_item_id]' name='add_pc_itm_$gtpcitmnfo[creature_item_id]'>
									<td>
										<select class='engine' name = 'add_pc_itm'>
											<option value = '1'>1</option>
											<option value = '2'>2</option>
											<option value = '3'>3</option>
											<option value = '4'>4</option>
											<option value = '5'>5</option>
											<option value = '10'>10</option>
											<option value = '15'>15</option>
											<option value = '20'>20</option>
											<option value = '25'>25</option>
											<option value = '50'>50</option>
											<option value = '75'>75</option>
											<option value = '100'>100</option>
											<option value = '125'>125</option>
											<option value = '150'>150</option>
											<option value = '200'>200</option>
											<option value = '250'>250</option>
											<option value = '500'>500</option>
										</select>	<input type='submit' value='+' name='add_pc_itm_value'>
									</td>
									</form>
									<td width = '2%'>
									</td>
										<form name = 'sub_pc_itm_value' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
										<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
										<input type='hidden' value='$expander_abbr' name='current_expander'>
										<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
										<input type='hidden' value='$component_expander' name = 'component_expander'>
										<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
										<input type='hidden' value='$materials_expander' name = 'materials_expander'>
										<input type='hidden' value='$items_expander' name = 'items_expander'>
										<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
										<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
										<input type='hidden' value='$gtpcitmnfo[creature_item_id]' name='sub_pc_itm_$gtpcitmnfo[creature_item_id]'>
									<td>
										<select class='engine' name = 'sub_pc_itm'>
				";
				
				$critmcnt = $gtpcitmnfo[creature_item_count];
				while($critmcnt >= 1)
				{
					echo"
							<option value = '-$critmcnt'>-$critmcnt</option>
					";
					
					$critmcnt--;
				}
				
				echo"
										</select> <input type='submit' value='-' name='sub_pc_itm_value'>
									</td>
									</form>
									<td width = '2%'>
									</td>
									<form name = 'del_pc_itm' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
									<td align = 'left'>
				";
				
				$get_pc_itm_del_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]' AND item_id = '$inst[item_id]' AND creature_item_quality_id = '$gtpcitmnfo[creature_item_quality_id]'") or die ("failed to get pc item info.");
				$gtpcitmdlnfo = mysql_fetch_assoc($get_pc_itm_del_info);
				
				echo"
										<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
										<input type='hidden' value='$expander_abbr' name='current_expander'>
										<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
										<input type='hidden' value='$component_expander' name = 'component_expander'>
										<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
										<input type='hidden' value='$materials_expander' name = 'materials_expander'>
										<input type='hidden' value='$items_expander' name = 'items_expander'>
										<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
										<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
										<input type='hidden' value='$gtpcitmdlnfo[creature_item_id]' name='del_pc_itm_$gtpcitmdlnfo[creature_item_id]'>
										<input type='submit' value='Drop $instance_display_name' name='del_pc_itm'>
									</td>
									</form>
				";
			}
			
			echo"
							</tr>
			";
		}
	
		echo"
					</table>
				</td>
			</tr>
		";
	}
}

echo"
			<tr>
				<td valign = 'top' align = 'left' colspan = '9'>
					<hr width = '100%'>
				</td>
			</tr>
			<tr>
				<td>
					<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$component_expander' name = 'component_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='1' name='char_expander'>
					<input type='submit' value='Back to Main' name='go_home'>
				</td>
				</form>
			</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>