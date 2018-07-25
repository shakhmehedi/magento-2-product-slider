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

namespace Mageplaza\Productslider\Model;

use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\Productslider\Helper\Data;
use Mageplaza\Productslider\Model\ResourceModel\SliderFactory as ResourceModelFactory;

/**
 * Class Cron
 * @package Mageplaza\Productslider\Model
 */
class Cron
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Mageplaza\Productslider\Model\SliderFactory
     */
    protected $_sliderFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Mageplaza\Productslider\Model\ResourceModel\SliderFactory
     */
    protected $_resourceModelSliderFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Cron constructor.
     * @param Context $context
     * @param TypeListInterface $cacheTypeList
     * @param DateTime $date
     * @param SliderFactory $sliderFactory
     * @param ResourceModelFactory $resourceModelSliderFactory
     * @param ResourceConnection $resource
     * @param Pool $cacheFrontendPool
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        TypeListInterface $cacheTypeList,
        DateTime $date,
        SliderFactory $sliderFactory,
        ResourceModelFactory $resourceModelSliderFactory,
        ResourceConnection $resource,
        Pool $cacheFrontendPool,
        Data $helper
    )
    {
        $this->_resource                   = $resource;
        $this->_sliderFactory              = $sliderFactory;
        $this->_resourceModelSliderFactory = $resourceModelSliderFactory;
        $this->date                        = $date;
        $this->_cacheTypeList              = $cacheTypeList;
        $this->_cacheFrontendPool          = $cacheFrontendPool;
        $this->helper                      = $helper;
    }

    /**
     * Auto Clean Block HTML cache
     */
    public function autoRefreshCache()
    {
        $currentTime = $this->date->gmtTimestamp();

        foreach ($this->helper->getActiveSliders() as $slider) {
            $cacheLifeTime = $slider->getData('time_cache');
            $cacheLastTime = $this->date->timestamp($slider->getData('cache_last_time'));

            if ($cacheLifeTime <= ($currentTime - $cacheLastTime) && $cacheLifeTime) {
                $cache_types = ['block_html'];
                foreach ($cache_types as $type) {
                    $this->_cacheTypeList->cleanType($type);
                }

                foreach ($this->_cacheFrontendPool as $cache_clean_flush) {
                    $cache_clean_flush->getBackend()->clean();
                }

                $this->saveCacheLastTime($currentTime);
            }
        }
    }

    /**
     * Update Cache last Time in database
     *
     * @param $currentTime
     */
    public function saveCacheLastTime($currentTime)
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $table      = $connection->getTableName('mageplaza_productslider_slider');

        $where = ['slider_id = ?' => 1];
        $connection->update($table, ['cache_last_time' => $this->date->gmtDate(null, $currentTime)], $where);
    }
}