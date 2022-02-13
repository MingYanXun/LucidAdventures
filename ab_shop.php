<?php
if (!eregi("modules.php", $PHP_SELF))
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

// library identifier
if(isset($_POST['library_id']))
{
	$library_id = $_POST['library_id'];
}
if(isset($_POST['ins_upd']))
{
	$ins_upd = $_POST['ins_upd'];
	if($ins_upd == 0)
	{
		$new_library_name = $_POST['new_library_name'];
		$new_library_rank = $_POST['new_library_rank'];
		$new_library_status = $_POST['new_library_status'];
		// echo"$new_library_name, $new_library_rank, $new_library_status<br>";
		$make_new_library = mysql_query("INSERT INTO dom_library (library,library_minimum_rank,library_status_id) VALUES ('$new_library_name','$new_library_rank','$new_library_status')") or die ("failed inserting new library.");
		
		$check_new_library = mysql_query("SELECT * FROM dom_library WHERE library = '$new_library_name' AND library_minimum_rank = '$new_library_rank' AND library_status_id = '$new_library_status'") or die ("Failed checking new library add.");
		$chknewlib = mysql_fetch_assoc($check_new_library);
		$library_id = $chknewlib[library_id];
	}
	if($ins_upd == 1)
	{
		$new_library_rank = $_POST['new_library_rank'];
		$new_library_status = $_POST['new_library_status'];
		// echo"$new_library_name, $new_library_rank, $new_library_status<br>";
		$update_library = mysql_query("UPDATE dom_library SET library_minimum_rank = '$new_library_rank', library_status_id = '$new_library_status' WHERE library_id = '$library_id'") or die ("failed updating library status, rank.");
	}
}

if(isset($_POST['del_library']))
{
	$del_library_id = $_POST['del_library'];
	$del_library_stuff = mysql_query("DELETE FROM dom_library_book WHERE library_id = '$library_id'") or die ("failed deleting library books.");
	$del_library_creature = mysql_query("DELETE FROM dom_library_creature WHERE library_id = '$library_id'") or die ("failed deleting library creatures.");
	$del_library_culture = mysql_query("DELETE FROM dom_library_culture WHERE library_id = '$library_id'") or die ("failed deleting library cultures.");
	$del_library_geography = mysql_query("DELETE FROM dom_library_geography WHERE library_id = '$library_id'") or die ("failed deleting library geography.");
	$del_library_main = mysql_query("DELETE FROM dom_library WHERE library_id = '$library_id'") or die ("failed deleting library.");
	$library_id = 1;	
}

if($library_id == 1)
{
	$library_name = "";
}

if($library_id > 1)
{
	// echo"$library_id<br>";
	$library_title = mysql_query("SELECT * FROM dom_library WHERE library_id = '$library_id'") or die ("failed getting library title.");
	$libttl = mysql_fetch_assoc($library_title);
	$library_name = "$libttl[library]";
}


require("header.php");
$nav_title = "View Library<br>$library_name";
include("modules/$module_name/includes/slurp_header.php");


if(isset($_POST['library_book_id']))
{
	$library_book_id = $_POST['library_book_id'];
	$new_library_book = mysql_query("INSERT INTO ".$slrp_prefix."library_book (library_id, item_book_id) VALUES ('$library_id','$library_book_id')") or die ("failed inserting library book.");
}

if(isset($_POST['del_from_library']))
{
	$del_library_book_id = $_POST['del_library_book_id'];
	// echo"$del_library_book_id<br>";
	$del_library_book = mysql_query("DELETE FROM ".$slrp_prefix."library_book WHERE library_id = '$library_id' AND item_book_id = '$del_library_book_id'") or die ("failed deleting library book.");
}

if(isset($_POST['del_culture_id']))
{
	$del_culture_id = $_POST['del_culture_id'];
	// echo"$del_culture_id<br>";
	$del_library_cultures = mysql_query("DELETE FROM ".$slrp_prefix."library_culture WHERE library_id = '$library_id' AND culture_id = '$del_culture_id'") or die ("failed deleting library culture.");
}

if(isset($_POST['del_creature_id']))
{
	$del_creature_id = $_POST['del_creature_id'];
	// echo"del pc: $del_creature_id<br>";
	$del_library_creatures = mysql_query("DELETE FROM ".$slrp_prefix."library_creature WHERE library_id = '$library_id' AND creature_id = '$del_creature_id'") or die ("failed deleting library creature.");
}

if(isset($_POST['del_geography_id']))
{
	$del_geography_id = $_POST['del_geography_id'];
	// echo"$del_geography_id<br>";
	$del_library_geographies = mysql_query("DELETE FROM ".$slrp_prefix."library_geography WHERE library_id = '$library_id' AND geography_id = '$del_geography_id'") or die ("failed deleting library geography.");
}

if(isset($_POST['del_race_id']))
{
	$del_race_id = $_POST['del_race_id'];
	// echo"del pc: $del_race_id<br>";
	$del_library_races = mysql_query("DELETE FROM ".$slrp_prefix."library_creature_subtype WHERE library_id = '$library_id' AND creature_subtype_id = '$del_race_id'") or die ("failed deleting library race.");
}

if($ntro_expander == 1)
{
	echo"
<tr height ='9'>
	<td colspan = '9' width = '100%'>
	</td>
</tr>

<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan = '9' width = '100%'>
		<hr width = '100%'>
	</td>
</tr>
	";
}

echo"

<tr background='themes/RedShores/images/base1.gif' height='24'>
	<form name = 'pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
	<td valign = 'middle' align = 'left' width = '18%'>
";

