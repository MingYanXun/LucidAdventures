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

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	$wide_align = "right";
}

if($curusrslrprnk[slurp_rank_id] >= 5)
{
	$wide_align = "left";
}

// end message table at top, and its row.
$get_events = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id = '$current_evnt_id'");
$gtevnt = mysql_fetch_assoc($get_events);

$event_next_game = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id = '$gtevnt[event_id]'") or die ("failed getting next event info");
$evngame = mysql_fetch_assoc($event_next_game);
$event_last_game = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_next_id = '$gtevnt[event_id]'") or die ("failed getting last event info");
$evlgame = mysql_fetch_assoc($event_last_game);

$current_evnt_name = strip_tags(stripslashes($gtevnt[event]));
$next_evnt_name = strip_tags(stripslashes($evngame[event]));
$last_evnt_name = strip_tags(stripslashes($evlgame[event]));
//the row that holds messages at the top

// get info on the event


// start a row to hold the main content, and a cell 5/6 of the screen wide, to leave the rest as a sidebar
// also start a table in the cell; it wil be  number of columns equal to the values set by rank at the beginning

if($current_evnt_id >= 2);
{
	echo"
	<tr>
				<td valign = 'top' width = '27%' align = 'left' colspan ='5'>
					<font size = '3' color = 'black'>
					<font size = '2'>Last Game: $last_evnt_name<br></font>
					<b>Event Name: $current_evnt_name</b><br>
					<font size = '2'>Next Game: $next_evnt_name</font>
					<br>
					<br>
					<font color = 'blue'>$gtevnt[event_date]</font>
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = '72%' align = 'left' colspan = '3'>
					<font size = '2' color = 'blue'>
					</font>
	";

	// start the main info boxes and form
	echo"
";
	
	$current_event_type = mysql_query("SELECT * FROM ".$slrp_prefix."event_type WHERE ".$slrp_prefix."event_type.event_type_id = '$gtevnt[event_type_id]'") or die ("failed getting current event type.");
	$currevnttyp = mysql_fetch_assoc($current_event_type);
	$currevnttypcnt = mysql_num_rows($current_event_type);
		
	if($currevnttypcnt >= 1)
	{
		echo"Event Type: $currevnttyp[event_type]";
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

$get_active_characters = mysql_query("SELECT * FROM ".$slrp_prefix."creature INNER JOIN nuke_users ON ".$slrp_prefix."creature.creature_nuke_user_id = nuke_users.user_id WHERE nuke_users.user_id > '1' AND ".$slrp_prefix."creature.creature_status_id = '4' ORDER BY nuke_users.username,".$slrp_prefix."creature.creature") or die ("failed getting pc list.");
while($gtactvchar = mysql_fetch_assoc($get_active_characters))
{
	$get_active_char_player = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$gtactvchar[creature_nuke_user_id]'");
	$gtactvcharplyr = mysql_fetch_assoc($get_active_char_player);
	
	if($active_char_count == 0)
	{
		echo"
			<tr>
				<td valign = 'top' colspan = '11' align = 'center'>
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
					Att.<br>(+$slrpnfo[slurp_xp_reason_1] XP)
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					Cbn.<br>(+$slrpnfo[slurp_xp_reason_2] XP)
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					Svc.<br>(+$slrpnfo[slurp_xp_reason_3] XP)
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					DT<br>(+$slrpnfo[slurp_xp_reason_4] XP)
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '55%' align = 'left'>
					<font size = '3' color = 'blue'>
					Changes from <i>$evlgame[event_date]</i> to <i>$gtevnt[event_date]</i>
					</font>
				</td>
			</tr>
		";
	}
	
	$get_recent_records = mysql_query("SELECT * FROM ".$slrp_prefix."creature_xp_log WHERE creature_id = '$gtactvchar[creature_id]' AND timestamp >= '$evlgame[event_date]' AND timestamp <= '$gtevnt[event_date]' ORDER BY timestamp") or die ("failed getting recent records.");
	
	echo"
			<tr>
				<td valign = 'top' colspan = '11' align = 'center'>
					<hr>
				</td>
			</tr>
			<tr>
				<td valign = 'top' width = '9%' align = 'left'>
					<font size = '3' color = 'black'>
					$gtactvchar[creature]<br>
					<font size = '1' color = 'blue'>
					$gtactvcharplyr[username]
					</font>
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					<input type = 'checkbox' name = '$gtevnt[event_id]_$gtactvchar[creature_id]_att' value = '1'
					";
					
					if($gtactvchar[creature_earned_xp_1] == 1)
					{
						echo"checked";
					}
					
					echo">
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					<input type = 'checkbox' name = '$gtevnt[event_id]_$gtactvchar[creature_id]_cbn' value = '1'
					";
					
					if($gtactvchar[creature_earned_xp_2] == 1)
					{
						echo"checked";
					}
					
					echo">
					</font>
				</td>
				<td  valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					<input type = 'checkbox' name = '$gtevnt[event_id]_$gtactvchar[creature_id]_svc' value = '1'
					";
					
					if($gtactvchar[creature_earned_xp_3] == 1)
					{
						echo"checked";
					}
					
					echo">
					</font>
				</td>
				<td  valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = '5%' align = 'left'>
					<font size = '3' color = 'blue'>
					<input type = 'checkbox' name = '$gtevnt[event_id]_$gtactvchar[creature_id]_dt' value = '1'
					";
					
					if($gtactvchar[creature_earned_xp_4] == 1)
					{
						echo"checked";
					}
					
					echo">
					</font>
				</td>
				<td  valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 55%' align = 'left'>
					<font size = '3' color = 'blue'>
	";
	
	while($gtrecrecs = mysql_fetch_assoc($get_recent_records))
	{
		$get_rec_user = mysql_query("SELECT * FROM nuke_users WHERE user_id > '1' AND user_id = '$gtrecrecs[user_id]'") or die ("failed getting rec rec user.");
		$gtrcusr = mysql_fetch_assoc($get_rec_user);
		
		echo"On $gtrecrecs[timestamp] $gtrcusr[username] $gtrecrecs[reason]<br>";
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