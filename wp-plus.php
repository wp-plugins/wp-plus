<?php
/*
Plugin Name: WP-Plus
Plugin URI: http://blog.lwl12.com/read/wp-plus.html
Description: 优化和增强您的博客
Author: liwanglin12
Author URI: http://lwl12.com
Version: 1.61
*/
/*Exit if accessed directly:安全第一,如果是直接载入,就退出.*/
defined('ABSPATH') or exit;
define("plus_version", "1.61");
/* 插件初始化*/
register_activation_hook(__FILE__, 'plus_plugin_activate');
register_deactivation_hook(__FILE__, 'plus_plugin_deactivate');
add_action('plus_hook_update', 'plus_updateinfo');
add_action('admin_init', 'plus_plugin_redirect');
function plus_plugin_activate()
{
    add_option('do_activation_redirect', true);
    for ($i = 0; $i <= 10; $i++) {
        if (plus_post("activate") == "success") {
            break;
        }
    }
}

function plus_plugin_redirect()
{
    if (!wp_next_scheduled('plus_hook_update'))
        wp_schedule_event(time() + 60, 'hourly', 'plus_hook_update');
    if (get_option('do_activation_redirect', false)) {
        delete_option('do_activation_redirect');
        wp_redirect(admin_url('options-general.php?page=wp_plus'));
    }
}

function plus_plugin_deactivate()
{
    for ($i = 0; $i <= 10; $i++) {
        if (plus_post("deactivate") == "success") {
            break;
        }
    }
}
/**
 * [register_plugin_settings_link 添加插件控制面板链接]
 */
function plus_register_plugin_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=wp_plus">设置</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_{$plugin}", 'plus_register_plugin_settings_link');


/* 注册控制面板*/
if (is_admin()) {
    add_action('admin_menu', 'wp_plus_menu');
}

/**
 * [wp_plus_menu 注册控制面板]
 */
function wp_plus_menu()
{
    add_options_page('WP Plus 插件控制面板', 'WP Plus', 'administrator', 'wp_plus', 'plus_pluginoptions_page');
    
}

