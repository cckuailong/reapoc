<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_related_submissions.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="rmagic">
    <?php
    ?>
   <div class="operationsbar">
        
        <div class="nav">
            <ul>
                <li onclick="window.history.back()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>
              
            </ul>
        </div>

    </div>

    <!--**Content area Starts**-->
     <div class="rmagic-analytics">
        <div class="rm-analytics-table-wrapper">
     <table  class="rm-form-analytics">
                    <?php
                    if ($data->submissions)
                    {
                        //echo "<pre>",  var_dump($data->submissions);
                        ?>
                        <tr>
                            <th><?php echo RM_UI_Strings::get("LABEL_EMAIL") ?></th>
                            <th><?php echo RM_UI_Strings::get("LABEL_FORM") ?></th>
                            <th><?php echo RM_UI_Strings::get("LABEL_SUBMITTED_ON") ?></th>
                            <th><?php echo RM_UI_Strings::get("ACTION"); ?></th></tr>

                        <?php
                       
                        if (is_array($data->submissions) || is_object($data->submissions))
                            foreach ($data->submissions as $submission)
                        {
                            if($submission->submission_id != $data->submission_id){
                        ?>
                                <tr>
                                    <td><?php echo $submission->user_email; ?></td>
                                     <td><?php 
                                     $forms=new RM_Forms;
                                     $forms->load_from_db($submission->form_id);
                                     $form_name=$forms->get_form_name();
                                     echo $form_name; 
                                     
                                     ?></td>   
                                          <td><?php echo RM_Utilities::localize_time($submission->submitted_on); ?></td>
                                    <td><a href="?page=rm_submission_view&rm_submission_id=<?php echo $submission->submission_id; ?>"><?php echo RM_UI_Strings::get("VIEW"); ?></a></td>
                                </tr>

                                <?php
                            }
                        }
                        ?>
                        <?php
                    } 
    ?>
                </table>
   </div>
          </div>
  </div>
<?php } ?>