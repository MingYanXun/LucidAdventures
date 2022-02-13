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
$nav_title = "View Character";
$nav_page = "pc_edit_new";
include("modules/$module_name/includes/slurp_header.php");
include("modules/$module_name/includes/fn_game_nfo.php");
include("modules/$module_name/includes/phpqrcode/qrlib.php");

// checkbox variables for the index
$expander_abbr = $_POST['current_expander'];
$expander = $expander_abbr."_expander";
// echo"exp: $expander_abbr, $expander<br>";
// echo"$curpcnfo[creature]<br>";
echo"
<tr background='themes/Vanguard/images/back2b.gif' height='24'>
	<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
	<td valign = 'middle' align = 'left' width='23%'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='hidden' value='1' name='char_expander'>
		<input class='submit3' type='submit' value='Back to Main' name='go_home'>
	</td>
	</form>
	<td valign = 'top' align = 'left' width = '2%'>
	</td>
	<form name = 'show_hide_ntro' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
	<td valign = 'middle' align = 'left' width='23%'>
		";
		
		if($ntro_expander == 0)
		{
			echo"
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='hidden' value='1' name = 'ntro_expander'>
		<input class='submit3' type='submit' value='Show Instructions'>
			";
		}
		if($ntro_expander == 1)
		{
			echo"
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='hidden' value='0' name = 'ntro_expander'>
		<input class='submit3' type='submit' value='Hide Instructions'>
			";
		}
		
		echo"
	</td>
	</form>
	<td valign = 'top' align = 'left' width = '2%'>
	</td>
	<td valign = 'middle' align = 'left' width='23%'>
		
	</td>
	<td valign = 'top' align = 'left' width = '2%'>
	</td>
	<td valign = 'middle' align = 'left' width='23%'>
		
	</td>	
</tr>
<tr height='9'>
	<td colspan='9'>
	</td>
</tr>
";


if($ntro_expander == 1)
{
	echo"
	<tr>
		<td colspan = '7' valign = 'top' align = 'left'>
	";
	
	Opentable3();
	
	echo"
	<font class='heading9'>This is the main page to manage <i>$current_character_information</i>. 
	<br>
	<br>
	~ ADMIN: From here, you can view out-of-play info about <i>$current_character_information</i>, like Build Points and how many resurrection stones <i>$current_character_information</i> has left.
	<br>
	~ ABILITIES: This shows you what Abilities <i>$current_character_information</i> knows.
	<br>
	~ BACKGROUND: The background information filled out for <i>$current_character_information</i> is available at the bottom.
	<p>Expand/Collapse any of these using the [Show/Hide...] buttons.
	</font>
	";
	
	if($admin_expander == 1)
	{
		echo"
		<hr class='pipes'>		
		<font class='heading1'><b>Admin panels</b> let you print <i>$current_character_information</i> and <i>$current_character_information</i>'s inventory and background. Status of <i>$current_character_information</i> (Unfinished, Pending, Active, Template, etc.) shows here. Build Points (BP) gain and expenditure and Downtime Notes (DT) submissions may be viewed by clicking the <input class='submit3' type='submit' value='Records for $current_character_information'> button in the top center of the expanded Admin section. <i>$current_character_information</i>'s resurrection stone count is also shown here.</font>
		";
	}
	
	if($ablist_expander == 1)
	{
		echo"
		<hr class='pipes'>
		<font class='heading1'>Each <b>Ability</b> name is a link that takes you to the Ability's description. Ability groups and Special Effect (Fire, Magic, Normal, etc.) are listed below the Ability name, followed by the cost. Components of the Ability are listed in the center, and the Verbal and the Description of the Ability are listed on the far right, along with any modifiers like 'Only usable by King Arthur' or 'Must know Apprentice Underwater Basketweaving.'</font>
		";
	
		//if($curpcnfo[creature_status_id] == 4)
		//{		
		//	echo"
		//		<p>
		//		<font class='heading1'>
		//		As you come to understand Abilities, you can collect their codes on your character. By successfully entering a code, your Character is learning an Ability--but you must have all the prerequisites to do so. Attempting to learn an Ability for which you do not qualify will fail.
		//		
		//	";
		//}
	}
	
	if($race_desc_expander == 1)
	{		
		echo"
		<hr class='pipes'>
		<font class='heading1'><i>$current_character_information</i>'s <b>Background</b> information shows in this panel; it is the same information you entered when creating <i>$current_character_information</i>--things like race, hometown, organizational ties, and so on. From time to time, Plot changes may occur to this information based on the story's progression. You will only be able to edit it while the character's status is Unfinished. </font>
		";
	}
	
	CloseTable3();
	
	echo"
		</td>
	</tr>
	";
}

if($curusrslrprnk[slurp_rank_id] <= 5)
{
	echo"
<tr background='themes/Vanguard/images/back2b.gif' height='24'>
	<td valign = 'middle' align = 'right' width='23%'>
		<font class='heading2'>ADMINISTRATION</font>
	</td>
	<td width='2%'>
	</td>
	<form name = 'show_adm' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
	";
	
	if($admin_expander == 0)
	{
		echo"
	<td valign = 'middle' align = 'left' width='23%'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$production_expander' name = 'production_expander'>
		<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
		<input type='hidden' value='1' name = 'admin_expander'>
		<input class='submit3' type='submit' value='Show Admin' name = 'show_adm'>
	</td>
	</form>
	<td width='2%'>
	</td>
	<td valign = 'middle' align = 'left'>
		<font class='heading1'><font color = '#33F406'>$build_left left</font> of <font color = '#00B2EE'>$curpcnfo[creature_xp_earned]</font> XP earned: <b><font color = '#00B2EE' title='Character Level = [(Total Build - 25)/10]'>Lvl $character_level</font><b></font>
	</td>
	<td width = '2%'>
	</td>
	";
		
	// if still unsubmitted
	if($curpcnfo[creature_status_id] == 2)
	{
		// make sure the starting character minumum is met
		if(($build_left) == 0)
		{
			// echo" (LEFT: $build_left)";
			echo"<form name = 'pc_submit' method='post' action = 'modules.php?name=$module_name&file=pc_edit_form'>";
		}
	}
	if($curpcnfo[creature_status_id] == 4)
	{
		echo"<form name = 'pc_submit' method='post' action = 'modules.php?name=$module_name&file=pc_history'>";
	}
	
	echo"<td valign = 'middle' align = 'right' width='23%'>";
	
	if($curpcnfo[creature_status_id] == 4) 
	{
		echo"
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='$expander_abbr' name='current_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='hidden' value='$compab_expander' name = 'compab_expander'>
						<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$materials_expander' name = 'materials_expander'>
						<input type='hidden' value='$items_expander' name = 'items_expander'>
						<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
						<input type='hidden' value='$production_expander' name = 'production_expander'>
						<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
						<input type='hidden' value='$admin_expander' name = 'admin_expander'>
						<input class='submit3' type='submit' value='Records for $current_character_information' name='pc_submit'>
						</form>
		";
	}
	
	// if still unsubmitted
	if($curpcnfo[creature_status_id] == 2)
	{
		// make sure the starting character minumum is met
		if(($build_left) == 0)
		{
				// echo" (LEFT: $build_left)";
				echo"
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
						<input type='hidden' value='3' name='pc_status'>
						<input class='submit3' type='submit' value='Submit $current_character_information!' name='pc_submit'>
						</form>
			";	
		}
	}
	
	echo"
	</td>
</tr>
		";
	}
	
	if($admin_expander == 1)
	{
		echo"
	<td valign = 'middle' align = 'left' width ='23%'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$production_expander' name = 'production_expander'>
		<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
		<input type='hidden' value='0' name = 'admin_expander'>
		<input class='submit3' type='submit' value='Hide Admin' name = 'hide_adm'>
	</td>
	</form>
	<td width = '2%'>
	</td>
	<form name = 'pc_del' method='post' action = 'modules.php?name=$module_name&file=obj_edit_form'>
	<td valign = 'middle' align = 'right' width='23%'>
		<font color = 'red'>
		<b><big>[</big>
		<input type='hidden' value='$curpcnfo[creature_id]' name='delete_creature_id'>
		<input type='hidden' value='1' name='confirm_delete'>
		<input type='hidden' value='$nav_title' name='nav_title'>
		<input type='hidden' value='$nav_page' name='nav_page'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$production_expander' name = 'production_expander'>
		<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
		<input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input class='submit3' type='submit' value='Delete $current_character_information' name='pc_del'>
		<big>]</b></big>
	</td>
	</form>
	<td width = '2%'>
	</td>
	";
	
	// if still unsubmitted
	if($curpcnfo[creature_status_id] == 2)
	{
		// make sure the starting character minumum is met
		if(($build_left) == 0)
		{
			// echo" (LEFT: $build_left)";
			echo"<form name = 'pc_submit' method='post' action = 'modules.php?name=$module_name&file=pc_edit_form'>";
		}
	}
	if($curpcnfo[creature_status_id] == 4)
	{
		echo"<form name = 'pc_submit' method='post' action = 'modules.php?name=$module_name&file=pc_history'>";
	}
	
	echo"
	<td valign = 'middle' align = 'right' width='23%'>
	";
	
	if($curpcnfo[creature_status_id] == 4) 
	{
		echo"
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
						<input type='hidden' value='$expander_abbr' name='current_expander'>
						<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
						<input type='hidden' value='$compab_expander' name = 'compab_expander'>
						<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
						<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
						<input type='hidden' value='$materials_expander' name = 'materials_expander'>
						<input type='hidden' value='$items_expander' name = 'items_expander'>
						<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
						<input type='hidden' value='$production_expander' name = 'production_expander'>
						<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
						<input type='hidden' value='$admin_expander' name = 'admin_expander'>
						<input class='submit3' type='submit' value='Records for $current_character_information' name='pc_submit'>
						</form>
		";
	}
	
	// if still unsubmitted
	if($curpcnfo[creature_status_id] == 2)
	{
		// make sure the starting character minumum is met
		if(($build_left) == 0)
		{
				// echo" (LEFT: $build_left)";
				echo"
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
						<input type='hidden' value='3' name='pc_status'>
						<input class='submit3' type='submit' value='Submit $current_character_information!' name='pc_submit'>
						</form>
			";	
		}
	}
	
	echo"
	</td>
</tr>
		";
	}
}

