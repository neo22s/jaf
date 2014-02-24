<?php
/**
* Image class file.
*
* @author Federico Ramirez <fedekiller@gmail.com>
* @link http://code.google.com/p/akaikiwi/
* @copyright Copyright &copy; 2008-2011 Federico Ramirez
* @license http://www.opensource.org/licenses/mit-license.php MIT Licence
*/

/*
* Image library
*/
class Image
{

	private $lib;
	private $src;
	private $output;
	private $width, $height;

	function __construct() {
		$this->lib = 'gd';
		$this->src = NULL;
		$this->output = 'JPEG';
		$this->width = 0;
		$this->height = 0;
	}

	/**
	* Set the current working library, default is GD
	* @param String $lib
	*/
	public function set_lib($lib = 'gd') {
		switch($lib) {
			case 'gd':
				$this->lib = $lib;
				break;
			default:
				trigger_error('Invalid library');
				break;
		}
	}

	/**
	* Set the output image format
	* @param String $output The format [GIF|JPG|PNG]
	*/
	public function set_output($output) {
		$this->output = $output;
	}

	/**
	* Set the source image to work on, it must be an existing image.
	* @param String $src Relative path to an image
	*/
	public function set_src($src) {
		if(file_exists($src)) {
			if($this->lib == 'gd') {
				if(!function_exists('gd_info')) {
					trigger_error('The specified library ('.
						$this->lib.') is not available.');
				}

				$data = getimagesize($src);
				$this->width = $data[0];
				$this->height = $data[1];
				$type = $data['mime'];

				$info = gd_info();
				
				if(	strpos(strtolower($type), 'jpg') !== FALSE 
					|| strpos(strtolower($type), 'jpeg') !== FALSE
				) {
					if(!$info['JPEG Support']) {
						trigger_error('GD does not have JPG 
							support.');
					}
					
					$this->src = imagecreatefromjpeg($src);
				} else if(strpos(strtolower($type), 'gif') !== FALSE) {
					if(!$info['GIF Read Support']) {
						trigger_error('GD does not have GIF read 
							support.');
					}
					$this->src = imagecreatefromgif($src);
				} else if(strpos(strtolower($type), 'png') !== FALSE) {
					if(!$info['PNG Support']) {
						trigger_error('GD does not have PNG 
							support.');
					}
					
					$this->src = imagecreatefrompng($src);
				} else {
					trigger_error('Invalid source image, invalid 
						file type: ' . $type);
				}
			} else {
				trigger_error('Invalid library');
			}
		} else {
			trigger_error('Invalid source image, image not
				found.');
		}
	}

	/**
	* Creates an image to work with
	* @param int $with The width of the image in pixels
	* @param int $height The height of the image in pixels
	* @param Array $color An array containing the RGB color, 
	* array(255, 0, 0) will do red
	*/
	public function create_src($width, $height, $color) {
		if(!is_array($color) || count($color) != 3) {
			trigger_error('Invalid color format.');
		}
		
		if($this->lib == 'gd') {
			if(!function_exists('gd_info')) {
				trigger_error('The specified library ('.$this->lib
					.') is not available.');
			}

			$this->src = imagecreatetruecolor($width, $height);
			$bg = imagecolorallocate($this->src, $color[0], $color[1], 
				$color[2]);
			imagefilltoborder($this->src, 0, 0, $bg, $bg);
		} else {
			trigger_error('Invalid library');
		}
	}

	/**
	* Creates an image with the current destiny image, whether created or loaded
	* @param String $dst If a path is specified it will save the image there and
	* return NULL, if a path isn't specified it will return the image binary
	* data.
	*/
	public function get_src($dst = NULL) {
		return $this->create_image($this->src, $dst);
	}

	/**
	* Resizes the current destiny image.
	* @param int $width The desired width in pixels
	* @param int $height The desired height in pixels
	* @param String $dst If a path is specified it will save the image there and 
	* return NULL, if a path isn't specified it will return the image binary 
	* data.
	*/
	public function resize($width, $height, $dst = NULL) {
		if($this->src == NULL) {
			trigger_error('Invalid source file: null.');
		}
		
		$r = NULL;
		
		if($this->lib == 'gd') {
			$base = imagecreatetruecolor($width, $height);
			imagecopyresized($base, $this->src, 0, 0, 0, 0, $width, $height, 
				$this->width, $this->height);

			$r = $this->create_image($base, $dst);

			imagedestroy($base);
		} else {
			trigger_error('Invalid library');
		}

		return $r;
	}

	/**
	* Crop an image
	* @param int $src_x Source x location in pixels
	* @param int $src_y Source y location in pixels
	* @param int $src_width Source width in pixels
	* @param int $src_height Source height in pixels
	* @param int $dst_width Destiny image width in pixels
	* @param int $dst_height Destiny image height in pixels
	* @param String $dst If a path is specified it will save the image there 
	* and return NULL, if a path isn't specified it will return the image binary
	* data.
	*/
	public function crop($src_x, $src_y, $src_width, $src_height, $dst_width, 
		$dst_height, $dst = NULL
	) {
		if($this->src == NULL) {
			trigger_error('Invalid source file: null.');
		}
		
		$r = NULL;
		
		if($this->lib == 'gd') {
			$base = imagecreatetruecolor($dst_width, $dst_height);
			imagecopyresized($base, $this->src, 0, 0, $src_x, $src_y, 
				$dst_width, $dst_height, $src_width, $src_height);

			$r = $this->create_image($base, $dst);

			imagedestroy($base);
		} else {
			trigger_error('Invalid library');
		}

		return $r;
	}

	/**
	* Rotates an image with the given angle
	* @param int $angle The angle to rotate the image, in grades
	* @param String $dst If a path is specified it will save the image there and 
	* return NULL, if a path isn't specified it will return the image binary
	* data.
	*/
	public function rotate($angle, $dst = NULL) {
		//@TODO: agregar lib check
		if($this->src == NULL) {
			trigger_error('Invalid source file: null.');
		}

		$r = NULL;

		if($this->lib == 'gd') {		
			$base = imagecreatetruecolor($this->width, $this->height);
			imagecopy($base, $this->src, 0, 0, 0, 0, $this->width,
				$this->height);
			$base = imagerotate($base, $angle, 0);
			
			$r = $this->create_image($base, $dst);
	
			imagedestroy($base);
		} else {
			trigger_error('Invalid library');
		}

		return $r;
	}

	public function watermark($mark, $x, $y) {
		//@TODO
	}

	public function write($string, $x, $y, $size = 5, $color = array(0, 0, 0), 
		$dst = NULL
	) {
		//@TODO: agregar lib check
		if($this->src == NULL) {
			trigger_error('Invalid source file: null.');
		}

		if(!is_array($color) || count($color) != 3) {
			trigger_error('Invalid color format.');
		}

		$r = NULL;

		if($this->lib == 'gd') {
			$base = imagecreatetruecolor($this->width, $this->height);
			imagecopy($base, $this->src, 0, 0, 0, 0, $this->width,
				$this->height);
			$color = imagecolorallocate($base, $color[0], $color[1], $color[2]);
			imagestring($base, $size, $x, $y, $color);

			$r = $this->create_image($base, $dst);
		} else {
			trigger_error('Invalid library');
		}

		return $r;
	}

	public function write_ttf($size, $angle, $x, $y, $color, $font, $text) {
		if($this->src == NULL) {
			trigger_error('Invalid source file: null.');
		}

		if(!is_array($color) || count($color) != 3) {
			trigger_error('Invalid color format.');
		}

		if($this->lib == 'gd') {
			$col = imagecolorallocate($this->src, $color[0], $color[1], 
				$color[2]);
			imagettftext($this->src, $size, $angle, $x, $y, $col, $font, $text);
		} else {
			trigger_error('Invalid library');
		}
	}

	private function create_image($base, $dst = NULL) {
		$r = NULL;
		
		if($this->lib == 'gd') {
			$f = 'imagejpeg';
			if($this->output == 'PNG') {
				$f = 'imagepng';
			} else if($this->output == 'GIF') {
				$f = 'imagegif';
			}

			if($dst == NULL) {
				$r = call_user_func($f, $base);
			} else {
				call_user_func($f, $base, $dst);
			}
		} else {
			trigger_error('Invalid library');
		}

		return $r;
	}
}