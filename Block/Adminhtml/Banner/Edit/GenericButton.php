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

namespace MageINIC\BannerSlider\Block\Adminhtml\Banner\Edit;

use Magento\Backend\Block\Widget\Context;
use MageINIC\BannerSlider\Api\BannerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @var BannerRepositoryInterface
     */
    protected BannerRepositoryInterface $bannerRepository;

    /**
     * @param Context                   $context
     * @param BannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository
    ) {
        $this->context = $context;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * Return MageINIC banner ID
     *
     * @return mixed|null
     * @throws LocalizedException
     */
    public function getBannerId(): mixed
    {
        $bannerId = $this->context->getRequest()->getParam('banner_id');

        if ($bannerId !== null) {
            $banner = $this->bannerRepository->getById($bannerId);
            return $banner->getId();
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
