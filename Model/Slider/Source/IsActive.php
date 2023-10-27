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

namespace MageINIC\BannerSlider\Model\Slider\Source;

use Magento\Framework\Data\OptionSourceInterface;
use MageINIC\BannerSlider\Model\Slider;

/**
 * BannerSlider Class IsActive
 */
class IsActive implements OptionSourceInterface
{
    /**
     * @var Slider
     */
    protected Slider $sliderModel;

    /**
     * @param Slider $sliderModel
     */
    public function __construct(
        Slider $sliderModel
    ) {
        $this->sliderModel = $sliderModel;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $availableOptions = $this->sliderModel->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = ['label' => $value, 'value' => $key];
        }
        return $options;
    }
}