// echo"<form name = 'home' method='post' action = 'modules.php?name=$module_name'><input type='hidden' value='1' name='$expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='submit' value='Back to Main' name='go_home'></form>" ;

if($curpcnfo[creature_id] >= 2)
{
	echo"
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='char' name='current_expander'>
		<input type='submit' value='Go to View $current_character_information' name='go_to_edit'>
	";
}
echo"
	</td>
	</form>
	<td width='2%'>
	</td>
	<form name = 'pc_eff_typ' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
	<td valign = 'middle' align = 'left' width = '18%'>
";

if($curpcnfo[creature_id] >= 2)
{
	echo"
		<input type='hidden' value='ab' name='current_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='submit' value='Go to Effects for $curpcnfo[creature]' name='go_to_eff_typ'>
	";
}

echo"
	</td>
	</form>
	<td width='2%'>
	</td>
	<form name = 'show_adm' method='post' action = 'modules.php?name=$module_name&file=ab_shop'>
	<td valign = 'middle' align = 'left' width = '18%'>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	if($admin_expander == 0)
	{
		echo"
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$library_id' name = 'library_id'>
		<input type='hidden' value='1' name = 'admin_expander'>
		<input type='submit' value='Show Admin Panels' name = 'show_adm'>
		";
	}
	
	if($admin_expander == 1)
	{
		echo"
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$library_id' name = 'library_id'>
		<input type='hidden' value='0' name = 'admin_expander'>
		<input type='submit' value='Hide Admin Panels' name = 'hide_adm'>
		";
	}
}
	
echo"
	</td>
	</form>
	<td width='2%'>
	</td>
	<form name = 'show_hide_instructions' method='post' action = 'modules.php?name=$module_name&file=ab_shop'>
		<input type='hidden' value='$libttl[library_id]' name='library_id'>
	<td valign = 'middle' align = 'left' width = '18%'>
";

if($ntro_expander == 1)
{
	echo"
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='0' name = 'ntro_expander'>
		<input type='submit' value='Hide Instructions'>
	";
}

if($ntro_expander == 0)
{
	echo"
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='1' name = 'ntro_expander'>
		<input type='submit' value='Show Instructions'>
	";
}

echo"
	</td>
	</form>
</tr>
<tr height='9'>
	<td colspan = '9' align = 'left' valign = 'middle' width = '18%'>
	</td>
</tr>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	if($curpcnfo[creature_id] <= 1)
	{
		echo"
<tr>
	<form name = 'view_library' method='post' action = 'modules.php?name=$module_name&file=ab_shop'>
	<td>
		<font classs='heading2'>
		<b>Available Libraries</b>
		</font>
		<br> 
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<select class='engine' name = 'library_id'>
		";
		
		if($library_id == 1)
		{
			echo"<option value = '1'>Choose a Library</option>";
		}
		
		if($library_id > 1)
		{
			echo"<option value = '$libttl[library_id]'>$libttl[library]</option>";
			echo"<option value = '1'>Reset Form</option>";
		}
		
		$library_list = mysql_query("SELECT * FROM ".$slrp_prefix."library WHERE library_minimum_rank >= '$curusrslrprnk[slurp_rank_id]' AND library_status_id >= '4' ORDER BY library") or die ("failed getting library list.");
		while($liblst = mysql_fetch_assoc($library_list))
		{
			// echo"$liblst[library_creature_id], $liblst[library_geography_id]<br>";
			echo"<option value = '$liblst[library_id]'>$liblst[library]</option>";
		}
		
		echo"
		</select><br><input type='submit' value='View Library' name='view_library'>
	</td>
	</form>
		";
		
		if($admin_expander == 1)
		{
			if($library_id > 1)
			{
				echo"
	<td width = '2%'>
	</td>
	<form name = 'add_to_library' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td colspan = '3'>
		<font classs='heading2'>
		<b>Add Book to Library</b>
		</font>
		<br>
		<input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<select class='engine' name = 'library_book_id'>
				";
				
				$library_abs = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ".$slrp_prefix."ability.ability_id > '1' ORDER BY ".$slrp_prefix."ability.ability") or die ("failed getting library now.");
				while($libabs = mysql_fetch_assoc($library_abs))
				{
					$library_book_item = mysql_query("SELECT * FROM ".$slrp_prefix."item INNER JOIN ".$slrp_prefix."item_book ON ".$slrp_prefix."item_book.item_id = ".$slrp_prefix."item.item_id WHERE ".$slrp_prefix."item_book.ability_id = '$libabs[ability_id]' ORDER BY ".$slrp_prefix."item.item") or die ("failed getting library item book now.");
					while($libbkitm = mysql_fetch_assoc($library_book_item))
					{
						echo"<option value = '$libbkitm[item_book_id]'>$libabs[ability] [$libbkitm[item]]</option>";
					}
				}
				
				echo"
		</select><br><input type='submit' value='Add to $libttl[library] Library' name='add_to_library'>
	</td>
	</form>
		";
			}
			
		echo"
	<td width = '2%'>
	</td>	
	<td align = 'center' valign ='top'>
		";
				
		if($library_id == 1)
		{
			echo"
		<font classs='heading2'>
		<b>New Library</b>
		</font>
		<br>
		<font size = '1'>
		<form name = 'new_library' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
		<input type='text' class='textbox3' cols='20' name='new_library_name'></input>
		<br>
			";
		}
		
		if($library_id > 1)
		{
			echo"
			<font classs='heading2'>
			<b>Rank and Status</b>
			</font>
			<br>
			<font size = '1'>
			<form name = 'update_library' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
			";
		}
		
		echo"
			Status: <select class='engine' name = 'new_library_status'>
		";
	}
	
	$get_current_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status INNER JOIN ".$slrp_prefix."library ON ".$slrp_prefix."library.library_status_id = ".$slrp_prefix."slurp_status.slurp_status_id WHERE ".$slrp_prefix."library.library_id = '$libttl[library_id]'") or die ("failed getting current status.");
	$gtcurrstts = mysql_fetch_assoc($get_current_status);
	$gtcurrsttscnt = mysql_num_rows($get_current_status);
	
	if($admin_expander == 1)
	{
		if($gtcurrsttscnt >= 1)
		{
			echo"<option value = '$gtcurrstts[slurp_status_id]'>$gtcurrstts[slurp_alt_status1]</option>";
		}
		
		$get_slurp_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_status_id > '1' AND slurp_status_id <= '8' ORDER BY slurp_status_id DESC") or die("failed to get status list.");
		while($gtslrpstts = mysql_fetch_assoc($get_slurp_status))
		{
			echo"<option value = '$gtslrpstts[slurp_status_id]'>$gtslrpstts[slurp_alt_status1]</option>";
		}
		
		echo"
		</select>
		<br>
		Rank: <select class='engine' name = 'new_library_rank'>
		";
	}
	
	$get_current_minimum_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix."library ON ".$slrp_prefix."library.library_minimum_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix."library.library_id = '$libttl[library_id]'") or die ("failed getting current min rank to view.");
	$gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);
	$gtcurrmnrnkcnt = mysql_num_rows($get_current_minimum_rank);
	
	if($admin_expander == 1)
	{
		if($gtcurrmnrnkcnt >= 1)
		{
			echo"<option value = '$gtcurrmnrnk[slurp_rank_id]'>$gtcurrmnrnk[slurp_rank]</option>";
		}
		
		$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id > '".$curusrslrprnk[slurp_rank_id]."' ORDER BY slurp_rank_id DESC") or die("failed to get rank list.");
		while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
		{
			echo"<option value = '$gtslrprnk[slurp_rank_id]'>$gtslrprnk[slurp_rank]</option>";
		}
		
		echo"
		</select>
		<input type='hidden' value='$library_id' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='1' name = 'admin_expander'>
		";
		
		if($library_id == 1)
		{
			echo"
		<br>
		<input type='hidden' value='0' name='ins_upd'>
		<input type='submit' value='New Library' name='new_library'>
			";
		}
		if($library_id > 1)
		{
			echo"
		<br>
		<input type='hidden' value='1' name='ins_upd'>
		<input type='submit' value='Update' name='update_library'>
			";
		}
		
		echo"
	</td>
	</form>
			";
		}
	}
	
	echo"
