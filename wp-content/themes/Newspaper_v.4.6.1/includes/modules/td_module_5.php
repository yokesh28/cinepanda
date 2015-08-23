<?php

class td_module_5 extends td_module {

    function __construct($post) {
        //run the parrent constructor
        parent::__construct($post);
    }

    function render() {
        ob_start();
        ?>

        <div class="td_mod5 td_mod_wrap <?php echo $this->get_no_thumb_class();?>" <?php echo $this->get_item_scope();?>>
            <?php echo $this->get_image('art-wide');?>
            <?php echo $this->get_title(td_util::get_option('tds_mod5_title_excerpt'));?>


            <div class="meta-info">
                <?php //echo $this->get_author();?>
                <?php echo $this->get_date();?>
                <?php echo $this->get_commentsAndViews();?>
            </div>

            <div class="td-post-text-excerpt">
                <?php echo $this->get_excerpt(td_util::get_option('tds_mod5_content_excerpt'));?>
            </div>

            <?php echo $this->get_item_scope_meta();?>
        </div>

        <?php return ob_get_clean();
    }
}