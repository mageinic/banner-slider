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

namespace MageINIC\BannerSlider\Block\Adminhtml\Slider\Tab\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Catalog\Helper\Image;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageINIC\BannerSlider\Model\Banner;

/**
 * BannerSlider BannerImage Class
 */
class BannerImage extends AbstractRenderer
{
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var Image
     */
    private Image $imageHelper;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Image $imageHelper
     * @param array $data
     */
    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager,
        Image                 $imageHelper,
        array                 $data = []
    ) {
        parent::__construct($context, $data);
        $this->imageHelper = $imageHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * Renders grid column
     *
     * @param DataObject $row
     * @return string
     * @throws NoSuchEntityException
     */
    public function render(DataObject $row): string
    {
        $this->_getValue($row);
        $mediaDirectory = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        if ($this->_getValue($row) != '') {
            if ($row['media_type'] == 1) {
                $imageUrl = $mediaDirectory . 'MageINIC/bannerslider/' . $this->_getValue($row);
                $img = '<img src="' . $imageUrl . '" width="100" height="100"/>';
            } elseif ($row['media_type'] == 2) {
                $imageUrl = $mediaDirectory . 'MageINIC/bannerslider/' . $this->_getValue($row);
                $img = '<video src="' . $imageUrl . '" width="100" height="100"/>';
            } else {
                $img = '<img src="' . $this->imageHelper
                        ->getDefaultPlaceholderUrl('image') . '"width="50" height="50"/>';
            }
            return $img;
        }
    }
}
