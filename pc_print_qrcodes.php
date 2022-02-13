<?php
if (!eregi("modules.php", $PHP_SELF))
{
	die ("You can't access this file directly...");
}

$index = 1;
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

include("modules/$module_name/includes/slurp_qr_header.php");
include("modules/$module_name/includes/phpqrcode/qrlib.php");
$pc_temp = $_POST['pc_to_print'];
include("modules/$module_name/includes/pcinfo.php");

echo"
<tr height='192'>
	<td width='334' align = 'center' valign = 'middle' background='themes/Vanguard/images/vanguard_QR_bg.gif'>
		Print $curpcnfo[creature]
		<br>
";
	
$tempDir = "images/";	
$fileName = "qrcode_local_$curpcnfo[creature].png";
$pngAbsoluteFilePath = $tempDir.$fileName;

if(!file_exists($pngAbsoluteFilePath))
{
	$codeContents = "http://localhost/modules.php?name=My_Vanguard&file=pc_print_page&pc_to_print=$curpcnfo[creature_id]&verbose=1";
	// $urlRelativeFilePath = EXAMPLE_TMP_URLRELPATH.$fileName;
	QRcode::png($codeContents, $pngAbsoluteFilePath); 
}

echo"
		<img src='$pngAbsoluteFilePath' title='Print $curpcnfo[creature]' height='172' width='172'>
	</td>
		<td width = '334' align = 'center' valign = 'middle' background='themes/Vanguard/images/vanguard_QR_bg.gif'>
			Print $curpcnfo[creature] Recoveries
			<br>
	";
		
	$rectempDir = "images/";	
	$recfileName = "qrcode_local_$curpcnfo[creature]_rec.png";
	$recpngAbsoluteFilePath = $rectempDir.$recfileName;
	
	if(!file_exists($recpngAbsoluteFilePath))
	{
		$reccodeContents = "http://localhost/modules.php?name=My_Vanguard&file=pc_print_rec&pc_to_print=$curpcnfo[creature_id]";
		// $urlRelativeFilePath = EXAMPLE_TMP_URLRELPATH.$fileName;
		QRcode::png($reccodeContents, $recpngAbsoluteFilePath); 
	}
	
	echo"
			<img src='$recpngAbsoluteFilePath' title='$curpcnfo[creature] Recoveries' height='172' width='172'>
		</td>
</tr>
<tr height = '9'>
	<td colspan = '2'>
	
	</td>
</tr>
<tr>
	<td colspan = '2' align='center' valign='bottom'>
	Set your default browser font to Garamond 14.
	<br>
	Set your browser print function to 80% scale.
	<br>
	Print the resulting page from your browser.
	<br>
	Cut out the two panes above and fold in half.
	<br>
	It should be around the size of a business card.
	<br>
	Discard this portion of the printed page.
	<br>
	When you arrive at the game, present the resulting card
	<br>
	to Logistics after paying, to print your stuff quickly.
	<br>
	And don't worry if you forget.
	<br>
	We can still get your character printed;
	<br>
	it just might take a few seconds more.  ;-}
	</td>
</tr>
";
// include("modules/$module_name/includes/slurp_footer.php");
// require("footer.php");
?>