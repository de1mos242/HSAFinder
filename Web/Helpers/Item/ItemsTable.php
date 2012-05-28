<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.  'HSAItemHelper.php';

class ItemsTable {
    public static function GetTable($items) {
        $header = '<table class="table table-striped table-bordered table-condensed" id="ItemsTable">'.
                '<thead>'.
                    '<tr>'.
                        '<th>Марка'.
                        '<th>Модель'.
                        '<th>Год'.
                        '<th>Кузов'.
                        '<th>Производитель'.
                        '<th>Номер'.
                        //'<th>Оригинальный номер'.
                        '<th>Левая/Правая'.
                        '<th>Задняя/Передняя'.
                        //'<th>Тип'.
                        '<th>Цена'.
                        '<th>Количество'.
                        '<th>Описание'.
                        '<th>Изменить'.
                        '<th>Удалить'.
            '<tbody id="ItemsTableBody">';
        $body = ItemsTable::GetRows($items);
        
        $end = '</tbody></table>';
        return $header.$body.$end;
    }
    
    public static function GetRows($items) {
        $body = '';
        foreach ($items as $item) {
            $itemId = $item->IdGet();
            $body.="<tr>";
            $body.='<td>'.$item->ModelGet()->MarkGet()->NameGet();
            $body.="<td>".$item->ModelGet()->NameGet();
            $body.='<td>'.$item->YearGet();
            $body.="<td>".$item->BodyGet();
            $body.="<td>".$item->HSATypeGet();
            $body.="<td>".$item->BrandNumberGet();
            //$body.="<td>".$item->OEMNumbersStringGet();
            $body.="<td>".HSAItemHelper::HandDirectionToLocalString($item->HandDirectionGet());
            $body.="<td>".HSAItemHelper::LineDirectionToLocalString($item->LineDirectionGet());
            //$body.="<td>".HSAItemHelper::TypeToLocalString($item->TypeGet());
            if ($item->ProductGet() != NULL) {
                $body.='<td>'.$item->ProductGet()->PriceGet();
                $body.="<td>".$item->ProductGet()->AmountLocaleGet();
                $body.="<td>".$item->ProductGet()->DescriptionGet();
            }
            else {
                $body.='<td/><td/><td/>';
            }

            $body.="<td><a href=\"".$_SERVER['PHP_SELF']."?route=Item/edit&itemId=$itemId\" class=\"btn btn-warning HSAButtonEdit\">Edit</a>";
            $body.="<td><a href=\"".$_SERVER['PHP_SELF']."?route=Item/delete&itemId=$itemId\" class=\"btn btn-danger HSAButtonEdit\">Delete</a>";
            $body.="<tr/>";
        }
        //echo "body = $body|";
        return $body;
    }
    
}
?>
