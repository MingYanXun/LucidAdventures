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
$nav_title = "Abilities List";
include("modules/$module_name/includes/slurp_header.php");

// echo"exp: $compab_expander<br>";
OpenTable3();

echo"
<table cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td width='32%' align='right' valign='top'>
";

if($curpcnfo[creature_id] >= 2)
{
	echo"
			<form name = 'pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='char' name='current_expander'>
			<input class='submit3' type='submit' value='Go to $current_character_information' name='go_to_edit'>
			</form>
	";
	
	if($curpcnfo[creature_status_id] == 2)
	{
		echo"
			<form name = 'pc_eff_typ' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
			<input type='hidden' value='ab' name='current_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input class='submit3' type='submit' value='Go to Abilities for $curpcnfo[creature]' name='go_to_eff_typ'>
			</form>
		";
	}
	
	if($curpcnfo[creature_status_id] == 3)
	{
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"
			<form name = 'pc_eff_typ' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
			<input type='hidden' value='ab' name='current_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input class='submit3' type='submit' value='Go to Abilities for $curpcnfo[creature]' name='go_to_eff_typ'>
			</form>
			";
		}
	}
	
	if($curpcnfo[creature_status_id] == 4)
	{
		echo"
			<form name = 'pc_eff_typ' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
			<input type='hidden' value='ab' name='current_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input class='submit3' type='submit' value='Go to Abilities for $curpcnfo[creature]' name='go_to_eff_typ'>
			</form>
		";
	}
}

if($compab_expander == 1)
{
	echo"
			<form name = 'show_hide_components' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='0' name = 'compab_expander'>
			<input class='submit3' type='submit' value='Hide Components'>
			</form>
	";
}

if($compab_expander == 0)
{
	echo"
			<form name = 'show_hide_components' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='1' name = 'compab_expander'>
			<input class='submit3' type='submit' value='Show Components'>
			</form>
	";
}

echo"
			<form name = 'show_hide_instructions' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
";

if($ntro_expander == 1)
{
	echo"
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
			<input type='hidden' value='0' name = 'ntro_expander'>
			<input class='submit3' type='submit' value='Hide Instructions'>
	";
}

if($ntro_expander == 0)
{
	echo"

			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
			<input type='hidden' value='1' name = 'ntro_expander'>
			<input class='submit3' type='submit' value='Show Instructions'>
	";
}

// filter handler
if(isset($_POST['filter_by_sp_eff_id']))
{
	$filter_by_sp_eff_id = $_POST['filter_by_sp_eff_id'];
}
else
{
	$filter_by_sp_eff_id = 1;
}

//set a counter to know if it's the first condition or not
$sp_eff_sort_string = "";

// check the filter after it is compiled
 // echo"id: $filter_by_sp_eff_id, STRING: $sp_eff_sort_string<br>";

$current_filter_effect_special = mysql_query("SELECT * FROM ".$slrp_prefix."effect_special WHERE effect_special_id = '$filter_by_sp_eff_id'") or die ("failed getting current sp eff filter.");
$crrfltreffspc = mysql_fetch_assoc($current_filter_effect_special);

// filter handler
if(isset($_POST['filter_by_ab_set_id']))
{
	$filter_by_ab_set_id = $_POST['filter_by_ab_set_id'];
}
else
{
	$filter_by_ab_set_id = 1;
}

//set a counter to know if it's the first condition or not
$filter_query_add_count = 0;
$sort_string = "";

// if the filter is chosen
if($filter_by_ab_set_id >= 2)
{
	$filter_query_add_count++;
	$filter_query_add_count++;
	
	$sort_string = $sort_string." AND ".$slrp_prefix."ability.ability_set_id = $filter_by_ab_set_id";
}

// if the filter is empty
if($filter_by_ab_set_id <= 1)
{
	$filter_query_add_count++;
	
	$sort_string = $sort_string." AND ".$slrp_prefix."ability.ability_set_id > 1";
}

// check the filter after it is compiled
// echo"STRING: $sort_string<br>";

