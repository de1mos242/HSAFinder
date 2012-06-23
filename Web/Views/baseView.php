<?php if ($registry->get("requestType") == "HTML") { ?>
<!DOCTYPE html>
<html>
    <head>
        <title>HSAFinder</title>
        <meta charset="utf-8">
        <!--<script src="<?=dirname($_SERVER['PHP_SELF'])?>/bootstrap/js/bootstrap.min.js"></script>-->
        <!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
        <script src="<?=dirname($_SERVER['PHP_SELF'])?>/js/jquery-1.7.2.min.js"></script>
        <script src="<?=dirname($_SERVER['PHP_SELF'])?>/bootstrap/js/bootstrap.min.js"></script>
        <? if (!is_null($registry->get("scriptFile"))) { ?>
            <script type="text/javascript" src="<?=dirname($_SERVER['PHP_SELF']).$registry->get('scriptFile')?>"></script>
        <? } ?>
        <? if (!is_null($registry->get("commonControllerScriptFile"))) { ?>
            <script type="text/javascript" src="<?=dirname($_SERVER['PHP_SELF']).$registry->get('commonControllerScriptFile')?>"></script>
        <? } ?>
        <link href="<?=dirname($_SERVER['PHP_SELF'])?>/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="<?=dirname($_SERVER['PHP_SELF'])?>/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
        
    </head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="<?=$_SERVER['PHP_SELF']?>">HSAFinder</a>
                    <ul class="nav">
                        <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Product/index">Прайс</a></li>
                        <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Item/index">Каталог</a></li>
                        <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Item/search">Поиск</a></li>
                    </ul>
                    <ul class="nav pull-right">
                      <li class="dropdown">
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Работа с каталогом<b class="caret"></b></a>
                          <ul class="dropdown-menu">
                            <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Item/clean">Очистить данные каталога</a>
                            <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Item/loadKYBEurope">Загрузить каталог KYBEurope</a>
                            <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Item/loadKYBJapan">Загрузить каталог KYBJapan</a>
                            <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Item/loadTokiko">Загрузить каталог Tokiko</a>
                            <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Item/create">Добавить элемент в каталог</a>
                            <!--<li><a data-toggle="modal" href="#upload_catalog">Загрузить каталог</a></li>
                            <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Item/generate">Сгенерировать тестовый каталог</a></li>-->
                            <li><a data-toggle="modal" href="#upload_price">Загрузить новый прайс</a></li>
                            <li><a href="<?=$_SERVER['PHP_SELF']?>?route=Product/generate">Сгенерировать тестовый прайс</a></li>
                          </ul>
                      </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <form method="post" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>?route=Product/upload">
            <div class="modal hide fade" id="upload_price">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>
                <h3>Загрузить новый прайс</h3>
            </div>
            <div class="modal-body">
                <input type="file" name="filename" class="input-file" id="fileInput">
                <p class="help-block">Выберите файл с прайсом.</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn" data-dismiss="modal">Отменить</button>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </div>
            </div>
        </form>
        
        <form method="post" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>?route=Item/upload">
            <div class="modal hide fade" id="upload_catalog">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>
                <h3>Загрузить новый каталог</h3>
            </div>
            <div class="modal-body">
                <input type="file" name="filename" class="input-file" id="fileInput">
                <p class="help-block">Выберите файл с каталогом.</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn" data-dismiss="modal">Отменить</button>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </div>
            </div>
        </form>
        
        <div class="container" style="margin-top: 40px;">
        <?php
            if ($registry->get("page_error") != null) {
                echo '<div class="alert-error">'.$registry->get("page_error").'</div>';
            }
            if ($registry->get("upload_error") != null) {
                echo '<div class="alert-error">'.$registry->get("upload_error").'</div>';
            }
        ?>
        <?php
            include($registry->get("showFile"));
        ?>
        </div>
        <footer>
            <p>&copy; De1mos 2012</p>
        </footer>
        
    </body>
</html>
<?php }
else if ($registry->get("requestType") == "JSON") {
    print json_encode($registry->get("content"));
}

?>