<?php
namespace acme\mdx\plus\app;

use acme\mdx\plus\utils\Plugin;

class InstantClick extends Plugin
{
    public function __construct()
    {
        parent::__construct();

        add_filter('wp_footer', array( $this, 'before_footer'), 0, 0);
        add_filter('wp_footer', array( $this, 'after_footer'), 100, 0);
    }

    /**
     * 注销所有滚动事件
     */
    public function before_footer( ){
        ?>
        <script type="text/javascript">
            $(window).off("scroll", "**");
        </script>
        <?php
    }


    /**
     * 加载instantclick
     */
    public function after_footer()
    {
        echo '<script src="' . plugin_dir_url(dirname(dirname(__FILE__))) . 'js/instantclick.min.js" data-no-instant></script>';
        echo "\n";
        echo '<script src="' . plugin_dir_url(dirname(dirname(__FILE__))) . 'js/instantclick.init.js" data-no-instant></script>';
        echo "\n";
    }
}
