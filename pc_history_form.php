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
$nav_title = "CHARACTER HISTORY ENTRY";
include("modules/$module_name/includes/slurp_header.php");
include("modules/$module_name/includes/fn_game_nfo.php");

if(isset($_POST['ntro_expander']))
{
	$ntro_expander = $_POST['ntro_expander'];
}
else
{
	$ntro_expander = 1;
}

if(isset($_POST['approved_notes']))
{
	$approved_notes = $_POST['approved_notes'];
	// echo"appr_notes: $approved_notes<br>";
	
	$approve_note = mysql_query("UPDATE ".$slrp_prefix."creature_game_note SET status_id = '4' WHERE creature_game_note_id = '$approved_notes'") or die ("failed updating approved notes.");
	
	$verify_approved_note = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note WHERE creature_game_note_id = '$approved_notes' AND status_id = '4'") or die ("failed verifying inserted approved note entry.");
	$$vrappntcnt = mysql_fetch_assoc($verify_approved_note);
	$vrappntcnt = mysql_num_rows($verify_approved_note);
	
	if($vrappntcnt == 1)
	{
		echo"
		<tr>
		<td width = '100%' align = 'left' valign = 'top'>
		<font color = 'yellow'>
		<li> $current_character_information's downtime note, <i>$vrappntcnt[creature_game_note]</i>, has been reviwed and returned.
		</font>
		<hr>
		</td>
		</tr>
		";
	}
	
	if($vrappntcnt == 0)
	{
		echo"
		<tr>
		<td width = '100%' align = 'left' valign = 'top'>
		<font color = 'red'>
		<li> $current_character_information's downtime note, <i>$vrappntcnt[creature_game_note]</i>, had and error being returned.
		</font>
		<hr>
		</td>
		</tr>
		";
	}
}

// for staff comments only
if(isset($_POST[staff_notes]))
{
	$staff_notes = $_POST[staff_notes];
	// echo"s_notes: $staff_notes<br>";

	if(isset($_POST['staff_goals_notes']))
	{
		$staff_goals_notes = $_POST['staff_goals_notes'];
		
		$verify_goals_entry = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note WHERE creature_game_note_id = '$staff_goals_notes'") or die ("failed verifying inserted goals entries.");
		$vrglntry = mysql_fetch_assoc($verify_goals_entry);
		
		// begin add comments section
		if(isset($_POST['staff_goals']))
		{
			if(isset($_POST['goals_min_rank']))
			{
				$goals_min_rank = $_POST['goals_min_rank'];
			}
			
			$gl_comment_type_id = 2;
			$staff_goals = mysql_real_escape_string($_POST['staff_goals']);
			
			// echo"gl_cmnt: $staff_goals, rank: $goals_min_rank<br>";
			
			if($staff_goals != "")
			{
				$add_goals_comment = mysql_query("INSERT INTO ".$slrp_prefix."creature_game_note_comment (creature_game_note_comment,creature_game_note_id,comment_type_id,user_id,creature_game_note_comment_min_rank) VALUES ('$staff_goals','$vrglntry[creature_game_note_id]','$gl_comment_type_id','$usrnfo[user_id]','$goals_min_rank')") or die ("failed inserting staff goal comment.");
			}
			
			$staff_goals = stripslashes($staff_goals);
		}
	}

	if(isset($_POST['staff_equipment_notes']))
	{
		$staff_equipment_notes = $_POST['staff_equipment_notes'];
		
		$verify_equipment_entry = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note WHERE creature_game_note_id = '$staff_equipment_notes'") or die ("failed verifying inserted equipment entries.");
		$vreqntry = mysql_fetch_assoc($verify_equipment_entry);
		
		if(isset($_POST['staff_equipment']))
		{
			if(isset($_POST['equipment_min_rank']))
			{
				$equipment_min_rank = $_POST['equipment_min_rank'];
			}
			
			$eq_comment_type_id = 3;
			$staff_equipment = mysql_real_escape_string($_POST['staff_equipment']);
			
			if($staff_equipment != "")
			{
				$add_equipment_comment = mysql_query("INSERT INTO ".$slrp_prefix."creature_game_note_comment (creature_game_note_comment,creature_game_note_id,comment_type_id,user_id,creature_game_note_comment_min_rank) VALUES ('$staff_equipment','$vreqntry[creature_game_note_id]','$eq_comment_type_id','$usrnfo[user_id]','$equipment_min_rank')") or die ("failed inserting staff equipment comment.");
			}
			
			$staff_equipment = stripslashes($staff_equipment);
		}
	}

	if(isset($_POST['staff_suggestions_notes']))
	{
		$staff_suggestions_notes = $_POST['staff_suggestions_notes'];
		
		$verify_suggestions_entry = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note WHERE creature_game_note_id = '$staff_suggestions_notes'") or die ("failed verifying inserted suggestions entries.");
		$vrsgntry = mysql_fetch_assoc($verify_suggestions_entry);
		
		if(isset($_POST['staff_suggestions']))
		{
			if(isset($_POST['suggestions_min_rank']))
			{
				$suggestions_min_rank = $_POST['suggestions_min_rank'];
			}
			
			$sg_comment_type_id = 5;
			$staff_suggestions = mysql_real_escape_string($_POST['staff_suggestions']);
			
			if($staff_suggestions != "")
			{
				$add_suggestions_comment = mysql_query("INSERT INTO ".$slrp_prefix."creature_game_note_comment (creature_game_note_comment,creature_game_note_id,comment_type_id,user_id,creature_game_note_comment_min_rank) VALUES ('$staff_suggestions','$vrsgntry[creature_game_note_id]','$sg_comment_type_id','$usrnfo[user_id]','$suggestions_min_rank')") or die ("failed inserting staff suggestions comment.");
			}
			
			$staff_suggestions = stripslashes($staff_suggestions);
		}
		// end add comments section
	}
	
	$verify_note_entry = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note WHERE creature_game_note_id = '$staff_notes'") or die ("failed verifying inserted notes entries.");
	// echo"SSS";
}

