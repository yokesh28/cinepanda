<div class="search-box pull-right clearfix">
    <form action="<?php echo esc_url(home_url('/')); ?>" class="search-form clearfix" method="get">
        <input type="search" onblur="if (this.value == '')
                    this.value = this.defaultValue;" onfocus="if (this.value == this.defaultValue)
                                this.value = '';" value="<?php _e('Enter keyworks', kopa_get_domain()); ?>"  name="s" class="search-text">
        <input type="submit" value="<?php _e('Search', kopa_get_domain()); ?>" class="search-submit">
    </form>
</div>
<!--search-box-->