// sort by status
// filter handler
if(isset($_POST['filter_by_status']))
{
	$filter_by_status = $_POST['filter_by_status'];
}
if(empty($_POST['filter_by_status']))
{
	if($filter_by_ab_set_id == 15)
	{
		$filter_by_status = 5;
	}
	if($filter_by_ab_set_id == 12)
	{
		$filter_by_status = 5;
	}
	if($filter_by_ab_set_id != 12)
	{
		if($filter_by_ab_set_id != 15)
		{
			$filter_by_status = 4;
		}
	}
}
// echo"status: $filter_by_status<br>";

if($filter_query_add_count >= 3)
{
	if($filter_by_sp_eff_id >= 2)
	{
	 	$abnames = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_id > '1' AND ".$slrp_prefix."ability.ability_status_id = '$filter_by_status' ".$sort_string." AND ".$slrp_prefix."ability.ability_id IN (SELECT ".$slrp_prefix."ability.ability_id FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_effect_special ON ".$slrp_prefix."ability_effect_special.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability_effect_special.effect_special_id = '$filter_by_sp_eff_id') ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting filter by ab set 2.");
	}
	
	if($filter_by_sp_eff_id == 1)
	{
		// echo"going1<br>";
		$abnames = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_id > '1' AND ".$slrp_prefix."ability.ability_min_rank >= '$curusrslrprnk[slurp_rank_id]' AND ".$slrp_prefix."ability.ability_status_id = '$filter_by_status' ".$sort_string." ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting filter by ab set 1.");
	}
}

if($filter_query_add_count <= 2)
{
	if($filter_by_sp_eff_id == 1)
	{
		// echo"going2<br>";
		$abnames = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_id > '1' AND ".$slrp_prefix."ability.ability_status_id = '$filter_by_status' ".$sort_string." AND ".$slrp_prefix."ability.ability_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting default ab list.");
	}
	if($filter_by_sp_eff_id >= 2)
	{
		// echo"going3<br>";
		$abnames = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."ability_effect_special ON ".$slrp_prefix."ability_effect_special.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."ability.ability_id > '1' ".$sort_string." AND ".$slrp_prefix."ability.ability_status_id = '$filter_by_status' AND ".$slrp_prefix."ability.ability_min_rank >= '$curusrslrprnk[slurp_rank_id]' AND ".$slrp_prefix."ability_effect_special.effect_special_id = '$filter_by_sp_eff_id' ORDER BY ".$slrp_prefix."ability_effect_special.effect_special_id, ".$slrp_prefix."ability.ability") or die ("failed getting filter by effect special.");
	}
}

$abnamescnt = mysql_num_rows($abnames);

$current_filter_ability_set = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id = '$filter_by_ab_set_id'") or die ("failed getting current ability_sets filter.");
$crrfltrabset = mysql_fetch_assoc($current_filter_ability_set);
$crrfltrabsetcnt = mysql_num_rows($current_filter_ability_set);

echo"
		</td>
		</form>
		<td width='2%'>
			&nbsp;
		</td>
		<form name = 'sp_eff_filter' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
		<td align = 'center' valign = 'top' width='32%'>
			<font color = '#7fffd4'>
			<font class='heading1'>Filter by Pool/Domain/Etc.</font>
			<br>
			<select class='engine' name = 'filter_by_ab_set_id'>
";

if($crrfltrabsetcnt >= 1)
{
	echo"
			<option value = '$crrfltrabset[ability_set_id]'>$crrfltrabset[ability_set]</option>
	";
}
			

echo"
			<option value = '1'>No Filter</option>
";

$get_all_ability_racial_set = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id = '12'") or die ("failed getting race ability set.");
while($gtallabrclst = mysql_fetch_assoc($get_all_ability_racial_set))
{	
	echo"
			<option value = '$gtallabrclst[ability_set_id]'>$gtallabrclst[ability_set]</option>
	";
}