if($curusrslrprnk[slurp_rank_id] >= 6)
{
	if($curpcnfo[creature_status_id] <= 4)
	{
		echo"
<tr background='themes/Vanguard/images/back2b.gif' height='24'>
	<td valign = 'middle' align = 'right' width='23%'>
		<font class='heading2'>ADMINISTRATION</font>
	</td>
	<td width='2%'>
	</td>
	<form name = 'show_adm' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
		";
		
		if($admin_expander == 0)
		{
			echo"
	<td valign = 'middle' align = 'left' width='23%'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$production_expander' name = 'production_expander'>
		<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
		<input type='hidden' value='1' name = 'admin_expander'>
		<input class='submit3' type='submit' value='Show Admin' name = 'show_adm'>
	</td>
	</form>
	<td width='2%'>
	</td>
	<td valign = 'middle' align = 'left'>
		<font class='heading1'><font color = '#33F406'>$build_left left</font> of <font color = '#00B2EE'>$curpcnfo[creature_xp_earned]</font> XP earned: <b><font color = '#00B2EE' title='Character Level = [(Total Build - 25)/10]'>Lvl $character_level</font><b></font>
	</td>
	<td width = '2%'>
	</td>
	<form name = 'pc_submit' method='post' action = 'modules.php?name=$module_name&file=pc_history'>
	<td valign = 'middle' align = 'right' width='23%'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$production_expander' name = 'production_expander'>
		<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
		<input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input class='submit3' type='submit' value='Records for $current_character_information' name='pc_submit'>
	</td>
	</form>
</tr>
			";
		}
		
		if($admin_expander == 1)
		{
			echo"
	<td valign = 'middle' align = 'left' width ='23%'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$production_expander' name = 'production_expander'>
		<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
		<input type='hidden' value='0' name = 'admin_expander'>
		<input class='submit3' type='submit' value='Hide Admin' name = 'hide_adm'>
	</td>
	</form>
	<td width = '2%'>
	</td>
	<form name = 'pc_del' method='post' action = 'modules.php?name=$module_name&file=obj_edit_form'>
	<td valign = 'middle' align = 'right' width='23%'>
		<font color = 'red'>
		<b><big>[</big>
		<input type='hidden' value='$curpcnfo[creature_id]' name='delete_creature_id'>
		<input type='hidden' value='1' name='confirm_delete'>
		<input type='hidden' value='$nav_title' name='nav_title'>
		<input type='hidden' value='$nav_page' name='nav_page'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$production_expander' name = 'production_expander'>
		<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
		<input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input class='submit3' type='submit' value='Delete $current_character_information' name='pc_del'>
		<big>]</b></big>
	</td>
	</form>
	<td width = '2%'>
	</td>
	<form name = 'pc_submit' method='post' action = 'modules.php?name=$module_name&file=pc_history'>
	<td valign = 'middle' align = 'right' width='23%'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$expander_abbr' name='current_expander'>
		<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
		<input type='hidden' value='$compab_expander' name = 'compab_expander'>
		<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='$materials_expander' name = 'materials_expander'>
		<input type='hidden' value='$items_expander' name = 'items_expander'>
		<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
		<input type='hidden' value='$production_expander' name = 'production_expander'>
		<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
		<input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input class='submit3' type='submit' value='Records for $current_character_information' name='pc_submit'>
	</td>
	</form>
</tr>
			";
		}
	}
}

echo"
<tr height='9'>
	<td colspan='9'>
	</td>
</tr>
";

if($race_desc_expander == 1)
{
	$get_pc_race = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype INNER JOIN ".$slrp_prefix."creature_creature_subtype ON ".$slrp_prefix."creature_subtype.creature_subtype_id = ".$slrp_prefix."creature_creature_subtype.creature_subtype_id WHERE ".$slrp_prefix."creature_creature_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting pc race.");
	$gtpcrc = mysql_fetch_assoc($get_pc_race);

	$get_creature_type = mysql_query("SELECT * FROM ".$slrp_prefix."creature_type INNER JOIN ".$slrp_prefix."creature_subtype_creature_type ON ".$slrp_prefix."creature_type.creature_type_id = ".$slrp_prefix."creature_subtype_creature_type.creature_type_id WHERE ".$slrp_prefix."creature_subtype_creature_type.creature_subtype_id = '$gtpcrc[creature_subtype_id]'") or die ("failed getting race.");
}


echo"
<tr>
<td colspan = '7' width = '100%'>
";

