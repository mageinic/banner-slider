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

namespace MageINIC\BannerSlider\Model\ResourceModel\Banner;

use MageINIC\BannerSlider\Model\ResourceModel\AbstractCollection;
use MageINIC\BannerSlider\Model\ResourceModel\Banner;

/**
 * MageINIC Banner Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'banner_id';

    /**
     * @var string
     */
    protected $eventPrefix = 'mageinic_banner_collection';

    /**
     * @var string
     */
    protected $eventObject = 'baner_collection';

    /**
     * Returns pairs banner_id - title
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->_toOptionArray('banner_id', 'name');
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(\MageINIC\BannerSlider\Model\Banner::class, Banner::class);
        $this->_map['fields']['banner_id'] = 'main_table.banner_id';
    }
}
