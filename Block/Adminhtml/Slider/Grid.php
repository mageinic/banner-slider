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

namespace MageINIC\BannerSlider\Block\Adminhtml\Slider;

use MageINIC\BannerSlider\Block\Adminhtml\Slider\Grid\Renderer\Action;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use MageINIC\BannerSlider\Model\ResourceModel\Slider\CollectionFactory;
use MageINIC\BannerSlider\Model\Slider;
use Magento\Framework\DataObject;
use MageINIC\BannerSlider\Model\Banner;
use Magento\Framework\Exception\FileSystemException;

/**
 * Adminhtml mageinic sliders grid
 */
class Grid extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var Slider
     */
    protected Slider $mageinicSlider;

    /**
     * @var Banner
     */
    protected Banner $options;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param Slider $mageinicSlider
     * @param CollectionFactory $collectionFactory
     * @param Banner $options
     * @param array $data
     */
    public function __construct(
        Context           $context,
        Data              $backendHelper,
        Slider            $mageinicSlider,
        CollectionFactory $collectionFactory,
        Banner             $options,
        array             $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->mageinicSlider = $mageinicSlider;
        $this->options = $options;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Construct
     *
     * @return void
     * @throws FileSystemException
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setId('mageinicSliderGrid');
        $this->setDefaultSort('slider_id');
        $this->setDefaultDir('ASC');
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        /* @var $collection \MageINIC\BannerSlider\Model\ResourceModel\Slider\Collection */
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('title', ['header' => __('Title'), 'index' => 'title']);

        $this->addColumn('slider_id', ['header' => __('URL Key'), 'index' => 'slider_id']);
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                [
                    'header' => __('Store View'),
                    'index' => 'store_id',
                    'type' => 'store',
                    'store_all' => true,
                    'store_view' => true,
                    'sortable' => false,
                    'filter_condition_callback' => [$this, '_filterStoreCondition']
                ]
            );
        }

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->mageinicSlider->getAvailableStatuses()
            ]
        );

        $this->addColumn(
            'start_date',
            [
                'header' => __('Start Date'),
                'index' => 'start_date',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'end_date',
            [
                'header' => __('End Date'),
                'index' => 'end_date',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'slider_actions',
            [
                'header' => __('Action'),
                'sortable' => false,
                'filter' => false,
                'renderer' => Action::class,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * After load collection
     *
     * @return void
     */
    protected function _afterLoadCollection(): void
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * Filter store condition
     *
     * @param DataObject $column
     * @return void
     */
    protected function _filterStoreCondition(DataObject $column): void
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * Row click url
     *
     * @param DataObject $row
     * @return string
     */
    public function getRowUrl($row): string
    {
        return $this->getUrl('*/*/edit', ['slider_id' => $row->getId()]);
    }
}
