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

use MageINIC\BannerSlider\Api\Data;
use MageINIC\BannerSlider\Api\BannerRepositoryInterface;
use MageINIC\BannerSlider\Api\Data\BannerSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageINIC\BannerSlider\Api\Data\BannerInterfaceFactory;
use MageINIC\BannerSlider\Model\ResourceModel\Banner as ResourceBanner;
use MageINIC\BannerSlider\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * BannerSlider Class BannerRepository
 */
class BannerRepository implements BannerRepositoryInterface
{
    /**
     * @var ResourceBanner
     */
    protected ResourceBanner $resource;

    /**
     * @var BannerFactory
     */
    protected BannerFactory $bannerFactory;

    /**
     * @var BannerCollectionFactory
     */
    protected BannerCollectionFactory $bannerCollectionFactory;

    /**
     * @var Data\BannerSearchResultsInterfaceFactory
     */
    protected BannerSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * @var BannerInterfaceFactory
     */
    protected BannerInterfaceFactory $dataBannerFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CollectionProcessorInterface|null
     */
    private $collectionProcessor;

    /**
     * @param ResourceBanner $resource
     * @param BannerFactory $bannerFactory
     * @param BannerInterfaceFactory $dataBannerFactory
     * @param BannerCollectionFactory $bannerCollectionFactory
     * @param BannerSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceBanner $resource,
        BannerFactory $bannerFactory,
        BannerInterfaceFactory $dataBannerFactory,
        BannerCollectionFactory $bannerCollectionFactory,
        Data\BannerSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->bannerFactory = $bannerFactory;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBannerFactory = $dataBannerFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(Data\BannerInterface $banner): Data\BannerInterface
    {
        $storeId = $this->storeManager->getStore()->getId();
        $banner->setStoreId($storeId);
        try {
            $this->resource->save($banner);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $banner;
    }

    /**
     * @inheritdoc
     */
    public function getById($bannerId): Data\BannerInterface
    {
        $banner = $this->bannerFactory->create();
        $this->resource->load($banner, $bannerId);
        if (!$banner->getId()) {
            throw new NoSuchEntityException(__('Banner with id "%1" does not exist.', $bannerId));
        }

        return $banner;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->bannerCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(Data\BannerInterface $banner): bool
    {
        try {
            $this->resource->delete($banner);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($bannerId): bool
    {
        return $this->delete($this->getById($bannerId));
    }
}
