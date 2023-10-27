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
 * MageINIC slider interface.
 */
interface SliderInterface
{
    public const SLIDER_ID = 'slider_id';
    public const TITLE = 'title';
    public const SLIDER_POSITION = 'slider_position';
    public const STATUS = 'status';
    public const SORT_TYPE = 'sort_type';
    public const DEFAULT_ITEMS = 'default_items';
    public const CAPTION = 'caption';
    public const START_DATE = 'start_date';
    public const END_DATE = 'end_date';

    /**
     * Get slider ID
     *
     * @return int|null
     */
    public function getSliderId(): ?int;

    /**
     * Get slider title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get slider position
     *
     * @return string
     */
    public function getSliderPosition(): string;

    /**
     * Get status
     *
     * @return int|null
     */
    public function getStatus(): ?int;

    /**
     * Get sort type
     *
     * @return int|null
     */
    public function getSortType(): ?int;

    /**
     * Get slider default items
     *
     * @return int|null
     */
    public function getDefaultItems(): ?int;

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption(): string;

    /**
     * Get start date
     *
     * @return string|null
     */
    public function getStartDate(): ?string;

    /**
     * Get end date
     *
     * @return string|null
     */
    public function getEndDate(): ?string;

    /**
     * Set SldierID
     *
     * @param int $sliderId
     * @return $this
     */
    public function setSliderId($sliderId): SliderInterface;

    /**
     * Set slider title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title): SliderInterface;

    /**
     * Set Slider Position
     *
     * @param string $sliderPosition
     * @return $this
     */
    public function setSliderPosition($sliderPosition): SliderInterface;

    /**
     * Set Status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status): SliderInterface;

    /**
     * Set SortType
     *
     * @param int $sortType
     * @return $this
     */
    public function setSortType($sortType): SliderInterface;

    /**
     * Set Default Items
     *
     * @param int $defaultItems
     * @return $this
     */
    public function setDefaultItems($defaultItems): SliderInterface;

    /**
     * Set slider caption
     *
     * @param string $caption
     * @return $this
     */
    public function setCaption($caption): SliderInterface;

    /**
     * Set start date
     *
     * @param string $startDate
     * @return $this
     */
    public function setStartDate($startDate): SliderInterface;

    /**
     * Set end date
     *
     * @param string $endDate
     * @return $this
     */
    public function setEndDate($endDate): SliderInterface;
}
