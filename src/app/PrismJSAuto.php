<?php

namespace acme\mdx\plus\app;

use acme\mdx\plus\utils\Plugin;

class PrismJSAuto extends Plugin
{

    /**
     * 资源地址
     */
    public $assets_addr;

    public function __construct()
    {
        parent::__construct();

        // MDX PLUS自定义设置
        if ($this->get_option('highlight_mode_auto', 'mdx_plus_syntax_highlighting') == 'on') {
            $this->assets_addr = $this->get_option('assets_addres', 'mdx_plus_syntax_highlighting');
            add_action('wp_enqueue_scripts', array( $this, 'prism_styles_scripts' ));
        }
        // 插件WP editormd的配置
        elseif ($this->get_option('highlight_mode_auto', 'syntax_highlighting') == 'on') {
            $this->assets_addr = $this->get_option('editor_addres', 'editor_style');
        }
        
        add_filter('script_loader_tag', array( $this, 'prism_autoloader_filter' ), 10, 3);
    }

    public function prism_styles_scripts()
    {
        $prism_base_url = $this->get_option('assets_addres', 'mdx_plus_syntax_highlighting') . '/assets/Prism.js'; //资源载入地址
        $prism_theme    = $this->get_option('highlight_library_style', 'mdx_plus_syntax_highlighting'); //语法高亮风格
        $line_numbers   = $this->get_option('line_numbers', 'mdx_plus_syntax_highlighting') == 'on' ? true : false; //行号显示
        $show_language  = $this->get_option('show_language', 'mdx_plus_syntax_highlighting') == 'on' ? true : false; //显示语言
        $copy_clipboard = $this->get_option('copy_clipboard', 'mdx_plus_syntax_highlighting') == 'on' ? true : false; //粘贴

        $toolbar        = $show_language == true ? true : false; //工具栏

        $prism_plugins  = array(
            'autoloader' => array(
                'js'  => true,
                'css' => false
            ),
            'toolbar' => array(
                'js'  => $toolbar,
                'css' => $toolbar
            ),
            'line-numbers' => array(
                'css' => $line_numbers,
                'js'  => $line_numbers
            ),
            'show-language' => array(
                'js'  => $show_language,
                'css' => false
            ),
            'copy-to-clipboard' => array(
                'js'  => $copy_clipboard,
                'css' => false
            ),
        );
        $prism_styles   = array();
        $prism_scripts  = array();

        $prism_scripts['prism-core-js'] = $prism_base_url . '/components/prism-core.min.js';

        if (empty($prism_theme) || $prism_theme == 'default') {
            $prism_styles['prism-theme-default'] = $prism_base_url . '/themes/prism.css';
        } elseif ($prism_theme == 'customize') {
            $prism_styles['prism-theme-style'] = $this->get_option('customize_my_style', 'mdx_plus_syntax_highlighting'); //自定义风格
        } else {
            $prism_styles['prism-theme-style'] = $prism_base_url . "/themes/prism-{$prism_theme}.css";
        }
        foreach ($prism_plugins as $prism_plugin => $prism_plugin_config) {
            if ($prism_plugin_config['css'] === true) {
                $prism_styles["prism-plugin-{$prism_plugin}"] = $prism_base_url . "/plugins/{$prism_plugin}/prism-{$prism_plugin}.css";
            }
            if ($prism_plugin_config['js'] === true) {
                $prism_scripts["prism-plugin-{$prism_plugin}"] = $prism_base_url . "/plugins/{$prism_plugin}/prism-{$prism_plugin}.min.js";
            }
        }

        // 代码粘贴代码增强
        // 引入clipboard
        $lib_url = $this->get_option('editor_addres', 'editor_style') . '/assets/ClipBoard/clipboard.min.js';

        if ($copy_clipboard) {
            wp_enqueue_script('copy-clipboard', $lib_url, array(), '2.0.1', true);
        }

        foreach ($prism_styles as $name => $prism_style) {
            wp_enqueue_style($name, $prism_style, array(), '1.15.0', 'all');
        }

        foreach ($prism_scripts as $name => $prism_script) {
            wp_enqueue_script($name, $prism_script, array(), '1.15.0', true);
        }
    }

    /**
     * 拦截并处理 “prism-plugin-autoloader”
     */
    public function prism_autoloader_filter($tag, $handle, $src)
    {
        if ('prism-plugin-autoloader' === $handle) {
            $tag = '<script type="text/javascript">function getPrismAutoloaderLanguagesPath(){return "' . $this->assets_addr . '/assets/Prism.js/components/";}</script>';
            $tag .= "\n";
            $tag .= '<script type="text/javascript" src="' . plugin_dir_url(dirname(dirname(__FILE__))) . 'js/Prism.js/plugins/autoloader/prism-autoloader.min.js"></script>';
            $tag .= "\n";
        }

        return $tag;
    }
}