if($admin_expander == 1)
{
	OpenTable3();
	echo"
			<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
				<tr>
					<td valign = 'top' align = 'left' width = '23%'>
						<table width='100%' cellpadding='0' cellspacing='0' border='0'>
							<tr background='themes/Vanguard/images/back2b.gif' height='24'>
								<td valign = 'middle' align = 'center' width = '100%' colspan = '2'>
									<input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='$curpcnfo[creature_id]' name = 'current_pc_id'>
									<font class='heading2'>PRINT</font>
								</td>
							</tr>
							<tr>
								<form name = 'pc_print_page' method='post' action = 'modules.php?name=$module_name&file=pc_print_page'>";
								
							//	echo"
							//	<td valign = 'top' align = 'left' width = '50%'>
							//		<input type = 'hidden' name = 'pc_to_print' value = '$curpcnfo[creature_id]'>
							//	<input type='checkbox' value='1' name='verbose'> Long?";
							//	</td>
							//	";
								
								echo"
								<td valign = 'middle' align = 'center' width = '100%' colspan = '2'>
									<input type = 'hidden' name = 'verbose' value = '1'>
									<input type = 'hidden' name = 'pc_to_print' value = '$curpcnfo[creature_id]'>
									<input class='submit3' type='submit' value='Character' name='pc_print_page'>
								</td>
								</form>
							</tr>
							<tr height='9'>
								<td valign = 'middle' align = 'center' width = '100%' colspan='2'>
								</td>
							</tr>
	";
	
	if($curpcnfo[creature_status_id] <= 4)
	{
		echo"
							<tr>
								<form name = 'pc_print_rec' method='post' action = 'modules.php?name=$module_name&file=pc_print_rec'>
								<td valign = 'middle' align = 'center' width = '100%' colspan = '2'>
									<input type='hidden' value='$expander_abbr' name='current_expander'>
									<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
									<input type='hidden' value='$compab_expander' name = 'compab_expander'>
									<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
									<input type='hidden' value='$materials_expander' name = 'materials_expander'>
									<input type='hidden' value='$items_expander' name = 'items_expander'>
									<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
									<input type='hidden' value='$production_expander' name = 'production_expander'>
									<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
									<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
									<input type='hidden' value='$admin_expander' name = 'admin_expander'>
									<input type='hidden' value='$curpcnfo[creature_id]' name = 'pc_to_print'>
									<input class='submit3' type='submit' value='Recoveries' name='pc_print_rec'>
								</td>
								</form>
							</tr>
							<tr height='9'>
								<td valign = 'middle' align = 'left' width = '100%' colspan='2'>
								</td>
							</tr>
							<tr>
								<form name = 'pc_print_qrcodes' method='post' action = 'modules.php?name=$module_name&file=pc_print_qrcodes'>
								<td valign = 'middle' align = 'center' width = '100%' colspan = '2'>
									<input type='hidden' value='$expander_abbr' name='current_expander'>
									<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
									<input type='hidden' value='$compab_expander' name = 'compab_expander'>
									<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
									<input type='hidden' value='$materials_expander' name = 'materials_expander'>
									<input type='hidden' value='$items_expander' name = 'items_expander'>
									<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
									<input type='hidden' value='$production_expander' name = 'production_expander'>
									<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
									<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
									<input type='hidden' value='$admin_expander' name = 'admin_expander'>
									<input type='hidden' value='$curpcnfo[creature_id]' name = 'pc_to_print'>
									<input class='submit3' type='submit' value='QR Codes for Game' name='pc_print_qrcodes'>
								</td>
								</form>
							</tr>
							<tr height='9'>
								<td valign = 'middle' align = 'left' width = '100%' colspan='2'>
								</td>
							</tr>
		";
	}	
	
	if(isset($_POST['pc_residence']))
	{
		$pc_residence = $_POST['pc_residence'];
		$change_pc_residence = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_market = '$pc_residence' WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating pc market.");
	}
	//if(isset($_POST['ab_curve_reset']))
	//{
	//	$change_pc_learning_curve = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_ability_learning_curve = creature_ability_learning_curve-1 WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating pc learning curve.");
	//}
	
	include("modules/$module_name/includes/pcinfo.php");
	if($curpcnfo[creature_status_id] == 4)
	{
		echo"		
							<tr background='themes/Vanguard/images/back2b.gif' height='24'>
								<td valign = 'middle' align = 'center' width = '100%' colspan='2'>
									<font class='heading2'>RESIDENCE</font><br>
								</td>
							</tr>
							<tr>
								<td valign = 'top' align = 'center' width = '100%' colspan='2'>
		";
		
		if($curusrslrprnk[slurp_rank_id] <= 3)
		{
			echo"
									<form name = 'pc_residence_form' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
									<select class='engine' name = 'pc_residence'>
									<option value = '$gtpcmtk[geography_id]'>
			";
		}
		
		echo"					<font size = '2' color = 'red'><b>$gtpcmtk[geography]</b></font>";
		
		if($curusrslrprnk[slurp_rank_id] <= 3)
		{
			echo"				</option>";
		
			$get_market_locations = mysql_query("SELECT * FROM ".$slrp_prefix."geography WHERE geography_market = '1' ORDER BY geography") or die ("Failed getting market locations.");
			while($gtmktlocs = mysql_fetch_assoc($get_market_locations))
			{
				echo"			<option value = '$gtmktlocs[geography_id]'>$gtmktlocs[geography]</option>";
			}
			
			echo"
									</select>
								</td>
							</tr>
							<tr>
								<td valign = 'top' align = 'center' width = '100%' colspan='2'>
									<input type='hidden' value='$expander_abbr' name='current_expander'>
									<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
									<input type='hidden' value='$compab_expander' name = 'compab_expander'>
									<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
									<input type='hidden' value='$materials_expander' name = 'materials_expander'>
									<input type='hidden' value='$items_expander' name = 'items_expander'>
									<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
									<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
									<input type='hidden' value='$production_expander' name = 'production_expander'>
									<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
									<input type='hidden' value='$admin_expander' name = 'admin_expander'>
									<input type='hidden' value='$curpcnfo[creature_id]' name = 'current_pc_id'>
									<input class='submit3' type='submit' value='Change Location' name='pc_residence_form'>
								</td>
								</form>
							</tr>
							<tr height='5'>
								<td valign = 'middle' align = 'left' width = '100%' colspan='2'>
								</td>
							</tr>
							<tr>
								<td valign = 'top' align = 'center' width = '100%' colspan='2'>
			";
		
			if($curpcnfo[creature_status_id] <= 4)
			{
				echo"
									<form name = 'go_to_pc_market' method='post' action = 'modules.php?name=$module_name&file=pc_market'>
									
									<input type='hidden' value='$expander_abbr' name='current_expander'>
									<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
									<input type='hidden' value='$compab_expander' name = 'compab_expander'>
									<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
									<input type='hidden' value='$materials_expander' name = 'materials_expander'>
									<input type='hidden' value='$items_expander' name = 'items_expander'>
									<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
									<input type='hidden' value='$harvest_expander' name = 'harvest_expander'>
									<input type='hidden' value='$production_expander' name = 'production_expander'>
									<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
									<input type='hidden' value='$admin_expander' name = 'admin_expander'>
									<input type='hidden' value='$curpcnfo[creature_id]' name = 'current_pc_id'>
									<input class='submit3' type='submit' value='Go to Market' name='go_to_pc_market'>
									</form>
				";
			}
		}
		
		echo"
								</td>
							</tr>
							<tr height='9'>
								<td valign = 'middle' align = 'left' width = '100%' colspan='2'>
								</td>
							</tr>
		";
	}
	
	echo"		
						</table>
					</td>
					<td width = '2%' valign = 'top' align = 'center'>
						<table width = '100%' cellspacing='0' cellpadding='0' border='0'>
							<tr background='themes/Vanguard/images/back2b.gif' height='24'>
								<td valign = 'middle' width = '100%' align = 'center'>
								
								</td>
							</tr>
						</table>
					</td>
					<td valign = 'top' align = 'left' width = '23%'>
						<table width='100%' cellpadding='0' cellspacing='0' border='0'>
							<tr background='themes/Vanguard/images/back2b.gif' height='24'>
								<td valign = 'middle' align = 'center' width = '100%'>
									<font class = 'heading2'>STATUS</font>
								</td>
							</tr>
							<tr>
								<td valign = 'top' align = 'center' width = '100%'>
								<font class = 'heading1'>
	";
	
	if($curpcnfo[creature_status_id] >= 1) 
	{
		if($curpcnfo[creature_status_id] <= 3) 
		{
			// begin character status pane		
			if(($build_left+35) > $curpcnfo[creature_xp_earned])
			{
				echo"
						$current_character_information must buy Abilities.<br><font color='#CC00FF'><b>$build_left Build Points left of $curpcnfo[creature_xp_earned].</b></font>
				";
			}
		}
	}
		
	// if finished creating a pc, offer a button to submit it
	// if still unsubmitted
	if($curpcnfo[creature_status_id] == 2)
	{
		$curpcearned = $curpcnfo[creature_xp_earned]-35;
		// make sure the starting character minumum is met
		if($build_left <= $curpcearned)
		{
				// echo" (LEFT: $build_left)";
				echo"
						$current_character_information looks finished.
						<br>
						<font title = '25 Life Experience Build; + 10 to start at Level 1; and the $curpcearned that $curpcnfo[creature] has earned.'><font color='#CC00FF'><b>$build_left</b></font> Build Points left
						<br>
						of <font color='#CC00FF'>$curpcnfo[creature_xp_earned]</font></font>
						<br>
						Click here to submit $current_character_information.
						<br>
						<br>
						<form name = 'pc_submit' method='post' action = 'modules.php?name=$module_name&file=pc_edit_form'>
						<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
						<input type='hidden' value='3' name='pc_status'>
						<input class='submit3' type='submit' value='I like $current_character_information!' name='pc_submit'>
						</form>
			";	
		}
	}
	echo"</form>";
	// list current status
	echo"
						<br>Current status is <font color='orange'>$curpcstts[slurp_status]</font>.
	";	
	
	// for staff to change
	if($curusrslrprnk[slurp_rank_id] <= 5)
	{
		echo"
					<form name = 'pc_status_change' method='post' action = 'modules.php?name=$module_name&file=pc_edit_form'>
					Change Status here:
					<br>
					<br>
					NPC? <input type='checkbox' value='1' name='pc_npc'";
																		
		if($curpcnfo[creature_npc] == 1)
		{
			echo" checked";
		}
			
		echo">
					<br>
					<br>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
					<select class='engine' name = 'pc_status'>
					<option value = '$curpcstts[slurp_status_id]'>$curpcstts[slurp_status]</option>
		";
		
		$status_list = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_status WHERE slurp_status_id != '$curpcstts[slurp_status_id]' AND slurp_status_id < '10' AND slurp_status_id != '1'") or die ("failed getting status list.");
		while($sttslst = mysql_fetch_assoc($status_list))
		{
			echo"
					<option value = '$sttslst[slurp_status_id]'>$sttslst[slurp_status]</option>
			";
		}
	
		echo"
					</select>
					<br>
					<br>
					<input class='submit3' type='submit' value='Change Status' name='pc_status_change'>
					</form>
		";	
	}
	// end character status pane
		
	if($curpcnfo[creature_status_id] == 4)
	{
		$curpcearned = $curpcnfo[creature_xp_earned]-35;
		
		echo"<font title='25 Life Experience Build; + 10 to start at Level 1; and the $curpcearned that $curpcnfo[creature] has earned.'>";
		
		if($curpcnfo[creature_xp_current] >= 1)
		{
			echo"
						<font color = '#4AC948'>
			";
		}
		
		if($curpcnfo[creature_xp_current] == 0)
		{
			echo"
						<font color = 'red'>
			";
		}
	
		echo"
						<br>
						<br><b>$curpcnfo[creature_xp_current]</b> left</font> of <font color = '#00B2EE'><b>$curpcnfo[creature_xp_earned]</b></font> XP earned<br><b><font color = '#00B2EE' title='Character Level = [(Total Build - 25)/10]'>Lvl $character_level</font><b></font></font>
		";
	}
		
	echo"
								</td>
							</tr>
							<tr height='9'>
								<td valign = 'middle' align = 'left' width = '100%'>
								</td>
							</tr>
						</table>
					</td>
					<td width = '2%' valign = 'top' align = 'center'>
						<table width = '100%' cellspacing='0' cellpadding='0' border='0'>
							<tr background='themes/Vanguard/images/back2b.gif' height='24'>
								<td valign = 'middle' width = '100%' align = 'center'>
								
								</td>
							</tr>
						</table>
					</td>
					<td valign = 'top' width = '23%' align = 'right'>
	";
				
	include("modules/$module_name/includes/fm_bag_stones.php");
	
	echo"
		</td>
		<td width = '2%' valign = 'top' align = 'center'>
			<table width = '100%' cellspacing='0' cellpadding='0' border='0'>
				<tr background='themes/Vanguard/images/back2b.gif' height='24'>
					<td valign = 'middle' width = '100%' align = 'center'>
					
					</td>
				</tr>
			</table>
		</td>
		<td valign = 'top' align = 'left' width = '23%'>
			<table cellpadding='0' cellspacing='0' border='0' width='100%' valign='top'>
				<tr background='themes/Vanguard/images/back2b.gif' height='24'>
				<form name = 'pc_min_rank' method='post' action = 'modules.php?name=$module_name&file=pc_edit_form'>
					<td valign = 'middle' align = 'center'>
						<font class='heading2'>
						<b>MIN RANK</b>
						</font>
					</td>
				</tr>
				<tr>
					<td valign = 'top' align = 'center'>
		";
		
		$get_current_minimum_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix."creature ON ".$slrp_prefix."creature.creature_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix."creature.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting current min rank to view.");
		$gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);
		
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{
			echo"<select class='engine' name = 'min_rank'><option value = '$gtcurrmnrnk[slurp_rank_id]'>$gtcurrmnrnk[slurp_rank]</option>";
			
			$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id > '$curusrslrprnk[slurp_rank_id]' ORDER BY slurp_rank_id DESC") or die("failed to get rank list.");
			while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
			{
				echo"<option value = '$gtslrprnk[slurp_rank_id]'>$gtslrprnk[slurp_rank]</option>";
			}
			
			echo"</select>";
		}
		if($curusrslrprnk[slurp_rank_id] >= 6)
		{
			echo"<b><font color = '$rank_color'>$gtcurrmnrnk[slurp_rank]</font></b>";
		}
		
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{
			echo"
						<br>
						<br>
						<input type='hidden' value='$curpcnfo[creature_status_id]' name='pc_status'><input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input class='submit3' type='submit' value='Change Rank' name='pc_change_rank'>
				";
		}
		
		echo"
					</td>
				</tr>
				</form>
		";
		
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{
			if($curpcnfo[creature_status_id] == 4)
			{
				echo"
				<tr height='9'>
					<td valign = 'middle' align = 'center'>
					</td>
				</tr>
				<tr background='themes/Vanguard/images/back2b.gif' height='24'>
					<td valign = 'middle' align = 'center'>
						<font class='heading2'>
						<b>Print (Web only)</b>
						</font>
					</td>
				</tr>
				<tr height='5'>
					<td valign = 'middle' align = 'center'>
					</td>
				</tr>
				<tr>
					<td valign = 'middle' align = 'center'>
				";
				
			//	$tempDir = "images/";	
			//	$fileName = "qrcode_$curpcnfo[creature].png";
			//	$pngAbsoluteFilePath = $tempDir.$fileName;
			//	
			//	if(!file_exists($pngAbsoluteFilePath))
			//	{
			//		$codeContents = $_SERVER['SERVER_NAME']."/modules.php?name=My_Vanguard&file=pc_print_page&pc_to_print=$curpcnfo[creature_id]&verbose=1";
			//		// $urlRelativeFilePath = EXAMPLE_TMP_URLRELPATH.$fileName;
			//		QRcode::png($codeContents, $pngAbsoluteFilePath); 
			//	}
			//	
			//	echo"
			//			<img src='$pngAbsoluteFilePath' title='Print $curpcnfo[creature]'>
			//		</td>
			//	</tr>
			//	";
			}
		}
		
		echo"
			</table>
			<br>
	";
		
	if($curpcnfo[creature_status_id] == 8)
	{
		echo"
			<table cellpadding='0' cellspacing='0' border='0' width='100%'>
				<tr background='themes/Vanguard/images/back2b.gif' height='24'>
					<td valign = 'middle' align = 'left' width = '100%'>
						<font class='heading2'>
						<b>USE TEMPLATE</b>
					</td>
				</tr>
				<tr>
					<td valign = 'top' align = 'left' width = '100%'>
						<li> Start a new character using the $current_character_information Template.
						<li> Choose a race and name, then click below.
						<br>
						<br>
						<form name = 'copy_template' method='post' action = 'modules.php?name=$module_name&file=obj_edit'>
						<select class='engine' name = 'new_pc_creature'>
						<option value = '1'>Choose a Race</option>
		";
	
		$creature_type_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_type WHERE creature_type_id > '1' AND creature_type != 'Gender' AND creature_type_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY creature_type") or die ("failed getting creature type list.");
	
		while($crtyplst = mysql_fetch_assoc($creature_type_list))
		{
			echo"
						<optgroup label = '$crtyplst[creature_type]'>
			";
			
			$creature_subtype_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype INNER JOIN ".$slrp_prefix."creature_subtype_creature_type ON ".$slrp_prefix."creature_subtype_creature_type.creature_subtype_id = ".$slrp_prefix."creature_subtype.creature_subtype_id WHERE ".$slrp_prefix."creature_subtype.creature_subtype_id > '1' AND ".$slrp_prefix."creature_subtype_creature_type.creature_type_id = '$crtyplst[creature_type_id]' AND ".$slrp_prefix."creature_subtype.creature_subtype_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY ".$slrp_prefix."creature_subtype.creature_subtype") or die ("failed to get creature subtype list.");
			
			while($crsbtyplst = mysql_fetch_assoc($creature_subtype_list))
			{
				echo"
						<option value = '$crsbtyplst[creature_subtype_id]'>$crsbtyplst[creature_subtype]</option>
				";
			}
			echo"
						</optgroup>
			";
		}
	
		echo"
						</select>
						<br>
						<br>
						<input type='text' class='textbox3' size='20%' name='new_subfocus_name'></input>
		";
		
		if($curusrslrprnk[slurp_rank_id] <= 6)
		{
			echo"
						<br>
						<br>
						<font color = 'white'>
						NPC? </font><input type='checkbox' value='1' name='pc_npc'> . . . 
			";
		}
		
		echo"
						<input type='hidden' value='2' name='copy_pc_status'>
						<input type='hidden' value='$usrnfo[user_id]' name='copy_user_id'>
						<input type='hidden' value='$curpcnfo[creature_id]' name='copy_pc_id'>
						<input type='hidden' value='7' name='current_focus_id'>
						<input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
						<input class='submit3' type='submit' value='Submit' name='copy_template'>
					</td>
					</form>
				</tr>
			</table>
		";
	}
	
	echo"
	</td>