</tr>
	";
}
if($curusrslrprnk[slurp_rank_id] >= 5)
{
	$get_current_status = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status INNER JOIN ".$slrp_prefix."library ON ".$slrp_prefix."library.library_status_id = ".$slrp_prefix."slurp_status.slurp_status_id WHERE ".$slrp_prefix."library.library_id = '$libttl[library_id]'") or die ("failed getting current status.");
	$gtcurrstts = mysql_fetch_assoc($get_current_status);
	$gtcurrsttscnt = mysql_num_rows($get_current_status);
	
	$get_current_minimum_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix."library ON ".$slrp_prefix."library.library_minimum_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix."library.library_id = '$libttl[library_id]'") or die ("failed getting current min rank to view.");
	$gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);
	$gtcurrmnrnkcnt = mysql_num_rows($get_current_minimum_rank);
}

if($admin_expander == 1)
{
	if($library_id > 1)
	{
		$library_cultures = mysql_query("SELECT * FROM ".$slrp_prefix."library_culture WHERE library_id = '$library_id'") or die ("failed getting existing library cultures.");
		
		if(isset($_POST['new_library_culture_id']))
		{
			$new_library_culture_id = $_POST['new_library_culture_id'];
			
			// verify the culture is not already in that place
			$existing_library_culture = mysql_query("SELECT * FROM ".$slrp_prefix."library_culture WHERE library_id = '$library_id' AND culture_id = '$new_library_culture_id'") or die ("failed verifying existing race library.");
			$exlibcultcnt = mysql_num_rows($existing_library_culture);
			
			if($exlibcultcnt == 0)
			{
				if($new_library_culture_id >= 2)
				{
					$new_library_culture = mysql_query("INSERT INTO ".$slrp_prefix."library_culture (library_id,culture_id) VALUES ('$library_id','$new_library_culture_id')") or die ("failed inserting new library culture.");
				}
			}
		}
		
		// associate new cultures with libraries
		echo"
<tr>
	<form name = 'library_culture' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td valign = 'top' align = 'right'>
		";
		
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"
		<font color = 'white' size = '1'>
		Associate Library to Culture:
		<br>
		<select class='engine' name ='new_library_culture_id'>
		<option>Choose Culture</option>
			";
			
			$get_culture_list = mysql_query("SELECT * FROM ".$slrp_prefix."culture WHERE culture_id > '1' AND culture_id NOT IN (SELECT culture_id FROM ".$slrp_prefix."library_culture WHERE library_id = '$library_id') ORDER BY culture") or die ("failed getting avalilable cultures list.");
			while($getcultlst = mysql_fetch_assoc($get_culture_list))
			{
				$exist_cult_list = strip_tags(stripslashes($getcultlst[culture]));
				echo"<option value = '$getcultlst[culture_id]'>$exist_cult_list</option>";
			}	
			
			echo"
		</select>
		<br>
		</font>
			";
		
			echo"
	</td>
	<td width = '2%'>
	</td>
	<td align = 'left' valign = 'bottom'>
		<input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='New Culture' name='new_library_cult'>
	</td>
	</form>
</tr>
			";
		}
		
		// show the current races for the library, and offer a delete button for admins
		$get_current_culture = mysql_query("SELECT * FROM ".$slrp_prefix."culture INNER JOIN ".$slrp_prefix."library_culture ON ".$slrp_prefix."library_culture.culture_id = ".$slrp_prefix."culture.culture_id WHERE ".$slrp_prefix."culture.culture_id > '1' AND ".$slrp_prefix."culture.culture_min_rank >= '$curusrslrprnk[slurp_rank_id]' AND ".$slrp_prefix."library_culture.library_id = '$library_id' ORDER BY ".$slrp_prefix."culture.culture") or die ("failed getting current library culture(s).");
		$getcurrcultcnt = mysql_num_rows($get_current_culture);
		
		if($getcurrcultcnt >= 1)
		{
			echo"
<tr>
	<td align = 'right' valign = 'top'>
		<font color = 'white' size = '1'>
		<br>
		Cultures associated with $libttl[library]:
		<br>
	</td>
</tr>
			";
		}
		
		while($getcurrcult = mysql_fetch_assoc($get_current_culture))
		{
			$get_curr_cult = strip_tags(stripslashes($getcurrcult[culture]));
			if(isset($_POST['del_lib_cult_'.$group_name.$group_type.'_'.$getcurrcult[culture_id]]))
			{
				$delete_checked_library_culture = mysql_query("DELETE FROM ".$slrp_prefix."library_culture WHERE library_id = '$library_id' AND culture_id = '$getcurrcult[culture_id]'") or die ("failed deleting $get_curr_cult.");
				
				$verify_deleted_library_culture = mysql_query("SELECT * FROM ".$slrp_prefix."library_culture WHERE library_id = '$library_id' AND culture_id = '$getcurrcult[culture_id]'") or die ("failed verifying deleted $get_curr_cult.");
				$verdellibcultcnt = mysql_num_rows($verify_deleted_library_culture);
				
				if($verdellibcultcnt >= 1)
				{
					if($verbose == 1)
					{
						echo"
<tr>
	<td colspan = '5' align = 'left' valign = 'top'>
		<font color = 'red' size = '2'>
		<li> $libttl[library] is no longer available to members of $get_curr_cult.
		<br><br>
	</td>
</tr>
						";
					}
				}
			}
			
			else
			{
				echo"
<tr>
	<td align = 'right' valign = 'top'>
		<font color = 'orange' size = '2'>
				";
				
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{			
					echo"
		<form name = 'new_focus' method='post' action = 'modules.php?name=$module_name&file=obj_edit'>
		<input type='hidden' value='30' name='current_focus_id'>
		<input type='hidden' value='$getcurrcult[culture_id]' name='culture'>
		<input type='submit' value='";
				}
				
				echo"$get_curr_cult";
				
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"' name='new_focus'></form>";
				}
				
				echo"
		</font>
	</td>
				";
						
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"
	<td width = '2%'>
	</td>
	<form name = 'del_library_cult' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td align = 'left' valign = 'top'>
		<input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$getcurrcult[culture_id]' name='del_culture_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='Delete $get_curr_cult' name='del_library_cult'>
	</td>
	</form>
					";
				}
					
				echo"
					</tr>
				";
			}
		}
		
		echo"
