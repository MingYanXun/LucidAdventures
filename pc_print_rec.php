<?php
if (!eregi("modules.php", $PHP_SELF))
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("modules/$module_name/includes/slurp_min_header.php");

echo"
<tr>
	<td width = '100%' align = 'left' valign = 'top'>
		
";

if(isset($_POST['pc_to_print']))
{
	$pc_temp = $_POST['pc_to_print'];
	}
	if(isset($_GET['pc_to_print']))
	{
		$pc_temp = $_GET['pc_to_print'];
	}
include("modules/$module_name/includes/pcinfo.php");

$listcount = $total_recoveries;
$rowcount = 0;
$colcount = 0;
// start character sheet
// this table (below) holds all the main content on top
echo"
<table cellpadding = '3' width = '100%' border='1' cellspacing='0'>
";

while($listcount >= 1)
{
	if($colcount == 0)
	{
		echo"<tr>";
	}
	
	echo"
	<form>
	<td width='50%' align='center' valign='top'>	
	";
	
	if($listcount == $total_recoveries)
	{
		if($colcount == 0)
		{
			echo"	<input type='button' onClick='window.print()'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
	}		;
	
	$colcount++;
	$listcount--;		
	
	echo"
		Recovery Sheet for $curpcnfo[creature]
		</form>
				<table cellpadding = '2' width = '100%' border='1' cellspacing='0'>
			";
		
			// echo"recoveries<br>";
			$recovering_pc_id = $curpcnfo[creature_id];
			include("modules/$module_name/includes/fn_pc_recpage_print.php");
			
			echo"
				</table>		
	</td>
	";
	
	if($colcount == 2)	
	{
		echo"</tr>";
		$colcount = 0;
	}
}

echo"
</table>
";	

// include("modules/$module_name/includes/slurp_footer.php");
// require("footer.php");
?>