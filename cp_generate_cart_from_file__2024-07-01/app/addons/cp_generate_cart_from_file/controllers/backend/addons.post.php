<?php
use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    fn_trusted_vars (
        'addon_data'
    );

    if ($mode == 'update') {
        if (isset($_REQUEST['addon_data']) && $_REQUEST['addon'] == 'cp_generate_cart_from_file') {
            foreach($_REQUEST['addon_data']['options'] as $option_id=>$val) {
                if($val == 'Y') {
                    Tygh::$app['view']->assign('option_id', $option_id );
                }
            }
        }
    }
}
