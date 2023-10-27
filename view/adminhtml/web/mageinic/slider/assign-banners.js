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

define(
    [
    'mage/adminhtml/grid'
    ], function () {
        'use strict';

        return function (config) {
            var selectedBanners = config.selectedBanners,
            sliderBanners = $H(selectedBanners),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

            $('in_slider_banners').value = Object.toJSON(sliderBanners);

            /**
             * Register Category Product
             *
             * @param {Object} grid
             * @param {Object} element
             * @param {Boolean} checked
             */
            function registerSliderBanner(grid, element, checked)
            {
                if (checked) {
                    if (element.positionElement) {
                        element.positionElement.disabled = false;
                        sliderBanners.set(element.value, element.positionElement.value);
                    }
                } else {
                    if (element.positionElement) {
                        element.positionElement.disabled = true;
                    }
                    sliderBanners.unset(element.value);
                }
                $('in_slider_banners').value = Object.toJSON(sliderBanners);
                grid.reloadParams = {
                    'selected_banners[]': sliderBanners.keys()
                };
            }

            /**
             * Click on product row
             *
             * @param {Object} grid
             * @param {String} event
             */
            function sliderBannerRowClick(grid, event)
            {
                var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

                if (trElement) {
                    checkbox = Element.getElementsBySelector(trElement, 'input');

                    if (checkbox[0]) {
                        checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                        gridJsObject.setCheckboxChecked(checkbox[0], checked);
                    }
                }
            }

            /**
             * Change product position
             *
             * @param {String} event
             */
            function positionChange(event)
            {
                var element = Event.element(event);

                if (element && element.checkboxElement && element.checkboxElement.checked) {
                    sliderBanners.set(element.checkboxElement.value, element.value);
                    $('in_slider_banners').value = Object.toJSON(sliderBanners);
                }
            }

            /**
             * Initialize category product row
             *
             * @param {Object} grid
             * @param {String} row
             */
            function sliderBannerRowInit(grid, row)
            {
                var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

                if (checkbox && position) {
                    checkbox.positionElement = position;
                    position.checkboxElement = checkbox;
                    position.disabled = !checkbox.checked;
                    position.tabIndex = tabIndex++;
                    Event.observe(position, 'keyup', positionChange);
                }
            }

            gridJsObject.rowClickCallback = sliderBannerRowClick;
            gridJsObject.initRowCallback = sliderBannerRowInit;
            gridJsObject.checkboxCheckCallback = registerSliderBanner;

            if (gridJsObject.rows) {
                gridJsObject.rows.each(
                    function (row) {
                        sliderBannerRowInit(gridJsObject, row);
                    }
                );
            }
        };
    }
);
