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

namespace MageINIC\BannerSlider\Block\Widget;

use Exception;
use MageINIC\BannerSlider\Api\BannerRepositoryInterface;
use MageINIC\BannerSlider\Api\Data\SliderInterface;
use MageINIC\BannerSlider\Model\Config\Source\Targets;
use MageINIC\BannerSlider\Model\ResourceModel\Slider\CollectionFactory;
use MageINIC\BannerSlider\Model\Slider;
use MageINIC\BannerSlider\Model\SliderFactory;
use MageINIC\BannerSlider\Helper\Data;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface as Serializer;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Widget\Block\BlockInterface;

/**
 * Block BannerSlider
 */
class BannerSlider extends Template implements BlockInterface
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'MageINIC_BannerSlider::sliders/slick.phtml';

    /**
     * @var bool
     */
    protected $isApplied = false;

    /**
     * @var SliderFactory
     */
    protected SliderFactory $sliderFactory;

    /**
     * @var Data
     */
    public Data $helper;

    /**
     * @var TimezoneInterface
     */
    protected TimezoneInterface $timezone;

    /**
     * @var Serializer
     */
    private Serializer $serializer;

    /**
     * @var Slider
     */
    protected Slider $sliderModel;

    /**
     * @var BannerRepositoryInterface
     */
    protected BannerRepositoryInterface $bannerRepository;

    /**
     * @param Context $context
     * @param SliderFactory $sliderFactory
     * @param FilterProvider $templateProcessor
     * @param Data $helper
     * @param CollectionFactory $collectionFactory
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezone
     * @param Serializer $serializer
     * @param Slider $sliderModel
     * @param BannerRepositoryInterface $bannerRepository
     * @param array $data
     */
    public function __construct(
        Context                   $context,
        SliderFactory             $sliderFactory,
        FilterProvider            $templateProcessor,
        Data                      $helper,
        CollectionFactory         $collectionFactory,
        DateTime                  $dateTime,
        TimezoneInterface         $timezone,
        Serializer                $serializer,
        Slider                    $sliderModel,
        BannerRepositoryInterface $bannerRepository,
        array                     $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->templateProcessor = $templateProcessor;
        $this->helper = $helper;
        $this->sliderFactory = $sliderFactory;
        $this->collectionFactory = $collectionFactory;
        $this->dateTime = $dateTime;
        $this->timezone = $timezone;
        $this->serializer = $serializer;
        $this->sliderModel = $sliderModel;
        $this->bannerRepository = $bannerRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get Current StoreId
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCurrentStoreId(): int
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get Banners Target
     *
     * @param string $targetValue
     * @return bool|string
     */
    public function getBannersTarget(string $targetValue): bool|string
    {
        switch ($targetValue) {
            case Targets::BANNER_TARGET_SAME_TAB:
                return false;
            case Targets::BANNER_TARGET_NEW_TAB:
                return '_blank';
        }
        return false;
    }

    /**
     * Get Banner Image Url
     *
     * @param string $image
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBannerImageUrl(string $image): string
    {
        return $this->helper->getBannerImageUrl($image);
    }

    /**
     * Get Slider Collection
     *
     * @return DataObject
     * @throws NoSuchEntityException
     */
    public function getSlider(): DataObject
    {
        $currentDateTime = $this->dateTime->gmtDate();
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('slider_id', ['eq' => $this->getSliderIdAtHome()]);
        $collection->addStoreFilter($this->storeManager->getStore()->getId());
        $collection->addFieldToFilter('start_date', ['lt' => $currentDateTime]);
        $collection->addFieldToFilter('end_date', ['gt' => $currentDateTime]);
        return $collection->getFirstItem();
    }

    /**
     * Get Banners Collection By Slider Id
     *
     * @param mixed $id
     * @return array
     * @throws LocalizedException
     */
    public function getBannersCollectionBySliderId(mixed $id): array
    {
        return $this->helper->getBannersCollectionBySliderId($id);
    }

    /**
     * Filters the Content
     *
     * @param string $string
     * @return string
     * @throws Exception
     */
    public function filterOutputHtml(string $string): string
    {
        return $this->templateProcessor->getPageFilter()->filter($string);
    }

    /**
     * Get Enable Extension
     *
     * @return bool
     */
    public function getEnableExtension(): bool
    {
        return $this->helper->getIsExtensionEnabled();
    }

    /**
     * Get SliderId At Home
     *
     * @return int
     */
    public function getSliderIdAtHome(): int
    {
        if (!$this->hasData('slider_id')) {
            $this->setData('slider_id', $this->helper->getSliderIdAtHome());
        }

        return (int)$this->getData('slider_id');
    }

    /**
     * Get Background Color
     *
     * @return string
     */
    public function getBackGroundColor(): string
    {
        if (!$this->hasData('color_option')) {
            $this->setData('color_option', $this->helper->getBackGroundColor());
        }

        return (string)$this->getData('color_option');
    }

    /**
     * Receive Arrow on slider Enabled or not
     *
     * @return bool
     */
    public function isArrow(): bool
    {
        if (!$this->hasData('arrow_slider')) {
            $this->setData('arrow_slider', $this->helper->isArrow());
        }
        return (bool)$this->getData('arrow_slider');
    }

    /**
     * Get Show Dots By Default
     *
     * @return bool
     */
    public function getShowDotsByDefault(): bool
    {
        if (!$this->hasData('dots_slider')) {
            $this->setData('dots_slider', $this->helper->getShowDotsByDefault());
        }

        return (bool)$this->getData('dots_slider');
    }

    /**
     * Get Infinite Looping
     *
     * @return bool
     */
    public function getInfiniteLooping(): bool
    {
        if (!$this->hasData('infinite_looping')) {
            $this->setData('infinite_looping', $this->helper->getEnableInfiniteLooping());
        }

        return (bool)$this->getData('infinite_looping');
    }

    /**
     * Get Autoplay
     *
     * @return bool
     */
    public function getAutoplay(): bool
    {
        if (!$this->hasData('autoplay')) {
            $this->setData('autoplay', $this->helper->isAutoPlay());
        }

        return (bool)$this->getData('autoplay');
    }

    /**
     * Get Slider Speed
     *
     * @return int
     */
    public function getSliderSpeed(): int
    {
        if (!$this->hasData('slider_speed')) {
            $this->setData('slider_speed', $this->helper->getSliderSpeed());
        }

        return (int)$this->getData('slider_speed');
    }

    /**
     * Get Autoplay Speed
     *
     * @return int
     */
    public function getAutoplaySpeed(): int
    {
        if (!$this->hasData('autoplay_speed')) {
            $this->setData('autoplay_speed', $this->helper->getAutoPlaySpeed());
        }

        return (int)$this->getData('autoplay_speed');
    }

    /**
     * Get Default Slick
     *
     * @return int
     */
    public function getDefaultSlick(): int
    {
        if (!$this->hasData('default_slick')) {
            $this->setData('default_slick', $this->helper->getDefaultSlick());
        }

        return (int)$this->getData('default_slick');
    }

    /**
     * Get Default Slide
     *
     * @return int
     */
    public function getDefaultSlide(): int
    {
        if (!$this->hasData('default_slide')) {
            $this->setData('default_slide', $this->helper->getDefaultSlide());
        }

        return (int)$this->getData('default_slide');
    }
    /**
     * Is Nav
     *
     * @return bool
     */
    public function isNav(): bool
    {
        if (!$this->hasData('navbar')) {
            $this->setData('navbar', $this->helper->isNav());
        }
        return (bool)$this->getData('navbar');
    }

    /**
     * Receive configuration for Slider component
     *
     * @return array
     */
    public function getJsonConfig()
    {
        return $this->getSliderConfig();
    }

    /**
     * Get Break Point
     *
     * @return string
     */
    public function getBreakPoint()
    {
        return $this->helper->getBreakPoints();
    }

    /**
     * Get Hyva Break Point
     *
     * @return string
     */
    public function getHyvaBreakPoint()
    {
        return $this->helper->getHyvaBreakPoints();
    }

    /**
     * Get Slider Config
     *
     * @return array
     */
    public function getSliderConfig(): array
    {

        return [
            'arrows' => $this->isArrow(),
            'infinite' => (bool)$this->getInfiniteLooping(),
            'dots' => (bool)$this->getShowDotsByDefault(),
            'speed' => $this->getSliderSpeed() ? $this->getSliderSpeed() : 400,
            'slidesToShow' => $this->getDefaultSlide() ? $this->getDefaultSlide() : 4,
            'slidesToScroll' => $this->getDefaultSlick() ? $this->getDefaultSlick() : 1,
            'autoplay' => (bool)$this->getAutoPlay(),
            'centerMode' => false,
            'autoplaySpeed' => $this->getAutoPlaySpeed() ? $this->getAutoPlaySpeed() : 2000,
            'responsive' => $this->helper->getBreakPoints()
        ];
    }

    /**
     * Get Helper Data
     *
     * @return Data
     */
    public function getHelperData()
    {
        return $this->helper;
    }
}
