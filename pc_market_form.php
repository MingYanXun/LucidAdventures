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

// view or edit mode
if(isset($_POST['view_edit']))
{
	$view_edit = $_POST['view_edit'];
}
if(empty($_POST['view_edit']))
{
	if(isset($_POST['edit']))
	{
		$post_edit = $_POST['edit'];
		if($post_edit == 1)
		{
			$view_edit = "edit";
		}
		if($post_edit == 0)
		{
			$view_edit = "view";
		}
	}
}
// echo"<tr><td>$post_edit = $view_edit</td></tr>";

// delete batches
if(isset($_POST['del_batch_id']))
{
	$creature_market_batch_id = $_POST['del_batch_id'];
	
	$to_be_deleted_trade_batch = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed checkign market batches for dupes.");
	$delbtch = mysql_fetch_assoc($to_be_deleted_trade_batch);
	
	echo"
<tr>
	<form name = 'batch_del' method='post' action = 'modules.php?name=$module_name&file=pc_market'>
	<td valign = 'top' align = 'left' colspan = '11' width = '100%'>
		<font size = '2' color = 'red'>
		Are you certain you want to delete the trade batch <i>$delbtch[market_batch]</i>?
		</font>
		<br>
		<br>
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
		<input type = 'hidden' value = '$delbtch[creature_market_batch_id]' name = 'del_batch_id'>
		<input type = 'submit' name = 'nevermind' value = 'Yes, Delete $delbtch[market_batch]'>
		</form>
		<br>
		<br>
		<form name = 'batch_del' method='post' action = 'modules.php?name=$module_name&file=pc_market_form'>
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
		<input type = 'hidden' value = '$delbtch[creature_market_batch_id]' name = 'creature_market_batch_id'>
		<input type='hidden' value='$view_edit' name='view_edit'>
		<input type = 'submit' name = 'batch_del' value = 'No, Take me back to $delbtch[market_batch]'>
		</form>
	</td>
</tr>
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan = '11' width = '100%'>
	</td>
</tr> 
	";
}

