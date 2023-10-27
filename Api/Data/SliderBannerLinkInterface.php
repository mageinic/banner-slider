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

namespace MageINIC\BannerSlider\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Banner Slider Banner Link Interface
 */
interface SliderBannerLinkInterface extends ExtensibleDataInterface
{
    /**
     * Get Id
     *
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Set Id
     *
     * @param  string $id
     * @return $this
     */
    public function setId($id): SliderBannerLinkInterface;

    /**
     * Get Position
     *
     * @return int|null
     */
    public function getPosition(): ?int;

    /**
     * Set Position
     *
     * @param  int $position
     * @return $this
     */
    public function setPosition($position): SliderBannerLinkInterface;

    /**
     * Get category id
     *
     * @return string
     */
    public function getSliderId(): string;

    /**
     * Set category id
     *
     * @param  string $sliderId
     * @return $this
     */
    public function setSliderId($sliderId): SliderBannerLinkInterface;

    /**
     * Retrieve existing extension attributes object.
     *
     * @return \MageINIC\BannerSlider\Api\Data\SliderBannerLinkExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param  \MageINIC\BannerSlider\Api\Data\SliderBannerLinkExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageINIC\BannerSlider\Api\Data\SliderBannerLinkExtensionInterface $extensionAttributes
    );
}
