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

use Tygh\Notifications\EventDispatcher;
use Tygh\Storage;
use Tygh\Registry;
use Tygh\Enum\YesNo;
use Tygh\Addons\GenerateCart\Notifications\EventIdProviders\CartProvider;
use Tygh\Mailer\Message;
use Tygh\Addons\PdfDocuments\Pdf;




if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_cp_generate_cart_from_file_check_products(array $products = []) : array
{
    $variations = [];
    $cart_data = [];
    $undefined_products = [];
    $disabled_products=[];

    if (!empty($products)) {
        $cp_params = [];
        //find by artiqles
        foreach ($products as $product) {
            if (!empty($product['artiqle'])) {
                $cp_params['artiqles'][] = $product['artiqle'];
            }
        }

        if (!empty($cp_params['artiqles'])) {
            $product_ids = fn_cp_generate_cart_from_file_get_product_id_by_artiqle($cp_params['artiqles']);

        }

        if (!empty($product_ids)) {

            foreach ($product_ids as $artiqle => $p_ids) {
                if (!empty($p_ids) && count($p_ids) > 1) {
                    $cp_params['pid'] = $p_ids;
                    list($cp_products) = fn_get_products($cp_params);
                    $variations[$artiqle]['products'] = $cp_products;

                    foreach ($products as $product) {
                        if ($product['artiqle'] == $artiqle) {
                            $variations[$artiqle]['amount'] = $product['amount'];
                            $variations[$artiqle]['reference_product'] = $product;
                        }
                    }
                } elseif (!empty($p_ids) && count($p_ids) == 1) {
                    foreach ($products as $product) {
                        if ($product['artiqle'] == $artiqle) {
                            $cart_data[$p_ids[0]]['amount'] = $product['amount'];

                        }
                    }
                } elseif (empty($p_ids)) {

                    foreach ($products as $product) {

                        if ($product['artiqle'] == $artiqle && fn_cp_generate_cart_from_file_check_product_status($product['artiqle'])) {
                            $undefined_products[] = $product;
                        } else {
                            $disabled_products[] = $product;
                        }
                    }
                }

            }
            //find by name
            if (!empty($undefined_products)) {
                foreach ($undefined_products as $undefined_product) {
                    if (!empty($undefined_product['name'])) {
                        $cp_params['names'][] = $undefined_product['name'];
                    }
                }

                if (!empty($cp_params['names'])) {
                    $product_ids = fn_cp_generate_cart_from_file_get_product_id_by_name($cp_params['names']);

                    if (!empty($product_ids)) {
                        foreach ($product_ids as $name => $p_ids) {
                            if (!empty($p_ids)) {
                                //set artiqle for finded product and unset this in array undefined products
                                foreach ($undefined_products as $key => $undefined_product) {
                                    if ($undefined_product['name'] === $name) {
                                        $product_ids[$name]['artiqle'] = $undefined_product['artiqle'];
                                        unset($undefined_products[$key]);
                                    }
                                }
                                $cp_params['pid'] = $p_ids;
                                list($cp_products) = fn_get_products($cp_params);

                                $variations[$product_ids[$name]['artiqle']]['products'] = $cp_products;

                                foreach ($products as $product) {
                                    if ($product['artiqle'] == $product_ids[$name]['artiqle']) {
                                        $variations[$product_ids[$name]['artiqle']]['amount'] = $product['amount'];
                                        $variations[$product_ids[$name]['artiqle']]['reference_product'] = $product;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    fn_print_die($variations, $cart_data, $undefined_products, $disabled_products);
    return [$variations, $cart_data, $undefined_products, $disabled_products];
}

function fn_cp_generate_cart_from_file_check_product_status($article) : bool
{
    if(!empty($article)){
        $status=db_get_field('SELECT status FROM ?:cp_products WHERE code = ?s', $article);
        if($status!=='D') {
            return true;
        }
    }
    return false;
}

function fn_cp_generate_cart_from_cart_validate_email($email) : bool
{
    return preg_match('([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)',$email);
}

function fn_cp_generate_cart_from_file_get_product_id_by_artiqle(array $artiqles = []) : array
{
    if (!empty($artiqles)) {
        $product_ids = [];
        $status=['A','H'];
        foreach ($artiqles as $artiqle) {
            $product_ids[$artiqle] = db_get_fields('SELECT product_id FROM ?:products WHERE product_code LIKE ?l AND status IN (?a)', '%' . $artiqle . '%', $status);
        }
    }

    return $product_ids;
}

function fn_cp_generate_cart_from_file_get_product_id_by_name(array $names = []) : array
{
    if (!empty($names)) {
        $product_ids = [];
        foreach ($names as $name) {
            $condition = explode(' ', trim($name));

            $count = count($condition);

            for ($i = 1; $i <= $count; $i++) {
                if (!empty($condition)) {
                    $product_ids[$name] = db_get_fields('SELECT product_id FROM ?:product_descriptions WHERE product LIKE ?l', '%' . implode(' ', $condition) . '%');
                    if (empty($product_ids[$name])) {
                        unset($condition[array_key_last($condition)]);
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            }
        }
    }

    return $product_ids;
}

function fn_cp_generate_cart_from_file_update_template (int $template_id, array $template_data) : int
{
    $template_data['timestamp'] = TIME;
    if (empty($template_id)) {
        $template_id = db_query("INSERT INTO ?:cp_generate_cart_from_file_templates ?e", $template_data);
    } else {
        db_query(
            'UPDATE ?:cp_generate_cart_from_file_templates SET ?u WHERE template_id = ?i',
            $template_data,
            $template_id
        );
    }

    if (!empty($template_id) && isset($template_data['default_template']) && $template_data['default_template'] == YesNo::YES) {
        db_query(
            'UPDATE ?:cp_generate_cart_from_file_templates SET default_template = ?s WHERE template_id <> ?i',
            YesNo::NO,
            $template_id
        );
    }

    return $template_id;
}

function fn_cp_generate_cart_from_file_update_attachment(array $save_data) : void
{
    if (!empty($save_data['template_id'])) {
        $attachment_id = db_get_field('SELECT attachment_id FROM ?:cp_generate_cart_from_file_attachments WHERE template_id = ?i', $save_data['template_id']);

        if (!empty($attachment_id)) {
            db_query(
                'UPDATE ?:cp_generate_cart_from_file_attachments SET ?u WHERE attachment_id = ?i',
                $save_data,
                $attachment_id
            );
        } else {
            db_query("INSERT INTO ?:cp_generate_cart_from_file_attachments ?e", $save_data);
        }
    }
}

function fn_cp_generate_cart_from_file_get_old_file (int $template_id) : string
{
    if (!empty($template_id)) {
        $filename = db_get_field('SELECT filename FROM ?:cp_generate_cart_from_file_attachments WHERE template_id = ?i', $template_id);
    }

    if (!empty($filename)) {
        return $filename;
    } else {
        return '';
    }
}

function fn_cp_generate_cart_from_file_get_template_data (int $template_id) : array
{
    if (!empty($template_id)) {
        $template_data = db_get_array('SELECT * FROM ?:cp_generate_cart_from_file_templates LEFT JOIN ?:cp_generate_cart_from_file_attachments ON ?:cp_generate_cart_from_file_attachments.template_id = ?:cp_generate_cart_from_file_templates.template_id WHERE ?:cp_generate_cart_from_file_templates.template_id = ?i', $template_id);
        $template_data = reset($template_data);
    }

    if (!empty($template_data['filename'])) {
        $storage_path = 'var/cp_generate_cart_from_file/'. $template_id;
        $template_data['path_to_file'] = $storage_path . '/' . $template_data['filename'];
    }

    if (!empty($template_data)) {
        return $template_data;
    } else {
        return [];
    }
}

function fn_cp_generate_cart_from_file_delete_template(int $template_id) : void
{
    if (!empty($template_id)) {
        db_query('DELETE FROM ?:cp_generate_cart_from_file_templates WHERE template_id = ?i', $template_id);
    }
}

function fn_cp_generate_cart_from_file_delete_attachment(int $template_id) : void
{
    if (!empty($template_id)) {
        Storage::instance('cp_generate_cart_from_file')->deleteDir($template_id);

        db_query('DELETE FROM ?:cp_generate_cart_from_file_attachments WHERE template_id = ?i', $template_id);
    }
}

function fn_cp_generate_cart_from_file_get_templates() : array
{
    $templates = db_get_array('SELECT * FROM ?:cp_generate_cart_from_file_templates');

    if (!empty($templates)) {
        return $templates;
    } else {
        return [];
    }
}

function fn_cp_generate_cart_from_file_get_attachment(int $attachment_id) : bool
{
    $data = db_get_row("SELECT * FROM ?:cp_generate_cart_from_file_attachments WHERE attachment_id = ?i", $attachment_id);

    if (empty($data)) {
        return false;
    }

    $attachment_obj = Storage::instance('cp_generate_cart_from_file');

    $attachment_filename = $data['template_id'] . '/' . $data['filename'];

    if (!$attachment_obj->isExist($attachment_filename)) {
        return false;
    }

    $attachment_obj->get($attachment_filename);

    return true;
}

function fn_cp_generate_cart_from_file_get_default_template_data () : array
{
    $template_data = db_get_array('SELECT * FROM ?:cp_generate_cart_from_file_templates LEFT JOIN ?:cp_generate_cart_from_file_attachments ON ?:cp_generate_cart_from_file_attachments.template_id = ?:cp_generate_cart_from_file_templates.template_id WHERE ?:cp_generate_cart_from_file_templates.default_template = ?s', YesNo::YES);
    $template_data = reset($template_data);

    if (!empty($template_data['filename'])) {
        $storage_path = 'var/cp_generate_cart_from_file/'. $template_data['template_id'];
        $template_data['path_to_file'] = $storage_path . '/' . $template_data['filename'];
    }

    if (!empty($template_data)) {
        return $template_data;
    } else {
        return [];
    }
}

function fn_cp_generate_cart_from_file_delete_file($filename)
{

    $export_obj = Storage::instance('cp_generate_cart_from_file');
    if ($export_obj->isExist($filename)) {
        $export_obj->delete($filename);
    }
}
function fn_cp_generate_cart_from_file_delete_dir()
{
    $save_dir_path = 'var/cp_generate_cart_from_file';
    if (@is_dir($save_dir_path)) {
        fn_rm($save_dir_path);
    }
}


function fn_cp_generate_cart_from_file_generate_csv_file($data)
{
    $export_obj= Storage::instance('cp_generate_cart_from_file');
    $cp_export_dir=$export_obj->options['dir'].$export_obj->options['prefix'];
    $data = array_map('fn_cp_generate_cart_from_file_array_map', $data);

    fn_mkdir($cp_export_dir);
    $delimiter = ';';
    $eol = "\n";
    $field_names=[
        __('cp_product'),
        __('cp_product_code'),
        __('cp_product_options'),
        __('cp_vendor'),
        __('cp_price'),
        __('cp_amount'),
        __('cp_total_price'),
    ];


    Tygh::$app['view']->assign('fields', $field_names);
    Tygh::$app['view']->assign('export_data', $data);
    Tygh::$app['view']->assign('delimiter', $delimiter);
    Tygh::$app['view']->assign('eol', $eol);
    $csv = Tygh::$app['view']->fetch('design/backend/templates/views/exim/components/export_csv.tpl');


    $filename=$export_obj->generateName('cart.csv');

    file_put_contents($cp_export_dir .'/'. $filename, $csv);

    return $filename;

}
function fn_cp_generate_cart_from_file_export_file($filename)
{
    register_shutdown_function('fn_cp_generate_cart_from_file_delete_file',$filename);
    $export_obj= Storage::instance('cp_generate_cart_from_file');

    if(!$export_obj->isExist($filename)){
        return false;
    }

    $export_obj->get($filename);

    return true;



}

function fn_cp_generate_cart_from_file_array_map($arr)
{
if(isset($arr['company_id']))
    {
        $arr = [
            'product' => $arr['product'],
            'product_code' => $arr['product_code'],
            'product_options' => $arr['product_options'],
            'company_id' => $arr['company_id'],
            'price' => $arr['price'],
            'amount' => $arr['amount'],
            'total_price' => $arr['total_price'],
        ];

    }else{
    $arr = [
        'product' => $arr['product'],
        'product_code' => $arr['product_code'],
        'product_options' => $arr['product_options'],
        'price' => $arr['price'],
        'amount' => $arr['amount'],
        'total_price' => $arr['total_price'],
    ];

}

    return $arr;
}
function fn_cp_generate_cart_from_file_send_mail($data)
{

    /** @var EventDispatcher $event_dispatcher */
    $event_dispatcher = Tygh::$app['event.dispatcher'];


    $event_dispatcher->dispatch(
        "cp_generate_cart_from_files.cp_generate_cart_from_file.send_mail",
        ['cart_data' => $data],
    );

}
function fn_cp_generate_cart_from_file_get_export_data($data)
{
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
    $cart_data=$data;
    $export_data=[];
    foreach($cart_data as $key=>$product){
        foreach($product as $field=>$value){
            if(fn_allowed_for('MULTIVENDOR')){
                $export_data[$key]['company_id']=fn_get_company_name($product['company_id']);
            }
            if(in_array($field,$export_fields)){
                $export_data[$key][$field]=$value;
            }
            if(!empty($export_data[$key]['product_options'])){
                if(is_array($export_data[$key]['product_options'])){
                    $export_data[$key]['product_options']= implode(',',$export_data[$key]['product_options']);
                }
            }else{
                $export_data[$key]['product_options']='';
            }
            if(!empty($export_data[$key]['price']) and !empty($export_data[$key]['amount'])) {

                $export_data[$key]['total_price'] = $export_data[$key]['price'] * $export_data[$key]['amount'];

            }

        }
    }
    return array_map('fn_cp_generate_cart_from_file_array_map',$export_data);

}
function fn_cp_generate_cart_from_file_mailer_send_pre($mailer,$transport, Message $message, $area, $lang_code)
{
    $data=$message->getData();

if(isset($data['cart_data'])){
    $dir=$data['cart_data']['dir'];
    $filename=$data['cart_data']['filename'];
    $message->addAttachment($dir.'/'.$filename,$filename);
}
}

function fn_cp_generate_cart_from_file_generate_pdf_file($data)
{
    $export_obj= Storage::instance('cp_generate_cart_from_file');
    $cp_export_dir=$export_obj->options['dir'].$export_obj->options['prefix'];

    $data = array_map('fn_cp_generate_cart_from_file_array_map', $data);
    $notice=__('cp_notice');
    fn_mkdir($cp_export_dir);
    $field_names=[
        __('cp_product'),
        __('cp_product_code'),
        __('cp_product_options'),
        __('cp_vendor'),
        __('cp_price'),
        __('cp_amount'),
        __('cp_total_price'),
    ];
    $total_price=0;
    $html='</thead>';
    foreach($field_names as $field_name){
        $html.="<th>{$field_name}</th>";
    }
    $html.='</thead> <tbody>';
    foreach($data as $product){
        $html.='<tr>';
        foreach($product as $field){
            $html.="<td>".$field . '</td>';

        }
        $total_price+=$product['total_price'];
        $html.='</tr>';
    }
    $html.='</tbody>';
    $html.="<tfoot><td></td><td></td><td></td><td></td><td></td><td>Итого:</td><td>{$total_price}</td></tfoot>";
    $html="<HTML><table border='2'>".$html."</table>$notice</HTML>";


    Pdf::render($html,$cp_export_dir.'/cart.pdf',true);

    return 'cart.pdf';

}
