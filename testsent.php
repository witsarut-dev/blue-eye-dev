<?php
echo "testssent";

$ch = curl_init();

$text = str_replace('%','',"ดีนะจ๊ะ");

$key  = "17f363eb609961ebb0df94ea616f7736";

// curl_setopt($ch, CURLOPT_URL,"http://sansarn.com/api/ssense-v2.php");
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS,"text={$text}&key={$key}");
curl_setopt($ch, CURLOPT_URL,"http://128.199.201.187/SSenseV2/Analyze");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"q={$text}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close ($ch);

$obj = json_decode($output,true);
print_r($obj);

?>
