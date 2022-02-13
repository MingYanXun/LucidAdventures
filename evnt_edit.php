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
$nav_title = "View Event";
include("modules/$module_name/includes/slurp_header.php");

if(isset($_POST['current_expander']))
{
	$expander_abbr = $_POST['current_expander'];
	$expander = ($expander_abbr."_expander");
}
// echo"exp: $expander_abbr, $expander<br>";

if(isset($_POST['newevntname']))
{
	$newevntname = $_POST['newevntname'];
	$new_evnt_type = $_POST['new_evnt_type'];
	$new_evnt_year = $_POST['new_evnt_year'];
	$new_evnt_month = $_POST['new_evnt_month'];
	$new_evnt_day = $_POST['new_evnt_day'];
	$new_evnt_hour = $_POST['new_evnt_hour'];
	$new_evnt_minute = $_POST['new_evnt_minute'];
	
	$end_evnt_year = $_POST['end_evnt_year'];
	$end_evnt_month = $_POST['end_evnt_month'];
	$end_evnt_day = $_POST['end_evnt_day'];
	$end_evnt_hour = $_POST['end_evnt_hour'];
	$end_evnt_minute = $_POST['end_evnt_minute'];
	
	$dt_evnt_year = $_POST['dt_evnt_year'];
	$dt_evnt_month = $_POST['dt_evnt_month'];
	$dt_evnt_day = $_POST['dt_evnt_day'];
	$dt_evnt_hour = $_POST['dt_evnt_hour'];
	$dt_evnt_minute = $_POST['dt_evnt_minute'];
	
	$lock_evnt_year = $_POST['lock_evnt_year'];
	$lock_evnt_month = $_POST['lock_evnt_month'];
	$lock_evnt_day = $_POST['lock_evnt_day'];
	$lock_evnt_hour = $_POST['lock_evnt_hour'];
	$lock_evnt_minute = $_POST['lock_evnt_minute'];
	
	$reset_evnt_year = $_POST['reset_evnt_year'];
	$reset_evnt_month = $_POST['reset_evnt_month'];
	$reset_evnt_day = $_POST['reset_evnt_day'];
	$reset_evnt_hour = $_POST['reset_evnt_hour'];
	$reset_evnt_minute = $_POST['reset_evnt_minute'];
	
	$new_evnt_date = $new_evnt_year."-".$new_evnt_month."-".$new_evnt_day." ".$new_evnt_hour.":".$new_evnt_minute.":00";
	$end_evnt_date = $end_evnt_year."-".$end_evnt_month."-".$end_evnt_day." ".$end_evnt_hour.":".$end_evnt_minute.":00";
	$dt_evnt_date = $dt_evnt_year."-".$dt_evnt_month."-".$dt_evnt_day." ".$dt_evnt_hour.":".$dt_evnt_minute.":00";
	$lock_evnt_date = $lock_evnt_year."-".$lock_evnt_month."-".$lock_evnt_day." ".$lock_evnt_hour.":".$lock_evnt_minute.":00";
	$reset_evnt_date = $reset_evnt_year."-".$reset_evnt_month."-".$reset_evnt_day." ".$reset_evnt_hour.":".$reset_evnt_minute.":00";
	
	$new_evnt = mysql_query("INSERT INTO ".$slrp_prefix."event (event,event_type_id,event_date,event_end,event_downtime_cutoff_date,event_database_lock_date,event_reset,event_slurp_id,event_xp_date) VALUES ('$newevntname','$new_evnt_type','$new_evnt_date','$end_evnt_date','$dt_evnt_date','$lock_evnt_date','$reset_evnt_date','$slrpnfo[slurp_id]','$reset_evnt_date')") or die ("failed inserting new event.");
	$new_events = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event = '$newevntname' AND event_type_id = '$new_evnt_type'") or die ("failed verifying new event");
	$nwevnt = mysql_fetch_assoc($new_events);
	$current_evnt_id = $nwevnt[event_id];
}

if(isset($_POST['current_evnt_id']))
{
	$current_evnt_id = $_POST['current_evnt_id'];
}

if(isset($_POST['min_rank']))
{
	$min_rank = $_POST['min_rank'];
	$update_event_rank = mysql_query("UPDATE ".$slrp_prefix."event SET event_min_rank = '$min_rank' WHERE event_id = '$current_evnt_id'") or die ("failed setting min rank.");
}

if(isset($_POST['edit_evnt_type']))
{
	$edit_evnt_type = $_POST['edit_evnt_type'];
	$update_event_type = mysql_query("UPDATE ".$slrp_prefix."event SET event_type_id = '$edit_evnt_type' WHERE event_id = '$current_evnt_id'") or die ("failed setting evnt_type.");
}

if(isset($_POST['next_evnt_id']))
{
	$next_evnt_id = $_POST['next_evnt_id'];
	$update_event_next = mysql_query("UPDATE ".$slrp_prefix."event SET event_next_id = '$next_evnt_id' WHERE event_id = '$current_evnt_id'") or die ("failed setting next event.");
}


