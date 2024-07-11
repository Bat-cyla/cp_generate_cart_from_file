<?php

use Tygh\Registry;
use Tygh\Tygh;



if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

$view = Tygh::$app['view'];
$cart = &Tygh::$app['session']['cart'];
$auth = &Tygh::$app['session']['auth'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'generate') {

        if(!empty($cart)){
            $product_data=$cart['products'];
            foreach($product_data as $product){
                $cart_data[]=$product;
            }

        }
        if(isset($_REQUEST['format'])){
            if($_REQUEST['format']=='csv_table'){
                $options=[
                    'delimiter'=> ";",
                    'filename'=> "cart.csv",
                    'price_dec_sign_delimiter'=>"."
                ];
                $export_fields=[
                    'product',
                    'product_code',
                    'product_options',
                    'price',
                    'amount',
                ];
                if(fn_allowed_for('MULTIVENDOR')){
                    $export_fields[]= 'company_id';
                }

                $export_data=fn_cp_generate_cart_from_file_get_export_data($cart_data,$export_fields);
                fn_cp_generate_cart_from_file_export_file($export_data, $options);
            }
        }



    }elseif($mode='send_mail'){

        $data=[];

        if(!empty($cart)) {
            $product_data = $cart['products'];
            foreach ($product_data as $product) {
                $cart_data[] = $product;
            }
            fn_cp_generate_cart_from_file_send_mail($data);


            return [CONTROLLER_STATUS_OK, 'checkout.cart'];
        }
    }
}
if ($mode == 'view') {
    if (defined('AJAX_REQUEST')) {
        if (empty($auth['user_id'])) {
            Tygh::$app['ajax']->assign('force_redirection', fn_url('auth.login_form'));
        } else {
            $cp_pdf_option= Registry::get('addons.cp_generate_cart_from_file.PDF_export');
            $cp_csv_option= Registry::get('addons.cp_generate_cart_from_file.CSV_export');
            if($cp_pdf_option=='Y' and $cp_csv_option=='Y'){
                $export_format='pdf_csv';
            }else{
                $cp_csv_option=='Y'?$export_format='csv_table':$export_format='pdf_cp';
            }
                Tygh::$app['view']->assign([
                    'export_format'=> $export_format,
                ]);
            $view->display('addons/cp_generate_cart_from_file/views/export_view.tpl');
        }
    }

    exit();
}