<?php
declare(strict_types=1);

/**
 * CakePHP 4.x User Management Plugin
 * Copyright (c) Chetan Varshney (The Director of Ektanjali Softwares Pvt Ltd), Product Copyright No- 11498/2012-CO/L
 *
 * Licensed under The GPL License
 * For full copyright and license information, please see the LICENSE.txt
 *
 * Product From - https://ektanjali.com
 * Product Demo - https://cakephp4-user-management.ektanjali.com
 */

namespace Usermgmt\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

class ImageHelper extends Helper {
	public $defaultImagePath = DEFAULT_IMAGE_PATH;

	/**
	 * Automatically resizes an image
	 *
	 * @param string $directoryPath Path to the directory, relative to the webroot directory.  for ex "img" or "img/myphotos"
	 * @param string $image image name for ex abc.jpg or with dir myphotos/abc.jpg
	 * @param array options array
			width: pass required width
			height: pass required height
			aspect: returned image in aspect ratio or not, do not work with when crop is true
			crop: returned image with croped position
			cropPosition: position from where image should be cropped, possible values 'center', 'top', 'top-center', 'left', 'right', 'bottom'
			allowEnlarge: if true and required image width is greater than original width or required image height is greater than original height then returned image will be stretched
	 * @access public
	 */
	public function resize($directoryPath, $image, $options=[]) {
		$default = ['width'=>null, 'height'=>null, 'aspect'=>false, 'crop'=>false, 'cropPosition'=>'center', 'allowEnlarge'=>false];
		$cropPositions = ['top'=>1, 'center'=>2, 'bottom'=>3, 'left'=>4, 'right'=>5, 'top-center'=>6];

		if(!is_array($options)) {
			throw new \Exception('options is not an array');
		}

		$options = $options + $default;

		$cachedir = 'imagecache';
		
		$directoryPath = str_replace("/", DS, str_replace("\\", DS, $directoryPath));
		$image = str_replace("/", DS, str_replace("\\", DS, $image));
		
		$directoryFullPath = WWW_ROOT.$directoryPath.DS;
		$imageFullPath = $directoryFullPath.$image;

		$directoryUrl = SITE_URL.str_replace(DS, '/', $directoryPath).'/';
		
		if(!file_exists($imageFullPath) || empty($image)) {
			$imageFullPath = $this->defaultImagePath;
		}

		if(filesize($imageFullPath) <= 1024) {
			$imageFullPath = $this->defaultImagePath;
		}

		$imageSize = getimagesize($imageFullPath);
		
		if(!$imageSize) {
			return ""; // image doesn't exist
		}

		$originalWidth = $imageSize[0];
		$originalHeight = $imageSize[1];

		if(empty($options['width'])) {
			//original image width
			$options['width'] = $originalWidth;
		}

		if(empty($options['height'])) {
			//original image height
			$options['height'] = $originalHeight;
		}
		
		if($options['aspect'] && !$options['crop']) {
			if(($originalHeight/$options['height']) > ($originalWidth/$options['width'])) {
				$options['width'] = ceil(($originalWidth/$originalHeight) * $options['height']);
			}
			else {
				$options['height'] = ceil($options['width'] / ($originalWidth/$originalHeight));
			}
		}

		if(!is_dir($directoryFullPath.$cachedir)) {
			mkdir($directoryFullPath.$cachedir, 0777, true);
		}
		
		$cachedImageUrl = $directoryUrl.$cachedir.'/'.$options['width'].'x'.$options['height'].'_'.basename($image);
		$cachedImagePath = $directoryFullPath.$cachedir.DS.$options['width'].'x'.$options['height'].'_'.basename($image);

		$isCached = false;

		if(file_exists($cachedImagePath)) {
			$csize = getimagesize($cachedImagePath);
			$isCached = ($csize[0] == $options['width'] && $csize[1] == $options['height']);

			if(@filemtime($cachedImagePath) < @filemtime($imageFullPath)) {
				$isCached = false;
			}
		}

		$options['width'] = (int) $options['width'];
		$options['height'] = (int) $options['height'];

		if(!$isCached) {
			include_once(USERMGMT_PATH.DS.'vendor'.DS.'imageresize'.DS.'ImageResize.php');

			$imageResize = new \ImageResize($imageFullPath);

			if($options['crop']) {
				$imageResize->crop($options['width'], $options['height'], $options['allowEnlarge'], $cropPositions[$options['cropPosition']]);
			} else {
				$imageResize->resize($options['width'], $options['height'], $options['allowEnlarge']);
			}

			$imageResize->save($cachedImagePath);
		}

		return $cachedImageUrl;
	}
}
