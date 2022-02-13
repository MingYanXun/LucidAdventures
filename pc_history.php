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
$nav_title = "RECORDS VAULT";
include("modules/$module_name/includes/slurp_header.php");
include("modules/$module_name/includes/fn_game_nfo.php");

if(isset($_POST['xp']))
{
	$xp_change = $_POST['xp'];
	
	if(empty($_POST['reason']))
	{
		echo"
		<tr>
		
		<td  valign = 'top' colspan = '7' align = 'left'>
		<font color = 'red'>
		<li>You did not enter a reason for adding XP manually.
		</td>
		</tr>
		";
	}
	else
	{
		$new_xp_total = ($curpcnfo[creature_xp_current]+$xp_change);
		$supplant_xp = $_POST['supplant_xp'];
		$user_reason = strip_tags(mysql_real_escape_string($_POST['reason']));
		$reason = ("Added ".$xp_change." (".$user_reason.") . . . New XP total: ".$new_xp_total);
		
		$add_xp = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_xp_current=creature_xp_current+'$xp_change',creature_xp_earned=creature_xp_earned+'$xp_change' WHERE creature_id = '$curpcnfo[creature_id]'");
		
		// echo"$pc_status, $reason, $usrnfo[1], $current_character_information<br>";
		$record_xp_log = mysql_query("INSERT INTO ".$slrp_prefix."creature_xp_log (creature_id,xp_value,user_id,reason) VALUES ('$curpcnfo[creature_id]','$xp_change','$usrnfo[user_id]','$reason')") or die ("failed adding character submission to xp log.");
		
		if($supplant_xp >= 1)
		{
			$supplant_normal_xp = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_supplant_xp = '$supplant_xp' WHERE creature_id = '$curpcnfo[creature_id]'") or die ("failed supplanting normal xp.");
		}
	}
}

if(isset($_POST['review_notes_id']))
{
	$review_notes_id = $_POST['review_notes_id'];
}

if(isset($_POST['player_notes']))
{
	$review_notes_id = $_POST['player_notes'];
}

if(isset($_POST['staff_notes']))
{
	$review_notes_id = $_POST['staff_notes'];
}

//	if(empty($_POST['staff_notes']))
//	{
//		if(empty($_POST['player_notes']))
//		{
//			if(empty($_POST['review_notes_id']))
//			{
//				$review_notes_id = 1;
//				$comments_expander = 0;
//			}
//		}
//	}

if($review_notes_id >= 2)
{
	$get_current_note = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note WHERE creature_game_note_id = '$review_notes_id' AND creature_game_note_id > '1'") or die ("failed getting current note.");
	$gtcurrnotcnt = mysql_num_rows($get_current_note);
	$gtcurrnot = mysql_fetch_assoc($get_current_note);
}

if(isset($_POST['staff_notes']))
{
	if(isset($_POST['staff_goals']))
	{
		if(isset($_POST['goals_min_rank']))
		{
			$goals_min_rank = $_POST['goals_min_rank'];
		}
		if(empty($_POST['goals_min_rank']))
		{
			$goals_min_rank = 7;
		}
		
		$gl_comment_id = $_POST['gl_comment_id'];
		$staff_goals = strip_tags(mysql_real_escape_string($_POST['staff_goals']));
		$gl_comment_type_id = 2;
		
		$update_goals_comment = mysql_query("UPDATE ".$slrp_prefix."creature_game_note_comment SET creature_game_note_comment = '$staff_goals', creature_game_note_comment_min_rank = '$goals_min_rank' WHERE creature_game_note_comment_id = '$gl_comment_id'") or die ("failed updating staff goal comment.");
	}
	
	if(isset($_POST['staff_equipment']))
	{
		if(isset($_POST['equipment_min_rank']))
		{
			$equipment_min_rank = $_POST['equipment_min_rank'];
		}
		if(empty($_POST['equipment_min_rank']))
		{
			$equipment_min_rank = 7;
		}
		
		$eq_comment_id = $_POST['eq_comment_id'];
		$staff_equipment = strip_tags(mysql_real_escape_string($_POST['staff_equipment']));
		$eq_comment_type_id = 3;
		
		$update_equipment_comment = mysql_query("UPDATE ".$slrp_prefix."creature_game_note_comment SET creature_game_note_comment = '$staff_equipment', creature_game_note_comment_min_rank = '$equipment_min_rank' WHERE creature_game_note_comment_id = '$eq_comment_id'") or die ("failed updating staff equipment comment.");
	}
	
	if(isset($_POST['staff_boons']))
	{
		if(isset($_POST['boons_min_rank']))
		{
			$boons_min_rank = $_POST['boons_min_rank'];
		}
		if(empty($_POST['boons_min_rank']))
		{
			$boons_min_rank = 7;
		}
		
		$bn_comment_id = $_POST['bn_comment_id'];
		$staff_boons = strip_tags(mysql_real_escape_string($_POST['staff_boons']));
		$bn_comment_type_id = 4;
		
		$update_boons_comment = mysql_query("UPDATE ".$slrp_prefix."creature_game_note_comment SET creature_game_note_comment = '$staff_boons', creature_game_note_comment_min_rank = '$boons_min_rank'WHERE creature_game_note_comment_id = '$bn_comment_id'") or die ("failed updating staff boons comment.");
	}
	
	if(isset($_POST['staff_suggestions']))
	{
		if(isset($_POST['suggestions_min_rank']))
		{
			$suggestions_min_rank = $_POST['suggestions_min_rank'];
		}
		if(empty($_POST['suggestions_min_rank']))
		{
			$suggestions_min_rank = 7;
		}
		
		$sg_comment_id = $_POST['sg_comment_id'];
		$staff_suggestions = strip_tags(mysql_real_escape_string($_POST['staff_suggestions']));
		$sg_comment_type_id = 5;
		
		$update_suggestions_comment = mysql_query("UPDATE ".$slrp_prefix."creature_game_note_comment SET creature_game_note_comment = '$staff_suggestions', creature_game_note_comment_min_rank = '$suggestions_min_rank' WHERE creature_game_note_comment_id = '$sg_comment_id'") or die ("failed updating staff suggestions comment.");
	}	
}

