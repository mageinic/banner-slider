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

namespace MageINIC\BannerSlider\Helper;

use MageINIC\BannerSlider\Api\BannerRepositoryInterface;
use MageINIC\BannerSlider\Api\Data\SliderInterface;
use MageINIC\BannerSlider\Api\SliderRepositoryInterface;
use MageINIC\BannerSlider\Model\BannerFactory;
use MageINIC\BannerSlider\Model\Config\Source\Targets;
use MageINIC\BannerSlider\Model\Slider;
use MageINIC\BannerSlider\Model\SliderLinkManagement;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface as Serializer;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface as FrameworkUrlInterface;

/**
 * Helper Data of Banner Slider
 */
class Data extends AbstractHelper
{
    public const EXTENSION_ENABLE_PATH = 'bannerslider/general/enable';
    public const ENABLE_AT_PAGE_PATH = 'bannerslider/general/enable_at_home';
    public const SLIDER_HOME_PAGE_PATH = 'bannerslider/general/slider_id';
    public const BACKGROUND_COLOR_PATH = 'bannerslider/slider_setting/color_option';
    public const MEDIA_DIR = 'MageINIC/bannerslider';
    public const STYLE_PATH = 'bannerslider/slider_setting/choose_your_style';
    public const ARROWS = 'bannerslider/slider_setting/arrows_slider';
    public const DOTS = 'bannerslider/slider_setting/dots_slider';
    public const INFINITE_LOOPING = 'bannerslider/slider_setting/infinite_looping';
    public const SLIDER_SPEED = 'bannerslider/slider_setting/slider_speed';
    public const DEFAULT_SLIDE = 'bannerslider/slider_setting/slide_to_show';
    public const DEFAULT_SLICK = 'bannerslider/slider_setting/slide_to_scroll';
    public const AUTOPLAY = 'bannerslider/slider_setting/autoplay_slider';
    public const AUTOPLAY_SPEED = 'bannerslider/slider_setting/autoplay_slider_speed';
    public const BREAKPOINT = 'bannerslider/slider_setting/breakpoints';
    public const NAVBAR = 'bannerslider/slider_setting/navbar';

    /**
     * @var UrlInterface
     */
    protected UrlInterface $backendUrl;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var BannerRepositoryInterface $bannerRepository
     */
    protected BannerRepositoryInterface $bannerRepository;

    /**
     * @var SliderLinkManagement $sliderLinkManagement
     */
    protected SliderLinkManagement $sliderLinkManagement;

    /**
     * @var Slider $sliderModel
     */
    protected Slider $sliderModel;

    /**
     * @var BannerFactory $bannerFactory
     */
    protected BannerFactory $bannerFactory;

    /**
     * @var Serializer
     */
    private Serializer $serializer;

    /**
     * @var SliderRepositoryInterface
     */
    private SliderRepositoryInterface $sliderRepository;

    /**
     * @var TimezoneInterface 
     */
    private TimezoneInterface $date;

    /**
     * @param Context $context
     * @param UrlInterface $backendUrl
     * @param Slider $sliderModel
     * @param BannerFactory $bannerFactory
     * @param SliderRepositoryInterface $sliderRepository
     * @param BannerRepositoryInterface $bannerRepository
     * @param SliderLinkManagement $sliderLinkManagement
     * @param TimezoneInterface $date
     * @param StoreManagerInterface $storeManager
     * @param Serializer $serializer
     */
    public function __construct(
        Context                   $context,
        UrlInterface              $backendUrl,
        Slider                    $sliderModel,
        BannerFactory             $bannerFactory,
        SliderRepositoryInterface $sliderRepository,
        BannerRepositoryInterface $bannerRepository,
        SliderLinkManagement      $sliderLinkManagement,
        TimezoneInterface         $date,
        StoreManagerInterface     $storeManager,
        Serializer                $serializer
    ) {
        parent::__construct($context);
        $this->backendUrl = $backendUrl;
        $this->sliderModel = $sliderModel;
        $this->bannerFactory = $bannerFactory;
        $this->sliderRepository = $sliderRepository;
        $this->sliderLinkManagement = $sliderLinkManagement;
        $this->bannerRepository = $bannerRepository;
        $this->storeManager = $storeManager;
        $this->date = $date;
        $this->serializer = $serializer;
    }