</tr>
	";
	
	if($curpcnfo[creature_status_id] == 4)
	{
		if($curusrslrprnk[slurp_rank_id] >= 6)
		{
			echo"
<tr height='9'>
	<td colspan = '9'>
	</td>
</tr>
<tr background='themes/Vanguard/images/back2b.gif' height='24'>
	<td valign = 'middle' align = 'center'>
		<font class='heading2'>
		
		</font>
	</td>
	<td valign = 'middle' align = 'left'>
	</td>
	<td valign = 'middle' align = 'center'>
		<font class='heading2'>
		Print (web only)
		</font>
	</td>
	<td valign = 'middle' align = 'left'>
	</td>
	<td valign = 'middle' align = 'center'>
		<font class='heading2'>
		
		</font>
	</td>
	<td valign = 'middle' align = 'left'>
	</td>
	<td valign = 'middle' align = 'center'>
		<font class='heading2'>
		Print Recs (web only)
		</font>
	</td>
</tr>
<tr>
	<td valign = 'top' align = 'left'>
		<font class='heading1'>
		
		</font>
	</td>
	<td width = '2%'>
	</td>
	<td valign = 'middle' align = 'center'>
		<font class='heading1'>
			";
						
	//		$tempDir = "images/";	
	//		$fileName = "qrcode_$curpcnfo[creature].png";
	//		$pngAbsoluteFilePath = $tempDir.$fileName;
	//		
	//		if(!file_exists($pngAbsoluteFilePath))
	//		{
	//			$codeContents = $_SERVER['SERVER_NAME']."/modules.php?name=My_Vanguard&file=pc_print_page&pc_to_print=$curpcnfo[creature_id]&verbose=1";
	//			// $urlRelativeFilePath = EXAMPLE_TMP_URLRELPATH.$fileName;
	//			QRcode::png($codeContents, $pngAbsoluteFilePath); 
	//		}
	//		
	//		echo"
	//					<img src='$pngAbsoluteFilePath' title='Print $curpcnfo[creature]'>"


			echo"
		</font>
</td>
</form>		
<td width = '2%'>
</td>
<td valign = 'middle' align = 'center' width = '32%'>
	<font class='heading1' color='white'>
	You must be logged into this website on the device scanning the QR codes for them to work correctly.
	<br>
	<br>
	We recommend using <a href='http://www.quickmark.com.tw/En/basic/downloadMain.asp'>Quickmark</a>,
	<br>
	but any QR code scanner should work.
	</font>
</td>
<td valign = 'middle' align = 'left'>
</td>
<td valign = 'middle' align = 'center'>
			";
		//	//	<img src='https://chart.googleapis.com/chart?cht=qr&chl=$qr_print_code&chs=120x120&choe=UTF-8&chld=L|0' alt='qr code'>	
		//	
		//	$rectempDir = "images/";	
		//	$recfileName = "qrcode_$curpcnfo[creature]_rec.png";
		//	$recpngAbsoluteFilePath = $rectempDir.$recfileName;
		//	
		//	if(!file_exists($recpngAbsoluteFilePath))
		//	{
		//		$reccodeContents = $_SERVER['SERVER_NAME']."/modules.php?name=My_Vanguard&file=pc_print_rec&pc_to_print=$curpcnfo[creature_id]";
		//		// $urlRelativeFilePath = EXAMPLE_TMP_URLRELPATH.$fileName;
		//		QRcode::png($reccodeContents, $recpngAbsoluteFilePath); 
		//	}
		//	
		//	echo"
		//			<img src='$recpngAbsoluteFilePath' title='Print Recoveries'>
	echo"</td>
</tr>
			";
		}
		
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{	
			if(isset($_POST['xp_conditions']))
			{
				if(isset($_POST['pc_earned_xp_1']))
				{
					$pc_earned_xp_1 = 1;
				}
				if(empty($_POST['pc_earned_xp_1']))
				{
					$pc_earned_xp_1 = 0;
				}
				
				$update_xp_condition_1 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_earned_xp_1 = '$pc_earned_xp_1' WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating xp conditions 1.");
				
				if(isset($_POST['pc_earned_xp_2']))
				{
					$pc_earned_xp_2 = 1;
				}
				if(empty($_POST['pc_earned_xp_2']))
				{
					$pc_earned_xp_2 = 0;
				}
				
				$update_xp_condition_2 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_earned_xp_2 = '$pc_earned_xp_2' WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating xp conditions 2.");
				
				
				if(isset($_POST['pc_earned_xp_3']))
				{
					$pc_earned_xp_3 = 1;
				}
				if(empty($_POST['pc_earned_xp_3']))
				{
					$pc_earned_xp_3 = 0;
				}
				
				$update_xp_condition_3 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_earned_xp_3 = '$pc_earned_xp_3' WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating xp conditions 3.");
								
				if(isset($_POST['pc_earned_xp_4']))
				{
					$pc_earned_xp_4 = 1;
				}
				if(empty($_POST['pc_earned_xp_4']))
				{
					$pc_earned_xp_4 = 0;
				}
				
				$update_xp_condition_4 = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_earned_xp_4 = '$pc_earned_xp_4' WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed updating xp conditions 4.");
			}
			
			$get_pc_again = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed checking updated xp conditions.");
			$gtpcag = mysql_fetch_assoc($get_pc_again);
			
			echo"
			<tr height='9'>
				<td colspan = '5'>
				</td>
			</tr>
			<tr background='themes/Vanguard/images/back2b.gif' height='24'>
				<td valign = 'middle' align = 'center'>
					<font class='heading2'>
					<b>REGULAR XP</b>
					</font>
				</td>
				<td valign = 'middle' align = 'left'>
				</td>
				<td valign = 'middle' align = 'center'>
					<font class='heading2'>
					<b>MANUAL XP</b>
					</font>
				</td>
				<td valign = 'middle' align = 'left'>
				</td>
				<td valign = 'middle' align = 'center'>
					<font class='heading2'>
					<b>PLAYER</b>
					</font>
				</td>
				<td valign = 'middle' align = 'left'>
				</td>
				<td valign = 'middle' align = 'center'>
					<font class='heading2'>
					Print Recs (web only)
					</font>
				</td>
			</tr>
			<tr>
				<td valign = 'top' align = 'left'>
			";
	
			if($gtpcag[creature_supplant_xp] >= 1)
			{
				echo"
					<font class='heading1'>
					XP has been manually updated.
					</font>
				";
			}
	
			if($gtpcag[creature_supplant_xp] == 1)
			{
				echo"
					<font class='heading1'>
					It is flagged to ignore Regular XP.
					</font>
				";
			}
			
			if($gtpcag[creature_supplant_xp] != 1)
			{
				echo"
					<font class='heading1'>
					<form name = 'xp_conditions' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
				";
				
				echo"<input type='checkbox' value='1' name='pc_earned_xp_1'";
				
				if($gtpcag[creature_earned_xp_1] >= 1)
				{
					echo"checked";
				}
				
				echo"> Attended $pgame[event] (+$slrpnfo[slurp_xp_reason_1])
					<br>
					<input type='checkbox' value='1' name='pc_earned_xp_2'";
							
				if($gtpcag[creature_earned_xp_2] >= 1)
				{
					echo"checked";
				}
				
				echo"> Stayed for checkout  (+$slrpnfo[slurp_xp_reason_2])
					<br>
					<input type='checkbox' value='1' name='pc_earned_xp_3'";
							
				if($gtpcag[creature_earned_xp_3] >= 1)
				{
					echo"checked";
				}
				
				echo"> Bonus Build paid (+$slrpnfo[slurp_xp_reason_3])
					<br>
					<input type='checkbox' value='1' name='pc_earned_xp_4'";
							
				if($gtpcag[creature_earned_xp_4] >= 1)
				{
					echo"checked";
				}
				
				echo"> Signed work time (+$slrpnfo[slurp_xp_reason_4])";
							
				$get_past_game_notes_count = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note INNER JOIN ".$slrp_prefix."event ON ".$slrp_prefix."event.event_id = ".$slrp_prefix."creature_game_note.event_id WHERE ".$slrp_prefix."creature_game_note.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_game_note.event_id = '$pgame[event_id]' ORDER BY ".$slrp_prefix."event.event_date DESC") or die ("failed getting existing notes.");
				$gtpstgmnotcnt = mysql_num_rows($get_past_game_notes_count);
				
				if($gtpstgmnotcnt >= 1)
				{
					echo"
					<br>
				<input type='checkbox' value='1' name='pc_earned_xp_5'";
								
					if($gtpcag[creature_earned_xp_5] >= 1)
					{
						echo"checked";
					}
					
					echo"> Downtime shows RP (+$slrpnfo[slurp_xp_reason_5])";
				}
				
				echo"
					<br>
					<br>
					</font>
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input class='submit3' type='submit' value='Modify Regular XP' name='xp_conditions'>
					</form>
				";
			}
			
			echo"
				</td>
				
				<td width = '2%'>
				</td>
				
				<form name = 'pc_history' method='post' action = 'modules.php?name=$module_name&file=pc_history'>
				<td valign = 'top' align = 'left' width = '32%'>
					<font class='heading1'>
					Reason:
					<br>
					<input type='text' class='textbox3' name='reason' size = '25%' value=''></input>
					<br>
					<br>
					<input type='radio' value='1' name='supplant_xp'><font size = '2'> Instead of regular XP</font>
					<br>
					<input type='radio' value='2' name='supplant_xp' checked><font size = '2'> In addition to regular XP</font>
					<br>
					<br>
					<input type='text' class='textbox3' size = '2%' name='xp' value=''></input> # of XP . . . <input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input class='submit3' type='submit' value='Add XP' name='pc_history'>
					</font>
				</td>
				</form>		
				
				<td width = '2%'>
				</td>
				<form name = 'pc_change_player' method='post' action = 'modules.php?name=$module_name&file=pc_edit_form'>
				<td valign = 'top' align = 'center' width = '32%'>
					<select class='engine' name = 'change_pc_player'>
			";
	
			$get_pc_player = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$curpcnfo[creature_nuke_user_id]'") or die("failed to get pc player.");
			$gtpcplyr = mysql_fetch_assoc($get_pc_player);
	
			echo"
					<option value = '$gtpcplyr[user_id]'>$gtpcplyr[username]</option>
			";
			
			$get_nuke_users = mysql_query("SELECT * FROM nuke_users INNER JOIN ".$slrp_prefix."slurp_user_rank ON ".$slrp_prefix."slurp_user_rank.user_id = nuke_users.user_id WHERE ".$slrp_prefix."slurp_user_rank.user_id > '2' ORDER BY username") or die("failed to get user list.");
			while($gtnkusrs = mysql_fetch_assoc($get_nuke_users))
			{
				echo"
					<option value = '$gtnkusrs[user_id]'>$gtnkusrs[username]</option>
				";
			}
			
			echo"
				</select>
				</font>
				<br>
				<br>
				<input type='hidden' value='$curpcnfo[creature_status_id]' name='pc_status'>
				<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
				<input class='submit3' type='submit' value='Change' name='pc_change_player'>
				</form>
			</td>
			<td valign = 'middle' align = 'left'>
			</td>
			<td valign = 'middle' align = 'center'>
			";
			//	<img src='https://chart.googleapis.com/chart?cht=qr&chl=$qr_print_code&chs=120x120&choe=UTF-8&chld=L|0' alt='qr code'>	
			
		//	$rectempDir = "images/";	
		//	$recfileName = "qrcode_$curpcnfo[creature]_rec.png";
		//	$recpngAbsoluteFilePath = $rectempDir.$recfileName;
		//
		//	if(!file_exists($recpngAbsoluteFilePath))
		//	{
		//		$reccodeContents = $_SERVER['SERVER_NAME']."/modules.php?name=My_Vanguard&file=pc_print_rec&pc_to_print=$curpcnfo[creature_id]";
		//		// $urlRelativeFilePath = EXAMPLE_TMP_URLRELPATH.$fileName;
		//		QRcode::png($reccodeContents, $recpngAbsoluteFilePath); 
		//	}
		//	
		//	echo"
		//						<img src='$recpngAbsoluteFilePath' title='Print Recoveries'>
		echo"		</td>
			</tr>
			";
		}		
	}
	
	echo"