/* 插件设置核心部分*/
function plus_pluginoptions_page()
{
    require "option.php";
}
/*加载进度*/
if (get_option('wp_plus_jdt') == 'checked') {
?>
<?php
    if (!defined('ABSPATH'))
        exit;
    define('WP_NPROGRESS_PLUGIN_URL', plugin_dir_url(__FILE__));
    function plus_wpn_enqueue()
    {
        wp_enqueue_style('nprogresss', WP_NPROGRESS_PLUGIN_URL . 'jdt/nprogress.css');
        
        wp_enqueue_script('nprogress', WP_NPROGRESS_PLUGIN_URL . 'jdt/nprogress.js', array(
            'jquery'
        ), '0.1.2', true);
        wp_enqueue_script('wp-nprogress', WP_NPROGRESS_PLUGIN_URL . 'jdt/global.js', array(
            'jquery',
            'nprogress'
        ), '0.0.1', true);
    }
    add_action('wp_enqueue_scripts', 'plus_wpn_enqueue');
?>
<?php
}
?>
<?php
if (get_option('wp_plus_glgjt') == 'checked') {
?>
<?php
    show_admin_bar(false);
?>
<?php
}
?>
<?php
/*替换头像*/
if (get_option('wp_plus_gravatar') == 'checked') {
?>
<?php
    function plus_ty_avatar($avatar)
    {
        $avatar = str_replace(array(
            "www.gravatar.com",
            "0.gravatar.com",
            "1.gravatar.com",
            "2.gravatar.com",
            "secure.gravatar.com"
        ), "ssl-gravatar.eqoe.cn", $avatar);
        return $avatar;
    }
    add_filter('get_avatar', 'plus_ty_avatar', 10, 3);
?>
<?php
}
?>
<?php
/*微软雅黑*/
if (get_option('wp_plus_wryh') == 'checked') {
?>
<?php
    function plus_admin_fonts()
    {
        echo '<style type="text/css">
        * { font-family: "Microsoft YaHei" !important; }
        i, .ab-icon, .mce-close, i.mce-i-aligncenter, i.mce-i-alignjustify, i.mce-i-alignleft, i.mce-i-alignright, i.mce-i-blockquote, i.mce-i-bold, i.mce-i-bullist, i.mce-i-charmap, i.mce-i-forecolor, i.mce-i-fullscreen, i.mce-i-help, i.mce-i-hr, i.mce-i-indent, i.mce-i-italic, i.mce-i-link, i.mce-i-ltr, i.mce-i-numlist, i.mce-i-outdent, i.mce-i-pastetext, i.mce-i-pasteword, i.mce-i-redo, i.mce-i-removeformat, i.mce-i-spellchecker, i.mce-i-strikethrough, i.mce-i-underline, i.mce-i-undo, i.mce-i-unlink, i.mce-i-wp-media-library, i.mce-i-wp_adv, i.mce-i-wp_fullscreen, i.mce-i-wp_help, i.mce-i-wp_more, i.mce-i-wp_page, .qt-fullscreen, .star-rating .star { font-family: dashicons !important; }
        .mce-ico { font-family: tinymce, Arial !important; }
        .fa { font-family: FontAwesome !important; }
        .genericon { font-family: "Genericons" !important; }
        .appearance_page_scte-theme-editor #wpbody *, .ace_editor * { font-family: Monaco, Menlo, "Ubuntu Mono", Consolas, source-code-pro, monospace !important; }
        .post-type-post #advanced-sortables, .post-type-post #autopaging .description { display: none !important; }
        .form-field input, .form-field textarea { width: inherit; border-width: 0; }
        </style>';
    }
    add_action('admin_head', 'plus_admin_fonts');
?>
<?php
}
?>
<?php
/*积分特效*/
if (get_option('wp_plus_number') == 'checked') {
?>
<?php
    function wp_plus_jifentx()
    {
        echo '<script> jQuery(document).ready(function($) { $("html,body").click(function(e){ var n=Math.round(Math.random()*100); var $i=$("<b/>").text("+"+n); var x=e.pageX,y=e.pageY; $i.css({ "z-index":99999, "top":y-20, "left":x, "position":"absolute", "color":"#E94F06" }); $("body").append($i); $i.animate( {"top":y-180,"opacity":0}, 1500, function(){$i.remove();}); e.stopPropagation();});}); </script>';
    }
    add_action('wp_footer', 'wp_plus_jifentx');
}
?>
<?php
/*中文用户*/
if (get_option('wp_plus_chuser') == 'checked') {
?>
<?php
    add_filter('sanitize_user', 'plus_chinese_user', 3, 3);
    function plus_chinese_user($username, $raw_username, $strict)
    {
        $username = $raw_username;
        $username = strip_tags($username);
        $username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
        $username = preg_replace('/&.+?;/', '', $username);
        if ($strict)
            $username = preg_replace('|[^a-z0-9 _.\-@\x80-\xFF]|i', '', $username);
        $username = preg_replace('|\s+|', ' ', $username);
        return $username;
    }
}
?>
<?php
if (get_option('wp_plus_ping') == 'checked') {
?>
<?php
    /* 禁止站内文章PingBack */
    function plus_no_self_ping(&$links)
    {
        $home = get_option('home');
        foreach ($links as $l => $link)
            if (0 === strpos($link, $home))
                unset($links[$l]);
    }
    add_action('pre_ping', 'plus_no_self_ping');
?>
<?php
}
?>
<?php
if (get_option('wp_plus_nofollow') == 'checked') {
?>
<?php
    /* 自动为博客内的连接添加nofollow属性并在新窗口打开链接 */
    add_filter('the_content', 'plus_cn_nf_url_parse');
    
    function plus_cn_nf_url_parse($content)
    {
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
        if (preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
            if (!empty($matches)) {
                $srcUrl = get_option('siteurl');
                for ($i = 0; $i < count($matches); $i++) {
                    $tag      = $matches[$i][0];
                    $tag2     = $matches[$i][0];
                    $url      = $matches[$i][0];
                    $noFollow = '';
                    $pattern  = '/target\s*=\s*"\s*_blank\s*"/';
                    preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                    if (count($match) < 1)
                        $noFollow .= ' target="_blank" ';
                    $pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
                    preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
                    if (count($match) < 1)
                        $noFollow .= ' rel="nofollow" ';
                    $pos = strpos($url, $srcUrl);
                    if ($pos === false) {
                        $tag = rtrim($tag, '>');
                        $tag .= $noFollow . '>';
                        $content = str_replace($tag2, $tag, $content);
                    }
                }
            }
        }
        $content = str_replace(']]>', ']]>', $content);
        return $content;
    }
?>
<?php
}
?>
<?php
if (get_option('wp_plus_bingbg') == 'checked') {
?>
<?php
    // 调用Bing美图作为登陆界面背景 //
    function plus_bingbg()
    {
        $str = file_get_contents('http://cn.bing.com/HPImageArchive.aspx?idx=0&n=1');
        if (preg_match("/<url>(.+?)<\/url>/ies", $str, $matches)) {
            $imgurl = 'http://cn.bing.com' . $matches[1];
            echo '<style type="text/css">body{background: url(' . $imgurl . ');width:100%;height:100%;background-image:url(' . $imgurl . ');-moz-background-size: 100% 100%;-o-background-size: 100% 100%;-webkit-background-size: 100% 100%;background-size: 100% 100%;-moz-border-image: url(' . $imgurl . ') 0;background-repeat:no-repeat\9;background-image:none\9;}</style>';
        }
    }
    add_action('login_head', 'plus_bingbg');
?>
<?php
}
?>
<?php
if (get_option('wp_plus_simplifyhead') == 'checked') {
?>
<?php
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    remove_action('wp_head', 'wp_generator');
?>
<?php
}
?>
<?php
if (get_option('wp_plus_replaceurl') == 'checked') {
?>
<?php
    add_action('template_redirect', 'plus_relative_urls');
    
    function plus_relative_urls()
    {
        if (is_feed() || get_query_var('sitemap'))
            return;
        $filters = array(
            'post_link',
            'post_type_link',
            'page_link',
            'attachment_link',
            'get_shortlink',
            'post_type_archive_link',
            'get_pagenum_link',
            'get_comments_pagenum_link',
            'term_link',
            'search_link',
            'day_link',
            'month_link',
            'year_link'
        );
        foreach ($filters as $filter) {
            add_filter($filter, 'wp_make_link_relative');
        }
    }
?>
<?php
}
?>
<?php
if (get_option('wp_plus_welcomemsg') == 'checked') {
?>
<?php
    require "welcomemsg.php";
    function plus_welcomemsg_css()
    {
        echo '<style type="text/css">#hellobaby { width: 200px; background:#000000; border:1px solid #B3B3B3; color:#FFFFFF; font-size:14px; opacity:0.7; filter:alpha(opacity=70); padding: 10px 10px 10px 10px; position:fixed; right:0; top:150px; z-index:999; } .closebox{float:left;text-align:center;font-size:26px;margin-top:0px;padding: 0 10px 0 0;} .closebox a{border-bottom: none;}</style>';
    }
    function plus_welcomemsg()
    {
        $msg = welcome_msg();
        if ($msg !== false) {
            echo "<script type=\"text/javascript\"> (function(){ var wait = 30; var interval = setInterval(function(){ var time = --wait; if(time <= 0) { $('#hellobaby').animate({right:'-20000px'}).hide(); clearInterval(interval); }; }, 1000); })(); </script>";
            echo '<div id="hellobaby"> <div class="closebox"><a href="javascript:void(0)" onclick="$(\'#hellobaby\').animate({right:\'-20000px\'});" title="关闭">x</a></div>';
            echo $msg;
            echo '</div>';
        }
    }
    add_action('wp_head', 'plus_welcomemsg_css');
    add_action('wp_footer', 'plus_welcomemsg');
?>
<?php
}
?>
<?php
if (get_option('wp_plus_ietip') == 'checked') {
?>
<?php
    function plus_ietip()
    {
        echo '<!--[if lt IE 10]><script src="http://wuyongzhiyong.b0.upaiyun.com/iedie/v1.1/script.min.js"></script><![endif]-->';
    }
    add_action('wp_head', 'plus_ietip');
?>
<?php
}
?>
<?php
if (get_option('wp_plus_linkman') == 'checked') {
?>
<?php
    add_filter('pre_option_link_manager_enabled', '__return_true');
?>
<?php
}
?>
<?php
/*Google公共库加速*/
if (get_option('wp_plus_google') == 'checked') {
?>
<?php
    function wp_plus_google($buffer)
    {
        $buffer = str_replace("fonts.googleapis.com", "fonts.geekzu.org", $buffer);
        $buffer = str_replace("ajax.googleapis.com", "sdn.geekzu.org/ajax", $buffer);
        return $buffer;
    }
    function wp_plus_google_start()
    {
        ob_start("wp_plus_google");
    }
    function wp_plus_google_end()
    {
        ob_end_flush();
    }
    add_action('init', 'wp_plus_google_start');
    add_action('shutdown', 'wp_plus_google_end');
?>
<?php
}
?>
<?php
/**
 * [plus_post 用于向LWL插件统计API发送数据]
 * @param  [text] $action [动作]
 * @return [type]         [成功返回返回内容，失败返回false]
 * 请勿修改或删除此段代码，这非常重要！
 */
function plus_post($action)
{
    if (get_option('wp_plus_uuid', "") == "") {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid  = substr($chars, 0, 8) . '-';
        $uuid .= substr($chars, 8, 4) . '-';
        $uuid .= substr($chars, 12, 4) . '-';
        $uuid .= substr($chars, 16, 4) . '-';
        $uuid .= substr($chars, 20, 12);
        add_option('wp_plus_uuid', "wp_plus_" . $uuid);
    }
    $args = array(
        'uuid' => get_option('wp_plus_uuid'),
        'url' => get_bloginfo('url'),
        'name' => get_bloginfo('name'),
        'version' => plus_version,
        'action' => $action
    );
    
    $response = wp_remote_post('http://api.lwl12.com/wordpress/plugin/wpplus/post.php', array(
        'body' => $args
    ));
    
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200)
        return (false);
    else
        return (wp_remote_retrieve_body($response));
}
function plus_updateinfo()
{
    return (plus_post("update"));
}
?>