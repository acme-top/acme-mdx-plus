<?php

/**
 * Plugin Name: WP ACME MDX PLUS
 * Plugin URI: https://github.com/acme-top/wp-acme-mdx-plus
 * Description: MDX增强插件，建议仅在使用的主题为MDX时启用，否则可能会发生不可预知的结果
 * Version: 0.1
 * Author: Acme
 * Author URI: http://www.acme.top
 * Text Domain: acme-mdx-plus
 * Domain Path: /languages
 */

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

namespace AcmeMdxPlusRoot;

use acme\mdx\plus\Main;

define('WP_ACME_MDX_PLUS_VER', '9.9.9'); //版本说明
define('WP_ACME_MDX_PLUS_URL', plugins_url('/', __FILE__)); //插件资源路径
define('WP_ACME_MDX_PLUS_PATH', dirname(__FILE__)); //插件路径文件夹
define('WP_ACME_MDX_PLUS_NAME', plugin_basename(__FILE__)); //插件名称

// 自动载入文件
require_once WP_ACME_MDX_PLUS_PATH . '/vendor/autoload.php';

/**
 * 执行插件函数
 */
function acme_mdx_plus_init()
{
    if (version_compare(PHP_VERSION, '5.3.29') < 0) {
        add_filter('template_include', '__return_null', 99);
        unset($_GET['activated']);
        add_action('admin_notices', function () {
            $message = __('Hey, we\'ve noticed that you\'re running an outdated version of PHP which is no longer supported. Make sure your site is fast and secure, by upgrading PHP to the latest version.', 'Acme Mdx Plus');
            printf('<div class="error"><p>%1$s</p></div>', esc_html($message));
        });
    } else {
        new Main();
    }
}

/**
 * 开始执行插件
 */
acme_mdx_plus_init();