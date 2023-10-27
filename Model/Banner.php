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

use MageINIC\BannerSlider\Api\Data\BannerInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * MageINIC block model
 */
class Banner extends AbstractModel implements BannerInterface, IdentityInterface
{
    /**
     * MageINIC block cache tag
     */
    public const CACHE_TAG = 'mageinic_bannerslider';

    /**
     * Block's statuses
     */
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;
    public const BANNER_TARGET_SAME_TAB = 0;
    public const BANNER_TARGET_NEW_TAB = 1;
    public const MEDIA_IMAGE = 1;
    public const MEDIA_VIDEO = 2;

    /**
     * @var string
     */
    protected $cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $eventPrefix = 'mageinic_banner';

    /**
     * Banner Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Banner::class);
    }

    /**
     * Prevent blocks recursion
     *
     * @return void
     */
    public function beforeSave()
    {
        if ($this->hasDataChanges()) {
            $this->setUpdateTime(null);
        }
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
    }

    /**
     * Retrieve banner id
     *
     * @return int
     */
    public function getBannerId(): int
    {
        return $this->getData(self::BANNER_ID);
    }

    /**
     * Retrieve banner name
     *
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->getData(self::NAME);
    }

    /**
     * Retrieve slider id
     *
     * @return int
     */
    public function getSliderId(): string
    {
        return $this->getData(self::SLIDER_ID);
    }

    /**
     * Retrieve status
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Retrieve banner image
     *
     * @return string
     */
    public function getMedia(): string
    {
        return $this->getData(self::MEDIA);
    }

    /**
     * Retrieve banner image alt
     *
     * @return string
     */
    public function getMediaAlt():string
    {
        return $this->getData(self::MEDIA_ALT);
    }

    /**
     * Retrieve banner Media type
     *
     * @return array|mixed|string|null
     */
    public function getMediaType(): string
    {
        return $this->getData(self::MEDIA_TYPE);
    }

    /**
     * Retrieve banner image caption
     *
     * @return string
     */
    public function getCaption(): string
    {
        return (string)$this->getData(self::CAPTION);
    }

    /**
     * Retrieve banner caption animation
     *
     * @return string
     */
    public function getCaptionAnimation(): string
    {
        return (string)$this->getData(self::CAPTION_ANIMATION);
    }

    /**
     * Retrieve banner link
     *
     * @return string
     */
    public function getLink(): string
    {
        return (string)$this->getData(self::LINK);
    }

    /**
     * Retrieve banner target
     *
     * @return int
     */
    public function getTarget(): int
    {
        return $this->getData(self::TARGET);
    }

    /**
     * Retrieve block creation time
     *
     * @return string
     */
    public function getCreationTime(): string
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Retrieve block update time
     *
     * @return string
     */
    public function getUpdateTime(): string
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Set BannerID
     *
     * @param  int $bannerId
     * @return BannerInterface
     */
    public function setBannerId($bannerId): BannerInterface
    {
        return $this->setData(self::BANNER_ID, $bannerId);
    }

    /**
     * Set banner name
     *
     * @param  string $name
     * @return BannerInterface
     */
    public function setName($name): BannerInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set SliderId
     *
     * @param  int $sliderId
     * @return BannerInterface
     */
    public function setSliderId($sliderId): BannerInterface
    {
        return $this->setData(self::SLIDER_ID, $sliderId);
    }

    /**
     * Set status
     *
     * @param  int $status
     * @return BannerInterface
     */
    public function setStatus($status): BannerInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set image caption
     *
     * @param  string $caption
     * @return BannerInterface
     */
    public function setCaption($caption): BannerInterface
    {
        return $this->setData(self::CAPTION, $caption);
    }

    /**
     * Set image caption animation
     *
     * @param  string $captionAnimation
     * @return BannerInterface
     */
    public function setCaptionAnimation($captionAnimation): BannerInterface
    {
        return $this->setData(self::CAPTION_ANIMATION, $captionAnimation);
    }

    /**
     * Set image link
     *
     * @param  string $link
     * @return BannerInterface
     */
    public function setLink($link): BannerInterface
    {
        return $this->setData(self::LINK, $link);
    }

    /**
     * Set image target
     *
     * @param  int $target
     * @return BannerInterface
     */
    public function setTarget($target): BannerInterface
    {
        return $this->setData(self::TARGET, $target);
    }

    /**
     * Set creation time
     *
     * @param  string $creationTime
     * @return BannerInterface
     */
    public function setCreationTime($creationTime): BannerInterface
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set update time
     *
     * @param  string $updateTime
     * @return BannerInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Prepare banner's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }

    /**
     * Prepare banner's targets.
     *
     * @return array
     */
    public function getAvailableTargets()
    {
        return [
            self::BANNER_TARGET_SAME_TAB => __('Same Tab'),
            self::BANNER_TARGET_NEW_TAB => __('New Tab')
        ];
    }

    /**
     * Prepare media's statuses.
     *
     * @return array
     */
    public function getAvailableMedias()
    {
        return [
            self::MEDIA_IMAGE => __('Image'),
            self::MEDIA_VIDEO => __('Video')
        ];
    }

    /**
     * Save array of banner id's for slider
     *
     * @param int $banner
     * @return null
     */
    public function saveBannerToSlider($banner)
    {
        if (!$banner->getId()) {
            return null;
        }
        $updatedSlider = $this->getResource()->saveBannerToSlider($banner);

        return $updatedSlider;
    }

    /**
     * @param string $media
     * @return Banner
     */
    public function setMedia(string $media): BannerInterface
    {
        return $this->setData(self::MEDIA, $media);
    }

    /**
     * @param int $mediaType
     * @return Banner
     */
    public function setMediaType($mediaType): BannerInterface
    {
        return $this->setData(self::MEDIA_TYPE, $mediaType);
    }

    /**
     * @param string $mediaAlt
     * @return Banner
     */
    public function setMediaAlt($mediaAlt): BannerInterface
    {
        return $this->setData(self::MEDIA_ALT, $mediaAlt);
    }

    /**
     * @return string
     */
    public function getTextPosition(): string
    {
        return $this->getData(self::TEXT_POSITION);
    }

    /**
     * @param string $textPosition
     * @return Banner
     */
    public function setTextPosition(string $textPosition): BannerInterface
    {
        return $this->setData(self::TEXT_POSITION, $textPosition);
    }
}
