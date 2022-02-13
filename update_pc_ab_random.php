<?php

// $random_update_abilities = mysql_query("SELECT * FROM dom_ability WHERE ability_id > '1' ORDER BY ability");

// use this one for PC ability random key updates.
$random_update_abilities = mysql_query("SELECT * FROM dom_object_random INNER JOIN dom_pc_ability ON dom_pc_ability.ability_id = dom_object_random.object_id WHERE dom_object_random.object_focus_id = '2' AND dom_object_random.object_random_current = '1' AND dom_pc_ability.ability_random_id != '0'") or die ("failed getting pc abilities");

while($rndupdab = mysql_fetch_array($random_update_abilities, MYSQL_NUM))
{
	echo"<hr>$rndupdab[1], $rndupdab[3]";
	// echo"Sum: $rndstrsum<br>";
	$random_update = mysql_query("UPDATE dom_pc_ability SET ability_random_id='$rndupdab[0]' WHERE ability_id = '$rndupdab[1]'");
	// $random_tracker = mysql_query("INSERT INTO dom_object_random (object_id,object_random,object_focus_id,object_slurp_id) VALUES ('$rndupdab[0]','$rndstrsum','2','2')");
	
	$get_new_random = mysql_query("SELECT * FROM dom_object_random WHERE object_id = '$rndupdab[0]' AND object_random_current = '1' AND object_focus_id = '2' AND object_slurp_id = '2'") or die ("failed getting new random.");
	$gtnwrnd = mysql_fetch_array($get_new_random, MYSQL_NUM);
	
	// echo"rnd_id: $gtnwrnd[0]: $gtnwrnd[3] ($gtnwrnd[4])<br>";
	
	$pc_ability_random = mysql_query("UPDATE dom_pc_ability SET ability_random_id = '$gtnwrnd[0]' WHERE ability_id = $rndupdab[0]'") or die ("failed updating pc ability random.");
}

?> 