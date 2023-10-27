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

namespace MageINIC\BannerSlider\Api\Data;

/**
 * MageINIC BannerSlider interface.
 */
interface BannerInterface
{
    public const BANNER_ID = 'banner_id';
    public const NAME = 'name';
    public const SLIDER_ID = 'slider_id';
    public const STATUS = 'status';
    public const TEXT_POSITION = 'text_position';
    public const MEDIA_ALT = 'media_alt';
    public const MEDIA_TYPE = 'media_type';
    public const MEDIA = 'media';
    public const CAPTION = 'caption';
    public const CAPTION_ANIMATION = 'caption_animation';
    public const LINK = 'link';
    public const TARGET = 'target';
    public const CREATION_TIME = 'creation_time';
    public const UPDATE_TIME = 'update_time';

    /**
     * Get banner ID
     *
     * @return int|null
     */
    public function getBannerId(): ?int;

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get slider ID
     *
     * @return string|null
     */
    public function getSliderId(): string;

    /**
     * Get status
     *
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * Get media
     *
     * @return string
     */
    public function getMedia(): string;

    /**
     * Get Media alt
     *
     * @return string
     */
    public function getMediaAlt(): string;

    /**
     * Get media type
     *
     * @return string
     */
    public function getMediaType(): string;

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption(): string;

    /**
     * Get caption animation
     *
     * @return string
     */
    public function getCaptionAnimation(): string;

    /**
     * Get text Position
     *
     * @return string
     */
    public function getTextPosition(): string;

    /**
     * Get link
     *
     * @return string
     */
    public function getLink(): string;

    /**
     * Get image target
     *
     * @return int|null
     */
    public function getTarget(): ?int;

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime(): ?string;

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime(): ?string;

    /**
     * Set BannerID
     *
     * @param string $bannerId
     * @return $this
     */
    public function setBannerId(string $bannerId): BannerInterface;

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): BannerInterface;

    /**
     * Set SliderID
     *
     * @param string $sliderId
     * @return $this
     */
    public function setSliderId($sliderId): BannerInterface;

    /**
     * Set Status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): BannerInterface;

    /**
     * Set image
     *
     * @param string $media
     * @return $this
     */
    public function setMedia(string $media): BannerInterface;

    /**
     * Set media Type
     *
     * @param string $mediaType
     * @return $this
     */
    public function setMediaType(string $mediaType): BannerInterface;

    /**
     * Set image alt
     *
     * @param string $mediaAlt
     * @return $this
     */
    public function setMediaAlt(string $mediaAlt): BannerInterface;

    /**
     * Set image caption
     *
     * @param string $caption
     * @return $this
     */
    public function setCaption(string $caption): BannerInterface;

    /**
     * Set image caption animation
     *
     * @param string $captionAnimation
     * @return $this
     */
    public function setCaptionAnimation(string $captionAnimation): BannerInterface;

    /**
     * Set Text Position
     *
     * @param string $textPosition
     * @return $this
     */
    public function setTextPosition(string $textPosition): BannerInterface;

    /**
     * Set image link
     *
     * @param string $link
     * @return $this
     */
    public function setLink(string $link): BannerInterface;

    /**
     * Set image target
     *
     * @param int $target
     * @return $this
     */
    public function setTarget(int $target): BannerInterface;

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return $this
     */
    public function setCreationTime(string $creationTime): BannerInterface;

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return $this
     */
    public function setUpdateTime(string $updateTime);
}
