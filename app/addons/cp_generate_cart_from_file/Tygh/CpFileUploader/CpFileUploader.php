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
namespace Tygh\CpFileUploader;

use Tygh\Storage;
use Tygh\Registry;

class CpFileUploader
{
    private string $name;
    private string $path;
    private string $error;

    public function __construct(string $name, string $path, string $error)
    {
        $this->name = $name;
        $this->path = $path;
        $this->error = $error;
    }

    public function uploadOrder() : array
    {
        if (
            !empty($this->name)
            && !empty($this->path)
            && empty($this->error)
        ) {
            $name = $this->name;
            $save_dir_path = 'var/cp_generate_cart_from_file/';
            if (!@is_dir($save_dir_path)) {
                if (fn_mkdir($save_dir_path) == false) {
                    return [];
                }
            }

            $ext = fn_get_file_ext($this->name);
            $extens = array('xls', 'xlsx', 'csv');
            if (!in_array(strtolower($ext), $extens) || !(filesize($this->path))) {
                fn_set_notification('W', __('warning'), __("cp_bad_file_extension"));
                return [];
            }

            if (in_array(fn_get_mime_content_type($this->path, true, 'text/plain'), Registry::get('config.forbidden_mime_types'))) {
                return [];
            }
            $new_file_path = $save_dir_path . $this->name;

            if (!file_exists($new_file_path)) {
                list($filesize, $filename) = Storage::instance('cp_generate_cart_from_file')->put($this->name, [
                    'file' => $this->path
                ]);
                $filename = fn_basename($filename);
                @chmod($new_file_path, DEFAULT_FILE_PERMISSIONS);
            }

            return [$new_file_path , $ext, $name];
        }
        return [];
    }

    public function uploadTemplate(int $template_id, string $old_filename) : array
    {
        if (
            !empty($this->name)
            && !empty($this->path)
            && empty($this->error)
        ) {
            $save_dir_path = 'var/cp_generate_cart_from_file/'. $template_id;

            if (
                @is_dir($save_dir_path)
                && !empty($old_filename)
                && file_exists($save_dir_path . '/' . $old_filename)
            ) {
                Storage::instance('cp_generate_cart_from_file')->delete($template_id . '/'. $old_filename);
            }

            if (!@is_dir($save_dir_path)) {
                if (fn_mkdir($save_dir_path) == false) {
                    return [];
                }
            }

            $ext = fn_get_file_ext($this->name);
            $extens = array('xls', 'xlsx', 'csv');
            if (!in_array(strtolower($ext), $extens) || !(filesize($this->path))) {
                fn_set_notification('W', __('warning'), __("cp_bad_file_extension"));
                return [];
            }

            if (in_array(fn_get_mime_content_type($this->path, true, 'text/plain'), Registry::get('config.forbidden_mime_types'))) {
                return [];
            }
            $new_file_path = $save_dir_path . $this->name;

            if (!file_exists($new_file_path)) {
                list($filesize, $filename) = Storage::instance('cp_generate_cart_from_file')->put($template_id . '/'. $this->name, [
                    'file' => $this->path
                ]);
                $filename = fn_basename($filename);
                @chmod($new_file_path, DEFAULT_FILE_PERMISSIONS);
            }
            return [$filename, $filesize];
        }
        return [];
    }
}