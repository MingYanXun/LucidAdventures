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
				<td valign = 'top' align = 'left' colspan = '3'>
					<font size = '3' color = 'black'>
					<b>Event Name: $current_evnt_name</b>: <font color = 'blue'>$gtevnt[event_date]</font>
					</font>
				</td>
			</tr>
	";
	// end table holding the main ability info
}

//end top row
//start tracking list

$active_char_count = 0;

$get_active_characters = mysql_query("SELECT * FROM ".$slrp_prefix."creature INNER JOIN ".$slrp_prefix."creature_game_note ON ".$slrp_prefix."creature_game_note.creature_id = ".$slrp_prefix."creature.creature_id WHERE ".$slrp_prefix."creature.creature_status_id = '4' AND ".$slrp_prefix."creature_game_note.event_id = '$evlgame[event_id]' ORDER BY ".$slrp_prefix."creature.creature") or die ("failed getting pc dt list.");
while($gtactvchar = mysql_fetch_assoc($get_active_characters))
{
	$get_active_char_player = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$gtactvchar[creature_nuke_user_id]'");
	$gtactvcharplyr = mysql_fetch_assoc($get_active_char_player);
	
	if($active_char_count == 0)
	{
		echo"
			<tr>
				<td valign = 'top' colspan = '3' align = 'center'>
					<hr>
				</td>
			</tr>
			<tr>
				<td valign = 'top' width = '14%' align = 'left'>
					<font size = '3' color = 'blue'>
					Character
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' width = '85%' align = 'left'>
					<font size = '3' color = 'blue'>
					Downtimes from <i>$evlgame[event_date]</i> to <i>$gtevnt[event_date]</i>
					</font>
				</td>
			</tr>
		";
	}
	
	$get_recent_records = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note WHERE creature_id = '$gtactvchar[creature_id]' AND event_id = '$evlgame[event_id]' ORDER BY date_submitted") or die ("failed getting recent records.");
	
	echo"
			<tr>
				<td valign = 'top' colspan = '3' align = 'center'>
					<hr>
				</td>
			</tr>
			<tr>
				<td valign = 'top' width = '100%' align = 'left' colspan ='3'>
					<font size = '3' color = 'black'><b>$gtactvchar[creature]</b> (<font size = '1' color = 'blue'>$gtactvcharplyr[username]
					</font>)</font>
				</td>
			</tr>
	";
	
	$gtrecrecs = mysql_fetch_assoc($get_recent_records);
	// {
		$get_rec_user = mysql_query("SELECT * FROM nuke_users WHERE user_id > '1' AND user_id = '$gtrecrecs[user_id]'") or die ("failed getting rec rec user.");
		$gtrcusr = mysql_fetch_assoc($get_rec_user);
		
		if($gtrecrecs[who_met] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					People Met: 
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[who_met]
					</font>
				</td>
			</tr>
			";
		}
		if($gtrecrecs[during] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					What Happened: 
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[during]
					</font>
				</td>
			</tr>
			";
		}
		if($gtrecrecs[modules_attended] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					Modules: 
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[modules_attended]
					</font>
				</td>
			</tr>
			";
		}
		if($gtrecrecs[goals_completed] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					Goals Completed: 
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[goals_completed]
					</font>
				</td>
			</tr>
			";
		}
		if($gtrecrecs[goals_set] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					New Goals: 
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[goals_set]
					</font>
				</td>
			</tr>
			";
		}
		if($gtrecrecs[equipment_acquired] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					Equipment Acquired:
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[equipment_acquired]
					</font>
				</td>
			</tr>
			";
		}
		if($gtrecrecs[equipment_desired] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					Equipment Desired:
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[equipment_desired]
					</font>
				</td>
			</tr>
			";
		}
		if($gtrecrecs[module_ideas] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					Ideas for Plot: 
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[module_ideas]
					</font>
				</td>
			</tr>
			";
		}
		if($gtrecrecs[rules] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					Rules and Corrections:
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[rules]
					</font>
				</td>
			</tr>
			";
		}
		if($gtrecrecs[staff_stuff] != "")
		{
			echo"
			<tr>
				<td valign = 'top' width = '14%' align = 'right'>
					<font size = '3' color = 'black'>
					Comments:
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td  valign = 'top' width = 85%' align = 'left'>
					<font size = '3' color = 'blue'>
					$gtrecrecs[staff_stuff]
					</font>
				</td>
			</tr>
			";
		}

	$active_char_count++;
	if($active_char_count == 5)
	{
		$active_char_count = 0;
	}
}

// include("modules/$module_name/includes/slurp_footer.php");
// require("footer.php");
?>