if(isset($_POST['player_notes']))
{
	if(isset($_POST['who_met']))
	{
		$who_met = strip_tags(mysql_real_escape_string($_POST['who_met']));
	}
	if(isset($_POST['during']))
	{
		$during = strip_tags(mysql_real_escape_string($_POST['during']));
	}

	if(isset($_POST['modules_attended']))
	{
		$modules_attended = strip_tags(mysql_real_escape_string($_POST['modules_attended']));
	}

	if(isset($_POST['goals_completed']))
	{
		$goals_completed = strip_tags(mysql_real_escape_string($_POST['goals_completed']));
	}

	if(isset($_POST['goals_set']))
	{
		$goals_set = strip_tags(mysql_real_escape_string($_POST['goals_set']));
	}

	if(isset($_POST['equipment_acquired']))
	{
		$equipment_acquired = strip_tags(mysql_real_escape_string($_POST['equipment_acquired']));
	}

	if(isset($_POST['equipment_desired']))
	{
		$equipment_desired = strip_tags(mysql_real_escape_string($_POST['equipment_desired']));
	}

	if(isset($_POST['boons_owed_in']))
	{
		$boons_owed_in = strip_tags(mysql_real_escape_string($_POST['boons_owed_in']));
	}

	if(isset($_POST['boons_owed_out']))
	{
		$boons_owed_out = strip_tags(mysql_real_escape_string($_POST['boons_owed_out']));
	}

	if(isset($_POST['boons_paid_in']))
	{
		$boons_paid_in = strip_tags(mysql_real_escape_string($_POST['boons_paid_in']));
	}

	if(isset($_POST['boons_paid_out']))
	{
		$boons_paid_out = strip_tags(mysql_real_escape_string($_POST['boons_paid_out']));
	}

	if(isset($_POST['module_ideas']))
	{
		$module_ideas = strip_tags(mysql_real_escape_string($_POST['module_ideas']));
	}

	if(isset($_POST['rules']))
	{
		$rules = strip_tags(mysql_real_escape_string($_POST['rules']));
	}

	if(isset($_POST['staff_stuff']))
	{
		$staff_stuff = strip_tags(mysql_real_escape_string($_POST['staff_stuff']));
	}
	
	$update_notes_entry = mysql_query("UPDATE ".$slrp_prefix."creature_game_note SET who_met = '$who_met', during = '$during', modules_attended = '$modules_attended', goals_completed = '$goals_completed', goals_set = '$goals_set', equipment_acquired = '$equipment_acquired', equipment_desired = '$equipment_desired', boons_owed_in = '$boons_owed_in' , boons_owed_out = '$boons_owed_out', boons_paid_in = '$boons_paid_in', boons_paid_out = '$boons_paid_out', module_ideas = '$module_ideas', rules = '$rules', staff_stuff = '$staff_stuff', date_submitted = '$today', status_id = '3' WHERE creature_game_note_id = '$notes_entry' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed updating existing note entry.");
	
}

if($ntro_expander == 1)
{
	echo"
	<tr>
	<td width = '100%' colspan='9'>
	";
	// dressing for the controls at the top
	OpenTable3();

	echo"
	<font class='heading1'>
	$slrpnfo[slurp_name]'s next game date is <font color = 'orange'>$ngame[event_date]</font>.
	<br>
	<br>
	Each game, Regular Build Points are added to Characters according to the following criteria:
	<br>
	<br>
	<li> Paying $35 = 3 Build
	<li> Paying $40 = 4 Build
	<li> Site Clean up = 1 Build
	<li> Monster time: every 6 hours over 2 required = 1 Build
	<li> Full Time Monster = 6 Build
	<li> Half time Monster/Paying $20 = 4 Build
	<li> Monstering the Field Mod = 1 Build
	<li> Donations of	$20 in value = 1 Build. There will be monthly exceptions for badly needed items.
	<li> New players who pay for their first event start at level 2, and gain x2 event Build for that event.
	<hr class='pipes'>
	Your Character gets most of the Experience within a few days of the game. You can Build them on the Character as soon as they appear. Don't worry about overspending or tracking it all; the website handles that part. You also have the option of filling out the Downtime Notes leading up to the next game, to help track what happened.
	<hr class='pipes'>
	On the left you will find a record of:
	<br>
	<br>
	<li> Build Points $current_character_information has earned.
	<li> Expenditures of those Build Points, by whom, and for what.
	<li> Abilities that $current_character_information has learned.
	<li> Other important changes to $current_character_information.
	<hr class='pipes'>
	On the right you will find the Downtime Notes management tools:
	<br>
	<br>
	<li> Downtime Notes cutoff status determines the availability of this form.
	<li> This form may be used up to $slrpnfo[slurp_notes_max] times per game per character.

	<li> Each entry will be reviewed by Staff and comments will be returned to you when Staff has had a chance to discuss your entries.
	</font>
	";
	
	CloseTable3();
}

