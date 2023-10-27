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

namespace MageINIC\BannerSlider\Block\Adminhtml\Slider\Tab;

use MageINIC\BannerSlider\Block\Adminhtml\Slider\Tab\Renderer\BannerImage;
use MageINIC\BannerSlider\Model\BannerFactory;
use MageINIC\BannerSlider\Model\Slider;
use MageINIC\BannerSlider\Model\SliderFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use MageINIC\BannerSlider\Model\Banner as Option;

/**
 * BannerSlider Banner Class
 */
class Banner extends Extended
{
    /**
     * @var Registry|null
     */
    protected $coreRegistry = null;

    /**
     * @var BannerFactory
     */
    protected $bannerFactory;

    /**
     * @var SliderFactory
     */
    protected $sliderFactory;

    /**
     * @var Option
     */
    private Option $option;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param BannerFactory $bannerFactory
     * @param SliderFactory $sliderFactory
     * @param Registry $coreRegistry
     * @param Option $option
     * @param array $data
     */
    public function __construct(
        Context       $context,
        Data          $backendHelper,
        BannerFactory $bannerFactory,
        SliderFactory $sliderFactory,
        Registry      $coreRegistry,
        Option        $option,
        array         $data = []
    ) {
        $this->bannerFactory = $bannerFactory;
        $this->sliderFactory = $sliderFactory;
        $this->coreRegistry = $coreRegistry;
        $this->option = $option;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('indianic_slider_banner');
        $this->setDefaultSort('banner_id');
        $this->setUseAjax(true);
    }

    /**
     * Get Slider
     *
     * @return Slider
     */
    public function getSlider()
    {
        return $this->sliderFactory->create()->load($this->getRequest()->getParam('slider_id'));
    }

    /**
     * Column Filter Collection
     *
     * @param object $column
     * @return $this|Banner
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_slider') {
            $bannerIds = $this->_getSelectedBanners();
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('banner_id', ['in' => $bannerIds]);
            } elseif (!empty($bannerIds)) {
                $this->getCollection()->addFieldToFilter('banner_id', ['nin' => $bannerIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare Collection
     *
     * @return Banner
     * @throws LocalizedException
     */
    protected function _prepareCollection()
    {
        if ($this->getSlider()->getId()) {
            $this->setDefaultFilter(['in_slider' => 1]);
        }

        $collection = $this->bannerFactory->create()->getCollection();
        $collection->addFieldToSelect('name');
        $collection->addFieldToSelect('banner_id');
        $collection->addFieldToSelect('media');
        $collection->addFieldToSelect('media_type');
        $collection->joinField(
            'position',
            'indianic_slider_banner',
            'position',
            'banner_id = main_table.banner_id',
            'position.slider_id = ' . (int)$this->getRequest()->getParam('slider_id', 0),
            'left'
        );

        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }

        $this->setCollection($collection);

        if ($this->getSlider()->getBannersReadonly()) {
            $bannerIds = $this->_getSelectedBanners();
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            $this->getCollection()->addFieldToFilter('banner_id', ['in' => $bannerIds]);
        }

        return parent::_prepareCollection();
    }

    /**
     * Prepare Column
     *
     * @return Banner
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        if (!$this->getSlider()->getBannersReadonly()) {
            $this->addColumn(
                'in_slider',
                [
                    'type' => 'checkbox',
                    'name' => 'in_slider',
                    'values' => $this->_getSelectedBanners(),
                    'index' => 'banner_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction'
                ]
            );
        }

        $this->addColumn(
            'banner_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'banner_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'media',
            [
                'header' => __('Media'),
                'sortable' => true,
                'index' => 'media',
                'renderer'  => BannerImage::class,
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'media_type',
            [
                'header' => __('Media Type'),
                'index' => 'media_type',
                'type' => 'options',
                'options' => $this->option->getAvailableMedias()
            ]
        );

        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'editable' => !$this->getSlider()->getBannersReadonly()
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid URL
     *
     * @return string
     */
    public function getGridUrl(): string
    {
        return $this->getUrl('bannerslider/*/Grid', ['_current' => true]);
    }

    /**
     * Retrieve selected Banners
     *
     * @return array
     */
    protected function _getSelectedBanners()
    {
        $banners = $this->getRequest()->getPost('selected_banners');

        if ($banners === null) {
            $banners = $this->getSlider()->getBannersPosition();
            return array_keys($banners);
        }
        return $banners;
    }
}