// everything not deleting batches
if(empty($_POST['del_batch_id']))
{
	if(isset($_POST['creature_market_batch_id']))
	{
		// the core variable identifying the batch
		$creature_market_batch_id = $_POST['creature_market_batch_id'];
	}
	if(empty($_POST['creature_market_batch_id']))
	{
		// new trade from the pc_market page
		if(isset($_POST['new_trade_batch']))
		{
			$batch_exists = 0;
		
			$new_private_invitee = $_POST['new_private_invitee'];	
			if($new_private_invitee >= 2)
			{
				$batch_private = 1;
			}
			if($new_private_invitee == 1)
			{
				$batch_private = 0;
			}
			
			$market_batch = $_POST['market_batch'];
			$check_dupe_trade_batch = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed checkign market batches for dupes.");
			while($chkduptrdbtch = mysql_fetch_assoc($check_dupe_trade_batch))
			{
				if($chkduptrdbtch[market_batch] == $market_batch)
				{			
					$batch_exists++;
				}
			}
			
			if($batch_exists >= 1)
			{
				echo"
	<tr>
		<td valign = 'top' align = 'left' colspan = '11' width = '100%'>
			<font size = '2' color = 'red'>
			The name $chkduptrdbtch[market_batch]/$market_batch already exists; please go back and make a new one.
			</font>
		</td>
	</tr>
	<tr background='themes/RedShores/images/row2.gif' height='9'>
		<td colspan = '11' width = '100%'>
		</td>
	</tr> 
				";
			}
			
			if($batch_exists == 0)
			{
				if(isset($_POST['anonymous']))
				{
					$anonymous = 1;
				}
				if(empty($_POST['anonymous']))
				{
					$anonymous = 0;
				}
				
				$new_temp_trade_batch = mysql_query("INSERT INTO ".$slrp_prefix."creature_market_batch (creature_id,market_batch,market_batch_start,market_batch_private_sale,market_batch_anonymous,market_batch_status_id) VALUES ('$curpcnfo[creature_id]','$market_batch','$now','$batch_private','$anonymous',1)") or die ("Failed inserting new temp trade batch.");
				$check_trade_batch = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_id = '$curpcnfo[creature_id]' AND market_batch = '$market_batch'") or die ("failed getting new market batch.");
				$chktrdbtch = mysql_fetch_assoc($check_trade_batch);
				
				if($new_private_invitee >= 2)
				{
					$add_new_private_invitee = mysql_query("INSERT INTO ".$slrp_prefix."creature_market_batch_private (creature_market_batch_id,private_creature_id) VALUES ('$chktrdbtch[creature_market_batch_id]','$new_private_invitee')") or die ("Failed inserting new batch private invitee.");
				}
			}
		}
	}
	
	if(empty($_POST['new_trade_batch']))
	{
		$check_trade_batch = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id = '$creature_market_batch_id '") or die ("failed getting existing temp market batch.");
	 	$chktrdbtch = mysql_fetch_assoc($check_trade_batch);	
	
		if(isset($_POST['trade_batch_status']))
		{
			$creature_market_batch_status = $_POST['trade_batch_status'];
			$upd_batch_status = mysql_query("UPDATE ".$slrp_prefix."creature_market_batch SET market_batch_status_id = '$creature_market_batch_status' WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed finalizing the batch status.");
			// echo"<tr><td>creature_market_batch_status</td></tr>";
			// for sale
			if($creature_market_batch_status == 3)
			{
				$remove_sale_from_bidding = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_bid WHERE creature_market_bid_id = '$creature_market_batch_id'") or die ("failed unbidding batches to be sold.");			
			}
			// currently used to bid for a sale
			if($creature_market_batch_status == 2)
			{
				$remove_bid_from_sale = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_bid WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed unselling batches to be bid.");
				$check_trade_bids = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch_bid WHERE creature_market_batch_id = '$creature_market_batch_id '") or die ("failed getting existing temp market batch.");
	 			while($chktrdbds = mysql_fetch_assoc($check_trade_bids))
	 			{
	 				$bank_leftover_bids = mysql_query("UPDATE ".$slrp_prefix."creature_market_batch SET market_batch_status_id = '0' WHERE creature_market_batch_id = '$chktrdbds[creature_market_bid_id]'") or die ("failed finalizing leftover batch status.");
				}
			}
			// banked/unused
			if($creature_market_batch_status == 0)
			{
				$check_trade_bids = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch_bid WHERE creature_market_batch_id = '$creature_market_batch_id '") or die ("failed getting existing temp market batch.");
	 			while($chktrdbds = mysql_fetch_assoc($check_trade_bids))
	 			{
	 				$bank_leftover_bids = mysql_query("UPDATE ".$slrp_prefix."creature_market_batch SET market_batch_status_id = '0' WHERE creature_market_batch_id = '$chktrdbds[creature_market_bid_id]'") or die ("failed finalizing leftover batch status.");
				}
				$remove_bid_from_banked = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_bid WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed unselling batches to be bid.");
				$remove_sale_from_bidding = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_bid WHERE creature_market_bid_id = '$creature_market_batch_id'") or die ("failed unbidding batches to be sold.");			
			}
		}
	
		// recording total value when saved
		if(isset($_POST['creature_market_batch_value']))
		{
			$creature_market_batch_value = $_POST['creature_market_batch_value'];
			$update_trade_batch_value = mysql_query("UPDATE ".$slrp_prefix."creature_market_batch SET market_batch_value = '$creature_market_batch_value' WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed updating trade batch value info");
		}
		// if not posted, provide the default
		if(empty($_POST['creature_market_batch_value']))
		{
			$creature_market_batch_value = $chktrdbtch[market_batch_value];
		}
	}
	
	if(isset($_POST['end_batch_year']))
	{
		$end_batch_year = $_POST['end_batch_year'];
		$end_batch_month = $_POST['end_batch_month'];
		$end_batch_day = $_POST['end_batch_day'];
		$end_batch_hour = $_POST['end_batch_hour'];
		// date format = 2000-01-01 00:00:00
		$end_batch_date_compiled = $end_batch_year."-".$end_batch_month."-".$end_batch_day." ".$end_batch_hour.":00:00";
		echo"$end_batch_year-$end_batch_month-$end_batch_day $end_batch_hour:00:00 = $end_batch_date_compiled<br>";
		$update_trade_batch_end_date = mysql_query("UPDATE ".$slrp_prefix."creature_market_batch SET market_batch_end = '$end_batch_date_compiled' WHERE creature_market_batch_id = '$chktrdbtch[creature_market_batch_id]'") or die ("failed updating trade batch end date");
	}
	if(empty($_POST['end_batch_year']))
	{
		$time = $chktrdbtch[market_batch_end];
		$end_batch_date = new DateTime($time);
		$end_batch_date_normal = date_format($end_batch_date, ' g:i A D, j M Y');
		$end_batch_date_normal_full = date_format($end_batch_date, ' g:i A l, F jS, Y');
	}
	
	$trade_batch_owner = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id = '$chktrdbtch[creature_id]'") or die ("failed getting trade batch owner");
	$trdbtchownr = mysql_fetch_assoc($trade_batch_owner);
	$trade_batch_owner_player = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$trdbtchownr[creature_nuke_user_id]'") or die ("failed getting trade batch owner");
	$trdbtchownrplyr = mysql_fetch_assoc($trade_batch_owner_player);
	
	// making a batch into a bid
	if(isset($_POST['desired_batch_id']))
	{
		$creature_market_batch_id = $_POST['desired_batch_id'];
		$bid_batch_id = $_POST['bid_batch_id'];
		
		$placing_a_bid = mysql_query("INSERT INTO ".$slrp_prefix."creature_market_batch_bid (creature_market_batch_id,creature_market_bid_id) VALUES ('$creature_market_batch_id','$bid_batch_id')") or die ("Failed inserting new batch bid.");
		$batch_to_bid = mysql_query("UPDATE ".$slrp_prefix."creature_market_batch SET market_batch_status_id = '2' WHERE creature_market_batch_id = '$bid_batch_id'") or die ("failed updating batch action for bids.");
	}
	
	// private auctions
	if(isset($_POST['new_private_pc']))
	{
		$new_private_invitee = $_POST['new_private_invitee'];
		$list_of_existing_private_buyers = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch_private WHERE creature_market_batch_id = '$creature_market_batch_id' AND private_creature_id = '$new_private_invitee'") or die("failed new private buyer check.");
		$listoexprvtbyrscnt = mysql_num_rows($list_of_existing_private_buyers);
		if($listoexprvtbyrscnt == 0)
		{
			$additional_private_invitee = mysql_query("INSERT INTO ".$slrp_prefix."creature_market_batch_private (creature_market_batch_id,private_creature_id) VALUES ('$creature_market_batch_id','$new_private_invitee')") or die ("Failed inserting new batch private invitee.");
		}
	}
	if(isset($_POST['del_private_pc']))
	{
		$list_of_current_private_buyers = mysql_query("SELECT private_creature_id FROM ".$slrp_prefix."creature_market_batch_private WHERE creature_market_batch_id = '$creature_market_batch_id'") or die("failed del private buyer check.");
		$listcurprvtbyrscnt = mysql_num_rows($list_of_current_private_buyers);
		// echo"<tr><td><font color = 'purple' size= '2'>buyers: $listcurprvtbyrscnt</font></td></tr>";
		while($listcurprvtbyrs = mysql_fetch_assoc($list_of_current_private_buyers))
		{
			//debug line
			// echo"<tr><td><font color = 'purple' size= '2'>$listoprvtbyrs[private_creature_id]</font></td></tr>";
		
			if(isset($_POST['del_private_'.$listcurprvtbyrs[private_creature_id].'_id']))
			{
				$del_private_invitee = $_POST['del_private_'.$listcurprvtbyrs[private_creature_id].'_id'];
				$delete_private_invitee = mysql_query("DELETE FROM ".$slrp_prefix."creature_market_batch_private WHERE creature_market_batch_id = '$creature_market_batch_id' AND private_creature_id = '$del_private_invitee'") or die ("Failed deleting new batch private invitee.");
			}
		}
	}
	
	// to test passing of variables...
	// uncomment the html at the top and bottom
	// then the lines at the end of each section
	
	// echo"<tr><td valign = 'top' align = 'left'  colspan = '11'>trade_batch: $chktrdbtch[market_batch]<table width = '100%'><tr><td valign = 'top' align = 'center'>";
	
	// updating or saving a batch from this page
	// or coming from the pc_market page as a new entry
	if($view_edit == "edit")
	{
		$verify_existing_material_ownership = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_id = '$curpcnfo[creature_id]' ORDER BY material_id") or die ("failed getting owned materials.");
	}
	// if coming from the pc_market page to view
	if($view_edit == "view")
	{
		$verify_existing_material_ownership = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material INNER JOIN ".$slrp_prefix."creature_object_for_sale ON ".$slrp_prefix."creature_object_for_sale.creature_object_id = ".$slrp_prefix."creature_material.creature_material_id WHERE ".$slrp_prefix."creature_material.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_object_for_sale.focus_id = '26' AND ".$slrp_prefix."creature_object_for_sale.creature_market_batch_id = '$chktrdbtch[creature_market_batch_id]'") or die ("failed getting trade batch materials view.");
	}
	
	$vrexmatownshpcnt = mysql_num_rows($verify_existing_material_ownership);
	while($vrexmatownshp = mysql_fetch_assoc($verify_existing_material_ownership))
	{
		// adding materials manually
		if(isset($_POST['sell_pc_mat_'.$vrexmatownshp[creature_material_id]]))
		{
			$sell_pc_mat_id = $_POST['sell_pc_mat_'.$vrexmatownshp[creature_material_id]];
			$markup_pc_mat_value = $_POST['markup_pc_mat_value_'.$vrexmatownshp[creature_material_id]];
			$sell_pc_mat_count = $_POST['sell_pc_mat_count_'.$vrexmatownshp[creature_material_id]];
			$pc_mat_seller_comments = $_POST['pc_mat_seller_comments_'.$vrexmatownshp[creature_material_id]];
			
			if($sell_pc_mat_id >= 2)
			{
				$check_batch_object = mysql_query("SELECT * FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_object_id = '$vrexmatownshp[creature_material_id]' AND creature_market_batch_id = '$creature_market_batch_id'") or die ("failed checking batch existence.");
				$chkbtchobjcnt = mysql_num_rows($check_batch_object);
				$chkbtchobj = mysql_fetch_assoc($check_batch_object);
				if($sell_pc_mat_count >= 1)
				{		
					if($chkbtchobjcnt == 1)
					{
						$update_trade_batch_info = mysql_query("UPDATE ".$slrp_prefix."creature_object_for_sale SET creature_object_sale_count = '$sell_pc_mat_count', creature_object_sale_markup = '$markup_pc_mat_value', creature_object_sale_comment = '$pc_mat_seller_comments' WHERE creature_object_for_sale_id = '$chkbtchobj[creature_object_for_sale_id]'") or die ("failed updating trade batch objct info");
					}
					if($chkbtchobjcnt == 0)
					{
						$insert_trade_batch_info = mysql_query("INSERT INTO ".$slrp_prefix."creature_object_for_sale(creature_object_id,focus_id,creature_market_batch_id,creature_object_sale_count,creature_object_sale_markup,creature_object_sale_comment) VALUES ('$vrexmatownshp[creature_material_id]','26','$creature_market_batch_id','$sell_pc_mat_count','$markup_pc_mat_value','$pc_mat_seller_comments')") or die ("failed inserting trade batch objct info");
					}
				
					$ex_pc_mat_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."material ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."material.material_default_unit_id WHERE ".$slrp_prefix."material.material_id = '$sell_pc_mat_id'") or die ("failed to get pc mat unit info.");
					$expcmatunit = mysql_fetch_assoc($ex_pc_mat_unit);	
				
					if(isset($_POST['id_pc_mat']))
					{
						$id_pc_mat = 1;
					}
					if(empty($_POST['id_pc_mat']))
					{
						if($vrexmatownshpcnt >= 1)
						{
							$id_pc_mat = $vrexmatownshp[creature_identified];
						}
						if($vrexmatownshpcnt == 0)
						{
							$id_pc_mat = 0;
						}
					}
				
					if($vrexmatownshpcnt == 0)
					{
						$active_count_size_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$sell_pc_mat_count'") or die ("failed getting current harvest count nfo.");
						$actcntsznfo = mysql_fetch_assoc($active_count_size_info);
					}
					if($vrexmatownshpcnt >= 1)
					{
						$active_count_size_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$sell_pc_mat_count'") or die ("failed getting owned current count nfo.");
						$actcntsznfo = mysql_fetch_assoc($active_count_size_info);
					}
				}
				if($sell_pc_mat_count == 0)
				{
					$delete_trade_batch_object = mysql_query("DELETE FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_object_for_sale_id = '$chkbtchobj[creature_object_for_sale_id]'") or die ("failed deleting trade batch object info");
				}
			}
			
			// echo"mat id: $sell_pc_mat_id, count: $sell_pc_mat_count [$expcmatunit[effect]/$expcmatunit[effect_abbr]], markup: $markup_pc_mat_value, comments: $pc_mat_seller_comments<br>";
		}
	}
	
	$verify_existing_item_ownership = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item INNER JOIN ".$slrp_prefix."item ON ".$slrp_prefix."item.item_id = ".$slrp_prefix."creature_item.item_id WHERE ".$slrp_prefix."creature_item.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_item.creature_item_count > 0 ORDER BY ".$slrp_prefix."creature_item.creature_identified, ".$slrp_prefix."item.item") or die ("failed getting owned items.");
	$vrexitmownshpcnt = mysql_num_rows($verify_existing_item_ownership);
	while($vrexitmownshp = mysql_fetch_assoc($verify_existing_item_ownership))
	{
		// adding items manually
		if(isset($_POST['sell_pc_itm_'.$vrexitmownshp[creature_item_id]]))
		{
			$sell_pc_itm_id = $_POST['sell_pc_itm_'.$vrexitmownshp[creature_item_id]];
			$markup_pc_itm_value = $_POST['markup_pc_itm_value_'.$vrexitmownshp[creature_item_id]];
			$sell_pc_itm_count = $_POST['sell_pc_itm_count_'.$vrexitmownshp[creature_item_id]];
			$pc_itm_seller_comments = $_POST['pc_itm_seller_comments_'.$vrexitmownshp[creature_item_id]];
			
			// make sure it is not working on a 1 (default, none)
			if($sell_pc_itm_id >= 2)
			{
				if($sell_pc_itm_count >= 1)
				{
					$check_batch_object2 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_object_id = '$vrexitmownshp[creature_item_id]' AND creature_market_batch_id = '$creature_market_batch_id'") or die ("failed checking batch item existence.");
					$chkbtchobj2cnt = mysql_num_rows($check_batch_object2);
					$chkbtchobj2 = mysql_fetch_assoc($check_batch_object2);
					
					if($chkbtchobj2cnt == 1)
					{
						$update_trade_batch_info = mysql_query("UPDATE ".$slrp_prefix."creature_object_for_sale SET creature_object_sale_count = '$sell_pc_itm_count', creature_object_sale_markup = '$markup_pc_itm_value', creature_object_sale_comment = '$pc_itm_seller_comments' WHERE creature_object_for_sale_id = '$chkbtchobj2[creature_object_for_sale_id]'") or die ("failed updating trade batch objct itm info");
					}
					if($chkbtchobj2cnt == 0)
					{
						$insert_trade_batch_info = mysql_query("INSERT INTO ".$slrp_prefix."creature_object_for_sale(creature_object_id,focus_id,creature_market_batch_id,creature_object_sale_count,creature_object_sale_markup,creature_object_sale_comment) VALUES ('$vrexitmownshp[creature_item_id]','10','$creature_market_batch_id','$sell_pc_itm_count','$markup_pc_itm_value','$pc_itm_seller_comments')") or die ("failed inserting trade batch objct itm info");
					}		
				
					$ex_pc_itm_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."item ON ".$slrp_prefix."effect.effect_id = ".$slrp_prefix."item.item_default_unit_id WHERE ".$slrp_prefix."item.item_id = '$sell_pc_itm_id'") or die ("failed to get pc itm unit info.");
					$expcitmunit = mysql_fetch_assoc($ex_pc_itm_unit);	
					
					if(isset($_POST['id_pc_itm']))
					{
						$id_pc_itm = 1;
					}
					if(empty($_POST['id_pc_itm']))
					{
						if($vrexitmownshpcnt >= 1)
						{
							$id_pc_itm = $vrexitmownshp[creature_identified];
						}
						if($vrexitmownshpcnt == 0)
						{
							$id_pc_itm = 0;
						}
					}
					
					if($vrexitmownshpcnt == 0)
					{
						$active_item_count_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$sell_pc_itm_count'") or die ("failed getting current item count nfo.");
						$actitmcntnfo = mysql_fetch_assoc($active_item_count_info);
					}
					if($vrexitmownshpcnt >= 1)
					{
						$active_item_count_info = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$sell_pc_itm_count'") or die ("failed getting owned current item count nfo.");
						$actitmcntnfo = mysql_fetch_assoc($active_item_count_info);
					}
				}
			}
			
			// echo"itm id: $sell_pc_itm_id, count: $sell_pc_itm_count [$expcitmunit[effect]/$expcitmunit[effect_abbr]], markup: $markup_pc_itm_value, comments: $pc_itm_seller_comments<br>";
		}
	}
	
	// uncomment this line to close thd egbug table if uncommented above
	// echo"</td></tr></table></td></tr>";
	// end debug sections	
	
	$get_trade_batch = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id = '$chktrdbtch[creature_market_batch_id]'") or die ("failed getting existing temp market batch.");
	$gttrdbtch = mysql_fetch_assoc($get_trade_batch);	
	// get the date stuff straightened out for the cutoff
	$time = $gttrdbtch[market_batch_end];
	$end_batch_date = new DateTime($time);
	$end_batch_date_normal = date_format($end_batch_date, ' g:i A D, j M Y');
	$end_batch_date_normal_full = date_format($end_batch_date, ' g:i A l, F jS, Y');
	
	$current_trade_batch_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_status_id = '$gttrdbtch[market_batch_status_id]'") or die ("failed getting current batch status.");
	$curtrdbtchstts = mysql_fetch_assoc($current_trade_batch_status);
	
	echo"
