<?php
if (!eregi("modules.php", $PHP_SELF)) {
  die ("You can't access this file directly...");
}
$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("header.php");
$nav_title = "Manage Production";
include("modules/$module_name/includes/slurp_header.php");
include("modules/$module_name/includes/fn_game_nfo.php");

if(isset($_POST['create_batch']))
{
	$create_type = $_POST['create_type'];
	$create_batch = $_POST['create_batch'];
	$create_limit = $_POST['create_limit'];
	
	if($create_type == "item")
	{
		$batch_cost = $_POST['batch_cost'];
		$batch_tier = $_POST['batch_tier'];
	}
	
	if($create_type == "mtl")
	{
		$batch_mtl_tier = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id = '$create_batch'") or die ("failed getting batch mtl tier.");
		$btchmtltr = mysql_fetch_assoc($batch_mtl_tier);
		$batch_cost = $btchmtltr[material_tier];
		$batch_tier = $btchmtltr[material_tier];
		$batch_geo_subtype = $_POST['batch_geo_subtype'];
	}
	  // echo"cost: $batch_cost, tier: $batch_tier, $create_type id: $create_batch, cr_lim: $create_limit<br>";
	if($create_type == "item")
	{
		$insert_default_batch = mysql_query("INSERT INTO ".$slrp_prefix."creature_batch (creature_id,item_id,material_id,batch_count,batch_count_value,batch_quality_id,batch_geography_subtype_id,batch_tier,batch_prp,batch_time,batch_count_default,batch_quality_default,batch_time_default,batch_prp_default,batch_event_id) VALUES ('$curpcnfo[creature_id]','$create_batch','1','635','1','667','1','$batch_tier','$batch_cost','648','635','667','648','$batch_cost','$slrpnfo[slurp_next_game_id]')") or die ("failed starting new batch.");
		$created_batch = mysql_query("SELECT * FROM ".$slrp_prefix."creature_batch WHERE item_id = '$create_batch' AND creature_id = '$curpcnfo[creature_id]' AND batch_status = '2' AND batch_quality_id = '667' AND batch_time = '648' AND batch_count = '635'") or die ("failed getting created batch item info.");

	}
	if($create_type == "mtl")
	{
		$insert_default_batch = mysql_query("INSERT INTO ".$slrp_prefix."creature_batch (creature_id,item_id,material_id,batch_count,batch_count_value,batch_quality_id,batch_geography_subtype_id,batch_tier,batch_prp,batch_time,batch_count_default,batch_quality_default,batch_time_default,batch_prp_default,batch_event_id) VALUES ('$curpcnfo[creature_id]','1','$create_batch','635','1','667','$batch_geo_subtype','$batch_tier','$batch_cost','648','635','667','648','$batch_cost','$slrpnfo[slurp_next_game_id]')") or die ("failed starting new batch.");
		$created_batch = mysql_query("SELECT * FROM ".$slrp_prefix."creature_batch WHERE material_id = '$create_batch' AND creature_id = '$curpcnfo[creature_id]' AND batch_status = '2' AND batch_quality_id = '667' AND batch_time = '648' AND batch_count = '635'") or die ("failed getting created batch mtl info.");
	}
	
	$crbtch = mysql_fetch_assoc($created_batch);

	if($create_type == "item")
	{
		$default_item_materials_67 = mysql_query("SELECT * FROM ".$slrp_prefix."material INNER JOIN ".$slrp_prefix."item_core_material ON ".$slrp_prefix."item_core_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."item_core_material.item_id = '$create_batch'") or die ("failed getting created item materials 67.");
		$dfitmmats67cnt = mysql_num_rows($default_item_materials_67);
		while($dfitmmats67 = mysql_fetch_assoc($default_item_materials_67))
		{
			$get_batch_units_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."item_core_material ON ".$slrp_prefix."item_core_material.item_core_material_unit = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."item_core_material.material_id = '$dfitmmats67[material_id]' AND ".$slrp_prefix."item_core_material.item_id = '$create_batch'") or die ("failed getting units list for df core materials.");
			$gtbtchuntslst = mysql_fetch_assoc($get_batch_units_list);
			$get_batch_units_count = mysql_query("SELECT * FROM ".$slrp_prefix."item_core_material WHERE item_id = '$create_batch' AND material_id = '$dfitmmats67[material_id]'") or die ("failed getting count for df core materials.");
			$gtbtchuntscount = mysql_fetch_assoc($get_batch_units_count);
			
			$insert_default_batch_ingredients = mysql_query("INSERT INTO ".$slrp_prefix."creature_batch_ingredients (creature_batch_id,material_id,material_unit,material_count,original_material_id) VALUES ('$crbtch[creature_batch_id]','$dfitmmats67[material_id]','$dfitmmats67[material_default_unit_id]','$gtbtchuntslst[effect_abbr]','$dfitmmats67[material_id]')") or die ("failed starting new batch ingredients.");
		}
	}
	
	$created_batch_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_batch WHERE creature_batch_id = '$crbtch[creature_batch_id]'") or die ("failed getting created batch item info.");
}

if(isset($_POST['current_batch']))
{
	// passing pc_batch_id from this form to itself
	$current_batch = $_POST['current_batch'];
	$posted_batch_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_batch WHERE creature_batch_id = '$current_batch'") or die ("failed getting posted batch item info.");
	$pstdbtchnfo = mysql_fetch_assoc($posted_batch_info);

	if($create_type == "item")
	{
		$batch_item = $pstdbtchnfo[item_id];	
	}
	if($create_type == "mtl")
	{
		$batch_item = $pstdbtchnfo[material_id];	
	}
	
	if(isset($_POST['batch_count']))
	{	
		$batch_count = $_POST['batch_count'];
	}
	else
	{
		$batch_count = $pstdbtchnfo[batch_count];	
	}
	
		if(isset($_POST['effect_value']))
	{	
		$effect_value = $_POST['effect_value'];
	}
	else
	{
		$effect_value = $pstdbtchnfo[batch_count_value];	
	}
	
	if(isset($_POST['batch_quality']))
	{	
		$batch_quality = $_POST['batch_quality'];
	}
	else
	{
		$batch_quality = $pstdbtchnfo[batch_quality_id];	
	}

	if(isset($_POST['batch_tier']))
	{	
		$batch_tier = $_POST['batch_tier'];
	}
	else
	{
		$batch_tier = $pstdbtchnfo[batch_tier];	
	}

	if(isset($_POST['batch_cost']))
	{	
		$batch_cost = $_POST['batch_cost'];
	}
	else
	{
		$batch_cost = $pstdbtchnfo[batch_prp];	
	}

	if(isset($_POST['batch_status']))
	{	
		$batch_status = $_POST['batch_status'];
	}
	else
	{
		$batch_status = $pstdbtchnfo[batch_status];	
	}
	
	if(isset($_POST['batch_time']))
	{	
		$batch_time = $_POST['batch_time'];
	}
	else
	{
		$batch_time = $pstdbtchnfo[batch_time];	
	}
	
	// echo"time: $batch_time, cost: $batch_cost, status: $batch_status qty: $batch_quality count: $batch_count_value<br>";
	$update_current_batch = mysql_query("UPDATE ".$slrp_prefix."creature_batch SET batch_count = '$batch_count',batch_count_value = '$effect_value',batch_quality_id = '$batch_quality',batch_tier = '$batch_tier',batch_prp = '$batch_cost',batch_status = '$batch_status',batch_time='$batch_time' WHERE creature_batch_id = '$current_batch'") or die ("failed updating current batch.");
	$created_batch_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_batch WHERE creature_batch_id = '$current_batch'") or die ("failed getting created batch item info.");
}

$crbtchnfo = mysql_fetch_assoc($created_batch_info);
$crbtchnfocnt = mysql_num_rows($created_batch_info);

// echo"$crbtchnfo[creature_batch_id], $crbtchnfo[item_id], $crbtchnfo[batch_status], $crbtchnfo[batch_quality_id]<br>";
if($create_type == "item")
{
	$current_batch_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$crbtchnfo[item_id]'") or die ("failed getting current batch item info.");
	$currbtchnfo = mysql_fetch_assoc($current_batch_item_info);
}
if($create_type == "mtl")
{
	$current_batch_mtl_info = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id = '$crbtchnfo[material_id]'") or die ("failed getting current batch mtl info.");
	$currbtchnfo = mysql_fetch_assoc($current_batch_mtl_info);
}

$currbtchprpdiff = $crbtchnfo[batch_prp] - $crbtchnfo[batch_prp_default];

$current_batch_count_default = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$crbtchnfo[batch_count_default]'") or die ("failed getting current batch count nfo.");
$currbtchcntdf = mysql_fetch_assoc($current_batch_count_default);

$current_batch_count_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$crbtchnfo[batch_count]'") or die ("failed getting current batch count nfo.");
$currbtchcntnfo = mysql_fetch_assoc($current_batch_count_info);
$currbtchcntdiff = $currbtchcntnfo[effect_tier] - $currbtchcntdf[effect_tier];

// $current_batch_qty_default = mysql_query("SELECT * FROM ".$slrp_prefix."quality WHERE quality_id = '$crbtchnfo[batch_quality_default]'") or die ("failed getting current batch count nfo.");
// $currbtchqtydf = mysql_fetch_assoc($current_batch_qty_default);
$current_batch_qty_default = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$crbtchnfo[batch_quality_default]'") or die ("failed getting current batch quality default nfo.");
$currbtchqtydf = mysql_fetch_assoc($current_batch_qty_default);

// $current_batch_qty_info = mysql_query("SELECT * FROM ".$slrp_prefix."quality WHERE quality_id = '$crbtchnfo[batch_quality_id]'") or die ("failed getting current batch quality nfo.");
// $currbtchqtynfo = mysql_fetch_assoc($current_batch_qty_info);
$current_batch_qty_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$crbtchnfo[batch_quality_id]'") or die ("failed getting current batch quality nfo.");
$currbtchqtynfo = mysql_fetch_assoc($current_batch_qty_info);
$currbtchqtydiff = $currbtchqtynfo[effect_tier] - $currbtchqtydf[effect_tier];
$currbtchqtydiffvis = $currbtchqtydiff + 2;

$current_batch_status_info = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_status_id = '$crbtchnfo[batch_status]'") or die ("failed getting current batch status nfo.");
$currbtchstatnfo = mysql_fetch_assoc($current_batch_status_info);

// $current_batch_time_default = mysql_query("SELECT * FROM ".$slrp_prefix."time_chart WHERE time_chart_id = '$crbtchnfo[batch_time_default]'") or die ("failed getting current batch count nfo.");
// $currbtchtmdf = mysql_fetch_assoc($current_batch_time_default);
$current_batch_time_default = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$crbtchnfo[batch_time_default]'") or die ("failed getting current batch time default nfo.");
$currbtchtmdf = mysql_fetch_assoc($current_batch_time_default);

// $current_batch_time_info = mysql_query("SELECT * FROM ".$slrp_prefix."time_chart WHERE time_chart_id = '$crbtchnfo[batch_time]'") or die ("failed getting current batch time nfo.");
// $currbtchtmnfo = mysql_fetch_assoc($current_batch_time_info);
$current_batch_time_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$crbtchnfo[batch_time]'") or die ("failed getting current batch time nfo.");
$currbtchtmnfo = mysql_fetch_assoc($current_batch_time_info);
$currbtchtmdiff = $currbtchtmdf[effect_tier] - $currbtchtmnfo[effect_tier];

if($create_type == "item")
{
	$start_batch_name = stripslashes(strip_tags($currbtchnfo[item]));
}
if($create_type == "mtl")
{
	$start_batch_name = stripslashes(strip_tags($currbtchnfo[material]));
}
$total_time_penalty = $currbtchqtydiff + $currbtchcntdiff;
$currbtchttldiff = $currbtchtmdiff + $currbtchqtydiff + $currbtchcntdiff;
$batch_total = $crbtchnfo[batch_prp_default] + $currbtchttldiff;

// echo"time_penalty = $total_time_penalty; batch total = $batch_total<br>";

if($batch_total <= 0)
{
	$batch_total = 1;
}

echo"
<tr>
	<td valign = 'top' align = 'left' colspan = '3'>
		<table width = '100%'>
			<tr>
				<form name = 'show_hide_instructions' method='post' action = 'modules.php?name=$module_name&file=pc_prod_batch'>
				<td valign = 'top' align = 'right' width = '49%'>
";

if($ntro_expander == 1)
{
	echo"
				<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
				<input type='hidden' value='$crbtchnfo[creature_batch_id]' name = 'current_batch'>
				<input type='hidden' value='$admin_expander' name = 'admin_expander'>
				<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
				<input type='hidden' value='$compab_expander' name = 'compab_expander'>
				<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
				<input type='hidden' value='$items_expander' name = 'items_expander'>
				<input type='hidden' value='$materials_expander' name = 'materials_expander'>
				<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
				<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
				<input type='hidden' value='$create_limit' name = 'create_limit'>
				<input type='hidden' value='$create_type' name = 'create_type'>
				<input type='hidden' value='0' name = 'ntro_expander'>
				<input type='submit' value='Hide Instructions' name='show_hide_instructions'>
	";
}

if($ntro_expander == 0)
{
	echo"
				<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
				<input type='hidden' value='$crbtchnfo[creature_batch_id]' name = 'current_batch'>
				<input type='hidden' value='$admin_expander' name = 'admin_expander'>
				<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
				<input type='hidden' value='$compab_expander' name = 'compab_expander'>
				<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
				<input type='hidden' value='$items_expander' name = 'items_expander'>
				<input type='hidden' value='$materials_expander' name = 'materials_expander'>
				<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
				<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
				<input type='hidden' value='$create_limit' name = 'create_limit'>
				<input type='hidden' value='$create_type' name = 'create_type'>
				<input type='hidden' value='1' name = 'ntro_expander'>
				<input type='submit' value='Show Instructions' name='show_hide_instructions'>
	";
}

echo"		</td>
				<td width = '2%'>
				</td>
				</form>
				<td valign = 'top' align = 'left' width = '49%'>
				<form name = 'pc_edit' method='post' action = 'modules.php?name=$module_name&file=pc_edit'>
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
				<input type='hidden' value='$create_type' name = 'create_type'>
				<input type='submit' value='Back to $curpcnfo[creature]' name='to_pc_edit'>
				</font>
				
				</td>
				</form>
			</tr>
			<tr>
				</form>
				<td valign = 'top' align = 'right' width = '49%'>

				</td>
				<td width = '2%'>
				</td>
				<form name = 'back_to_pc_prod' method='post' action = 'modules.php?name=$module_name&file=pc_prod'>				
				<td valign = 'top' align = 'left' width = '49%'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$component_expander' name = 'component_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='$create_type' name = 'create_type'>
					<input type='submit' value='Back to Production' name='back_to_pc_prod'>
				</td>
				</form>
			</tr>
		</table>
	</td>	
	<td valign = 'top' align = 'left' colspan = '5'>
		<table width = '100%'>
";

$pcid = $curpcnfo[creature_id];
$boxed = 1;
$prp_hidden = 0;
include("modules/$module_name/includes/fn_prod_pts.php");

echo"
		</table>
	</td>
</tr>
<tr>
	<td width = '100%' colspan = '9' valign = 'top' align = 'center'>
";

if($ntro_expander == 1)
{
	echo"
					<font color ='orange' size = '2'>
					<b>Orange indicates the recipe default ingredient and quantity.</font><br>
					<font color ='#33F406' size = '2'>
					<b>Bright green indicates an ingredient that will be used in this batch.</font><br>
					<font color ='red' size = '2'>
					Red indicates an insufficient quantity for this batch. </b>
					</font>
	";
}

echo"
	</td>
</tr>
";

if($ntro_expander == 1)
{
	echo"
<tr>
	<td valign = 'top' align = 'left' colspan = '9'>
		<table width = '100%'>
			<tr>
				<td valign = 'top' align = 'center' colspan = '5'>
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
					<br>
					<b> Free Trade Harvesting: <font color = 'white'>(by Terrain)</b>
					<li> Tier I and II Free Trade Items only
					<li> +1 Prod Point per Material Tier (+1 TC) 
					<li> Subtract 1 Tier from Batch Time (-1 TC)
					<li> +1 Tier (Double) Batch Size (+1 TC)</font>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left'>
					<font size = '2' color = '#7fffd4'>
					<br>
					<b> Harvesting: <font color = 'white'>(non-Free Trade Items, by Terrain)</b>
					<li> Up to Harvester's Production Tier
					<li> +1 Prod Point per Material Tier (+1 TC)
					<li> +1 Tier (Double) Batch Size (+1 TC)
					<li> Harvesting Tools (-1 TC)</font>
				</td>
			</tr>
			<tr>
				<td valign = 'top' align = 'left' colspan = '5'>
					<font size = '2' color = '#7fffd4'>
					<br>
					<b> Crafting <font color = 'white'>Default is 1 Item of Standard (QTY II) in 24 hours,
					<br> +1 Time Chart Tier per DRB, -1 Time Chart Tier for Each appropriate Craft Skill Tier.</b>
					<li> +1 Item Durability (DRB) above the default (up to Crafter Craft Tier) (+1 TC)
					<li> +2 Item Capacity above the default (up to Crafter Craft Tier) (+1 TC)
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
	</td>

	<td width = '2%'>
	
	</td>
</tr>
	";
}

if($crbtchnfocnt >= 1)
{
	echo"
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan='9'>
	</td>
</tr> 
<tr>
	<td colspan = '";
	
	if($create_type == "item")
	{
		echo"6";	
	}
	
	if($create_type == "mtl")
	{
		echo"4";	
	}
	
	echo"
	'>
		<font color = 'yellow' size = '2'><b>
	";
	
	if($create_type == "item")
	{
		echo"
		Batch: <font color = '#33F406'>
		$currbtchnfo[item]</font> ";
		$default_item_materials = mysql_query("SELECT * FROM ".$slrp_prefix."material INNER JOIN ".$slrp_prefix."item_core_material ON ".$slrp_prefix."item_core_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."item_core_material.item_id = '$currbtchnfo[item_id]'") or die ("failed getting created item materials.");
		$dfitmmatscnt = mysql_num_rows($default_item_materials);
		while($dfitmmats = mysql_fetch_assoc($default_item_materials))
		{
			$get_batch_units_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."item_core_material ON ".$slrp_prefix."item_core_material.item_core_material_unit = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."item_core_material.material_id = '$dfitmmats[material_id]' AND ".$slrp_prefix."item_core_material.item_id = '$currbtchnfo[item_id]'") or die ("failed getting units list for df core materials.");
			$gtbtchuntslst = mysql_fetch_assoc($get_batch_units_list);
			
			$get_batch_units_count = mysql_query("SELECT * FROM ".$slrp_prefix."item_core_material WHERE item_id = '$currbtchnfo[item_id]' AND material_id = '$dfitmmats[material_id]'") or die ("failed getting count for df core materials.");
			$gtbtchuntscount = mysql_fetch_assoc($get_batch_units_count);
			
			echo" [<font color ='orange'>$gtbtchuntscount[item_core_material_count] $gtbtchuntslst[effect_abbr] $dfitmmats[material]</font>]";
		}
	}
	
	if($create_type == "mtl")
	{
		echo"
	Harvest: <font color = '#33F406'>
	$currbtchnfo[material]</font>
	<br>
	Status: <font color = 'orange'>$currbtchstatnfo[slurp_status]</font>
	</b></font>
</td>
		";
	}
		
	$batch_tier_default =1;
	$batch_tier_temp = $currbtchcntnfo[effect_tier];
	if($batch_tier_temp == 1)
	{
		$batch_tier_default = 1;
		// echo"size count: $batch_tier_default, cr_lim: $create_limit<br>";
	}
	if($batch_tier_temp >= 2)
	{
		while($batch_tier_temp >= 2)
		{
			$batch_tier_default = ($batch_tier_default*2);
			// echo"size count: $batch_tier_default, cr_lim: $create_limit<br>";
			$batch_tier_temp--;
		}
	}
	
	echo"
	<td align = 'right'>
	<font color = 'yellow' size = '2'><b>
	Base PrP Cost: <br><font color = '#33F406'>$crbtchnfo[batch_prp_default] PrP
	</font>
	</b></font>
</td>
<tr>

<tr>
<td width = '18%' valign = 'top' align = 'left'>
	<font color = 'yellow' size = '2'><b>
	Count: <font color = '#33F406'>$crbtchnfo[batch_count_value] (<font color = 'orange'>$currbtchcntdiff PrP</font>/<font color = 'orange'>+$currbtchcntdiff Time</font>)</font>
	</b></font><br>";
	
//		echo"<select class='engine' name = 'adjust_batch_count'>";
	
	$batch_count_temp = $currbtchcntdf[effect_tier] + $size_subtotal;
	
	$batch_siz_dropdown = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect_effect_subtype.effect_id = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."effect.effect_id > '1' AND ".$slrp_prefix."effect.effect_tier <= '$batch_count_temp' AND ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '37' AND ".$slrp_prefix."effect.effect_desc LIKE '%Entit%' AND ".$slrp_prefix."effect.effect_slurp_id = '$slrpnfo[slurp_id]' ORDER BY ".$slrp_prefix."effect.effect_tier ") or die ("failed getting current batch count nfo.");
	while($btchszdrpdn = mysql_fetch_assoc($batch_siz_dropdown))
	{
		$batch_count_tier_default = 1;
		$effect_tier_temp = $btchszdrpdn[effect_tier];
		$effect_min_value = $btchszdrpdn[effect_min_value];
		$effect_max_value = $btchszdrpdn[effect_abbr];
		while($effect_tier_temp >= 2)
		{
			$batch_count_tier_default = ($batch_count_tier_default*2);
			// echo"size count: $batch_count_tier_default, cr_lim: $create_limit<br>";
			$effect_tier_temp--;
		}
		
		while($effect_min_value <= $effect_max_value)
		{
			$batch_count_prp = $btchszdrpdn[effect_tier] - $currbtchcntdf[effect_tier];
			if($create_limit >= $effect_min_value)
			{
				if($batch_count_prp <= ($prod_points_left - $batch_total))
				{			
					echo"
					<p>
					<form name = 'edit_batch_size' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod_batch'>
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
					<input type='hidden' value='$create_type' name = 'create_type'>
					<input type='hidden' value='$create_limit' name = 'create_limit'>
					<input type='hidden' value='$btchszdrpdn[effect_id]' name = 'batch_count'>
					<input type='hidden' value='$effect_min_value' name = 'effect_value'>
					<input type='hidden' value='$crbtchnfo[creature_batch_id]' name = 'current_batch'>
					<input type='submit' value='$batch_count_prp PrP/+$batch_count_prp Time:  (".roman($btchszdrpdn[effect_tier]).") $effect_min_value' name='change_batch_count'>
					</form>
					";
				}
			}
			
			$effect_min_value++;
		}
//			echo"<option value = '$btchszdrpdn[effect_id]'>$batch_count_prp PrP/+$btchszdrpdn[effect_tier] Time:  (".roman($btchszdrpdn[effect_tier]).") $btchszdrpdn[effect]</option>";
	}
	
//		echo"</select>";
	
	echo"
	</td>
	";
	
	if($create_type == "item")
	{
		echo"
		<td width = '2%'>
		</td>
		<td width = '18%' valign = 'top' align = 'left'>
			<font color = 'yellow' size = '2'><b>
			 Quality: <font color = '#33F406'>$currbtchqtynfo[effect] (<font color = 'orange'>$currbtchqtydiff PrP</font>/<font color = 'orange'>+$currbtchqtydiff Time</font>)</font>
			</b></font><br>
		";
			
		// echo"select name = 'adjust_batch_qty'>";
		$batch_qty_temp = $currbtchqtydf[effect_tier] + $production_subtotal;
		
		// $batch_qty_dropdown = mysql_query("SELECT * FROM ".$slrp_prefix."quality WHERE quality_id > '1' AND quality_id != '$crbtchnfo[batch_quality_id]' AND quality_tier <= '$batch_qty_temp' ORDER BY quality_tier ASC") or die ("failed getting current batch quality nfo.");
		$batch_qty_dropdown = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect_effect_subtype.effect_id = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."effect.effect_id > '1' AND ".$slrp_prefix."effect.effect_tier <= '$batch_qty_temp' AND ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '39' AND ".$slrp_prefix."effect.effect_desc NOT LIKE '%+%' AND ".$slrp_prefix."effect.effect_slurp_id = '$slrpnfo[slurp_id]' ORDER BY ".$slrp_prefix."effect.effect_tier ") or die ("failed getting current batch count nfo.");
		while($btchqtydrpdn = mysql_fetch_assoc($batch_qty_dropdown))
		{
			$batch_qty_prp = $btchqtydrpdn[effect_tier]-2;
			
			$batch_qty_time_penalty = $batch_qty_prp;
			$batch_qty_tier_visible = $btchqtydrpdn[effect_tier];
				
			echo"
			<p>
			<form name = 'edit_batch_qty' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod_batch'>
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
			<input type='hidden' value='$create_type' name = 'create_type'>
			<input type='hidden' value='$create_limit' name = 'create_limit'>
			<input type='hidden' value='$btchqtydrpdn[effect_id]' name = 'batch_quality'>
			<input type='hidden' value='$crbtchnfo[creature_batch_id]' name = 'current_batch'>
			<input type='submit' value='$batch_qty_prp PrP/+$batch_qty_prp Time:  (".roman($batch_qty_tier_visible).") $btchqtydrpdn[effect]' name='change_batch_qty'>
			</form>
			";
			// echo"<option value = '$btchqtydrpdn[effect_id]'>$batch_qty_prp PrP/+$btchqtydrpdn[effect_tier] Time: (".roman($batch_qty_tier_visible).") $btchqtydrpdn[effect]</option>";
		}
	}
	
	// echo"</select>";
	
	// echo"time_tier: $currbtchtmnfo[effect_tier] default: $currbtchtmdf[effect_tier]  total_penalty: ($currbtchcntdiff) $total_time_penalty<br>";
	$time_adjusted = $currbtchtmdf[effect_tier] + $total_time_penalty;
	
	// time starts at Tier 9 (1 day) and works down or up from there
	$batch_time_max = ($time_adjusted + $time_subtotal);
	$batch_time_min = ($time_adjusted - $time_subtotal);
	$time_limits = $currbtchtmnfo[effect_tier] + $total_time_penalty;
	
	$batch_time_adjusted_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."effect_effect_subtype.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '26' AND ".$slrp_prefix."effect.effect_tier = '$time_limits'") or die ("failed getting adjusted batch time nfo.");
	$btchtmadjnfo = mysql_fetch_assoc($batch_time_adjusted_info);
	
	echo"
	</td>
	";
	
	echo"
	<td width = '2%'>
	</td>
	<td width = '18%' valign = 'top' align = 'left'>
		<font color = 'yellow' size = '2'><b>
		Time: <font color = '#33F406'>$btchtmadjnfo[effect] (<font color = 'orange'>$currbtchtmdiff PrP</font>)
		</b></font><br>
	";
		
	// echo"<select class='engine' name = 'adjust_batch_time'>";
	
	$batch_time_dropdown = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."effect_effect_subtype.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '26' AND ".$slrp_prefix."effect.effect_tier >= '$batch_time_min' AND ".$slrp_prefix."effect.effect_tier <= '$batch_time_max' ORDER BY ".$slrp_prefix."effect.effect_tier DESC") or die ("failed getting current batch time nfo.");
	while($btchtmdrpdn = mysql_fetch_assoc($batch_time_dropdown))
	{
		$batch_time_tier = $btchtmdrpdn[effect_tier] - $total_time_penalty; 
		$batch_tier_adjust = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."effect_effect_subtype.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '26' AND ".$slrp_prefix."effect.effect_tier = '$batch_time_tier'") or die ("failed getting adjusted batch time nfo."); 
		$btchtradj = mysql_fetch_assoc($batch_tier_adjust);
		
		$time_prp_time = $btchtradj[effect_tier] - $currbtchtmdf[effect_tier];
		// echo "$batch_time_tier = $btchtmdrpdn[effect_tier] - $total_time_penalty<br>";
		// echo "$time_prp_time = $btchtradj[effect_tier] - $currbtchtmdf[effect_tier]<br>";
		$time_prp_cost = -($time_prp_time);
		
		if($time_prp_cost <= ($prod_points_left - $batch_total))
		{	
			echo"
			<p>
			<form name = 'edit_batch_prp' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod_batch'>
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
			<input type='hidden' value='$create_type' name = 'create_type'>
			<input type='hidden' value='$create_limit' name = 'create_limit'>
			<input type='hidden' value='$btchtradj[effect_id]' name = 'batch_time'>
			<input type='hidden' value='$crbtchnfo[creature_batch_id]' name = 'current_batch'>
			<input type='submit' value='$time_prp_cost PrP/+$time_prp_time Time:  (".roman($btchtmdrpdn[effect_tier]).") $btchtmdrpdn[effect]' name='change_batch_time'>
			</form>
			";
		}
		// echo"<option value = '$btchtmdrpdn[time_chart_id]'>$time_prp_cost PrP/$time_prp_cost Time:  (".roman($btchtmdrpdn[time_chart_tier]).") $btchtmdrpdn[time_chart]</option>";
	}
	
	// echo"</select>";
	
	if(empty($batch_count_tier_default))
	{
		$batch_count_tier_default = 1;
	}
	
	echo"
	</td>
	<td width = '2%'>
	</td>
	<td width = '18%' valign = 'top' align = 'right'>
		<font color = 'orange' size = '2'><b>
		+ $currbtchttldiff PrP
		<hr>
		= <font size = '4' color = '#33F406'>$batch_total PrP
		</b></font></font></font>
		<br>
		<br>
		<form name = 'save_batch' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod'>
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
		<input type='hidden' value='$create_type' name = 'create_type'>
		<input type='hidden' value='$crbtchnfo[creature_batch_id]' name = 'saved_batch'>
		<input type='hidden' value='$crbtchnfo[batch_count_value]' name = 'effect_value'>
		<input type='hidden' value='$crbtchnfo[batch_count]' name = 'saved_batch_count'>
		<input type='hidden' value='$batch_tier_default' name = 'saved_batch_multiplier'>
		<input type='hidden' value='$crbtchnfo[batch_quality_id]' name = 'saved_batch_quality'>
		<input type='hidden' value='$btchtmadjnfo[effect_id]' name = 'saved_batch_time'>
		<input type='hidden' value='$batch_total' name = 'saved_batch_prp'>
		";
		
