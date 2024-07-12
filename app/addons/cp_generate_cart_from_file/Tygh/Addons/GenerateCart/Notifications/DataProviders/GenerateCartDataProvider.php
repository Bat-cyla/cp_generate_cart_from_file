<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

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