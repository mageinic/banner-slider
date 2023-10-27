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

use MageINIC\BannerSlider\Api\GetUtilitySliderIdentifiersInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Utility MageINIC BannerSlider Sliders
 */
class GetUtilitySliderIdentifiers implements GetUtilitySliderIdentifiersInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get List Page Identifiers
     *
     * @return array
     */
    public function execute()
    {
        $homePageIdentifier = $this->scopeConfig->getValue(
            'web/default/cms_home_page',
            ScopeInterface::SCOPE_STORE
        );
        $noRouteIdentifier  = $this->scopeConfig->getValue(
            'web/default/cms_no_route',
            ScopeInterface::SCOPE_STORE
        );
        $noCookieIdentifier = $this->scopeConfig->getValue(
            'web/default/cms_no_cookies',
            ScopeInterface::SCOPE_STORE
        );
        return [$homePageIdentifier, $noRouteIdentifier, $noCookieIdentifier];
    }
}