echo"
</td>
<tr>

<td colspan = '3'  valign = 'top' width = '40%' align = 'left'>

<table width = '100%' cellspacing='0' cellpadding='0' border='0'>
<tr background='themes/Vanguard/images/back2b.gif' height='24'>
<form name = 'back_to_pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
<td  valign = 'middle' align = 'center'>
<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
<input type='hidden' value='char' name='current_expander'>
<input class='submit3' type='submit' value='Back to $curpcnfo[creature]' name='back_to_pc_edit_new'>
</td>
</form>

<td width = '2%'>
	&nbsp;
</td>
<form name = 'show_hide_instructions' method='post' action='modules.php?name=$module_name&file=pc_history'>
<td valign = 'middle' align = 'center'>";

if($ntro_expander == 1)
{
	echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
	<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='review_notes_id'>
	<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
	<input type='hidden' value='$comments_expander' name = 'comments_expander'>
	<input type='hidden' value='0' name = 'ntro_expander'>
	<input class='submit3' type='submit' value='Hide Instructions' name='show_hide_instructions'>";
}

if($ntro_expander == 0)
{
	echo"<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
	<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='review_notes_id'>
	<input type='hidden' value='$race_desc_expander' name = 'race_desc_expander'>
	<input type='hidden' value='$comments_expander' name = 'comments_expander'>
	<input type='hidden' value='1' name = 'ntro_expander'>
	<input class='submit3' type='submit' value='Show Instructions' name='show_hide_instructions'>";
}

$get_existing_notes = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note INNER JOIN ".$slrp_prefix."event ON ".$slrp_prefix."event.event_id = ".$slrp_prefix."creature_game_note.event_id WHERE ".$slrp_prefix."creature_game_note.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_game_note.status_id >= '3' ORDER BY ".$slrp_prefix."event.event_date DESC") or die ("failed getting existing notes.");
$gtexnotcnt = mysql_num_rows($get_existing_notes);

$who_met = strip_tags(mysql_real_escape_string($_POST['who_met']));
$during = strip_tags(mysql_real_escape_string($_POST['during']));
$modules_attended = strip_tags(mysql_real_escape_string($_POST['modules_attended']));
$goals_completed = strip_tags(mysql_real_escape_string($_POST['goals_completed']));
$goals_set = strip_tags(mysql_real_escape_string($_POST['goals_set']));
$equipment_acquired = strip_tags(mysql_real_escape_string($_POST['equipment_acquired']));
$equipment_desired = strip_tags(mysql_real_escape_string($_POST['equipment_desired']));
$boons_owed_in = strip_tags(mysql_real_escape_string($_POST['boons_owed_in']));
$boons_owed_out = strip_tags(mysql_real_escape_string($_POST['boons_owed_out']));
$boons_paid_in = strip_tags(mysql_real_escape_string($_POST['boons_paid_in']));
$boons_paid_out = strip_tags(mysql_real_escape_string($_POST['boons_paid_out']));
$module_ideas = strip_tags(mysql_real_escape_string($_POST['module_ideas']));
$rules = strip_tags(mysql_real_escape_string($_POST['rules']));
$staff_stuff = strip_tags(mysql_real_escape_string($_POST['staff_stuff']));


// count games to limit posting ability

$get_past_game_notes_count = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note INNER JOIN ".$slrp_prefix."event ON ".$slrp_prefix."event.event_id = ".$slrp_prefix."creature_game_note.event_id WHERE ".$slrp_prefix."creature_game_note.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_game_note.event_id = '$pgame[0]' ORDER BY ".$slrp_prefix."event.event_date DESC") or die ("failed getting existing notes.");
$gtpstgmnotcnt = mysql_num_rows($get_past_game_notes_count);
$notes_temp = $slrpnfo[slurp_notes_max]-1;

echo"
</td>
</form>

</tr>
<tr>

<td valign = 'middle' width = '49%' align = 'left'>
<font class='heading2'>
OCCURRED
</td>

<td width = '2%'>
&nbsp;
</td>

<td valign = 'middle' width = '49%' align = 'left'>
<font class='heading2'>
USER
</td>

</tr>
<tr>

<td align = 'left' valign = 'top' colspan = '3'>
<font class='heading1'>
[PC Status] Description of action taken.<br><font color='orange'>Orange is the most recent</font>.
</font>
</td>

</tr>
</table>

</td>

<td width = '2%' valign = 'top' align = 'center'>
	<table width = '100%' cellspacing='0' cellpadding='0' border='0'>
		<tr background='themes/Vanguard/images/back2b.gif' height='24'>
			<td valign = 'middle' width = '100%' align = 'center'>
			&nbsp;
			</td>
		</tr>
	</table>
</td>

<td valign = 'top' align = 'left'>
<table width = '100%' cellspacing='0' cellpadding='0' border='0'>
<tr background='themes/Vanguard/images/back2b.gif' height='24'>
	<td valign = 'middle' width = '32%' align = 'center'>
		<font class='heading2'>
		VIEW NOTES
		</font>
	</td>
	<td width='2%'>
	&nbsp;
	</td>
	<td valign = 'middle' width = '32%' align = 'center'>
		<font class='heading2'>
		UP FOR REVIEW
		</font>
	</td>