<tr height='9' background='themes/RedShores/images/row2.gif'>
	<td colspan = '9' align = 'left' valign = 'middle'>
		
	</td>
</tr>
		";
		
		$library_races = mysql_query("SELECT * FROM ".$slrp_prefix."library_creature_subtype WHERE library_id = '$library_id'") or die ("failed getting existing library races.");
		
		if(isset($_POST['new_library_race_id']))
		{
			$new_library_race_id = $_POST['new_library_race_id'];
			
			// verify the race is not already in that place
			$existing_library_race = mysql_query("SELECT * FROM ".$slrp_prefix."library_creature_subtype WHERE library_id = '$library_id' AND creature_subtype_id = '$new_library_race_id'") or die ("failed verifying existing race library.");
			$exlibrccnt = mysql_num_rows($existing_library_race);
			
			if($exlibrccnt == 0)
			{
				if($new_library_race_id >= 2)
				{
					$new_library_race = mysql_query("INSERT INTO ".$slrp_prefix."library_creature_subtype (library_id,creature_subtype_id) VALUES ('$library_id','$new_library_race_id')") or die ("failed inserting new library race.");
				}
			}
		}
		
		// associate new creatures with libraries
		echo"
<tr>
	<form name = 'library_creature' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td valign = 'top' align = 'right'>
		";
		
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"
		<font color = 'white' size = '1'>
		<br>
		Associate Library to Race:
		<br>
		<select class='engine' name ='new_library_race_id'>
		<option>Choose Race</option>
			";
			
			$get_race_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype WHERE creature_subtype_id > '1' AND creature_subtype_id NOT IN (SELECT creature_subtype_id FROM ".$slrp_prefix."library_creature_subtype WHERE library_id = '$library_id') ORDER BY creature_subtype") or die ("failed getting avalilable race list.");
			while($getrclst = mysql_fetch_assoc($get_race_list))
			{
				$exist_rc_list = strip_tags(stripslashes($getrclst[creature_subtype]));
				echo"<option value = '$getrclst[creature_subtype_id]'>$exist_rc_list</option>";
			}	
			
			echo"
		</select>
		<br>
		</font>
			";
			
			echo"
	</td>
	<td width = '2%'>
	</td>
	<td align = 'left' valign = 'bottom'>
		<input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='New Race' name='new_library_race'>
	</td>
	</form>
