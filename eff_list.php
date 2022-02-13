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
include("modules/$module_name/includes/slurp_header.php");

// checkbox variables for the index
if(isset($_POST['current_expander']))
{
	$expander_abbr = $_POST['current_expander'];
	$expander = ($expander_abbr."_expander");
}
// echo"exp: $expander_abbr, $expander<br>";

echo"
<table width = '100%'>
<tr>

<td colspan = '5'>
Materials are used to create Items, and have varying quality denoted by Tiers. Materials are grouped into types (like Metals) and subtypes (like Precious Metals). Each subtype fits into one or more Material Tiers, and also contains individual Materials. Abilities both in Items and in use by Characters may require any subtype, individual, or type of Material(s) to produce a given effect. 
</td>

</tr>
<form name = 'production_subtype' method='post' action = 'modules.php?name=$module_name&file=mat_list_form'>
";

$get_material_types = mysql_query("SELECT * FROM dom_material_type WHERE material_type_id > '1' ORDER BY material_type") or die("failed to get mat types.");
while($getmattyp = mysql_fetch_array($get_material_types, MYSQL_NUM))
{
	echo"
	<tr>

	<td colspan = '5'>
	<hr width = '100%'>
	</td>

	</tr>
	<tr>
	
	<td colspan = '5'>
	<font color = 'yellow'>
	<li>$getmattyp[1]
	</font>
	</td>
	
	</tr>
	";

	$get_material_subtypes = mysql_query("SELECT * FROM dom_material_subtype INNER JOIN dom_material_subtype_type ON dom_material_subtype_type.material_subtype_id = dom_material_subtype.material_subtype_id WHERE dom_material_subtype_type.material_type_id = '$getmattyp[0]' ORDER BY material_subtype") or die("failed to get mat subtypes.");
	while($getmatsubtyp = mysql_fetch_array($get_material_subtypes, MYSQL_NUM))
	{
		echo"
		<tr>
		
		<td width = '3%'>
		</td>
		
		<td width = '67%' colspan = '2'>
		<font color = '#7fffd4'>
		* $getmatsubtyp[1]
		</font>
		</td>
		
		<td width = '2%'>
		</td>
		
		<td width = '28%'>
		<font color = '#7fffd4'>
		<input type='checkbox' value='$getmatsubtyp[0]' name='$getmatsubtyp[0]_required'>
		<input type='hidden' value='51' name='$getmatsubtyp[0]_focus_exclusion_id'>
		<input type='hidden' value='25' name='$getmatsubtyp[0]_focus_id'>
		Add to Production		
		</font>
		</td>
		
		</tr>
		";
		
		$get_materials = mysql_query("SELECT * FROM dom_material INNER JOIN dom_material_material_subtype ON dom_material_material_subtype.material_id = dom_material.material_id WHERE dom_material_material_subtype.material_subtype_id = '$getmatsubtyp[0]' ORDER BY material") or die("failed to get materials.");
		while($getmat = mysql_fetch_array($get_materials, MYSQL_NUM))
		{
			echo"
			<tr>
			
			<td width = '3%'>
			</td>
			
			<td width = '3%'>
			</td>
			
			<td width = '94%' colspan = '3'>
			<font color = 'white'>
			~ $getmat[1]
			</font>
			</td>
			
			</tr>
			";
		}
	}
}

echo"
</tr>
<tr>

<td colspan = '5'>
<hr width = '100%'>
</td>

</tr>
<tr>

<td colspan = '5'>
<input type='hidden' value='$curpcnfo[0]' name='current_pc_id'>
<input type='hidden' value='$expander_abbr' name='current_expander'>
<input type='submit' value='Require for Ability' name='production_subtype'>
</form>
</td>

</tr>
<tr>

<td>
<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
<input type='hidden' value='$curpcnfo[0]' name='current_pc_id'>
<input type='hidden' value='1' name='".$expander_abbr."_expander'>
<input type='submit' value='Back to Main' name='go_home'>
</form>
</td>

<td width = '2%' align = 'left' valign = 'top'>
</td>

<td align = 'left' valign = 'top'>
</td>

<td width = '2%' align = 'left' valign = 'top'>
</td>

<td align = 'left' valign = 'top'>
</td>

</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>