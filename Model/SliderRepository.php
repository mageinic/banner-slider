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
use MageINIC\BannerSlider\Api\Data\SliderInterface;
use MageINIC\BannerSlider\Api\Data\SliderInterfaceFactory;
use MageINIC\BannerSlider\Api\Data\SliderSearchResultsInterfaceFactory;
use MageINIC\BannerSlider\Api\SliderRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageINIC\BannerSlider\Model\ResourceModel\Slider as ResourceSlider;
use MageINIC\BannerSlider\Model\ResourceModel\Slider\CollectionFactory as SliderCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * BannerSlider Class SliderRepository
 */
class SliderRepository implements SliderRepositoryInterface
{
    /**
     * @var ResourceSlider
     */
    protected ResourceSlider $resource;

    /**
     * @var SliderFactory
     */
    protected SliderFactory $sliderFactory;

    /**
     * @var SliderCollectionFactory
     */
    protected SliderCollectionFactory $sliderCollectionFactory;

    /**
     * @var Data\SliderSearchResultsInterfaceFactory
     */
    protected SliderSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * @var SliderInterfaceFactory
     */
    protected SliderInterfaceFactory $dataSliderFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CollectionProcessorInterface|null
     */
    private $collectionProcessor;

    /**
     * @param ResourceSlider                      $resource
     * @param SliderFactory                       $sliderFactory
     * @param SliderInterfaceFactory              $dataSliderFactory
     * @param SliderCollectionFactory             $sliderCollectionFactory
     * @param SliderSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                    $dataObjectHelper
     * @param DataObjectProcessor                 $dataObjectProcessor
     * @param StoreManagerInterface               $storeManager
     * @param CollectionProcessorInterface        $collectionProcessor
     */
    public function __construct(
        ResourceSlider                           $resource,
        SliderFactory                            $sliderFactory,
        Data\SliderInterfaceFactory              $dataSliderFactory,
        SliderCollectionFactory                  $sliderCollectionFactory,
        Data\SliderSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper                         $dataObjectHelper,
        DataObjectProcessor                      $dataObjectProcessor,
        StoreManagerInterface                    $storeManager,
        CollectionProcessorInterface             $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->sliderFactory = $sliderFactory;
        $this->sliderCollectionFactory = $sliderCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSliderFactory = $dataSliderFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(SliderInterface $slider): Data\SliderInterface
    {
        if (empty($slider->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $slider->setStoreId($storeId);
        }
        try {
            $this->resource->save($slider);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the slider: %1',
                    $exception->getMessage()
                )
            );
        }
        return $slider;
    }

    /**
     * @inheritdoc
     */
    public function getById($sliderId): Data\SliderInterface
    {
        $slider = $this->sliderFactory->create();
        $slider->load($sliderId);
        if (!$slider->getId()) {
            throw new NoSuchEntityException(__('Slider with id "%1" does not exist.', $sliderId));
        }
        return $slider;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->sliderCollectionFactory->create();
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
    public function delete(SliderInterface $slider): bool
    {
        try {
            $this->resource->delete($slider);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the slider: %1',
                    $exception->getMessage()
                )
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($sliderId): bool
    {
        return $this->delete($this->getById($sliderId));
    }
}