$comment_list = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_comment_id > '1'") or die ("failed verifying target comment for delete.");
$cmntlstcnt = mysql_num_rows($comment_list);

while($cmntlst = mysql_fetch_assoc($comment_list))
{
	// begin delete comments section
	if(isset($_POST['del_comment_'.$cmntlst[creature_game_note_id]]))
	{
		$del_comment_id = $_POST['del_comment_'.$cmntlst[creature_game_note_id]];
		
		$verify_target_comment = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_comment_id = '$del_comment_id'") or die ("failed verifying target comment for delete.");
		$vrtrgcmnt = mysql_fetch_assoc($verify_target_comment);
		
		$delete_comment = mysql_query("DELETE FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_comment_id = '$vrtrgcmnt[creature_game_note_comment_id]'") or die ("failed deleting comment.");
		
		$verify_deleted_comment = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_comment_id = '$vrtrgcmnt[creature_game_note_comment_id]'") or die ("failed deleting comment.");
		$vrdlcmntcnt = mysql_num_rows($verify_deleted_comment);
		
		if($vrdlcmntcnt >= 1)
		{
			echo"
			<tr>
			<td width = '100%' align = 'left' valign = 'top'>
			<font color = 'red'>
			<li> ";
			
			if($vrtrgcmnt[user_id] == $usrnfo[user_id])
			{
				echo"Your";
			}
			
			if($vrtrgcmnt[user_id] != $usrnfo[user_id])
			{
				echo"$usrnfo[name]'s";
			}
			
			echo" comment has NOT been deleted.
			</font>
			<br>
			<br>
			Try again or check with admin if there is a problem.
			</td>
			</tr>
			<tr>
			
			<td width = '100%' align = 'left' valign = 'top'>
			<hr>
			</td>
			
			</tr>
			";
		}
		
		if($vrdlcmntcnt == 0)
		{
			echo"
			<tr>
			<td width = '100%' align = 'left' valign = 'top'>
			<font color = 'yellow'>
			<li> ";
			
			if($vrtrgcmnt[user_id] == $usrnfo[user_id])
			{
				echo"Your";
			}
			
			if($vrtrgcmnt[user_id] != $usrnfo[user_id])
			{
				echo"$usrnfo[name]'s";
			}
			
			echo" comment has been deleted.
			</font>
			</td>
			
			</tr>
			<tr>
			
			<td width = '100%' align = 'left' valign = 'top'>
			<hr>
			</td>
			
			</tr>
			";
		}
	}
}

