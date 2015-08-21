<?php
require 'vendor/autoload.php';
use GifTimer\GifTimer;

$last_date = strtotime("+15 minutes");

$gt = new GifTimer($last_date);
$image  = $gt->get();

header('Content-type: image/gif');
header('Content-Disposition: filename="timer.gif"');
echo $image;
unset($image);
exit;