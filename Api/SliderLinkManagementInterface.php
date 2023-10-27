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

/**
 * Banner Slider Link Management Interface
 */
interface SliderLinkManagementInterface
{
    /**
     * Get products assigned to category
     *
     * @param  int $sliderId
     * @return \MageINIC\BannerSlider\Api\Data\SliderBannerLinkInterface[]
     */
    public function getAssignedBanners($sliderId);

    /**
     * Assign banner to given sliders
     *
     * @param  string $bannerId
     * @param  int[]  $sliderIds
     * @return bool
     */
    public function assignBannerToSliders($bannerId, array $sliderIds);
}