$get_focus_name = "event";

if($curusrslrprnk[slurp_rank_id] <= 5)
{
	$wide_align = "right";
}

if($curusrslrprnk[slurp_rank_id] >= 6)
{
	$wide_align = "left";
}

// end message table at top, and its row.
$get_events = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id = '$current_evnt_id'");
$gtevnt = mysql_fetch_assoc($get_events);

$event_next_game = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id = '$gtevnt[event_next_id]'") or die ("failed getting next event info");
$evngamecnt = mysql_num_rows($event_next_game);
$evngame = mysql_fetch_assoc($event_next_game);

$event_last_game = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_next_id = '$gtevnt[event_id]'") or die ("failed getting last event info");
$evlgamecnt = mysql_num_rows($event_last_game);
$evlgame = mysql_fetch_assoc($event_last_game);

$current_evnt_name = strip_tags(stripslashes($gtevnt[event]));
$next_evnt_name = strip_tags(stripslashes($evngame[event]));
$last_evnt_name = strip_tags(stripslashes($evlgame[event]));
//the row that holds messages at the top

// get ideal dates derived from the event
$ideal_start = new DateTime($gtevnt[event_date]);	
$ideal_start_date = date_format($ideal_start, 'l, F jS, Y');
$ideal_start_time = date_format($ideal_start, 'g:i A');
$ideal_start = $ideal_start_date." at ".$ideal_start_time;
// echo" ideal start: $ideal_start<br>";
$ideal_end = new DateTime($gtevnt[event_end]);	
$ideal_end_date = date_format($ideal_end, 'l, F jS, Y');
$ideal_end_time = date_format($ideal_end, 'g:i A');
$ideal_end  = $ideal_end_date." at ".$ideal_end_time;
// echo" ideal end: $ideal_end<br>";
$ideal_dt = new DateTime($gtevnt[event_downtime_cutoff_date]);	
$ideal_dt_date = date_format($ideal_dt, 'l, F jS, Y');
$ideal_dt_time = date_format($ideal_dt, 'g:i A');
$ideal_dt = $ideal_dt_date." at ".$ideal_dt_time;
// echo" ideal dt: $ideal_dt<br>";
$ideal_lock = new DateTime($gtevnt[event_database_lock_date]);	
$ideal_lock_date = date_format($ideal_lock, 'l, F jS, Y');
$ideal_lock_time = date_format($ideal_lock, 'g:i A');
$ideal_lock = $ideal_lock_date." at ".$ideal_lock_time;
// echo" ideal lock: $ideal_lock<br>";
$ideal_reset = new DateTime($gtevnt[event_xp_date]);	
$ideal_reset_date = date_format($ideal_reset, 'l, F jS, Y');
$ideal_reset_time = date_format($ideal_reset, 'g:i A');
$ideal_reset = $ideal_reset_date." at ".$ideal_reset_time;
// echo" ideal reset: $ideal_reset<br>";
$ideal_next = new DateTime($evngame[event_date]);	
$ideal_next_date = date_format($ideal_next, 'l, F jS, Y');
$ideal_next_time = date_format($ideal_next, 'g:i A');
$ideal_next = $ideal_next_date." at ".$ideal_next_time;
// echo" ideal next: $ideal_next<br>";
$ideal_last = new DateTime($evlgame[event_date]);	
$ideal_last_date = date_format($ideal_last, 'l, F jS, Y');
$ideal_last_month = date_format($ideal_last, 'F');
$ideal_last_time = date_format($ideal_last, 'g:i A');
$ideal_last = $ideal_last_date." at ".$ideal_last_time;
// echo" ideal next: $ideal_next<br>";

// start a row to hold the main content, and a cell 5/6 of the screen wide, to leave the rest as a sidebar
// also start a table in the cell; it wil be  number of columns equal to the values set by rank at the beginning