</tr>
";
if($now >= $ngame[event_downtime_cutoff_date])
{
	echo"
		<tr>
			<td valign = 'middle' width = '100%' align = 'center'>
				<font class='heading1'>
				Notes Deadline was <font color = 'yellow'>$ngame[event_downtime_cutoff_date]</font>. Notes entry is closed
				</font>
			</td>
		</tr>
	";
}

echo"
<tr>
<td valign = 'top' width = '32%' align = 'center'>
";

if($review_notes_id >= 2)
{
	if($gtcurrnot[status_id] >= 3)
	{
		if($comments_expander == 1)
		{
			echo"
			<form name = 'staff_comments' method='post' action = 'modules.php?name=$module_name&file=pc_history'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='0' name='comments_expander'>
			<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='review_notes_id'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input class='submit3' type='submit' value='Hide Comments' name='staff_comments'>
			</form>";
		}
		
		if($comments_expander == 0)
		{
			echo"
			<form name = 'staff_comments' method='post' action = 'modules.php?name=$module_name&file=pc_history'>
			<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
			<input type='hidden' value='1' name='comments_expander'>
			<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='review_notes_id'>
			<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
			<input class='submit3' type='submit' value='Show Comments' name='staff_comments'>
			</form>";
		}
	}
}

if($gtexnotcnt == 0)
{
	echo"<font class='heading1'>There are no notes yet for <i>$pgame[event]</i>.</font>";
}
if($gtexnotcnt >= 1)
{
	if($gtpstgmnotcnt <= $notes_temp)
	{
		echo"
		<form name = 'pc_history_view_notes' method='post' action = 'modules.php?name=$module_name&file=pc_history'>
		<select name = 'review_notes_id'>
		<option value = '0'>Reset Form</option>
		";
	}

	while($gtexnot = mysql_fetch_assoc($get_existing_notes))
	{
		$get_event_date = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id = '$gtexnot[event_id]'") or die ("failed gatting event date.");
		$gtevdt = mysql_fetch_assoc($get_event_date);
		
		echo"<option value = '$gtexnot[creature_game_note_id]'>$gtexnot[creature_game_note]</option>";
	}

	echo"
	</select>
	<br>
	<br>
	<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
	<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
	<input type='hidden' value='char' name='current_expander'>
	<input class='submit3' type='submit' value = 'Go!' name = 'pc_history_view_notes'>
	";
}

echo"
</td>
</form>

<td width = '2%'>
&nbsp;
</td>

<form name = 'pc_history_pending_notes' method='post' action = 'modules.php?name=$module_name&file=pc_history'>
<td valign = 'top' width = '32%' align = 'right'>
";

if($curusrslrprnk[slurp_rank_id] <= 4)
{
	$get_pending_notes = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note INNER JOIN ".$slrp_prefix."event ON ".$slrp_prefix."event.event_id = ".$slrp_prefix."creature_game_note.event_id WHERE ".$slrp_prefix."creature_game_note.creature_id = '$curpcnfo[creature_id]' AND ".$slrp_prefix."creature_game_note.status_id = '3' ORDER BY ".$slrp_prefix."event.event_date DESC") or die ("failed getting existing notes.");
	$gtpndnotcnt = mysql_num_rows($get_pending_notes);

	if($gtpndnotcnt >=1)
	{
		echo"<select name = 'review_notes_id'>";
		$notes_temp = $slrpnfo[slurp_notes_max]-1;
		
		while($gtpndnot = mysql_fetch_assoc($get_pending_notes))
		{
			$get_event_date = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id = '$gtpndnot[event_id]'") or die ("failed gatting event date.");
			$gtevdt = mysql_fetch_assoc($get_event_date);
			
			echo"<option value = '$gtpndnot[creature_game_note_id]'>$gtpndnot[creature_game_note]</option>";
		}
		
		echo"
		</select>
		<br>
		<br>
		<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
		<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
		<input type='hidden' value='char' name='current_expander'>
		<input class='submit3' type='submit' value='Go!' name='pc_history_pending_notes'>
		";
	}
}

echo"
</td>
</form>

</tr>

</table>


</td>

</tr>
	<tr background='themes/Vanguard/images/row2.gif' height='9'>
		<td colspan='9'>
		</td>
	</tr> 
<tr>
	<td valign = 'top' colspan = '3'>
		<table width = '100%' cellpadding='0' border='0' cellspacing='0'>
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
		<td valign = 'top' colspan = '3' align = 'left'>	
			<hr class='pipes'>
		</td>
	</tr>
	<tr>

	<td valign = 'top' width = '49%' align = 'left'>
	<font class='heading2'>
	$rptxplog[timestamp]
	</font>
	</td>
	
	<td valign = 'top' width = '2%' align = 'left'>
	&nbsp;
	</td>
	
	<td valign = 'top' width = '49%' align = 'left'>
	<font class='heading2'>
	$userentry[username]
	";
	
	if($userentry[name] != "")
	{
		echo"	($userentry[name])";
	}
	
	echo"
	</font>
	</td>

	</tr>
	<tr>

	<td valign = 'top' colspan = '3' align = 'left'>
	<font class='heading2'>
	";
	
	if($rptxplogcntr == $rptxplogcnt)
	{
		echo"<font class='heading2'>
		<font color= 'orange'>
		";
	}
	else
	{
		echo"<font class='heading1'>";
	}
	
	echo"
			$rptxplog[reason]
			</font>
			</font>
		</td>

	</tr>
	";
	
	$rptxplogcntr--;
}

