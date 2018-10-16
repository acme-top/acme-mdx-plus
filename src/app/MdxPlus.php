<?php
namespace acme\mdx\plus\app;

use acme\mdx\plus\utils\Plugin;

class MdxPlus extends Plugin
{
    public function __construct()
    {
        parent::__construct();

        // 移除默认的MDX JS，使用修改后的
        remove_action('wp_enqueue_scripts', 'mdx_js');

        // 载入 css & js
        add_action('wp_enqueue_scripts', array( $this, 'mdx_css' ));
        add_action('wp_enqueue_scripts', array( $this, 'mdx_js' ));
 
        if (is_admin()) {
            // 处理文章内容
            add_filter('wp_insert_post_data', array( $this, 'insert_post_data'), 100, 2);
        }
    }

    /**
     * 载入css & js
     */
    public function mdx_css()
    {
        wp_register_style('acme_mdx_plus_css', plugin_dir_url(dirname(dirname(__FILE__))) . 'css/style.css', array( 'mdx_mdui_css' ), '1.0', 'all', true);
        wp_enqueue_style('acme_mdx_plus_css');
    }

    /**
     * 载入css & js
     */
    public function mdx_js()
    {
        wp_register_script('mdx_jquery', get_template_directory_uri().'/js/jquery.min.js', false, '', false);
        wp_register_script('mdx_mdui_js', get_template_directory_uri().'/mdui/js/mdui.min.js', false, '', true);
        wp_register_script('mdx_sl_js', get_template_directory_uri().'/js/lazyload.js', false, '', true);
        wp_enqueue_script('mdx_jquery');
        wp_enqueue_script('mdx_mdui_js');

        if (mdx_get_option("mdx_auto_night_style")=="true") {
            wp_register_script('mdx_ns_js', get_template_directory_uri().'/js/nsc.js', false, '', true);
            wp_enqueue_script('mdx_ns_js');
        }
        if (mdx_get_option("mdx_smooth_scroll")=="true") {
            wp_register_script('mdx_ss_js', get_template_directory_uri().'/js/smooth.js', false, '', true);
            wp_enqueue_script('mdx_ss_js');
        }
        if (mdx_get_option("mdx_real_search")=="true") {
            wp_register_script('mdx_rs_js', get_template_directory_uri().'/js/search.js', false, '', true);
            wp_enqueue_script('mdx_rs_js');
        }
        if (is_home()) {
            wp_register_script('mdx_ajax_js', get_template_directory_uri().'/js/ajax.js', false, '', true);
            wp_enqueue_script('mdx_ajax_js');
        } elseif (is_category()||is_archive()||is_search()) {
            wp_register_script('mdx_ajax_js', get_template_directory_uri().'/js/ajax_other.js', false, '', true);
            wp_enqueue_script('mdx_ajax_js');
        }

        //if(is_single() || is_page()){
        wp_register_script('mdx_qr_js', get_template_directory_uri().'/js/qr.js', false, '', false);
        wp_register_script('mdx_ra_js', get_template_directory_uri().'/js/ra.js', false, '', false);
        wp_register_script('mdx_h2c_js', get_template_directory_uri().'/js/h2c.js', false, '', false);
        wp_enqueue_script('mdx_qr_js');
        wp_enqueue_script('mdx_ra_js');
        wp_enqueue_script('mdx_h2c_js');
        //}
        wp_enqueue_script('mdx_sl_js');

        if (is_single() || is_page()) {
            wp_register_script('acme_mdx_plus_doc_toc_js', plugin_dir_url(dirname(dirname(__FILE__))) . 'js/jquery.doc.toc.js', array( 'mdx_jquery' ), '1.0', true);
            wp_register_script('acme_mdx_plus_js', plugin_dir_url(dirname(dirname(__FILE__))) . 'js/acme.mdx.plus.js', array( 'acme_mdx_plus_doc_toc_js' ), '1.0', true);
            wp_enqueue_script('acme_mdx_plus_doc_toc_js');
            wp_enqueue_script('acme_mdx_plus_js');
        }
    }

    /**
     * 保存文章后
     */
    public function insert_post_data($data, $postarr)
    {
        $types = [ 'post', 'page', 'revision' ];

        // 仅当POST内容为指定的类型时才进行处理
        if (!in_array($data['post_type'], $types)) {
            return $data;
        }

        // 是否使文章或者页面支持Markdown语法
        $wpcom_publish_posts_with_markdown = get_option('wpcom_publish_posts_with_markdown');

        // 仅在开启markdown语法后才对图片、表格进行处理
        if (!$wpcom_publish_posts_with_markdown) {
            return $data;
        }

        // 还原转义的字符串
        $data['post_content'] = stripslashes($data['post_content']);

        // 处理图片
        $data['post_content'] = preg_replace_callback('#<(img)([^>]+?)(>(.*?)</\\1>|[\/]?>)#si', array( $this, 'process_image' ), $data['post_content']);

        // 处理表格
        $data['post_content'] = preg_replace_callback('#<(table)([^>]*?)>#si', array( $this, 'process_table' ), $data['post_content']);

        return $data;
    }

    /**
     * 处理图片
     */
    public function process_image($matches)
    {
        $old_attributes_str = $matches[2];
        $img = wp_kses_hair($old_attributes_str, wp_allowed_protocols());

        if (empty($img['src'])) {
            return $matches[0];
        }

        // 获取上传的路径
        $upload_path = get_option('upload_path', 'wp-content/uploads');

        // 判断是否以斜杠结尾，否则补上
        if (substr_compare($upload_path, '/', -1, 1) !== 0) {
            $upload_path .= '/';
        }

        // 获取相对路径
        $url = substr($img['src']['value'], strpos($img['src']['value'], $upload_path) + strlen($upload_path));

        $post_id = attachment_url_to_postid($url);

        if (empty($post_id)) {
            $size = @getimagesize($img['src']['value']);

            if ($size == false) {
                return $matches[0];
            }

            $data['width'] = $size[0];
            $data['height'] = $size[1];
        } else {
            $data = get_post_meta($post_id, '_wp_attachment_metadata', true);
        }

        // 宽度
        $img['width'] = [
            'name' => 'width',
            'value' => $data['width'],
            'whole' => 'width="' . $data['width'] . '"',
            'vless' => 'n'
        ];

        // 高度
        $img['height'] = [
            'name' => 'height',
            'value' => $data['height'],
            'whole' => 'height="' . $data['height'] . '"',
            'vless' => 'n'
        ];

        $html = '<img width="' . $img['width']['value'] . '" height="' . $img['height']['value'] . '" class="aligncenter ' . $img['class']['value'] . '" src="' . $img['src']['value'] . '" alt="' . $img['src']['value'] . '">';
        
        return $html;
    }

    /**
     * 处理表格，为表格添加class样式：mdui-table
     */
    public function process_table($matches)
    {
        $old_attributes_str = $matches[2];
        $attrs = wp_kses_hair($old_attributes_str, wp_allowed_protocols());

        if (empty($attrs['class'])) {
            $attrs['class'] = [
                'name' => 'class',
                'value' => '',
                'whole' => 'class=""',
                'vless' => 'n',
            ];
        }

        $html = "<table";

        foreach ($attrs as $name => $attr) {
            if ($name == "class") {
                $attr['value'] .= (empty($attr['value']) ? '' : ' ') . 'mdui-table';
            }

            $html .= ' ' . $name . '="' . $attr['value'] . '"';
        }

        $html .= '>';

        return $html;
    }
}
