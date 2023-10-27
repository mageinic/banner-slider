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
use MageINIC\BannerSlider\Api\Data\SliderInterface;
use MageINIC\BannerSlider\Model\BannerFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Slider Model
 */
class Slider extends AbstractDb
{
    /**
     * @var null
     */
    protected $_store = null;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @var DateTime
     */
    protected DateTime $dateTime;

    /**
     * @var EntityManager
     */
    protected EntityManager $entityManager;

    /**
     * @var MetadataPool
     */
    protected MetadataPool $metadataPool;

    /**
     * @var string
     */
    protected $_sliderBannerTable;

    /**
     * @var Manager
     */
    private Manager $_eventManager;

    /**
     * @var BannerFactory
     */
    private BannerFactory $bannerFactory;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param Manager $eventManager
     * @param BannerFactory $bannerFactory
     * @param string $connectionName
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager,
        DateTime              $dateTime,
        EntityManager         $entityManager,
        MetadataPool          $metadataPool,
        Manager               $eventManager,
        BannerFactory         $bannerFactory,
        $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->_eventManager = $eventManager;
        $this->bannerFactory = $bannerFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * Check if page identifier exist for specific store, return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return string
     * @throws LocalizedException
     */
    public function checkIdentifier(string $identifier, int $storeId): string
    {
        $entityMetadata = $this->metadataPool->getMetadata(SliderInterface::class);

        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Select::COLUMNS)
            ->columns('ms.' . $entityMetadata->getIdentifierField())
            ->order('mss.store_id DESC')
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param array|int $store
     * @param int|null $isActive
     * @return Select
     */
    protected function _getLoadByIdentifierSelect(string $identifier, array|int $store, int $isActive = null): Select
    {
        $entityMetadata = $this->metadataPool->getMetadata(SliderInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $this->getConnection()->select()
            ->from(['ms' => $this->getMainTable()])
            ->join(['mss' => $this->getTable('indainic_bannerslider_slider_store')],
                'ms.' . $linkField . ' = cps.' . $linkField,[])
            ->where('ms.slider_id = ?', $identifier)
            ->where('mss.store_id IN (?)', $store);
        if ($isActive !== null) {
            $select->where('ms.status = ?', $isActive);
        }
        return $select;
    }

    /**
     * Get Connection
     *
     * @return AdapterInterface|bool
     * @throws Exception
     */
    public function getConnection(): AdapterInterface|bool
    {
        return $this->metadataPool->getMetadata(SliderInterface::class)->getEntityConnection();
    }

    /**
     * Get Store
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore(): StoreInterface
    {
        return $this->_storeManager->getStore($this->_store);
    }

    /**
     * @inheritDoc
     */
    public function setStore($store): static
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $sliderId
     * @return array
     * @throws LocalizedException
     */
    public function lookupStoreIds(int $sliderId): array
    {
        $connection = $this->getConnection();
        $entityMetadata = $this->metadataPool->getMetadata(SliderInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = $connection->select()
            ->from(['mss' => $this->getTable('indainic_bannerslider_slider_store')], 'store_id')
            ->join(['ms' => $this->getMainTable()], 'mss.' . $linkField . ' = ms.' . $linkField, [])
            ->where('ms.' . $entityMetadata->getIdentifierField() . ' = :slider_id');

        return $connection->fetchCol($select, ['slider_id' => (int)$sliderId]);
    }

    /**
     * Save Slider Banners
     *
     * @param mixed $slider
     * @return $this
     * @throws Exception
     */
    public function saveSliderBanners(mixed $slider): static
    {
        $slider->setIsChangedBannerList(false);
        $id = $slider->getSliderId();
        $banners = $slider->getPostedBanners();
        if ($banners === null) {
            return $this;
        }
        if (count($slider->getBannersPosition()) > 0) {
            $oldBanners = $slider->getBannersPosition();
        } else {
            $oldBanners = [];
        }
        $insert = array_diff_key($banners, $oldBanners);
        $delete = array_diff_key($oldBanners, $banners);
        $update = array_intersect_key($banners, $oldBanners);
        $update = array_diff_assoc($update, $oldBanners);
        $connection = $this->getConnection();
        if (!empty($delete)) {
            $cond = ['banner_id IN(?)' => array_keys($delete), 'slider_id=?' => $id];
            $connection->delete($this->getSliderBannerTable(), $cond);
            foreach ($delete as $bannerId => $position) {
                $_banner = $this->bannerFactory->create()->load($bannerId);
                $sliders = explode(',', $_banner->getSliderId());
                $updatedSliders = $this->deleteElement($id, $sliders);
                $_banner->setSliderId(implode(',', $updatedSliders))->save();
            }
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $bannerId => $position) {
                $data[] = [
                    'slider_id' => (int)$id,
                    'banner_id' => (int)$bannerId,
                    'position' => (int)$position,
                ];
                $_banner = $this->bannerFactory->create()->load($bannerId);
                $sliders = explode(',', $_banner->getSliderId());
                if (!in_array($id, $sliders)) {
                    array_push($sliders, $id);
                    $_banner->setSliderId(implode(',', $sliders))->save();
                }
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
            $this->_eventManager->dispatch(
                'mageinic_slider_change_banners',
                ['slider' => $slider, 'banner_ids' => $bannerIds]
            );
            $slider->setChangedBannerIds($bannerIds);
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $slider->setIsChangedBannerList(true);
            $bannerIds = array_keys($insert + $delete + $update);
            $slider->setAffectedBannerIds($bannerIds);
        }
        return $this;
    }

    /**
     * Get Slider identifier
     *
     * @param AbstractModel $object
     * @param string $value
     * @param string|null $field
     * @return false|float|int|mixed|string
     * @throws LocalizedException
     */
    private function getSliderId(AbstractModel $object, $value, $field = null): mixed
    {
        $entityMetadata = $this->metadataPool->getMetadata(SliderInterface::class);
        if (!is_numeric($value) && $field === null)
        {
            $field = 'slider_id';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $sliderId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId())
        {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $sliderId = count($result) ? $result[0] : false;
        }
        return $sliderId;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param AbstractModel $object
     * @return Select
     * @throws LocalizedException
     */
    protected function _getLoadSelect($field, $value, $object): Select
    {
        $entityMetadata = $this->metadataPool->getMetadata(SliderInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId())
        {
            $storeIds = [
                Store::DEFAULT_STORE_ID,
                (int)$object->getStoreId(),
            ];
            $select->join([
                'indainic_bannerslider_slider_store' => $this->getTable('indainic_bannerslider_slider_store')
            ], $this->getMainTable() . '.' . $linkField . ' = indainic_bannerslider_slider_store.' . $linkField, [])
                ->where('status = ?', 1)
                ->where('indainic_bannerslider_slider_store.store_id IN (?)', $storeIds)
                ->order('indainic_bannerslider_slider_store.store_id DESC')
                ->limit(1);
        }
        return $select;
    }

    /**
     * Get Banner Position
     *
     * @param mixed $slider
     * @return array
     * @throws Exception
     */
    public function getBannersPosition(mixed $slider): array
    {
        $select = $this->getConnection()->select()->from( $this->getSliderBannerTable(), ['banner_id', 'position'])
            ->where('slider_id = :slider_id');
        $bind = ['slider_id' => (int)$slider->getId()];
        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * Slider product table name getter
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
     * Delete
     *
     * @param AbstractModel $object
     * @return AbstractDb|Slider|$this
     * @throws Exception
     */
    public function delete(AbstractModel $object): AbstractDb|Slider|static
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this|Slider
     * @throws LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null): Slider|static
    {
        $sliderId = $this->getSliderId($object, $value, $field);
        if ($sliderId) {
            $this->entityManager->load($object, $sliderId);
        }
        return $this;
    }

    /**
     * Delete Element
     *
     * @param mixed $element
     * @param mixed $array
     * @return mixed
     */
    public function deleteElement(mixed $element, mixed $array): mixed
    {
        $index = array_search($element, $array);
        if ($index !== false) {
            unset($array[$index]);
        }
        return $array;
    }

    /**
     * Save
     *
     * @param AbstractModel $object
     * @return AbstractDb|Slider|$this
     * @throws Exception
     */
    public function save(AbstractModel $object): AbstractDb|Slider|static
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('indianic_bannerslider_slider', 'slider_id');
    }

    /**
     *  Check whether page identifier is numeric
     *
     * @param  AbstractModel $object
     * @return bool
     */
    protected function isNumericPageIdentifier(AbstractModel $object): bool
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether page identifier is valid
     *
     * @param  AbstractModel $object
     * @return bool
     */
    protected function isValidPageIdentifier(AbstractModel $object): bool
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }
}
