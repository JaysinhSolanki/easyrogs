
<?php
//
// A very simple PHP example that sends a HTTP POST to a remote site
//

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://test.gumption.pk/jeff/phase3/system/application/makepdf.php?id=G4VMN67FwfxyvQdX&downloadORwrite=1&view=0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close ($ch);

// Further processing ..
echo $server_output;
echo "<br>";
if ($server_output == "OK") { echo "Done"; } else { echo "Error"; }
?>

