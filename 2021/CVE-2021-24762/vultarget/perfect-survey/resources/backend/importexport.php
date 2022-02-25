<?php if (!defined('ABSPATH'))  exit; // Exit if accessed directly ?>
<?php

$surveys = prsv_get_post_type_model()->get_all_surveys();
if(!empty($_POST['action']))
{
  switch($_POST['action'])
  {
    case 'export':

    $data = array();

    foreach($_POST['surveys'] as $postID)
    {
      $data[] = prsv_get_post_type_model()->get_survey_complete($postID);
    }

    @ob_end_clean();
    @ob_start();
    $data = json_encode($data);
    header('Content-type: application/json');
    header('Content-disposition: attachment; filename="export-survey-"'.date("YmdHis").'.json"');
    echo $data;
    exit;

    break;

    case 'import':

    if(empty($_FILES) || empty($_FILES['importfile']) || $_FILES['importfile']['type'] != 'application/json')
    {
      header('Location: '.admin_url('edit.php?post_type=ps&page=importexport&message='.__('File must be a valid JSON export from '.PRSV_PLUGIN_NAME).'&type=error'));
      exit;
    }

    $surveys =  json_decode(file_get_contents($_FILES['importfile']['tmp_name']),true);

    $res = !empty($surveys);

    foreach($surveys as $survey){
      $res = $res && prsv_get_post_type_model()->import_survey($survey);
    }

    header('Location: '.admin_url('edit.php?post_type=ps&page=importexport&message='.__('Import '.($res ? 'Success' : 'Failed')).'&type='.($res ? 'success' : 'error')));
    exit;
    break;
  }
}
?>
<div id="importexport" class="wrap">
  <h2><?php _e('Import or export your survey', 'perfect-survey') ?></h2>
  <?php if(isset($_GET['message'])){ ?>
    <p class="message-<?php echo $_GET['type'];?>"><?php echo $_GET['message'];?></p>
  <?php } ?>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="psrv_boximport_export_survey survey-block">
      <table class="widefat survey_settings survey_input" cellspacing="0">
        <thead>
          <tr>
            <th colspan="3"><?php _e('Export your surveys', 'perfect-survey') ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="2">
              <p><?php _e('Select the surveys you want to export. Use the download button to export to a .json file that you can then import into another Perfect Survey installation.', 'perfect-survey') ?></p>
            </td>
          </tr>
          <tr>
            <td class="label">
              <label><?php _e('Select your surveys', 'perfect-survey') ?></label>
            </td>
            <td>
              <div class="survey-wrap-input">
                <ul class='survey_select_all_export'>
                  <li>
                    <label>
                      <input type='checkbox' id="btn-survery-export-all"> <?php _e('Export all survey', 'perfect-survey') ?>
                    </label>
                  </li>
                  <?php
                  if($surveys)
                  {
                    foreach($surveys as $survey)
                    {
                      ?>
                      <li>
                        <label>
                          <input type='checkbox' name='surveys[]' value="<?php echo $survey['ID'];?>"> <?php echo $survey['post_title'] ? $survey['post_title'] : __('(no title)') ?>
                        </label>
                      </li>
                      <?php
                    }
                  }
                  else
                  {
                    ?>
                    <li>
                      <?php _e('No survey found!');?>
                    </li>
                    <?php
                  }
                  ?>
                </ul>
                <?php
                if($surveys)
                {
                  ?>
                  <ul>
                    <li>
                      <button type="submit" name="action" value="export" class='button button-primary button-large'> <?php _e('Download selected survey', 'perfect-survey') ?></button>
                    </li>
                  </ul>
                  <?php
                }
                ?>
              </div>
            </form>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="survey-block">
    <table class="widefat survey_settings survey_input" cellspacing="0">
      <thead>
        <tr>
          <th colspan="3"><?php _e('Import your surveys', 'perfect-survey') ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="2">
            <p><?php _e('Select the Perfect Survey JSON file you want to import. When you click the import button below, Perfect Survey will import your survey', 'perfect-survey') ?></p>
          </td>
        </tr>
        <tr>
          <td class="label">
            <label><?php _e('Select file', 'perfect-survey') ?></label>
          </td>
          <td>
            <div class="survey-wrap-input">
              <ul class='survey_select_all_export'>
                <li>
                  <input type='file' name="importfile" placeholder="<?php _e('Choose JSON file to import');?>" />
                </li>
              </ul>
              <ul>
                <li>
                  <button type="submit" name="action" value="import" class='button button-primary button-large'> <?php _e('Import', 'perfect-survey') ?></button>
                </li>
              </ul>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</form>
</div>
<script>
jQuery(function($){
  $("#btn-survery-export-all").click(function(){
    $('.survey_select_all_export input[type=checkbox]').not('#btn-survery-export-all').prop('checked',$(this).prop('checked'));
  });
})
</script>