</tr>
			
			";
		}
		
		// show the current races for the library, and offer a delete button for admins
		$get_current_race = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype INNER JOIN ".$slrp_prefix."library_creature_subtype ON ".$slrp_prefix."library_creature_subtype.creature_subtype_id = ".$slrp_prefix."creature_subtype.creature_subtype_id WHERE ".$slrp_prefix."creature_subtype.creature_subtype_id > '1' AND ".$slrp_prefix."creature_subtype.creature_subtype_min_rank >= '$curusrslrprnk[slurp_rank_id]' AND ".$slrp_prefix."library_creature_subtype.library_id = '$library_id' ORDER BY ".$slrp_prefix."creature_subtype.creature_subtype") or die ("failed getting current library race(s).");
		$getcurrrccnt = mysql_num_rows($get_current_race);
		
		if($getcurrrccnt >= 1)
		{
			echo"
<tr>
	<td align = 'right' valign = 'top'>
		<font color = 'white' size = '1'>
		Races associated with $libttl[library]:
		<br>
	</td>
</tr>
			";
		}
		
		while($getcurrrc = mysql_fetch_assoc($get_current_race))
		{
			$get_curr_rc = strip_tags(stripslashes($getcurrrc[creature_subtype]));
			if(isset($_POST['del_lib_race_'.$group_name.$group_type.'_'.$getcurrrc[creature_subtype_id]]))
			{
				$delete_checked_library_race = mysql_query("DELETE FROM ".$slrp_prefix."library_creature_subtype WHERE library_id = '$library_id' AND creature_subtype_id = '$getcurrrc[creature_subtype_id]'") or die ("failed deleting $get_curr_rc.");
				
				$verify_deleted_library_race = mysql_query("SELECT * FROM ".$slrp_prefix."library_creature_subtype WHERE library_id = '$library_id' AND creature_subtype_id = '$getcurrrc[creature_subtype_id]'") or die ("failed verifying deleted $get_curr_rc.");
				$verdellibrccnt = mysql_num_rows($verify_deleted_library_race);
				
				if($verdellibrccnt >= 1)
				{
					if($verbose == 1)
					{
						echo"
<tr>
	<td colspan = '5' align = 'left' valign = 'top'>
		<font color = 'red' size = '2'>
		<li> $libttl[library] is no longer available to $get_curr_rc(s).
		<br><br>
	</td>
</tr>
						";
					}
				}
			}
			
			else
			{
				echo"
<tr>
	<td align = 'right' valign = 'top'>
		<font color = 'orange' size = '2'>
				";
				
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{			
					echo"
		<form name = 'new_focus' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
		<input type='hidden' value='7' name='current_focus_id'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$getcurrrc[creature_subtype_id]' name='race'>
		<input type='submit' value='";
				}
				
				echo"$get_curr_rc";
				
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"' name='new_focus'></form>";
				}
				
				echo"
		</font>
	</td>
				";
						
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"
	<td width = '2%'>
	</td>
	<form name = 'del_library_race' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td align = 'left' valign = 'top'>
		<input type='hidden' value='$getcurrrc[creature_subtype_id]' name='del_race_id'><input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='Delete $get_curr_rc' name='del_library_race'>
	</td>
	</form>
					";
				}
				
				echo"
</tr>
				";
			}
		}
		
		echo"
<tr height='9' background='themes/RedShores/images/row2.gif'>
	<td colspan = '9' align = 'left' valign = 'middle'>
	
	</td>
</tr>
		";
		
		$library_creatures = mysql_query("SELECT * FROM ".$slrp_prefix."library_creature WHERE library_id = '$library_id'") or die ("failed getting existing library creatures.");
		
		if(isset($_POST['new_library_creature_id']))
		{
			$new_library_creature_id = $_POST['new_library_creature_id'];
			
			// verify the culture is not already in that place
			$existing_library_creature = mysql_query("SELECT * FROM ".$slrp_prefix."library_creature WHERE library_id = '$library_id' AND creature_id = '$new_library_creature_id'") or die ("failed verifying existing creature library.");
			$exlibcrcnt = mysql_num_rows($existing_library_creature);
			
			if($exlibcrcnt == 0)
			{
				if($new_library_creature_id >= 2)
				{
					$new_library_creature = mysql_query("INSERT INTO ".$slrp_prefix."library_creature (library_id,creature_id) VALUES ('$library_id','$new_library_creature_id')") or die ("failed inserting new library creature.");
				}
			}
		}
		
		// associate new creatures with libraries
		echo"
<tr>
	<form name = 'library_creature' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td valign = 'top' align = 'right'>
		";
		
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"
		<font color = 'white' size = '1'>
		<br>
		Associate Library to Creature:
		<br>
		<select class='engine' name ='new_library_creature_id'>
		<option>Choose Creature</option>
			";
			
			$get_creature_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id > '1' AND creature_id NOT IN (SELECT creature_id FROM ".$slrp_prefix."library_creature WHERE library_id = '$library_id') ORDER BY creature") or die ("failed getting avalilable creature list.");
			while($getcrlst = mysql_fetch_assoc($get_creature_list))
			{
				$exist_cr_list = strip_tags(stripslashes($getcrlst[creature]));
				echo"<option value = '$getcrlst[creature_id]'>$exist_cr_list</option>";
			}	
			
			echo"
		</select>
		<br>
		</font>
			";
			
			echo"
	</td>
	<td width = '2%'>
	</td>
	<td align = 'left' valign = 'bottom'>
		<input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='New Creature' name='new_library_creature'>
	</td>
	</form>
