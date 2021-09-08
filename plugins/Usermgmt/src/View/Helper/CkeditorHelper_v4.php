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
	 * @param array $ckoptions array of CkEditor attributes for this textarea
	 * @return string javascript code to initialise the CkEditor area
	 */
	public function _build($textAreaId, $ckoptions=array(), $toolbar_options=null) {
		if(!$this->_script) {
			// We don't want to add this every time, it's only needed once
			$this->_script = true;
			$this->Html->script('/plugins/ckeditor/ckeditor', array('block'=>true));
		}
		if(!empty($ckoptions)) {
			$json = json_encode($ckoptions);
			$json = rtrim($json, '}');
			$json .= ", ".$toolbar_options."}";
		} else {
			$json = "{".$toolbar_options."}";
		}
		return $this->Html->scriptBlock("CKEDITOR.replace( '".$textAreaId."', ".$json.");");
	}

	/**
	 * Creates a CkEditor textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $options Array of HTML attributes.
	 * @param array $ckoptions Array of CkEditor attributes for this textarea
	 * @param string $preset
	 * @return string An HTML textarea element with CkEditor
	 */
	public function textarea($fieldName, $options=array(), $ckoptions=array(), $preset=null) {
		$options['type'] = 'textarea';
		$toolbar_options = null;
		if(!empty($preset)) {
			$toolbar_options = $this->preset($preset);
		}
		if(isset($options['id'])) {
			$textAreaId = $options['id'];
		} else {
			$textAreaId = str_replace('_', '-', str_replace('.', '-', strtolower($fieldName)));
		}
		return $this->Form->control($fieldName, $options).$this->_build($textAreaId, $ckoptions, $toolbar_options);
	}

	/**
	 * Creates a CkEditor textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $options Array of HTML attributes.
	 * @param array $ckoptions Array of CkEditor attributes for this textarea
	 * @return string An HTML textarea element with CkEditor
	 */
	public function input($fieldName, $options=array(), $ckoptions=array(), $preset=null) {
		return $this->textarea($fieldName, $options, $ckoptions, $preset);
	}

	/**
	 * Creates a CkEditor textarea.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $options Array of HTML attributes.
	 * @param array $ckoptions Array of CkEditor attributes for this textarea
	 * @return string An HTML textarea element with CkEditor
	 */
	public function control($fieldName, $options=array(), $ckoptions=array(), $preset=null) {
		return $this->textarea($fieldName, $options, $ckoptions, $preset);
	}

	/**
	 * Creates a preset for ckoptions
	 *
	 * @param string $name
	 * @return array
	 */
	private function preset($name) {
		// Basic
		if($name == 'basic') {
			$toolbar = "toolbar: [
						[ 'Bold', 'Italic' ],
						[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent' ],
						[ 'Link', 'Unlink']
					]";
			return $toolbar;
		}
		// Standard Feature
		if($name == 'standard') {
			$toolbar = "toolbar: [
						[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
						[ 'Scayt' ],
						[ 'Link', 'Unlink', 'Anchor' ],
						[ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ],
						[ 'Maximize' ],
						[ 'Source' ],
						'/',
						[ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ],
						[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ],
						[ 'Styles', 'Format' ]
					]";
			return $toolbar;
		}
		// Full Feature
		if($name == 'full') {
			$toolbar = "toolbar: [
						[ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ],
						[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
						[ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ],
						[ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ],
						'/',
						[ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ],
						[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ],
						[ 'Link', 'Unlink', 'Anchor' ],
						[ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ],
						'/',
						[ 'Styles', 'Format', 'Font', 'FontSize' ],
						[ 'TextColor', 'BGColor' ],
						[ 'Maximize', 'ShowBlocks' ]
					]";
			return $toolbar;
		}
		return null;
	}
}
