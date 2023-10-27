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

namespace MageINIC\BannerSlider\Block\System\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use MageINIC\BannerSlider\Block\System\Config\Field\DynamicColumn;

/**
 * class for DynamicRow
 */
class DynamicRow extends AbstractFieldArray
{
    /**
     * @var DynamicColumn|null
     */
    private ?DynamicColumn $columnRenderer = null;

    /**
     * Prepare rendering the new field by adding all the needed columns
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'breakpoint',
            [
                'label' => __('Breakpoint'),
                'class' => 'required-entry validate-digits'
            ]
        );
        $this->addColumn(
            'slides_to_show',
            [
                'label' => __('Slides to Show'),
                'class' => 'required-entry validate-digits'
            ]
        );
        $this->addColumn(
            'slides_to_scroll',
            [
                'label' => __('Slides to Scroll'),
                'class' => 'required-entry validate-digits'
            ]
        );
        $this->addColumn(
            'dots',
            [
                'label' => __('Dots'),
                'renderer' => $this->getColumnRenderer()
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Row');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $dynamicColumn = $row->getTemplete();
        if ($dynamicColumn !== null) {
            $options['option_' . $this->getColumnRenderer()->calcOptionHash($dynamicColumn)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Get Column Renderer.
     *
     * @return DynamicColumn
     * @throws LocalizedException
     */
    private function getColumnRenderer(): DynamicColumn
    {
        if (!$this->columnRenderer) {
            $this->columnRenderer = $this->getLayout()
                ->createBlock(DynamicColumn::class, '', ['data' => ['is_render_to_js_template' => true]]);
            $this->columnRenderer->setClass('required-entry');
        }
        return $this->columnRenderer;
    }
}
