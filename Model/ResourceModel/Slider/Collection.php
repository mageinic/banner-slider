<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_BannerSlider
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\BannerSlider\Model\ResourceModel\Slider;

use MageINIC\BannerSlider\Api\Data\SliderInterface;
use MageINIC\BannerSlider\Model\ResourceModel\AbstractCollection;
use MageINIC\BannerSlider\Model\ResourceModel\Slider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;

/**
 * Slider collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'slider_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * @var string
     */
    protected $_eventPrefix = 'magecoders_slider_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'slider_collection';

    /**
     * Set first store flag
     *
     * @param  bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Add filter by store
     *
     * @param  int|array|Store $store
     * @param  bool            $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(\MageINIC\BannerSlider\Model\Slider::class, Slider::class);
        $this->_map['fields']['slider_id'] = 'main_table.slider_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(SliderInterface::class);
        $this->performAfterLoad('indainic_bannerslider_slider_store', $entityMetadata->getLinkField());
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        $entityMetadata = $this->metadataPool->getMetadata(SliderInterface::class);
        $this->joinStoreRelationTable('indainic_bannerslider_slider_store', $entityMetadata->getLinkField());
    }
}
