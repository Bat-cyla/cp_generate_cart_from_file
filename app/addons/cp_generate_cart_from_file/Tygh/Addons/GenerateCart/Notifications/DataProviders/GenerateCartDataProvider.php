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

namespace Tygh\Addons\GenerateCart\Notifications\DataProviders;


use Tygh\Exceptions\DeveloperException;
use Tygh\Notifications\DataProviders\BaseDataProvider;

class GenerateCartDataProvider extends BaseDataProvider
{
    protected $cart = [];

    public function __construct(array $data)
    {

        if (empty($data['cart_data'])) {
            throw new DeveloperException('Cart must not be empty.');
        }

        $this->cart = $data['cart_data'];

        $data['lang_code'] = $this->getLangCode();

        $data['cart_data'] = $this->cart;

        parent::__construct($data);

    }


    protected function getLangCode()
    {
        return CART_LANGUAGE;
    }
}