    /**
     * Get Base Url Media
     *
     * @param string $path
     * @param bool $secure
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseUrlMedia(string $path = '', bool $secure = false): string
    {
        return $this->storeManager->getStore()->getBaseUrl(FrameworkUrlInterface::URL_TYPE_MEDIA, $secure) . $path;
    }

    /**
     * Get MediaUrl
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(FrameworkUrlInterface::URL_TYPE_MEDIA);
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
     * Get Slider status at home page
     *
     * @return boolean
     */
    public function getIsExtensionEnabled(): bool
    {
        return $this->scopeConfig->getValue(self::EXTENSION_ENABLE_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Slider status at home page
     *
     * @return boolean
     */
    public function getIsEnabledAtHome(): bool
    {
        return $this->scopeConfig->getValue(self::ENABLE_AT_PAGE_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get SliderId At Home
     *
     * @return int
     */
    public function getSliderIdAtHome(): int
    {
        return (int)$this->scopeConfig->getValue(self::SLIDER_HOME_PAGE_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Show Dots By Default
     *
     * @return bool
     */
    public function getShowDotsByDefault(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::DOTS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check status
     *
     * @return bool
     */
    public function isArrow(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::ARROWS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is Auto Play
     *
     * @return bool
     */
    public function isAutoPlay(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::AUTOPLAY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Enable Infinite Looping
     *
     * @return bool
     */
    public function getEnableInfiniteLooping(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::INFINITE_LOOPING, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is Nav
     *
     * @return bool
     */
    public function isNav(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::NAVBAR, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Slider Speed
     *
     * @return int
     */
    public function getSliderSpeed(): int
    {
        return (int)$this->scopeConfig->getValue(self::SLIDER_SPEED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Auto Play Speed
     *
     * @return int
     */
    public function getAutoPlaySpeed(): int
    {
        return (int)$this->scopeConfig->getValue(self::AUTOPLAY_SPEED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Defualt Slider
     *
     * @return int
     */
    public function getDefaultSlide(): int
    {
        return (int)$this->scopeConfig->getValue(self::DEFAULT_SLIDE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Default Slick
     *
     * @return int
     */
    public function getDefaultSlick(): int
    {
        return (int)$this->scopeConfig->getValue(self::DEFAULT_SLICK, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get background color
     *
     * @return string
     */
    public function getBackGroundColor(): string
    {
        return (string)$this->scopeConfig->getValue(self::BACKGROUND_COLOR_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get style of slider
     *
     * @return string
     */
    public function getStyle(): string
    {
        return (string)$this->scopeConfig->getValue(self::STYLE_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Slider Data By Id
     *
     * @param mixed $id
     * @return SliderInterface
     * @throws LocalizedException
     */
    public function getSliderDataById(mixed $id)
    {
        return $this->sliderRepository->getById($id);
    }

    /**
     *  Get Banners Collection By SliderId
     *
     * @param mixed $id
     * @return array
     * @throws LocalizedException
     */
    public function getBannersCollectionBySliderId(mixed $id): array
    {
        $slider = $this->getSliderDataById($id);
        $banners = $this->sliderModel->getCurrentSliderBannersPosition($slider);
        $bannersData = [];
        foreach ($banners as $key => $value) {
            $loadedBanner = $this->bannerRepository->getById($key);
            if (!$loadedBanner->getStatus()) {
                continue;
            }
            $loadedBanner->setPosition($value);
            $bannersData[] = $loadedBanner;
            usort($bannersData, [$this, "cmp"]);
        }

        return $bannersData;
    }

    /**
     * Get Banners Target
     *
     * @param mixed $targetValue
     * @return bool|string
     */
    public function getBannersTarget(mixed $targetValue): bool|string
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
     * Get Slider Timing By Slider Id
     *
     * @param mixed $id
     * @return bool
     * @throws LocalizedException
     */
    public function getSliderTimingBySliderId(mixed $id): bool
    {
        $slider = $this->getSliderDataById($id);
        $startDate = $slider->getStartDate();
        $endDate = $slider->getEndDate();

        $current = date('Y-m-d h:i:sa');
        $start = $this->date->date($startDate)->format('Y-m-d h:i:sa');
        $end = $this->date->date($endDate)->format('Y-m-d h:i:sa');

        $showSlider = false;
        if ($current > $start && $current < $end) {
            $showSlider = true;
        }

        return $showSlider;
    }

    /**
     * Get Banner ImageUrl
     *
     * @param string $image
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBannerImageUrl(string $image): string
    {
        return $this->getMediaUrl().self::MEDIA_DIR.'/'.$image;
    }

    /**
     * CMP
     *
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    private function cmp(mixed $a, mixed $b): int
    {
        return strcmp($a->getPosition(), $b->getPosition());
    }

    /**
     * Get Slider id
     *
     * @param mixed $config_path
     * @return array|bool|int|string|null
     */
    public function getConfig(mixed $config_path): array|bool|int|string|null
    {
        return $this->scopeConfig->getValue($config_path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Slider Banner Url
     *
     * @return string
     */
    public function getSliderBannerUrl(): string
    {
        return $this->backendUrl->getUrl('*/*/banners', ['_current' => true]);
    }

    /**
     * Get Backend Url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getBackendUrl(string $route = '', array $params = ['_current' => true]): string
    {
        return $this->backendUrl->getUrl($route, $params);
    }

    /**
     * Receive Breakpoints
     *
     * @return string
     */
    public function getBreakPoints(): string
    {
        $breakpoints = $this->scopeConfig->getValue(self::BREAKPOINT, ScopeInterface::SCOPE_STORE);
        $values = [];
        $breakpoints = $this->serializer->unserialize($breakpoints);
        foreach ($breakpoints as $breakpoint) {
            $values[] = [
                "breakpoint" => (int)$breakpoint['breakpoint'],
                "settings" => [
                    "slidesToShow" => (int)$breakpoint['slides_to_show'],
                    "slidesToScroll" => (int)$breakpoint['slides_to_scroll'],
                    "dots" => (bool)$breakpoint['dots']
                ]
            ];
        }
        return $this->serializer->serialize($values);
    }
}
