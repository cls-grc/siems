<?php
$dir = __DIR__ . '/assets/img';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$gcash = @file_get_contents("https://logo.clearbit.com/gcash.com?size=200");
$maya  = @file_get_contents("https://logo.clearbit.com/maya.ph?size=200");
$card  = @file_get_contents("https://logo.clearbit.com/mastercard.com?size=200");

if(!$gcash) {
    // fallback if clearbit blocks curl wrapper
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $gcash = @file_get_contents("https://logo.clearbit.com/gcash.com?size=200", false, $context);
    $maya = @file_get_contents("https://logo.clearbit.com/maya.ph?size=200", false, $context);
    $card = @file_get_contents("https://logo.clearbit.com/mastercard.com?size=200", false, $context);
}

if ($gcash) file_put_contents("$dir/gcash.png", $gcash);
if ($maya) file_put_contents("$dir/maya.png", $maya);
if ($card) file_put_contents("$dir/card.png", $card);

echo file_exists("$dir/gcash.png") ? "Success gcash. " : "Fail gcash. ";
echo file_exists("$dir/maya.png") ? "Success maya." : "Fail maya.";
?>