//		// insert maker's mark checkbox
//		if($create_type == "item")
//		{
//			$check_makers_mark = mysql_query("SELECT * FROM ".$slrp_prefix."creature_ability WHERE creature_id = '$curpcnfo[creature_id]' AND ability_id = '101'") or die ("Failed checking maker's mark.");
//			$chkmkrsmrkcnt = mysql_num_rows($check_makers_mark);
//			if($chkmkrsmrkcnt == 1)
//			{
//				echo"<font size = '2' color = 'red'><b>Mark Item(s)?</b></font> <input type='checkbox' value='1' name='batch_marked'><br><br>";
//			}
//		}
		
		echo"
		<input type='submit' value='Start ";
		
		if($create_type == "item")
		{
			echo"Crafting";
		}
		if($create_type == "mtl")
		{
			echo"Harvesting";
		}
		
		echo"' name='save_batch'>
		</form>
	";
	
	// echo"now: $now<br>";

	// echo"interval: ";
	$interval = "+$btchtmadjnfo[effect]";
	$date_now = new DateTime($now);
	$date_now->modify($interval);
	// echo $date_now->format('Y-m-d H:i:s')"<br>$btchtmadjnfo[effect_abbr]<br>";
  $batch_estimated_end = date_format($date_now, 'D, j M, g:i A');
	
	echo"
		<br>
		<font color = 'yellow'>Completion Estimate:
		<br>
		<font color = '#33F406'>$batch_estimated_end</font></font>
		<br>
		";
		
		echo"
		<form name = 'reset_batch' method = 'post' action = 'modules.php?name=$module_name&file=pc_prod_batch'>
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
			<input type='hidden' value='$create_limit' name = 'create_limit'>
			<input type='hidden' value='$create_type' name = 'create_type'>
			<input type='hidden' value='$crbtchnfo[batch_count_default]' name = 'batch_count'>
			<input type='hidden' value='$crbtchnfo[batch_quality_default]' name = 'batch_quality'>
			<input type='hidden' value='$crbtchnfo[batch_time_default]' name = 'batch_time'>
			<input type='hidden' value='$crbtchnfo[creature_batch_id]' name = 'current_batch'>
			<input type='submit' value='Reset Batch' name='reset_batch'>
		</form>
	</td>
	<td width = '2%'>
	</td>
	<td width = '18%' valign = 'middle' align = 'left'>
		<font color = 'yellow' size = '2'><b>
		</font>
		</b>
	</td>
<tr>
<tr>
	";
}

include("modules/$module_name/includes/slurp_footer.php");
?>