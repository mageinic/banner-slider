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

use MageINIC\BannerSlider\Api\BannerRepositoryInterface;
use MageINIC\BannerSlider\Api\Data\SliderBannerLinkInterface;
use MageINIC\BannerSlider\Api\SliderLinkRepositoryInterface;
use MageINIC\BannerSlider\Api\SliderRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;

/**
 * BannerSlider SliderLinkRepository
 */
class SliderLinkRepository implements SliderLinkRepositoryInterface
{
    /**
     * @var SliderFactory
     */
    public SliderFactory $sliderFactory;

    /**
     * @var SliderRepository
     */
    protected SliderRepository $sliderRepository;

    /**
     * @var BannerRepositoryInterface
     */
    protected BannerRepositoryInterface $bannerRepository;

    /**
     * @param SliderFactory             $sliderFactory
     * @param SliderRepositoryInterface $sliderRepository
     * @param BannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        SliderFactory             $sliderFactory,
        SliderRepositoryInterface $sliderRepository,
        BannerRepositoryInterface $bannerRepository
    ) {
        $this->sliderFactory = $sliderFactory;
        $this->sliderRepository = $sliderRepository;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @inheritdoc
     */
    public function save(SliderBannerLinkInterface $bannerLink): mixed
    {
        $slider = $this->sliderFactory->create()->load($bannerLink->getSliderId());
        $banner = $this->bannerRepository->getById($bannerLink->getId());
        $bannerPositions = $slider->getBannersPosition();
        $bannerPositions[$banner->getId()] = $bannerLink->getPosition();
        $slider->setPostedBanners($bannerPositions);

        try {
            $slider->saveSliderBanners($slider);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Could not save banner "%1" with position %2 to slider %3',
                    $banner->getBannerrId(),
                    $bannerLink->getPosition(),
                    $slider->getSliderId()
                ),
                $e
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function delete(SliderBannerLinkInterface $bannerLink): bool
    {
        return $this->deleteByIds($bannerLink->getSliderId(), $bannerLink->getId());
    }

    /**
     * @inheritdoc
     */
    public function deleteByIds($sliderId, $id): mixed
    {
        if (empty($sliderId)) {
            return null;
        }
        $slider = $this->sliderFactory->create()->load($sliderId);
        if (!$slider->getId()) {
            return null;
        }
        $banner = $this->bannerRepository->getById($id);
        $bannerPositions = $slider->getBannersPosition();
        $bannerID = $banner->getBannerId();
        if (!isset($bannerPositions[$bannerID])) {
            throw new InputException(__('Slider does not contain specified banner'));
        }
        $backupPosition = $bannerPositions[$bannerID];
        unset($bannerPositions[$bannerID]);
        $slider->setPostedBanners($bannerPositions);
        try {
            $slider->saveSliderBanners($slider);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Could not save banner "%banner" with position %position to slider %slider',
                    [
                        "banner" => $banner->getBannerId(),
                        "position" => $backupPosition,
                        "slider" => $slider->getSliderId()
                    ]
                ),
                $e
            );
        }
        return true;
    }
}
