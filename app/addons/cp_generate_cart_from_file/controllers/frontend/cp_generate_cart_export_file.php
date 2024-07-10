<?php

use Tygh\Registry;
use Tygh\Tygh;
use Tygh\Addons\GenerateCart\Notifications\EventIdProviders\CartProvider;


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
                            if(is_array($export_data[$key]['product_options'])){
                                $export_data[$key]['product_options']=implode(',',$export_data[$key]['product_options']);
                            }
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
                $export_data=array_map('fn_cp_generate_cart_from_file_array_map',$export_data);
                fn_cp_generate_cart_from_file_export_file($export_data, $options);
            }
        }



    }elseif($mode='send_mail'){

        $params=[
            'gift_cert_id' => 1,
            'gift_cert_data' =>
        [
            'gift_cert_code' => 'GC-EFTB-VA7U-J035',
            'status' => 'A',
            'company_id' => 0,
            'recipient' => 'qweq',
            'sender' => 'qweqwe',
            'message' => '',
            'amount' => 50,
            'send_via' => 'E',
            'email' => 'cp.nemov@gmail.com',
            'template' => 'default.tpl',
        ],

        'certificate_status' => 'A',
        'security_hash' => 'd83c8e9df6ef9587c66a45287956b413',
        'dispatch' => 'gift_certificates.update'
        ];
        $force_notification = fn_get_notification_rules($params);
        /** @var \Tygh\Notifications\EventDispatcher $event_dispatcher */
        $event_dispatcher = Tygh::$app['event.dispatcher'];

        /** @var \Tygh\Notifications\Settings\Factory $notification_settings_factory */
        $notification_settings_factory = Tygh::$app['event.notification_settings.factory'];
        $notification_rules = $notification_settings_factory->create($force_notification);

        $event_dispatcher->dispatch(
            "gift_certificates.gift_certificate.updated",
            ['certificate_data' => $data],
            $notification_rules,
            new CartProvider($data)
        );


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