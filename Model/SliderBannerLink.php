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

use MageINIC\BannerSlider\Api\Data\SliderBannerLinkExtensionInterface;
use MageINIC\BannerSlider\Api\Data\SliderBannerLinkInterface;
use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\Framework\Api\ExtensionAttributesInterface;

/**
 * Class For SliderBannerLink
 */
class SliderBannerLink extends AbstractExtensibleObject implements SliderBannerLinkInterface
{
    /**
     * Constant for confirmation status
     */
    public const KEY_BANNER_ID = 'banner_id';
    public const KEY_POSITION = 'position';
    public const KEY_SLIDER_ID = 'slider_id';

    /**
     * @inheritdoc
     */
    public function getId(): ?string
    {
        return $this->_get(self::KEY_BANNER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): ?int
    {
        return $this->_get(self::KEY_POSITION);
    }

    /**
     * @inheritdoc
     */
    public function getSliderId(): string
    {
        return $this->_get(self::KEY_SLIDER_ID);
    }

    /**
     * Set Id
     *
     * @param  string $id
     * @return $this
     */
    public function setId($id): SliderBannerLinkInterface
    {
        return $this->setData(self::KEY_BANNER_ID, $id);
    }

    /**
     * Set Position
     *
     * @param  int $position
     * @return $this
     */
    public function setPosition($position): SliderBannerLinkInterface
    {
        return $this->setData(self::KEY_POSITION, $position);
    }

    /**
     * Set slider id
     *
     * @param  string $sliderId
     * @return $this
     */
    public function setSliderId($sliderId): SliderBannerLinkInterface
    {
        return $this->setData(self::KEY_SLIDER_ID, $sliderId);
    }

    /**
     * Get ExtensionAttributes
     *
     * @return SliderBannerLinkExtensionInterface|ExtensionAttributesInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set ExtensionAttributes
     *
     * @param  SliderBannerLinkExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        SliderBannerLinkExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
