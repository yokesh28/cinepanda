<?php

/*  ----------------------------------------------------------------------------
    This is the search box used at the top of the search results
    It's used by /search.php
 */

?>

<h1 itemprop="name" class="entry-title td-page-title">
    <span class="td-search-query"><?php echo get_search_query(); ?></span> - <span> <?php  echo __td('search results');?></span>
</h1>

<div class="search-page-search-wrap">

    <form role="search" method="get" class="td-search-form-widget" action="<?php echo home_url( '/' ); ?>">
        <div>
            <input class="td-widget-search-input" type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" /><input class="wpb_button wpb_btn-inverse btn" type="submit" id="searchsubmit" value="<?php _etd('Search')?>" />
        </div>
    </form>
</div>

<div class="td_search_subtitle">
    <?php _etd("If you_re not happy with the results, please do another search");?>
</div>