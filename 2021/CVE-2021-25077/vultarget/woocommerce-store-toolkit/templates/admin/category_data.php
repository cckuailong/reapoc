<?php
echo '<tr class="form-field">';
echo '<th scope="row" valign="top"><label>' . __( 'Category heirachy', 'woocommerce-store-toolkit' ) . '</label></th>';
echo '<td>';
echo $category_heirachy;
echo '<br />';
echo '</tr>';

echo '<tr class="form-field">';
echo '<th scope="row" valign="top"><label>' . __( 'Category heirachy depth', 'woocommerce-store-toolkit' ) . '</label></th>';
echo '<td>';
echo sprintf( __( '%d level(s) deep', 'woocommerce-store-toolkit' ), $category_depth );
echo '</td>';
echo '</tr>';