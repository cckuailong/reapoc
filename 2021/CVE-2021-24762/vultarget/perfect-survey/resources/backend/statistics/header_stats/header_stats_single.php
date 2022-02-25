<?php
echo '<div class="psr_genera_container_boxed_stats">';
foreach (prsv_return_graph($answers_totals, $answers_labels, prsv_colors_palette()) as $stats) {
  echo '<div class="survey_progress_bar">';
  echo '<div class="survey_value_single_bar" style="width:'.$stats[2].'%; background-color:'.$stats[2].'"></div>';
  echo '</div>';
  echo '<div class="survey_progress_bar_legend"><ul>';
  echo '<li><span class="survey_progress_bar_legend_label">'.$stats[1].' - <strong>'.number_format($stats[2], 2).'%</strong></span></li>';
  echo '</ul></div>';
};
echo '</div>';
?>
