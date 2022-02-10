<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 11/7/20 21:02
 * Version: 1.0.0
 */
if(!defined("ABSPATH")) die();
//$all_downloads
if(!isset($scid)) $scid = uniqid();
$_scparams =  \WPDM\__\Crypt::encrypt($scparams);
?>
<style>
    .card-datatable ul.pagination{
        margin:  5px auto;
        float: right;
    }
    .pagination  li a:hover{
        color:  var(--color-primary) !important;
        border: 1px solid var(--color-primary) !important;
        background: #ffffff;
    }
    .pagination  li.active a:hover,
    .pagination  li.active a{
        color:  #ffffff;
        border: 1px solid var(--color-primary-active);
        background: var(--color-primary);
    }
    #card_datatable_<?php echo $scid;  ?> td.update_date,
    #card_datatable_<?php echo $scid;  ?> th{
        white-space: nowrap;
    }
    #card_datatable_<?php echo $scid;  ?> th:last-child,
    #card_datatable_<?php echo $scid;  ?> td:last-child{
        text-align: right;
        width: 100px;
        vertical-align: middle;
    }
    #card_datatable_<?php echo $scid;  ?> th.thumb,
    #card_datatable_<?php echo $scid;  ?> td.thumb,
    #card_datatable_<?php echo $scid;  ?> th.icon,
    #card_datatable_<?php echo $scid;  ?> td.icon {
        width: 56px;
        min-width: 56px;
        max-width: 56px;
        padding-right: 0  !important;
        vertical-align: middle !important;
    }
    #card_datatable_<?php echo $scid;  ?> td.thumb .datatable-thumb,
    #card_datatable_<?php echo $scid;  ?> td.icon .datatable-icon{
        max-width: 100%;
        border-radius: 0.2rem;
    }
    #card_datatable_<?php echo $scid;  ?> td:last-child .btn{
        display: block;
        white-space: nowrap;
    }
    .small-txt{
        font-size: 11px;
        opacity: 0.9;
    }

     #card_datatable_<?php echo $scid;  ?> table,#card_datatable_<?php echo $scid;  ?> td,#card_datatable_<?php echo $scid;  ?> th{
         border: 0;
     }
    #card_datatable_<?php echo $scid;  ?>{
        overflow: hidden;
    }
    #card_datatable_<?php echo $scid;  ?>{
        font-size: 10pt;
        min-width: 100%;
    }
    #card_datatable_<?php echo $scid;  ?> .wpdm-download-link img{
        box-shadow: none !important;
        max-width: 100%;
    }
    #card_datatable_<?php echo $scid;  ?> .form.control,
    #card_datatable_<?php echo $scid;  ?> .btn{
        border-radius: 0.2rem;
    }
    .w3eden .pagination{
        margin: 0 !important;
    }
    #card_datatable_<?php echo $scid;  ?> td:not(:first-child){
        vertical-align: middle !important;
    }
    #card_datatable_<?php echo $scid;  ?> td.__dt_col_download_link .btn{
        display: block;
        width: 100%;
    }
    #card_datatable_<?php echo $scid;  ?> td.__dt_col_download_link,
    #card_datatable_<?php echo $scid;  ?> th#download_link{
        max-width: 155px !important;
        width: 155px;

    }
    #card_datatable_<?php echo $scid;  ?> th{
        background-color: rgba(0,0,0,0.04);
        border-bottom: 1px solid rgba(0,0,0,0.025);
    }

    #card_datatable_<?php echo $scid;  ?> .package-title{
        color:#36597C;
        font-size: 11pt;
        font-weight: 700;
    }
    #card_datatable_<?php echo $scid;  ?> .small-txt{
        margin-right: 7px;
    }
    #card_datatable_<?php echo $scid;  ?> td{
        min-width: 150px;
    }

    #card_datatable_<?php echo $scid;  ?> td.__dt_col_categories{
        max-width: 300px;
    }

    #card_datatable_<?php echo $scid;  ?> .search-field{
        padding-left: 32px;
        background: #ffffff url(data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMiIgaGVpZ2h0PSI1MTIiIHZpZXdCb3g9IjAgMCAyNCAyNCIgd2lkdGg9IjUxMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgZGF0YS1uYW1lPSJMYXllciAyIj48bGluZWFyR3JhZGllbnQgaWQ9Ik9yYW5nZV9ZZWxsb3ciIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIiB4MT0iLTEuNzYyIiB4Mj0iMjAuOTg0IiB5MT0iMjUuMzY3IiB5Mj0iNi44ODYiPjxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iI2ZmZjMzYiIvPjxzdG9wIG9mZnNldD0iLjA0IiBzdG9wLWNvbG9yPSIjZmVlNzJlIi8+PHN0b3Agb2Zmc2V0PSIuMTE3IiBzdG9wLWNvbG9yPSIjZmVkNTFiIi8+PHN0b3Agb2Zmc2V0PSIuMTk2IiBzdG9wLWNvbG9yPSIjZmRjYTEwIi8+PHN0b3Agb2Zmc2V0PSIuMjgxIiBzdG9wLWNvbG9yPSIjZmRjNzBjIi8+PHN0b3Agb2Zmc2V0PSIuNjY5IiBzdG9wLWNvbG9yPSIjZjM5MDNmIi8+PHN0b3Agb2Zmc2V0PSIuODg4IiBzdG9wLWNvbG9yPSIjZWQ2ODNjIi8+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjZTkzZTNhIi8+PC9saW5lYXJHcmFkaWVudD48cGF0aCBkPSJtMjIuNzA3IDIxLjI5My01LjEwNy01LjExMWE5LjM1NSA5LjM1NSAwIDEgMCAtMS40MTggMS40MThsNS4xMTEgNS4xMTFhMSAxIDAgMCAwIDEuNDE0LTEuNDE0em0tMTkuNzA3LTEwLjk2YTcuMzM0IDcuMzM0IDAgMSAxIDcuMzMzIDcuMzM0IDcuMzQyIDcuMzQyIDAgMCAxIC03LjMzMy03LjMzNHoiIGZpbGw9InVybCgjT3JhbmdlX1llbGxvdykiLz48L3N2Zz4=);
        background-repeat: no-repeat;
        background-size: 12px;
        background-position: 10px center;
    }
    #card_datatable_<?php echo $scid;  ?> .small-txt,
    #card_datatable_<?php echo $scid;  ?> small{
        font-size: 9pt;
    }
    .w3eden .table-striped tbody tr:nth-of-type(2n+1) {
        background-color: rgba(0,0,0,0.015);
    }

    @media (max-width: 799px) {
        #card_datatable_<?php echo $scid;  ?> tr {
            display: block;
            border: 3px solid rgba(0,0,0,0.3) !important;
            margin-bottom: 10px !important;
            position: relative;
        }
        #card_datatable_<?php echo $scid;  ?> thead{
            display: none;
        }
        #card_datatable_<?php echo $scid;  ?>,
        #card_datatable_<?php echo $scid;  ?> td:first-child {
            border: 0 !important;
        }
        #card_datatable_<?php echo $scid;  ?> td {
            display: block;
        }
        #card_datatable_<?php echo $scid;  ?> td.__dt_col_download_link {
            display: block;
            max-width: 100% !important;
            width: auto !important;

        }
    }


