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

namespace MageINIC\BannerSlider\Model;

use MageINIC\BannerSlider\Api\Data\SliderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Model Slider
 */
class Slider extends AbstractModel implements SliderInterface, IdentityInterface
{
    /**
     * Slider's Statuses
     */
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;

    /**
     * MageINIC page cache tag
     */
    public const CACHE_TAG = 'mageinic_slider';

    /**
     * @var string
     */
    protected $cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $eventPrefix = 'mageinic_slider';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Slider::class);
    }
    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores(): array
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array)$this->getData('store_id');
    }

    /**
     * Check if page identifier exist for specific store
     *
     * @param string $identifier
     * @param int $storeId
     * @return mixed
     * @throws LocalizedException
     */
    public function checkIdentifier(string $identifier, int $storeId): mixed
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Prepare slider's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses(): array
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getSliderId(): ?int
    {
        return $this->getData(self::SLIDER_ID);
    }

    /**
     * Retrieve slider title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return (string)$this->getData(self::TITLE);
    }

    /**
     * Retrieve slider position
     *
     * @return string
     */
    public function getSliderPosition(): string
    {
        return (string)$this->getData(self::SLIDER_POSITION);
    }

    /**
     * Retrieve status
     *
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Retrieve slider sort type
     *
     * @return int
     */
    public function getSortType(): ?int
    {
        return $this->getData(self::SORT_TYPE);
    }

    /**
     * Retrieve slider default items
     *
     * @return int
     */
    public function getDefaultItems(): ?int
    {
        return $this->getData(self::DEFAULT_ITEMS);
    }

    /**
     * Retrieve slider caption
     *
     * @return string
     */
    public function getCaption(): string
    {
        return (string)$this->getData(self::CAPTION);
    }

    /**
     * @inheritdoc
     */
    public function getStartDate(): ?string
    {
        return $this->getData(self::START_DATE);
    }

    /**
     * @inheritdoc
     */
    public function getEndDate(): ?string
    {
        return $this->getData(self::END_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setSliderId($sliderId): SliderInterface
    {
        return $this->setData(self::SLIDER_ID, $sliderId);
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title): SliderInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function setSliderPosition($sliderPosition): SliderInterface
    {
        return $this->setData(self::SLIDER_POSITION, $sliderPosition);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status): SliderInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function setSortType($sortType): SliderInterface
    {
        return $this->setData(self::SORT_TYPE, $sortType);
    }

    /**
     * @inheritdoc
     */
    public function setDefaultItems($defaultItems): SliderInterface
    {
        return $this->setData(self::DEFAULT_ITEMS, $defaultItems);
    }

    /**
     * @inheritdoc
     */
    public function setCaption($caption): SliderInterface
    {
        return $this->setData(self::CAPTION, $caption);
    }

    /**
     * @inheritdoc
     */
    public function setStartDate($startDate): SliderInterface
    {
        return $this->setData(self::START_DATE, $startDate);
    }

    /**
     * @inheritdoc
     */
    public function setEndDate($endDate): SliderInterface
    {
        return $this->setData(self::END_DATE, $endDate);
    }

    /**
     * Retrieve array of banner id's for slider
     *
     * The array returned has the following format:
     * array($bannerId => $position)
     *
     * @return array
     */
    public function getBannersPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('banners_position');
        if ($array === null) {
            $array = $this->getResource()->getBannersPosition($this);
            $this->setData('banners_position', $array);
        }
        return $array;
    }

    /**
     * Retrieve array of banner id's for slider
     *
     * The array returned has the following format:
     * array($bannerId => $position)
     *
     * @return array
     */
    public function getCurrentSliderBannersPosition($slider)
    {
        if (!$slider->getSliderId()) {
            return [];
        }

        $array = $slider->getData('banners_position');
        if ($array === null) {
            $array = $this->getResource()->getBannersPosition($slider);
            $slider->setData('banners_position', $array);
        }
        return $array;
    }

    /**
     * Save array of banner id's for slider
     *
     * @param int $slider
     * @return null
     */
    public function saveSliderBanners($slider)
    {
        if (!$slider->getId()) {
            return null;
        }
        $updatedSlider = $this->getResource()->saveSliderBanners($slider);
        return $updatedSlider;
    }
}
