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

namespace MageINIC\BannerSlider\Block\Adminhtml\Slider\Edit;

use Magento\Backend\Block\Widget\Context;
use MageINIC\BannerSlider\Api\SliderRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * BannerSlider Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var SliderRepositoryInterface
     */
    protected SliderRepositoryInterface $sliderRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param SliderRepositoryInterface $sliderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                   $context,
        SliderRepositoryInterface $sliderRepository,
        LoggerInterface           $logger,
    ) {
        $this->context = $context;
        $this->sliderRepository = $sliderRepository;
        $this->logger = $logger;
    }

    /**
     * Return Banner Slider ID
     *
     * @return mixed|null
     * @throws LocalizedException
     */
    public function getSliderId(): mixed
    {
        try {
            return $this->sliderRepository->getById(
                $this->context->getRequest()->getParam('slider_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            $this->logger->error('Slider not found: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
