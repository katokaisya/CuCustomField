<?php
/**
 * @var BcAppView $this
 * @var string $currentModelName
 */
?>


<tr id="Row<?php echo $currentModelName . Inflector::camelize('google_maps'); ?>Group">
	<th class="col-head bca-form-table__label">
		初期値
	</th>
	<td class="col-input bca-form-table__input">
		<div class="googlemaps-input-box">
			<p>
			<span id="Row<?php echo $currentModelName . Inflector::camelize('google_maps_latitude'); ?>">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_latitude', '緯度') ?>
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_latitude', ['type' => 'text', 'size' => 22]) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_latitude') ?>
			</span>
			<span id="Row<?php echo $currentModelName . Inflector::camelize('google_maps_longitude'); ?>">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_longitude', '経度') ?>
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_longitude', ['type' => 'text', 'size' => 22]) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_longitude') ?>
			</span>

			<span id="Row<?php echo $currentModelName . Inflector::camelize('google_maps_zoom'); ?>">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_zoom', 'ズーム値') ?>
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_zoom', ['type' => 'text', 'size' => 4]) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_zoom') ?>
			</span>
			</p>
			<p>
			<span id="Row<?php echo $currentModelName . Inflector::camelize('google_maps_text'); ?>">
				<?php echo $this->BcForm->label('CuCustomFieldDefinition.google_maps_text', 'ポップアップテキスト') ?>
				<?php echo $this->BcForm->input('CuCustomFieldDefinition.google_maps_text', ['type' => 'text', 'size' => 60]) ?>
				<?php echo $this->BcForm->error('CuCustomFieldDefinition.google_maps_text') ?>
			</span>
			</p>
		</div>
	</td>
</tr>
