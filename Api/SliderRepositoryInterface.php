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

namespace MageINIC\BannerSlider\Api;

use MageINIC\BannerSlider\Api\Data\SliderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Banner Slider Repository Interface
 */
interface SliderRepositoryInterface
{
    /**
     * Save slider.
     *
     * @param  \MageINIC\BannerSlider\Api\Data\SliderInterface $slider
     * @return \MageINIC\BannerSlider\Api\Data\SliderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SliderInterface $slider): Data\SliderInterface;

    /**
     * Retrieve slider.
     *
     * @param  string $sliderId
     * @return \MageINIC\BannerSlider\Api\Data\SliderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($sliderId): Data\SliderInterface;

    /**
     * Retrieve slider matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     * @return \MageINIC\BannerSlider\Api\Data\SliderSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete slider.
     *
     * @param  SliderInterface $slider
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(SliderInterface $slider): bool;

    /**
     * Delete slider by ID.
     *
     * @param  int $sliderId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($sliderId): bool;
}