<tr>
	<td valign = 'top' align = 'center' colspan = '11' width = '100%'>
		<table width = '100%' cellspacing = '0'>
			<tr>
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
				<form name = 'pc_edit' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
				<td valign = 'middle' align = 'left' width = '18%'>
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
				</td>
				</form>
				<td width = '2%'>
				</td>
				<form name = 'mat_list' method='post' action = 'modules.php?name=$module_name&file=obj_list'>
				<td valign = 'middle' align = 'center' width = '18%'>
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
				<td valign = 'middle' align = 'right' width = '18%'>
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
			</tr>
		</table>
	</td>
</tr>
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan = '11' width = '100%'>
	</td>
</tr> 
	";
	
	// Echo"P3: $prod_pts<br>";
	if($view_edit == "view")
	{
		$get_pc_material = mysql_query("SELECT * FROM ".$slrp_prefix."creature_object_for_sale INNER JOIN ".$slrp_prefix."creature_material ON ".$slrp_prefix."creature_object_for_sale.creature_object_id = ".$slrp_prefix."creature_material.creature_material_id WHERE ".$slrp_prefix."creature_object_for_sale.creature_market_batch_id = '$creature_market_batch_id' AND ".$slrp_prefix."creature_object_for_sale.focus_id = '26'") or die ("failed getting pc material for batch.");
	}
	if($view_edit == "edit")
	{
		$get_pc_material = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material INNER JOIN ".$slrp_prefix."material ON ".$slrp_prefix."creature_material.material_id = ".$slrp_prefix."material.material_id WHERE ".$slrp_prefix."creature_material.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_material.creature_material_count >= '1' ORDER BY ".$slrp_prefix."material.material") or die ("failed getting pc material 2.");
	}
	
	$curpcmatcnt = mysql_num_rows($get_pc_material);
	
	echo"
