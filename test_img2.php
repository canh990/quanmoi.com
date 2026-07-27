<?php
require 'vendor/autoload.php';

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

$manager = new ImageManager(new Driver());
$img = $manager->createImage(100, 100);

$encoded = $img->encodeUsingFileExtension('webp', 80);
echo "ENCODED CLASS: " . get_class($encoded) . "\n";
echo "ENCODED SIZE: " . strlen((string)$encoded) . "\n";
