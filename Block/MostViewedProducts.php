<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Productslider
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Productslider\Block;

/**
 * Class MostViewedProducts
 * @package Mageplaza\Productslider\Block
 */
class MostViewedProducts extends AbstractSlider
{
    /**
     * Get Product Collection of MostViewed Products
     * @return mixed
     */
    public function getProductCollection()
    {
        return $this->getMostViewedProductsCollection();
    }

    /**
     * @return string
     */
    public function getProductCacheKey()
    {
        return 'mageplaza_product_slider_mostviewed';
    }
}