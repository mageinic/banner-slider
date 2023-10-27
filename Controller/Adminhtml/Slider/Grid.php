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

namespace MageINIC\BannerSlider\Controller\Adminhtml\Slider;

use MageINIC\BannerSlider\Block\Adminhtml\Slider\Tab\Banner;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\LayoutFactory;
use MageINIC\BannerSlider\Model\Slider;
use Magento\Framework\Registry;
use MageINIC\BannerSlider\Model\SliderFactory;

/**
 * BannerSlider Grid Class
 */
class Grid extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_BannerSlider::save';

    /**
     * @var RawFactory
     */
    protected RawFactory $resultRawFactory;

    /**
     * @var LayoutFactory
     */
    protected LayoutFactory $layoutFactory;

    /**
     * @var Registry
     */
    public Registry $registry;

    /**
     * @var Config
     */
    public Config $config;

    /**
     * @var Slider|SliderFactory
     */
    private Slider|SliderFactory $slider;

    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     * @param Registry $registry
     * @param Config $config
     * @param SliderFactory $slider
     */
    public function __construct(
        Context       $context,
        RawFactory    $resultRawFactory,
        LayoutFactory $layoutFactory,
        Registry      $registry,
        Config        $config,
        SliderFactory $slider
    ) {
        $this->registry = $registry;
        $this->config = $config;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->slider = $slider;
        parent::__construct($context);
    }

    /**
     * Grid Action
     *
     * @return Redirect|ResponseInterface|Raw|ResultInterface
     */
    public function execute()
    {
        $slider = $this->_initSlider(true);
        if (!$slider) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('bannerslider/*/', ['_current' => true, 'id' => null]);
        }
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(Banner::class, 'mageinic.admin.banner.grid')
                ->toHtml());
    }

    /**
     * Initialize requested slider and put it into registry.
     *
     * @param bool $getRootInstead
     * @return Slider|false
     */
    public function _initSlider($getRootInstead = false): bool|Slider
    {
        $sliderId = (int)$this->getRequest()->getParam('slider_id', false);
        $storeId = (int)$this->getRequest()->getParam('store_id');

        /** @var SliderFactory $model */
        $slider = $this->slider->create();
        $slider->setStoreId($storeId);
        if ($sliderId) {
            $slider->load($sliderId);
        }
        $this->registry->register('slider', $slider);
        $this->registry->register('current_slider', $slider);
        $this->config->setStoreId($this->getRequest()->getParam('store'));

        return $slider;
    }
}