if(isset($_POST['player_notes']))
{
	$player_notes = mysql_real_escape_string($_POST['player_notes']);
	
	// begin posted notes
	if(isset($_POST['who_met']))
	{
		$who_met = mysql_real_escape_string($_POST['who_met']);
	}
	if(isset($_POST['during']))
	{
		$during = mysql_real_escape_string($_POST['during']);
	}

	if(isset($_POST['modules_attended']))
	{
	$modules_attended = mysql_real_escape_string($_POST['modules_attended']);
	}

	if(isset($_POST['goals_completed']))
	{
	$goals_completed = mysql_real_escape_string($_POST['goals_completed']);
	}

	if(isset($_POST['goals_set']))
	{
	$goals_set = mysql_real_escape_string($_POST['goals_set']);
	}

	if(isset($_POST['equipment_acquired']))
	{
	$equipment_acquired = mysql_real_escape_string($_POST['equipment_acquired']);
	}

	if(isset($_POST['equipment_desired']))
	{
	$equipment_desired = mysql_real_escape_string($_POST['equipment_desired']);
	}

	if(isset($_POST['module_ideas']))
	{
	$module_ideas = mysql_real_escape_string($_POST['module_ideas']);
	}

	if(isset($_POST['rules']))
	{
	$rules = mysql_real_escape_string($_POST['rules']);
	}

	if(isset($_POST['staff_stuff']))
	{
	$staff_stuff = mysql_real_escape_string($_POST['staff_stuff']);
	}
	// end posted notes

	// use preview to determine if it is the first or second edit, and update if the second
	if(isset($_POST['preview']))
	{
		$preview = 1;
		
		$update_notes_entry = mysql_query("UPDATE ".$slrp_prefix."creature_game_note SET who_met='$who_met',during='$during',modules_attended='$modules_attended',goals_completed='$goals_completed',goals_set='$goals_set',equipment_acquired='$equipment_acquired',equipment_desired='$equipment_desired',boons_owed_in='$boons_owed_in',boons_owed_out='$boons_owed_out',boons_paid_in='$boons_paid_in',boons_paid_out='$boons_paid_out',module_ideas='$module_ideas',rules='$rules',staff_stuff='$staff_stuff',date_submitted='$today' WHERE creature_game_note_id = '$player_notes' AND creature_id = '$curpcnfo[creature_id]'") or die ("failed updating existing note entry.");
	}
	
	// and insert of the first edit
	if(empty($_POST['preview']))
	{
		if(empty($_POST[staff_notes]))
		{
			$preview = 0;
			
			$get_existing_notes = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note INNER JOIN ".$slrp_prefix."event ON ".$slrp_prefix."event.event_id = ".$slrp_prefix."creature_game_note.event_id WHERE ".$slrp_prefix."creature_game_note.creature_id = '$curpcnfo[creature_id]' ORDER BY ".$slrp_prefix."event.event_date DESC") or die ("failed getting existing notes.");
			$gtexnotcnt = mysql_num_rows($get_existing_notes);
			
			$notes_counter = $gtexnotcnt+1;
			
			$notes_name = $pgame[event]." Notes #$notes_counter";
			
			$new_notes_entry = mysql_query("INSERT INTO ".$slrp_prefix."creature_game_note (creature_game_note,event_id,creature_id,who_met,during,modules_attended,goals_completed,goals_set,equipment_acquired,equipment_desired,boons_owed_in,boons_owed_out,boons_paid_in,boons_paid_out,module_ideas,rules,staff_stuff,date_submitted) VALUES ('$notes_name','$pgame[event_id]','$curpcnfo[creature_id]','$who_met','$during','$modules_attended','$goals_completed','$goals_set','$equipment_acquired','$equipment_desired','$boons_owed_in','$boons_owed_out','$boons_paid_in','$boons_paid_out','$module_ideas','$rules','$staff_stuff','$today')") or die ("failed inserting new note entry.");
			
			//set preview variable to 1 because it was forcing an extra confirmation page; this will skip the confirmation and go to pc_history instead of pc_history_form
			$preview = 1;
		}
	}

	// verify the existing info and use it for display
	$verify_note_entry = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note WHERE who_met = '$who_met' AND during = '$during' AND modules_attended = '$modules_attended' AND goals_completed = '$goals_completed' AND goals_set = '$goals_set' AND equipment_acquired = '$equipment_acquired' AND equipment_desired = '$equipment_desired' AND boons_owed_in = '$boons_owed_in' AND boons_owed_out = '$boons_owed_out' AND boons_paid_in = '$boons_paid_in' AND boons_paid_out = '$boons_paid_out' AND module_ideas = '$module_ideas' AND rules = '$rules' AND staff_stuff = '$staff_stuff'") or die ("failed verifying inserted notes entries.");
	/// echo"PPP";
}

