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

namespace MageINIC\BannerSlider\Ui\Component;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Ui\Component\Container;

/**
 * DataProvider for Banner Slider ui.
 */
class DataProvider extends UiDataProvider
{
    /**
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $authorization;

    /**
     * @var array
     */
    private $additionalFilterPool;

    /**
     * DataProvider Constructor.
     *
     * @param string                 $name
     * @param string                 $primaryFieldName
     * @param string                 $requestFieldName
     * @param Reporting              $reporting
     * @param SearchCriteriaBuilder  $searchCriteriaBuilder
     * @param RequestInterface       $request
     * @param AuthorizationInterface $authorization
     * @param FilterBuilder          $filterBuilder
     * @param array                  $meta
     * @param array                  $data
     * @param array                  $additionalFilterPool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        AuthorizationInterface $authorization,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = [],
        array $additionalFilterPool = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->meta = array_replace_recursive($meta, $this->prepareMetadata());
        $this->additionalFilterPool = $additionalFilterPool;
        $this->authorization = $authorization;
    }

    /**
     * Get authorization info.
     *
     * @return AuthorizationInterface|mixed
     */
    private function getAuthorizationInstance(): mixed
    {
        return $this->authorization;
    }

    /**
     * Prepares Meta
     *
     * @return array
     */
    public function prepareMetadata(): array
    {
        $metadata = [];

        if (!$this->getAuthorizationInstance()->isAllowed('MageINIC_BannerSlider::save')) {
            $metadata = [
                'mageinic_banner_columns' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'editorConfig' => ['enabled' => false],
                                'componentType' => Container::NAME
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $metadata;
    }

    /**
     * Add Filter
     *
     * @param  Filter $filter
     * @return void
     */
    public function addFilter(Filter $filter): void
    {
        if (!empty($this->additionalFilterPool[$filter->getField()])) {
            $this->additionalFilterPool[$filter->getField()]
                ->addFilter($this->searchCriteriaBuilder, $filter);
        } else {
            parent::addFilter($filter);
        }
    }
}
