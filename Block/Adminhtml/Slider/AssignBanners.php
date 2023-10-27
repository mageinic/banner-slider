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

use MageINIC\BannerSlider\Block\Adminhtml\Slider\Tab\Banner;
use MageINIC\BannerSlider\Model\Slider;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use MageINIC\BannerSlider\Model\SliderFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\BlockInterface;

/**
 * BannerSlider AssignBanners Class
 */
class AssignBanners extends Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'slider/assign_banners.phtml';

    /**
     * @var Grid
     */
    protected $bannerGrid;

    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * @var EncoderInterface
     */
    protected EncoderInterface $jsonEncoder;

    /**
     * @var SliderFactory
     */
    protected SliderFactory $sliderFactory;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * AssignProducts constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param EncoderInterface $jsonEncoder
     * @param SliderFactory $sliderFactory
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        Context          $context,
        Registry         $registry,
        EncoderInterface $jsonEncoder,
        SliderFactory    $sliderFactory,
        SerializerInterface $serializer,
        array            $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->sliderFactory = $sliderFactory;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    public function getBannerGrid(): BlockInterface
    {
        if (null === $this->bannerGrid) {
            $this->bannerGrid = $this->getLayout()->createBlock(
                Banner::class,
                'mageinic.admin.banner.grid'
            );
        }
        return $this->bannerGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml(): string
    {
        return $this->getBannerGrid()->toHtml();
    }

    /**
     * Banner Json
     *
     * @return string
     */
    public function getBannersJson(): string
    {
        $banners = $this->getSlider()->getBannersPosition();
        if (!empty($banners)) {
            return $this->serializer->serialize($banners);
        }
        return '{}';
    }

    /**
     * Get Slider
     *
     * @return Slider
     */
    public function getSlider(): Slider
    {
        return $this->sliderFactory->create()->load($this->getRequest()->getParam('slider_id'));
    }
}