$vrntntry = mysql_fetch_assoc($verify_note_entry);
$vrntntrycnt = mysql_num_rows($verify_note_entry);

if($vrntntrycnt >= 1)
{	
	if(isset($_POST[staff_notes]))
	{
		echo"
		<form name = 'pc_history_form' method='post' action = 'modules.php?name=$module_name&file=pc_history'>
		";
	}
	else
	{
		// if a preview, go back to this form; if already previewed, got back to the history page
		if($preview == 1)
		{
			echo"
			<form name = 'pc_history' method='post' action = 'modules.php?name=$module_name&file=pc_history'>
			";
		}
		if($preview == 0)
		{
			echo"
			<form name = 'pc_history_form' method='post' action = 'modules.php?name=$module_name&file=pc_history_form'>
			";
		}
	}

	echo"
	<tr>
	<td colspan = '7' align = 'left' valign = 'top'>

	<font class='heading2'>
	GOALS
	</font>
	<br>
	What new characters did $current_character_information meet?
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='who_met' rows = '4' cols = '100'>";
	}
	
	$who_met = stripslashes($vrntntry[who_met]);
	
	echo"$who_met";
	
	if(empty($_POST[staff_notes]))
	{	
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	What did $current_character_information do during the game?
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='during' rows = '4' cols = '100'>";
	}
	
	$during = stripslashes($vrntntry[during]);
	
	echo"$during";

	
	if(empty($_POST[staff_notes]))
	{
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	In what modules or other significant events did $current_character_information participate?
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='modules_attended' rows = '4' cols = '100'>";
	}
	
	$modules_attended = stripslashes($vrntntry[modules_attended]);
	
	echo"$modules_attended";
	
	if(empty($_POST[staff_notes]))
	{
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	What goals, if any, did the character complete?
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='goals_completed' rows = '4' cols = '100'>";
	}
	
	$goals_completed = stripslashes($vrntntry[goals_completed]);
	
	echo"$goals_completed";
	
	if(empty($_POST[staff_notes]))
	{
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	What new goals, if any, were set by $current_character_information?
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='goals_set' rows = '4' cols = '100'>";
	}
	
	$goals_set = stripslashes($vrntntry[goals_set]);
	
	echo"$goals_set";
	
	if(empty($_POST[staff_notes]))
	{	
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	";
	
	$existing_goal_comments = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_id = '$vrntntry[creature_game_note_id]' AND comment_type_id = '2' AND creature_game_note_comment_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY comment_timestamp") or die ("failed getting posted goal comments.");
	$exglcmmcnt = mysql_num_rows($existing_goal_comments);

	if($exglcmmcnt >= 1)
	{
		while($exglcmm = mysql_fetch_assoc($existing_goal_comments))
		{
			$get_gl_commenting_user = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$exglcmm[user_id]'") or die ("failed getting commenting staff id.");
			$gtglcmmusr = mysql_fetch_assoc($get_gl_commenting_user);
			
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
			
			echo"<font color = '$rank_color'>$exglcmm[comment_timestamp]: $gtglcmmusr[name] wrote: </font><i>$goal_comment</i><hr>";
		}
	}

	if(isset($_POST['staff_goals']))
	{
		if($staff_goals != "")
		{
			$staff_goals = mysql_real_escape_string($_POST['staff_goals']);
			
			$get_staff_goal_comments = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment  WHERE creature_game_note_comment = '$staff_goals' AND creature_game_note_id = '$vrntntry[0]' AND comment_type_id = '$gl_comment_type_id' AND user_id = '$usrnfo[user_id]'") or die ("failed verifying staff goal comment.");
			$gtstfglcmnt = mysql_fetch_assoc($get_staff_goal_comments);
			
			$new_goal_comment = stripslashes($gtstfglcmnt[creature_game_note_comment]);
			
			echo"
			<input type='hidden' value='$gtstfglcmnt[creature_game_note_comment_id]' name='gl_comment_id'>
			<textarea name ='staff_goals' rows = '4' cols = '100'>$new_goal_comment</textarea>
			<br>
			<br>
			";
			
			if($curusrslrprnk[slurp_rank_id] <= $gtstfglcmnt[creature_game_note_comment_min_rank])
			{
				$current_rank_information = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix."creature_game_note_comment ON ".$slrp_prefix."creature_game_note_comment.creature_game_note_comment_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix."creature_game_note_comment.creature_game_note_comment_id = '$gtstfglcmnt[creature_game_note_comment_id]' AND ".$slrp_prefix."slurp_rank.slurp_rank_id >= '$curusrslrprnk[slurp_rank_id]'") or die ("failed getting current rank info.");
				$currrnknfo = mysql_fetch_assoc($current_rank_information);
				
				echo"<font size = '1' color = '#7fffd4'>
				Min Rank: </font><select class='engine' name = 'goals_min_rank'>
				<option value = '$currrnknfo[slurp_rank_id]'>$currrnknfo[slurp_rank]</option>
				";
				
				$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id >= '$curusrslrprnk[slurp_rank_id]' ORDER BY slurp_rank_id") or die("failed to get rank list.");
				while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
				{
					echo"
					<option value = '$gtslrprnk[slurp_rank_id]'>$gtslrprnk[slurp_rank]</option>
					";
				}
				
				echo"
				</select>
				<br>
				<br>
				";
			}
		}
	}
	
	echo"
	<font class='heading2'>
	EQUIPMENT
	</font>
	<br>
	List Items and Materials that $current_character_information has acquired, how, and from whom/where:
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='equipment_acquired' rows = '4' cols = '100'>";
	}
	
	$equipment_acquired = stripslashes($vrntntry[equipment_acquired]);

	echo"$equipment_acquired";
	
	if(empty($_POST[staff_notes]))
	{
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	List Items and Materials that $current_character_information would like to acquire by next gather, how, and from whom/where:
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='equipment_desired' rows = '4' cols = '100'>";
	}
	
	$equipment_desired = stripslashes($vrntntry[equipment_desired]);
	
	echo"$equipment_desired";
	
	if(empty($_POST[staff_notes]))
	{
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	";

	$existing_equipment_comments = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_id = '$vrntntry[0]' AND comment_type_id = '3' AND creature_game_note_comment_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY comment_timestamp") or die ("failed getting posted equipment comments.");
	$exeqcmmcnt = mysql_num_rows($existing_equipment_comments);

	if($exeqcmmcnt >= 1)
	{
		while($exeqcmm = mysql_fetch_assoc($existing_equipment_comments))
		{
			$get_eq_commenting_user = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$exeqcmm[user_id]'") or die ("failed getting commenting staff id.");
			$gteqcmmusr = mysql_fetch_assoc($get_eq_commenting_user);
			
			$equipment_comment = stripslashes($exeqcmm[creature_game_note_comment]);
			
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
			
			echo"<font color = '$rank_color'>$exeqcmm[comment_timestamp]: $gteqcmmusr[uname] wrote: </font><i>$equipment_comment</i><hr>";
		}
	}
	
	if(isset($_POST['staff_equipment']))
	{
		if($staff_equipment != "")
		{
			$staff_equipment = mysql_real_escape_string($_POST['staff_equipment']);
			
			$get_staff_equipment_comments = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment  WHERE creature_game_note_comment = '$staff_equipment' AND creature_game_note_id = '$vrntntry[creature_game_note_id]' AND comment_type_id = '$eq_comment_type_id' AND user_id = '$usrnfo[user_id]' ORDER BY comment_timestamp") or die ("failed verifying staff equipment comment.");
			$gtstfeqcmnt = mysql_fetch_assoc($get_staff_equipment_comments);
			
			$new_equipment_comment = stripslashes($gtstfeqcmnt[creature_game_note_comment]);
			
			echo"
			<input type='hidden' value='$gtstfeqcmnt[creature_game_note_comment]' name='eq_comment_id'>
			<textarea name ='staff_equipment' rows = '4' cols = '100'>$new_equipment_comment</textarea>
			<br>
			<br>
			";
			
			if($curusrslrprnk[slurp_rank_id] <= $gtstfeqcmnt[creature_game_note_comment_min_rank])
			{
				$current_rank_information = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix."creature_game_note_comment ON ".$slrp_prefix."creature_game_note_comment.creature_game_note_comment_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix."creature_game_note_comment.creature_game_note_comment_id = '$gtstfeqcmnt[creature_game_note_comment_id]' AND ".$slrp_prefix."slurp_rank.slurp_rank_id >= '$curusrslrprnk[slurp_rank_id]'") or die ("failed getting current rank info.");
				$currrnknfo = mysql_fetch_assoc($current_rank_information);
				
				echo"<font size = '1' color = '#7fffd4'>
				Min Rank: </font><select class='engine' name = 'equipment_min_rank'>
				<option value = '$currrnknfo[slurp_rank_id]'>$currrnknfo[slurp_rank]</option>
				";
				
				$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id >= '$curusrslrprnk[slurp_rank_id]' ORDER BY slurp_rank_id") or die("failed to get rank list.");
				while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
				{
					echo"
					<option value = '$gtslrprnk[slurp_rank_id]'>$gtslrprnk[slurp_rank]</option>
					";
				}
				
				echo"
				</select>
				<br>
				<br>
				";
			}
		}
	}
	
	echo"
	<font class='heading2'>
	SUGGESTIONS
	</font>
	<br>
	Module Ideas for Plot consideration:
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='module_ideas' rows = '4' cols = '100'>";
	}
	
	$module_ideas = stripslashes($vrntntry[module_ideas]);
	
	echo"$module_ideas";
	
	if(empty($_POST[staff_notes]))
	{
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	Rules corrections, loopholes noted, or new ideas:
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='rules' rows = '4' cols = '100'>";
	}
	
	$rules = stripslashes($vrntntry[rules]);
	
	echo"$rules";
	
	if(empty($_POST[staff_notes]))
	{
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	Commendations and complaints; professional observations about Staff:
	<br>
	";
	
	if(empty($_POST[staff_notes]))
	{
		echo"<textarea name ='staff_stuff' rows = '4' cols = '100'>";
	}
	
	$staff_stuff = stripslashes($vrntntry[staff_stuff]);
	
	echo"$staff_stuff";
	
	if(empty($_POST[staff_notes]))
	{
		echo"</textarea>";
	}
	
	echo"
	<br>
	<br>
	";
	
	$existing_suggestions_comments = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment WHERE creature_game_note_id = '$vrntntry[creature_game_note_id]' AND comment_type_id = '5' AND creature_game_note_comment_min_rank >= '$curusrslrprnk[slurp_rank_id]' ORDER BY comment_timestamp") or die ("failed getting posted suggestions comments.");
	$exsgcmmcnt = mysql_num_rows($existing_suggestions_comments);

	if($exsgcmmcnt >= 1)
	{
		while($exsgcmm = mysql_fetch_assoc($existing_suggestions_comments))
		{
			$get_sg_commenting_user = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$exsgcmm[user_id]'") or die ("failed getting commenting staff id.");
			$gtsgcmmusr = mysql_fetch_assoc($get_sg_commenting_user);
			
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
			
			echo"<font color = '$rank_color'>$exsgcmm[comment_timestamp]: $gtsgcmmusr[name] wrote: </font><i>$suggestions_comment</i><hr>";
		}
	}
	
	if(isset($_POST['staff_suggestions']))
	{
		if($staff_suggestions != "")
		{
			$staff_suggestions = mysql_real_escape_string($_POST['staff_suggestions']);
			
			$get_staff_suggestions_comments = mysql_query("SELECT * FROM ".$slrp_prefix."creature_game_note_comment  WHERE creature_game_note_comment = '$staff_suggestions' AND creature_game_note_id = '$vrntntry[creature_game_note_id]' AND comment_type_id = '$sg_comment_type_id' AND user_id = '$usrnfo[user_id]' ORDER BY comment_timestamp") or die ("failed verifying staff suggestions comment.");
			$gtstfsgcmnt = mysql_fetch_assoc($get_staff_suggestions_comments);
			
			$new_suggestions_comment = stripslashes($gtstfsgcmnt[creature_game_note_comment]);
			
			echo"
			<input type='hidden' value='$gtstfsgcmnt[creature_game_note_comment_id]' name='sg_comment_id'>
			<textarea name ='staff_suggestions' rows = '4' cols = '100'>$new_suggestions_comment</textarea>
			<br>
			<br>
			";
			
			if($curusrslrprnk[slurp_rank_id] <= $gtstfsgcmnt[slurp_rank_id])
			{
				$current_rank_information = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank INNER JOIN ".$slrp_prefix."creature_game_note_comment ON ".$slrp_prefix."creature_game_note_comment.creature_game_note_comment_min_rank = ".$slrp_prefix."slurp_rank.slurp_rank_id WHERE ".$slrp_prefix."creature_game_note_comment.creature_game_note_comment_id = '$gtstfsgcmnt[creature_game_note_comment_id]' AND ".$slrp_prefix."slurp_rank.slurp_rank_id >= '$curusrslrprnk[slurp_rank_id]'") or die ("failed getting current rank info.");
				$currrnknfo = mysql_fetch_assoc($current_rank_information);
				
				echo"<font size = '1' color = '#7fffd4'>
				Min Rank: </font><select class='engine' name = 'suggestions_min_rank'>
				<option value = '$currrnknfo[slurp_rank_id]'>$currrnknfo[slurp_rank]</option>
				";
				
				$get_slurp_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id >= '$curusrslrprnk[slurp_rank_id]' ORDER BY slurp_rank_id") or die("failed to get rank list.");
				while($gtslrprnk = mysql_fetch_assoc($get_slurp_rank))
				{
					echo"
					<option value = '$gtslrprnk[slurp_rank_id]'>$gtslrprnk[slurp_rank]</option>
					";
				}
				
				echo"
				</select>
				<br>
				<br>
				";
			}
		}
	}
	
	echo"
	<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
	<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
	<input type='hidden' value='char' name='current_expander'>
	";

	if(isset($_POST[staff_notes]))
	{
		echo"<input type='hidden' value='$vrntntry[creature_game_note]' name='staff_notes'><input class='submit3' type='submit' value='Submit' name='pc_history'>";
	}
	else
	{
		if($preview == 1)
		{
			echo"<input type='hidden' value='1' name='player_notes'><input class='submit3' type='submit' value='Submit' name='pc_history'>";
			$character_dt_update = mysql_query("UPDATE ".$slrp_prefix."creature SET creature_earned_xp_4 = '1' WHERE creature_id = '$curpcnfo[creature_id]'");
		}
		if($preview == 0)
		{
			echo"<input type='hidden' value='1' name='preview'><input type='hidden' value='$vrntntry[creature_game_note]' name='player_notes'><input class='submit3' type='submit' value='Preview' name='pc_history_form'>";
		}
	}
	
	echo"
	</form>
	";
	
}

echo"
<tr background='themes/RedShores/images/row2.gif' height='9'>
	<td colspan='9'>
	</td>
</tr> 
<tr background='themes/RedShores/images/base1.gif' height='24'>
<form name = 'back_to_pc_edit_new' method='post' action = 'modules.php?name=$module_name&file=pc_edit_new'>

<td colspan = '9'>
<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
<input type='hidden' value='$ntro_expander' name = 'ntro_expander'>
<input type='hidden' value='char' name='current_expander'>
<input class='submit3' type='submit' value='Back to View/Edit' name='back_to_pc_edit_new'>
</td>

</tr>
</form>
";

include("modules/$module_name/includes/slurp_footer.php");
?>