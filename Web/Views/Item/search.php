<div class="span8">
    <form class="form-horizontal">
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
                <label class="control-label" for="modelSelect">Выберите модель</label>
                <div class="controls">
                    <select id="modelSelect" disabled="true"></select>
                </div>
                <label class="control-label" for="yearSelect">Выберите год</label>
                <div class="controls">
                    <select id="yearSelect" disabled="true"></select>
                </div>
                <label class="control-label" for="bodySelect">Выберите кузов</label>
                <div class="controls">
                    <select id="bodySelect" disabled="true"></select>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<div id="findedItems">
    <?php 
        require_once dirname(dirname(dirname(__FILE__))). DIRECTORY_SEPARATOR . "Helpers" .
            DIRECTORY_SEPARATOR . "Item" . DIRECTORY_SEPARATOR . "ItemsTable.php";
        echo ItemsTable::GetTable($registry->get("content"));
    ?>
</div>

<label id="debug1"></label><label id="debug2"></label>
