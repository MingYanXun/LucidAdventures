<?php
if (!eregi("modules.php", $PHP_SELF)) 
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("header.php");
//$_POST['current_pc_id'] = 1;
$nav_title = "My Dominion Main";
include("modules/$module_name/includes/slurp_header.php");

if(isset($_POST['current_pc_id']))
{
	$current_pc_id = $_POST['current_pc_id'];
}
else
{
	$current_pc_id = 1;
}

echo"
<tr>
<td colspan = '9' align = 'left' valign = 'top'>
<font color = 'yellow'>
";

if($ntro_expander == 1)
{
	echo"<b>
	<li> Choose which version of your character to view.
	<hr>
	";
}

echo"
<form name = 'pc_2008' method = 'post' action = 'modules.php?name=$module_name&file=pc_edit'>
<input type='hidden' value='2008' name='2008_char'>
<input type='hidden' value='$current_pc_id' name='current_pc_id'>
<input type='submit' value='View 2008 format' name='pc_2008'>
</form>
";


if($curusrslrprnk[0] <= 8)
{
echo"<form name = 'pc_2009' method = 'post' action = 'modules.php?name=$module_name&file=pc_edit_new'>";
echo"
<hr>
<input type='hidden' value='2009' name='2009_char'>
<input type='hidden' value='$current_pc_id' name='current_pc_id'>
<input type='submit' value='View 2009 format' name='pc_2009'>
";
echo"</form>";
}

echo"
</font>
</b>
</td>

</tr>
";



require("modules/$module_name/includes/slurp_footer.php");
include("footer.php");
?>