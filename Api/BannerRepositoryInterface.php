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

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * MageINIC Banner interface.
 */
interface BannerRepositoryInterface
{
    /**
     * Save Banner.
     *
     * @param  \MageINIC\BannerSlider\Api\Data\BannerInterface $banner
     * @return \MageINIC\BannerSlider\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\BannerInterface $banner): Data\BannerInterface;

    /**
     * Retrieve banner.
     *
     * @param  int $bannerId
     * @return \MageINIC\BannerSlider\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($bannerId): Data\BannerInterface;

    /**
     * Retrieve banners matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \MageINIC\BannerSlider\Api\Data\BannerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete banner.
     *
     * @param  \MageINIC\BannerSlider\Api\Data\BannerInterface $banner
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\BannerInterface $banner): bool;

    /**
     * Delete banner by ID.
     *
     * @param  int $bannerId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($bannerId): bool;
}
