<?php
namespace acme\mdx\plus;

use acme\mdx\plus\app\MdxPlus;
use acme\mdx\plus\utils\Plugin;
use acme\mdx\plus\utils\Settings;
use acme\mdx\plus\app\PrismJSAuto;
use acme\mdx\plus\app\InstantClick;
use acme\mdx\plus\app\CustomizeAttribute;
use acme\mdx\plus\utils\Internationalization;

class Main extends Plugin
{
    public function __construct()
    {
        parent::__construct();

        $this->init();
    }

    public function init()
    {
        if (is_admin()) {
            // 初始化国际化
            new Internationalization();
            // 实现设置类
            new Settings();
        } else {
            // 初始化instantClick.js
            $this->get_option('support_instantclick', 'mdx_plus_instantclick') == 'on' ? new InstantClick() : null;
        }

        // 主题增强
        new MdxPlus();
        // 处理自定义属性
        new CustomizeAttribute();

        // 根据选项开启相关选项
        $this->get_option('highlight_mode_auto', 'mdx_plus_syntax_highlighting') == 'on' ? new PrismJSAuto() : null;
    }
}