<tr>
	<td valign = 'top' align = 'left' colspan = '11' width = '100%'>
		<table width = '100%' cellspacing = '0'>
			<tr>
				<td valign = 'middle' align = 'center' width = '15%'>
					<font size = '5' color = 'red'>
					<b>$gttrdbtch[market_batch] ($gttrdbtch[market_batch_value])</b>
					<br>
					<font size = '1'>$curtrdbtchstts[slurp_alt_status2] ending $end_batch_date_normal_full
					</font></font>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'middle' align = 'center' width = '15%'>
	";
	
	// general sales
	if($gttrdbtch[market_batch_private_sale] == 0)
	{
		// not anonymous
		if($gttrdbtch[market_batch_anonymous] == 0)
		{
			echo"
					[<a href='http://dominiontest.inclave.com/modules.php?name=Private_Messages&file=index&mode=post&u=".$trdbtchownr[creature_nuke_user_id]."'>$trdbtchownr[creature]</a>]
			";
		}
		// anonymous
		if($gttrdbtch[market_batch_anonymous] == 1)
		{
			// admins know who the seller is
			if($curusrslrprnk[slurp_rank_id] <= 4)
			{
				echo"
						<font size = '1' color = 'purple'>Admin </font><font size = '1'>[<a href='http://dominiontest.inclave.com/modules.php?name=Private_Messages&file=index&mode=post&u=".$trdbtchownr[creature_nuke_user_id]."'>$trdbtchownr[creature]</a>]</font><br>
			";
			}
			// non-admin sellers know who the seller is
			if($curusrslrprnk[slurp_rank_id] >= 5)
			{
				if($curpcnfo[creature_id] == $trdbtchownr[creature_id])
				{
					echo"
							<font size = '1' color = 'purple'>You are the Seller.</font><br>
					";
				}
			}
			
			echo"
							[<font color='red'><b>Anonymous</b></font>]
			";
		}
	}
	
	echo"
		</td>
		<td width = '2%'>
		</td>
	";
	
	// if not the batch owner, show the current character's bids
	if($curpcnfo[creature_id] != $trdbtchownr[creature_id])
	{
		// ...only with items for sale
		if($gttrdbtch[market_batch_status_id] == 3)
		{
			$get_pc_bids = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch_bid INNER JOIN ".$slrp_prefix."creature_market_batch ON ".$slrp_prefix."creature_market_batch.creature_market_batch_id = ".$slrp_prefix."creature_market_batch_bid.creature_market_bid_id WHERE ".$slrp_prefix."creature_market_batch_bid.creature_market_batch_id = '$creature_market_batch_id' AND ".$slrp_prefix."creature_market_batch.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting bids.");
			$pcbidscnt = mysql_num_rows($get_pc_bids);
			
			// show the current bid if there is one
			if($pcbidscnt >= 1)
			{
				while($pcbids = mysql_fetch_assoc($get_pc_bids))
				{
					$pc_bids = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id = '$pcbids[creature_market_bid_id]'") or die ("failed getting bids instance.");
					$pcbd = mysql_fetch_assoc($pc_bids);
					
					echo"
						<form name = 'view_trade_batch' method = 'post' action = 'modules.php?name=$module_name&file=pc_market_form'>
						<td valign = 'middle' align = 'center' width = '15%'>
							<font color='red' size = '1'>Your Current Bid:</font>
							<br>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='$pcbd[creature_market_batch_id]' name='creature_market_batch_id'>
							<input type='hidden' value='view' name='view_edit'>
							<input type='hidden' value='$expander_abbr' name='current_expander'>
							<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
							<input type='hidden' value='$component_expander' name = 'component_expander'>
							<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
							<input type='hidden' value='$materials_expander' name = 'materials_expander'>
							<input type='hidden' value='$items_expander' name = 'items_expander'>
							<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
							<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
							<input type='hidden' value='1' name='char_expander'>
							<input type='submit' name='view_trade_batch' value='$pcbd[market_batch] ($pcbd[market_batch_value])'>
						</td>
						<td width = '2%'>
						</td>
						</form>
					";
				}
			}
			
			// if there is not one, offer to add an existing one
			if($pcbidscnt == 0)
			{
				$get_existing_batches_for_trade = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id > '1' AND market_batch_private_sale = '0' AND market_batch_status_id = '0' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed getting current pc general market batches.");
				$gtexbtchfortrdcnt = mysql_num_rows($get_existing_batches_for_trade);
				echo"
				<form name = 'place_bids' method = 'post' action = 'modules.php?name=$module_name&file=pc_market_form'>
				<td valign = 'middle' align = 'center' width = '15%'>
				<font color='red' size = '2'>
				";
				
				// if none exist, let them know
				if($gtexbtchfortrdcnt == 0)
				{
					echo"
						You must create a general batch to offer in trade
						<br>
						or free one you are using to bid.
					";
				}
				// if they do exist, offer a list
				if($gtexbtchfortrdcnt >= 1)
				{
					echo"
						Place a Bid using one of your batches:<br>
						<select class='engine' name = 'bid_batch_id'>
					";
						
					while($gtexbtchfortrd = mysql_fetch_assoc($get_existing_batches_for_trade))
					{
						echo"<option value = '$gtexbtchfortrd[creature_market_batch_id]'>$gtexbtchfortrd[market_batch] ($gtexbtchfortrd[market_batch_value])</option>";
					}
					
					echo"</select>
						<input type='hidden' value='$creature_market_batch_id' name='desired_batch_id'>
						<input type='hidden' value='$creature_market_batch_id' name='creature_market_batch_id'>
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='view' name='view_edit'>
						<input type='hidden' value='$expander_abbr' name='current_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='hidden' value='$component_expander' name = 'component_expander'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$materials_expander' name = 'materials_expander'>
						<input type='hidden' value='$items_expander' name = 'items_expander'>
						<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
						<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
						<input type='hidden' value='1' name='char_expander'>
						<input type = 'submit' name = 'place_bids' value = 'Place Bid on $gttrdbtch[market_batch]'>
						</font>
					";
				}
				
				echo"
					</td>
					<td width = '2%'>
					</td>
					</form>
				";
			}
		}
	}
	
	// if bidder, show what the batch is bidding on
	if($curpcnfo[creature_id] == $trdbtchownr[creature_id])
	{
		// currently bidding on...
		if($gttrdbtch[market_batch_status_id] == 2)
		{
			$get_pc_bids = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch_bid INNER JOIN ".$slrp_prefix."creature_market_batch ON ".$slrp_prefix."creature_market_batch.creature_market_batch_id = ".$slrp_prefix."creature_market_batch_bid.creature_market_batch_id WHERE ".$slrp_prefix."creature_market_batch_bid.creature_market_bid_id = '$gttrdbtch[creature_market_batch_id]'") or die ("failed getting batch bids 2.");
			$pcbidscnt = mysql_num_rows($get_pc_bids);
			
			if($pcbidscnt == 0)
			{
				echo"
				<td valign = 'middle' align = 'center' width = '15%'>
				</td>
				<td width = '2%'>
				</td>
				";
			}
			if($pcbidscnt >= 1)
			{
				$pcbids = mysql_fetch_assoc($get_pc_bids);
			
				$pc_bids = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id = '$pcbids[creature_market_batch_id]'") or die ("failed getting bids instance.");
				$pcbd = mysql_fetch_assoc($pc_bids);
				echo"
					<form name = 'view_trade_batch' method = 'post' action = 'modules.php?name=$module_name&file=pc_market_form'>
					<td valign = 'middle' align = 'center' width = '15%'>
						<font color='red' size = '1'>Currently used to bid on:</font>
						<br>
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='$pcbd[creature_market_batch_id]' name='creature_market_batch_id'>
						<input type='hidden' value='view' name='view_edit'>
						<input type='hidden' value='$expander_abbr' name='current_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='hidden' value='$component_expander' name = 'component_expander'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$materials_expander' name = 'materials_expander'>
						<input type='hidden' value='$items_expander' name = 'items_expander'>
						<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
						<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
						<input type='hidden' value='1' name='char_expander'>
						<input type='submit' name='view_trade_batch' value='$pcbd[market_batch] ($pcbd[market_batch_value])'>
					</td>
					<td width = '2%'>
					</td>
					</form>
				";
			}
		}
		// if it is for sale, show the bids from others
		if($gttrdbtch[market_batch_status_id] == 3)
		{
			echo"
			<td width = '49%' align = 'center' valign = 'middle' colspan ='3'>
				<table width = '100%' cellspacing = '0'>
			";
			
			$batch_bids = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch_bid INNER JOIN ".$slrp_prefix."creature_market_batch ON ".$slrp_prefix."creature_market_batch.creature_market_batch_id = ".$slrp_prefix."creature_market_batch_bid.creature_market_bid_id WHERE ".$slrp_prefix."creature_market_batch_bid.creature_market_batch_id = '$gttrdbtch[creature_market_batch_id]'") or die ("failed getting batch bids 2.");
			$bidscnt = mysql_num_rows($batch_bids);
			// $bids = mysql_fetch_assoc($batch_bids);
			while($bids = mysql_fetch_assoc($batch_bids))
			{
				$batch_bidder = mysql_query("SELECT * FROM ".$slrp_prefix."creature_market_batch WHERE creature_market_batch_id = '$bids[creature_market_bid_id]'") or die ("failed getting bids.");
				$btchbddr = mysql_fetch_assoc($batch_bidder);
			
				$public_bidder = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id = '$btchbddr[creature_id]'") or die("failed to get bidders in market");
				$gtpblcbddr = mysql_fetch_assoc($public_bidder);
				echo"
					<tr>
						<form name = 'view_bid_batch' method = 'post' action = 'modules.php?name=$module_name&file=pc_market_form'>
						<td width = '32%' align = 'center' valign = 'middle'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='$btchbddr[creature_market_batch_id]' name='creature_market_batch_id'>
							<input type='hidden' value='view' name='view_edit'>
							<input type='hidden' value='$expander_abbr' name='current_expander'>
							<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
							<input type='hidden' value='$component_expander' name = 'component_expander'>
							<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
							<input type='hidden' value='$materials_expander' name = 'materials_expander'>
							<input type='hidden' value='$items_expander' name = 'items_expander'>
							<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
							<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
							<input type='hidden' value='1' name='char_expander'>
							<input type='submit' name='view_bid_batch' value='$btchbddr[market_batch] ($btchbddr[market_batch_value])'>
						</td>
						</form>
						<td width = '2%'>
						</td>
						<td width = '32%' align = 'center' valign = 'middle'>
				";
				
				if($btchbddr[market_batch_anonymous] == 0)
				{
					echo"[<a href='http://dominiontest.inclave.com/modules.php?name=Private_Messages&file=index&mode=post&u=".$gtpblcbddr[creature_nuke_user_id]."'>$gtpblcbddr[creature]</a>]";
				}
				
				if($btchbddr[market_batch_anonymous] == 1)
				{
					echo"[<font color = 'red'><b>Anonymous</b></font>]";
				}
				
				echo"
						</td>
						<td width = '2%'>
						</td>
						<form name = 'make_trade' method = 'post' action = 'modules.php?name=$module_name&file=pc_market'>
						<td width = '32%' align = 'center' valign = 'middle'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='$gttrdbtch[creature_market_batch_id]' name='make_trade_batch_id'>
							<input type='hidden' value='$btchbddr[creature_market_batch_id]' name='make_trade_bid_id'>
							<input type='hidden' value='view' name='view_edit'>
							<input type='hidden' value='$expander_abbr' name='current_expander'>
							<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
							<input type='hidden' value='$component_expander' name = 'component_expander'>
							<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
							<input type='hidden' value='$materials_expander' name = 'materials_expander'>
							<input type='hidden' value='$items_expander' name = 'items_expander'>
							<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
							<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
							<input type='hidden' value='1' name='char_expander'>
							<input type='submit' name='make_trade' value='Accept $btchbddr[market_batch]'>
						</td>
						</form>
					<tr>
				";
			}
			
			echo"
					</table>
				</td>
				<td width = '2%'>
				</td>
			";
		}
	}
	//private sales
	if($gttrdbtch[market_batch_private_sale] == 1)
	{
		echo"
				<form name = 'del_private_pc' method = 'post' action = 'modules.php?name=$module_name&file=pc_market_form'>
				<td valign = 'right' align = 'left' width = '15%'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td width = '20%'>
		";
		// being viewed by the seller
		if($curpcnfo[creature_id] == $trdbtchownr[creature_id])
		{
			if($view_edit == "edit")
			{
				echo"<font size = '1' color = 'red'><b>DEL</b></font>";
			}
		}
		// show the seller if not anonymous
		if($gttrdbtch[market_batch_anonymous] == 0)
		{
			echo"
							</td>
							<td width = '80%'>[<a href='http://dominiontest.inclave.com/modules.php?name=Private_Messages&file=index&mode=post&u=".$trdbtchownr[creature_nuke_user_id]."'>$trdbtchownr[creature]</a>]
							<td>
						</tr>
			";
		}
		// anonymous offers show only the word
		if($gttrdbtch[market_batch_anonymous] == 1)
		{
			echo"
							</td>
							<td width = '80%'>[<font color='red'><b>Anonymous</b></font>]
							<td>
						</tr>
			";
		}
		
		$get_private_invitees = mysql_query("SELECT * FROM ".$slrp_prefix."creature INNER JOIN ".$slrp_prefix."creature_market_batch_private ON ".$slrp_prefix."creature_market_batch_private.private_creature_id = ".$slrp_prefix."creature.creature_id WHERE ".$slrp_prefix."creature_market_batch_private.creature_market_batch_id = '$gttrdbtch[creature_market_batch_id]' ORDER BY ".$slrp_prefix."creature.creature") or die("failed to get other private characters in market.");
		$gtprvtnvtscnt = mysql_num_rows($get_private_invitees);
		
		$invitees = $gtprvtnvtscnt;
		while($gtprvtnvts = mysql_fetch_assoc($get_private_invitees))
		{
			echo"
						<tr>
							<td width = '20%'>
			";
			if($view_edit == "edit")
			{
				if($curusrslrprnk[slurp_rank_id] >= 5)
				{
					if($curpcnfo[creature_id] == $trdbtchownr[creature_id])
					{
						echo"<input type='checkbox' value='$gtprvtnvts[creature_id]' name='del_private_".$gtprvtnvts[creature_id]."_id'>";
					}
				}
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"<input type='checkbox' value='$gtprvtnvts[creature_id]' name='del_private_".$gtprvtnvts[creature_id]."_id'>";
				}
			}
			
			echo"
							</td>
							<td width = '80%'>
								<a href='http://dominiontest.inclave.com/modules.php?name=Private_Messages&file=index&mode=post&u=".$gtprvtnvts[creature_nuke_user_id]."'>$gtprvtnvts[creature]</a>
							<td>
						</tr>
			";
			
			$invitees--;
		}
		
		echo"
						<tr>
							<td colspan = '2'>
		";
		if($view_edit == "edit")
		{
			if($gttrdbtch[market_batch_status_id] == 3)
			{
				if($curusrslrprnk[slurp_rank_id] >= 5)
				{
					if($curpcnfo[creature_id] == $trdbtchownr[creature_id])
					{
						echo"
									<input type='hidden' value='$gttrdbtch[creature_market_batch_id]' name='creature_market_batch_id'>
									<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
									<input type='hidden' value='$expander_abbr' name='current_expander'>
									<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
									<input type='hidden' value='$component_expander' name = 'component_expander'>
									<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
									<input type='hidden' value='$materials_expander' name = 'materials_expander'>
									<input type='hidden' value='$items_expander' name = 'items_expander'>
									<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
									<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
									<input type='hidden' value='edit' name='view_edit'>
									<input type='hidden' value='1' name='char_expander'>
									<input type='submit' name='del_private_pc' value='Delete'>
						";
					}
				}
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"
									<input type='hidden' value='$gttrdbtch[creature_market_batch_id]' name='creature_market_batch_id'>
									<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
									<input type='hidden' value='$expander_abbr' name='current_expander'>
									<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
									<input type='hidden' value='$component_expander' name = 'component_expander'>
									<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
									<input type='hidden' value='$materials_expander' name = 'materials_expander'>
									<input type='hidden' value='$items_expander' name = 'items_expander'>
									<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
									<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
									<input type='hidden' value='edit' name='view_edit'>
									<input type='hidden' value='1' name='char_expander'>
									<input type='submit' name='del_private_pc' value='Delete'>
					";
				}
			}
		}
		
		echo"
								</td>
							</tr>
						</table>
					</td>
					</form>
					<td width = '2%'>
					</td>
	
		";
		
		if($view_edit == "edit")
		{
			if($gttrdbtch[market_batch_status_id] == 3)
			{
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"
					<form name = 'new_private_pc' method = 'post' action = 'modules.php?name=$module_name&file=pc_market_form'>
					<td valign = 'middle' align = 'left' width = '18%'>
					<input type='hidden' value='$gttrdbtch[creature_market_batch_id]' name='creature_market_batch_id'>
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
					<font color='red' size='2'>Invite Another? </font>
					<select class='engine' name='new_private_invitee'>
					<option value = '1'>Not now, thanks!</option>
					";
					
					$get_private_character = mysql_query("SELECT ".$slrp_prefix."creature.* FROM ".$slrp_prefix."creature WHERE ".$slrp_prefix."creature.creature_id != '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature.creature_id > '1' AND ".$slrp_prefix."creature.creature_status_id = '4' AND ".$slrp_prefix."creature.creature_id NOT IN (SELECT ".$slrp_prefix."creature_market_batch_private.private_creature_id FROM ".$slrp_prefix."creature_market_batch_private WHERE ".$slrp_prefix."creature_market_batch_private.creature_market_batch_id = '$gttrdbtch[creature_market_batch_id]') ORDER BY ".$slrp_prefix."creature.creature") or die("failed to get ptivate character.");
					while($gtprvtchrctr = mysql_fetch_assoc($get_private_character))
					{
						echo"<option value = '$gtprvtchrctr[creature_id]'>$gtprvtchrctr[creature]</option>";
					}
					
					echo"
					</select>
					<input type='hidden' value='$view_edit' name='view_edit'>
					<input type='submit' name='new_private_pc' value='New Private Invitee'>
					</td>
					</form>
					<td width = '2%'>
					</td>
					<td valign = 'middle' align = 'center' width = '18%'>
					
					</td>
					<td width = '2%'>
					</td>
					";
				}
				
				if($curusrslrprnk[slurp_rank_id] >= 5)
				{
					if($curpcnfo[creature_id] == $trdbtchownr[creature_id])
					{
						echo"
					<form name = 'new_private_pc' method = 'post' action = 'modules.php?name=$module_name&file=pc_market_form'>
					<td valign = 'middle' align = 'left' width = '18%'>
					<input type='hidden' value='$gttrdbtch[creature_market_batch_id]' name='creature_market_batch_id'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$component_expander' name = 'component_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='edit' name='view_edit'>
					<input type='hidden' value='1' name='char_expander'>
					<font color='red' size='2'>Invite Another? </font>
					<select class='engine' name='new_private_invitee'>
					<option value = '1'>Not now, thanks!</option>
						";
						
						$get_private_character = mysql_query("SELECT ".$slrp_prefix."creature.* FROM ".$slrp_prefix."creature WHERE ".$slrp_prefix."creature.creature_id != '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature.creature_id > '1' AND ".$slrp_prefix."creature.creature_status_id = '4' AND ".$slrp_prefix."creature.creature_id NOT IN (SELECT ".$slrp_prefix."creature_market_batch_private.private_creature_id FROM ".$slrp_prefix."creature_market_batch_private WHERE ".$slrp_prefix."creature_market_batch_private.creature_market_batch_id = '$gttrdbtch[creature_market_batch_id]') ORDER BY ".$slrp_prefix."creature.creature") or die("failed to get ptivate character.");
						while($gtprvtchrctr = mysql_fetch_assoc($get_private_character))
						{
							echo"<option value = '$gtprvtchrctr[creature_id]'>$gtprvtchrctr[creature]</option>";
						}
						
						echo"
					</select>
					<input type='submit' name='new_private_pc' value='New Private Invitee'>
					</td>

					<td width = '2%'>
					</td>
					<td valign = 'middle' align = 'center' width = '18%'>

					</td>
					</form>
					<td width = '2%'>
					</td>
						";
					}
				}
			}
		}
	}
	
	if($view_edit == "edit")
	{
		if($gttrdbtch[market_batch_status_id] == 0)
		{
			if($curusrslrprnk[slurp_rank_id] <= 4)
			{
				echo"
						<form name = 'delete_batch_final' method='post' action = 'modules.php?name=$module_name&file=pc_market_form'>
						<td valign = 'middle' align = 'right' width = '18%'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='$expander_abbr' name='current_expander'>
							<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
							<input type='hidden' value='$component_expander' name = 'component_expander'>
							<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
							<input type='hidden' value='$materials_expander' name = 'materials_expander'>
							<input type='hidden' value='$items_expander' name = 'items_expander'>
							<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
							<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
							<input type='hidden' value='$view_edit' name='view_edit'>
							<input type='hidden' value='1' name='char_expander'>
							<input type = 'hidden' value = '$gttrdbtch[creature_market_batch_id]' name = 'del_batch_id'>
							<input type = 'submit' value = 'Delete $gttrdbtch[market_batch]' name = 'delete_batch_final'>
						</td>
						</form>
				";
			}
			if($curusrslrprnk[slurp_rank_id] >= 5)
			{
				if($gttrdbtch[creature_id] == $curpcnfo[creature_id])
				{
					echo"
						<form name = 'delete_batch' method='post' action = 'modules.php?name=$module_name&file=pc_market_form'>
						<td valign = 'middle' align = 'right' width = '18%'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='$expander_abbr' name='current_expander'>
							<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
							<input type='hidden' value='$component_expander' name = 'component_expander'>
							<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
							<input type='hidden' value='$materials_expander' name = 'materials_expander'>
							<input type='hidden' value='$items_expander' name = 'items_expander'>
							<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
							<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
							<input type='hidden' value='$view_edit' name='view_edit'>
							<input type='hidden' value='1' name='char_expander'>
							<input type = 'hidden' value = '$gttrdbtch[creature_market_batch_id]' name = 'del_batch_id'>
							<input type = 'submit' value = 'Delete $gttrdbtch[market_batch]' name = 'delete_batch'>
						</td>
						</form>
					";
				}
			}
		}
	}
	
	echo"
			</tr>
		</table>
	
	</td>
