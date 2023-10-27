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

namespace MageINIC\BannerSlider\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Banner Caption Position
 */
class CaptionPosition implements ArrayInterface
{
    public const BANNER_POSITION_DEFAULT = 'default';
    public const BANNER_POSITION_MIDDLE_LEFT = 'middle-left';
    public const BANNER_POSITION_MIDDLE_CENTER = 'middle-center';
    public const BANNER_POSITION_MIDDLE_RIGHT = 'middle-right';

    /**
     * Return array of sliders
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $targets = [
            [
                'value' => self::BANNER_POSITION_DEFAULT,
                'label' => __('Default')
            ],
            [
                'value' => self::BANNER_POSITION_MIDDLE_LEFT,
                'label' => __('Left')
            ],
            [
                'value' => self::BANNER_POSITION_MIDDLE_CENTER,
                'label' => __('Center')
            ],
            [
                'value' => self::BANNER_POSITION_MIDDLE_RIGHT,
                'label' => __('Right')
            ]
        ];

        return $targets;
    }
}
