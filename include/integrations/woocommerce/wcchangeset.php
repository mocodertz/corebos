<?php
/*************************************************************************************************
 * Copyright 2020 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class woocommercechangeset extends cbupdaterWorker {

	public function applyChange() {
		$fieldLayout=array(
			'Products' => array(
				'LBL_WC_INFORMATION'=> array(
					'wcproductsyncstatus' => array(
						'label' => 'wcproductsyncstatus',
						'columntype'=>'varchar(26)',
						'typeofdata'=>'V~O',
						'uitype'=>'16',
						'displaytype'=>'1',
						'massedit'=>'1',
						'vals' => array(
							'Active',
							'Published',
							'Inactive',
						)
					),
					'wcproductcode' => array(
						'label' => 'wcproductcode',
						'columntype'=>'int(11)',
						'typeofdata'=>'I~O',
						'uitype'=>'1',
						'displaytype'=>'1',
						'massedit'=>'0',
					),
				),
			),
		);
		$this->massCreateFields($fieldLayout);
	}

	public function undoChange() {
		$fieldLayout=array(
			'Products' => array(
				'wcproductsyncstatus',
				'wcproductcode',
			)
		);
		$this->massHideFields($fieldLayout);
		$this->sendMsg('Changeset '.get_class($this).' undone!');
		$this->markUndone();
	}
}