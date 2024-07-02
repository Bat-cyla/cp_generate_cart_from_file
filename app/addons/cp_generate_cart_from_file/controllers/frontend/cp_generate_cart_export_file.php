<?php

use Tygh\Tygh;


if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

$view = Tygh::$app['view'];
$cart = &Tygh::$app['session']['cart'];
$auth = &Tygh::$app['session']['auth'];
//$options=Tygh::$app['session']['options'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'generate') {
        if(!empty($cart)){
            $cart=$cart['products'];
            foreach($cart as $product){
                $cart_data[]=$product;
            }
        }
        if(isset($_REQUEST['format'])){
            if($_REQUEST['format']=='csv_table'){
                $options=[
                    'delimiter'=> ";",
                    'filename'=> "cart2.csv",
                    'price_dec_sign_delimiter'=>"."
                ];
                $export_fields=[
                    'product',
                    'product_code',
                    'product_options',
                    'price',
                    'amount',
                ];
                $export_data=[];
                foreach($cart_data as $key=>$product){
                    foreach($product as $field=>$value){
                        if(in_array($field,$export_fields)){
                            $export_data[$key][$field]=$value;
                        }
                        $export_data[$key]['product_options']=implode(',',$export_data[$key]['product_options']);
                        $export_data[$key]['total_price']=$export_data[$key]['price']*$export_data[$key]['amount'];
                    }
                }
                //fn_print_die($export_data);
                fn_cp_generate_cart_export_file($export_data, $options, '"');
            }
        }



    }
}
if ($mode == 'view') {
    if (defined('AJAX_REQUEST')) {
        if (empty($auth['user_id'])) {
            Tygh::$app['ajax']->assign('force_redirection', fn_url('auth.login_form'));
        } else {
            //fn_print_r($options);
            $view->display('addons/cp_generate_cart_from_file/views/export_view.tpl');
        }
    }

    exit();
}