</tr>
			";
		}
		
		// show the current races for the culture, and offer a delete button for admins
		$get_current_creature = mysql_query("SELECT * FROM ".$slrp_prefix."creature INNER JOIN ".$slrp_prefix."library_creature ON ".$slrp_prefix."library_creature.creature_id = ".$slrp_prefix."creature.creature_id WHERE ".$slrp_prefix."creature.creature_id > '1' AND ".$slrp_prefix."creature.creature_min_rank >= '$curusrslrprnk[slurp_rank_id]' AND ".$slrp_prefix."library_creature.library_id = '$library_id' ORDER BY ".$slrp_prefix."creature.creature") or die ("failed getting current library creature(s).");
		$getcurrcrcnt = mysql_num_rows($get_current_creature);
		
		if($getcurrcrcnt >= 1)
		{
			echo"
<tr>
	<td align = 'right' valign = 'top'>
		<font color = 'white' size = '1'>
		Creatures associated with $libttl[library]:
		<br>
	</td>
</tr>
			";
		}
		
		while($getcurrcr = mysql_fetch_assoc($get_current_creature))
		{
			$get_curr_cr = strip_tags(stripslashes($getcurrcr[creature]));
			if(isset($_POST['del_lib_creature_'.$group_name.$group_type.'_'.$getcurrcr[creature_id]]))
			{
				$delete_checked_library_creature = mysql_query("DELETE FROM ".$slrp_prefix."library_creature WHERE library_id = '$library_id' AND creature_id = '$getcurrcr[creature_id]'") or die ("failed deleting $get_curr_cr.");
				
				$verify_deleted_library_creature = mysql_query("SELECT * FROM ".$slrp_prefix."library_creature WHERE library_id = '$library_id' AND creature_id = '$getcurrcr[creature_id]'") or die ("failed verifying deleted $get_curr_cr.");
				$verdellibcrcnt = mysql_num_rows($verify_deleted_library_creature);
				
				if($verdellibcrcnt >= 1)
				{
					if($verbose == 1)
					{
						echo"
<tr>
	<td colspan = '5' align = 'left' valign = 'top'>
		<font color = 'red' size = '2'>
		<li> $libttl[library] is no longer available to $get_curr_cr(s).
		<br><br>
	</td>
</tr>
						";
					}
				}
			}
			
			else
			{
				echo"
<tr>
	<td align = 'right' valign = 'top'>
		<font color = 'orange' size = '2'>
				";
				
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{			
					echo"
		<form name = 'new_focus' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
		<input type='hidden' value='7' name='current_focus_id'>
		<input type='hidden' value='$getcurrcr[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$getcurrcr[creature_id]' name='creature'>
		<input type='submit' value='";
				}
				
				echo"$get_curr_cr";
				
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"' name='new_focus'></form>";
				}
				
				echo"
		</font>
	</td>
				";
						
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"
	<td width = '2%'>
	</td>
	<form name = 'del_library_creature' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td align = 'left' valign = 'top'>
		<input type='hidden' value='$getcurrcr[creature_id]' name='del_creature_id'><input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='Delete $get_curr_cr' name='del_library_creature'>
	</td>
	</form>
					";
				}
				
				echo"
</tr>
				";
			}
		}
		
		echo"
<tr height='9' background='themes/RedShores/images/row2.gif'>
	<td colspan = '9' align = 'left' valign = 'middle'>
	
	</td>
</tr>
		";
		
		$library_geography = mysql_query("SELECT * FROM ".$slrp_prefix."library_geography WHERE library_id = '$library_id'") or die ("failed getting existing library geography.");
		
		if(isset($_POST['new_library_geography_id']))
		{
			$new_library_geography_id = $_POST['new_library_geography_id'];
			
			// verify the geography is not already in that place
			$existing_library_geography = mysql_query("SELECT * FROM ".$slrp_prefix."library_geography WHERE library_id = '$library_id' AND geography_id = '$new_library_geography_id'") or die ("failed verifying existing geography library.");
			$exlibgeocnt = mysql_num_rows($existing_library_geography);
			
			if($exlibgeocnt == 0)
			{
				if($new_library_geography_id >= 2)
				{
					$new_library_geography = mysql_query("INSERT INTO ".$slrp_prefix."library_geography (library_id,geography_id) VALUES ('$library_id','$new_library_geography_id')") or die ("failed inserting new library geography.");
				}
			}
		}
		
		// associate new creatures with libraries
		echo"
<tr>
	<form name = 'library_geography' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td valign = 'top' align = 'right'>
		";
		
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"
		<font color = 'white' size = '1'>
		<br>
		Associate Library to Geography:
		<br>
		<select class='engine' name ='new_library_geography_id'>
		<option>Choose Geography</option>
			";
			
			$get_geography_list = mysql_query("SELECT * FROM ".$slrp_prefix."geography WHERE geography_id > '1' AND geography_id NOT IN (SELECT geography_id FROM ".$slrp_prefix."library_geography WHERE library_id = '$library_id') ORDER BY geography") or die ("failed getting avalilable geography list.");
			while($getgeolst = mysql_fetch_assoc($get_geography_list))
			{
				$exist_geo_list = strip_tags(stripslashes($getgeolst[geography]));
				echo"<option value = '$getgeolst[geography_id]'>$exist_geo_list</option>";
			}	
			
			echo"
		</select>
		<br>
		</font>
			";
			
			echo"
	</td>
	<td width = '2%'>
	</td>
	<td align = 'left' valign = 'bottom'>
		<input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='New Geography' name='new_library_geography'>
	</td>
	</form>
