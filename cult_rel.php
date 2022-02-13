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
if(isset($_POST['group_name']))
{
	$group_name = $_POST['group_name'];
	$nav_obj = mysql_query("SELECT * FROM van_object WHERE object_table = '$group_name'") or die ("failed getting nav obj.");
	$nvobj = mysql_fetch_assoc($nav_obj);
}

$nav_title = "$nvobj[object] Relations";
$nav_page = 'cult_rel';
include("modules/$module_name/includes/slurp_header.php");

// checkbox variables for the index
if(isset($_POST['current_expander']))
{
	$expander_abbr = $_POST['current_expander'];
	$expander = ($expander_abbr."_expander");
}
// echo"exp: $expander_abbr, $expander<br>";

if(isset($_POST['group_name']))
{
	$group_name = $_POST['group_name'];
	$instance_name = $group_name;
	// echo"grp: $group_name<br>";
	
	$verbose = $_POST['verbose'];
	
	$get_focus = mysql_query("SELECT * FROM ".$slrp_prefix."focus WHERE focus_table = '".$group_name."' OR focus_table = '".$group_name."_subtype' OR focus_table = '".$group_name."_type' ORDER BY focus_id") or die("failed to get $group_name focus.");
	$getfoc = mysql_fetch_assoc($get_focus);
	
	$object_list_3 = mysql_query("SELECT * FROM ".$slrp_prefix."object WHERE object_table = '$group_name' AND object_view_rank_id >= '$curusrslrprnk[slurp_rank_id]'") or die ("failed getting object list 3.");
	$objlst3 = mysql_fetch_assoc($object_list_3);
}

echo"
<table border='0' width = '100%' cellpadding='0' cellspacing='0'>
	<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
		<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
		<td valign='middle' align='left'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_creature_id'>
			<input type='hidden' value='1' name='".$expander_abbr."_expander'>
			<input class='submit3' type='submit' value='Back to Main' name='go_home'>
		</td>
		</form>
		<td colspan='3' align='right' valign='middle'>
			<font class='heading2'>
			CULTURE
			</font>
		</td>
		<td width='2%'>
		</td>
		<td colspan='4' align='center' valign='middle'>
			<font class='heading2'>
			RELATION
			</font>
		</td>
	</tr> 
";

