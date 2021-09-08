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
	 * @param string $presetOptions options of TinyMCE attributes for this textarea
	 * @return void
	 */
	public function generateEditor($textAreaId, $presetOptions) {
		if(!$this->_script) {
			// We don't want to add this every time, it's only needed once
			$this->_script = true;
			$this->Html->script('/plugins/tinymce/js/tinymce/tinymce.min', ['block'=>true]);
		}

		$presetOptions = "selector: '#".$textAreaId."',".$presetOptions;
		
		$this->Html->scriptStart(['block'=>true]);
		
		echo "tinyMCE.init({".$presetOptions."});";
		
		$this->Html->scriptEnd();
	}

	/**
	 * Creates a TinyMCE textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $htmlAttributes HTML attributes.
	 * @param array $tinymceOptions TinyMCE attributes for this textarea
	 * @param string $preset
	 * @return string An HTML textarea element with TinyMCE
	 */
	public function textarea($fieldName, $htmlAttributes=array(), $tinymceOptions=array(), $preset=null) {
		$htmlAttributes['type'] = 'textarea';

		$presetOptions = "";

		if(!empty($preset)) {
			$presetOptions = $this->preset($preset);
		}

		if(is_array($tinymceOptions)) {
			foreach($tinymceOptions as $key=>$value) {
				$presetOptions .= ",".$key.":'".$value."'";
			}

			$presetOptions = ltrim($presetOptions, ',');
		}
		
		if(isset($htmlAttributes['id'])) {
			$textAreaId = $htmlAttributes['id'];
		} else {
			$textAreaId = str_replace('_', '-', str_replace('.', '-', strtolower($fieldName)));
		}

		return $this->Form->control($fieldName, $htmlAttributes).$this->generateEditor($textAreaId, $presetOptions);
	}

	/**
	 * Creates a TinyMCE textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $htmlAttributes HTML attributes.
	 * @param array $tinymceOptions TinyMCE attributes for this textarea
	 * @param string $preset
	 * @return string An HTML textarea element with TinyMCE
	 */
	public function input($fieldName, $htmlAttributes=array(), $tinymceOptions=array(), $preset=null) {
		return $this->textarea($fieldName, $htmlAttributes, $tinymceOptions, $preset);
	}

	/**
	 * Creates a TinyMCE textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $htmlAttributes HTML attributes.
	 * @param array $tinymceOptions TinyMCE attributes for this textarea
	 * @param string $preset
	 * @return string An HTML textarea element with TinyMCE
	 */
	public function control($fieldName, $htmlAttributes=array(), $tinymceOptions=array(), $preset=null) {
		return $this->textarea($fieldName, $htmlAttributes, $tinymceOptions, $preset);
	}

	/**
	 * Creates a preset for Tinymce options
	 *
	 * @param string $name preset name
	 * @return string
	 */
	private function preset($name) {
		$preset = "";

		// Full Feature
		if($name == 'full' || $name == 'umcode') {
			$preset = "
				plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',

				imagetools_cors_hosts: ['picsum.photos'],

				menubar: 'file edit view insert format tools table help',

				toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',

				toolbar_sticky: true,

				autosave_ask_before_unload: true,

				autosave_interval: '30s',

				autosave_prefix: '{path}{query}-{id}-',

				autosave_restore_when_empty: false,
				
				autosave_retention: '2m',

				image_advtab: true,
				
				content_css: [
					'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
					'//www.tiny.cloud/css/codepen.min.css'
				],
				
				importcss_append: true,
				
				height: 600,
				
				image_caption: true,

				quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
				
				noneditable_noneditable_class: 'mceNonEditable',
				
				toolbar_drawer: 'sliding',

				contextmenu: 'link image imagetools table'";
		}

		// Basic Feature
		if($name == 'basic') {
			$preset = "
				plugins: [
					'advlist autolink lists link image charmap print preview anchor',
					'searchreplace visualblocks code fullscreen',
					'insertdatetime media table paste code help wordcount'
				],

				menubar: false,

				toolbar: 'undo redo | formatselect |  bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
				content_css: [
					'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
					'//www.tiny.cloud/css/codepen.min.css'
				]";
		}

		return $preset;
	}
}
