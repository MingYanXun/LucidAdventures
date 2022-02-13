<?php
if (!eregi("modules.php", $PHP_SELF))
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("modules/$module_name/includes/slurp_min_header_no_border.php");

if(isset($_POST['current_evnt_id']))
{
	$current_evnt_id = $_POST['current_evnt_id'];
}

if(isset($_POST['edit_evnt_type']))
{
	$edit_evnt_type = $_POST['edit_evnt_type'];
	$update_event_type = mysql_query("UPDATE ".$slrp_prefix."event SET event_type_id = '$edit_evnt_type' WHERE event_id = '$current_evnt_id'") or die ("failed setting evnt_type.");
}

$get_focus_name = "event";

if($curusrslrprnk[0] <= 4)
{
	$wide_align = "right";
}

if($curusrslrprnk[0] >= 5)
{
	$wide_align = "left";
}

// end message table at top, and its row.
$get_events = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id = '$current_evnt_id'");
$gtevnt = mysql_fetch_array($get_events, MYSQL_NUM);

$event_next_game = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id = '$gtevnt[6]'") or die ("failed getting next event info");
$evngame = mysql_fetch_array($event_next_game, MYSQL_NUM);
$event_last_game = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_next_id = '$gtevnt[0]'") or die ("failed getting last event info");
$evlgame = mysql_fetch_array($event_last_game, MYSQL_NUM);

$current_evnt_name = strip_tags(stripslashes($gtevnt[1]));
$next_evnt_name = strip_tags(stripslashes($evngame[1]));
$last_evnt_name = strip_tags(stripslashes($evlgame[1]));
//the row that holds messages at the top

// get info on the event


// start a row to hold the main content, and a cell 5/6 of the screen wide, to leave the rest as a sidebar
// also start a table in the cell; it wil be  number of columns equal to the values set by rank at the beginning

if($current_evnt_id >= 2);
{
	echo"
	<tr>
				<td valign = 'top' width = '27%' align = 'left' colspan ='7'>
					<font size = '3' color = 'black'>
					<font size = '2'>Last Game: $last_evnt_name<br></font>
					<b>Event Name: $current_evnt_name</b><br>
					<font size = '2'>Next Game: $next_evnt_name</font>
					<br>
					<br>
					<font color = 'blue'>$gtevnt[3]</font>
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = '72%' align = 'left'>
					<font size = '2' color = 'blue'>
					</font>
	";

	// start the main info boxes and form
	echo"
";
	
	$current_event_type = mysql_query("SELECT * FROM ".$slrp_prefix."event_type WHERE ".$slrp_prefix."event_type.event_type_id = '$gtevnt[2]'") or die ("failed getting current event type.");
	$currevnttyp = mysql_fetch_array($current_event_type, MYSQL_NUM);
	$currevnttypcnt = mysql_num_rows($current_event_type);
		
	if($currevnttypcnt >= 1)
	{
		echo"Event Type: $currevnttyp[1]";
	}
	
	echo"
				</td>
			</tr>
	";
	// end table holding the main ability info
}

//end top row
//start tracking list

$active_char_count = 0;

$get_active_characters = mysql_query("SELECT * FROM ".$slrp_prefix."pc INNER JOIN nuke_users ON ".$slrp_prefix."pc.pc_nuke_user_id = nuke_users.user_id WHERE nuke_users.user_id > '1' AND ".$slrp_prefix."pc.pc_status_id = '4' ORDER BY nuke_users.username,dom_pc.pc") or die ("failed getting pc list.");
while($gtactvchar = mysql_fetch_array($get_active_characters, MYSQL_NUM))
{
	$get_active_char_player = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$gtactvchar[4]'");
	$gtactvcharplyr = mysql_fetch_array($get_active_char_player, MYSQL_NUM);
	
	if($active_char_count == 0)
	{
		echo"
			<tr>
				<td valign = 'top' colspan = '9' align = 'center'>
					<hr>
				</td>
			</tr>
			<tr>
				<td valign = 'top' width = '9%' align = 'left'>
					<font size = '3' color = 'blue'>
					Player
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					Att.<br>(+$slrpnfo[23] XP)
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					Cbn.<br>(+$slrpnfo[24] XP)
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					Svc.<br>(+$slrpnfo[25] XP)
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '72%' align = 'left'>
					<font size = '3' color = 'blue'>
					Changes from <i>$evlgame[3]</i> to <i>$gtevnt[3]</i>
					</font>
				</td>
			</tr>
		";
	}
	
	$get_recent_records = mysql_query("SELECT * FROM ".$slrp_prefix."pc_xp_log WHERE pc_id = '$gtactvchar[0]' AND timestamp >= '$evlgame[3]' AND timestamp <= '$gtevnt[3]' ORDER BY timestamp") or die ("failed getting recent records.");

	echo"
			<tr>
				<td valign = 'top' colspan = '9' align = 'center'>
					<hr>
				</td>
			</tr>
			<tr>
				<td valign = 'top' width = '9%' align = 'left'>
					<font size = '3' color = 'black'>
					$gtactvchar[1]<br>
					<font size = '1' color = 'blue'>
					$gtactvcharplyr[1]
					</font>
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					<input type = 'checkbox' name = '$gtevnt[0]_$gtactvchar[0]_att' value = '1'>
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					<input type = 'checkbox' name = '$gtevnt[0]_$gtactvchar[0]_cbn' value = '1'>
					</font>
				</td>
				<td  valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					<input type = 'checkbox' name = '$gtevnt[0]_$gtactvchar[0]_svc' value = '1'>
					</font>
				</td>
				<td  valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 72%' align = 'left'>
					<font size = '3' color = 'blue'>
	";
	
	while($gtrecrecs = mysql_fetch_array($get_recent_records, MYSQL_NUM))
	{
		$get_rec_user = mysql_query("SELECT * FROM nuke_users WHERE user_id > '1' AND user_id = '$gtrecrecs[3]'") or die ("failed getting rec rec user.");
		$gtrcusr = mysql_fetch_array($get_rec_user, MYSQL_NUM);
		
		echo"On $gtrecrecs[5] $gtrcusr[1] $gtrecrecs[4]<br>";
	}

	echo"
					</font>
				</td>
			</tr>
	";

	$active_char_count++;
	if($active_char_count == 5)
	{
		$active_char_count = 0;
	}
}

// include("modules/$module_name/includes/slurp_footer.php");
// require("footer.php");
?>