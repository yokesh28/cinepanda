<div id="tab-single-post" class="kopa-content-box tab-content tab-content-1">    

    <div class="kopa-box-head">
        <?php echo KopaIcon::getIcon('hand-right'); ?>
        <span class="kopa-section-title"><?php _e('Featured Post Thumbnail', kopa_get_domain()); ?></span>
    </div><!--kopa-box-head-->
    <div class="kopa-box-body">
        <div class="kopa-element-box kopa-theme-options">
            <span class="kopa-component-title"><?php _e('Show/Hide Featured Post Thumbnail in single post', kopa_get_domain()); ?></span>            
            <?php
            $kopa_featured_image_post_status = array(
                'show' => __('Show', kopa_get_domain()),
                'hide' => __('Hide', kopa_get_domain())
            );
            $kopa_featured_image_name = "kopa_theme_options_featured_image_status";
            foreach ($kopa_featured_image_post_status as $value => $label) {
                $kopa_featured_image_status_id = $kopa_featured_image_name . "_{$value}";
                ?>
                <label  for="<?php echo $kopa_featured_image_status_id; ?>" class="kopa-label-for-radio-button"><input type="radio" value="<?php echo esc_attr( $value ); ?>" id="<?php echo $kopa_featured_image_status_id; ?>" name="<?php echo $kopa_featured_image_name; ?>" <?php echo ($value == get_option($kopa_featured_image_name, 'show')) ? 'checked="checked"' : ''; ?>><?php echo $label; ?></label>
                <?php
            } // end foreach
            ?>
        </div>
    </div>

    <div class="kopa-box-head">
        <?php echo KopaIcon::getIcon('hand-right'); ?>
        <span class="kopa-section-title"><?php _e('About Author', kopa_get_domain()); ?></span>
    </div><!--kopa-box-head-->
    <div class="kopa-box-body">
        <div class="kopa-element-box kopa-theme-options">            
            <?php
            $about_author_status = array(
                'show' => __('Show', kopa_get_domain()),
                'hide' => __('Hide', kopa_get_domain())
            );
            $about_author_name = "kopa_theme_options_post_about_author";
            foreach ($about_author_status as $value => $label) {
                $about_author_id = $about_author_name . "_{$value}";
                ?>
                <label  for="<?php echo $about_author_id; ?>" class="kopa-label-for-radio-button"><input type="radio" value="<?php echo $value; ?>" id="<?php echo $about_author_id; ?>" name="<?php echo $about_author_name; ?>" <?php echo ($value == get_option($about_author_name, 'show')) ? 'checked="checked"' : ''; ?>><?php echo $label; ?></label>
                <?php
            } // endforeach
            ?>
        </div>
    </div>



    <div class="kopa-box-head">
        <?php echo KopaIcon::getIcon('hand-right'); ?>
        <span class="kopa-section-title"><?php _e('Related Posts', kopa_get_domain()); ?></span>
    </div><!--kopa-box-head-->

    <div class="kopa-box-body">

        <div class="kopa-element-box kopa-theme-options">
            <span class="kopa-component-title"><?php _e('Get By', kopa_get_domain()); ?></span>
            <select class="" id="kopa_theme_options_post_related_get_by" name="kopa_theme_options_post_related_get_by">
                <?php
                $post_related_get_by = array(
                    'hide'     => __('-- Hide --', kopa_get_domain()),
                    'post_tag' => __('Tag', kopa_get_domain()),
                    'category' => __('Category', kopa_get_domain())
                );
                foreach ($post_related_get_by as $value => $title) {
                    printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value == get_option('kopa_theme_options_post_related_get_by', 'post_tag')) ? 'selected="selected"' : '');
                }
                ?>
            </select>
        </div>

        <div class="kopa-element-box kopa-theme-options">
            <span class="kopa-component-title"><?php _e('Limit', kopa_get_domain()); ?></span>
            <input type="number" value="<?php echo get_option('kopa_theme_options_post_related_limit', 4); ?>" id="kopa_theme_options_post_related_limit" name="kopa_theme_options_post_related_limit">
        </div>
    </div><!--tab-theme-skin-->  

</div><!--tab-container-->