</tr>
<form name = 'save_batch' method = 'post' action = 'modules.php?name=$module_name&file=pc_market_form'>
	";
	
	if($curpcmatcnt >= 1)
	{
		echo"
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan='11'>
	</td>
</tr> 
<tr>
	<td valign = 'top' width = '100%' colspan = '11'>
		<table width = '100%' cellspacing = '0'>
			<tr background='themes/RedShores/images/base1.gif' height='24'>
				<td valign = 'middle' align = 'left' width = '18%'>
					<font color = 'red' size = '2'>
					<b>MATERIALS</b>
					</font>	
				</td>				
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan='11'>
	</td>
</tr>
<tr>
	<td align = 'left' colspan = '11' width = '100%'>
		<table cellspacing = '0' width = '100%' align = 'left'>
			<tr>
		";
		
		if($view_edit == "edit")
		{
			echo"
				<td align = 'right' width = '18%' valign = 'top'>
					<font color = 'red' size = '1'>UNUSED</font>
				</td>
				<td width = '2%'>
				</td>
			";
		}
		
		echo"
				<td align = 'right' width = '18%' valign = 'top'>
					<font color = 'red' size = '1'>UNIT</font>
				</td>
				<td width = '2%'>
				</td>
				<td align = 'left' width = '18%' valign = 'top'>
					<font color = 'red' size = '1'>MATERIAL</font>
				</td>
				<td width = '2%'>
				</td>
				<td width = '18%' valign = 'top'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'># in BATCH</font>
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>Count Value+Tier-1</font>
							</td>
						</tr>
					</table>
				</td>
				<td width = '2%'>
				</td>
				<td width = '18%' valign = 'top'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>MARKUP</font>
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>LINE VALUE</font>	
							</td>
						</tr>
					</table>
				</td>
				<td width = '2%'>
				</td>
				<td width = '18%' valign = 'top'>
					<font color = 'red' size = '1'>SELLER COMMENTS</font>
				</td>
			</tr>
		";
		
		$line_mat_total_mat = 0;
		$line_mat_total_tier = 0;
		$line_mat_total_markup = 0;
		$line_mat_total_count = 0;
		while($curpcmat = mysql_fetch_assoc($get_pc_material))
		{
			$line_mat_subtotal_value =0;
			$mat_count_adjustment = 0;
			$line_mat_subtotal = 0;
			$sell_pc_mat_count = 0;
			
			if($view_edit == "edit")
			{
				// $query = "edit ";
				$get_pc_mat_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_material_id = '$curpcmat[creature_material_id]' AND creature_identified = '1'") or die ("failed to get pc mat info.");
			}
			if($view_edit == "view")
			{
				// $query = "view ";
				$get_pc_mat_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_material WHERE creature_material_id = '$curpcmat[creature_object_id]' AND creature_identified = '1'") or die ("failed to get pc mat info.");
			}
			$gtpcmatnfo = mysql_fetch_assoc($get_pc_mat_info);
			
			if(isset($_POST['sell_pc_mat_'.$gtpcmatnfo[creature_material_id]]))
			{
				$flag = "isset";
				$sell_pc_mat_id = $_POST['sell_pc_mat_'.$gtpcmatnfo[creature_material_id]];
				$markup_pc_mat_value = $_POST['markup_pc_mat_value_'.$gtpcmatnfo[creature_material_id]];
				$sell_pc_mat_count = $_POST['sell_pc_mat_count_'.$gtpcmatnfo[creature_material_id]];
				$pc_mat_seller_comments = $_POST['pc_mat_seller_comments_'.$gtpcmatnfo[creature_material_id]];
				$get_pc_mat_count = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect_effect_subtype.effect_id = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '37' AND (".$slrp_prefix."effect.effect >= ".$curpcmat[creature_material_count]." AND ".$slrp_prefix."effect.effect_min_value <= ".$curpcmat[creature_material_count].")") or die ("failed to get pc mat count info isset.");
			}
			if(empty($_POST['sell_pc_mat_'.$gtpcmatnfo[creature_material_id]]))
			{
				$get_pc_mat_sale = mysql_query("SELECT * FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_object_id = '$gtpcmatnfo[creature_material_id]' AND focus_id = '26' AND creature_market_batch_id = '$creature_market_batch_id' AND creature_object_sale_count >= '1'") or die ("failed to get pc mat sale.");
				$gtpcmatsale = mysql_fetch_assoc($get_pc_mat_sale);
				$gtpcmatsalecnt = mysql_num_rows($get_pc_mat_sale);
				if($gtpcmatsalecnt >= 1)
				{
					$flag = "empty >1 $curpcmat[material]";
					$sell_pc_mat_id = $gtpcmatnfo[creature_material_id];
					$markup_pc_mat_value = $gtpcmatsale[creature_object_sale_markup];
					$sell_pc_mat_count = $gtpcmatsale[creature_object_sale_count];
					$pc_mat_seller_comments = $gtpcmatsale[creature_object_sale_comment];
				
					$get_pc_mat_count = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect_effect_subtype.effect_id = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '37' AND (".$slrp_prefix."effect.effect >= '$gtpcmatsale[creature_object_sale_count]' AND ".$slrp_prefix."effect.effect_min_value <= '$gtpcmatsale[creature_object_sale_count]')") or die ("failed to get pc mat count info empty.");
					$gtpcmatcount = mysql_fetch_assoc($get_pc_mat_count);
					// for multiples, don't start counting until 2 or more
					$mat_count_adjustment = ($gtpcmatcount[effect_tier]-1);
				}
				if($gtpcmatsalecnt == 0)
				{
					$flag = "empty $curpcmat[material]";
					$sell_pc_mat_id = $curpcmat[creature_material_id];
					$markup_pc_mat_value = 0;
					$sell_pc_mat_count = 0;
					$pc_mat_seller_comments = "";
				}
			}
			
			$get_pc_mat_unit = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."material ON ".$slrp_prefix."material.material_default_unit_id = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."material.material_id = '$curpcmat[material_id]'") or die ("failed to get pc mat unit info.");
			$gtpcmatunit = mysql_fetch_assoc($get_pc_mat_unit);
			
			$mat_instance = mysql_query("SELECT * FROM ".$slrp_prefix."material WHERE material_id = '$curpcmat[material_id]'") or die ("failed getting mat group instance desc.");
			$matinst = mysql_fetch_assoc($mat_instance);
			
			$material_instance_used = mysql_query("SELECT SUM(creature_object_sale_count) FROM ".$slrp_prefix."creature_object_for_sale INNER JOIN ".$slrp_prefix."creature_market_batch ON ".$slrp_prefix."creature_market_batch.creature_market_batch_id = ".$slrp_prefix."creature_object_for_sale.creature_market_batch_id WHERE ".$slrp_prefix."creature_object_for_sale.creature_object_id = '$gtpcmatnfo[creature_material_id]' AND ".$slrp_prefix."creature_object_for_sale.focus_id = '26' AND ".$slrp_prefix."creature_object_for_sale.creature_object_sale_count >= '1' AND ".$slrp_prefix."creature_market_batch.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_market_batch.market_batch_status_id != '1'") or die ("failed to get pc mat sale.");
			$matinstused = mysql_fetch_array($material_instance_used, MYSQL_NUM);
			$matinstleft = $gtpcmatnfo[creature_material_count] - $matinstused[0];
			
			if($gtpcmatnfo[creature_identified] >= 1)
			{
				$mat_instance_display_name = stripslashes($matinst[material]);
			}
			if($gtpcmatnfo[creature_identified] == 0)
			{
				$mat_instance_display_name = stripslashes($matinst[material_short_name]);
			}

			// then add that to the tier to get the value
			if($sell_pc_mat_count >= 1)
			{
				$line_mat_subtotal_value = $mat_count_adjustment + $matinst[material_tier];
				$line_mat_subtotal = $markup_pc_mat_value + $line_mat_subtotal_value;
				$line_mat_total_mat = $line_mat_total_mat + $line_mat_subtotal;
			}
			$line_mat_total_tier = $line_mat_total_tier + $line_mat_subtotal_value;
			$line_mat_total_markup = $line_mat_total_markup + $markup_pc_mat_value;
			$line_mat_total_count = $line_mat_total_count + $sell_pc_mat_count;
			
			$crmtlcnt = 1;
			$crmtllft = $sell_pc_mat_count+$matinstleft;
			
			echo"
<tr>
			";
			
			// debugging
			// echo"<font color = 'purple'><li> $mat_instance_display_name</font>";
			// echo"<li> id: $sell_pc_mat_id";
			// echo"<li> value: $markup_pc_mat_value";
			// echo"<li> ct: $sell_pc_mat_count";
			// echo"<li> comment: $pc_mat_seller_comments";			
			// echo"<li> $query $flag";		
			
			if($view_edit == "edit")
			{
				echo"
	<td align = 'right' width='18%'>
		<font color = 'orange' size = '2'><b>$matinstleft</b></font>
	</td>
	<td width = '2%'>
	</td>
				";
			}
			
			echo"
	<td align = 'right' width='18%' valign = 'middle'>
		<font color = 'orange' size = '2'><b>$gtpcmatunit[effect] &nbsp; of</b></font>
	</td>
	<td width = '2%'>
	</td>
	<td align = 'left' valign = 'middle' width='18%'>
		<font color = 'orange' size = '2'><b>
			";
			
			if($gtpcmatnfo[creature_identified] == 0)
			{
				echo"* ";
			}
			
			echo"$mat_instance_display_name
	</b></font>
	</td>
			";
			
			$pcmatcnt = $gtpcmatnfo[creature_material_count];
			
			echo"			
	<td width = '2%'>
	</td>
		<input type='hidden' value='$gtpcmatnfo[creature_material_id]' name='sell_pc_mat_$gtpcmatnfo[creature_material_id]'>
	<td width='18%'>
		<table width = '100%' cellspacing = '0'>
			<tr>
				<td valign = 'top' align = 'left' width = '49%'>
			";
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($crmtllft >= 1)
				{
					echo"<select class='engine' name = 'sell_pc_mat_count_$gtpcmatnfo[creature_material_id]'>";
				
					if(isset($sell_pc_mat_count))
					{
						echo"<option value = '$sell_pc_mat_count'>";
					}
				}
			}
			
			if($view_edit == "view")
			{
				if(isset($sell_pc_mat_count))
				{
					echo"$sell_pc_mat_count";
				}
			}
			
			if($view_edit == "edit")
			{
				echo"$sell_pc_mat_count";
			}
			
			if($view_edit == "view")
			{
				echo" (+$mat_count_adjustment value)";
			}
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($crmtllft >= 1)
				{
					echo"</option>";
					if($sell_pc_mat_count >= 1)
					{
						echo"<option value = '0'>0</option>";
					}
					
					while($crmtlcnt <= $crmtllft)
					{
						echo"<option value = '$crmtlcnt'>$crmtlcnt</option>";
						
						$crmtlcnt++;
					}
					
					echo"</select>";
				}
			}
			
			echo"
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '49%'>
							";
							
							if($sell_pc_mat_count >= 1)
							{
								echo"$line_mat_subtotal_value";
							}
							
							echo"
				</td>
			</tr>
		</table>
	</td>
	<td width = '2%'>
	</td>
	<td width='18%'>
		<table width = '100%' cellspacing = '0'>
			<tr>
				<td valign = 'top' align = 'left' width = '49%'>
			";
		
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($crmtllft >= 1)
				{
					echo"<select class='engine' name = 'markup_pc_mat_value_$gtpcmatnfo[creature_material_id]'>";
					if(isset($markup_pc_mat_value))
					{
						echo"<option value='$markup_pc_mat_value'>";
					}
				}
			}
			
			if(isset($markup_pc_mat_value))
			{
				echo"$markup_pc_mat_value";
			}
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($crmtllft >= 1)
				{
					echo"
						</option>
						<option value = '0'>0</option>
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
						</select>
					";
				}
			}
			
			echo"
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '49%'>
							";
							
							if($sell_pc_mat_count >= 1)
							{
								echo"$line_mat_subtotal";
							}
							
							echo"
				</td>
			</tr>
		</table>
	</td>
	<td width = '2%'>
	</td>
	<td align = 'left' width = '18%'>
			";
				
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($crmtllft >= 1)
				{
					echo"<input cols='40' type='text' class='textbox3' value = '";
				}
			}
			
			if(isset($pc_mat_seller_comments))
			{
				echo"$pc_mat_seller_comments";
			}
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($crmtllft >= 1)
				{
					echo"' name='pc_mat_seller_comments_$gtpcmatnfo[creature_material_id]'>";
				}
			}
			
			echo"
	</td>