$get_all_ability_constant_set = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id = '2' OR ability_set_id = '11'") or die ("failed getting constant ability set.");
while($gtallabconstlst = mysql_fetch_assoc($get_all_ability_constant_set))
{	
	echo"
			<option value = '$gtallabconstlst[ability_set_id]'>$gtallabconstlst[ability_set]</option>
	";
}

$get_all_ability_pools_list = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id > '1' AND ability_set LIKE '%Pool%' ORDER BY ability_set") or die ("failed getting all ability Pools.");
while($gtallabpoollst = mysql_fetch_assoc($get_all_ability_pools_list))
{	
	echo"
			<option value = '$gtallabpoollst[ability_set_id]'>$gtallabpoollst[ability_set]</option>
	";
}

$get_all_ability_domains_list = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id > '1' AND ability_set LIKE '%Domain%' ORDER BY ability_set") or die ("failed getting all ability Domains.");
while($gtallabdmnslst = mysql_fetch_assoc($get_all_ability_domains_list))
{	
	echo"
			<option value = '$gtallabdmnslst[ability_set_id]'>$gtallabdmnslst[ability_set]</option>
	";
}

$get_all_ability_advanced_set = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id = '14'") or die ("failed getting adv ability set.");
while($gtallabadvancedlst = mysql_fetch_assoc($get_all_ability_advanced_set))
{	
	echo"
			<option value = '$gtallabadvancedlst[ability_set_id]'>$gtallabadvancedlst[ability_set]</option>
	";
}

$get_all_ability_advantages_set = mysql_query("SELECT * FROM ".$slrp_prefix."ability_set WHERE ability_set_id = '15'") or die ("failed getting creation advantages ability set.");
while($gtallabadvantagelst = mysql_fetch_assoc($get_all_ability_advantages_set))
{	
	echo"
			<option value = '$gtallabadvantagelst[ability_set_id]'>$gtallabadvantagelst[ability_set]</option>
	";
}


echo"
			</select>
			</font>
			<br>
			<font class='heading1'>Filter by Special Effect</font>
			<br>
			<select class='engine' name = 'filter_by_sp_eff_id'>
			<option value = '$crrfltreffspc[effect_special_id]'>$crrfltreffspc[effect_special]</option>
			<option value = '1'>No Filter</option>
";

$get_all_effect_specials_list = mysql_query("SELECT * FROM ".$slrp_prefix."effect_special WHERE effect_special_id > '1' AND effect_special_id != '$crrfltreffspc[effect_special_id]' ORDER BY effect_special") or die ("failed getting all effect_specials.");
while($gtalleffspclst = mysql_fetch_assoc($get_all_effect_specials_list))
{	
	echo"<option value = '$gtalleffspclst[effect_special_id]'>$gtalleffspclst[effect_special]</option>";
}

echo"</select>";
echo"
		</td>
		<td width='2%'>
			&nbsp;
		</td>
		<td align = 'left' valign='top' width='32%'>
";

if($curusrslrprnk[slurp_rank_id] <= 5)
{
	$filter_by_status_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_status_id = '$filter_by_status'") or die ("Failed getting current status filter.");
	$fltbysttsstts = mysql_fetch_assoc($filter_by_status_status);
	
	$get_status_options = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_status_id > '1' AND slurp_status_id < '8'") or die ("failed getting status list options.");
	// $gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);
	
	echo"
				<font class='heading1'>Filter by Status</font>
				<br>
				<select class='engine' name = 'filter_by_status'>
				<option value = '$fltbysttsstts[slurp_status_id]'>$fltbysttsstts[slurp_status]/$fltbysttsstts[slurp_alt_status1]</option>
	";
	
	while($gtstatopts = mysql_fetch_assoc($get_status_options))
	{
		echo"<option value = '$gtstatopts[slurp_status_id]'>$gtstatopts[slurp_status]/$gtstatopts[slurp_alt_status1]</option>";
	}
	
	echo"	</select>";
}

if($abnamescnt >= 2)
{
	$ability_tally ="$abnamescnt Abilities here.";
}
if($abnamescnt == 1)
{
	$ability_tally ="$abnamescnt Ability here.";
}


