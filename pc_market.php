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
$nav_title = "Market";
$nav_page = "pc_market";
include("modules/$module_name/includes/slurp_header.php");

// checkbox variables for the index
$expander_abbr = $_POST['current_expander'];
$expander = $expander_abbr."_expander";
//echo"exp: $expander_abbr, $expander<br>";

if(isset($_POST['del_batch_id']))
{
	$creature_market_batch_id = $_POST['del_batch_id'];
	$delete_trade_batch = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed deleting batch.");
	$delete_trade_batch_private = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_private WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed deleting batch private.");
	$delete_trade_batch_objects = mysql_query("DELETE FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed deleting batch objects.");
	$delete_trade_batch_bids = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_bid WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed deleting batch bids.");
}

// making a bid into a batch
if(isset($_POST['remove_bid_id']))
{
	$remove_bid_id = $_POST['remove_bid_id'];
	$remove_market_batch_id = $_POST['remove_market_batch_id'];
	
	$clearing_a_bid = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_bid WHERE creature_market_batch_id = '$remove_market_batch_id' AND creature_market_bid_id = '$remove_bid_id'") or die ("Failed deleting bid.");
	$bid_to_batch = mysql_query("UPDATE ".$slrp_prefix."creature_market_batch SET market_batch_status_id = '0' WHERE creature_market_batch_id = '$remove_bid_id'") or die ("failed updating batch action for bids.");
}

echo"
<tr>
	<td valign = 'top' align = 'left'  colspan = '10' width = '100%'>
		<table width = '100%' cellspacing = '0'>
			<tr>
				<form name = 'pc_edit' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
				<td width = '15%' align = 'left' valign = 'middle'>
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
				<form name = 'show_hide_instructions' method='post' action = 'modules.php?name=$module_name&file=pc_market'>
				<td valign = 'middle' align = 'left' width = '15%'>
";

if($ntro_expander == 1)
{
	echo"
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='0' name = 'ntro_expander'>
					<input type='submit' value='Hide Instructions' name='show_hide_instructions'>
	";
}

if($ntro_expander == 0)
{
	echo"
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='1' name = 'ntro_expander'>
					<input type='submit' value='Show Instructions' name='show_hide_instructions'>
	";
}

echo"
				</td>
				</form>
				<td width = '2%'>
				</td>
				<form name = 'mat_list' method='post' action = 'modules.php?name=$module_name&file=obj_list'>
				<td valign = 'middle' align = 'center' width = '15%'>
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
				</td>
				</form>
				<td width = '2%'>
				</td>
				<form name = 'item_list' method='post' action = 'modules.php?name=$module_name&file=obj_list'>
				<td width = '15%' align = 'center' valign = 'middle'>
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
				<td width = '2%'>
				</td>
				<form name = 'pc_production' method='post' action = 'modules.php?name=$module_name&file=pc_prod'>
				<td valign = 'middle' align = 'right' width ='15%'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$admin_expander' name = 'admin_expander'>
					<input type='submit' value='Go to Production'>
					</font>
				</td>
				</form>
				<td width = '2%'>
				</td>
				<td valign = 'middle' align = 'right' width ='15%'>
				</td>
			</tr>
		</table>
	</td>
</tr>
";

