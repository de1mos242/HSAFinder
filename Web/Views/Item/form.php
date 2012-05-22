<? 	
	$item = $registry->get("content");
	$HSATypes = array("KYB","TOKIKO");
	$lineDirections = array('FRONT'=>"Передняя", 'REAR'=>"Задняя");
	$handDirections = array('LEFT'=>"Левая", 'RIGHT'=>"Правая");
	$year = '';
	$body = ''; 
	if ($item != NULL) {
		$markName = $item->ModelGet()->MarkGet()->NameGet();
		$modelName = $item->ModelGet()->NameGet();
		$HSAType = $item->HSATypeGet();
		$year = $item->YearGet();
		$body = $item->BodyGet();
	}

?>
<form class="form-horizontal">
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="markSelect">Марка</label>
			<div class="controls">
				<select id="markSelect">
					<?
						foreach ($registry->get("marks") as $value) {
							echo "<option value=\"$value\"";
							if (isset($markName) && $markName == $value)
								echo " selected";
							echo ">$value</option>";
						}
					?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="modelSelect">Модель</label>
			<div class="controls">
				<select id="modelSelect">
					<?
						foreach ($registry->get("models") as $value) {
							echo "<option value=\"$value\"";
							if (isset($modelName) && $modelName == $value)
								echo " selected";
							echo ">$value</option>";
						}
					?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="HSATypeSelect">Производитель</label>
			<div class="controls">
				<select id="HSATypeSelect">
					<?
						foreach ($HSATypes as $value) {
							echo "<option value=\"$value\"";
							if (isset($HSAType) && $HSAType == $value)
								echo " selected";
							echo ">$value</option>";
						}
					?>
				</select>
			</div>
		</div>
		<div class="control-group">
      <label class="control-label" for="yearInput">Год</label>
      <div class="controls">
          <input type="text" class="input-xlarge" id="yearInput" value="<?=$year?>">
          <p class="help-block">Введите год</p>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="bodyInput">Кузов</label>
      <div class="controls">
          <input type="text" class="input-xlarge" id="bodyInput" value="<?=$body?>">
          <p class="help-block">Введите кузов</p>
      </div>
    </div>
	</fieldset>
</form>
