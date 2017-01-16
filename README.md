# GIF Timer
Create GIF countdown timer image. Purpose of this file is to create a GIF animation to show countdown timer based on parameters.
## Usage
Use `composer install` to install the dependancies. Then you can create the image like this:

```php
use GifTimer\GifTimer;
$uptime = strtotime("+15 minutes");
$gt = new GifTimer($uptime);
$image  = $gt->get();
```
You can then output the file to browser:
```php
header('Content-type: image/gif');
header('Content-Disposition: filename="timer.gif"');
echo $image;
unset($image);
exit;
```
Or write the file into file system.
```php
file_put_contents('/timer.gif', $image);
```
## Demo
[ashiqur.com](http://ashiqur.com/works/gif-timer/index.php)

## Parameters
* **$end_time**
  * int time in second defines the destination time.
* $start_time
  * (optional) int time in second from which the counter starts.
  * default: `time()`
* $step
  * (optional) int number of seconds for each step of timer.
  * default: 1
* $width
  * (optional) int width of the gif image.
  * default: 400
* $height
  * (optional) int height of the gif image.
  * default: 80
* $font_size
  * (optional) int default font size.
  * default: 20
* $font
  * (optional) string path to the font.
  * default: `/font/OpenSans-Light.ttf`
* $bg_color
  * (optional) string background color in hex.
  * default: 3D3D3D
* $last_bg_color
  * (optional) string defines a color for the last frame in hex.
  * default: 3D3D3D
* $color
  * (optional) string text color in hex.
  * default: FFFFFF

## Dependancy
[GIF Creator from Sybio](https://github.com/Sybio/GifCreator)
