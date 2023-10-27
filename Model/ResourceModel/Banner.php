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

namespace MageINIC\BannerSlider\Model\ResourceModel;

use Exception;
use MageINIC\BannerSlider\Api\Data\BannerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * MageINIC block model
 */
class Banner extends AbstractDb
{
    public const STATUS_ENABLED = 1;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @var $_sliderBannerTable
     */
    protected $_sliderBannerTable;

    /**
     * @var EntityManager
     */
    protected EntityManager $entityManager;

    /**
     * @var MetadataPool
     */
    protected MetadataPool $metadataPool;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager,
        EntityManager         $entityManager,
        MetadataPool          $metadataPool,
        $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('indianic_bannerslider_banner', 'banner_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection(): AdapterInterface|bool
    {
        return $this->metadataPool->getMetadata(BannerInterface::class)->getEntityConnection();
    }

    /**
     * Get SliderIds
     *
     * @param mixed $banner
     * @return array
     */
    public function getSliderIds(mixed $banner)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from( $this->getSliderBannerTable(),'slider_id')
            ->where('banner_id = ?', $banner->getBannerId());
        return $connection->fetchCol($select);
    }

    /**
     * Category product table name getter
     *
     * @return string
     */
    public function getSliderBannerTable(): string
    {
        if (!$this->_sliderBannerTable) {
            $this->_sliderBannerTable = $this->getTable('indianic_slider_banner');
        }
        return $this->_sliderBannerTable;
    }

    /**
     * Get Banner Id
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param mixed|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws Exception
     */
    private function getBannerId(AbstractModel $object, $value, $field = null): bool|int|string
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        if (!is_numeric($value) && $field === null) {
            $field = 'banner_id';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     * @throws LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null): static
    {
        $bannerId = $this->getBannerId($object, $value, $field);
        if ($bannerId) {
            $this->entityManager->load($object, $bannerId);
        }
        return $this;
    }

    /**
     *  Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param AbstractModel $object
     * @return Select
     * @throws Exception
     */
    protected function _getLoadSelect($field, $value, $object): Select
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = parent::_getLoadSelect($field, $value, $object);
        return $select;
    }

    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @param AbstractModel $object
     * @return bool
     * @throws LocalizedException
     */
    public function getIsUniqueBlockToStores(AbstractModel $object): bool
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = $this->getConnection()->select()
            ->from(['cb' => $this->getMainTable()])
            ->where('cb.banner_id = ?', $object->getData('banner_id'));
        if ($object->getId()) {
            $select->where('cb.' . $entityMetadata->getIdentifierField() . ' <> ?', $object->getId());
        }
        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }
        return true;
    }

    /**
     * Save an object.
     *
     * @param AbstractModel $object
     * @return $this
     * @throws Exception
     */
    public function save(AbstractModel $object): static
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Delete
     *
     * @param AbstractModel $object
     * @return AbstractDb|Banner|$this
     * @throws Exception
     */
    public function delete(AbstractModel $object): AbstractDb|Banner|static
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Save Banner Data to Slider
     *
     * @param mixed $slider
     * @return $this
     */
    public function saveBannerToSlider($slider): static
    {
        $slider->setIsChangedBannerList(false);
        $id = $slider->getSliderId();
        $banners = $slider->getPostedBanners();

        if ($banners === null) {
            return $this;
        }
        $oldBanners = $slider->getBannersPosition();
        $insert = array_diff_key($banners, $oldBanners);
        $delete = array_diff_key($oldBanners, $banners);
        $update = array_intersect_key($banners, $oldBanners);
        $update = array_diff_assoc($update, $oldBanners);
        $connection = $this->getConnection();
        if (!empty($delete)) {
            $cond = ['banner_id IN(?)' => array_keys($delete), 'slider_id=?' => $id];
            $connection->delete($this->getSliderBannerTable(), $cond);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $bannerId => $position) {
                $data[] = [
                    'slider_id' => (int)$id,
                    'banner_id' => (int)$bannerId,
                    'position' => (int)$position,
                ];
            }
            $connection->insertMultiple($this->getSliderBannerTable(), $data);
        }

        if (!empty($update)) {
            foreach ($update as $bannerId => $position) {
                $where = ['slider_id = ?' => (int)$id, 'banner_id = ?' => (int)$bannerId];
                $bind = ['position' => (int)$position];
                $connection->update($this->getSliderBannerTable(), $bind, $where);
            }
        }
        if (!empty($insert) || !empty($delete)) {
            $bannerIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $slider->setIsChangedBannerList(true);
            $bannerIds = array_keys($insert + $delete + $update);
            $slider->setAffectedProductIds($bannerIds);
        }
        return $this;
    }
}
