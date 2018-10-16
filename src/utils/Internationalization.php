<?php

namespace acme\mdx\plus\utils;

use acme\mdx\plus\utils\Plugin;

class Internationalization extends Plugin
{
    public function __construct()
    {
        parent::__construct();
        
        add_action('plugins_loaded', array( $this, 'load_plugin_textdomain' ));
    }

    /**
     * 指定文件夹
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            $this->text_domain,
            false,
            dirname(dirname(dirname(plugin_basename(__FILE__)))) . '/languages/'
        );
    }
}
