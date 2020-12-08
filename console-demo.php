<?php

require 'vendor/autoload.php';

use Afzafri\ABXExpressTrackingApi;

if (isset($argv[1])) {
	print_r(ABXExpressTrackingApi::crawl($argv[1]));
} else {
	echo "Usage: " . $argv[0] . " <Tracking code>\n";
}