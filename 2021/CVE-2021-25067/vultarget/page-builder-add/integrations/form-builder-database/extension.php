<?php


if ( ! defined( 'ABSPATH' ) ) exit;





function popb_formBuilder_database_renderFormDataTable($postID){
    ob_start();
    $smfb_formBuilder_data_list = get_post_meta($postID,'ulpb_formBuilder_data_submission',true);

    if (!function_exists('ulpb_available_pro_widgets') ) {
        echo '
            <style>
                #formSubmissionsPremiumNotice {
                    display:block !important;
                }
            </style>
        ';
    }

    $formNames = array();
    $formNameKeyEntries = array();
    $formNamesCount = 0;
    if ($smfb_formBuilder_data_list == '' || empty($smfb_formBuilder_data_list)) {
        $smfb_formBuilder_data_list = array();
    } 
    $entryDataListCount = 0;
    $smfb_formBuilder_data_listNew = array();
    foreach ($smfb_formBuilder_data_list as $key => $value) {
        $value['Form_Fields']['defaultIndex'] = $entryDataListCount;
        $searchForName = in_array( $value["Form_Name"],$formNames);
        if ($searchForName == false) {
            $formNames[$formNamesCount] = $value["Form_Name"];
            $formNamesCount++;
        }
        array_push($smfb_formBuilder_data_listNew, $value);
        $entryDataListCount++;
    }

    $smfb_formBuilder_data_list = $smfb_formBuilder_data_listNew;

    foreach ($formNames as $formName) {
        foreach ( $smfb_formBuilder_data_list as $smfb_formBuilder_single_item) {

            if ($formName !== $smfb_formBuilder_single_item['Form_Name']) {
              continue;
            }
            if (isset($formNameKeyEntries[$formName])) {
                $prevEntries = $formNameKeyEntries[$formName];
            }else{
                $prevEntries = '';
            }
            
            if (is_array($prevEntries)) {
                $thisEntry = array_merge( array( $smfb_formBuilder_single_item['Form_Fields'] ), $prevEntries) ;
            }else{
                $thisEntry = array( $smfb_formBuilder_single_item['Form_Fields'] );
            }
            
            $formNameKeyEntries[$formName] = $thisEntry;
        }
    }


    if ($smfb_formBuilder_data_list == '' || empty($smfb_formBuilder_data_list)) {

        echo "<h2>No form submissions found.</h2>";
    }else{ ?>
        <div style=' margin:0 auto; font-family:sans-serif,arial;font-size:17px; width:80%;margin-top: 50px;'>
        <?php 
        $formNumberCounter = 0;
        foreach ($formNameKeyEntries as $formNameKey => $formNameKeyEntry) {
            $dataListSize = sizeof($formNameKeyEntry);
            $dataListSize = $dataListSize -1;
            $smfb_form_Fields = $formNameKeyEntry[0];
            
            ?>
            <div class="PB_accordion_forms">
              <?php echo "<h4>$formNameKey</h4>"; ?>
              <div>
                <h4>Total Form Submissions :  <?php echo($dataListSize+1); ?> </h4>
                <h4 id="formSubmissionsPremiumNotice" style="display:none;">Latest 100 Submissions being shown, To view all Form Submissions please upgrade to premium plan.</h4>
                <table class='w3-table w3-striped w3-bordered w3-card-4 smfb_form_data_table' style="min-width: 650px;">
                <tr style="background:#69C0FB; color: #fff; padding: 5px;" class="topHeaderRow_formTable">
                  <th> # </th>
                <?php foreach ( $smfb_form_Fields as $smfb_field_name => $smfb_formBuilder_single_field) {
                   $smfb_field_name = str_replace('_', ' ', $smfb_field_name);
                       if ($smfb_field_name == 'defaultIndex') {
                           continue;
                       }else {
                        echo "<th> $smfb_field_name </th>";
                       }
                    } ?>
                  <th> Delete </th>
                  <th> View </th>
                  </tr>
                    <?php
                      $entryCounter = 1;
                      $delEntryCounter = count($formNameKeyEntry);
                      foreach ($formNameKeyEntry as $formNameKeyEntryKey => $smfb_formBuilder_single_item) {

                        if (function_exists('ulpb_available_pro_widgets') ) {
                        
                        }else {
                            if ($entryCounter > 100) {
                                continue;
                            }
                        }
                        
                        if (isset($smfb_formBuilder_single_item['date'])) {
                            $submittedDate = $smfb_formBuilder_single_item['date'];
                        }else{
                            $submittedDate = 'Not Set';
                        }

                        ?>
                          <tr>
                            <td><br> <?php echo $entryCounter; ?> <br><br></td>
                            <?php
                                foreach ($smfb_formBuilder_single_item as $valeKey => $vale) {
                                    if ($valeKey == 'defaultIndex') {
                                        continue;
                                    }else{
                                        echo "<td><br> $vale <br><br></td>";
                                    }
                                }
                            ?>
                            
                        
                            <td>
                                <div class="entryDeleteBtn edb-<?php echo($smfb_formBuilder_single_item['defaultIndex']); ?>" data-entryIndex="<?php echo($smfb_formBuilder_single_item['defaultIndex']); ?>" ><span class="dashicons dashicons-trash" data-entryIndex="<?php echo($smfb_formBuilder_single_item['defaultIndex']); ?>"></span></div>
                            </td>
                                    
                                    
                            
                            <td>
                              <div class="entryViewBtn">View</div>
                            </td>
                          </tr>

                        <?php $entryCounter++;
                        $delEntryCounter--; 
                        } ?>
                  
                    </table>
                    <script type="text/javascript">

                            <?php
                            $formNameKeyWithoutSpaces = str_replace(' ', '', $formNameKey);
                            $formNameKeyWithoutSpaces = str_replace('.', '', $formNameKeyWithoutSpaces);
                            $formNameKeyWithoutSpaces = str_replace('-', '', $formNameKeyWithoutSpaces);
                            $formNameKeyWithoutSpaces = str_replace('+', '', $formNameKeyWithoutSpaces);
                            
                             echo "var allFormDataObject_$formNameKeyWithoutSpaces"; ?> = [ <?php 
                                echo "[";
                                    foreach ($formNameKeyEntry[0] as $key => $val) {
                                        if ($key == 'defaultIndex') {
                                            continue;
                                        }else{
                                            $key = str_replace('\'', '"', $key);
                                            $key = str_replace('_', ' ', $key);
                                            echo "'$key'".",";
                                        }
                                        
                                    }
                                    echo "],";
                              foreach ($formNameKeyEntry as $formNameKeyEntryKey => $smfb_formBuilder_single_item) {

                                    $ThisForm_Field = $smfb_formBuilder_single_item;
                                    echo "[";
                                    foreach ($ThisForm_Field as $key => $value) {
                                        if ($key == 'defaultIndex') {
                                            continue;
                                        }else{
                                            $value = str_replace('\'', '"', $value);
                                            echo "'$value'".",";
                                        }
                                        
                                    }
                                    echo "],";

                                } ?>];

                    </script>
                    <br>
                    <br>
                    <?php 
                        if (function_exists('ulpb_available_pro_widgets') ) {
                            echo "<div class='btn-green large-btn downloadFormDatabtn' style='float:left;'  data-formID='allFormDataObject_$formNameKeyWithoutSpaces' data-formName='$formNameKey' >Download Data</div>";
                        }
                    ?>
                    
              </div>
            </div>

            <?php 
            $formNumberCounter++;
        }
          ?>
        
        


        </div>

        <br>
        <br>
        <br>

        
        <form id="formBuilderDataListEmpty">
            <input type="hidden" name="ps_ID" value="<?php echo $postID; ?>">
            <div class="btn-red large-btn emptyFormDataBtn" style="max-width:150px;float: right; background: #f44336;">Delete All Data</div>

            <p style="margin: 25px; font-size: 25px;"></p>
        </form>


        <?php 
        if (function_exists('ulpb_available_pro_widgets') ) { ?>

            <script type="text/javascript">
            ( function( $ ) {

                function exportToCsv(filename, rows) {
                    var processRow = function (row) {
                        var finalVal = '';
                        for (var j = 0; j < row.length; j++) {
                            var innerValue = row[j] === null ? '' : row[j].toString();
                            if (row[j] instanceof Date) {
                                innerValue = row[j].toLocaleString();
                            };
                            var result = innerValue.replace(/"/g, '""');
                            if (result.search(/("|,|\n)/g) >= 0)
                                result = '"' + result + '"';
                            if (j > 0)
                                finalVal += ',';
                            finalVal += result;
                        }

                        return finalVal + '\r\n';
                    };

                    var csvFile = '';
                    for (var i = 0; i < rows.length; i++) {
                        csvFile += processRow(rows[i]);
                    }

                    var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
                    if (navigator.msSaveBlob) { // IE 10+
                        navigator.msSaveBlob(blob, filename);
                    } else {
                        var link = document.createElement("a");
                        if (link.download !== undefined) { // feature detection
                            // Browsers that support HTML5 download attribute
                            var url = URL.createObjectURL(blob);
                            link.setAttribute("href", url);
                            link.setAttribute("download", filename);
                            link.style.visibility = 'hidden';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                    }
                }
        
                $('.downloadFormDatabtn').on('click',function(){
                    var selectedForm = $(this).attr('data-formID');
                    var data_formName = $(this).attr('data-formName');
                    var thisData = eval(selectedForm)
                    var utc = new Date().toJSON().slice(0,10).replace(/-/g,'_');
                    exportToCsv('PluginOps_'+data_formName+'_data_'+utc+'.csv',thisData);
                });
            })(jQuery);
            </script>
            
        <?php } ?>
    
    <?php
    }

$formTabeRendered = ob_get_contents();
ob_end_clean();

return $formTabeRendered;

}

?>