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
include("modules/$module_name/includes/slurp_header.php");

if(isset($_POST['ntro_expander']))
{
	$ntro_expander = $_POST['ntro_expander'];
}
else
{
	$ntro_expander = 1;
}

if(isset($_POST['xp']))
{
	$xp_change = $_POST['xp'];
	
	if(empty($_POST['reason']))
	{
		echo"
		You did not enter a reason for adding XP manually.
		<br>
		<br>
		";
	}
	else
	{
		$new_xp_total = ($curpcnfo[19]+$xp_change);
		$user_reason = $_POST['reason'];
		$reason = ("Added ".$xp_change." XP. ".$user_reason." . . . New XP total: ".$new_xp_total);
		
		$add_xp = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_xp_current=creature_xp_current+'$xp_change',creature_xp_earned=creature_xp_earned+'$xp_change' WHERE creature_id = '$curpcnfo[creature_id]'");
		
		// echo"$pc_status, $reason, $usrnfo[1], $curpcnfo[1]<br>";
		$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','$xp_change','$usrnfo[user_id]','$reason')") or die ("failed adding character submission to xp log.");
	}
}

echo"
<tr>

<td width = '18%' align = 'left' valign = 'top'>
<font color ='yellow'>
OCCURRED
</td>
<td width = '2%' align = 'left' valign = 'top'>
</td>
<td width = '18%' align = 'left' valign = 'top'>
<font color ='yellow'>
USER
</td>
<td width = '2%' align = 'left' valign = 'top'>
</td>
<td width = '60% align = 'left' valign = 'top' colspan = '5'>
<font color ='yellow'>
DOWNTIME NOTES
</td>

</tr>
<tr>

<td colspan = '3'>
<font color ='white'>
Description of action taken. (<font color='orange'>orange is the most recent</font>).
</td>

</tr>
<tr>

<td colspan = '9'>
<hr width = '100%'>
</td>

</tr>
<tr>

<td colspan = '3'>

<table width = '100%'>
";

$report_xp_log = mysql_query("SELECT * FROM ".$slrp_prefix."creature_xp_log WHERE creature_id = '$curpcnfo[creature_id]' ORDER BY timestamp DESC") or die ("failed getting xp log.");
$rptxplogcnt = mysql_num_rows($report_xp_log);
$rptxplogcntr = $rptxplogcnt;
while($rptxplog = mysql_fetch_assoc($report_xp_log))
{
	
	$get_log_user_entries = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$rptxplog[user_id]'") or die("failed getting report user entries.");
	$userentry = mysql_fetch_assoc($get_log_user_entries);
	
	echo"
	<tr>

	<td width = '49%' align = 'left' valign = 'top'>
	<font size = '1'>
	<font color= 'yellow'>
	$rptxplog[timestamp]
	</td>
	<td width = '2%' align = 'left' valign = 'top'>
	</td>
	<td width = '49%' align = 'left' valign = 'top'>
	<font size = '1'>
	<font color= 'yellow'>
	$userentry[xp_value] ($userentry[creature_id])
	</td>

	</tr>
	<tr>

	<td colspan = '3' align = 'left' valign = 'top'>	
	";
	
	if($rptxplogcntr == $rptxplogcnt)
	{
		echo"
		<font color= 'orange'>
		";
	}
	else
	{
		echo"
		<font color= 'white'>
		";
	}
	
	echo"
	$rptxplog[reason]
	</td>

	</tr>
	<tr>

	<td colspan = '3' align = 'left' valign = 'top'>	
	<hr>
	</td>

	</tr>
	";
	
	$rptxplogcntr--;
}

echo"
</table>

</td>

</tr>
<tr>

<td colspan = '9'>
<hr width = '100%'>
<form name = 'back_to_pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
<input type='hidden' value='$curpcnfo[creature]' name='current_pc_id'>
<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
<input type='submit' value='Back to View/Edit' name='back_to_pc_edit_new'>
</form>
</td>

</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
?>