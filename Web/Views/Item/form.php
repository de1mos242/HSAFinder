<? 	
	$item = $registry->get("content");
	$HSATypes = array("KYB","TOKICO");
	$lineDirections = array('FRONT'=>"Передняя", 'REAR'=>"Задняя");
	$handDirections = array('BOTH'=>"Обе", 'LEFT'=>"Левая", 'RIGHT'=>"Правая");
	$types = array("empty" => '', 'GAS' => 'Газовая', 'OIL' => 'Масляная');
	$year = '';
	$body = ''; 
	$HSANumber = '';
	$id = '';
	if ($item != NULL) {
		$markName = $item->ModelGet()->MarkGet()->NameGet();
		$modelName = $item->ModelGet()->NameGet();
		$HSAType = $item->HSATypeGet();
		$year = $item->YearGet();
		$body = $item->BodyGet();
		$HSANumber = $item->BrandNumberGet();
		$handDirection = $item->HandDirectionGet();
		$lineDirection = $item->LineDirectionGet();
		$type = $item->TypeGet();
		$id = $item->IdGet();
	}

?>
<form class="form-horizontal" method="post" 
	action="<?=$_SERVER['PHP_SELF']?>?route=Item/save"
	enctype="multipart/form-data">
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="markSelect">Марка</label>
			<div class="controls">
				<select id="markSelect" name="markSelect">
					<? foreach ($registry->get("marks") as $value) {
							echo "<option value=\"$value\"";
							if (isset($markName) && $markName == $value)
								echo " selected";
							echo ">$value</option>";
						}	?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="modelSelect">Модель</label>
			<div class="controls">
				<select id="modelSelect" name="modelSelect">
					<? foreach ($registry->get("models") as $value) {
							echo "<option value=\"$value\"";
							if (isset($modelName) && $modelName == $value)
								echo " selected";
							echo ">$value</option>";
						}	?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="HSATypeSelect">Производитель</label>
			<div class="controls">
				<select id="HSATypeSelect" name="HSATypeSelect">
					<? foreach ($HSATypes as $value) {
							echo "<option value=\"$value\"";
							if (isset($HSAType) && $HSAType == $value)
								echo " selected";
							echo ">$value</option>";
						} ?>
				</select>
			</div>
		</div>
		<div class="control-group">
      <label class="control-label" for="yearInput">Год</label>
      <div class="controls">
          <input type="text" class="input-large" id="yearInput" name="yearInput" value="<?=$year?>">
          <!--<p class="help-block">Введите год</p>-->
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="bodyInput">Кузов</label>
      <div class="controls">
          <input type="text" class="input-large" id="bodyInput" name="bodyInput" value="<?=$body?>">
          <!--<p class="help-block">Введите кузов</p>-->
      </div>
    </div>
    <div class="control-group">
			<label class="control-label" for="HandDirectionSelect">Левая/Правая</label>
			<div class="controls">
				<select id="HandDirectionSelect" name="HandDirectionSelect">
					<? foreach ($handDirections as $key => $value) {
							echo "<option value=\"$key\"";
							if (isset($handDirection) && $handDirection == $key)
								echo " selected";
							echo ">$value</option>";
						} ?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="LineDirectionSelect">Передняя/задняя</label>
			<div class="controls">
				<select id="LineDirectionSelect" name="LineDirectionSelect">
					<? foreach ($lineDirections as $key => $value) {
							echo "<option value=\"$key\"";
							if (isset($lineDirection) && $lineDirection == $key)
								echo " selected";
							echo ">$value</option>";
						} ?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="TypeSelect">Газовая/Масляная</label>
			<div class="controls">
				<select id="TypeSelect" name="TypeSelect">
					<? foreach ($types as $key => $value) {
							echo "<option value=\"$key\"";
							if (isset($type) && $type == $key)
								echo " selected";
							echo ">$value</option>";
						} ?>
				</select>
			</div>
		</div>
		<div class="control-group">
      <label class="control-label" for="HSANumberInput">Номер</label>
      <div class="controls">
          <input type="text" class="input-large" id="HSANumberInput" name="HSANumberInput" value="<?=$HSANumber?>">
      </div>
    </div>
    <input type="hidden" id="ItemId" name="ItemId" value="<?=$id?>"/>
	</fieldset>
	<input type="submit" value="Сохранить" />
</form>
