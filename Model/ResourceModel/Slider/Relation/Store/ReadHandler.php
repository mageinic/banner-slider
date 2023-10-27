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

namespace MageINIC\BannerSlider\Model\ResourceModel\Slider\Relation\Store;

use MageINIC\BannerSlider\Model\ResourceModel\Slider;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * BannerSlider Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected MetadataPool $metadataPool;

    /**
     * @var Slider
     */
    protected Slider $resourceSlider;

    /**
     * @param MetadataPool $metadataPool
     * @param Slider $resourceSlider
     */
    public function __construct(
        MetadataPool $metadataPool,
        Slider       $resourceSlider
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceSlider = $resourceSlider;
    }

    /**
     * Execute action
     *
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws LocalizedException
     */
    public function execute($entity, $arguments = []): object
    {
        if ($entity->getId()) {
            $stores = $this->resourceSlider->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
        }
        return $entity;
    }
}
