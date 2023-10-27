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

namespace MageINIC\BannerSlider\Block\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Color For Widget
 */
class Color extends Template implements BlockInterface
{
    /**
     * Function prepareElementHtml
     *
     * @param AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $defaultColor = "#ffffff";
        $value = $element->getValue() ?: $defaultColor;
        $element->setData('after_element_html', '
                <input type="text"
                    value="' . $value . '"
                    id="' . $element->getHtmlId() . '"
                    name="' . $element->getName() . '"
                >
                <script type="text/javascript">
                require(["jquery", "jquery/colorpicker/js/colorpicker"], function ($) {
                    $currentElement = $("#' . $element->getHtmlId() . '");
                    $currentElement.css("backgroundColor", "'. $value .'");
                    $currentElement.ColorPicker({
                        color: "' . $value . '",
                        onChange: function (hsb, hex, rgb) {
                            $currentElement.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
                });
                </script><style>.colorpicker {z-index: 10010}</style>');
        $element->setValue(null);
        return $element;
    }
}
