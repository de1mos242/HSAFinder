<table>
    <td>
        <div class="span8">
            <form class="form-horizontal" onSubmit="updateItems(); return false;">
                <fieldset>
                    <div class="control-group">
                        <label class="control-label" for="markSelect">Выберите марку</label>
                        <div class="controls">
                            <select id="markSelect" >
                                <option value="empty"></option>
                                <?php
                                    foreach ($registry->get("contentMark") as $mark) {
                                        echo '<option value="'.$mark.'">'.$mark."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="modelSelect">Выберите модель</label>
                        <div class="controls">
                            <select id="modelSelect" disabled="true"></select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="yearSelect">Выберите год</label>
                        <div class="controls">
                            <select id="yearSelect" disabled="true"></select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="bodySelect">Выберите кузов</label>
                        <div class="controls">
                            <select id="bodySelect" disabled="true"></select>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    <td>
        <div class="span8">
            <form class="form-horizontal" onSubmit="updateItems(); return false;">
                <fieldset>
                    <div class="control-group">
                        <label class="control-label" for="lineDirecitonSelect">Передняя/Задняя</label>
                        <div class="controls">
                            <select id="lineDirecitonSelect" >
                                <?php
                                    foreach ($registry->get("contentLineDirections") as $key => $value) {
                                        echo '<option value="'.$key.'">'.$value."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="handDirecitonSelect">Правая/Левая</label>
                        <div class="controls">
                            <select id="handDirecitonSelect" >
                                <?php
                                    foreach ($registry->get("contentHandDirections") as $key => $value) {
                                        echo '<option value="'.$key.'">'.$value."</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="control-label" for="brandNumberInput">Номер</label>
                        <div class="controls">
                            <input type="text" class="input-large" id="brandNumberInput"/>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
</table>
<div id="findedItems">
    <?php 
        require_once dirname(dirname(dirname(__FILE__))). DIRECTORY_SEPARATOR . "Helpers" .
            DIRECTORY_SEPARATOR . "Item" . DIRECTORY_SEPARATOR . "ItemsTable.php";
        echo ItemsTable::GetTable($registry->get("content"));
    ?>
</div>