</style>
<div  class="w3eden">
<div class="card card-datatable" id="card_datatable_<?php echo $scid;  ?>">
    <div class="card-header bg-white">
        <form method="get" id="datatable_filter_<?php echo $scid; ?>">
            <input type="hidden" name="_scparams"  value="<?php echo $_scparams; ?>" />
        <div class="row">
            <div class="col-md-3">
                <input type="search"  placeholder="<?php echo __( "Search...", "download-manager" ); ?>" class="form-control search-field" onclick="this.select()" name="skw">
            </div>
            <div class="col-md-3">
                <?php wp_dropdown_categories(['taxonomy' => 'wpdmcategory', 'hide_empty' => true, 'name'  => 'category', 'id' => 'category_'.$scid, 'class' => 'form-control custom-select', 'show_option_all' => __( "All Categories", "download-manager" )]) ?>
            </div>
            <div class="col-md-2">
                <select class="form-control custom-select" id="orderby"  name="orderby">
                    <option value="title"><?php echo __( "Title", "download-manager" ); ?></option>
                    <option value="download_count"><?php echo __( "Download Count", "download-manager" ); ?></option>
                    <option value="date"><?php echo __( "Publish Date", "download-manager" ); ?></option>
                    <option value="update_date"><?php echo __( "Update Date", "download-manager" ); ?></option>
                    <option value="package_size_b"><?php echo __( "Package Size", "download-manager" ); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control custom-select" id="order"  name="order">
                    <option value="asc"><?php echo __( "Asc", "download-manager" ); ?></option>
                    <option value="desc"><?php echo __( "Desc", "download-manager" ); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary btn-block">Apply Filter</button>
            </div>
        </div>
        </form>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <?php  foreach ($colheads as $index => $colhead){ ?>
                <th class="<?php echo $cols[$index] ?>"><?php __($colhead, WPDM_TEXT_DOMAIN); ?></th>
            <?php  } ?>
        </tr>
        </thead>
        <tbody id="wpdm_datatable_<?php echo $scid;  ?>">
        <tr v-for="package in packages">
            <?php  foreach ($cols as $col){ ?>
                <td  class="<?php echo  $col; ?>"><span v-html="package.<?php echo str_replace(",", "__", $col); ?>"></span></td>
            <?php  } ?>
        </tr>
        </tbody>
    </table>
    <div class="card-footer bg-white" id="card_footer_<?php echo $scid;  ?>">
        <div class="float-right"  id="__pginate">
            <div id="_paginate">

            </div>
        </div>
        <div id="_total"  style="line-height: 38px">
            Total {{total}} items found
        </div>
    </div>
