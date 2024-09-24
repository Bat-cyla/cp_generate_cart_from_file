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

use Tygh\Enum\SiteArea;
use Tygh\Notifications\DataValue;
use Tygh\Addons\GenerateCart\Notifications\DataProviders\GenerateCartDataProvider;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Enum\UserTypes;
use Tygh\Notifications\Transports\Mail\MailTransport;




defined('BOOTSTRAP') or die('Access denied');

$mail_event = [
    'id'        => 'cp_generate_cart_from_files.cp_generate_cart_from_file.send_mail',
    'group'     => 'cp_generate_cart_from_file',
    'data_provider' => [GenerateCartDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::CUSTOMER => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => SiteArea::STOREFRONT,
                'from'            => 'company_orders_department',
                'to'              => DataValue::create('cart_data.email'),
                'template_code'   => 'send_mail_notification',
                'legacy_template' => 'addons/cp_generate_cart_from_file/cp_generate_cart_from_file.tpl',
                'language_code'   => DataValue::create('lang_code', CART_LANGUAGE),
            ]),
        ],
    ],
];


$send_mail_event = $mail_event;

$send_mail_event['id'] = 'cp_generate_cart_from_files.cp_generate_cart_from_file.send_mail';

$schema[$send_mail_event['id']] = $send_mail_event;

return $schema;