</table>
	";
	CloseTable3();
}

include("modules/$module_name/includes/pcinfo.php");

//begin ablities
$get_pc_abilities_count = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_ability.creature_ability_count > '-999' ORDER BY ".$slrp_prefix."ability.ability ASC");
$curpcabscnt = mysql_num_rows($get_pc_abilities_count);

$hover_ablist_text = "";
$get_hover_abilities_list = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' ORDER BY ".$slrp_prefix."ability.ability") or die("failed to get elf pc ability.");
$gthvrabscnt = mysql_num_rows($get_hover_abilities_list);
$hvabs_count = $gthvrabscnt;
if($gthvrabscnt >= 1)
{
	while($gthvrabs = mysql_fetch_assoc($get_hover_abilities_list))
	{
		if($hvabs_count >= 1)
		{
			$hover_ablist_text = $hover_ablist_text."$gthvrabs[ability]";
			
			if($hvabs_count >= 2)
			{
				$hover_ablist_text = $hover_ablist_text."; ";
			}
		}
		$hvabs_count--;
	}
}

echo"
<tr>
	<td colspan = '9' align = 'left' valign  = 'top' width='100%'>
		<table cellpadding='0' cellspacing='0' border='0' width = '100%'>
			<tr background='themes/Vanguard/images/back2b.gif' height='24'>
				<td width = '100%' align = 'left' valign = 'middle' colspan='9'>
					<table cellpadding='0' cellspacing='0' border='0' width='100%'>
						<tr>
							<td width='23%' align='right' valign='middle'>
								<font class='heading2' title='$hover_ablist_text'><b>$curpcabscnt ABILITIES</b></font>
							</td>
							<td width='2%'>
							</td>
							<form name = 'show_hide_ablist' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
							<td width='15%' align='left' valign='middle'>
							";
							
							if($ablist_expander == 1)
							{
								echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='0' name = 'ablist_expander'><input class='submit3' type='submit' value='Hide Abilities'>";
							}
								
							if($ablist_expander == 0)
							{
								echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'><input type='hidden' value='1' name = 'ablist_expander'><input class='submit3' type='submit' value='Show Abilities'>";
							}
							
							echo"
							</td>
							</form>
							<td width='2%'>
							</td>
							<form name = 'pc_ab' method='post' action = 'modules.php?name=$module_name&file=pc_eff_typ'>
							<td width='15%' align='left' valign='middle'>
							";

							if($curpcnfo[creature_status_id] == 2)
							{
								echo"
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
								<input class='submit3' type='submit' value='Spend Build' name='pc_ab'>
								";
							}
							
							if($curusrslrprnk[slurp_rank_id] <= 7)
							{
								if($curpcnfo[creature_status_id] == 3) 
								{
									echo"
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
								<input class='submit3' type='submit' value='Spend Build' name='pc_ab'>
									";
								}
							}

							if($curpcnfo[creature_status_id] == 4)
							{
								echo"
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
								<input class='submit3' type='submit' value='Spend Build' name='pc_ab'>
								";
							}
							
							echo"		
							</td>
							</form>
							<td width='2%'>
							</td>
							<form name = 'pc_ab_list' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
							<td width='18%' align='right' valign='middle'>
							<font class='heading1'>
							<font color = '#33F406' title = 'Total Hit Points = 10 + (Level*5) [+Extra Hit Points] [+Pain Tolerance]'>$total_hit_points</font> HP and <font color = '#00B2EE'title = 'Total Recoveries = 5 [+Extra Recoveries] [+Pensive]'>$total_recoveries</font> Recs/day
							</font>
							</td>
							</form>
							<td width='2%'>
							</td>
							<form name = 'pc_ab_list' method='post' action = 'modules.php?name=$module_name&file=ab_list'>
							<td width='18%' align='right' valign='middle'>
								<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
								<input type='hidden' value='$expander_abbr' name='current_expander'>
								<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
								<input type='hidden' value='$compab_expander' name = 'compab_expander'>
								<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
								<input type='hidden' value='$production_expander' name = 'production_expander'>
								<input type='hidden' value='$materials_expander' name = 'materials_expander'>
								<input type='hidden' value='$items_expander' name = 'items_expander'>
								<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
								<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
								<input type='hidden' value='$admin_expander' name = 'admin_expander'>
								<input class='submit3' type='submit' value='Abilities List' name='pc_ab_list'>
							</td>
							</form>
						</tr>
					</table>
				</td>
			</tr>