echo"
</table>

</td>

<td width = '2%'>
&nbsp;
</td>

<form name = 'pc_history_notes' method='post' action = 'modules.php?name=$module_name&file=pc_history_form'>
<td valign = 'top' align = 'left'>
<hr class='pipes'>
";
if($curpcnfo[creature_status_id] == 4)
{
	if($review_notes_id >= 2)
	{
		$who_met = stripslashes($gtcurrnot[who_met]);
		
		echo"
		<font class='heading2'>
		<b>GOALS and ROLEPLAY</b>
		</font>
		<br>
		<br>
		<font class='heading1'>
		What new characters did $current_character_information meet?
		<br>
		<font color = 'orange'>
		$who_met
		</font>
		";
	}

	else
	{	
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<font class='heading2'>GOALS and ROLEPLAY</font>
				<br>
				<font class='heading1'>What new characters did $current_character_information meet?
				<br>
				<input type='text' class='textbox3' size='70%' name='who_met'></input>
				";
			}
		}
	}

	if($review_notes_id >= 2)
	{
		$during = stripslashes($gtcurrnot[during]);
		
		echo"
		<br>
		<br>
		What did $current_character_information do during the game?
		<br>
		<font color = 'orange'>
		$during
		</font>
		";
	}

	else
	{
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<br>
				<br>
				What did $current_character_information do during the game?
				<br>
				<input type='text' class='textbox3' size='70%' name='during'></input>
				";
			}
		}
	}

	if($review_notes_id >= 2)
	{
		$modules_attended = stripslashes($gtcurrnot[modules_attended]);
		
		echo"
		<br>
		<br>
		In what modules or other significant events did $current_character_information participate?
		<br>
		<font color = 'orange'>
		$modules_attended
		</font>
		";
	}

	else
	{
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<br>
				<br>
				In what modules or other significant events did $current_character_information participate?
				<br>
				<input type='text' class='textbox3' size='70%' name='modules_attended'></input>
				";
			}
		}
	}

	if($review_notes_id >= 2)
	{
		$goals_completed = stripslashes($gtcurrnot[goals_completed]);
		
		echo"
		<br>
		<br>
		What goals, if any, did the character complete?
		<br>
		<font color = 'orange'>
		$goals_completed
		</font>
		";
	}

	else
	{
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<br>
				<br>
				What goals, if any, did the character complete?
				<br>
				<input type='text' class='textbox3' size='70%' name='goals_completed'></input>
				";
			}
		}
	}

	if($review_notes_id >= 2)
	{
		$goals_set = stripslashes($gtcurrnot[goals_set]);
		
		echo"
		<br>
		<br>
		What new goals, if any, were set by $current_character_information?
		<br>
		<font color = 'orange'>
		$goals_set
		</font>
		<br>
		<br>
		";
	}

	else
	{
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<br>
				<br>
				What new goals, if any, were set by $current_character_information?
				<br>
				<input type='text' class='textbox3' size='70%' name='goals_set'></input>
				";
			}
		}
	}

	echo"</font><font class='heading1'>";

	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		if($review_notes_id >= 2)
		{
			if($gtcurrnot[status_id] == 3)
			{
				echo"
				<br>
				<br>
				<font color = 'orange'><li> Add Response to Activity Notes</font>
				<input type='text' class='textbox3' size='50%' name='staff_goals'></input> . . .	<font size = '1' color = 'white'>Min Rank: <select name = 'goals_min_rank'>";
				
				$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id >= '$curusrslrprnk[slurp_rank_id]' ORDER BY slurp_rank_id") or die("failed to get rank list.");
				while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
				{
					echo"
					<option value = '$gtslrprnk[slurp_rank_id]'>$gtslrprnk[slurp_rank]</option>
					";
				}
				
				echo"
				</select>
				</font>
				<br>
				<br>
				";
			}
		}
	}

	$existing_goal_comments = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_id = '$gtcurrnot[creature_game_note_id]' AND comment_type_id = '2' AND creature_game_note_comment_min_rank >= '$curusrslrprnk[slurp_rank_id]'") or die ("failed getting posted goal comments.");
	$exglcmmcnt = mysql_num_rows($existing_goal_comments);

	if($exglcmmcnt >= 1)
	{
		$gl_del_count = 0;

		while($exglcmm = mysql_fetch_assoc($existing_goal_comments))
		{
			$get_gl_commenting_user = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$usrnfo[user_id]'") or die ("failed getting commenting staff id.");
			$gtglcmmusr = mysql_fetch_assoc($get_gl_commenting_user);
			
			$gl_del_count++;
			if($gl_del_count == 1)
			{
				if($comments_expander == 1)
				{
					if($gtcurrnot[status_id] == 3)
					{
						if($usrnfo[user_id] != $gtglcmmusr[user_id])
						{
							if($curusrslrprnk[slurp_rank_id] <= 3)
							{
								echo"
								<font size ='1' color = 'red'>DELETE</font><br>
								";
							}
						}
						
						if($usrnfo[user_id] == $gtglcmmusr[user_id])
						{
							echo"
							<font size ='1' color = 'red'>DELETE</font><br>
							";
						}
					}
				}
			}
			
			if($comments_expander == 1)
			{
				if($usrnfo[user_id] != $gtglcmmusr[user_id])
				{
					if($curusrslrprnk[slurp_rank_id] <= 3)
					{
						if($gtcurrnot[status_id] == 3)
						{
							echo"
							<input type='checkbox' value='$exglcmm[creature_game_note_comment_id]' name='del_comment_$exglcmm[creature_game_note_comment_id]'></input>
							";
						}
					}
				}
				
				if($usrnfo[user_id] == $gtglcmmusr[user_id])
				{
					if($gtcurrnot[status_id] == 3)
					{
						echo"
						<input type='checkbox' value='$exglcmm[creature_game_note_comment_id]' name='del_comment_$exglcmm[creature_game_note_comment_id]'></input>
						";
					}
				}
				
				$goal_comment = stripslashes($exglcmm[creature_game_note_comment]);
				
				if($exglcmm[creature_game_note_comment_min_rank] == 8)
				{
					$rank_color = "#4AC948";
				}
				if($exglcmm[creature_game_note_comment_min_rank] == 7)
				{
					$rank_color = "orange";
				}
				if($exglcmm[creature_game_note_comment_min_rank] == 6)
				{
					$rank_color = "#99ff00";
				}
				if($exglcmm[creature_game_note_comment_min_rank] == 5)
				{
					$rank_color = "yellow";
				}
				if($exglcmm[creature_game_note_comment_min_rank] == 4)
				{
					$rank_color = "#00B2EE";
				}
				if($exglcmm[creature_game_note_comment_min_rank] == 3)
				{
					$rank_color = "#CC00FF";
				}
				if($exglcmm[creature_game_note_comment_min_rank] == 2)
				{
					$rank_color = "black";
				}
				
				echo"<font color = '$rank_color'>$exglcmm[comment_timestamp]: $gtglcmmusr[username] wrote: </font><i>$goal_comment</i><br>";
			}
		}
	}

	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		if($review_notes_id >= 2)
		{
			if($gtcurrnot[status_id] == 3)
			{
				echo"
				<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='staff_goals_notes'>
				<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='staff_notes'>
				<input class='submit3' type='submit' value='Submit Responses' name='pc_history_notes'>
				<br>
				";
			}
		}
	}

	if($review_notes_id >= 2)
	{
		$equipment_acquired = stripslashes($gtcurrnot[equipment_acquired]);
		
		echo"
		<hr class='pipes'>
		<font class='heading2'>
		EQUIPMENT for $gtcurrnot[creature_game_note]
		</font>
		<font class='heading1'>
		<br>
		List Items and Materials that $current_character_information has acquired, how, and from whom/where:
		<br>
		<font color = 'orange'>
		$equipment_acquired
		</font>
		";
	}

	else
	{
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<hr class='pipes'>
				<font class='heading2'>
				EQUIPMENT
				</font>
				<br>
				<font class='heading1'>
				List Items and Materials that $current_character_information has acquired, how, and from whom/where:
				<br>
				<input type='text' class='textbox3' size='70%' name='equipment_acquired'></input>
				";
			}
		}
	}

	if($review_notes_id >= 2)
	{
		$equipment_desired = stripslashes($gtcurrnot[equipment_desired]);
		
		echo"
		<br>
		<br>
		List Items and Materials that $current_character_information would like to acquire by next gather, how, and from whom/where:
		<br>
		<font color = 'orange'>
		$equipment_desired
		</font>
		<br>
		<br>
		";
	}

	else
	{
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<br>
				<br>
				List Items and Materials that $current_character_information would like to acquire by next gather, how, and from whom/where:
				<br>
				<input type='text' class='textbox3' size='70%' name='equipment_desired'></input>
				";
			}
		}
	}

	echo"</font><font color = 'white'>";

	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		if($review_notes_id >= 2)
		{	
			if($gtcurrnot[status_id] == 3)
			{
				echo"
				<br>
				
				<font color = 'orange'><li> Add Response to Equipment Notes</font>
				<input type='text' class='textbox3' size='50%' name='staff_equipment'></input> . . .	<font class='heading1'>Min Rank: <select name = 'equipment_min_rank'>";
				
				$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id >= '$curusrslrprnk[slurp_rank_id]' ORDER BY slurp_rank_id") or die("failed to get rank list.");
				while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
				{
					echo"
					<option value = '$gtslrprnk[slurp_rank_id]'>$gtslrprnk[slurp_rank]</option>
					";
				}
				
				echo"
				</select>
				</font>
				<br>
				<br>
				";
			}
		}
	}

	$existing_equipment_comments = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_id = '$gtcurrnot[creature_game_note_id]' AND comment_type_id = '3' AND creature_game_note_comment_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY comment_timestamp") or die ("failed getting posted equipment comments.");
	$exeqcmmcnt = mysql_num_rows($existing_equipment_comments);

	if($exeqcmmcnt >= 1)
	{
		$eq_del_count = 0;
		
		while($exeqcmm = mysql_fetch_assoc($existing_equipment_comments))
		{
			$eq_del_count++;
			if($eq_del_count == 1)
			{
				if($comments_expander == 1)
				{
					if($gtcurrnot[status_id] == 3)
					{
						if($usrnfo[user_id] != $gteqcmmusr[user_id])
						{
							if($curusrslrprnk[slurp_rank_id] <= 3)
							{
								echo"
								<font size ='1' color = 'red'>DELETE</font><br>
								";
							}
						}
						
						if($usrnfo[user_id] == $gteqcmmusr[user_id])
						{
							echo"
							<font size ='1' color = 'red'>DELETE</font><br>
							";
						}
					}
				}
			}
			
			$get_eq_commenting_user = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$exeqcmm[user_id]'") or die ("failed getting commenting staff id.");
			$gteqcmmusr = mysql_fetch_assoc($get_eq_commenting_user);
			
			if($comments_expander == 1)
			{
				if($usrnfo[user_id] != $gteqcmmusr[user_id])
				{
					if($curusrslrprnk[slurp_rank_id] <= 2)
					{
						if($gtcurrnot[status_id] == 3)
						{
							echo"
							<input type='checkbox' value='$exeqcmm[creature_game_note_comment_id]' name='del_comment_$exeqcmm[creature_game_note_comment_id]'></input>
							";
						}
					}
				}
				
				if($usrnfo[user_id] == $gteqcmmusr[user_id])
				{
					if($gtcurrnot[status_id] == 3)
					{
						echo"
						<input type='checkbox' value='$exeqcmm[creature_game_note_comment_id]' name='del_comment_$exeqcmm[creature_game_note_comment_id]'></input>
						";
					}
				}
				
				$equipment_comment =stripslashes($exeqcmm[creature_game_note_comment]);
				
				if($exeqcmm[creature_game_note_comment_min_rank] == 8)
				{
					$rank_color = "#4AC948";
				}
				if($exeqcmm[creature_game_note_comment_min_rank] == 7)
				{
					$rank_color = "orange";
				}
				if($exeqcmm[creature_game_note_comment_min_rank] == 6)
				{
					$rank_color = "#99ff00";
				}
				if($exeqcmm[creature_game_note_comment_min_rank] == 5)
				{
					$rank_color = "yellow";
				}
				if($exeqcmm[creature_game_note_comment_min_rank] == 4)
				{
					$rank_color = "#00B2EE";
				}
				if($exeqcmm[creature_game_note_comment_min_rank] == 3)
				{
					$rank_color = "#CC00FF";
				}
				if($exeqcmm[creature_game_note_comment_min_rank] == 2)
				{
					$rank_color = "black";
				}
				
				echo"<font color = '$rank_color'>$exeqcmm[comment_timestamp]: $gteqcmmusr[username] wrote: </font><i>$equipment_comment</i><br>";
			}
		}
	}

	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		if($review_notes_id >= 2)
		{
			if($gtcurrnot[status_id] == 3)
			{
				echo"
				
				<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='staff_equipment_notes'>
				<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='staff_notes'>
				<input class='submit3' type='submit' value='Submit Responses' name='pc_history_notes'>
				<br>
				<br>
				";
			}
		}
	}

	if($review_notes_id >= 2)
	{
		$module_ideas = stripslashes($gtcurrnot[module_ideas]);
		
		echo"
		<hr class='pipes'>
		<font class='heading2'>
		SUGGESTION BOX for $gtcurrnot[creature_game_note]
		</font>
		<br>
		<font class='heading1'>
		Module Ideas for Plot consideration:
		<br>
		<font color = 'orange'>
		$module_ideas
		</font>
		";
	}

	else
	{
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<hr class='pipes'>
				<font class='heading2'>
				SUGGESTION BOX
				</font>
				<br>
				<font class='heading1'>
				Module Ideas for Plot consideration:
				<br>
				<input type='text' class='textbox3' size='70%' name='module_ideas'></input>
				";
			}
		}
	}

	if($review_notes_id >= 2)
	{
		$rules = stripslashes($gtcurrnot[rules]);
		
		echo"
		<br>
		<br>
		Rules corrections, loopholes noted, or new ideas:
		<br>
		<font color = 'orange'>
		$rules
		</font>
		";
	}
	else
	{
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<br>
				<br>
				Rules corrections, loopholes noted, or new ideas:
				<br>
				<input type='text' class='textbox3' size='70%' name='rules'></input>
				";
			}
		}
	}

	if($review_notes_id >= 2)
	{
		$staff_stuff= stripslashes($gtcurrnot[staff_stuff]);
		
		echo"
		<br>
		<br>
		Commendations and complaints; professional observations about Staff:
		<br>
		<font color = 'orange'>
		$staff_stuff
		</font>
		<br>
		<br>
		";
	}
	else
	{
		if($now <= $ngame[event_downtime_cutoff_date])
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				echo"
				<br>
				<br>
				Commendations and complaints; professional observations about Staff:
				<br>
				<input type='text' class='textbox3' size='70%' name='staff_stuff'></input>
				<br>
				<br>
				";
			}
		}
	}

	echo"</font><font class='heading1'>";

	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		if($review_notes_id >= 2)
		{
			if($gtcurrnot[status_id] == 3)
			{
				echo"
				<br>
				
				<font color = 'orange'><li> Add Response to Suggestion Box Notes</font>
				<input type='text' class='textbox3' size='50%' name='staff_suggestions'></input> . . .	<font size = '1' color = 'white'>Min Rank: <select name = 'suggestions_min_rank'>";
				
				$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id >= '$curusrslrprnk[slurp_rank_id]' ORDER BY slurp_rank_id") or die("failed to get rank list.");
				while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
				{
					echo"
					<option value = '$gtslrprnk[slurp_rank_id]'>$gtslrprnk[slurp_rank]</option>
					";
				}
				
				echo"
				</select>
				</font>
				<br>
				<br>
				";
			}
		}
	}

	$existing_suggestions_comments = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_id = '$gtcurrnot[creature_game_note_id]' AND comment_type_id = '5' AND creature_game_note_comment_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY comment_timestamp") or die ("failed getting posted suggestions comments.");
	$exsgcmmcnt = mysql_num_rows($existing_suggestions_comments);
	
	// echo"suggcnt: $exsgcmmcnt<br>";

	if($exsgcmmcnt >= 1)
	{
		$sg_del_count = 0;
		while($exsgcmm = mysql_fetch_assoc($existing_suggestions_comments))
		{
			// echo"comment: $exsgcmm[1]<br>";
			$sg_del_count++;
			if($sg_del_count == 1)
			{
				if($comments_expander == 1)
				{
					if($gtcurrnot[status_id] == 3)
					{
						if($usrnfo[user_id] != $gtsgcmmusr[user_id])
						{
							if($curusrslrprnk[slurp_rank_id] <= 3)
							{
								echo"
								<font size ='1' color = 'red'>DELETE</font><br>
								";
							}
						}
						
						if($usrnfo[user_id] == $gtsgcmmusr[user_id])
						{
							echo"
							<font size ='1' color = 'red'>DELETE</font><br>
							";
						}
					}
				}
			}
			
			$get_sg_commenting_user = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$exsgcmm[user_id]'") or die ("failed getting commenting staff id.");
			$gtsgcmmusr = mysql_fetch_assoc($get_sg_commenting_user);
			
			if($comments_expander == 1)
			{
				if($usrnfo[user_id] != $gtsgcmmusr[user_id])
				{
					if($curusrslrprnk[slurp_rank_id] <= 2)
					{
						if($gtcurrnot[status_id] == 3)
						{
							echo"
							<input type='checkbox' value='$exsgcmm[creature_game_note_comment_id]' name='del_comment_$exsgcmm[creature_game_note_comment_id]'></input>
							";
						}
					}
				}
				
				if($usrnfo[user_id] == $gtsgcmmusr[user_id])
				{
					if($gtcurrnot[status_id] == 3)
					{
						echo"
						<input type='checkbox' value='$exsgcmm[creature_game_note_comment_id]' name='del_comment_$exsgcmm[creature_game_note_comment_id]'></input>
						";
					}
				}
				
				$suggestions_comment = stripslashes($exsgcmm[creature_game_note_comment]);
				
				if($exsgcmm[creature_game_note_comment_min_rank] == 8)
				{
					$rank_color = "#4AC948";
				}
				if($exsgcmm[creature_game_note_comment_min_rank] == 7)
				{
					$rank_color = "orange";
				}
				if($exsgcmm[creature_game_note_comment_min_rank] == 6)
				{
					$rank_color = "#99ff00";
				}
				if($exsgcmm[creature_game_note_comment_min_rank] == 5)
				{
					$rank_color = "yellow";
				}
				if($exsgcmm[creature_game_note_comment_min_rank] == 4)
				{
					$rank_color = "#00B2EE";
				}
				if($exsgcmm[creature_game_note_comment_min_rank] == 3)
				{
					$rank_color = "#CC00FF";
				}
				if($exsgcmm[creature_game_note_comment_min_rank] == 2)
				{
					$rank_color = "black";
				}
				
				echo"<font color = '$rank_color'> $exsgcmm[timestamp]: $gtsgcmmusr[username] wrote:</font> <i>$suggestions_comment</i><br>";
			}
		}
	}

	if($curusrslrprnk[slurp_rank_id] <= 3)
	{
		if($review_notes_id >= 2)
		{
			if($gtcurrnot[status_id] == 3)
			{
				echo"
				<br>
				<br>
				<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='staff_suggestions_notes'>
				<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='staff_notes'>
				<input class='submit3' type='submit' value='Submit Responses' name='pc_history_notes'>
				";
			}
		}
	}

	echo"
	<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
	<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
	<input type='hidden' value='char' name='current_expander'>
	";

	if(isset($_POST['staff_notes']))
	{
		echo"<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='staff_notes'>";
	}
	if(empty($_POST['staff_notes']))
	{
		if($gtcurrnot[status_id] <= 2)
		{
			if($gtpstgmnotcnt <= $notes_temp)
			{
				if($now <= $ngame[event_downtime_cutoff_date])
				{
					echo"
					<br>
					<br>
					<input type='hidden' value='$gtcurrnot[creature_game_note_id]' name='player_notes'>
					<input class='submit3' type='submit' value='Submit' name='pc_history_notes'>
					";
				}
			}
		}
	}

	if($curusrslrprnk[slurp_rank_id] <= 4)
	{
		if($review_notes_id >= 2)
		{
			if($gtcurrnot[status_id] == 3)
			{
				echo"<font class='heading1'> . . . . . . . . Check to Approve This Note <input type='checkbox' value='$gtcurrnot[creature_game_note_id]' name='approved_notes'><br><br>";
			}
		}
	}
}

echo"
</form>
</td>

</tr>

<tr background='themes/Vanguard/images/back2b.gif' height='24'>
<form name = 'back_to_pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>
<td colspan = '9'>
<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
<input type='hidden' value='char' name='current_expander'>
<input class='submit3' type='submit' value='Back to View/Edit' name='back_to_pc_edit_new'>
</td>
</form>

</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>