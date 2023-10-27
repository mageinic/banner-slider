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

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Option\ArrayInterface;

/**
 * Banner Targets
 */
class Targets implements ArrayInterface
{
    public const BANNER_TARGET_SAME_TAB = 0;
    public const BANNER_TARGET_NEW_TAB = 1;

    /**
     * @var ModuleManager
     */
    protected ModuleManager $moduleManager;

    /**
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ModuleManager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Return array of sliders
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if (!$this->moduleManager->isEnabled('MageINIC_BannerSlider')) {
            return [];
        }
        $targets = [
            [
                'value' => self::BANNER_TARGET_SAME_TAB,
                'label' => __('Same Tab')
            ],
            [
                'value' => self::BANNER_TARGET_NEW_TAB,
                'label' => __('New Tab')
            ]
        ];
        return $targets;
    }
}