</tr>
			";
		}
		
		// show the current geography for the library, and offer a delete button for admins
		$get_current_geography = mysql_query("SELECT * FROM ".$slrp_prefix."geography INNER JOIN ".$slrp_prefix."library_geography ON ".$slrp_prefix."library_geography.geography_id = ".$slrp_prefix."geography.geography_id WHERE ".$slrp_prefix."geography.geography_id > '1' AND ".$slrp_prefix."geography.geography_min_rank >= '$curusrslrprnk[slurp_rank_id]' AND ".$slrp_prefix."library_geography.library_id = '$library_id' ORDER BY ".$slrp_prefix."geography.geography") or die ("failed getting current library geography(s).");
		$getcurrgeocnt = mysql_num_rows($get_current_geography);
		
		if($getcurrgeocnt >= 1)
		{
			echo"
<tr>
	<td align = 'right' valign = 'top'>
		<font color = 'white' size = '1'>
		Locations associated with $libttl[library]:
		<br>
	</td>
</tr>
			";
		}
		
		while($getcurrgeo = mysql_fetch_assoc($get_current_geography))
		{
			$get_curr_geo = strip_tags(stripslashes($getcurrgeo[geography]));
			if(isset($_POST['del_lib_geography_'.$group_name.$group_type.'_'.$getcurrgeo[geography_id]]))
			{
				$delete_checked_library_geography = mysql_query("DELETE FROM ".$slrp_prefix."library_geography WHERE library_id = '$library_id' AND geography_id = '$getcurrgeo[geography_id]'") or die ("failed deleting $get_curr_geo.");
				
				$verify_deleted_library_geography = mysql_query("SELECT * FROM ".$slrp_prefix."library_geography WHERE library_id = '$library_id' AND geography_id = '$getcurrgeo[geography_id]'") or die ("failed verifying deleted $get_curr_geo.");
				$verdellibgeocnt = mysql_num_rows($verify_deleted_library_geography);
				
				if($verdellibgeocnt >= 1)
				{
					if($verbose == 1)
					{
						echo"
<tr>
	<td colspan = '5' align = 'left' valign = 'top'>
		<font color = 'red' size = '2'>
		<li> $libttl[library] is no longer available to denizens of $get_curr_geo(s).
		<br><br>
	</td>
</tr>
						";
					}
				}
			}
			
			else
			{
				echo"
<tr>
	<td align = 'right' valign = 'top'>
		<font color = 'orange' size = '2'>
				";
				
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{			
					echo"
		<form name = 'new_focus' method='post' action = 'modules.php?name=$module_name&file=obj_edit'>
		<input type='hidden' value='27' name='current_focus_id'>
		<input type='hidden' value='$getcurrgeo[geography_id]' name='geography'>
		<input type='submit' value='";
				}
				
				echo"$get_curr_geo";
				
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"' name='new_focus'></form>";
				}
				
				echo"
		</font>
	</td>
				";
						
				if($curusrslrprnk[slurp_rank_id] <= 4)
				{
					echo"
	<td width = '2%'>
	</td>
	<form name = 'del_library_geography' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td align = 'left' valign = 'top'>
		<input type='hidden' value='$getcurrgeo[geography_id]' name='del_geography_id'><input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='Delete $get_curr_geo' name='del_library_geography'>
	</td>
	</form>
					";
				}
				
				echo"
</tr>
				";
			}
		}
	}
}


echo"</form>";

// end top row and start of form

// start new row for content

if($library_id > 1)
{
	echo"
</tr>
<tr height='9' background='themes/RedShores/images/row2.gif'>
	<td colspan = '9' align = 'left' valign = 'middle'>
	
	</td>
</tr>
<tr>
	<td colspan = '9' align = 'left' valign = 'middle'>
		<font size = '2' color = 'red'>
		$libttl[library] is currently $gtcurrstts[slurp_alt_status1] and viewable by $gtcurrmnrnk[slurp_rank] rank or higher.
		</font>
	</td>
</tr>
	";
}

