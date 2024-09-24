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

use Tygh\Registry;
use Tygh\Tygh;
use Tygh\Enum\YesNo;



if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

$view = Tygh::$app['view'];
$cart = Tygh::$app['session']['cart'];
$auth = Tygh::$app['session']['auth'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'generate') {
        if(!empty($cart)){
            $product_data=$cart['products'];
            foreach($product_data as $product){
                $cart_data[]=$product;
            }
        }
        $export_data=fn_cp_generate_cart_from_file_get_export_data($cart_data);
        if(isset($_REQUEST['format'])){
            if($_REQUEST['format']=='pdf_cp'){
                $file=fn_cp_generate_cart_from_file_generate_pdf_file($export_data);
            }elseif($_REQUEST['format']=='csv_table'){
                $file=fn_cp_generate_cart_from_file_generate_csv_file($export_data);
            }
            fn_cp_generate_cart_from_file_export_file($file);
        }
        return [CONTROLLER_STATUS_OK, 'checkout.cart'];
    }
    elseif($mode='send_mail'){
        if (!empty($cart)) {
            $product_data = $cart['products'];
            foreach ($product_data as $product) {
                $cart_data[] = $product;
            }
            $export_data = fn_cp_generate_cart_from_file_get_export_data($cart_data);
            $data['email'] = $_REQUEST['email'];
            if(!(fn_cp_generate_cart_from_cart_validate_email($data['email']))){
                fn_set_notification("E",__('error'), __('cp_not_valid_email_address'));
                return [CONTROLLER_STATUS_OK, 'checkout.cart'];
            }
            $data['dir']=Registry::get('config.dir.var').Registry::get('config.storage.cp_generate_cart_from_file.prefix');
                if(isset($_REQUEST['format'])) {
                    if($_REQUEST['format']=='pdf_cp'){
                        $data['filename']=fn_cp_generate_cart_from_file_generate_pdf_file($export_data);
                    }elseif($_REQUEST['format']=='csv_table'){
                        $data['filename']=fn_cp_generate_cart_from_file_generate_csv_file($export_data);
                    }
                    fn_cp_generate_cart_from_file_send_mail($data);
                    fn_cp_generate_cart_from_file_delete_file($data['filename']);
            }
        }
    return [CONTROLLER_STATUS_OK, 'checkout.cart'];
    }
}
if ($mode == 'view') {
    if (defined('AJAX_REQUEST')) {
        if (empty($auth['user_id'])) {
            Tygh::$app['ajax']->assign('force_redirection', fn_url('auth.login_form'));
        } else {
            $cp_option= Registry::get('addons.cp_generate_cart_from_file');
            if($cp_option['PDF_export']==YesNo::YES and $cp_option['CSV_export']==YesNo::YES){
                $export_format='pdf_csv';
            }elseif($cp_option['PDF_export']==YesNo::NO and $cp_option['CSV_export']==YesNo::NO){
                fn_set_notification("W",__('warning'), __('cp_warning_format'));
            }else{
                $cp_option['PDF_export']==YesNo::YES?$export_format='pdf_cp':$export_format='csv_table';
            }
                Tygh::$app['view']->assign([
                    'export_format'=> $export_format,
                ]);
            $view->display('addons/cp_generate_cart_from_file/views/export_view.tpl');
        }
    }

    exit();
}