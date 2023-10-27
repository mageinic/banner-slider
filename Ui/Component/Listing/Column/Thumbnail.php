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

namespace MageINIC\BannerSlider\Ui\Component\Listing\Column;

use Magento\Catalog\Helper\Image;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * BannerSlider Thumbnail Class
 */
class Thumbnail extends Column
{
    public const NAME = 'image';
    public const ALT_FIELD = 'name';

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var Image
     */
    private Image $imageHelper;

    /**
     * @var Repository
     */
    private Repository $assetRepo;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * Thumbnail Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Image $imageHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param Repository $assetRepo
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface      $context,
        UiComponentFactory    $uiComponentFactory,
        Image                 $imageHelper,
        ScopeConfigInterface  $scopeConfig,
        UrlInterface          $urlBuilder,
        StoreManagerInterface $storeManager,
        Repository            $assetRepo,
        array                 $components = [],
        array                 $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->assetRepo = $assetRepo;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $storeScope = ScopeInterface::SCOPE_STORE;
            foreach ($dataSource['data']['items'] as & $item) {
                $mediaKey = array_search('media', array_keys($item));
                $url = '';
                if ($item[$fieldName] != '') {
                    if ($mediaKey== '6' && $item['media_type'] == '1') {
                        $url = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                            . 'MageINIC/bannerslider/' . $item[$fieldName];
                    } elseif ($item['media_type'] == '2') {
                        $url = $this->getAssetRepo();
                    }
                } else {
                        $url = $this->imageHelper->getDefaultPlaceholderUrl('image');
                }
                $item[$fieldName . '_src'] = $url;
                $item[$fieldName . '_alt'] = $this->getAlt($item) ?: '';
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'bannerslider/banner/edit',
                    ['banner_id' => $item['banner_id']]
                );
                $item[$fieldName . '_orig_src'] = $url;
            }
        }
        return $dataSource;
    }

    /**
     * Get Alt
     *
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row): ?string
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return $row[$altField] ?? null;
    }

    /**
     * Get Asset
     *
     * @return string
     */
    public function getAssetRepo(): string
    {
        return $this->assetRepo->getUrl("MageINIC_BannerSlider::images/Video.jpeg");
    }
}