$get_instances = mysql_query("SELECT * FROM ".$slrp_prefix.$instance_name." WHERE ".$slrp_prefix.$instance_name.".".$instance_name."_min_rank >= '$curusrslrprnk[slurp_rank_id]' AND ".$slrp_prefix.$instance_name.".".$instance_name."_id > '1' AND ".$slrp_prefix.$instance_name.".".$instance_name."_status_id = '4' AND ".$slrp_prefix.$instance_name.".".$instance_name."_slurp_id = '$slrpnfo[slurp_id]' ORDER BY ".$instance_name."") or die("failed to get ".$instance_name."s instance.");
while($getinst = mysql_fetch_assoc($get_instances))
{				
	$instance_focus = mysql_query("SELECT * FROM ".$slrp_prefix."focus WHERE focus_table = '$group_name'") or die ("failed getting instance focus id.");
	$instfc = mysql_fetch_assoc($instance_focus);
	
	$instance = mysql_query("SELECT ".$instance_name."_desc FROM ".$slrp_prefix.$instance_name." WHERE ".$instance_name."_id = '".$getinst[$instance_name.'_id']."'") or die ("failed getting group instance desc.");
	$inst = mysql_fetch_assoc($instance);
	
	echo"
	<tr>
		<td width = '49%' valign = 'top' align = 'right' colspan='4'>
			<table width = '100%' border='0'>
	";

	
	echo"
				<tr>
					<td width = '100%' colspan = '3' align = 'right'>
					<font class='heading2'>
	";
	
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		echo"(".$getinst[$instance_name.'_id'].") ";
	}

	echo"<a href='modules.php?name=$module_name&file=obj_edit&expander_abbr=$expander_abbr&current_focus_id=30&culture=".$getinst[$instance_name.'_id']."' class='content2'>$getinst[$instance_name]</a><br><i><font color = 'white' size = '1'>";
		
	$get_group_subtypes = mysql_query("SELECT * FROM ".$slrp_prefix.$group_name."_subtype INNER JOIN ".$slrp_prefix.$group_name."_".$group_name."_subtype ON ".$slrp_prefix.$group_name."_".$group_name."_subtype.".$group_name."_subtype_id = ".$slrp_prefix.$group_name."_subtype.".$group_name."_subtype_id WHERE ".$slrp_prefix.$group_name."_".$group_name."_subtype.".$group_name."_id = '".$getinst[$instance_name.'_id']."' AND ".$slrp_prefix.$group_name."_subtype.".$group_name."_subtype_min_rank >= '$curusrslrprnk[slurp_rank_id]' AND ".$slrp_prefix.$group_name."_subtype.".$group_name."_subtype_status_id >= '4' AND ".$slrp_prefix.$group_name."_subtype.".$group_name."_subtype_slurp_id = '$slrpnfo[slurp_id]' ORDER BY ".$group_name."_subtype") or die("failed to get ".$expander_abbr." subtypes.");
	while($getgrpsubtyp = mysql_fetch_assoc($get_group_subtypes))
	{
		$group_subtype = mysql_query("SELECT ".$group_name."_subtype_desc FROM ".$slrp_prefix.$group_name."_subtype WHERE ".$group_name."_subtype_id = '".$getgrpsubtyp[$group_name.'_subtype_id']."'") or die ("failed getting group subtype desc.");
		$grpsbtyp = mysql_fetch_assoc($group_subtype);	
		
		echo $getgrpsubtyp[$group_name.'_subtype'];
		
		if($curusrslrprnk[slurp_rank_id] <= 4)
		{
			echo"<br>(".$getgrpsubtyp[$group_name.'_subtype_id'].")";
		}
	}
	
	echo" </i> </font>
					</font>
					</td>
				</tr>
	";
	
	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		$get_members = mysql_query("SELECT * FROM ".$slrp_prefix."creature_culture WHERE ".$slrp_prefix."creature_culture.culture_id = '".$getinst[$instance_name.'_id']."' AND (".$slrp_prefix."creature_culture.culture_tolerance_id = '10' OR ".$slrp_prefix."creature_culture.culture_tolerance_id = '11')") or die ("failed getting membership.");
		while($gtmmbrs = mysql_fetch_assoc($get_members))
		{
			$member_info = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id = '$gtmmbrs[creature_id]' AND creature_status_id = '4' AND creature_min_rank >= '$curusrslrprnk[slurp_rank_id]'") or die ("Failed geting culture members.");
			$mmbrnfocnt = mysql_num_rows($member_info);
			if($mmbrnfocnt == 1)
			{
				$mmbrnfo = mysql_fetch_assoc($member_info);
						
				$relation_info = mysql_query("SELECT * FROM ".$slrp_prefix."culture_tolerance WHERE culture_tolerance_id = '$gtmmbrs[culture_tolerance_id]'") or die ("Failed geting culture member relation.");
				$rltnnfo = mysql_fetch_assoc($relation_info);
				
				echo"
				<tr>
					<td width = '49%' align = 'left'>
						<font color= 'orange' size = '2'>$rltnnfo[culture_tolerance]</font>
					</td>
					</td>
					<td width = '2%'>
					</td>
					<td width = '49%' align = 'right'>
					<a href = 'modules.php?name=$module_name&file=pc_edit_new&current_pc_id=$mmbrnfo[creature_id]&ntro_expander=char' class='default'>$mmbrnfo[creature]'</a>
					</td>
					</form>
				</tr>
				";
			}
		}
	}
	
	echo"
			</table>
		</td>
		<td width = '2%'>
		</td>
		<td width = '5%' valign='top'>
		";			
		// graphic handler for all objects
		$current_focus_id = $instfc[focus_id];
		$current_object_id = $getinst[$instance_name.'_id'];
		$dressed=0;
		include("modules/$module_name/includes/fm_obj_graphic.php");
		echo"</td>
		<td width = '2%'>
		</td>
	";
	
	echo"		
		<td width = '49%' valign = 'top' align = 'left'>
			<table width = '100%' border='0'>
	";
	
	$get_tolerated_list = mysql_query("SELECT * FROM ".$slrp_prefix."culture_culture_tolerance INNER JOIN ".$slrp_prefix."culture_tolerance ON ".$slrp_prefix."culture_tolerance.culture_tolerance_id = ".$slrp_prefix."culture_culture_tolerance.culture_tolerance_id WHERE ".$slrp_prefix."culture_culture_tolerance.culture_id = '".$getinst[$instance_name.'_id']."' ORDER BY ".$slrp_prefix."culture_tolerance.culture_tolerance") or die ("failed getting tolerated list.");
	$gettoldlstcnt = mysql_num_rows($get_tolerated_list);
	while($gettoldlst = mysql_fetch_assoc($get_tolerated_list))
	{
		$tolerated_cultures = mysql_query("SELECT * FROM ".$slrp_prefix."culture WHERE culture_id = '$gettoldlst[tolerates_culture_id]' AND culture_status_id = '4' AND culture_min_rank >= '$curusrslrprnk[slurp_rank_id]'") or die ("Failed getting culture related.");
		$tolcultcnt = mysql_num_rows($tolerated_cultures);
		if($tolcultcnt == 1)
		{
			$tolcult = mysql_fetch_assoc($tolerated_cultures);
	
			$get_tolerances_list = mysql_query("SELECT * FROM ".$slrp_prefix."culture_tolerance WHERE culture_tolerance_id = '$gettoldlst[culture_tolerance_id]'") or die ("failed getting tolerance info.");
			$gettolslst = mysql_fetch_assoc($get_tolerances_list);
			echo"
			<tr>
				<td width = '49%' align = 'right'>
					<font size = '2' color = 'orange'>
					$gettolslst[culture_tolerance]
					</font>
				</td>
				<td width = '2%'>
				</td>				
				<td width = '49%' align = 'left'>
					<a href='modules.php?name=$module_name&file=obj_edit&expander_abbr=$expander_abbr&current_focus_id=30&culture=$tolcult[culture_id]' class='content2'>$tolcult[culture]</a>
				</td>									
				</form>
			</tr>
			";
		}
	}
	
	echo"
			</table>
		</td>
	</tr>

	<tr background='themes/$ThemeSel/images/back11bottom.gif' height='9'>
		<td colspan='9'>
		</td>
	</tr> 
	";
}

echo"
<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
	<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
	<td valign='middle' align='left' colspan='9'>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_creature_id'>
		<input type='hidden' value='1' name='".$expander_abbr."_expander'>
		<input class='submit3' type='submit' value='Back to Main' name='go_home'>
	</td>
	</form>
</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>