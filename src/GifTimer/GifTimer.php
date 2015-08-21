<?php
/**
 * GifTimer.php
 *
 * @package: gif-timer.
 * @author: Ashiqur Rahman
 * @link: http://ghumkumar.com
 */

namespace GifTimer;
use GifCreator\GifCreator;
use DateTime;

class GifTimer {

	/**
	 * @var int time in second defines the destination time.
	 */
	private $end_time;

	/**
	 * @var int time in second from which the counter starts.
	 */
	private $start_time;

	/**
	 * @var int number of seconds for each step of timer.
	 */
	private $step;

	/**
	 * @var int width of the gif image.
	 */
	private $width;

	/**
	 * @var int height of the gif image.
	 */
	private $height;

	/**
	 * @var int default font size.
	 */
	private $font_size;

	/**
	 * @var string path to the font.
	 */
	private $font;

	/**
	 * @var string background color in hex.
	 */
	private $bg_color;

	/**
	 * @var string defines a color for the last frame in hex.
	 */
	private $last_bg_color;

	/**
	 * @var string text color in hex.
	 */
	private $color;

	/**
	 * @var array stores the frames of the animated gif.
	 */
	private $frames;

	/**
	 * @var array duration of each frame.
	 */
	private $durations;

	/**
	 * @var resource stores the final gif image.
	 */
	private $gif;

	public function __construct(
		$end_time,
		$start_time = 0,
		$step = 1,
		$width = 400,
		$height = 80,
		$font_size = 20,
		$font = '',
		$bg_color = '3D3D3D',
		$last_bg_color = '******',
		$color = 'FFFFFF'
	) {
		if(!$end_time || $end_time <= $start_time) {
			throw new \Exception('End time need to be larger than the start time');
		}

		if(strlen($bg_color) !== 6 || strlen($last_bg_color) !== 6 || strlen($color) !== 6) {
			throw new \Exception('Please provide hexadecimal values for color (e.g. FFFFFF)');
		}

		$this->end_time = $end_time;
		$this->start_time = ($start_time == 0 ? time() : $start_time);
		$this->step = $step;
		$this->width = $width;
		$this->height = $height;
		$this->font_size = $font_size;

		if(strlen($font) > 0) {
			$this->font = $font;
		} else {
			$this->font = __DIR__ . '/font/OpenSans-Light.ttf';
		}

		$this->bg_color[] = '0x'.substr($bg_color, 0, 2);
		$this->bg_color[] = '0x'.substr($bg_color, 2, 2);
		$this->bg_color[] = '0x'.substr($bg_color, 4, 2);

		if($last_bg_color == '******') {
			$this->last_bg_color = $this->bg_color;
		} else {
			$this->last_bg_color[] = '0x'.substr($last_bg_color, 0, 2);
			$this->last_bg_color[] = '0x'.substr($last_bg_color, 2, 2);
			$this->last_bg_color[] = '0x'.substr($last_bg_color, 4, 2);
		}

		$this->color[] = '0x'.substr($color, 0, 2);
		$this->color[] = '0x'.substr($color, 2, 2);
		$this->color[] = '0x'.substr($color, 4, 2);

		$this->frames = array();
		$this->gif = null;

		$this->render_gif();
	}

	/**
	 * Generates the gif file.
	 * @throws \Exception
	 */
	private function render_gif() {

		$diff = $this->end_time - $this->start_time;
		$now = $this->start_time;

		for($diff; $diff >= 0; $diff -= $this->step) {

			$dtF = new DateTime("@$now");
			$dtT = new DateTime("@$this->end_time");
			$text = $dtF->diff($dtT)->format('%a D / %h H / %i M / %s S');

			$last_frame = false;
			if($diff == 0) {
				$last_frame = true;
			}

			$this->frames[] = $this->render_frame($text, $last_frame);

			if($diff == 0) {
				$this->durations[] = -1;
			} else {
				$this->durations[] = 100 * $this->step;
			}

			$now += $this->step;
		}

		$gc = new GifCreator();
		$gc->create($this->frames, $this->durations, 0);
		$this->gif = $gc->getGif();
	}

	/**
	 * Renders each frame of the gif image
	 * @param $text string the text to display on the frame
	 * @param $last_frame bool whether this is the last frame
	 *
	 * @return resource image frame
	 */
	private function render_frame($text, $last_frame) {

		$image = ImageCreate($this->width, $this->height);

		$background = ImageColorAllocate($image, $this->bg_color[0], $this->bg_color[1], $this->bg_color[2]);

		if($last_frame) {
			$background = ImageColorAllocate($image, $this->last_bg_color[0], $this->last_bg_color[1], $this->last_bg_color[2]);
		}

		$color = ImageColorAllocate($image, $this->color[0], $this->color[1], $this->color[2]);

		ImageFilledRectangle($image, 1, 1, $this->width-1, $this->height-1, $background);

		$size = ImageTTFBBox($this->font_size, 0, $this->font, $text);
		$X = ($this->width - (abs($size[2]- $size[0])))/2;
		$Y = (($this->height - (abs($size[5] - $size[3])))/2 + (abs($size[5] - $size[3])));

		ImageTTFText($image, $this->font_size, 0, ($X-1), $Y, $color, $this->font, $text);
		return $image;
	}

	/**
	 * Returns the gif image
	 * @return null|resource
	 */
	public function get() {
		return $this->gif;
	}
}