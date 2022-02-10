<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
    <label>Columns :</label>
    <select class="wooProductsColumn">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
    </select>
    <br><br><hr><br>
    <label>Number Of Products :</label>
    <input type="number" class="wooProductsCount" >
    <br><br><hr><br>
    <br> <h3> Filters </h3>
    <br>
    <label>Filter By Categories :</label>
    <input type="text" class="wooProductsCategories">
    <br><br><hr><br>
    <label>Order By :</label>
    <select class="wooProductsOrderBy">
        <option value="date">Date</option>
        <option value="title">Title</option>
        <option value="rand">Random</option>
        <option value="id">Id</option>
        <option value="popularity">Best Selling</option>
    </select>
    <br><br><hr><br>
    <label>Order :</label>
    <select class="wooProductsOrder">
        <option value="asc">Ascending</option>
        <option value="desc">Descending</option>
    </select>
</div>