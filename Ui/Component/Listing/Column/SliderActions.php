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

use Magento\Framework\EscaperFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use MageINIC\BannerSlider\Block\Adminhtml\Slider\Grid\Renderer\Action\UrlBuilder;

/**
 * Class BannerActions
 */
class SliderActions extends Column
{
    /**
     * Url path
     */
    public const MAGEINIC_URL_PATH_EDIT = 'bannerslider/slider/edit';
    public const MAGEINIC_URL_PATH_DELETE = 'bannerslider/slider/delete';

    /**
     * @var UrlBuilder
     */
    protected UrlBuilder $actionUrlBuilder;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param EscaperFactory $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder         $actionUrlBuilder,
        UrlInterface       $urlBuilder,
        EscaperFactory     $escaper,
        array              $components = [],
        array              $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->escaper = $escaper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['slider_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::MAGEINIC_URL_PATH_EDIT,
                            ['slider_id' => $item['slider_id']]
                        ),
                        'label' => __('Edit')
                    ];
                    $title = $this->escaper->create()->escapeHtml($item['title']);
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::MAGEINIC_URL_PATH_DELETE,
                            ['slider_id' => $item['slider_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1', $title),
                            'message' => __('Are you sure you want to delete a %1 record?', $title)
                        ]
                    ];
                }
                if (isset($item['identifier'])) {
                    $item[$name]['preview'] = [
                        'href' => $this->actionUrlBuilder->getUrl(
                            $item['identifier'],
                            $item['_first_store_id'] ?? null,
                            $item['store_code'] ?? null
                        ),
                        'label' => __('View')
                    ];
                }
            }
        }
        return $dataSource;
    }
}
