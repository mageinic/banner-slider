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

namespace MageINIC\BannerSlider\Controller\Adminhtml;

use MageINIC\BannerSlider\Model\SliderFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory as SliderLayoutFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * BannerSlider Slider Class
 */
abstract class Slider extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_BannerSlider::save';

    /**
     * @var Registry
     */
    protected Registry $coreRegistry;

    /**
     * @var SliderFactory
     */
    protected SliderFactory $sliderFactory;

    /**
     * @var RawFactory
     */
    protected RawFactory $resultRawFactory;

    /**
     * @var SliderLayoutFactory
     */
    protected SliderLayoutFactory $layoutFactory;

    /**
     * @var PageFactory
     */
    protected PageFactory $_resultPageFactory;

    /**
     * @var LayoutFactory
     */
    protected LayoutFactory $_resultLayoutFactory;

    /**
     * @var ForwardFactory
     */
    protected ForwardFactory $_resultForwardFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param ForwardFactory $resultForwardFactory
     * @param SliderFactory $sliderFactory
     * @param RawFactory $resultRawFactory
     * @param SliderLayoutFactory $layoutFactory
     */
    public function __construct(
        Context             $context,
        Registry            $coreRegistry,
        PageFactory         $resultPageFactory,
        LayoutFactory       $resultLayoutFactory,
        ForwardFactory      $resultForwardFactory,
        SliderFactory       $sliderFactory,
        RawFactory          $resultRawFactory,
        SliderLayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->sliderFactory = $sliderFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage($resultPage): Page
    {
        $resultPage->setActiveMenu('MageINIC_BannerSlider::mageinic_banner')
            ->addBreadcrumb(__('Sliders'), __('Sliders'))
            ->addBreadcrumb(__('MageINIC Bannersliders'), __('MageINIC Bannersliders'));
        return $resultPage;
    }
}
