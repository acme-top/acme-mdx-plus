<?php
namespace acme\mdx\plus\utils;

class Plugin
{
    /**
     * 唯一标识符
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * 翻译域
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $text_domain
     */
    protected $text_domain;

    /**
     * 插件版本
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;
    
    /**
     * 定义插件核心功能
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->plugin_name = 'WP Acme Mdx Plus';
        $this->text_domain = 'acme-mdx-plus';
        $this->version     = WP_ACME_MDX_PLUS_VER;
    }
    
    /**
     * 获取字段值
     *
     * @param string $option  字段名称
     * @param string $section 字段名称分组
     * @param string $default 没搜索到返回空
     *
     * @return mixed
     */
    public function get_option($option, $section, $default = '')
    {
        $options = get_option($section);

        if (isset($options[ $option ])) {
            return $options[ $option ];
        }

        return $default;
    }
}