if($current_evnt_id >= 2);
{
	echo"
<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
	<td align = 'left' valign = 'middle' colspan = '3'>
		<font class='heading2'>
	";

	// start the main info boxes and form
	echo"
		&nbsp;&nbsp;<font color = 'orange'>$current_evnt_name</font></font>
	</td>
	<td width='2%'>
		&nbsp;
	</td>
	<form name = 'evnt_del' method = 'post' action = 'modules.php?name=$module_name'>
	<td colspan='3' align='center' valign='middle'>
	";
	
	if($now >= $gtevnt[event_end])
	{
		echo"<font class='heading1'><font color='orange'>This event was in the past.</font></font>";
	}
	if($now <= $gtevnt[event_end] AND $now >= $gtevnt[event_date])
	{
		echo"<font class='heading1'><font color='yellow'>This event is NOW.</font></font>";
	}
	if($now <= $gtevnt[event_date])
	{
		echo"<font class='heading1'><font color='orange'>This event is in the future.</font></font>";
	}
	
	echo"
	</td>
	<td width='2%'>
	&nbsp;
	</td>
	<form name = 'evnt_del' method = 'post' action = 'modules.php?name=$module_name'>
	<td align='right' valign='middle'>
	";
	
	if($curusrslrprnk[slurp_rank_id] <= 5)
	{
		echo"
		<input type='hidden' value='$current_evnt_id' name='delete_event_id'>
		<input type='hidden' value='1' name='evnt_expander'>
		<input type='hidden' value='$ntro_expander' name='ntro_expander'>
		<font color='red'><b>[<input type='submit' class='submit3' value='Delete'>]</b></font>
		";
	}
	
	echo"
		</td>
	</form>
</tr>
<tr height='9'>
	<td>
	
	</td>
</tr>
<tr>
<form name = 'evnt_new' method = 'post' action = 'modules.php?name=$module_name&file=evnt_edit'>
	<td align='left' valign='top' colspan='9'>
		<font class='heading1'>Beginning <font color='orange'>$ideal_start</font>.</font>
		<br>
		<font class='heading1'>Ending <font color = 'orange'>$ideal_end</font>.</font>
		<br>
		<font class='heading1'>Downtime cutoff/DB Lock is <font color = 'orange'>$ideal_dt</font>.</font>
		<br>
		<font class='heading1'>XP for the previous event is given <font color = 'orange'>$ideal_reset</font>.</font>
		<br>
	";
	
	if($evlgamecnt == 1)
	{
		echo"
		<font class='heading1'>The previous game was <a class='storytitle' href='modules.php?name=My_Vanguard&file=evnt_edit&current_evnt_id=$evlgame[event_id]&expander_abbr=evnt'>$last_evnt_name</a> on <a class='storytitle' href='modules.php?name=My_Vanguard&file=evnt_edit&current_evnt_id=$evlgame[event_id]&expander_abbr=evnt'>$ideal_last</a></font>.
		<br>
		";
	}
	if($evlgamecnt == 0)
	{
		echo"
	<font class='heading1'>The previous game was <font color='orange'>not set</font>.</font>
	<br>
		";
	}
	
	echo"<font class='heading1'>The next game is <font color='orange'>";
	
	if($curusrslrprnk[slurp_rank_id] <= 5)
	{	
		echo"
		<select class='engine' name = 'next_evnt_id'>
		";
	}
	
	if($gtevnt[event_next_id] > 0)
	{
		$next_event_type = mysql_query("SELECT * FROM ".$slrp_prefix."event_type INNER JOIN ".$slrp_prefix."event ON ".$slrp_prefix."event.event_type_id = ".$slrp_prefix."event_type.event_type_id WHERE ".$slrp_prefix."event.event_id = '$evngame[event_id]'") or die ("failed getting event next type.");
		$nxtevnttyp = mysql_fetch_assoc($next_event_type);
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{
			echo"
		<option value='$evngame[event_id]'>
		$nxtevnttyp[event_type] ($evngame[event_date]): $evngame[event]
		</option>
			";
		}
		
		if($curusrslrprnk[slurp_rank_id] >= 6)
		{
			echo"<a class='storytitle' href='modules.php?name=My_Vanguard&file=evnt_edit&current_evnt_id=$evngame[event_id]&expander_abbr=evnt'>$evngame[event]</a> on <a class='storytitle' href='modules.php?name=My_Vanguard&file=evnt_edit&current_evnt_id=$evngame[event_id]&expander_abbr=evnt'>$ideal_next</a></font>";
		}
		
		
		
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{		
			$get_active_events = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id > '1' AND event_slurp_id = '$slrpnfo[slurp_id]' ORDER BY event_date DESC, event_type_id DESC");
			while($actvevnts = mysql_fetch_assoc($get_active_events))
			{
				$active_event_type = mysql_query("SELECT * FROM ".$slrp_prefix."event_type WHERE event_type_id = '$actvevnts[event_type_id]'") or die ("failed getting event type.");
				$actvevnttyp = mysql_fetch_assoc($active_event_type);
				
				echo"
		<option value='$actvevnts[event_id]'>$actvevnttyp[event_type] ($actvevnts[event_date]): $actvevnts[event]</option>
				";
			}
					
			echo"
		</select>
			";
		}
	}
	if($gtevnt[event_next_id] <= 1)
	{
		echo"not set";
	}
	
	echo"</font>.</font>
		<br>
		<font class='heading1'>This event is a ";
	
	if($curusrslrprnk[slurp_rank_id] <= 5)
	{
		echo"
		<select class='engine' name = 'edit_evnt_type'>
		";
	}
	
	$current_event_type = mysql_query("SELECT * FROM ".$slrp_prefix."event_type WHERE ".$slrp_prefix."event_type.event_type_id = '$gtevnt[event_type_id]'") or die ("failed getting current event type.");
	$currevnttyp = mysql_fetch_assoc($current_event_type);
	$currevnttypcnt = mysql_num_rows($current_event_type);
		
	if($currevnttypcnt >= 1)
	{
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{
			echo"
			<option value = '$currevnttyp[event_type_id]'>$currevnttyp[event_type]</option>
			";
		}
		if($curusrslrprnk[slurp_rank_id] >= 6)
		{
			echo"<font color='orange'>$currevnttyp[event_type]</font>";
		}
	}
	
	if($curusrslrprnk[slurp_rank_id] <= 5)
	{
		$event_type_list = mysql_query("SELECT * FROM ".$slrp_prefix."event_type WHERE event_type_id > '1' ORDER BY event_type") or die ("failed getting event_type list.");
		while($evnttyplist = mysql_fetch_assoc($event_type_list))
		{
			echo"<option value = '$evnttyplist[event_type_id]'>$evnttyplist[event_type]</option>";		
		}
	
		echo"</select>";
	}
	
	echo".</font><br>";
	
	$get_current_minimum_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix."event ON ".$slrp_prefix."event.event_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix."event.event_id = '$gtevnt[event_id]'") or die ("failed getting current min rank to view.");
	$gtcurrmnrnk = mysql_fetch_assoc($get_current_minimum_rank);
	$gtcurrmnrnkcnt = mysql_num_rows($get_current_minimum_rank);
	
	if($curusrslrprnk[slurp_rank_id] >= 6)
	{
		if($gtcurrmnrnkcnt == 1)
		{
			echo"<font class='heading1'>You must be of rank <font color='orange'>$gtcurrmnrnk[slurp_rank]</font> to view this event.</font>";
		}
	}
	if($curusrslrprnk[slurp_rank_id] <= 5)
	{
		if($gtcurrmnrnkcnt == 0)
		{
			echo"<font class='heading1'>Miniminum Rank to View: </font><select class='engine' name = 'min_rank'><option value = '1'>Choose One</option>";
		}
		
		if($gtcurrmnrnkcnt == 1)
		{
			echo"<font class='heading1'>Miniminum Rank to View: </font><select class='engine' name = 'min_rank'><option value = '$gtcurrmnrnk[slurp_rank_id]'>$gtcurrmnrnk[slurp_rank]</option>";
		}
		
		$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id > '$curusrslrprnk[slurp_rank_id]' ORDER BY slurp_rank_id DESC") or die("failed to get rank list.");
		while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
		{
			echo"<option value = '$gtslrprnk[slurp_rank_id]'>$gtslrprnk[slurp_rank]</option>";
		}
		
		echo"</select>";
		}

		echo"
		</font>
	</td>
</tr>
<tr height='9'>
	<td colspan = '9' align = 'left' valign = 'top'>
	
	</td>
</tr>
		";
	
		if($curusrslrprnk[slurp_rank_id] <= 5)
		{
			echo"
<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
	<td align = 'left' valign = 'middle'>
		<input type='hidden' value='evnt' name='current_expander'>
		<input type='hidden' value='$curpcnfo[creature]' name='current_pc_id'>
		<input type='hidden' value='$gtevnt[event_id]' name='current_evnt_id'>
		<input class='submit3' type='submit' value='Submit' name='evnt_info_edit'>
		</font>
	</td>
	</form>
	<td  valign = 'top' width = '2%'>
		&nbsp;
	</td>
		";
		// end table holding the main ability info	

		$get_active_characters00 = mysql_query("SELECT * FROM ".$slrp_prefix."creature WHERE creature_id > '1' AND creature_status_id = '4' ORDER BY creature") or die ("failed getting pc list.");
		while($gtactvchar00 = mysql_fetch_assoc($get_active_characters00))
		{
			if(isset($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_att']))
			{
				$updated_att = $_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_att'];
			}
			if(empty($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_att']))
			{
				if(isset($_POST['empty_flag']))
				{
					$updated_att = -1;
				}
				if(empty($_POST['empty_flag']))
				{
					$updated_att = $gtactvchar00[creature_earned_xp_1];
				}
			}
		
			if(isset($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_cbn']))
			{
				$updated_cbn = $_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_cbn'];
			}
			if(empty($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_cbn']))
			{
				if(isset($_POST['empty_flag']))
				{
					$updated_cbn = 0;
				}
				if(empty($_POST['empty_flag']))
				{
					$updated_cbn = $gtactvchar00[creature_earned_xp_2];
				}
			}
			
			if(isset($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_bonus']))
			{
				$updated_bonus = $_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_bonus'];
			}
			if(empty($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_bonus']))
			{
				if(isset($_POST['empty_flag']))
				{
					$updated_bonus = 0;
				}
				if(empty($_POST['empty_flag']))
				{
					$updated_bonus = $gtactvchar00[creature_earned_xp_3];
				}
			}
			
			if(isset($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_svc']))
			{
				$updated_svc = $_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_svc'];
			}
			if(empty($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_svc']))
			{
				if(isset($_POST['empty_flag']))
				{
					$updated_svc = 0;
				}
				if(empty($_POST['empty_flag']))
				{
					$updated_svc = $gtactvchar00[creature_earned_xp_4];
				}
			}
			
			if(isset($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_dt']))
			{
				$updated_dt = $_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_dt'];
			}
			if(empty($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_dt']))
			{
				if(isset($_POST['empty_flag']))
				{
					$updated_dt = 0;
				}
				if(empty($_POST['empty_flag']))
				{
					$updated_dt = $gtactvchar00[creature_earned_xp_5];
				}
			}
			
			if(isset($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_donate']))
			{
				$updated_donate = $_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_donate'];
			}
			if(empty($_POST[$gtevnt[event_id].'_'.$gtactvchar00[creature_id].'_donate']))
			{
				if(isset($_POST['empty_flag']))
				{
					$updated_donate = 0;
				}
				if(empty($_POST['empty_flag']))
				{
					$updated_donate = $gtactvchar00[creature_earned_xp_6];
				}
			}
			
			// echo"$gtactvchar00[1] att: $updated_att . cbn: $updated_cbn . svc: $updated_svc . dt: $updated_dt<br>";
			
			$update_xp_fields = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_earned_xp_1 = '$updated_att', creature_earned_xp_2 = '$updated_cbn', creature_earned_xp_3 = '$updated_bonus', creature_earned_xp_4 = '$updated_svc', creature_earned_xp_5 = '$updated_dt', creature_earned_xp_6 = '$updated_donate' WHERE creature_id = '$gtactvchar00[creature_id]'");
		}
		
		//end top row
		//start tracking list
		
		echo"
			<form name = 'evnt_print_dt' method = 'post' action = 'modules.php?name=$module_name&file=evnt_print_dt'>
			<td valign = 'middle' align = 'center'>
				<input type='hidden' value='evnt' name='current_expander'>
				<input type='hidden' value='$gtevnt[event_id]' name='current_evnt_id'>
				<input class='submit3' type='submit' value='Print Downtimes' name='evnt_print_dt'>
			</td>
			</form>
			<td  valign = 'top' width = '2%'>
				&nbsp;
			</td>
			<form name = 'evnt_print_page' method = 'post' action = 'modules.php?name=$module_name&file=evnt_print_page'>
			<td valign = 'middle' align = 'center'>
				<input type='hidden' value='evnt' name='current_expander'>
				<input type='hidden' value='$gtevnt[event_id]' name='current_evnt_id'>
				<input class='submit3' type='submit' value='Print XP Form' name='evnt_print_page'>
			</td>
			</form>
			<td  valign = 'top' width = '2%'>
				&nbsp;
			</td>
			<form name = 'evnt_print_svc' method = 'post' action = 'modules.php?name=$module_name&file=evnt_print_blank'>
			<td valign = 'middle' align = 'center'>
				<input type='hidden' value='evnt' name='current_expander'>
				<input type='hidden' value='$gtevnt[event_id]' name='current_evnt_id'>
				<input class='submit3' type='submit' value='Print Admission Form' name='evnt_print_blank'>
			</td>
			</form>
			<td  valign = 'top' width = '2%'>
				&nbsp;
			</td>
			<form name = 'evnt_print_svc' method = 'post' action = 'modules.php?name=$module_name&file=evnt_print_svc'>
			<td valign = 'middle' colspan = '2' align = 'center'>
				<input type='hidden' value='evnt' name='current_expander'>
				<input type='hidden' value='$gtevnt[event_id]' name='current_evnt_id'>
				<input class='submit3' type='submit' value='Print Service Form' name='evnt_print_svc'>
			</td>
			</form>
		</tr>
		<form name = 'evnt_edit' method = 'post' action = 'modules.php?name=$module_name&file=evnt_edit'>
		<tr>
			<td colspan='9'>
				<table cellpadding='0' cellspacing='0' border='0'>
			";
			
			$get_active_characters = mysql_query("SELECT * FROM ".$slrp_prefix."creature INNER JOIN nuke_users ON ".$slrp_prefix."creature.creature_nuke_user_id = nuke_users.user_id WHERE nuke_users.user_id > '1' AND ".$slrp_prefix."creature.creature_status_id = '4' ORDER BY nuke_users.username,".$slrp_prefix."creature.creature") or die ("failed getting pc list 321.");			
		}
		if($curusrslrprnk[slurp_rank_id] >= 6)
		{
			echo"
					<tr>
						<td colspan='9'>
							<table cellpadding='0' cellspacing='0' border='0'>
			";
			
			$get_active_characters = mysql_query("SELECT * FROM ".$slrp_prefix."creature INNER JOIN nuke_users ON ".$slrp_prefix."creature.creature_nuke_user_id = nuke_users.user_id WHERE nuke_users.user_id = '$usrnfo[user_id]' AND ".$slrp_prefix."creature.creature_status_id = '4' ORDER BY ".$slrp_prefix."creature.creature") or die ("failed getting pc list 321.");
		}
		
		$active_char_count = 0;
		
		
		while($gtactvchar = mysql_fetch_assoc($get_active_characters))
		{
			$get_active_char_player = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$gtactvchar[creature_nuke_user_id]'");
			$gtactvcharplyr = mysql_fetch_assoc($get_active_char_player);
			
			if($active_char_count == 0)
			{
				echo"
				<tr height='9'>
					<td colspan='15'>
					</td>
				</tr>
				<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
					<td valign = 'middle' colspan='7' align = 'left'>
						<font class='heading2'>
						&nbsp;Player &nbsp;&nbsp;&nbsp; Character
						</font>
					</td>
					<td  valign = 'top' width = '1%'>
					</td>
					<td valign = 'middle' colspan='7' align = 'right'>
						<font class='heading2'>
						Rewards for $ideal_last_month
						</font>
					</td>
				</tr>
				<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
					<td valign = 'middle' width = '20%' align = 'center'>
						<font class='tiny'>
						Payment
						</font>
					</td>
					<td  valign = 'top' width = '1%'>
					</td>
				";
				
				if($gtevnt[event_id] == $ngame[event_id])
				{
					echo"
						<td valign = 'middle' width = '8%' align = 'center'>
							<font class='tiny'>
							Cbn
							</font>
						</td>
						<td valign = 'top' width = '1%'>
						</td>
						<td valign = 'middle' width = '8%' align = 'center'>
							<font class='tiny'>
							Field
							</font>
						</td>
						";
						
						if($gtactvchar[creature_earned_xp_5] <= 1)
						{
							echo"
							<td valign = 'top' width = '1%'>
							</td>
							<td valign = 'middle' width = '16%' align = 'center'>
								<font class='tiny'>
								1st Pd?
								</font>
							</td>
							";
						}
						
						echo"
						<td valign = 'top' width = '1%'>
						</td>
						<td valign = 'middle' width = '8%' align = 'center'>
							<font class='tiny'>
							Donations
							</font>
						</td>
						<td valign = 'top' width = '1%'>
						</td>
						<td valign = 'middle' width = '8%' align = 'center'>
							<font class='tiny'>
							Monster
							</font>
						</td>
						<td valign = 'top' width = '1%'>												
						</td>
						<td valign = 'middle' width = '8%' align = 'right'>
						";
						
						if($curusrslrprnk[slurp_rank_id] <= 5)
						{
							echo"
							<font class='tiny'>
							<input type='hidden' value='evnt' name='current_expander'>
							<input type='hidden' value='1' name='empty_flag'>
							<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
							<input type='hidden' value='$gtevnt[event_id]' name='current_evnt_id'>
							<input class='submit3' type='submit' value='Save XP' name='evnt_edit'>
							</font>
							";
						}
						if($curusrslrprnk[slurp_rank_id] >= 6)
						{
							echo"
							<font class='tiny'>
							Projected
							</font>
							";
						}
						
						echo"
						</td>
					";
				}
				
				echo"
				<td valign = 'middle' align = 'center' colspan = '4'>
				<font class='tiny'>
				
				</font>
				</td>
				</tr>
				";
			}
			
			$get_recent_records = mysql_query("SELECT * FROM ".$slrp_prefix."creature_xp_log WHERE creature_id = '$gtactvchar[creature_id]' AND timestamp >= '$evlgame[event_date]' AND timestamp <= '$gtevnt[event_date]' ORDER BY timestamp DESC") or die ("failed getting recent records.");
			echo"
			<tr height='9'>
				<td colspan='15'>
				</td>
			</tr>
			
			<tr background='themes/$ThemeSel/images/back2b.gif' height='24'>
				<td valign = 'middle' colspan='7' align = 'left'>
					<font class='heading1'>
			";
			
			if($curusrslrprnk[slurp_rank_id] <= 5)
			{
				echo"
					$gtactvchar[creature_id]
				";
			}
			
			echo"
					&nbsp;$gtactvcharplyr[username]&nbsp;&nbsp;&nbsp;<font color='orange'><a href='modules.php?name=My_Vanguard&file=pc_edit_new&current_pc_id=$gtactvchar[creature_id]' class='content2'>$gtactvchar[creature]</a>
					</font>
					</font>
				</td>
				<td width='1%'>
				</td>
				<td valign = 'bottom' colspan='7' align = 'left'>
				
				</td>			
			</tr>
			<tr>
				<td valign = 'bottom' width='20%' align = 'center'>
			";
			
			if($gtevnt[event_id] == $ngame[event_id])
			{
				$xp_temp = 0;
				
				if($now <= $ngame[event_xp_date])
				{
					if($curusrslrprnk[slurp_rank_id] <= 5)
					{
						echo"
					<select class='engine' name='$gtevnt[event_id]_$gtactvchar[creature_id]_att'>
					";
					
					if($gtactvchar[creature_earned_xp_1] == 4)
					{
						echo"<option value='4'>4: $40</option>";
					}
					if($gtactvchar[creature_earned_xp_1] == 3)
					{
						echo"<option value='3'>3: $35</option>";
					}
					if($gtactvchar[creature_earned_xp_1] == 2)
					{
						echo"<option value='2'>4: P/T Mon/$20</option>";
					}
					if($gtactvchar[creature_earned_xp_1] == 0)
					{
						echo"<option value='0'>4: Free</option>";
					}
					if($gtactvchar[creature_earned_xp_1] == 1)
					{
						echo"<option value='1'>0: Complimentary</option>";
					}
					if($gtactvchar[creature_earned_xp_1] == 6)
					{
						echo"<option value='6'>6: F/T Mon</option>";
					}
					
					echo"
					<option value='1'>0: Complimentary</option>
					<option value='4'>4: $40</option>
					<option value='3'>3: $35</option>
					<option value='2'>4: P/T Mon/$20</option>
					<option value='0'>4: Free</option>
					<option value='6'>6: F/T Mon</option>
					</select>
					";
					}
					
					if($gtactvchar[creature_earned_xp_1] == 4)
					{						
						$xp_temp = $xp_temp + 4;
						$display_xp_temp = 4;
					}
					if($gtactvchar[creature_earned_xp_1] == 3)
					{
						$xp_temp = $xp_temp + 3;
						$display_xp_temp = 3;
					}
					if($gtactvchar[creature_earned_xp_1] == 2)
					{
						$xp_temp = $xp_temp + 4;
						$display_xp_temp = 4;
					}
					if($gtactvchar[creature_earned_xp_1] == 0)
					{
						$xp_temp = $xp_temp + 4;
						$display_xp_temp = 4;
					}
					if($gtactvchar[creature_earned_xp_1] == 6)
					{
						$xp_temp = $xp_temp + 6;
						$display_xp_temp = 6;
					}
					if($gtactvchar[creature_earned_xp_1] == 1)
					{
						$display_xp_temp = 0;
					}					
					
					echo"<br><br><font class='heading1'><font color = '#CC00FF'>+$display_xp_temp</font></font>";
					// echo" = $xp_temp";
				}
				
				echo"
				</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'bottom' width = '8%' align = 'center'>
				<font class='heading1'><font color = '#CC00FF'>";
				
				if($now <= $ngame[event_xp_date])
				{
					if($curusrslrprnk[slurp_rank_id] <= 5)
					{
						echo"
					<input type = 'checkbox' name = '$gtevnt[event_id]_$gtactvchar[creature_id]_cbn' value = '1'";
						
						if($gtactvchar[creature_earned_xp_2] >= 1)
						{
							echo"checked";
						}
						
						echo">";
					}
					
					if($gtactvchar[creature_earned_xp_2] >= 1)
					{
						echo"<br><br>+$slrpnfo[slurp_xp_reason_2]";
						
						$xp_temp = $xp_temp + $slrpnfo[slurp_xp_reason_2];
					}
					if($gtactvchar[creature_earned_xp_2] == 0)
					{
						echo"<br><br>+0";
					}
					
					// echo" = $xp_temp";
				}
				
				echo"
				</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'bottom' width = '8%' align = 'center'>
				<font class='heading1'><font color = '#CC00FF'>";
				
				if($now <= $ngame[event_xp_date])
				{
					if($curusrslrprnk[slurp_rank_id] <= 5)
					{
						echo"
					<input type = 'checkbox' name = '$gtevnt[event_id]_$gtactvchar[creature_id]_bonus' value = '1'";
						
						if($gtactvchar[creature_earned_xp_3] >= 1)
						{
							echo"checked";
						}
						
						echo">";
					}
					
					if($gtactvchar[creature_earned_xp_3] >= 1)
					{
						echo"<br><br>+$slrpnfo[slurp_xp_reason_3]";
						
						$xp_temp = $xp_temp + $slrpnfo[slurp_xp_reason_3];
					}
					if($gtactvchar[creature_earned_xp_3] == 0)
					{
						echo"<br><br>+0";
					}
					
					// echo" = $xp_temp";
				}
				
				echo"
				</font>
				</td>
				";
				
				if($gtactvchar[creature_earned_xp_5] <= 1)
				{
					echo"
					<td valign = 'top' width = '1%'>
					</td>
					<td valign = 'bottom' width = '16%' align = 'center'>
					<font class='heading1'><font color = '#CC00FF'>";
					
					if($now <= $ngame[event_xp_date])
					{
						if($curusrslrprnk[slurp_rank_id] <= 5)
						{
							echo"
						<input type = 'checkbox' name = '$gtevnt[event_id]_$gtactvchar[creature_id]_dt' value = '1'";
							
							if($gtactvchar[creature_earned_xp_5] == 1)
							{
								echo"checked";								
							}
							
							echo">";
						}
						
						if($gtactvchar[creature_earned_xp_5] == 1)
						{
							echo"<br><br>x$slrpnfo[slurp_starting_bonus_1]+10";
							
							$xp_temp = 10+($xp_temp * $slrpnfo[slurp_starting_bonus_1]);
						}
						if($gtactvchar[creature_earned_xp_5] != 1)
						{
							echo"<br><br>+0";
						}
						
						// echo" = $xp_temp";
					}
					
					echo"
					</font>
					</td>
					";
				}
				
				echo"
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'bottom' width = '8%' align = 'center'>
				<font class='heading1'><font color = '#CC00FF'>";
												
				if($now <= $ngame[event_xp_date])
				{
					if($curusrslrprnk[slurp_rank_id] <= 5)
					{
						echo"<select class='engine' name = '$gtevnt[event_id]_$gtactvchar[creature_id]_donate'>";
						
						if($gtactvchar[creature_earned_xp_6] == 0)
						{
							echo"<option  value = '0'>0: $0</option>";
						}
						if($gtactvchar[creature_earned_xp_6] == 1)
						{
							echo"<option  value = '1'>1: up to $30</option>";
						}
						if($gtactvchar[creature_earned_xp_6] == 2)
						{
							echo"<option  value = '2'>2: up to $50</option>";
						}
						if($gtactvchar[creature_earned_xp_6] == 3)
						{
							echo"<option  value = '3'>3: up to $70</option>";
						}
						if($gtactvchar[creature_earned_xp_6] == 4)
						{
							echo"<option  value = '4'>4: up to $90</option>";
						}
						if($gtactvchar[creature_earned_xp_6] == 5)
						{
							echo"<option  value = '5'>5: over $100</option>";
						}
						
						echo"<option  value = '0'>0: $0</option>";
						echo"<option  value = '1'>1: up to $30</option>";
						echo"<option  value = '2'>2: up to $50</option>";
						echo"<option  value = '3'>3: up to $70</option>";
						echo"<option  value = '4'>4: up to $90</option>";
						echo"<option  value = '5'>5: over $100</option>";
						echo"</select>";
					}
					
					echo"<br><br>";
					
					$xp_temp = $xp_temp + $gtactvchar[creature_earned_xp_6];
					
					echo"+$gtactvchar[creature_earned_xp_6]";
				}
				
				echo"
				</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'bottom' width = '8%' align = 'center'>
				<font class='heading1'><font color = '#CC00FF'>";
												
				if($now <= $ngame[event_xp_date])
				{
					if($curusrslrprnk[slurp_rank_id] <= 5)
					{
						echo"<select class='engine' name = '$gtevnt[event_id]_$gtactvchar[creature_id]_svc'>";
						
						if($gtactvchar[creature_earned_xp_4] == 0)
						{
							echo"<option  value = '0'>0: 2+ hrs</option>";
						}
						if($gtactvchar[creature_earned_xp_4] == 1)
						{
							echo"<option  value = '1'>1: 8+ hrs</option>";
						}
						if($gtactvchar[creature_earned_xp_4] == 2)
						{
							echo"<option  value = '2'>2: 14+ hrs</option>";
						}
						if($gtactvchar[creature_earned_xp_4] == 3)
						{
							echo"<option  value = '3'>3: 20+ hrs</option>";
						}
						if($gtactvchar[creature_earned_xp_4] == 4)
						{
							echo"<option  value = '4'>4: 26+ hrs</option>";
						}
						if($gtactvchar[creature_earned_xp_4] == -1)
						{
							echo"<option  value = '-1'>-1: 0 hrs</option>";
						}
						
						echo"<option  value = '0'>0: 2+ hrs</option>";
						echo"<option  value = '1'>1: 8+ hrs</option>";
						echo"<option  value = '2'>2: 14+ hrs</option>";
						echo"<option  value = '3'>3: 20+ hrs</option>";
						echo"<option  value = '4'>4: 26+ hrs</option>";
						echo"<option  value = '-1'>-1: 0 hrs</option>";
						echo"</select>";
					}
					
					echo"<br><br>";
					
					$xp_temp = $xp_temp + $gtactvchar[creature_earned_xp_4];
					
					echo"+$gtactvchar[creature_earned_xp_4]";
				}
				
				echo"
				</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'bottom' width = '8%' align = 'center'><br><br>
				<font class='heading1'><font size = '5' color = '#CC00FF'>
				";
				
				if($xp_temp < 0)
				{
					$xp_temp = 0;
				}
				
				if($ngame[event_regular_xp_flag] == 0)
				{
					echo"<b>$xp_temp</b>";
				}
				
				echo"</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				";
			}
			
			echo"
			</tr>
			<tr height='9'>
				<td colspan='15'>
				</td>
			</tr>
			<tr>
				<td  valign = 'top' align = 'left' colspan='15'>
			";
			
			while($gtrecrecs = mysql_fetch_assoc($get_recent_records))
			{
				$get_rec_user = mysql_query("SELECT * FROM nuke_users WHERE user_id > '1' AND user_id = '$gtrecrecs[user_id]'") or die ("failed getting rec rec user.");
				$gtrcusr = mysql_fetch_assoc($get_rec_user);
				
				echo"<font class='heading7'>$gtrcusr[username] on $gtrecrecs[timestamp]: </font><font class='heading1'>$gtrecrecs[xp_value] ($gtrecrecs[reason])</font><br>";
			}
			
			echo"
						</td>
					</tr>									
		";
		
		$active_char_count++;
		if($active_char_count == 10)
		{
			$active_char_count = 0;
		}
	}
	echo"
			</table>
		</td>
	</tr>
	</form>
	";
}


include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>