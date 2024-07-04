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

                if(isset($cart_data['company_id'])){
                    $export_fields[]= 'company_id';
                }
                $export_data=[];
                foreach($cart_data as $key=>$product){
                    foreach($product as $field=>$value){
                        if(in_array($field,$export_fields)){
                            $export_data[$key][$field]=$value;
                        }
                        if(!empty($export_data[$key]['product_options'])){
                            $export_data[$key]['product_options']=implode(',',$export_data[$key]['product_options']);
                        }else{
                            $export_data[$key]['product_options']='';
                        }
                        if(!empty($export_data[$key]['price']) and !empty($export_data[$key]['amount'])) {
                            $export_data[$key]['total_price'] = $export_data[$key]['price'] * $export_data[$key]['amount'];
                        }else{
                            $export_data[$key]['total_price']=0;
                        }

                    }
                }

                fn_cp_generate_cart_from_file_export_file($export_data, $options);


            }
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