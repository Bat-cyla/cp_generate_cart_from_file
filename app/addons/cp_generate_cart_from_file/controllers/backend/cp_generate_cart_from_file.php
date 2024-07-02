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

use Tygh\Tygh;
use Tygh\CpFileUploader\CpFileUploader;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$view = Tygh::$app['view'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update') {
        $params = $_REQUEST;
        $template_data = $params['template_data'];
        if (!empty($params['template_data'])) {
            $template_id = fn_cp_generate_cart_from_file_update_template(
                $template_data['template_id'],
                $template_data
            );
        }

        if (!empty($template_id) && isset($_FILES, $_FILES['cp_attachment']) && empty(reset($_FILES['cp_attachment']['error']))) {
            $file = $_FILES['cp_attachment'];
            $old_filename = fn_cp_generate_cart_from_file_get_old_file($template_id);
            $uploader = new CpFileUploader(
                reset($file['name']),
                reset($file['tmp_name']),
                reset($file['error'])
            );
            list($filename, $filesize) = $uploader->uploadTemplate($template_id, $old_filename);

            $save_data = [
                'template_id' => $template_id,
                'filename' => $filename,
                'filesize' => $filesize
            ];
            fn_cp_generate_cart_from_file_update_attachment($save_data);
        }

        if (!empty($template_id)) {
            return [CONTROLLER_STATUS_OK, 'cp_generate_cart_from_file.update?template_id=' . $template_id];
        } else {
            return [CONTROLLER_STATUS_OK, 'cp_generate_cart_from_file.update'];
        }
    }

    if ($mode == 'delete') {
        $params = $_REQUEST;
        $template_id = !empty($params['template_id']) ? $params['template_id'] : 0;

        if (!empty($template_id)) {
            fn_cp_generate_cart_from_file_delete_template($template_id);
            fn_cp_generate_cart_from_file_delete_attachment($template_id);
        }

        return [CONTROLLER_STATUS_OK, 'cp_generate_cart_from_file.manage'];
    }
}

if ($mode == 'manage') {
    $templates = fn_cp_generate_cart_from_file_get_templates();

    $view->assign([
        'templates' => $templates,
    ]);
}elseif ($mode == 'update') {
    $params = $_REQUEST;
    $template_id = !empty($params['template_id']) ? $params['template_id'] : 0;
    $template_data = fn_cp_generate_cart_from_file_get_template_data($template_id);

    $view->assign([
        'template_data' => $template_data
    ]);
}