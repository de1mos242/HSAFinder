<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.  'HSAItemHelper.php';

class ItemsTable {
    public static function GetTable($items) {
        $header = '<table class="table table-striped table-bordered table-condensed" id="ItemsTable">'.
                '<thead>'.
                    '<tr>'.
                        '<td>Марка'.
                        '<td>Модель'.
                        '<td>Год'.
                        '<td>Кузов'.
                        '<td>Производитель'.
                        '<td>Номер'.
                        //'<td>Оригинальный номер'.
                        '<td>Левая/Правая'.
                        '<td>Задняя/Передняя'.
                        //'<td>Тип'.
                        '<td>Цена'.
                        '<td>Количество'.
                        '<td>Описание'.
                        '<td>Изменить'.
                        '<td>Удалить'.
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
                $body.="<td>".$item->ProductGet()->AmountGet();
                $body.="<td>".$item->ProductGet()->DescriptionGet();
            }
            else {
                $body.='<td/><td/><td/>';
            }

            $body.="<td><a href=\"".$_SERVER['PHP_SELF']."?route=Item/edit&itemId=$itemId\" class=\"btn btn-warning HSAButtonEdit\">Edit</a>";
            $body.="<td><a href=\"".$_SERVER['PHP_SELF']."?route=Item/delete&itemId=$itemId\" class=\"btn btn-danger HSAButtonEdit\">Delete</a>";
            $body.="<tr/>";
        }
        return $body;
    }
    
}
?>
