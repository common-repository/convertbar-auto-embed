<?php
/*
Plugin Name: ConvertBar Auto Embed WordPress plugin
Plugin URI: http://convertbar.com/plugin
Description: This plugin will automatically add the correct embed code to your site!
Version: 1.0.2
Author: ConvertBar
License: CC0
License URI: https://creativecommons.org/publicdomain/zero/1.0/
Tested up to: 4.7
Requires at least: 3.0.0
*/

function footer_script()
{
    include("embed-code.php");
}

function convbar_is_embed_code_set()
{
    return !!get_option("convbar_embed_id", false);
}

function convbar_activation_redirect($plugin)
{
    if ($plugin == plugin_basename(__FILE__)) {
        exit(wp_redirect(admin_url("admin.php?page=convertbar")));
    }
}

function convbar_check_embed_code($embedCode)
{
    $url = "https://app.convertbar.com/check-embed-code?embed-code=" . urlencode($embedCode);
    $remote = wp_remote_get($url);
    $result = json_decode($remote["body"], true);

    return array_key_exists("valid", $result) ? $result["valid"] : false;

}

function convbar_admin_notice()
{
    global $pagenow;
    if (!(($pagenow == 'admin.php' || $pagenow == 'tools.php') && (isset($_GET['page']) && $_GET['page'] == 'convertbar')) && !convbar_is_embed_code_set()) {
        ?>
        <div class="notice notice-error is-dismissible"><p><a
                        href="<?php admin_url("admin.php?page=convertbar") ?>">Please
                    add the ConvertBar embed code for your website</a></p></div>
        <?php
    }
}

//Thank you velcrow: http://stackoverflow.com/a/4694816/2167545
function convbar_is_valid_uuid4($uuid)
{
    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
}

function convbar_show_convertbar_page()
{
    $success = null;
    if (convbar_is_embed_code_set()) {
        $success = true;
    }
    if (array_key_exists("convertbar-code", $_POST)) {
        $embedCode = $_POST["convertbar-code"];
        if (convbar_is_valid_uuid4($embedCode) && convbar_check_embed_code($embedCode)) {
            update_option("convbar_embed_id", $embedCode);
            $success = true;
        } else {
            $success = false;
        }
    }
    $embedCode = get_option("convbar_embed_id", "");

    include("embed-page.php");
}

function convbar_add_admin_page()
{
    add_submenu_page(
        'tools.php',
        'ConvertBar',
        'ConvertBar',
        'manage_options',
        'convertbar',
        'convbar_show_convertbar_page'
    );
}

function convbar_load_admin_style()
{
    global $pagenow;

    if ((($pagenow == 'admin.php' || $pagenow == 'tools.php') && array_key_exists('page',
            $_GET) && $_GET['page'] == 'convertbar')
    ) {
        wp_enqueue_style('convertbar_font_awesome', plugin_dir_url(__FILE__) . '/css/font-awesome.css', false,
            '1.0.0');
        wp_enqueue_style('convertbar_css', plugin_dir_url(__FILE__) . '/css/styles.css', false, '1.0.0');
    }
}

add_action('admin_enqueue_scripts', 'convbar_load_admin_style');
add_action('admin_notices', 'convbar_admin_notice');
add_action('activated_plugin', 'convbar_activation_redirect');
add_action('admin_menu', 'convbar_add_admin_page');
add_action('wp_footer', 'footer_script'); ?>