$library_ab_check = mysql_query("SELECT * FROM ".$slrp_prefix."ability WHERE ability_id > '1' ORDER BY ability");
while($libchk = mysql_fetch_assoc($library_ab_check))
{
	// echo"$libchk[ability]<br>";
	$get_library_book_info = mysql_query("SELECT * FROM ".$slrp_prefix."item_book INNER JOIN ".$slrp_prefix."library_book ON ".$slrp_prefix."item_book.item_book_id = ".$slrp_prefix."library_book.item_book_id WHERE ".$slrp_prefix."library_book.library_id = '$library_id' AND ".$slrp_prefix."item_book.ability_id = '$libchk[ability_id]'") or die ("failed to get library item info.");
	$gtlibbknfocnt = mysql_num_rows($get_library_book_info);
	if($gtlibbknfocnt >= 1)
	{
		while($gtlibbknfo = mysql_fetch_assoc($get_library_book_info))
		{
			$instance = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtlibbknfo[item_id]'") or die ("failed getting group instance short_desc.");
			$inst = mysql_fetch_assoc($instance);
			$instance_display_name = stripslashes($inst[item_short_name]);
			// echo"$inst[item]";
				
			echo"
<tr>
	<td align = 'left' valign = 'middle' colspan='3'>";
			
			if($curusrslrprnk[slurp_rank_id] <= 4)
			{
				if($admin_expander == 1)
				{
					$admin_display_name = stripslashes($inst[item]);
					echo"$admin_display_name";
				}
				if($admin_expander == 0)
				{
					echo"$instance_display_name";
				}
			}
			if($curusrslrprnk[slurp_rank_id] >= 4)
			{
				echo"$instance_display_name";
			}
			
			echo"<br>";
					
			$listed_item_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."item_subtype INNER JOIN ".$slrp_prefix."item_item_subtype ON ".$slrp_prefix."item_subtype.item_subtype_id = ".$slrp_prefix."item_item_subtype.item_subtype_id WHERE ".$slrp_prefix."item_item_subtype.item_id = '$inst[item_id]'") or die ("failed getting library item_subtype.");
			$listitmsub = mysql_fetch_assoc($listed_item_subtype);
			// echo"list item sub: $listitmsub[item_subtype_id], book? $gtlibbknfo[item_book_id]<br>";
			
			if($listitmsub[item_subtype_id] >= 89)
			{
				if($listitmsub[item_subtype_id] <= 93)
				{
					$book_ability_random = $gtlibbknfo[object_random_id];
					$check_ability_id = $libchk[ability_id];
					include("modules/$module_name/includes/fn_ab_pc_check.php");
				}
			}
			
			echo"
	</td>
		 	";
	
			// for staff only
			if($curusrslrprnk[slurp_rank_id] <= 4)
			{
				if($admin_expander == 1)
				{
					$get_library_book_del = mysql_query("SELECT * FROM ".$slrp_prefix."item_book INNER JOIN ".$slrp_prefix."library_book ON ".$slrp_prefix."item_book.item_book_id = ".$slrp_prefix."library_book.item_book_id WHERE ".$slrp_prefix."library_book.library_id = '$library_id' AND ".$slrp_prefix."item_book.ability_id = '$libchk[ability_id]'") or die ("failed to get pc item info.");
					$gtlibbkdl = mysql_fetch_assoc($get_library_book_del);
					{
						$delete_item_subtype = mysql_query("SELECT * FROM ".$slrp_prefix."item_subtype INNER JOIN ".$slrp_prefix."item_item_subtype ON ".$slrp_prefix."item_subtype.item_subtype_id = ".$slrp_prefix."item_item_subtype.item_subtype_id WHERE ".$slrp_prefix."item_item_subtype.item_id = '$gtlibbkdl[item_id]'") or die ("failed getting listed del item_subtype.");
						$delitmsub = mysql_fetch_assoc($delete_item_subtype);
						
						$get_lib_item_del_info = mysql_query("SELECT * FROM ".$slrp_prefix."item WHERE item_id = '$gtlibbkdl[item_id]'") or die ("failed to get del pc item info.");
						$gtlbitmdlnfo = mysql_fetch_assoc($get_lib_item_del_info);
						
						echo"
	<form name = 'del_from_library' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
	<td align = 'left'>
		<input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$gtlibbkdl[item_book_id]' name='del_library_book_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='Delete $libchk[ability]' name='del_from_library'>
	</td>
	</form>
						";
					}
				}
			}
			
			echo"
	<form name = 'view_lib_ab' method='post' action = 'modules.php?name=$module_name&file=ab_edit'> 
	<td align = 'left'>
		<input type='hidden' value='$libttl[library_id]' name='library_id'><input type='hidden' value='$libchk[ability_id]' name='current_ab_id'><input type='hidden' value='$gtlibbkdl[item_book_id]' name='del_library_book_id'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='submit' value='View $libchk[ability]' name='view_lib_ab'>
	</td>
	</form>
</tr>
<tr height='9' background='themes/RedShores/images/row2.gif'>
	<td colspan = '9' align = 'left' valign = 'middle'>
	
	</td>
</tr>
			";
		}
	}
}

echo"
<tr height='9'>
	<td colspan = '9' align = 'left' valign = 'middle'>
	
	</td>
</tr>
<tr height='9' background='themes/RedShores/images/row2.gif'>
	<td colspan = '9' align = 'left' valign = 'middle'>
	
	</td>
</tr>

<tr background='themes/RedShores/images/base1.gif' height='24'>
	<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
	<td>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='1' name='ab_expander'>
		<input type='submit' value='Back to Main' name='go_home'>
	</td>
	</form>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	echo"
	<td width = '2%' align = 'left' valign = 'middle'>
	</td>
	<form name = 'del_library' method='post' action = 'modules.php?name=$module_name&file=ab_shop'>
	<td align = 'left' valign = 'middle'>
	";
	
	if($library_id >= 2)
	{
		echo"
		<input type='hidden' value='ab' name='current_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$library_id' name='library_id'>
		<input type='submit' value='Delete $libttl[library]' name='del_library'>
		";
	}
	
	echo"
	</td>
	</form>
	";
}

echo"
	<td width = '2%' align = 'left' valign = 'middle'>
	</td>
	<form name = 'pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
	<td align = 'left' valign = 'middle'>
";

if($curpcnfo[creature_id] >= 2)
{
	echo"
		<input type='hidden' value='char' name='current_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='submit' value='Go to View/Edit' name='go_to_edit'>
	";
}

echo"
	</td>
	</form>
	<td width = '2%' align = 'left' valign = 'top'>
	</td>
	<form name = 'pc_eff_typ' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
	<td align = 'left' valign = 'middle' colspan='3'>
";

if($curpcnfo[creature_id] >= 2)
{
	echo"
		<input type='hidden' value='ab' name='current_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='submit' value='Go to Effect Types' name='go_to_eff_typ'>
			";
}

echo"
	</td>
	</form>
</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>