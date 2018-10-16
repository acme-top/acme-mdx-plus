<?php
namespace acme\mdx\plus\app;

use acme\mdx\plus\utils\Plugin;

class CustomizeAttribute extends Plugin
{
    public function __construct()
    {
        parent::__construct();

        // 较低的优先级，在内容生成之后进行
        add_filter('the_content', array( $this, 'customize_attribute' ), 20);
    }

    /**
     * 自定义文字段落属性
     * @param       string      $content
     * @return      string
     */
    public function customize_attribute($content)
    {
        /* 段落加样式属性 */
        $content = preg_replace_callback('#<p><!--(?=\.)(.*?)--></p>[\s\S]*?<p>(.*?)</p>#', array( $this, 'para_add_custom_class' ), $content);
        $content = preg_replace_callback('#<p><!--begin(.*?)--></p>([\s\S]*?)<p><!--end\1--></p>#', array( $this, 'multi_para_add_custom_class' ), $content);
            
        return $content;
    }
        
    /**
     * 对段落添加自定义class
     * @param       array      $matches
     * @return      string
     */
    public function para_add_custom_class($matches)
    {
        $class = str_replace('.', ' ', $matches[1]);
        if (strpos($matches[2], '<em>') === 0) {
            $matches[2] = preg_replace('#^<em>(.*?)</em>[\:,\.\!：，。！ ]*([\w\W]*)#', "<em class=\"callout-title\">$1</em><p>$2</p>", $matches[2]);
        }

        return sprintf('<div class="callout%s">%s</div>', $class, $matches[2]);
    }
        
    /**
     * 对多段落添加自定义class
     * @param       array      $matches
     * @return      string
     */
    public function multi_para_add_custom_class($matches)
    {
        $class = str_replace('.', ' ', $matches[1]);
        if (strpos($matches[2], '<p><em>') !== false) {
            $matches[2] = preg_replace('#<p><em>(.*?)</em>[\:,\.\!：，。！ ]*#', "<em class=\"callout-title\">$1</em><p>", $matches[2]);
        }

        return sprintf('<div class="callout%s">%s</div>', $class, $matches[2]);
    }
}
