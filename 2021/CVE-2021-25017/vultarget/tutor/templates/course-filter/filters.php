<?php
    $filter_object = new \TUTOR\Course_Filter();
    $filter_levels = array(
        'beginner'=> __('Beginner', 'tutor'),
        'intermediate'=> __('Intermediate', 'tutor'),
        'expert'=> __('Expert', 'tutor')
    );
    $filter_prices=array(
        'free'=> __('Free', 'tutor'),
        'paid'=> __('Paid', 'tutor'),
    );

    $supported_filters = tutor_utils()->get_option('supported_course_filters', array());
    $supported_filters = array_keys($supported_filters);
?>
<form>  
    <?php do_action('tutor_course_filter/before'); ?>
    <?php
        if(in_array('search', $supported_filters)){
            ?>
            <div class="tutor-course-search-field">
                <input type="text" name="keyword" placeholder="<?php _e('Search...'); ?>"/>
                <i class="tutor-icon-magnifying-glass-1"></i>
            </div>
            <?php
        }
    ?>
    <div>
        <?php
            if(in_array('category', $supported_filters)){
                ?>
                <div>
                    <h4><?php _e('Category', 'tutor'); ?></h4>
                    <?php $filter_object->render_terms('category'); ?>
                </div>
                <?php
            }

            if(in_array('tag', $supported_filters)){
                ?>
                <div>
                    <h4><?php _e('Tag', 'tutor'); ?></h4>
                    <?php $filter_object->render_terms('tag'); ?>
                </div>
                <?php
            }
        ?>
    </div>
    <div>
        <?php
            if(in_array('difficulty_level', $supported_filters)){
                ?>
                <div>
                    <h4><?php _e('Level', 'tutor'); ?></h4>
                    <?php 
                        foreach($filter_levels as $value=>$title){
                            ?>
                                <label>
                                    <input type="checkbox" name="tutor-course-filter-level" value="<?php echo $value; ?>"/>&nbsp;
                                    <?php echo $title; ?>
                                </label>
                            <?php
                        }
                    ?>
                </div>
                <?php
            }

            
            $is_membership = get_tutor_option('monetize_by')=='pmpro' && tutils()->has_pmpro();
            if(!$is_membership && in_array('price_type', $supported_filters)){
                ?>
                <div>
                    <h4><?php _e('Price', 'tutor'); ?></h4>
                    <?php 
                        foreach($filter_prices as $value=>$title){
                            ?>
                                <label>
                                    <input type="checkbox" name="tutor-course-filter-price" value="<?php echo $value; ?>"/>&nbsp;
                                    <?php echo $title; ?>
                                </label>
                            <?php
                        }
                    ?>
                </div>
                <?php
            }
        ?>
    </div>
    <div class="tutor-clear-all-filter">
        <a href="#" onclick="window.location.reload()">
            <i class="tutor-icon-cross"></i> Clear All Filter
        </a>
    </div>
    <?php do_action('tutor_course_filter/after'); ?>
</form>