<?php
echo '<div class="psr_genera_container_boxed_stats">';
foreach($answers as $answer){
  echo '<div class="survey_progress_bar_generalcontainer">';
  echo '<h4>'.$answer['text'].'</h4>';
  foreach (prsv_return_graph($answers_totals[$answer['answer_id']], $answers_labels[$answer['answer_id']], prsv_colors_palette()) as $stats) {
    echo '<div class="survey_progress_bar">';
    echo '<div class="survey_value_single_bar" style="width:'.$stats[2].'%;"></div>';
    echo '</div>';
    echo '<div class="survey_progress_bar_legend"><ul>';
    echo '<li><span class="survey_progress_bar_legend_label">'.$stats[1].' - <strong>'.number_format($stats[2], 2).'%</strong></span></li>';
    echo '</ul></div>';
  };
  echo '</div>';
}
echo '</div>';
?>
