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

class TinymceHelper extends Helper {
	public $helpers = ['Html', 'Form'];
	public $_script = false;

	/**
	 * Adds the tinymce.min.js file and constructs the options
	 *
	 * @param string $textAreaId field id
	 * @param array $tinyoptions Array of TinyMCE attributes for this textarea
	 * @return void
	 */
	public function _build($textAreaId, $tinyoptions=array()) {
		if(!$this->_script) {
			// We don't want to add this every time, it's only needed once
			$this->_script = true;
			$this->Html->script('/plugins/tinymce/js/tinymce/tinymce.min', ['block'=>true]);
		}

		$tinyoptions['selector'] = '#'.$textAreaId;
		
		$this->Html->scriptStart(['block'=>true]);
		
		echo 'tinyMCE.init('.json_encode($tinyoptions).');';
		
		$this->Html->scriptEnd();
	}

	/**
	 * Creates a TinyMCE textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $options Array of HTML attributes.
	 * @param array $tinyoptions Array of TinyMCE attributes for this textarea
	 * @param string $preset
	 * @return string An HTML textarea element with TinyMCE
	 */
	public function textarea($fieldName, $options=array(), $tinyoptions=array(), $preset=null) {
		$options['type'] = 'textarea';

		if(!empty($preset)) {
			$preset_options = $this->preset($preset);

			if(is_array($preset_options) && is_array($tinyoptions)) {
				$tinyoptions = array_merge($preset_options, $tinyoptions);
			}else{
				$tinyoptions = $preset_options;
			}
		}

		if(isset($options['id'])) {
			$textAreaId = $options['id'];
		} else {
			$textAreaId = str_replace('_', '-', str_replace('.', '-', strtolower($fieldName)));
		}

		return $this->Form->control($fieldName, $options).$this->_build($textAreaId, $tinyoptions);
	}

	/**
	 * Creates a TinyMCE textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $options Array of HTML attributes.
	 * @param array $tinyoptions Array of TinyMCE attributes for this textarea
	 * @return string An HTML textarea element with TinyMCE
	 */
	public function input($fieldName, $options=array(), $tinyoptions=array(), $preset=null) {
		return $this->textarea($fieldName, $options, $tinyoptions, $preset);
	}

	/**
	 * Creates a TinyMCE textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $options Array of HTML attributes.
	 * @param array $tinyoptions Array of TinyMCE attributes for this textarea
	 * @return string An HTML textarea element with TinyMCE
	 */
	public function control($fieldName, $options=array(), $tinyoptions=array(), $preset=null) {
		return $this->textarea($fieldName, $options, $tinyoptions, $preset);
	}

	/**
	 * Creates a preset for TinyOptions
	 *
	 * @param string $name
	 * @return array
	 */
	private function preset($name) {
		// Full Feature
		if($name == 'full') {
			return array(
				'theme' =>  'modern',
				'plugins' =>    array('advlist autolink lists link image charmap print preview hr anchor pagebreak', 'searchreplace wordcount visualblocks visualchars code fullscreen', 'insertdatetime media nonbreaking save table contextmenu directionality', 'emoticons template paste textcolor'),
				'toolbar1' =>   'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
				'toolbar2' =>   'print preview media | forecolor backcolor emoticons',
				'image_advtab' =>   true,
				'templates' =>  array(array('title'=>'Test template 1', 'content'=>'Test 1'), array('title'=>'Test template 2', 'content'=>'Test 2')),
			);
		}

		// Basic
		if($name == 'basic') {
			return array(
				'plugins' =>    'advlist autolink lists link image charmap print preview anchor', 'searchreplace visualblocks code fullscreen', 'insertdatetime media table contextmenu paste',
				'toolbar' =>    'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
			);
		}

		// Simple
		if($name == 'simple') {
			return array(
				'plugins' =>    'advlist autolink lists link image charmap print preview anchor', 'searchreplace visualblocks code fullscreen', 'insertdatetime media table contextmenu paste',
				'toolbar' =>    'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
			);
		}

		// UMCode
		if($name == 'umcode') {
			return array(
				'theme' =>  'modern',
				'plugins' =>    array('advlist autolink lists link image charmap print preview hr anchor pagebreak', 'searchreplace wordcount visualblocks visualchars code fullscreen', 'insertdatetime media nonbreaking save table contextmenu directionality', 'emoticons template paste textcolor'),
				'toolbar1' =>   'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
				'toolbar2' =>   'print preview media | forecolor backcolor emoticons',
				'image_advtab' =>   true,
				'templates' =>  array(array('title'=>'Test template 1', 'content'=>'Test 1'), array('title'=>'Test template 2', 'content'=>'Test 2')),
			);
		}
		return null;
	}
}
