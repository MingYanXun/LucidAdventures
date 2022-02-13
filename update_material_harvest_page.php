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
$nav_title = "Material Harvest Update";
include("modules/$module_name/includes/slurp_header.php");

// counts the difference in days given two Y-m-d values
function day_count($date1, $date2)
{
    $current = $date1;
    $datetime2 = date_create($date2);
    $count = 0;
    while(date_create($current) < $datetime2){
        $current = gmdate("Y-m-d", strtotime("+1 day", strtotime($current)));
        // echo"current: $current<br>";
        $count++;
    }
    return $count;
}

if($event_period == 0)
{
	// also clear event dates (more for testing purposes)
	$clear_period_no = 1;
	while($clear_period_no <= 5)
	{
		$clear_harvest_periods = mysql_query("UPDATE ".$slrp_prefix."event SET event_harvest_reset_date_".$clear_period_no." = '' WHERE event_id = '$pgame[event_id]'") or die ("failed clearing harvest start dates for $pgame[event].");
		$clear_period_no++;
	}
	
	// random number of harvesting periods, plus one; EX: (2,4) makes between 3 and 5
	$hrvperiod = rand(2,4);
	$hrvperiod_true = ($hrvperiod+1);
	// plain language delay after game start; include +/-
	// assumes game start is 9PM Friday, placing the end at midnight the following Sunday
	$game_length = "+2 Days 3 Hours";
	//  add the game length and format for use
	$event_start = new DateTime($pgame[event_date]);
	$event_start->modify($game_length);
	$event_end = date_format($event_start, 'Y-m-d');
	$event_end_full = date_format($event_start, 'Y-m-d H:i:s');
	//insert the game end into the event table
	$create_first_harvest_start = mysql_query("UPDATE ".$slrp_prefix."event SET event_harvest_reset_date_1 = '$event_end_full' WHERE event_id = '$pgame[event_id]'") or die ("failed setting game end for harvest start.");
	// format the last possible date for the downtime window
	$db_cutoff = new DateTime($pgame[event_database_lock_date]);
	$db_cutoff = date_format($db_cutoff, 'Y-m-d');
	// calculate downtime in days, and divide by periods
	$downtime = day_count($event_end, $db_cutoff);
	$period_avg = round($downtime/$hrvperiod_true);
	
	// uncomment to check
	//echo"DB Cutoff: $db_cutoff . . . days of downtime: $downtime . . .	$period_no Period avg: $period_avg<br>Period 1 start $event_end_full<br>";
	
	$period_start = new DateTime($pgame[event_date]);
	$period_start->modify($game_length);
	$period_no = 1;
	
	// add time for each period after the game end
	while($hrvperiod >= $period_no)			
	{
		$period_days = "+$period_avg Days";
		$period_start->modify($period_days);
		$operator = rand(0,1);
		if($operator == 1)
		{
			$sign = "+";
		}
		if($operator == 0)
		{
			$sign = "-";
		}
		// vary it by up to 2 days, 30 minutes or as little as 3 hours +/-
		$random_hours = rand(3,48);
		$random_minutes= rand(0,30);
		// plain language adjustment and format for use
		$current_period = "$sign"."$random_hours Hours $random_minutes Minutes";
		// echo"$period_days $current_period<br>";
		$period_start->modify($current_period);
		$hrv_start = date_format($period_start, 'Y-m-d H:i:s');
		//increment the period#
		$period_no++;
		// echo"Period $period_no start $hrv_start<br>";
		// set each field with the matching period start date
		$create_harvest_period = mysql_query("UPDATE ".$slrp_prefix."event SET event_harvest_reset_date_".$period_no." = '$hrv_start' WHERE event_id = '$pgame[event_id]'") or die ("failed setting game end for harvest start.");
	}
	
	// update the flag to period 1
	$adjust_harvest_period = mysql_query("UPDATE ".$slrp_prefix."event SET event_harvest_reset_flag = '1' WHERE event_id = '$pgame[event_id]'") or die ("failed setting game end for harvest start.");
	// set each field with the matching period start date
	$check_harvest_period = mysql_query("SELECT * FROM ".$slrp_prefix."event WHERE event_id = '$pgame[event_id]'") or die ("failed getting game info for harvest start.");
	$chkhrvpd = mysql_fetch_assoc($check_harvest_period);
}

if($event_period >= 1)
{
	// depends on variable $pgame, $now from /includes/slurp_game.php
	// set the harvest period flag and header display variable $last_hrv_period
	if($pgame[event_harvest_reset_date_1] <= $now)
	{
		if($pgame[event_harvest_reset_date_2] <= $now)
		{
			if($pgame[event_harvest_reset_date_3] <= $now)
			{
				if($pgame[event_harvest_reset_date_4] <= $now)
				{
					if($pgame[event_harvest_reset_date_5] <= $now)
					{
						if($pgame[event_harvest_reset_flag] < 5)
						{
							$last_hrv_period = $pgame[event_harvest_reset_date_5];
							include("modules/$module_name/includes/new_material_harvest.php");
							$adjust_harvest_period = mysql_query("UPDATE ".$slrp_prefix."event SET event_harvest_reset_flag = '5' WHERE event_id = '$pgame[event_id]'") or die ("failed setting harvest reset flag.");
						}
					}
					if($pgame[event_harvest_reset_date_5] > $now)
					{
						if($pgame[event_harvest_reset_flag] < 4)
						{
							$last_hrv_period = $pgame[event_harvest_reset_date_4];
							include("modules/$module_name/includes/new_material_harvest.php");
							$adjust_harvest_period = mysql_query("UPDATE ".$slrp_prefix."event SET event_harvest_reset_flag = '4' WHERE event_id = '$pgame[event_id]'") or die ("failed setting harvest reset flag.");
						}
					}
				}
				if($pgame[event_harvest_reset_date_4] > $now)
				{
					if($pgame[event_harvest_reset_flag] < 3)
					{
						$last_hrv_period = $pgame[event_harvest_reset_date_3];
						include("modules/$module_name/includes/new_material_harvest.php");
						$adjust_harvest_period = mysql_query("UPDATE ".$slrp_prefix."event SET event_harvest_reset_flag = '3' WHERE event_id = '$pgame[event_id]'") or die ("failed setting harvest reset flag.");
					}
				}
			}
			if($pgame[event_harvest_reset_date_3] > $now)
			{
				if($pgame[event_harvest_reset_flag] < 2)
				{
					$last_hrv_period = $pgame[event_harvest_reset_date_2];
					include("modules/$module_name/includes/new_material_harvest.php");
					$adjust_harvest_period = mysql_query("UPDATE ".$slrp_prefix."event SET event_harvest_reset_flag = '2' WHERE event_id = '$pgame[event_id]'") or die ("failed setting harvest reset flag.");
				}
			}
		}
		if($pgame[event_harvest_reset_date_2] > $now)
		{
			$last_hrv_period = $pgame[event_harvest_reset_date_1];
			include("modules/$module_name/includes/new_material_harvest.php");
			$adjust_harvest_period = mysql_query("UPDATE ".$slrp_prefix."event SET event_harvest_reset_flag = '1' WHERE event_id = '$pgame[event_id]'") or die ("failed setting harvest reset flag.");
		}
	}
}


include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>