</div>
</div>
<script src="<?php echo  WPDM_BASE_URL ?>assets/js/vue.min.js"></script>
<script>
    var datatable_<?php echo $scid;  ?> = new Vue({
        el: '#wpdm_datatable_<?php echo $scid;  ?>',
        data: {
            packages: []
        }
    });
    var  scparams = '<?php echo $_scparams;  ?>';
    var paginate;
    var pages = '<?php echo $pages; ?>';

    function  createPagination(){
        jQuery('#card_footer_<?php echo $scid;  ?>  #__pginate').html('<div id="_paginate"></div>');
        Vue.component('paginate', VuejsPaginate)
        paginate =  new Vue({
            el: '#_paginate',
            template: `
                <paginate
                  :pageCount="pages"
                  :containerClass="'pagination'"
                  :clickHandler="nextPage">
                </paginate>
              `,
            methods: {
                nextPage: function(pageNum) {
                    WPDM.blockUI('#card_datatable_<?php echo $scid;  ?>');
                    jQuery.get('<?php echo  wpdm_rest_url('alldownloads'); ?>', {_scparams: scparams, cp:  pageNum}, function(response){
                        datatable_<?php echo $scid;  ?>.packages = response.packages;
                        scparams = response._scparams;
                        WPDM.unblockUI('#card_datatable_<?php echo $scid;  ?>');
                    });
                }
            }
        });
    }


    jQuery.getScript('https://unpkg.com/vuejs-paginate@2.1.0/dist/index.js', function () {

        createPagination();

    });

    jQuery(function ($) {
        $.get('<?php echo  wpdm_rest_url('alldownloads'); ?>', {_scparams: '<?php echo $_scparams;  ?>'}, function(response){
            datatable_<?php echo $scid;  ?>.packages = response.packages;
            $('#_total').html("Total <b>"+response.total+"</b> items found");
        });
        $('#datatable_filter_<?php echo $scid ?>').submit(function (e) {
            e.preventDefault();
            WPDM.blockUI('#card_datatable_<?php echo $scid;  ?>');
            $(this).ajaxSubmit({
                url:  '<?php echo  wpdm_rest_url('alldownloads'); ?>',
                success: function(response){
                    datatable_<?php echo $scid;  ?>.packages = response.packages;
                    pages = response.pages;
                    $('#_total').html('Total  <b>'+response.total+'</b> items  found');
                    createPagination();
                    WPDM.unblockUI('#card_datatable_<?php echo $scid;  ?>');
                }
            });
        });
    });
</script>

