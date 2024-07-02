<?php
/*****************************************************************************
 *                                                        © 2013 Cart-Power   *
 *           __   ______           __        ____                             *
 *          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
 *      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
 *     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
 *    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
 *                                                                            *
 *                                                                            *
 * -------------------------------------------------------------------------- *
 * This is commercial software, only users who have purchased a valid license *
 * and  accept to the terms of the License Agreement can install and use this *
 * program.                                                                   *
 * -------------------------------------------------------------------------- *
 * website: https://store.cart-power.com                                      *
 * email:   sales@cart-power.com                                              *
 ******************************************************************************/

use Tygh\CpFileReader\CpXlsColumnException;
use Tygh\Tygh;
use Tygh\Storage;
use Tygh\CpFileReader\CpFileReader;
use Tygh\CpFileUploader\CpFileUploader;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

$view = Tygh::$app['view'];
$cart = &Tygh::$app['session']['cart'];
$auth = &Tygh::$app['session']['auth'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'generate') {
        if (isset($_FILES, $_FILES['cp_generate_attach'])) {
            $params = $_REQUEST;
            $file = $_FILES['cp_generate_attach'];
            $array_fields = $params['array_fields'];

            $uploader = new CpFileUploader(
                reset($file['name']),
                reset($file['tmp_name']),
                reset($file['error'])
            );

            list($file_path, $ext, $name) = $uploader->uploadOrder();

            if (!empty($file_path) && $ext) {
                $reader = new CpFileReader($file_path, $ext);
                try {
                    $res = $reader->read($array_fields);
                } catch (CpXlsColumnException $e) {
                    fn_set_notification('E', __('error'), $e->getMessage());
                    exit;
                } finally {
                    Storage::instance('cp_generate_cart_from_file')->delete($name);
                }
            } else {
                Tygh::$app['ajax']->assign('non_ajax_notifications', true);
                fn_set_notification('E', __('error'), __("cp_error_upload_file"));
                Tygh::$app['ajax']->assign('force_redirection', fn_url());

                exit();
            }

            if (!empty($res)) {
                list($variations, $cart_data, $undefined_products) = fn_cp_generate_cart_from_file_check_products($res);
            } else {
                Tygh::$app['ajax']->assign('non_ajax_notifications', true);
                fn_set_notification('E', __('error'), __("cp_bad_table"));
                Tygh::$app['ajax']->assign('force_redirection', fn_url());

                exit();
            }

            if (!empty($cart_data)) {
                fn_add_product_to_cart($cart_data, $cart, $auth, true);
                fn_calculate_cart_content($cart, $auth, 'E', true, 'F', true);
                fn_save_cart_content($cart, $auth['user_id']);
                $cart_data = [];
            }

            if (!empty($variations) || !empty($undefined_products)) {
                $view->assign([
                    'variations' => $variations,
                    'undefined_products' => $undefined_products,
                ]);
                $view->display('addons/cp_generate_cart_from_file/views/variations.tpl');

                exit();
            } else {
                Tygh::$app['ajax']->assign('non_ajax_notifications', true);
                fn_set_notification('N', __('notice'), __("cp_import_success"));
                Tygh::$app['ajax']->assign('force_redirection', fn_url('checkout.cart'));

                exit();
            }
        }
    }

    if ($mode == 'finish') {
        $params = $_REQUEST;
        if (!empty($params['chosen_products'])) {
            $chosen_products = $params['chosen_products'];

            foreach ($chosen_products as $product_id => $product_amount) {
                $cart_data[$product_id]['amount'] = !empty($product_amount) ? $product_amount : 0;
            }
            fn_add_product_to_cart($cart_data, $cart, $auth, true);
        }
        fn_calculate_cart_content($cart, $auth, 'E', true, 'F', true);
        fn_save_cart_content($cart, $auth['user_id']);
        $cart_data = [];

        Tygh::$app['ajax']->assign('non_ajax_notifications', true);
        fn_set_notification('N', __('notice'), __("cp_import_success"));
        Tygh::$app['ajax']->assign('force_redirection', fn_url('checkout.cart'));

        exit();
    }
}

if ($mode == 'view') {
    if (defined('AJAX_REQUEST')) {
        if (empty($auth['user_id'])) {
            Tygh::$app['ajax']->assign('force_redirection', fn_url('auth.login_form'));
        } else {
            $template_data = fn_cp_generate_cart_from_file_get_default_template_data();
            $view->assign([
                'template_data' => $template_data,
            ]);
            $view->display('addons/cp_generate_cart_from_file/views/view.tpl');


        }
    }

    exit();
}