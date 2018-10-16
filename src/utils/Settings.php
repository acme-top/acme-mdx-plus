<?php
namespace acme\mdx\plus\utils;

use acme\mdx\plus\utils\Plugin;
use acme\mdx\plus\utils\Debugger;
use \SettingsApi\SettingsApi as SettingsGo;

class Settings extends Plugin
{
    /**
     * @var SettingsGo
     */
    private $settings_api;
    
    public function __construct()
    {
		parent::__construct();

        $this->settings_api = new SettingsGo();

        add_action('admin_init', array( $this, 'admin_init' ));
        add_action('admin_menu', array( $this, 'admin_menu' ));
    }

    function admin_init() {

		//检查编辑器静态资源，如果是默认配置选项提前条件下，不符合最新版资源强制升级
		$option = get_option('editor_style');
		$addres = $option['editor_addres'];
		$SSL = is_ssl() ? 'https:' : 'http:';

		//判断本地选项是否jsdelivr地址，如果是则判断是否最新地址
		$addresResult = preg_match('/cdn\.jsdelivr\.net/i',$addres);
		if ( $addresResult && $addres !== $SSL . '//cdn.jsdelivr.net/wp/wp-editormd/tags/' . WP_EDITORMD_VER  ) {
			$option['editor_addres'] = $SSL . '//cdn.jsdelivr.net/wp/wp-editormd/tags/' . WP_EDITORMD_VER;
			update_option('editor_style',$option);
		}
		//如果空值填入最新CDN地址
		if ( $this->get_option('editor_addres', 'editor_style') === '' ) {
			$option['editor_addres'] = $SSL . '//cdn.jsdelivr.net/wp/wp-editormd/tags/' . WP_EDITORMD_VER;
			update_option('editor_style',$option);
        }

		//set the settings
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );

