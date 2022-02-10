<?php 
    ob_start();

    foreach($categories as $category) {
        ?>
        <div>
            <label>
                <input type="checkbox" name="category" value="<?php echo $category->term_id; ?>"/> 
                <?php echo $category->name; ?>
            </label>
        </div>
        <?php
    }

    $category_list = ob_get_clean();
?>

<div class="tutor-instructor-filter" 
    <?php 
        foreach($attributes as $key => $value) {
            echo 'data-' . $key . '="' . $value . '" ';
        }
    ?>>
    <div class="tutor-instructor-filter-sidebar">
        <div>
            <div class="tutor-category-text">
                <span>Category</span>
                <span class="clear-instructor-filter">
                    <i class="tutor-icon-line-cross"></i> <span><?php _e('Clear All', 'tutor'); ?></span>
                </span>
            </div>
            <br/>
        </div>
        <div class="course-category-filter">
            <?php echo $category_list; ?>
        </div>
    </div>
    <div class="tutor-instructor-filter-result">
        <div class="filter-pc">
            <div class="keyword-field">
                <i class="tutor-icon-magnifying-glass-1"></i>
                <input type="text" name="keyword" placeholder="<?php _e('Search any instructor...', 'tutor'); ?>"/>
            </div>
        </div>
        <div class="filter-mobile">
            <div class="mobile-filter-container">
                <div class="keyword-field mobile-screen">
                    <i class="tutor-icon-magnifying-glass-1"></i>
                    <input type="text" name="keyword" placeholder="<?php _e('Search any instructor...', 'tutor'); ?>"/>
                </div>
                <i class="tutor-icon-filter-tool-black-shape"></i>
            </div>
            <div class="mobile-filter-popup">
                <div>
                    <div class="tutor-category-text">
                        <div class="expand-instructor-filter"></div>
                        <span>Category</span>
                        <span class="clear-instructor-filter">
                            <i class="tutor-icon-line-cross"></i> <span><?php _e('Clear All', 'tutor'); ?></span>
                        </span>
                    </div>
                    <div>
                        <?php echo $category_list; ?>
                    </div>
                    <div>
                        <button class="tutor-btn btn-sm">
                            <?php _e('Apply Filter', 'tutor'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="selected-cate-list">

            </div>
        </div>
        <div class="filter-result-container">
            <?php echo $content; ?>
        </div>
    </div>
</div>