if(isset($_POST['make_trade']))
{
	// get the post variable for the sale batch
	$make_trade_batch_id = $_POST['make_trade_batch_id'];
	$get_batch_inventory = mysql_query("SELECT * FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_market_batch_id = '$make_trade_batch_id'") or die ("failed getting sold batch objects.");
	
	// this gets used later, but is here to get the name of the bidder
	$make_trade_bid_id = $_POST['make_trade_bid_id'];
	$get_bid_inventory_bidder = mysql_query("SELECT * FROM ".$slrp_prefix."creature INNER JOIN ".$slrp_prefix."creature_market_batch ON ".$slrp_prefix."creature_market_batch.creature_id = ".$slrp_prefix."creature.creature_id WHERE ".$slrp_prefix."creature_market_batch.creature_market_batch_id = '$make_trade_bid_id'") or die ("failed getting sold bidder.");
	$gtbidinvtrybddr = mysql_fetch_assoc($get_bid_inventory_bidder);
	
	while($gtbtchinvtry = mysql_fetch_assoc($get_batch_inventory))
	{
		// materials
		if($gtbtchinvtry[focus_id] == 26)
		{
			// get the batch's materials
			$get_batch_inventory_materials = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_material_id = '$gtbtchinvtry[creature_object_id]'") or die ("failed getting sold batch materials.");
			while($gtbtchinvtrymats = mysql_fetch_assoc($get_batch_inventory_materials))
			{
				// get material attributes
				$get_batch_inventory_materials_instance = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id = '$gtbtchinvtrymats[material_id]'") or die ("failed getting sold batch material instance.");
				$gtbtchinvtrymatsinst = mysql_fetch_assoc($get_batch_inventory_materials_instance);
				$get_batch_inventory_materials_instance_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtbtchinvtrymatsinst[material_default_unit_id]'") or die ("failed getting sold batch material instance size.");
				$gtbtchinvtrymatsinstunit = mysql_fetch_assoc($get_batch_inventory_materials_instance_unit);
				
				// get the bidder's materials
				$get_bidder_inventory_materials = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$gtbidinvtrybddr[creature_id]' AND material_id = '$gtbtchinvtrymatsinst[material_id]' AND creature_material_unit_id = '$gtbtchinvtrymats[creature_material_unit_id]'") or die ("failed getting existing bidder materials.");
				$gtbddrinvtrymatscnt = mysql_num_rows($get_bidder_inventory_materials);
				// if they already have it, increment it
				if($gtbddrinvtrymatscnt >= 1)
				{
					$gtbddrinvtrymats = mysql_fetch_assoc($get_bidder_inventory_materials);
					$bidder_mtl_count_new = ($gtbddrinvtrymats[creature_material_count]+$gtbtchinvtry[creature_object_sale_count]);
					$bidder_mtl_count_new_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$gtbtchinvtrymats[creature_material_count]' AND effect_abbr >= '$gtbtchinvtrymats[creature_material_count]' AND effect LIKE '%Entit%'") or die ("failed getting sold batch material instance.");
					$bddrmtlcntnwamt = mysql_fetch_assoc($bidder_mtl_count_new_amount);
					
					$update_bidder_inventory_materials = mysql_query("UPDATE ".$slrp_prefix."creature_material SET creature_material_count='$bidder_mtl_count_new',creature_material_count_id='$bddrmtlcntnwamt[effect_id]' WHERE creature_id = '$gtbidinvtrybddr[creature_id]' AND material_id = '$gtbtchinvtrymatsinst[material_id]' AND creature_material_unit_id = '$gtbtchinvtrymatsinstunit[effect_id]'") or die ("failed updating existing bidder materials.");
				}
				// if they do not have that unit of that material, add it
				if($gtbddrinvtrymatscnt == 0)
				{
					$bidder_mtl_count_new = $gtbtchinvtry[creature_object_sale_count];
					$bidder_mtl_count_new_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$gtbtchinvtrymats[creature_material_count]' AND effect_abbr >= '$gtbtchinvtrymats[creature_material_count]' AND effect LIKE '%Entit%'") or die ("failed getting sold batch material instance.");
					$bddrmtlcntnwamt = mysql_fetch_assoc($bidder_mtl_count_new_amount);
					
					$insert_bidder_inventory_materials = mysql_query("INSERT INTO ".$slrp_prefix."creature_material (creature_id,material_id,creature_material_count_id,creature_material_count,creature_material_unit_id,creature_identified) VALUES ('$gtbidinvtrybddr[creature_id]','$gtbtchinvtrymatsinst[material_id]','$bddrmtlcntnwamt[effect_id]','$bidder_mtl_count_new','$gtbtchinvtrymatsinstunit[effect_id]','1')") or die ("failed inserting existing bidder materials.");
				}
				
				// now verify the update happened to the bidder's metarials
				$verify_bidder_inventory_materials = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$gtbidinvtrybddr[creature_id]' AND material_id = '$gtbtchinvtrymatsinst[material_id]' AND creature_material_count_id = '$bddrmtlcntnwamt[effect_id]' AND creature_material_count = '$bidder_mtl_count_new' AND creature_material_unit_id = '$gtbtchinvtrymatsinstunit[effect_id]' AND creature_identified = '1'") or die ("failed checking updated bidder materials.");
				$vrfybddrinvtrymtlscnt = mysql_num_rows($verify_bidder_inventory_materials);
				if($vrfybddrinvtrymtlscnt == 1)
				{
					echo"
				<tr>
					<td colspan='11'>
					<font color = 'orange'>$gtbidinvtrybddr[creature] now owns ($gtbtchinvtry[creature_object_sale_count])($bidder_mtl_count_new total) $gtbtchinvtrymatsinstunit[effect] of $gtbtchinvtrymatsinst[material].</font><br>
					</td>
				</tr>
					";
				}
				
				// decrement the seller's materials
				$seller_material_decrement_total = ($gtbtchinvtrymats[creature_material_count]-$gtbtchinvtry[creature_object_sale_count]);
				$seller_mtl_dec_ttl_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$seller_material_decrement_total' AND effect_abbr >= '$seller_material_decrement_total' AND effect LIKE '%Entit%'") or die ("failed getting traded bid material instance.");
				$sllrmtldecttlamt = mysql_fetch_assoc($seller_mtl_dec_ttl_amount);
				$decrement_seller_inventory_materials = mysql_query("UPDATE ".$slrp_prefix."creature_material SET creature_material_count='$seller_material_decrement_total',creature_material_count_id='$sllrmtldecttlamt[effect_id]' WHERE creature_material_id = '$gtbtchinvtry[creature_object_id]'") or die ("failed decrementing existing seller materials.");
				
				// now verify the decrement happened to the seller's metarials
				$verify_seller_decrement_materials = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$gtbtchinvtrymatsinst[material_id]' AND creature_material_count_id = '$sllrmtldecttlamt[effect_id]' AND creature_material_count = '$seller_material_decrement_total' AND creature_material_unit_id = '$gtbtchinvtrymatsinstunit[effect_id]' AND creature_identified = '1'") or die ("failed checking updated bidder materials.");
				$vrfysllrdecmtlcnt = mysql_num_rows($verify_seller_decrement_materials);
				$vrfysllrdecmtl = mysql_fetch_assoc($verify_seller_decrement_materials);
				if($vrfysllrdecmtlcnt == 1)
				{
					echo"
				<tr>
					<td colspan='11'>
					<font color = 'orange'>$curpcnfo[creature] traded ($gtbtchinvtry[creature_object_sale_count]) $gtbtchinvtrymatsinst[material] to $gtbidinvtrybddr[creature] ($vrfysllrdecmtl[creature_material_count] left)</font><br>
					</td>
				</tr>
					";
				}
			}
		}
		if($gtbtchinvtry[focus_id] == 10)
		{
			$get_batch_inventory_items = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_item_id = '$gtbtchinvtry[creature_object_id]'") or die ("failed getting sold batch items.");
			while($gtbtchinvtryitms = mysql_fetch_assoc($get_batch_inventory_items))
			{
				$get_batch_inventory_items_instance = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtbtchinvtryitms[item_id]'") or die ("failed getting sold batch item instance.");
				$gtbtchinvtryitmsinst = mysql_fetch_assoc($get_batch_inventory_items_instance);
				$get_batch_inventory_items_instance_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$gtbtchinvtryitms[creature_item_count]' AND effect_abbr >= '$gtbtchinvtryitms[creature_item_count]' AND effect LIKE '%Entit%'") or die ("failed getting sold batch item instance.");
				$gtbtchinvtryitmsinstamt = mysql_fetch_assoc($get_batch_inventory_items_instance_amount);
				$get_batch_inventory_items_instance_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtbtchinvtryitmsinst[item_default_unit_id]'") or die ("failed getting sold batch item instance size.");
				$gtbtchinvtryitmsinstunit = mysql_fetch_assoc($get_batch_inventory_items_instance_unit);
				$get_batch_inventory_items_instance_quality = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtbtchinvtryitms[creature_item_quality_id]'") or die ("failed getting sold batch item instance quality.");
				$gtbtchinvtryitmsinstqty = mysql_fetch_assoc($get_batch_inventory_items_instance_quality);
				
				$get_bidder_inventory_items = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$gtbidinvtrybddr[creature_id]' AND item_id = '$gtbtchinvtryitmsinst[item_id]' AND creature_item_quality_id = '$gtbtchinvtryitmsinstqty[effect_id]'") or die ("failed getting existing bidder items.");
				$gtbddrinvtryitmscnt = mysql_num_rows($get_bidder_inventory_items);
				if($gtbddrinvtryitmscnt >= 1)
				{
					$gtbddrinvtryitms = mysql_fetch_assoc($get_bidder_inventory_items);
					$bidder_itm_count_new = ($gtbddrinvtryitms[creature_item_count]+$gtbtchinvtry[creature_object_sale_count]);
					$bidder_itm_count_new_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$bidder_itm_count_new' AND effect_abbr >= '$bidder_itm_count_new' AND effect LIKE '%Entit%'") or die ("failed getting sold batch item instance.");
					$bddritmcntnwamt = mysql_fetch_assoc($bidder_itm_count_new_amount);
					
					$update_bidder_inventory_items = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_item_count='$bidder_itm_count_new',creature_item_count_id='$bddritmcntnwamt[effect_id]' WHERE creature_id = '$gtbidinvtrybddr[creature_id]' AND item_id = '$gtbtchinvtryitmsinst[item_id]' AND creature_item_quality_id = '$gtbtchinvtryitmsinstqty[effect_id]'") or die ("failed updating existing bidder items.");
				}
				if($gtbddrinvtryitmscnt == 0)
				{
					$bidder_itm_count_new = $gtbtchinvtry[creature_object_sale_count];
					$bidder_itm_count_new_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$bidder_itm_count_new' AND effect_abbr >= '$bidder_itm_count_new' AND effect LIKE '%Entit%'") or die ("failed getting sold batch item instance.");
					$bddritmcntnwamt = mysql_fetch_assoc($bidder_itm_count_new_amount);
					
					$insert_bidder_inventory_items = mysql_query("INSERT INTO ".$slrp_prefix."creature_item (creature_id,item_id,creature_item_count_id,creature_item_count,creature_identified,creature_item_quality_id,creature_item_book_id,creature_item_random_id) VALUES ('$gtbidinvtrybddr[creature_id]','$gtbtchinvtryitmsinst[item_id]','$bddritmcntnwamt[effect_id]','$bidder_itm_count_new','1','$gtbtchinvtryitmsinstqty[effect_id]','$gtbtchinvtryitms[creature_item_book_id]','$gtbtchinvtryitms[creature_item_random_id]')") or die ("failed inserting existing bidder items.");
				}
					
				$verify_bidder_inventory_items = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$gtbidinvtrybddr[creature_id]' AND item_id = '$gtbtchinvtryitmsinst[item_id]' AND creature_item_count_id = '$bddritmcntnwamt[effect_id]' AND creature_item_count = '$bidder_itm_count_new' AND creature_item_quality_id = '$gtbtchinvtryitmsinstqty[effect_id]' AND creature_identified = '1' AND creature_item_book_id = '$gtbtchinvtryitms[creature_item_book_id]' AND creature_item_random_id = '$gtbtchinvtryitms[creature_item_random_id]'") or die ("failed checking updated bidder items.");
				$vrfybddrinvtryitmscnt = mysql_num_rows($verify_bidder_inventory_items);
				if($vrfybddrinvtryitmscnt == 1)
				{
					echo"
				<tr>
					<td colspan='11'>
					<font color = 'green'>$gtbidinvtrybddr[creature] now owns ($gtbtchinvtry[creature_object_sale_count])($bidder_itm_count_new total) of $gtbtchinvtryitmsinstqty[effect] quality $gtbtchinvtryitmsinst[item].</font><br>
					</td>
				</tr>
					";
				}
				
				// decrement the seller's items
				$seller_item_decrement_total = ($gtbtchinvtryitms[creature_item_count]-$gtbtchinvtry[creature_object_sale_count]);
				$seller_itm_dec_ttl_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$seller_item_decrement_total' AND effect_abbr >= '$seller_item_decrement_total' AND effect LIKE '%Entit%'") or die ("failed getting traded bid item count tier.");
				$sllritmdecttlamt = mysql_fetch_assoc($seller_itm_dec_ttl_amount);
				$decrement_seller_inventory_items = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_item_count='$seller_item_decrement_total',creature_item_count_id='$sllritmdecttlamt[effect_id]' WHERE creature_item_id = '$gtbtchinvtry[creature_object_id]'") or die ("failed decrementing existing seller items.");
						
				// now verify the decrement happened to the seller's metarials
				$verify_seller_decrement_items = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]' AND item_id = '$gtbtchinvtryitmsinst[item_id]' AND creature_item_count = '$seller_item_decrement_total' AND creature_item_count_id = '$sllritmdecttlamt[effect_id]' AND creature_item_quality_id = '$gtbtchinvtryitmsinstqty[effect_id]'") or die ("failed checking decremented seller items.");
				$vrfysllrdecitmcnt = mysql_num_rows($verify_seller_decrement_items);
				$vrfysllrdecitm = mysql_fetch_assoc($verify_seller_decrement_items);
				if($vrfysllrdecitmcnt == 1)
				{
					echo"
				<tr>
					<td colspan='11'>
					<font color = 'green'>$curpcnfo[creature] traded ($gtbtchinvtry[creature_object_sale_count]) $gtbtchinvtryitmsinst[item] to $gtbidinvtrybddr[creature] ($vrfysllrdecitm[creature_item_count] left)</font><br>
					</td>
				</tr>
					";
				}
				
			}
		}
	}

	$get_bid_inventory = mysql_query("SELECT * FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_market_batch_id = '$make_trade_bid_id'") or die ("failed getting sold bid objects.");
	while($gtbidinvtry = mysql_fetch_assoc($get_bid_inventory))
	{
		if($gtbidinvtry[focus_id] == 26)
		{
			$get_bid_inventory_materials = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_material_id = '$gtbidinvtry[creature_object_id]'") or die ("failed getting sold bid materials.");
			while($gtbidinvtrymats = mysql_fetch_assoc($get_bid_inventory_materials))
			{
				$get_bid_inventory_materials_instance = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id = '$gtbidinvtrymats[material_id]'") or die ("failed getting sold bid material instance.");
				$gtbidinvtrymatsinst = mysql_fetch_assoc($get_bid_inventory_materials_instance);
				$get_bid_inventory_materials_instance_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$gtbidinvtrymats[creature_material_count]' AND effect_abbr >= '$gtbidinvtrymats[creature_material_count]' AND effect LIKE '%Entit%'") or die ("failed getting sold bid material instance.");
				$gtbidinvtrymatsinstamt = mysql_fetch_assoc($get_bid_inventory_materials_instance_amount);
				$get_bid_inventory_materials_instance_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtbidinvtrymatsinst[material_default_unit_id]'") or die ("failed getting sold bid material instance size.");
				$gtbidinvtrymatsinstunit = mysql_fetch_assoc($get_bid_inventory_materials_instance_unit);
				
				$get_seller_inventory_materials = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$gtbidinvtrymatsinst[material_id]' AND creature_material_unit_id = '$gtbidinvtrymats[creature_material_unit_id]'") or die ("failed getting existing seller materials.");
				$gtsllrinvtrymatscnt = mysql_num_rows($get_seller_inventory_materials);
				if($gtsllrinvtrymatscnt >= 1)
				{
					$gtsllrinvtrymats = mysql_fetch_assoc($get_seller_inventory_materials);
					$seller_mtl_count_new = ($gtsllrinvtrymats[creature_material_count]+$gtbidinvtry[creature_object_sale_count]);
					$seller_mtl_count_new_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$seller_mtl_count_new' AND effect_abbr >= '$seller_mtl_count_new' AND effect LIKE '%Entit%'") or die ("failed getting traded bid material instance.");
					$sllrmtlcntnwamt = mysql_fetch_assoc($seller_mtl_count_new_amount);
					
					$update_seller_inventory_materials = mysql_query("UPDATE ".$slrp_prefix."creature_material SET creature_material_count='$seller_mtl_count_new',creature_material_count_id='$sllrmtlcntnwamt[effect_id]' WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$gtbidinvtrymatsinst[material_id]' AND creature_material_unit_id = '$gtbidinvtrymatsinstunit[effect_id]'") or die ("failed updating existing seller materials.");
				}
				if($gtsllrinvtrymatscnt == 0)
				{
					$seller_mtl_count_new = $gtbidinvtry[creature_object_sale_count];
					$seller_mtl_count_new_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$seller_mtl_count_new' AND effect_abbr >= '$seller_mtl_count_new' AND effect LIKE '%Entit%'") or die ("failed getting traded batch material instance.");
					$sllrmtlcntnwamt = mysql_fetch_assoc($seller_mtl_count_new_amount);
					
					$insert_seller_inventory_materials = mysql_query("INSERT INTO ".$slrp_prefix."creature_material (creature_id,material_id,creature_material_count_id,creature_material_count,creature_material_unit_id,creature_identified) VALUES ('$curpcnfo[creature_id]','$gtbidinvtrymatsinst[material_id]','$sllrmtlcntnwamt[effect_id]','$seller_mtl_count_new','$gtbidinvtrymatsinstunit[effect_id]','1')") or die ("failed inserting existing seller materials.");
				}
				// now verify the update happened to the bidder's metarials
				$verify_seller_inventory_materials = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' AND material_id = '$gtbidinvtrymatsinst[material_id]' AND creature_material_count_id = '$sllrmtlcntnwamt[effect_id]' AND creature_material_count = '$seller_mtl_count_new' AND creature_material_unit_id = '$gtbidinvtrymatsinstunit[effect_id]' AND creature_identified = '1'") or die ("failed checking updated seller materials.");
				$vrfysllrinvtrymtlscnt = mysql_num_rows($verify_seller_inventory_materials);
				if($vrfysllrinvtrymtlscnt == 1)
				{
					echo"
				<tr>
					<td colspan='11'>
					<font color = 'blue'>$curpcnfo[creature] now owns ($gtbidinvtry[creature_object_sale_count])($seller_mtl_count_new total) of $gtbidinvtrymatsinst[material].</font><br>
					</td>
				</tr>
					";
				}
				
				// decrement the bidder's materials
				$bidder_material_decrement_total = ($gtbidinvtrymats[creature_material_count]-$gtbidinvtry[creature_object_sale_count]);
				$bidder_mtl_dec_ttl_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$bidder_material_decrement_total' AND effect_abbr >= '$bidder_material_decrement_total' AND effect LIKE '%Entit%'") or die ("failed getting traded bid material instance.");
				$bddrmtldecttlamt = mysql_fetch_assoc($bidder_mtl_dec_ttl_amount);
				$decrement_bidder_inventory_materials = mysql_query("UPDATE ".$slrp_prefix."creature_material SET creature_material_count='$bidder_material_decrement_total',creature_material_count_id='$bddrmtldecttlamt[effect_id]' WHERE creature_material_id = '$gtbidinvtry[creature_object_id]'") or die ("failed decrementing existing bidder materials.");
				
				// now verify the decrement happened to the bidder's metarials
				$verify_bidder_decrement_materials = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$gtbidinvtrybddr[creature_id]' AND material_id = '$gtbidhinvtrymatsinst[material_id]' AND creature_material_count_id = '$bddrmtldecttlamt[effect_id]' AND creature_material_count = '$bidder_material_decrement_total' AND creature_material_unit_id = '$gtbidinvtrymatsinstunit[effect_id]' AND creature_identified = '1'") or die ("failed checking decremented bidder materials.");
				$vrfybddrdecmtlcnt = mysql_num_rows($verify_bidder_decrement_materials);
				$vrfybddrdecmtl = mysql_fetch_assoc($verify_bidder_decrement_materials);
				if($vrfybddrdecmtlcnt == 1)
				{
					echo"
				<tr>
					<td colspan='11'>
					<font color = 'blue'>$gtbidinvtrybddr[creature] traded ($gtbidinvtry[creature_object_sale_count]) of $gtbidinvtrymatsinst[material] to $curpcnfo[creature] ($vrfybddrdecmtl[creature_material_count] left)</font><br>
					</td>
				</tr>
					";
				}
			}
		}
		if($gtbidinvtry[focus_id] == 10)
		{
			$get_bid_inventory_items = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_item_id = '$gtbidinvtry[creature_object_id]'") or die ("failed getting sold bid items.");
			while($gtbidinvtryitms = mysql_fetch_assoc($get_bid_inventory_items))
			{
				$get_bid_inventory_items_instance = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtbidinvtryitms[item_id]'") or die ("failed getting sold bid item instance.");
				$gtbidinvtryitmsinst = mysql_fetch_assoc($get_bid_inventory_items_instance);
				$get_bid_inventory_items_instance_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$gtbidinvtryitms[creature_item_count]' AND effect_abbr >= '$gtbidinvtryitms[creature_item_count]' AND effect LIKE '%Entit%'") or die ("failed getting sold bid item instance.");
				$gtbidinvtryitmsinstamt = mysql_fetch_assoc($get_bid_inventory_items_instance_amount);
				$get_bid_inventory_items_instance_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtbidinvtryitmsinst[item_default_unit_id]'") or die ("failed getting sold bid item instance size.");
				$gtbidinvtryitmsinstunit = mysql_fetch_assoc($get_bid_inventory_items_instance_unit);
				$get_bid_inventory_items_instance_quality = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtbidinvtryitms[creature_item_quality_id]'") or die ("failed getting sold bid item instance quality.");
				$gtbidinvtryitmsinstqty = mysql_fetch_assoc($get_bid_inventory_items_instance_quality);
					
				$get_seller_inventory_items = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]' AND item_id = '$gtbidinvtryitmsinst[item_id]' AND creature_item_quality_id = '$gtbidinvtryitmsinstqty[effect_id]'") or die ("failed getting existing seller items.");
				$gtsllrinvtryitmscnt = mysql_num_rows($get_seller_inventory_items);
				if($gtsllrinvtryitmscnt >= 1)
				{
					$gtsllrinvtryitms = mysql_fetch_assoc($get_seller_inventory_items);
					$seller_itm_count_new = ($gtsllrinvtryitms[creature_item_count]+$gtbidinvtry[creature_object_sale_count]);
					$seller_itm_count_new_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$seller_itm_count_new' AND effect_abbr >= '$seller_itm_count_new' AND effect LIKE '%Entit%'") or die ("failed getting traded bid item instance.");
					$sllritmcntnwamt = mysql_fetch_assoc($seller_itm_count_new_amount);
					
					$update_seller_inventory_items = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_item_count='$seller_itm_count_new',creature_item_count_id='$sllritmcntnwamt[effect_id]' WHERE creature_id = '$curpcnfo[creature_id]' AND item_id = '$gtbidinvtryitmsinst[item_id]' AND creature_item_quality_id = '$gtbidinvtryitmsinstqty[effect_id]'") or die ("failed updating existing seller items.");
				}
				if($gtsllrinvtryitmscnt == 0)
				{
					$seller_itm_count_new = $gtbidinvtry[creature_object_sale_count];
					$seller_itm_count_new_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$seller_itm_count_new' AND effect_abbr >= '$seller_itm_count_new' AND effect LIKE '%Entit%'") or die ("failed getting traded bid item instance.");
					$sllritmcntnwamt = mysql_fetch_assoc($seller_itm_count_new_amount);
					
					$insert_seller_inventory_items = mysql_query("INSERT INTO ".$slrp_prefix."creature_item (creature_id,item_id,creature_item_count_id,creature_item_count,creature_identified,creature_item_quality_id,creature_item_book_id,creature_item_random_id) VALUES ('$curpcnfo[creature_id]','$gtbidinvtryitmsinst[item_id]','$sllritmcntnwamt[effect_id]','$seller_itm_count_new','1','$gtbidinvtryitmsinstqty[effect_id]','$gtbidinvtryitms[creature_item_book_id]','$gtbidinvtryitms[creature_item_random_id]')") or die ("failed inserting existing seller items.");
				}
					
				$verify_seller_inventory_items = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$curpcnfo[creature_id]' AND item_id = '$gtbidinvtryitmsinst[item_id]' AND creature_item_count_id = '$sllritmcntnwamt[effect_id]' AND creature_item_count = '$seller_itm_count_new' AND creature_item_quality_id = '$gtbidinvtryitmsinstqty[effect_id]' AND creature_identified = '1' AND creature_item_book_id = '$gtbidinvtryitms[creature_item_book_id]' AND creature_item_random_id = '$gtbidinvtryitms[creature_item_random_id]'") or die ("failed checking updated seller items.");
				$vrfysllrinvtryitmscnt = mysql_num_rows($verify_seller_inventory_items);
				if($vrfysllrinvtryitmscnt == 1)
				{
					echo"
				<tr>
					<td colspan='11'>
					<font color = 'purple'>$curpcnfo[creature] now owns ($gtbidinvtry[creature_object_sale_count])($seller_itm_count_new total) of $gtbidinvtryitmsinstqty[effect] quality $gtbidinvtryitmsinst[item].</font><br>
					</td>
				</tr>
					";
				}
				
				// decrement the bidder's items
				$bidder_item_decrement_total = ($gtbidinvtryitms[creature_item_count]-$gtbidinvtry[creature_object_sale_count]);
				$bidder_itm_dec_ttl_amount = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_min_value <= '$bidder_item_decrement_total' AND effect_abbr >= '$bidder_item_decrement_total' AND effect LIKE '%Entit%'") or die ("failed getting traded bid item count tier.");
				$bddritmdecttlamt = mysql_fetch_assoc($bidder_itm_dec_ttl_amount);
				$decrement_bidder_inventory_items = mysql_query("UPDATE ".$slrp_prefix."creature_item SET creature_item_count='$bidder_item_decrement_total',creature_item_count_id='$bddritmdecttlamt[effect_id]' WHERE creature_id = '$gtbidinvtrybddr[creature_id]' AND item_id = '$gtbidinvtryitmsinst[item_id]' AND creature_item_quality_id = '$gtbidinvtryitmsinstqty[effect_id]' AND creature_identified = '1' AND creature_item_book_id = '$gtbidinvtryitms[creature_item_book_id]' AND creature_item_random_id = '$gtbidinvtryitms[creature_item_random_id]'") or die ("failed decremented bidder items.");
						
				// now verify the decrement happened to the bidder's metarials
				$verify_bidder_decrement_items = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_id = '$gtbidinvtrybddr[creature_id]' AND item_id = '$gtbidinvtryitmsinst[item_id]' AND creature_item_count = '$bidder_item_decrement_total' AND creature_item_count_id = '$bddritmdecttlamt[effect_id]' AND creature_item_quality_id = '$gtbidinvtryitmsinstqty[effect_id]' AND creature_identified = '1' AND creature_item_book_id = '$gtbidinvtryitms[creature_item_book_id]' AND creature_item_random_id = '$gtbidinvtryitms[creature_item_random_id]'") or die ("failed checking decremented bidder items.");
				$vrfybddrdecitmcnt = mysql_num_rows($verify_bidder_decrement_items);
				$vrfybddrdecitm = mysql_fetch_assoc($verify_bidder_decrement_items);
				if($vrfybddrdecitmcnt == 1)
				{
					echo"
				<tr>
					<td colspan='11'>
					<font color = 'purple'>$gtbidinvtrybddr[creature] traded ($gtbidinvtry[creature_object_sale_count]) $gtbidinvtryitmsinst[item] to $curpcnfo[creature] ($vrfybddrdecitm[creature_item_count] left)</font><br>
					</td>
				</tr>
					";
				}
			}
		}
	}

	$delete_trade_batch = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id = '$make_trade_batch_id'") or die ("failed deleting batch.");
	$delete_trade_batch_private = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_private WHERE creature_market_batch_id = '$make_trade_batch_id'") or die ("failed deleting batch private.");
	$delete_trade_batch_objects = mysql_query("DELETE FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_market_batch_id = '$make_trade_batch_id'") or die ("failed deleting batch objects.");
	$delete_trade_batch_bids = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_bid WHERE creature_market_batch_id = '$make_trade_batch_id'") or die ("failed deleting batch bids.");
	
	$delete_trade_bid = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id = '$make_trade_bid_id'") or die ("failed deleting batch.");
	$delete_trade_batch_objects = mysql_query("DELETE FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_market_batch_id = '$make_trade_bid_id'") or die ("failed deleting batch objects.");
	
	$delete_empty_inventory_materials = mysql_query("DELETE FROM ".$slrp_prefix."creature_material WHERE creature_material_id > '1' AND creature_material_count <= '0'") or die ("failed removing empty materials entries.");
	$delete_empty_inventory_items = mysql_query("DELETE FROM ".$slrp_prefix."creature_item WHERE creature_item_id > '1' AND creature_item_count <= '0' AND creature_knows_recipe = '0'") or die ("failed removing empty items entries.");
}

