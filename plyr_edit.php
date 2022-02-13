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
include("modules/$module_name/includes/slurp_header.php");

if(isset($_POST['current_expander']))
{
	$expander_abbr = $_POST['current_expander'];
	$expander = ($expander_abbr."_expander");
}
// echo"exp: $expander_abbr, $expander<br>";

if(isset($_POST['new_slurp_user']))
{
	$slurp_user = $_POST['new_slurp_user'];
	$slurp_user_rank = $_POST['new_slurp_user_rank'];
}

if(isset($_POST['edit_slurp_user']))
{
	$slurp_user = $_POST['edit_slurp_user'];
	$slurp_user_rank = $_POST['edit_slurp_user_rank'];
}

// echo"post: $new_slurp_user<br>$new_slurp_user_rank<br>";

$verify_existing_user = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_user_rank WHERE user_id = '$slurp_user'") or die ("failed to verify existing user.");
$verexusrcnt = mysql_num_rows($verify_existing_user);

// if so, inform the user
if($verexusrcnt >= 1)
{	
	//  since it exists, update
	$update_slurp_user = mysql_query("UPDATE ".$slrp_prefix."slurp_user_rank SET slurp_rank_id = '$slurp_user_rank' WHERE user_id = '$slurp_user'") or die ("failed inserting new slurp user.");
	$verexusr = mysql_fetch_assoc($verify_existing_user);
	$verexusr_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id = '$slurp_user_rank'") or die ("failed getting existing user rank.");
	$verexusrrnk = mysql_fetch_assoc($verexusr_rank);

	$nuke_user = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$verexusr[user_id]'") or die ("failed getting nuke user.");
	$nkusr = mysql_fetch_assoc($nuke_user);

	echo"
	<tr>
	<td colspan = '7' align = 'left' valign = 'top'>
	<font color = 'yellow' size = '2'>
	<li>$nkusr[username] has a rank of <i>$verexusrrnk[slurp_rank]</i> in the game <i>$slrpnfo[slurp_name]</i>.
	</font>
	<hr>
	</td>
	</tr>
	";
}

// if not, insert and verify
if($verexusrcnt == 0)
{
	// for everything but creatures and object types, follow the default insert of just a name for now
	$insert_slurp_user = mysql_query("INSERT INTO ".$slrp_prefix."slurp_user_rank (user_id,slurp_rank_id) VALUES ('$slurp_user','$slurp_user_rank')") or die ("failed inserting new slurp user.");
	
	$verify_new_slurp_user = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_user_rank WHERE user_id = '$new_slurp_user' AND slurp_rank_id = '$new_slurp_user_rank'") or die ("failed verifying inserted slurp user.");
	$vrnwslrpusrcnt = mysql_num_rows($verify_new_slurp_user);
	
	// echo"ver: $vrnwslrpusr[1]<br>";
	
	// if it inserted correctly, set it as the default variable
	if($vrnwslrpusrcnt >= 1)
	{
		$vrnwslrpusr = mysql_fetch_assoc($verify_new_slurp_user);
		
		$verexusr_rank = mysql_query("SELECT * FROM ".$slrp_prefix."slurp_rank WHERE slurp_rank_id = '$vrnwslrpusr[slurp_rank_id]'") or die ("failed getting existing user rank.");
		$verexusrrnk = mysql_fetch_assoc($verexusr_rank);
		
		$nuke_user = mysql_query("SELECT * FROM nuke_users WHERE user_id = '$vrnwslrpusr[user_id]'") or die ("failed getting nuke user.");
		$nkusr = mysql_fetch_assoc($nuke_user);
		
		echo"
		<tr>
		<td colspan = '7' = 'left' valign = 'top'>
		<font color = 'yellow' size = '2'>
		<li>$nkusr[username] has a rank of <i>$verexusrrnk[slurp_rank]</i> in the game <i>$slrpnfo[slurp_name]</i>.
		</font>
		<hr>
		</td>
		</tr>
		";
		
		$edit_player_id = $vrnwslrpusr[user_id];
	}
	
	if($vrnwslrpusrcnt == 0)
	{
		echo"
		<tr>
		<td colspan = '7' = 'left' valign = 'top'>
		<font color = 'red' size = '2'>
		<li> The player was not created from the users list. Please try again or contact an admin if there is a problem.
		</font>
		<hr>
		</td>
		</tr>
		";
	}
}


echo"
<tr>

<td colspan = '9'>
<hr width = '100%'>
<form name = 'home' method='post' action = 'modules.php?name=$module_name'>
<input type='hidden' value='1' name='plyr_expander'>
<input type='hidden' value='$curpcnfo[creature_id]' name='current_pc_id'>
<input type='submit' value='Back to Main' name='go_home'>
</form>
</td>

</tr>
";

include("modules/$module_name/includes/slurp_footer.php");
require("footer.php");
?>