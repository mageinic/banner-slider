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

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as CollectionAbstractCollection;

/**
 * Abstract collection of MageINIC pages and blocks
 */
abstract class AbstractCollection extends CollectionAbstractCollection
{
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var MetadataPool
     */
    protected MetadataPool $metadataPool;

    /**
     * Joined fields data
     *
     * @var array
     */
    protected array $_joinFields = [];

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface        $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface       $eventManager
     * @param StoreManagerInterface  $storeManager
     * @param MetadataPool           $metadataPool
     * @param AdapterInterface|null  $connection
     * @param AbstractDb|null        $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface        $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface       $eventManager,
        StoreManagerInterface  $storeManager,
        MetadataPool           $metadataPool,
        AdapterInterface       $connection = null,
        AbstractDb             $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->metadataPool = $metadataPool;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Join regular table field and use an attribute as fk
     *
     * @param string $alias
     * @param string $table
     * @param string $field
     * @param string $bind
     * @param string|array $cond
     * @param string $joinType
     * @return $this
     * @throws LocalizedException
     */
    public function joinField($alias, $table, $field, $bind, $cond = null, $joinType = 'inner')
    {
        if (isset($this->_joinFields[$alias])) {
            throw new LocalizedException(__('A joined field with this alias is already declared.'));
        }
        $connection = $this->getConnection();
        $table = $this->getTable($table);
        $tableAlias = $alias;

        list($pKey, $fKey) = explode('=', $bind);
        $pKey = $this->getSelect()->getConnection()->quoteColumnAs(trim($pKey), null);
        $bindCond = $tableAlias . '.' . trim($pKey) . '=' . trim($fKey);

        switch ($joinType) {
            case 'left':
                $joinMethod = 'joinLeft';
                break;
            default:
                $joinMethod = 'join';
                break;
        }
        $condArr = [$bindCond];

        if ($cond !== null) {
            if (is_array($cond)) {
                foreach ($cond as $key => $value) {
                    $condArr[] = $this->_getConditionSql($tableAlias . '.' . $key, $value);
                }
            } else {
                $condArr[] = str_replace('{{table}}', $tableAlias, $cond);
            }
        }
        $cond = '(' . implode(') AND (', $condArr) . ')';

        $this->getSelect()->{$joinMethod}(
            [$tableAlias => $table],
            $cond,
            $field ? [$alias => $field] : []
        );

        $this->_joinFields[$alias] = ['table' => $tableAlias, 'field' => $field];

        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @param  array|string          $field
     * @param  string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return Select
     */
    public function getSelectCountSql(): Select
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Select::GROUP);

        return $countSelect;
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $linkField
     * @return void
     * @throws NoSuchEntityException
     */
    protected function performAfterLoad(string $tableName, string $linkField): void
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['cms_entity_store' => $this->getTable($tableName)])
                ->where('cms_entity_store.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $storesData[$linkedId], true);
                    if ($storeIdKey !== false) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
    }

    /**
     * Perform adding filter by store
     *
     * @param array|int|Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter(Store|array|int $store, bool $withAdmin = true): void
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $store], 'public');
    }

    /**
     * Join store relation table if there is store filter
     *
     * @param  string      $tableName
     * @param  string|null $linkField
     * @return void
     */
    protected function joinStoreRelationTable($tableName, $linkField): void
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = store_table.' . $linkField,
                []
            )->group('main_table.' . $linkField);
        }
        parent::_renderFiltersBefore();
    }
}
