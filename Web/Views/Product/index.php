<p/>
<table class="table">
    <thead> 
        <tr>
            <td>HSAId</td>
            <td>Type</td>
            <td>Description</td>
            <td>Amount</td>
            <td>Price</td>
        </tr>
    </thead>
    <tbody>
<?php

foreach ($registry->get("content") as $product) {
    echo "<tr>";
    echo '<td>'.$product->HSAIdGet()."</td>";
    echo "<td>".$product->TypeGet()."</td>";
    echo "<td>".$product->DescriptionGet()."</td>";
    echo "<td>".$product->AmountGet()."</td>";
    echo "<td>".$product->PriceGet()."</td>";
    echo "<tr/>";
}

?>
    </tbody>
</table>