</tr>
			";
		}
	
		echo"
<tr height = '9'>
			";
			
			if($view_edit == "edit")
			{
				echo"
	<td align = 'right' width = '18%' valign = 'top'>
	</td>
	<td width = '2%'>
	</td>
				";
			}
			
			echo"
	<td valign = 'top' align = 'left' width = '18%'>
	</td>
	<td width = '2%'>
	</td>
	<td valign = 'top' align = 'left' width = '18%'>
	</td>
	<td width = '2%'>
	</td>
	<td background='themes/RedShores/images/row2.gif' valign = 'top' align = 'left' width = '32%' colspan = '3'>
	</td>
	<td width = '2%'>
	</td>
	<td valign = 'top' align = 'left' width = '18%'>
	</td>
</tr>
<tr>
			";
			
			if($view_edit == "edit")
			{
				echo"
	<td align = 'right' width = '18%' valign = 'top'>
	</td>
	<td width = '2%'>
	</td>
				";
			}
			
			echo"
	<td align = 'right' width = '18%'>
		<font color = 'red' size = '1'></font>
	</td>
	<td width = '2%'>
	</td>
	<td align = 'left' width = '18%'>
		<font color = 'red' size = '1'></font>
	</td>
	<td width = '2%'>
	</td>
	<td width = '18%'>
		<table width = '100%' cellspacing = '0'>
			<tr>
				<td valign = 'top' align = 'left' width = '49%'>
					<font color = 'red' size = '1'>SUBTOTAL</font>
					<br>
					$line_mat_total_count
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '49%'>
					<font color = 'red' size = '1'>SUBTOTAL</font>
					<br>
					$line_mat_total_tier
				</td>
			</tr>
		</table>
	</td>
	<td width = '2%'>
	</td>
	<td width = '18%'>
		<table width = '100%' cellspacing = '0'>
			<tr>
				<td valign = 'top' align = 'left' width = '49%'>
					<font color = 'red' size = '1'>SUBTOTAL</font>
					<br>
					$line_mat_total_markup
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '49%'>
					<font color = 'red' size = '1'>SUBTOTAL</font>
					<br>
					$line_mat_total_mat
				</td>
			</tr>
		</table>
	</td>
	<td width = '2%'>
	</td>
	<td width = '18%'>
	</td>