if(empty($_POST['make_trade']))
{
	/// clean up unfinished trade batches.
	$cleanup_unfinished_market_batches = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch WHERE market_batch_status_id = '1' and creature_id = '$curpcnfo[creature_id]'") or die ("failed cleaning up temp market batches.");
	$cleanup_unfinished_private_sales = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_private WHERE creature_market_batch_id NOT IN (SELECT creature_market_batch_id FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id > '1')") or die ("failed cleaning up temp private batches.");
	$cleanup_unfinished_objects = mysql_query("DELETE FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_market_batch_id NOT IN (SELECT creature_market_batch_id FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id > '1')") or die ("failed cleaning up temp batch objects.");
	
	$today_hour_next = $today_hour+2;
	if($today_hour_next >= 25)
	{
		$today_hour_next = ($today_hour_next-24);
	}
	
	echo"
				<tr background='themes/RedShores/images/row2.gif' height='9'>
					<td colspan='11'>
					</td>
				</tr>
				<tr>
					<form name = 'new_trade_batch' method = 'post' action = 'modules.php?name=$module_name&file=pc_market_form'>
					<td valign = 'middle' align = 'left' width = '15%'>
						<font color='red' size='2'>
						<b>NEW TRADE OFFER</b> <font size = '1'>to end at
						<br>					
						Yr.: <select class='engine' name = 'end_batch_year'>
						<option value = '$today_year'>$today_year</option>
						<option value = '2013'>2013</option>
						<option value = '2014'>2014</option>
						<option value = '2015'>2015</option>
						</select> Mo.: <select class='engine' name = 'end_batch_month'>
						<option value = '$today_month'>$today_month</option>
						<option value = '01'>Jan</option>
						<option value = '02'>Feb</option>
						<option value = '03'>Mar</option>
						<option value = '04'>Apr</option>
						<option value = '05'>May</option>
						<option value = '06'>Jun</option>
						<option value = '07'>Jul</option>
						<option value = '08'>Aug</option>
						<option value = '09'>Sep</option>
						<option value = '10'>Oct</option>
						<option value = '11'>Nov</option>
						<option value = '12'>Dec</option>
						</select>	Day: <select class='engine' name = 'end_batch_day'>
						<option value = '$today_day'>$today_day</option>
						<option value = '01'>01</option>
						<option value = '02'>02</option>
						<option value = '03'>03</option>
						<option value = '04'>04</option>
						<option value = '05'>05</option>
						<option value = '06'>06</option>
						<option value = '07'>07</option>
						<option value = '08'>08</option>
						<option value = '09'>09</option>
						<option value = '10'>10</option>
						<option value = '11'>11</option>
						<option value = '12'>12</option>
						<option value = '13'>13</option>
						<option value = '14'>14</option>
						<option value = '15'>15</option>
						<option value = '16'>16</option>
						<option value = '17'>17</option>
						<option value = '18'>18</option>
						<option value = '19'>19</option>
						<option value = '20'>20</option>
						<option value = '21'>21</option>
						<option value = '22'>22</option>
						<option value = '23'>23</option>
						<option value = '24'>24</option>
						<option value = '25'>25</option>
						<option value = '26'>26</option>
						<option value = '27'>27</option>
						<option value = '28'>28</option>
						<option value = '29'>29</option>
						<option value = '30'>30</option>
						<option value = '31'>31</option>
						</select>	Hr.: <select class='engine' name = 'end_batch_hour'>
						<option value = '$today_hour_next'>$today_hour_next</option>
						<option value = '01'>01</option>
						<option value = '02'>02</option>
						<option value = '03'>03</option>
						<option value = '04'>04</option>
						<option value = '05'>05</option>
						<option value = '06'>06</option>
						<option value = '07'>07</option>
						<option value = '08'>08</option>
						<option value = '09'>09</option>
						<option value = '10'>10</option>
						<option value = '11'>11</option>
						<option value = '12'>12</option>
						<option value = '13'>13</option>
						<option value = '14'>14</option>
						<option value = '15'>15</option>
						<option value = '16'>16</option>
						<option value = '17'>17</option>
						<option value = '18'>18</option>
						<option value = '19'>19</option>
						<option value = '20'>20</option>
						<option value = '21'>21</option>
						<option value = '22'>22</option>
						<option value = '23'>23</option>
						<option value = '24'>24</option>
						</select>:00
						</font>
					</td>
					<td width='2%'>
					</td>
					<td valign = 'middle' align = 'left' width = '15%'>
						<font color='red' size='2'>
						<b>Anonymous Seller?</b>: <input type='checkbox' value='1' name='anonymous'>
					</td>
					<td width='2%'>
					</td>
					<td valign = 'middle' align = 'left' width = '15%'>
						<input type='hidden' value='edit' name='view_edit'>
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
						<font color='red' size='1'>Private? </font>
						<select class='engine' name='new_private_invitee'>
						<option value = '1'>General Sale</option>
	";
	
	$get_private_character = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id != '$curpcnfo[creature_id]' AND creature_id > '1' AND creature_status_id = '4' ORDER BY creature") or die("failed to get ptivate character.");
	while($gtprvtchrctr = mysql_fetch_assoc($get_private_character))
	{
		echo"<option value = '$gtprvtchrctr[creature_id]'>$gtprvtchrctr[creature]</option>";
	}
	
	echo"
						</select>
					</td>
					<td width = '2%'>
					</td>
					<td width = '15%' valign = 'middle' align = 'right'>
						<font color = 'red' size = '1'>Label: </font><input cols = '20' type = 'text' class='textbox3' name = 'market_batch'>
					</td>
					<td width = '2%'>
					</td>
					<td width = '15%' valign = 'middle' align = 'right'>
						<input type='submit' name='new_trade_batch' value='New Trade Offer'>
					</td>
					</form>
				</tr>
	";
	
	$get_general_batches_for_trade = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id > '1' AND market_batch_private_sale = '0' AND market_batch_status_id = '3' AND creature_id != '$curpcnfo[creature_id]'") or die ("failed getting current pc market batches.");
	$gtgnrlbtchfortrdcnt = mysql_num_rows($get_general_batches_for_trade);
	
	if($gtgnrlbtchfortrdcnt >= 1)
	{
		echo"
					<tr background='themes/RedShores/images/row2.gif' height='9'>
						<td colspan='11'>
						</td>
					</tr> 
					<tr>
						<td valign = 'middle' align = 'left' colspan = '11'>
							<table width = '100%' cellspacing = '0'>
								<tr background='themes/RedShores/images/base1.gif' height='24'>
									<td valign = 'middle' align = 'left' width = '15%'>
										<font size = '2' color = 'red'>
										<b>GENERAL OFFERS</b> (Value)<br>
										</font>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' valign = 'left'>
									<font size = '2' color = 'white'>
									<b>[<font size = '2' color = 'red'>CREATOR</font>]</b>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' valign = 'middle'>
									<font size = '2' color = 'red'>
									<b>CONTENTS</b>
									</font>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' align = 'center' valign = 'middle'>
									<font size = '2' color = 'red'>
									<b>YOUR BIDS</b> (Value)
									</font>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' align = 'center' valign = 'middle'>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' align = 'center' valign = 'middle'>
									<font size = '2' color = 'red'>
									<b>CONTENTS</b>
									</font>
									</td>
								</tr>
							</table>
						</td>
					</tr>
		";
	
		while($gtgnrlbtchfortrd = mysql_fetch_assoc($get_general_batches_for_trade))
		{
			$temp_market_batch_id = $gtgnrlbtchfortrd[creature_market_batch_id];
			include("modules/$module_name/includes/fm_trade_batch.php");
		}
	}
	
	$get_private_batches_for_trade = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch INNER JOIN ".$slrp_prefix."creature_market_batch_private ON ".$slrp_prefix."creature_market_batch.creature_market_batch_id = ".$slrp_prefix."creature_market_batch_private.creature_market_batch_id WHERE ".$slrp_prefix."creature_market_batch.creature_market_batch_id > '1' AND ".$slrp_prefix."creature_market_batch.market_batch_private_sale = '1' AND ".$slrp_prefix."creature_market_batch.market_batch_status_id = '3' AND ".$slrp_prefix."creature_market_batch.creature_id != '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_market_batch_private.private_creature_id = '$curpcnfo[creature_id]'") or die ("failed getting current private market batches.");
	$gtprvtbtchfortrdcnt = mysql_num_rows($get_private_batches_for_trade);
	
	if($gtprvtbtchfortrdcnt >= 1)
	{
		echo"
					<tr background='themes/RedShores/images/row2.gif' height='9'>
						<td colspan='11'>
						</td>
					</tr> 
					<tr>
						<td valign = 'middle' align = 'left' colspan = '11'>
							<table width = '100%' cellspacing = '0'>
								<tr background='themes/RedShores/images/base1.gif' height='24'>
									<td valign = 'middle' align = 'left' width = '15%'>
										<font size = '2' color = 'red'>
										<b>PRIVATE OFFERS to YOU</b> (Value)<br>
										</font>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' valign = 'middle' align = 'left'>
									<font size = '2' color = 'white'>
									<b>[<font size = '2' color = 'red'>CREATOR</font>], <font size = '2' color = 'red'>INVITEES</font></b>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' valign = 'middle'>
									<font size = '2' color = 'red'>
									<b>CONTENTS</b>
									</font>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' align = 'center' valign = 'middle'>
									<font size = '2' color = 'red'>
									<b>YOUR BIDS</b> (Value)
									</font>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' align = 'center' valign = 'middle'>
									</td>
									<td width = '2%'>
									</td>
									<td width = '15%' align = 'center' valign = 'middle'>
									<font size = '2' color = 'red'>
									<b>CONTENTS</b>
									</font>
									</td>
								</tr>
							</table>
						</td>
					</tr>
		";
	
		while($gtprvtbtchfortrd = mysql_fetch_assoc($get_private_batches_for_trade))
		{
			$temp_market_batch_id = $gtprvtbtchfortrd[creature_market_batch_id];
			if($gtprvtbtchfortrd[market_batch_private_sale] == 1)
			{
				$private = 1;
			}
			include("modules/$module_name/includes/fm_trade_batch.php");
		}
	}
	
	$get_current_batches_for_trade = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id > '1' AND market_batch_status_id = '3' and creature_id = '$curpcnfo[creature_id]'") or die ("failed getting current pc market batches.");
	$gtbtchfortrdcnt = mysql_num_rows($get_current_batches_for_trade);
	
	if($gtbtchfortrdcnt >= 1)
	{
		echo"
		<tr background='themes/RedShores/images/row2.gif' height='9'>
			<td colspan='11'>
			</td>
		</tr> 
		<tr>
			<td valign = 'middle' align = 'left' colspan = '11'>
				<table width = '100%' cellspacing = '0'>
					<tr background='themes/RedShores/images/base1.gif' height='24'>
						<td valign = 'middle' align = 'left' width = '15%'>
							<font size = '2' color = 'red'>
							<b>OFFERS from YOU</b> (Value)<br>
							</font>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' valign = 'middle' align = 'left'>
						<font size = '2' color = 'white'>
						<b>[<font size = '2' color = 'red'>CREATOR</font>], <font size = '2' color = 'red'>INVITEES</font></b>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' valign = 'middle'>
						<font size = '2' color = 'red'>
						<b>CONTENTS</b>
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' align = 'center' valign = 'middle'>
						<font size = '2' color = 'red'>
						<b>BIDS</b> (Value)
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' align = 'center' valign = 'middle'>
						<font size = '2' color = 'red'>
						<b>BIDDER</b>
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' align = 'center' valign = 'middle'>
						<font size = '2' color = 'red'>
						<b>CONTENTS<b>
						</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		";
	
		while($gtbtchfortrd = mysql_fetch_assoc($get_current_batches_for_trade))
		{
			$temp_market_batch_id = $gtbtchfortrd[creature_market_batch_id];
			if($gtbtchfortrd[market_batch_private_sale] == 1)
			{
				$private = 1;
			}
			include("modules/$module_name/includes/fm_trade_batch.php");
		}
	}
	
	$get_current_batches_held = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id > '1' AND market_batch_status_id = '0' and creature_id = '$curpcnfo[creature_id]'") or die ("failed getting current pc market batches.");
	$gtbtchhldcnt = mysql_num_rows($get_current_batches_held);
	
	if($gtbtchhldcnt >= 1)
	{
		echo"
		<tr background='themes/RedShores/images/row2.gif' height='9'>
			<td colspan='11'>
			</td>
		</tr> 
		<tr>
			<td valign = 'middle' align = 'left' colspan = '11'>
				<table width = '100%' cellspacing = '0'>
					<tr background='themes/RedShores/images/base1.gif' height='24'>
						<td valign = 'middle' align = 'left' width = '15%'>
							<font size = '2' color = 'red'>
							<b>BANKED</b> (Value)<br>
							</font>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' valign = 'middle' align = 'left'>
						<font size = '2' color = 'white'>
						<b>[<font size = '2' color = 'red'>CREATOR</font>], <font size = '2' color = 'red'>INVITEES</font></b>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' valign = 'middle'>
						<font size = '2' color = 'red'>
						<b>CONTENTS</b>
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' align = 'center' valign = 'middle'>
						<font size = '2' color = 'red'>
						<b>BIDS</b> (Value)
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' align = 'center' valign = 'middle'>
						<font size = '2' color = 'red'>
						<b>BIDDER</b>
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td width = '15%' align = 'center' valign = 'middle'>
						<font size = '2' color = 'red'>
						<b>CONTENTS</b>
						</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		";
	
		while($gtbtchhld = mysql_fetch_assoc($get_current_batches_held))
		{
			$temp_market_batch_id = $gtbtchhld[creature_market_batch_id];
			if($gtbtchhld[market_batch_private_sale] == 1)
			{
				$private = 1;
			}
			include("modules/$module_name/includes/fm_trade_batch.php");
		}
	}
}

echo"
			<tr background='themes/RedShores/images/row2.gif' height='9'>
				<td colspan='11'>
				</td>
			</tr>
			<tr background='themes/RedShores/images/base1.gif' height='24'>
			";
			
			if(isset($_POST['make_trade']))
			{
				echo"
				<form name = 'market' method='post' action = 'modules.php?name=$module_name&file=pc_market'>
				<td valign = 'middle' align = 'left' width = '18%'>
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
					<input type='submit' value='Back to Market' name='go_market'>
				</td>
				</form>
				<td width = '2%'>
				</td>
				";
			}
			
			echo"
				<form name = 'pc_production' method='post' action = 'modules.php?name=$module_name&file=pc_prod'>
				<td valign = 'middle' align = 'left' width ='15%'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$admin_expander' name = 'admin_expander'>
					<input type='submit' value='Go to Production'>
					</font>
				</td>
				</form>
				<td width = '2%'>
				</td>
				<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
				<td valign = 'middle' colspan = '9' width = '100%' align = 'right'>
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