		//initialize settings
		$this->settings_api->admin_init();
	}

    public function admin_menu()
    {
        add_options_page($this->plugin_name . __(' Options', $this->text_domain), $this->plugin_name, 'manage_options', __FILE__, array( $this, 'plugin_page' ));
    }

    public function plugin_page()
    {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo Debugger::debug($this->text_domain);

        if ($this->get_option('hide_ads', 'editor_advanced') == 'off') {
            //判断地区，根据不同的地区进入不同的文档
            switch (get_bloginfo('language')) {
                case 'zh-CN':
                    $donateImgUrl = '//gitee.com/JaxsonWang/JaxsonWang/raw/master/mydonate';
                    break;
                default:
                    $donateImgUrl = '//github.com/JaxsonWang/WP-Editor.md/raw/docs/screenshots';
            }
            echo '<div id="donate">';
            echo '<h3>' . __('Donate', $this->text_domain) . '</h3>';
            echo '<p style="width: 50%">' . __('It is hard to continue development and support for this plugin without contributions from users like you. If you enjoy using WP-Editor.md and find it useful, please consider making a donation. Your donation will help encourage and support the plugin’s continued development and better user support.Thank You!', $this->text_domain) . '</p>';
            echo '<p style="display: table;"><strong style="display: table-cell;vertical-align: middle;">Alipay(支付宝)：</strong><a rel="nofollow" target="_blank" href="'. $donateImgUrl .'/alipay.jpg"><img width="100" src="'. $donateImgUrl .'/alipay.jpg"/></a></p>';
            echo '<p style="display: table;"><strong style="display: table-cell;vertical-align: middle;">WeChat(微信)：</strong><a rel="nofollow" target="_blank" href="'. $donateImgUrl .'/wechart.jpg"><img width="100" src="'. $donateImgUrl .'/wechart.jpg"/></a></p>';
            echo '<p style="display: table;"><strong style="display: table-cell;vertical-align: middle;">PayPal(贝宝)：</strong><a rel="nofollow" target="_blank" href="https://www.paypal.me/JaxsonWangChina">https://www.paypal.me/JaxsonWangChina</a></p>';
            echo '</div>';
            echo '</div>';
        }

        $this->script_style();
    }

    public function get_settings_sections()
    {
        $sections = array(
            array(
                'id'    => 'mdx_plus_instantclick',
                'title' => __('Basic Settings', $this->text_domain)
            ),
            array(
                'id'    => 'mdx_plus_syntax_highlighting',
                'title' => __('Syntax Highlighting Settings', $this->text_domain)
            ),
            array(
                'id'    => 'mdx_plus_emoji',
                'title' => __('Emoji Settings', $this->text_domain)
            ),
            array(
                'id'    => 'mdx_plus_toc',
                'title' => __('TOC Settings', $this->text_domain)
            ),
        );

        return $sections;
    }

    /**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	function get_settings_fields() {
		$settings_fields = array(
			'mdx_plus_instantclick'       => array(
				array (
					'name'    => 'support_instantclick',
					'label'   => __( 'Support InstantClick', $this->text_domain ),
					'desc'    => __( 'InstantClick', $this->text_domain ),
					'type'    => 'checkbox',
					'default' => 'off'
				)
			),
			'mdx_plus_syntax_highlighting' => array(
				array(
					'name'    => 'highlight_mode_auto',
					'label'   => __( 'Auto load mode', $this->text_domain ),
					'desc'    => __( '', $this->text_domain ),
					'type'    => 'checkbox',
					'default' => 'off'
				),
				array(
					'name'    => 'line_numbers',
					'label'   => __( 'Line Numbers', $this->text_domain ),
					'desc'    => __( '', $this->text_domain ),
					'type'    => 'checkbox',
					'default' => 'off'
				),
				array(
					'name'    => 'show_language',
					'label'   => __( 'Show Language', $this->text_domain ),
					'desc'    => __( '', $this->text_domain ),
					'type'    => 'checkbox',
					'default' => 'off'
				),
				array(
					'name'    => 'copy_clipboard',
					'label'   => __( 'Copy to Clipboard', $this->text_domain ),
					'desc'    => __( '', $this->text_domain ),
					'type'    => 'checkbox',
					'default' => 'off'
				),
				array(
					'name'    => 'highlight_library_style',
					'label'   => __( 'PrismJS Syntax Highlight Style', $this->text_domain ),
					'desc'    => __( 'Syntax highlight theme style', $this->text_domain ),
					'type'    => 'select',
					'options' => array(
						'default'        => 'Default',
						'dark'           => 'Dark',
						'funky'          => 'Funky',
						'okaidia'        => 'Okaidia',
						'twilight'       => 'Twilight',
						'coy'            => 'Coy',
						'solarizedlight' => 'Solarized Light',
						'tomorrow'       => 'Tomorrow Night',
						'customize'       => __( 'Customize Style', $this->text_domain ),
					),
					'default' => 'default'
				),
				array(
					'name'    => 'customize_my_style',
					'label'   => __( 'Customize Style Library', $this->text_domain ),
					'desc'    => __( 'Get More <a href="https://github.com/JaxsonWang/Prism.js-Style" target="_blank" rel="nofollow">Theme Style</a>', $this->text_domain ),
					'type'    => 'text',
					'default' => 'nothing'
				),
				array(
					'name'    => 'assets_addres',
					'label'   => __( 'PrismJS Static Resource Addres', $this->text_domain ),
					'desc'    => __( 'Please make sure the resources are up to date.<br/>' , $this->text_domain ) . __('Please upload the resource (the unzipped folder name is "assets") to your server or cdn. If your resource address is: "http(s)://example.com/myfile/assets", you should fill in: "http(s)://example.com/myfile ". <br/>',$this->text_domain),
					'type'    => 'text',
					'default' => ''
				),
			),
			'mdx_plus_emoji'        => array(
				array(
					'name'    => 'support_emoji',
					'label'   => __( 'Support Emoji', $this->text_domain ),
					'desc'    => __( '', $this->text_domain ),
					'type'    => 'checkbox',
					'default' => 'off'
				)
			),
			'mdx_plus_toc'          => array(
				array(
					'name'    => 'support_toc',
					'label'   => __( 'Support ToC', $this->text_domain ),
					'desc'    => __( 'Table of Contents', $this->text_domain ),
					'type'    => 'checkbox',
					'default' => 'off'
				)
			)
		);

		return $settings_fields;
    }
    
    /**
	 * Get all the pages
	 *
	 * @return array page names with key value pairs
	 */
	function get_pages() {
		$pages         = get_pages();
		$pages_options = array();
		if ( $pages ) {
			foreach ( $pages as $page ) {
				$pages_options[ $page->ID ] = $page->post_title;
			}
		}

		return $pages_options;
    }
    
    private function script_style() {
		?>
        <style type="text/css" rel="stylesheet">
            /*设置选项样式*/
            .debugger-wrap {
                margin-top: 10px;
                display: none;
            }

            .debugger-wrap tbody tr {
                width: 100%;
                text-align: left;
            }

            .debugger-wrap tbody tr th {
                padding: 5px 10px 5px 0;
            }

            .debugger-wrap tbody tr th:nth-child(2) {
                color: #006686;
                width: 75%;
            }

            .CodeMirror {
                width: 600px;
            }

            div.CodeMirror-linenumber.CodeMirror-gutter-elt {
                left: -10px!important;
                width: 20px!important;
            }

            pre.CodeMirror-line {
                left: 20px;
            }

            span.error {
                color: #dc3232;
                font-weight: 600;
                margin-right: 10px;
            }

            span.updated {
                color: #46b450;
                font-weight: 600;
            }

        </style>
        <script type="text/javascript">
            (function ($) {
                //插入信息
                $('#jquery').text(jQuery.fn.jquery);
                //切换显示信息
                $('#debugger').click(function () {
                    $('.debugger-wrap').fadeToggle();
                    $('#donate').fadeToggle();
                });
                //判断非调试界面则隐藏
                $('a[href!="#editor_advanced"].nav-tab').click(function () {
                    $('.debugger-wrap').fadeOut();
                    $('#donate').fadeIn();
                });

            })(jQuery);
        </script>
		<?php
	}
}