</tr>
		";
	}
	
	if($view_edit == "edit")
	{
		$get_pc_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item INNER JOIN ".$slrp_prefix."item ON ".$slrp_prefix."creature_item.item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."creature_item.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_item.creature_item_count >= '1' ORDER BY ".$slrp_prefix."item.item") or die ("failed to get pc item info.");
	}
	if($view_edit == "view")
	{
		$get_pc_item_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_object_for_sale INNER JOIN ".$slrp_prefix."creature_item ON ".$slrp_prefix."creature_object_for_sale.creature_object_id = ".$slrp_prefix."creature_item.creature_item_id WHERE ".$slrp_prefix."creature_object_for_sale.creature_market_batch_id = '$creature_market_batch_id' AND ".$slrp_prefix."creature_object_for_sale.focus_id = '10'") or die ("failed to get batch item info.");
	}
	
	$curpcitmcnt = mysql_num_rows($get_pc_item_info);
	
	if($curpcitmcnt >= 1)
	{
		echo"
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan='11' width = '100%'>
	</td>
</tr> 
<tr>
	<td valign = 'top' width = '100%' colspan = '11'>
		<table width = '100%' cellspacing = '0'>
			<tr background='themes/RedShores/images/base1.gif' height='24'>
				<td valign = 'middle' align = 'left' width = '18%'>
					<font color = 'red' size = '2'>
					<b>ITEMS</b>
					</font>	
				</td>				
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td valign = 'top' align = 'left' width = '49%'>
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' align = 'left' width = '49%'>
							</td>
						</tr>
					</table>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan='11' width = '100%'>
	</td>
</tr>
<tr>
	<td align = 'left' colspan = '11' width = '100%'>
		<table cellspacing = '0' width = '100%' align = 'left'>
			<tr>
		";
		
		if($view_edit == "edit")
		{
			echo"
				<td align = 'right' width = '15%' valign = 'top'>
					<font color = 'red' size = '1'>UNUSED</font>
				</td>
				<td width = '2%'>
				</td>
			";
		}

		echo"
				<td align = 'center' width='32%' colspan = '3' valign = 'top'>								
					<font color = 'red' size = '1'>ITEM</font>
				</td>
				<td width = '2%'>
				</td>
				<td width='18%' valign = 'top'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'># in BATCH
								</font>
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>Count Value+Tier</font>	
							</td>
						</tr>
					</table>
				</td>
				<td width = '2%'>
				</td>
				<td width='18%' valign = 'top'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>MARKUP</font>
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>LINE VALUE</font>	
							</td>
						</tr>
					</table>
				</td>
				<td width = '2%'>
				</td>
				<td width='18%' valign = 'top'>
				<font color = 'red' size = '1'>SELLER COMMENTS</font>	
				</td>
			</tr>
		";
		
		$line_item_total_item = 0;
		$line_item_total_tier = 0;
		$line_item_total_markup = 0;
		$line_item_totl_count = 0;

		while($curpcitm = mysql_fetch_assoc($get_pc_item_info))
		{
			$line_item_subtotal_value =0;
			$item_count_adjustment = 0;
			$line_item_subtotal = 0;
			$sell_pc_itm_count = 0;
			
			if($view_edit == "edit")
			{
				$get_pc_itm_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_item_id = '$curpcitm[creature_item_id]' AND creature_identified = '1'") or die ("failed to get pc itm info.");
			}
			if($view_edit == "view")
			{		
				$get_pc_itm_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature_item WHERE creature_item_id = '$curpcitm[creature_object_id]' AND creature_identified = '1'") or die ("failed to get pc itm info.");
			}
			$gtpcitmnfo = mysql_fetch_assoc($get_pc_itm_info);
			
			if(isset($_POST['sell_pc_itm_'.$gtpcitmnfo[creature_item_id]]))
			{
				$sell_pc_item_id = $_POST['sell_pc_itm_'.$gtpcitmnfo[creature_item_id]];
				$markup_pc_itm_value = $_POST['markup_pc_itm_value_'.$gtpcitmnfo[creature_item_id]];
				$sell_pc_itm_count = $_POST['sell_pc_itm_count_'.$gtpcitmnfo[creature_item_id]];
				$pc_itm_seller_comments = $_POST['pc_itm_seller_comments_'.$gtpcitmnfo[creature_item_id]];
			}
			if(empty($_POST['sell_pc_itm_'.$gtpcitmnfo[creature_item_id]]))
			{
				$get_pc_itm_sale = mysql_query("SELECT * FROM ".$slrp_prefix."creature_object_for_sale WHERE creature_object_id = '$gtpcitmnfo[creature_item_id]' AND focus_id = '10' AND creature_market_batch_id = '$creature_market_batch_id' AND creature_object_sale_count >= '1'") or die ("failed to get pc itm sale.");
				$gtpcitmsale = mysql_fetch_assoc($get_pc_itm_sale);
				$gtpcitmsalecnt = mysql_num_rows($get_pc_itm_sale);
				if($gtpcitmsalecnt >= 1)
				{
					$flag = "empty >1 $curpcitm[item]";
					$sell_pc_item_id = $gtpcitmnfo[creature_item_id];
					$markup_pc_itm_value = $gtpcitmsale[creature_object_sale_markup];
					$sell_pc_itm_count = $gtpcitmsale[creature_object_sale_count];
					$pc_itm_seller_comments = $gtpcitmsale[creature_object_sale_comment];
				
					$get_pc_itm_count = mysql_query("SELECT * FROM ".$slrp_prefix."effect INNER JOIN ".$slrp_prefix."effect_effect_subtype ON ".$slrp_prefix."effect_effect_subtype.effect_id = ".$slrp_prefix."effect.effect_id WHERE ".$slrp_prefix."effect_effect_subtype.effect_subtype_id = '37' AND (".$slrp_prefix."effect.effect >= '$gtpcitmsale[creature_object_sale_count]' AND ".$slrp_prefix."effect.effect_min_value <= '$gtpcitmsale[creature_object_sale_count]')") or die ("failed to get pc itm count info empty.");
					$gtpcitmcount = mysql_fetch_assoc($get_pc_itm_count);
					// for multiples, don't start counting until 2 or more
					$itm_count_adjustment = ($gtpcitmcount[effect_tier]-1);
				}
				if($gtpcitmsalecnt == 0)
				{
					$flag = "empty $curpcitm[item]";
					$sell_pc_itm_id = $curpcitm[creature_item_id];
					$markup_pc_itm_value = 0;
					$sell_pc_itm_count = 0;
					$pc_itm_seller_comments = "";
				}
			}
			
			$instance = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtpcitmnfo[item_id]'") or die ("failed getting group instance desc.");
			$inst = mysql_fetch_assoc($instance);
			$instance_quality = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$gtpcitmnfo[creature_item_quality_id]'") or die ("failed getting item instance quality desc.");
			$instqty = mysql_fetch_assoc($instance_quality);
			$instance_used = mysql_query("SELECT SUM(creature_object_sale_count) FROM ".$slrp_prefix."creature_object_for_sale INNER JOIN ".$slrp_prefix."creature_market_batch ON ".$slrp_prefix."creature_market_batch.creature_market_batch_id = ".$slrp_prefix."creature_object_for_sale.creature_market_batch_id WHERE ".$slrp_prefix."creature_object_for_sale.creature_object_id = '$gtpcitmnfo[creature_item_id]' AND ".$slrp_prefix."creature_object_for_sale.focus_id = '10' AND ".$slrp_prefix."creature_object_for_sale.creature_object_sale_count >= '1' AND ".$slrp_prefix."creature_market_batch.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_market_batch.market_batch_status_id != '1'") or die ("failed to get pc itm sale.");
			$instused = mysql_fetch_array($instance_used, MYSQL_NUM);
			$instleft = $gtpcitmnfo[creature_item_count] - $instused[0];
			
			
			if($gtpcitmnfo[creature_identified] >= 1)
			{
				$item_instance_display_name = stripslashes($inst[item]);
			}
			if($gtpcitmnfo[creature_identified] == 0)
			{
				$item_instance_display_name = stripslashes($inst[item_short_name]);
			}
			
			// items in particular are worth 1 more in value, finsihed goods vs. raw materials
			if($sell_pc_itm_count >= 1)
			{
				$line_item_subtotal_value = $item_count_adjustment + $inst[item_tier] + 1;
				$line_item_subtotal = $markup_pc_itm_value + $line_item_subtotal_value;
				$line_item_total_item = $line_item_total_item + $line_item_subtotal;
			}
			$line_item_total_tier = $line_item_total_tier + $line_item_subtotal_value;
			$line_item_total_markup = $line_item_total_markup + $markup_pc_itm_value;
			$line_item_total_count = $line_item_total_count + $sell_pc_itm_count;
			
			$critmcnt = 1;
			$critmlft = $sell_pc_itm_count+$instleft;
			
			echo"	<tr>";
			
			// debugging
			// echo"<font color = 'purple'><li> $item_instance_display_name</font>";
			// echo"<li> id: $sell_pc_itm_id";
			// echo"<li> value: $markup_pc_itm_value";
			// echo"<li> ct: $sell_pc_itm_count";
			// echo"<li> comment: $pc_itm_seller_comments";
			// echo"<li> $query $flag";
			
			if($view_edit == "edit")
			{
				echo"
				<td align = 'right' width='18%' valign = 'top'>
					<font color = 'orange' size = '2'><b>$instleft &nbsp; of</b></font>
				</td>
				<td width = '2%'>
				</td>
				";
			}

			echo"
				<td align = 'center' width='32%' colspan = '3' valign = 'top'>
					<font color = 'orange' size = '2'><b>
			";
					
			if($gtpcitmnfo[creature_identified] == 0)
			{
				echo"* ";
			}
			
			echo"$item_instance_display_name ($instqty[effect])</font></b>";
			
			
			$listed_item_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."item_subtype INNER JOIN ".$slrp_prefix."item_item_subtype ON ".$slrp_prefix."item_subtype.item_subtype_id = ".$slrp_prefix."item_item_subtype.item_subtype_id WHERE ".$slrp_prefix."item_item_subtype.item_id = '$inst[item_id]'") or die ("failed getting listed item_subtype.");
			$listitmsub = mysql_fetch_assoc($listed_item_subtype);
			// echo"list item sub: $listitmsub[item_subtype_id], book? $gtpcitmnfo[creature_item_book_id]<br>";
			// echo"<br>item id: $sell_pc_item_id, count: $sell_pc_itm_count <br>markup: $markup_pc_itm_value<br>$line_item_subtotal_value = $inst[item_tier] + $item_count_adjustment + 1<br>";
			
			if($listitmsub[item_subtype_id] >= 89)
			{
				if($listitmsub[item_subtype_id] <= 93)
				{
					if($gtpcitmnfo[creature_item_book_id] == 1)
					{
						echo"<br><font color = 'orange'><b><i><li>Tabula Rasa</i></b>";
					}
					if($gtpcitmnfo[creature_item_book_id] >= 2)
					{
						$item_book_info = mysql_query("SELECT * FROM ".$slrp_prefix."item_book WHERE item_book_id = '$gtpcitmnfo[creature_item_book_id]'") or die ("failed getting item_book_info.");
						while($itmbknfo = mysql_fetch_assoc($item_book_info))
						{
							$item_book_ability = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id = '$itmbknfo[ability_id]'") or die("failed getting item book ability.");
							$itmbkab = mysql_fetch_assoc($item_book_ability);
							
							$item_book_abrand = mysql_query("SELECT * FROM ".$slrp_prefix."object_random WHERE object_random_id = '$itmbknfo[ability_object_random_id]'") or die("failed getting item book ability_random.");
							$itmbkabrnd = mysql_fetch_assoc($item_book_abrand);
							// echo"<font color = 'green'>$itmbkab[ability]</font><br>";
							$rejection = 0;
							// get the attributes list
							$all_attr = mysql_query("SELECT * FROM ".$slrp_prefix."attribute_type WHERE attribute_type_id > '1'") or die ("failed getting attribute exclusion list.");
							while($attrs = mysql_fetch_assoc($all_attr))
							{
								// echo"attr1: $attrs[attribute_type]<br>";
								// get mods pointing at the attribute and modifying this ability
								$required_attrs = mysql_query("SELECT * FROM ".$slrp_prefix."ability_ability_modifier INNER JOIN ".$slrp_prefix."ability_modifier_subfocus ON ".$slrp_prefix."ability_modifier_subfocus.ability_modifier_id = ".$slrp_prefix."ability_ability_modifier.ability_modifier_id WHERE ".$slrp_prefix."ability_modifier_subfocus.focus_id = '4' AND ".$slrp_prefix."ability_modifier_subfocus.subfocus_id = '$attrs[attribute_type_id]' AND ".$slrp_prefix."ability_ability_modifier.ability_id = '$itmbkab[ability_id]'") or die ("failed getting required attrs.");
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
			
							// see if they have the required effects
							$required_efftyps = mysql_query("SELECT * FROM ".$slrp_prefix."effect_type INNER JOIN ".$slrp_prefix."ability_effect_type ON ".$slrp_prefix."ability_effect_type.effect_type_id = ".$slrp_prefix."effect_type.effect_type_id WHERE ".$slrp_prefix."ability_effect_type.ability_id = '$itmbkab[ability_id]'");
							$getrqefftypscnt = mysql_num_rows($required_efftyps);
							$rndstrsum = "";
							$rqtrcnt = $getrqefftypscnt;
							
							while($reqefftyp = mysql_fetch_assoc($required_efftyps))
							{
								$required_tiers = mysql_query("SELECT * FROM ".$slrp_prefix."ability_effect_type WHERE effect_type_id = '$reqefftyp[effect_type_id]' AND ability_id = '$itmbkab[ability_id]'");
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
								// echo"eff rejection +1 = $rejection<br>";
							}
							
							// if they have the required effects (counter is zero)
							if($rqtrcnt == 0)
							{
	
							}
							
							// echo"rejection: $rejection<br>";
						}
						
						if($rejection == 0)
						{
							echo"
					<font color = 'red'>
					<br><b>$itmbkab[ability]</b>: <font color = 'blue'>$itmbkabrnd[object_random]</font>
							";
						}
						if($rejection >= 1)
						{
							echo"<br><font color = 'blue' size = '2'><b>$itmbkabrnd[object_random]<br>";
						}
					}
				}
			}
			
			echo"
				</td>		
				<td width = '2%'>
				</td>
					<input type='hidden' value='$gtpcitmnfo[creature_item_id]' name='sell_pc_itm_$gtpcitmnfo[creature_item_id]'>
				<td width='18%' valign = 'top'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td valign = 'top' align = 'left' width = '49%'>
			";
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($critmlft >= 1)
				{
					echo"<select class='engine' name = 'sell_pc_itm_count_$gtpcitmnfo[creature_item_id]'>";
					
					if(isset($sell_pc_itm_count))
					{
						echo"<option value = '$sell_pc_itm_count'>";
					}
				}
			}
			
			if(isset($sell_pc_itm_count))
			{
				echo"$sell_pc_itm_count";
			}
			
			if($view_edit == "view")
			{
				echo" (+$item_count_adjustment value)";
			}
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($critmlft >= 1)
				{
					echo"</option>";
					if($sell_pc_itm_count >= 1)
					{
						echo"<option value = '0'>0</option>";
					}
					
					while($critmcnt <= $critmlft)
					{
						echo"<option value = '$critmcnt'>$critmcnt</option>";
						
						$critmcnt++;
					}
					
					echo"</select>";
				}
			}			
			echo"
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' align = 'left' width = '49%'>
			";
			
			if($sell_pc_itm_count >= 1)
			{
				echo"$line_item_subtotal_value";
			}
			
			echo"
							</td>
						</tr>
					</table>
				</td>
				<td width = '2%'>
				</td>
				<td width='18%' valign = 'top'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td valign = 'top' align = 'left' width = '49%'>
			";
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($critmlft >= 1)
				{
					echo"<select class='engine' name = 'markup_pc_itm_value_$gtpcitmnfo[creature_item_id]'>";
					if(isset($markup_pc_itm_value))
					{
						echo"<option value='$markup_pc_itm_value'>";
					}
				}
			}
			
			if(isset($markup_pc_itm_value))
			{
				echo"$markup_pc_itm_value";
			}
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($critmlft >= 1)
				{
					echo"		</option>
									<option value = '0'>0</option>
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
									</select>
					";
				}
			}
			
			echo"
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' align = 'left' width = '49%'>
			";
			
			if($sell_pc_itm_count >= 1)
			{
				echo"$line_item_subtotal";
			}
			
			echo"
							</td>
						</tr>
					</table>
				</td>
				<td width = '2%'>
				</td>
				<td align = 'left' width='18%' valign = 'top'>
			";
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($critmlft >= 1)
				{
					echo"<input cols='40' type='text' class='textbox3' value = '";
				}
			}
			
			if(isset($pc_itm_seller_comments))
			{
				echo"$pc_itm_seller_comments";
			}
			
			if($view_edit == "edit")
			{
				// limit the amount to what they have plius what is in this batch
				if($critmlft >= 1)
				{
					echo"' name='pc_itm_seller_comments_$gtpcitmnfo[creature_item_id]'>";
				}
			}
			
			echo"
				</td>
			</tr>
			";
			
		}
	
	echo"
			<tr height = '9'>
	";
		
	if($view_edit == "edit")
	{
		echo"
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
		";
	}
	
	echo"
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
				<td background='themes/RedShores/images/row2.gif' valign = 'top' align = 'left' width = '32%' colspan = '3'>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
			</tr>
			<tr>
	";
		
	if($view_edit == "edit")
	{
		echo"
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
		";
	}
	
	echo"
				<td align = 'left' width = '18%'>
					<font color = 'red' size = '1'></font>
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '18%'>
				</td>
				<td width = '2%'>
				</td>
				<td width = '18%'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>SUBTOTAL</font>
								<br>
								$line_item_total_count
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>SUBTOTAL</font>
								<br>
								$line_item_total_tier
							</td>
						</tr>
					</table>
				</td>
				<td width = '2%'>
				</td>
				<td width = '18%'>
					<table width = '100%' cellspacing = '0'>
						<tr>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>SUBTOTAL</font>
								<br>
								$line_item_total_markup
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' align = 'left' width = '49%'>
								<font color = 'red' size = '1'>SUBTOTAL</font>
								<br>
								$line_item_total_item
							</td>
						</tr>
					</table>
				</td>
				<td width = '2%'>
				</td>
				<td width = '18%'>
				
				</td>
			</tr>
		</table>
	</td>