";

if($ablist_expander == 1)
{
	echo"
			<tr height='9'>
				<td colspan='9'>
				</td>
			</tr>
			<tr>
				<td width='100%' align = 'left' valign = 'middle' colspan='9'>
	";

	OpenTable3();
	
	echo"
					<table cellpadding='0' cellspacing='0' border='0' width='100%'>
	";
	
	if($curusrslrprnk[slurp_rank_id] >= 2)
	{
		if($curpcnfo[creature_status_id] == 4)
		{
			// get libraries
			$library_list = mysql_query("SELECT * FROM ".$slrp_prefix."library WHERE library_minimum_rank >= '$curusrslrprnk[slurp_rank_id]' AND library_status_id >= '4' ORDER BY library") or die ("failed getting library list.");
			$liblstcnt = mysql_num_rows($library_list);
			
			if($liblstcnt >= 1)
			{
				// if there are entries, list them in th drop-down of all libraries for that market/residence
				echo"
			<tr height='24' background='themes/Vanguard/images/back2b.gif'>
				<form name = 'pc_ab_list' method='post' action = 'modules.php?name=$module_name&file=ab_shop'> 
				<td align='left' valign='middle' width='100%' colspan='9'>
					<font class='heading2'>Available Libraries&nbsp;&nbsp;&nbsp;
					<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
					<input type='hidden' value='$expander_abbr' name='current_expander'>
					<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
					<input type='hidden' value='$compab_expander' name = 'compab_expander'>
					<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
					<input type='hidden' value='$materials_expander' name = 'materials_expander'>
					<input type='hidden' value='$items_expander' name = 'items_expander'>
					<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
					<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
					<input type='hidden' value='$production_expander' name = 'production_expander'>
					<input type='hidden' value='$admin_expander' name = 'admin_expander'>
					<select class='engine' name = 'library_id'>
					<option value='1'>Choose Library</option>
				";
				
				while($liblst = mysql_fetch_assoc($library_list))
				{
					$library_cult = mysql_query("SELECT * FROM ".$slrp_prefix."library_culture WHERE library_id = '$liblst[library_id]' AND culture_id > '1'") or die ("failed getting library culture list.");
					$libcultcnt = mysql_num_rows($library_cult);
					$library_geo= mysql_query("SELECT * FROM ".$slrp_prefix."library_geography WHERE library_id = '$liblst[library_id]' AND geography_id > '1'") or die ("failed getting library geography list.");
					$libgeocnt = mysql_num_rows($library_geo);
					$library_pc = mysql_query("SELECT * FROM ".$slrp_prefix."library_creature WHERE library_id = '$liblst[library_id]' AND creature_id > '1'") or die ("failed getting library creature list.");
					$libpccnt = mysql_num_rows($library_pc);
					$library_race = mysql_query("SELECT * FROM ".$slrp_prefix."library_creature_subtype WHERE library_id = '$liblst[library_id]' AND creature_subtype_id > '1'") or die ("failed getting library race list.");
					$librccnt = mysql_num_rows($library_race);
				
					$liblst_total = $libpccnt+$libgeocnt+$libcultcnt+$librccnt;
					// echo"<option value = ''>$liblst_total = $libpccnt+$libgeocnt+$libcultcnt+$librccnt</option>";
					if($liblst_total >= 1)
					{
						$libcnt = 0;
						while($libcult = mysql_fetch_assoc($library_cult))
						{
							$get_creature_cultures = mysql_query("SELECT * FROM ".$slrp_prefix."creature_culture WHERE creature_id = '$curpcnfo[creature_id]' AND culture_id = '$libcult[culture_id]' AND (culture_tolerance_id = '8' OR culture_tolerance_id = '10' OR culture_tolerance_id = '11')") or die ("failed getting pc culture list");
							while($pccultr = mysql_fetch_assoc($get_creature_cultures))
							{
								// echo"$libcult[library_culture_id] == $pccultr[culture_id]<br>";
								if($libcult[culture_id] == $pccultr[culture_id])
								{
									echo"<option value = '$liblst[library_id]'>$liblst[library]</option>";
								}
							}
						}
						while($libpc = mysql_fetch_assoc($library_pc))
						{
							// echo"$libcult[library_creature_id] == $curpcnfo[creature_id]<br>";
							if($libpc[creature_id] == $curpcnfo[creature_id])
							{
								echo"<option value = '$liblst[library_id]'>$liblst[library]</option>";
							}
						}
						while($libgeo = mysql_fetch_assoc($library_geo))
						{
							// echo"$libgeo[library_geography_id] == $curpcnfo[creature_market]<br>";
							if($libgeo[geography_id] == $curpcnfo[creature_market])
							{
								echo"<option value = '$liblst[library_id]'>$liblst[library]</option>";
							}
						}
						while($librc = mysql_fetch_assoc($library_race))
						{
							// echo"$libgeo[library_geography_id] == $curpcnfo[creature_market]<br>";
							$get_creature_race34 = mysql_query("SELECT * FROM ".$slrp_prefix."creature_creature_subtype WHERE creature_id = '$curpcnfo[creature_id]' AND creature_subtype_id = '$librc[creature_subtype_id]'") or die ("failed getting pc race list");
							while($pcrc34 = mysql_fetch_assoc($get_creature_race34))
							{
								// echo"$libcult[library_culture_id] == $pccultr[culture_id]<br>";
								if($librc[creature_subtype_id] == $pcrc34[creature_subtype_id])
								{
									echo"<option value = '$liblst[library_id]'>$liblst[library]</option>";
								}
							}
						}
					}
				}
			
				echo"
						</select> . . . <input class='submit3' type='submit' value='View Library' name='ab_shop'>
						</font>
					</td>
					</form>
				</tr>
				";
			}
		}
	}
	
	echo"	
			<tr height='9'>
				<td colspan='7'>
				</td>
			</tr>
			<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
				<td width='20%' align='center' valign='middle'>
					<font class='heading2'>ABILITY</font>
	";
	
	echo"
				</td>
				<td width = '2%'>
				</td>
				<td width = '20%' align = 'left' valign = 'middle'>
	";
	if($compab_expander == 1)
	{
		echo"
					<font class='Heading2'>
					Usage (Hover for Info)
					</font>
		";
	}
	
	echo"		
				</td>
				<td width = '2%'>
				</td>
				<form name = 'show_hide_components' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'> 
				<td width = '44%' align = 'left' valign = 'middle'>
				<font class='heading2'>Verbal/Description/Prereqs</font>&nbsp;&nbsp;&nbsp;
	";
	
	if($compab_expander == 0)
	{
		echo"
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
							<input type='hidden' value='1' name = 'compab_expander'>
							<input class='submit3' type='submit' value='Show Components'>
		";
	}
	if($compab_expander == 1)
	{
		echo"
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
							<input type='hidden' value='0' name = 'compab_expander'>
							<input class='submit3' type='submit' value='Hide Components'>
		";
	}
	
	echo"				
				</td>
				</form>
			</tr>
	";
	
	// get racial abilities for the character
	$get_race_abilities2 = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND  ".$slrp_prefix."creature_ability.creature_ability_count > '-999' AND ".$slrp_prefix."ability.ability_set_id = '12' ORDER BY ".$slrp_prefix."ability.ability ASC") or die ("failed getting pc race abs.");
	$currcab2cnt = mysql_num_rows($get_race_abilities2);
	if($currcab2cnt >= 1)
	{
		echo"
			<tr>
				<td width='100%' colspan='9'>
					<table width='100%' cellpadding='0' cellspacing='0' border='0'>
		";
	
		while($currcab2 = mysql_fetch_assoc($get_race_abilities2))
		{
			$ab_nfo_id = $currcab2[ability_id];
			$dressed = 0;
			$ab_shop = 0;
			include("modules/$module_name/includes/fn_ab_nfo.php");
		}
			
		echo"
					</table>
				</td>
			</tr>
		";
	}
	
	// get learned abilities for the character
	$get_pc_abilities2 = mysql_query("SELECT * FROM ".$slrp_prefix."ability INNER JOIN ".$slrp_prefix."creature_ability ON ".$slrp_prefix."creature_ability.ability_id = ".$slrp_prefix."ability.ability_id WHERE ".$slrp_prefix."creature_ability.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."ability.ability_set_id != '12' ORDER BY ".$slrp_prefix."ability.ability ASC") or die ("failed getting pc race abs.");
	$curpcab2cnt = mysql_num_rows($get_pc_abilities2);
	if($curpcab2cnt >= 1)
	{
		echo"
			<tr>
				<td width='100%' colspan='9'>
					<table width='100%' cellpadding='0' cellspacing='0' border='0'>
		";
	
		while($curpcab2 = mysql_fetch_assoc($get_pc_abilities2))
		{
			$ab_nfo_id = $curpcab2[ability_id];
			$dressed = 0;
			$ab_shop = 0;
			include("modules/$module_name/includes/fn_ab_nfo.php");
		}
			
		echo"
					</table>
				</td>
			</tr>
		";
	}
	
		echo"
		</table>
	";
	
	CloseTable3();

	echo"
	</td>
</tr>
	";
}

echo"
		</table>
	</td>
</tr>
";

if($curpcnfo[creature_status_id] != 8)
{
	echo"
	<tr height='9'>
		<td valign = 'top' align = 'left' colspan = '9'>
		</td>
	</tr>
	<tr background='themes/Vanguard/images/back2b.gif' height='24'>
		<td valign = 'middle' align = 'right' width='23%'>
			<font class='heading2'>
			<b>BACKGROUND</b>
			</font>
		</td>
		<td width = '2%'>
		</td>
		<form name = 'race_desc' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>	
		<td valign = 'middle' align = 'left' width='23%'>
	";
	
	if($race_desc_expander == 1)
	{
		echo"
			<input type='hidden' value='0' name='race_desc_expander'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$expander_abbr' name='current_expander'>
			<input type='hidden' value='$compab_expander' name = 'compab_expander'>
			<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$materials_expander' name = 'materials_expander'>
			<input type='hidden' value='$items_expander' name = 'items_expander'>
			<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
			<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
			<input type='hidden' value='$production_expander' name = 'production_expander'>
			<input type='hidden' value='$admin_expander' name = 'admin_expander'>
			<input class='submit3' type='submit' value='Hide Background Info' name='race_desc'>";
	}
	
	if($race_desc_expander == 0)
	{
		echo"
			<input type='hidden' value='1' name='race_desc_expander'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$expander_abbr' name='current_expander'>
			<input type='hidden' value='$compab_expander' name = 'compab_expander'>
			<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$materials_expander' name = 'materials_expander'>
			<input type='hidden' value='$items_expander' name = 'items_expander'>
			<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
			<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
			<input type='hidden' value='$production_expander' name = 'production_expander'>
			<input type='hidden' value='$admin_expander' name = 'admin_expander'>
			<input class='submit3' type='submit' value='Show Background Info' name='race_desc'>	
		";
	}
	
	echo"
		</td>
		</form>
		<td width = '2%'>
		</td>
		<td align = 'left' valign = 'middle' width='33%'>
		";
		
		if($race_desc_expander == 0)
		{
			$get_pc_race = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype INNER JOIN ".$slrp_prefix."creature_creature_subtype ON ".$slrp_prefix."creature_subtype.creature_subtype_id = ".$slrp_prefix."creature_creature_subtype.creature_subtype_id WHERE ".$slrp_prefix."creature_creature_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting pc race.");
			$gtpcrc = mysql_fetch_assoc($get_pc_race);
			
			$verify_pc_reg= mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype INNER JOIN ".$slrp_prefix."creature_geography_subtype ON ".$slrp_prefix."creature_geography_subtype.geography_subtype_id = ".$slrp_prefix."geography_subtype.geography_subtype_id WHERE ".$slrp_prefix."creature_geography_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc region info.");
			$verpcrg = mysql_fetch_assoc($verify_pc_reg);
			$verpcrgcnt = mysql_num_rows($verify_pc_reg);
			echo"<font class='heading1'>$gtpcrc[creature_subtype]</font>";
		}
		
		echo"
		</td>
		</form>
		<td width='2%'>
		</td>
		<form name = 'pc_bg' method='post' action = 'modules.php?name=$module_name&file=pc_bg'>
		<td align = 'right' valign = 'middle' width='13%'>
	";
	
	$get_pc_creature_sub = mysql_query("SELECT * FROM ".$slrp_prefix."creature_creature_subtype WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed getting pc creature sub.");
	$gtpccrsb = mysql_fetch_assoc($get_pc_creature_sub);
	
	if($curpcnfo[creature_status_id] == 2)
	{
		echo"
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='$expander_abbr' name='current_expander'>
			<input type='hidden' value='$compab_expander' name = 'compab_expander'>
			<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input type='hidden' value='$materials_expander' name = 'materials_expander'>
			<input type='hidden' value='$items_expander' name = 'items_expander'>
			<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
			<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
			<input type='hidden' value='$production_expander' name = 'production_expander'>
			<input type='hidden' value='$admin_expander' name = 'admin_expander'>
			<input type='hidden' value='$curpcnfo[creature_nuke_user_id]' name='player_id'>
			<input class='submit3' type='submit' value='Edit Background' name='pc_bg'>
		";
	}
	
	if($curpcnfo[creature_status_id] >= 3)
	{
		if($curusrslrprnk[slurp_rank_id] <= 7)
		{
			echo"
				<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
				<input type='hidden' value='$expander_abbr' name='current_expander'>
				<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
				<input type='hidden' value='$compab_expander' name = 'compab_expander'>
				<input type='hidden' value='$compeff_expander' name = 'compeff_expander'>
				<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
				<input type='hidden' value='$materials_expander' name = 'materials_expander'>
				<input type='hidden' value='$items_expander' name = 'items_expander'>
				<input type='hidden' value='$recipe_expander' name = 'recipe_expander'>
				<input type='hidden' value='$ablist_expander' name = 'ablist_expander'>
				<input type='hidden' value='$production_expander' name = 'production_expander'>
				<input type='hidden' value='$admin_expander' name = 'admin_expander'>
				<input type='hidden' value='$curpcnfo[creature_nuke_user_id]' name='player_id'>
				<input class='submit3' type='submit' value='Edit Background' name='pc_bg'>
			";
		}
	}
	
	echo"
		</td>
		</form>
	</tr>
	<tr>
		<td colspan = '9' height='9'>
		</td>
	</tr>
	";
	
	if($race_desc_expander == 1)
	{
		echo"<tr>
		<td align = 'left' colspan='9' valign='top' width='100%'>";
		
		OpenTable3();
		echo"<table cellspacing='0' cellpadding='0' border='0' width='100%'>
				<tr>
					<td width = '100%' colspan = '9'>
		";
			
		$verify_pc_reg = mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype INNER JOIN ".$slrp_prefix."creature_geography_subtype ON ".$slrp_prefix."creature_geography_subtype.geography_subtype_id = ".$slrp_prefix."geography_subtype.geography_subtype_id WHERE ".$slrp_prefix."creature_geography_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc region info.");
		$verpcrg = mysql_fetch_assoc($verify_pc_reg);
		$verpcrgcnt = mysql_num_rows($verify_pc_reg);
		
		$verify_pc_loc = mysql_query("SELECT * FROM ".$slrp_prefix."geography INNER JOIN ".$slrp_prefix."creature_geography ON ".$slrp_prefix."creature_geography.geography_id = ".$slrp_prefix."geography.geography_id WHERE ".$slrp_prefix."creature_geography.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc location info.");
		$verpclc = mysql_fetch_assoc($verify_pc_loc);
		$verpclccnt = mysql_num_rows($verify_pc_loc);
		
		$verify_pc_cult = mysql_query("SELECT * FROM ".$slrp_prefix."culture INNER JOIN ".$slrp_prefix."creature_culture ON ".$slrp_prefix."creature_culture.culture_id = ".$slrp_prefix."culture.culture_id WHERE ".$slrp_prefix."creature_culture.creature_id = '$curpcnfo[creature_id]'") or die ("failed verifying pc culture origin info.");
		$verpccultcnt = mysql_num_rows($verify_pc_cult);
		
		$get_pc_race = mysql_query("SELECT * FROM ".$slrp_prefix."creature_subtype INNER JOIN ".$slrp_prefix."creature_creature_subtype ON ".$slrp_prefix."creature_subtype.creature_subtype_id = ".$slrp_prefix."creature_creature_subtype.creature_subtype_id WHERE ".$slrp_prefix."creature_creature_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting pc race.");
		$gtpcrc = mysql_fetch_assoc($get_pc_race);
	
		$get_creature_type = mysql_query("SELECT * FROM ".$slrp_prefix."creature_type INNER JOIN ".$slrp_prefix."creature_subtype_creature_type ON ".$slrp_prefix."creature_type.creature_type_id = ".$slrp_prefix."creature_subtype_creature_type.creature_type_id WHERE ".$slrp_prefix."creature_subtype_creature_type.creature_subtype_id = '$gtpcrc[creature_subtype_id]'") or die ("failed getting race.");
		while($gtcrtyp = mysql_fetch_assoc($get_creature_type))
		{
			echo"<font class='heading2'>PHENOTYPE: $gtcrtyp[creature_type]</font>";
			echo"<br>";
			echo"<font class='heading1'>$gtcrtyp[creature_type_desc]</font>";
			echo"<br>";
			echo"<br>";
		}	
	
		echo"<font class='heading2'>RACE: $gtpcrc[creature_subtype]</font>";
		echo"<br>";
		echo"<font class='heading1'>$gtpcrc[creature_subtype_desc]</font>";
		echo"<br>";
		echo"<br>";
	
		
//		$get_creature_region = mysql_query("SELECT * FROM ".$slrp_prefix."geography_subtype INNER JOIN ".$slrp_prefix."creature_geography_subtype ON ".$slrp_prefix."geography_subtype.geography_subtype_id = ".$slrp_prefix."creature_geography_subtype.geography_subtype_id WHERE ".$slrp_prefix."creature_geography_subtype.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting pc region.");
//		while($gtcrrg = mysql_fetch_assoc($get_creature_region))
//		{
//			echo"<font class='heading2'>REGION of ORIGIN: $gtcrrg[geography_subtype]</font>";
//			echo"<br>";
//			echo"<font class='heading1'>$gtcrrg[geography_subtype_desc]</font>";
//			echo"<br>";
//			echo"<br>";
//		}
//		
//		$get_creature_locale = mysql_query("SELECT * FROM ".$slrp_prefix."geography INNER JOIN ".$slrp_prefix."creature_geography ON ".$slrp_prefix."geography.geography_id = ".$slrp_prefix."creature_geography.geography_id WHERE ".$slrp_prefix."creature_geography.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting pc locale.");
//		while($gtcrlcl = mysql_fetch_assoc($get_creature_locale))
//		{
//			echo"<font class='heading2'>PLACE of ORIGIN: $gtcrlcl[geography]</font>";
//			echo"<br>";
//			echo"<font class='heading1'>$gtcrlcl[geography_desc]</font>";
//			echo"<br>";
//			echo"<br>";
//		}
	
		$post_creature_insanity_listings = mysql_query("SELECT * FROM ".$slrp_prefix."creature_effect WHERE ".$slrp_prefix."creature_effect.creature_id = '$curpcnfo[creature_id]'") or die ("failed getting insanity post list.");
		while($postcrnsan = mysql_fetch_assoc($post_creature_insanity_listings))
		{
			$verify_crnsannfo = mysql_query("SELECT * FROM ".$slrp_prefix."effect WHERE effect_id = '$postcrnsan[effect_id]'") or die ("failed verifying pc insanity effect info.");
			$verpcnsan = mysql_fetch_assoc($verify_crnsannfo);
			$get_insanity_target_modifier_info = mysql_query("SELECT * FROM ".$slrp_prefix."ability_modifier WHERE ability_modifier_id = '$postcrnsan[effect_modifier_id]'") or die ("failed getting tolerances list.");
			$getnsantargmodnfo = mysql_fetch_assoc($get_insanity_target_modifier_info);
			
			echo"<font class='heading2'>Insanity: </font><font class='heading1'>$verpcnsan[effect] [$getnsantargmodnfo[ability_modifier_short]]:</font><br><br>";
		}
		
		echo"
		</td>
	</tr>
		
	<tr>
		<td valign = 'top' align = 'left' colspan = '5'>
			<font class='heading2'>
			Parents:</font> <font class='heading1'>$curpcnfo[creature_parents]</font>
			
			<br>
			<br>
			<font class='heading2'>
			Social Background:</font> <font class='heading1'>$curpcnfo[creature_socio]</font>
		</td>
	</tr>
		";
	}
	
	if($race_desc_expander == 1)
	{
		echo"
	<tr>
		<td colspan = '9' height='9'>
		</td>
	</tr>
	<tr>
		<td valign = 'top' align = 'left' width = '32%'>
			<font class='heading2'>
			Events that Shaped $current_character_information
		";
		
		if($curusrslrprnk[slurp_rank_id] <= 3)
		{
			echo" <font class='heading1'><font color = 'red'>($current_character_true_name)</font></font>";
		}
		
		echo"
			<br>
			<font class='heading1'>
			<li>$curpcnfo[creature_shaped1] 
			<li>$curpcnfo[creature_shaped2] 
			<li>$curpcnfo[creature_shaped3] 
		</td>
		<td valign = 'top' align = 'left' width = '2%'>
		</td>
		<td valign = 'top' align = 'left' width = '32%'>
			<font class='heading2'>
			Hatreds, Taboos, Hunters/-eds, Obsessions, etc.
			<br>
			</font>
			<font class='heading1'>
			<li>$curpcnfo[creature_hunted1] 
		";
		
		if($curpcnfo[creature_hunted2] != "")
		{
			echo"<li>$curpcnfo[creature_hunted2]";
		}
		
		if($curpcnfo[creature_hunted3] != "")
		{
			echo"<li>$curpcnfo[creature_hunted3]";
		}
		
		echo"
		</td>
		<td valign = 'top' align = 'left' width = '2%'>
		</td>
		<td valign = 'top' align = 'left' width = '32%'>
		";
		
//		echo"
//			<font class='heading2'>			
//				Organizational ties
//			<br>
//			</font>
//			<font class='heading1'>
//		";
//		
//		while($verpccult = mysql_fetch_assoc($verify_pc_cult))
//		{
//			$verify_pc_cult_tol = mysql_query("SELECT * FROM ".$slrp_prefix."culture_tolerance INNER JOIN ".$slrp_prefix."creature_culture ON ".$slrp_prefix."creature_culture.culture_tolerance_id = ".$slrp_prefix."culture_tolerance.culture_tolerance_id WHERE ".$slrp_prefix."creature_culture.creature_id = '$curpcnfo[creature_id]' AND culture_id = '$verpccult[culture_id]'") or die ("failed verifying pc culture tolerance info.");
//			$verpcculttol = mysql_fetch_assoc($verify_pc_cult_tol);
//			$verpcculttolcnt = mysql_num_rows($verify_pc_cult_tol);
//			
//			$grphc_subfocus = mysql_query("SELECT * FROM ".$slrp_prefix."object_graphic WHERE object_focus_id = '30' AND object_id = '$verpccult[culture_id]' AND object_slurp_id = '$slrpnfo[slurp_id]'") or die("failed to get culture graphic.");
//			$grphcsbfccnt = mysql_num_rows($grphc_subfocus);
//			$grphcsbfc = mysql_fetch_assoc($grphc_subfocus);
//			$graphic_identifier = $grphcsbfc[graphic_id];
//			if($graphic_identifier >= 2)
//			{
//				// get the object graphic, if any
//				$get_object_graphic = mysql_query("SELECT * FROM ".$slrp_prefix."graphic WHERE graphic_id = '$graphic_identifier'") or die ("failed to get graphic info.");
//				$gtobjgrphccnt = mysql_num_rows($get_object_graphic);
//				$gtobjgrphc = mysql_fetch_assoc($get_object_graphic);
//				$graphic_identifier = $gtobjgrphc[graphic_id];
//				// echo"current graphic($gtobjgrphccnt): $gtpbjgrphc[graphic]<br>";
//				echo"<img src = 'images/$gtobjgrphc[graphic]' height = '20' width = '20'> ";
//			}
//			
//			echo"<font size = '2' color = 'orange'>  $verpccult[culture] ($verpcculttol[culture_tolerance])<br>";
//		}
//		
		echo"
		</td>
	</tr>
		";
		if($curpcnfo[creature_story] != '')
		{
			echo"
	<tr>
		<td colspan = '9' height='9'>
		</td>
	</tr>
	<tr>
		<td valign = 'top' align = 'left' width = '100%' colspan = '5'>
		<font class='heading2'>
		Full Background Story
		<br>
		</font>
		$curpcnfo[creature_story]
		</td>
	</tr>
			";
		}
		
		echo"
	</table>
		";
		
		CloseTable3();
		echo"
		</td>
	</tr>
		";
	}
}

echo"
<tr background='themes/Vanguard/images/back2b.gif' height='24'>
	<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
	<td valign = 'middle' align = 'left' width='23%'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$ntro_expander' name = 'ntro_expander'><input type='hidden' value='$materials_expander' name = 'materials_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='hidden' value='1' name='char_expander'>
		<input class='submit3' type='submit' value='Back to Main' name='go_home'>
	</td>
	</form>
	<td valign = 'top' align = 'left' width = '2%'>
	</td>
	<form name = 'show_hide_ntro' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
	<td valign = 'middle' align = 'left' width='23%'>
		";
		
		if($ntro_expander == 0)
		{
			echo"
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='hidden' value='1' name = 'ntro_expander'>
		<input class='submit3' type='submit' value='Show Instructions'>
			";
		}
		if($ntro_expander == 1)
		{
			echo"
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'><input type='hidden' value='$expander_abbr' name='current_expander'><input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'><input type='hidden' value='$compab_expander' name = 'compab_expander'><input type='hidden' value='$production_expander' name = 'production_expander'><input type='hidden' value='$items_expander' name = 'items_expander'><input type='hidden' value='$recipe_expander' name = 'recipe_expander'><input type='hidden' value='$ablist_expander' name = 'ablist_expander'><input type='hidden' value='$admin_expander' name = 'admin_expander'>
		<input type='hidden' value='0' name = 'ntro_expander'>
		<input class='submit3' type='submit' value='Hide Instructions'>
			";
		}
		
		echo"
	</td>
	</form>
	<td valign = 'top' align = 'left' width = '2%'>
	</td>
		<td valign = 'middle' align = 'left' width='23%'>
		
	</td>
	<td valign = 'top' align = 'left' width = '2%'>
	</td>
		<td valign = 'middle' align = 'left' width='23%'>
		
	</td>
</tr>
	<tr height='9'>
		<td valign = 'top' align = 'left' colspan = '9'>
		</td>
	</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>