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

use MageINIC\BannerSlider\Api\Data\SliderBannerLinkInterface;

/**
 * Banner Slider Link Repository Interface
 */
interface SliderLinkRepositoryInterface
{
    /**
     * LinkRepositoryInterface
     *
     * @param  SliderBannerLinkInterface $bannerLink
     * @return mixed
     */
    public function save(SliderBannerLinkInterface $bannerLink): mixed;

    /**
     * Remove the product assignment from the category
     *
     * @param  SliderBannerLinkInterface $bannerLink
     * @return bool
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(SliderBannerLinkInterface $bannerLink): bool;

    /**
     * Remove the product assignment from the category by category id and bannerId
     *
     * @param  int $sliderId
     * @param  int $bannerId
     * @return bool
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteByIds($sliderId, $bannerId): mixed;
}