echo"
			<br>
			<br>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$component_expander' name = 'component_expander'>
			<input class='submit3' type='submit' value='Filter' name = 'sp_eff_filter'>
			</font>
		</td>
		</form>
	</tr>
	<tr>
		<td align = 'right' valign = 'bottom' width='32%'>
		<font class = 'heading1'>$ability_tally
		</td>
		<td width = '2%'>
			&nbsp;
		</td>
		<td align = 'middle' valign = 'bottom' width='32%'>";
		
$filter_header = "<font class = 'heading1'>Pool/Domain:";
if($filter_by_ab_set_id >= 1)
{
	if($filter_by_ab_set_id == 1)
	{
		echo $filter_header." None<br>Special Effect: $crrfltreffspc[effect_special]";
	}
	
	if($filter_by_ab_set_id >= 2)
	{
		echo $filter_header." $crrfltrabset[ability_set]<br>Special Effect: $crrfltreffspc[effect_special]";
	}
}

if($filter_by_ab_set_id <= 0)
{
	echo $filter_header." None<br>Special Effect: $crrfltreffspc[effect_special]";
}

echo"
			</font>
		</td>
		<td width = '2%'>
			&nbsp;
		</td>
		<td align = 'left' valign = 'bottom' width='32%'>
";
// $sp_eff_filter_header = "<font color = 'yellow' size = '1'>Currently filtering on";

if($curusrslrprnk[slurp_rank_id] <= 5)
{
	// echo $sp_eff_filter_header." $fltbysttsstts[slurp_status] abilities.";
	echo"<font color = 'yellow' size = '1'>Status: $fltbysttsstts[slurp_status]<br>Racials & Advantages are Restricted.</font>";
}

echo"
		</td>
	</tr>
</table>
";

CloseTable3();

echo"
<table cellpadding='0' cellspacing='0' border='0'>
	<tr>
		<td colspan = '9'>
			<table cellpadding='0' cellspacing='0' border='0'>
";

// create list (temp table) of all items,

$abcnt = 0;
while($shoplist = mysql_fetch_assoc($abnames))
{
	// echo"$shoplist[ability]<br>";
	$ab_nfo_id = $shoplist[ability_id];
	$dressed = 1;
	$ab_shop = 0;
	include("modules/$module_name/includes/fn_ab_nfo.php");
}

echo"
			</table>
		</td>
	</tr>
	<tr background='themes/Vanguard/images/row2.gif' height='9'>
		<td colspan='9'>
		</td>
	</tr> 
	<tr background='themes/Vanguard/images/base1.gif' height='24'>
		<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
		<td colspan = '3' valign = 'middle'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='1' name='ab_expander'>
			<input class='submit3' type='submit' value='Back to Main' name='go_home'>
		</td>
		</form>
";

if($curpcnfo[creature_id] >= 2)
{
	echo"
		<td width = '2%' align = 'left' valign = 'top'>
		 &nbsp;
		</td>
		<form name = 'pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
		<td align = 'left' valign = 'middle'>
			<input type='hidden' value='char' name='current_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input class='submit3' type='submit' value='Go to $curpcnfo[creature]' name='go_to_edit'>
		</td>
		</form>
		<td width = '2%' align = 'left' valign = 'middle'>
			&nbsp;
		</td>
		<form name = 'pc_eff_typ' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
		<td align = 'left' valign = 'middle'>
	";
	
	if($curpcnfo[creature_status_id] == 2)
	{
		echo"
			<input type='hidden' value='ab' name='current_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input class='submit3' type='submit' value='Go to Abilities for $curpcnfo[creature]' name='go_to_eff_typ'>
		";
	}
	
	if($curpcnfo[creature_status_id] == 4)
	{
		echo"
			<input type='hidden' value='ab' name='current_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input class='submit3' type='submit' value='Go to Effects for $curpcnfo[creature]' name='go_to_eff_typ'>
		";
	}
}

echo"
		</td>
		</form>
	</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>