<?php

/**
 * Plugin Name: WP ACME MDX PLUS
 * Plugin URI: https://github.com/acme-top/wordpress-remove-row-actions
 * Description: MDX增强插件
 * Version: 0.1
 * Author: Acme
 * Author URI: http://www.acme.top
*/

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Markdown Extra Customize Attribute
 */
class Acme_Mdx_Plus
{
	/**
	 * 初始化
	 */
	public function init(){
		// 载入 css & js
		add_action('wp_enqueue_scripts', array( $this, 'acme_mdx_css' ) );
		add_action('wp_enqueue_scripts', array( $this, 'acme_mdx_js' ) );

		// 较低的优先级，在内容生成之后进行
		add_filter( 'the_content', array( $this, 'customize_attribute' ), 20 );

		if(is_admin()){
			add_filter( 'wp_insert_post_data', array( $this, 'acme_wp_insert_post_data'), 100, 2 );
		}
	}

	/**
	 * 载入css & js
	 */
	public function acme_mdx_css(){
		wp_register_style('acme_mdx_plus_css', plugin_dir_url(__FILE__) . 'css/style.css', array( 'mdx_mdui_css' ), '', 'all', true);
		wp_enqueue_style('acme_mdx_plus_css');
	}

	/**
	 * 载入css & js
	 */
	public function acme_mdx_js(){
		if(is_single() || is_page()){
			wp_register_script('acme_mdx_plus_doc_toc_js', plugin_dir_url(__FILE__) . 'js/jquery.doc.toc.js', array( 'mdx_jquery' ), '', true);
			wp_register_script('acme_mdx_plus_post_js', plugin_dir_url(__FILE__) . 'js/post.js', array( 'acme_mdx_plus_doc_toc_js' ), '', true);
			wp_enqueue_script('acme_mdx_plus_doc_toc_js');
			wp_enqueue_script('acme_mdx_plus_post_js');
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



	///////////////////////////////////////////////////////////////////////////
		
	/**
	 * 自定义文字段落属性
	 * @param       string      $content
	 * @return      string
	 */
	public function customize_attribute( $content ) {
		/* 段落加样式属性 */
		$content = preg_replace_callback( '#<p><!--(?=\.)(.*?)--></p>[\s\S]*?<p>(.*?)</p>#', array( $this, 'para_add_custom_class' ), $content );
		$content = preg_replace_callback( '#<p><!--begin(.*?)--></p>([\s\S]*?)<p><!--end\1--></p>#', array( $this, 'multi_para_add_custom_class' ), $content );
			
		return $content;
	}
		
	///////////////////////////////////////////////////////////////////////////


	/**
	 * 对段落添加自定义class
	 * @param       array      $matches
	 * @return      string
	 */
	public function para_add_custom_class( $matches ) {
		$class = str_replace('.', ' callout-', $matches[1]);
		if ( strpos($matches[2], '<em>') === 0 ) {
			$matches[2] = preg_replace('#^<em>(.*?)</em>[\:,\.\!：，。！ ]*([\w\W]*)#', "<em class=\"callout-title\">$1</em><p>$2</p>", $matches[2]);
		}

		return sprintf('<div class="callout%s">%s</div>', $class, $matches[2]);
	}
		
	///////////////////////////////////////////////////////////////////////////
		
	/**
	 * 对多段落添加自定义class
	 * @param       array      $matches
	 * @return      string
	 */
	public function multi_para_add_custom_class( $matches ) {
		$class = str_replace('.', ' callout-', $matches[1]);
		if ( strpos($matches[2], '<p><em>') !== false ) {
			$matches[2] = preg_replace('#<p><em>(.*?)</em>[\:,\.\!：，。！ ]*#', "<em class=\"callout-title\">$1</em><p>", $matches[2]);
		}

		return sprintf('<div class="callout%s">%s</div>', $class, $matches[2]);
	}

	///////////////////////////////////////////////////////////////////////////

	public function acme_wp_insert_post_data( $data, $postarr ){

		$data['post_content'] = preg_replace_callback('#<(img)([^>]+?)(>(.*?)</\\1>|[\/]?>)#si', array( $this, 'acme_process_image' ), stripslashes( $data['post_content'] ));

		return $data;
	}

	/**
	 * 处理图片
	 */
	public function acme_process_image( $matches ) {
		$old_attributes_str = $matches[2];
		$img = wp_kses_hair( $old_attributes_str, wp_allowed_protocols() );

		if ( empty( $img['src'] ) ){
			return $matches[0];
		}

		// 获取上传的路径
		$upload_path = get_option( 'upload_path', 'wp-content/uploads' );

		// 判断是否以斜杠结尾，否则补上
		if( substr_compare( $upload_path, '/', -1, 1 ) !== 0 ){
			$upload_path .= '/';
		}

		// 获取相对路径
		$url = substr( $img['src']['value'], strpos( $img['src']['value'], $upload_path ) + strlen( $upload_path ) );

		$post_id = attachment_url_to_postid($url);

		if(empty($post_id)){
			return $matches[0];
		}

		$data = get_post_meta( $post_id, '_wp_attachment_metadata', true );

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

}

// init Acme Mdx Plus
$acme_mdx_plus_obj = new Acme_Mdx_Plus();
$acme_mdx_plus_obj->init();


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
