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
	$wide_align = "center";
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
				<td valign = 'top' align = 'left'>
					<font size = '3' color = 'black'>
					<font size = '2'>Last Game: $last_evnt_name<br></font>
					<b>Event Name: $current_evnt_name</b><br>
					<font size = '2'>Next Game: $next_evnt_name</font>
					</font>
				</td>
				<td valign = 'top' width = '1%'>
				</td>
				<td valign = 'top' align = 'left'>
					<font size = '2' color = 'black'>
					</font>
	";

	// start the main info boxes and form
	$current_event_type = mysql_query("SELECT * FROM ".$slrp_prefix."event_type WHERE ".$slrp_prefix."event_type.event_type_id = '$gtevnt[2]'") or die ("failed getting current event type.");
	$currevnttyp = mysql_fetch_array($current_event_type, MYSQL_NUM);
	$currevnttypcnt = mysql_num_rows($current_event_type);
	
	if($currevnttypcnt >= 1)
	{
		echo"Event Type: $currevnttyp[1]
				<br>
$gtevnt[3]</font>
				";
	}
	
	echo"
				</td>
			</tr>
	";
	// end table holding the main ability info
}

//end top row
//start tracking list

echo"
	<tr>
		<td valign = 'top' colspan = '8' align = 'center'>
			<hr>
		</td>
	</tr>
	<tr>
		<td valign = 'top' align = 'center'>
			<font size = '3' color = 'black'>
			Player
			</font>
		</td>
		<td valign = 'top' width = '1%'>
		</td>
		<td valign = 'top' width = '16%' align = 'center'>
			<font size = '3' color = 'black'>
			Service Type
			</font>
				<table width = '100%'>
						<tr>
							<td valign = 'top' width = '18%' align = 'center'>
							<font color = 'brown' size = '1'>
							monster
							</font>
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' width = '18%' align = 'center'>
							<font color = 'black' size = '1'>
							setup
							</font>
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' width = '18%' align = 'center'>
							<font color = 'blue' size = '1'>
							staging
							</font>
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' width = '18%' align = 'center'>
							<font color = 'green' size = '1'>
							kitchen
							</font>
							</td>
							<td width = '2%'>
							</td>
							<td valign = 'top' width = '18%' align = 'center'>
							<font color = 'purple' size = '1'>
							cleanup
							</font>
							</td>
							<td width = '2%'>
							</td>
						</tr>
					</table>
		</td>
		<td valign = 'top' width = '1%'>
		</td>
		<td valign = 'top' width = '10%' align = 'center'>
			<font size = '3' color = 'black'>
			Start
			</font>
		</td>
		<td valign = 'top' width = '10%' align = 'center'>
			<font size = '3' color = 'black'>
			End
			</font>
		</td>
		<td valign = 'top' width = '1%'>
		</td>
		<td valign = 'top' width = '10%' align = 'center'>
			<font size = '3' color = 'black'>
			Staff Initials
			</font>
		</td>
	</tr>
	<tr>
		<td valign = 'top' colspan = '8' align = 'center'>
			<hr>
		</td>
	</tr>
";

$active_char_count_total = 0;

while($active_char_count_total <= 40)
{
	echo"
		<tr>
			<td valign = 'top' align = 'right' width = '20%'>
				<font size = '3' color = 'black'>
				___________________________
				</font>
			</td>
			<td valign = 'top' width = '1%'>
			</td>
			<td valign = 'top' width = '16%' align = 'center'>
			<table width = '100%'>
					<tr>
						<td valign = 'top' width = '18%' align = 'center'>
						<font color = 'brown' size = '1'>
						_____
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td valign = 'top' width = '18%' align = 'center'>
						<font color = 'black' size = '1'>
						_____
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td valign = 'top' width = '18%' align = 'center'>
						<font color = 'blue' size = '1'>
						_____
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td valign = 'top' width = '18%' align = 'center'>
						<font color = 'green' size = '1'>
						_____
						</font>
						</td>
						<td width = '2%'>
						</td>
						<td valign = 'top' width = '18%' align = 'center'>
						<font color = 'purple' size = '1'>
						_____
						</font>
						</td>
						<td width = '2%'>
						</td>
					</tr>
				</table>	
			</td>
			<td valign = 'top' width = '1%'>
			</td>
			<td valign = 'top' width = '10%' align = 'center'>
				<font size = '3' color = 'black'>
				_______________
				</font>
			</td>
			<td valign = 'top' width = '10%' align = 'center'>
				<font size = '3' color = 'black'>
				_______________
				</font>
			</td>
			<td valign = 'top' width = '1%'>
			</td>
			<td valign = 'top' width = '10%' align = 'center'>
				<font size = '3' color = 'black'>
				_______________
				</font>
			</td>
		</tr>
	";

	$active_char_count_total++;
}

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>