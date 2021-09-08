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

class CkeditorHelper extends Helper {
	public $helpers = ['Html', 'Form'];
	public $_script = false;

	/**
	 * Adds the ckeditor.js file and constructs the options
	 *
	 * @param string $textAreaId field id
	 * @param string $presetOptions options of CkEditor attributes for this textarea
	 * @return javascript
	 */
	public function generateEditor($textAreaId, $presetOptions) {
		if(!$this->_script) {
			// We don't want to add this every time, it's only needed once
			$this->_script = true;
			$this->Html->script('/plugins/ckeditor/ckeditor', array('block'=>true));

			$this->Html->scriptStart(['block'=>true]);
		
			echo "var allCkEditors = new Object();";
			
			$this->Html->scriptEnd();
		}

		return $this->Html->scriptBlock("ClassicEditor.create(document.querySelector('#".$textAreaId."'), {".$presetOptions."}).then(editor=>{ allCkEditors['".$textAreaId."'] = editor; }).catch(error=>{ console.error(error); });");
	}

	/**
	 * Creates a CkEditor textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $htmlAttributes HTML attributes.
	 * @param array $ckeditorOptions CkEditor attributes for this textarea
	 * @param string $preset
	 * @return string An HTML textarea element with CkEditor
	 */
	public function textarea($fieldName, $htmlAttributes=array(), $ckeditorOptions=array(), $preset=null) {
		$htmlAttributes['type'] = 'textarea';

		$presetOptions = "";

		if(!empty($preset)) {
			$presetOptions = $this->preset($preset);
		}

		if(is_array($ckeditorOptions)) {
			foreach($ckeditorOptions as $key=>$value) {
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
	 * Creates a CkEditor textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $htmlAttributes HTML attributes.
	 * @param array $ckeditorOptions CkEditor attributes for this textarea
	 * @param string $preset
	 * @return string An HTML textarea element with CkEditor
	 */
	public function input($fieldName, $htmlAttributes=array(), $ckeditorOptions=array(), $preset=null) {
		return $this->textarea($fieldName, $htmlAttributes, $ckeditorOptions, $preset);
	}

	/**
	 * Creates a CkEditor textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $htmlAttributes HTML attributes.
	 * @param array $ckeditorOptions CkEditor attributes for this textarea
	 * @param string $preset
	 * @return string An HTML textarea element with CkEditor
	 */
	public function control($fieldName, $htmlAttributes=array(), $ckeditorOptions=array(), $preset=null) {
		return $this->textarea($fieldName, $htmlAttributes, $ckeditorOptions, $preset);
	}

	/**
	 * Creates a preset for CkEditor options
	 *
	 * @param string $name preset name
	 * @return string
	 */
	private function preset($name) {
		$preset = "";

		// Full Feature
		if($name == 'full' || $name == 'umcode') {
			$preset = "";
		}

		// Basic Feature
		if($name == 'basic') {
			$preset = "
				toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
				heading: {
					options: [
						{ model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
						{ model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
						{ model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
					]
				}";
		}

		return $preset;
	}
}