</tr>
		";
	}
	
	$final_batch_count_total = $line_mat_total_count + $line_item_total_count;
	$final_batch_value_total = $line_mat_total_tier + $line_item_total_tier;
	$final_batch_markup_total = $line_mat_total_markup + $line_item_total_markup;
	$final_batch_total = $line_mat_total_mat + $line_item_total_item;
	$update_trade_batch_value = mysql_query("UPDATE ".$slrp_prefix."creature_market_batch SET market_batch_value = '$final_batch_total' WHERE creature_market_batch_id = '$creature_market_batch_id'") or die ("failed updating trade batch value info");
	
	echo"
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan='11'>
	</td>
</tr>
<tr>
	";
	
	if($view_edit == "edit")
	{
		echo"
	<td valign = 'top' align = 'left' width = '18%'>
	</td>
	<td width = '2%'>
	</td>
		";
	}
	
	echo"
	<td align = 'center' width = '32%' colspan = '3'>
				<font color='red' size = '2'>
				<b>ENDING	$end_batch_date_normal_full
				</font>
	</td>
	<td width = '2%'>
	</td>

	<td width = '18%'>
		<table width = '100%' cellspacing = '0'>
			<tr>
				<td valign = 'top' align = 'left' width = '49%'>
					<font color = 'red' size = '1'>TOTAL</font>
					<br>
					$final_batch_count_total
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '49%'>
					<font color = 'red' size = '1'>TOTAL</font>
					<br>
					$final_batch_value_total
				</td>
			</tr>
		</table>
	</td>
	<td width = '2%'>
	</td>
	<td width = '18%'>
		<table width = '100%' cellspacing = '0'>
			<tr>
				<td valign = 'top' align = 'left' width = '49%'>
					<font color = 'red' size = '1'>TOTAL</font>
					<br>
					$final_batch_markup_total
				</td>
				<td width = '2%'>
				</td>
				<td valign = 'top' align = 'left' width = '49%'>
					<font color = 'red' size = '1'>TOTAL</font>
					<br>
					$final_batch_total
				</td>
			</tr>
		</table>
	</td>
	<td width = '2%'>
	</td>
	<td width = '18%'>
	
	</td>
</tr>
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan='11'>
	</td>
</tr>
	";
	
	// end of the main pane
	// start of the bottom buttons
	
	echo"<tr><td width = '100%' colspan = '11' align = 'left' valign = 'top'><table width = '100%' cellspacing = '0'>";
	$end_batch = date_parse($time);
	
	if($view_edit == "edit")
	{
		echo"
			<tr background='themes/RedShores/images/base1.gif' height='24'>
				<td align = 'left' width = '58%' colspan = '5'>
					<font color='red' size='2'>End Yr.: 
					<font size='1'><b>
					<select class='engine' name = 'end_batch_year'>
					<option value = '$end_batch[year]'>$end_batch[year]</option>
					<option value = '2013'>2013</option>
					<option value = '2014'>2014</option>
					<option value = '2015'>2015</option>
					</select> M. <select class='engine' name = 'end_batch_month'>
					<option value = '$end_batch[month]'>$end_batch[month]</option>
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
					</select> D. <select class='engine' name = 'end_batch_day'>
					<option value = '$end_batch[day]'>$end_batch[day]</option>
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
					</select>	at <select class='engine' name = 'end_batch_hour'>
					<option value = '$end_batch[hour]'>$end_batch[hour]</option>
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
					</select> :00
				</b></font>
				</td>
				<td width = '2%'>
				</td>
				<td width = '32%' colspan = '3' valign = 'middle'>
					<font size='1' color='red'>Change Status:</font>
					<select class='engine' name = 'trade_batch_status'>
					";
					if($curtrdbtchstts[slurp_status_id] != 1)
					{
						echo"<option value = '$curtrdbtchstts[slurp_status_id]'>$curtrdbtchstts[slurp_alt_status2]</option>";
					}
					
					echo"
					<option value='0'>Banked</option>
					<option value='3'>For Sale</option>
					<option value='2'>Current Bid</option>
					</select>
				</td>
				<td width = '2%'>
				</td>
				<td width = '18%' valign = 'middle' align = 'center'>
					<input type='hidden' value='$gttrdbtch[creature_market_batch_id]' name='creature_market_batch_id'>
					<input type='hidden' value='$final_batch_total' name='creature_market_batch_value'>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$component_expander' name = 'component_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='edit' name='view_edit'>
					<input type='hidden' value='1' name='char_expander'>
					<input type='submit' name='sell_trade_batch' value='Update $gttrdbtch[market_batch]'>
				</td>
				<td width = '2%'>
				</td>
				<td width = '18%' valign = 'middle' align = 'center'>
				</td>
			</tr>
			<tr background='themes/RedShores/images/row2.gif' height='9'>
				<td colspan = '11' width = '100%'>
				</td>
			</tr>
		";
	}
	
	echo"	</form>";
}

$bottom_cols = 9;
$back_to_main_width = 32;
echo"
			<tr background='themes/RedShores/images/base1.gif' height='24'>
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

if($view_edit == "view")
{
	if($curusrslrprnk[slurp_rank_id] >= 5)
	{
		if($trdbtchownr[creature_id] == $curpcnfo[creature_id])
		{
			$bottom_cols = 7;
			$back_to_main_width = 18;
			echo"
				<form name = 'edit_batch' method='post' action = 'modules.php?name=$module_name&file=pc_market_form'>
				<td valign = 'middle' align = 'left' width = '18%'>
					<input type='hidden' value='$gttrdbtch[creature_market_batch_id]' name='creature_market_batch_id'>
					<input type='hidden' value='$final_batch_total' name='creature_market_batch_value'>
					<input type='hidden' value='$trdbtchownr[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$component_expander' name = 'component_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='edit' name='view_edit'>
					<input type='hidden' value='1' name='char_expander'>
					<input type='submit' value='Edit $gttrdbtch[market_batch]' name='edit_batch'>
				</td>
				</form>
				<td width = '2%'>
				</td>
			";
		}
	}
}

if($view_edit == "view")
{
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		$bottom_cols = 7;
		$back_to_main_width = 18;
		echo"
				<form name = 'edit_batch' method='post' action = 'modules.php?name=$module_name&file=pc_market_form'>
				<td valign = 'middle' align = 'left' width = '18%'>
					<input type='hidden' value='$gttrdbtch[creature_market_batch_id]' name='creature_market_batch_id'>
					<input type='hidden' value='$final_batch_total' name='creature_market_batch_value'>
					<input type='hidden' value='$trdbtchownr[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$component_expander' name = 'component_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
					<input type='hidden' value='edit' name='view_edit'>
					<input type='hidden' value='1' name='char_expander'>
					<input type='submit' value='Admin Edit $gttrdbtch[market_batch] [$trdbtchownr[creature]]' name='edit_batch'>
				</td>
				</form>
				<td width = '2%'>
				</td>	
		";
	}
}

echo"
				<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
				<td colspan = '$bottom_cols' valign = 'middle' align = 'right' width = '$back_to_main_width%'>
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
				<td width = '2%'>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan = '11' width = '100%'>
	</td>
</tr>
";

// close the block so the footer bahaves...
echo"
		</table>
	</td>
</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>