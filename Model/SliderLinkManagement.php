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
use MageINIC\BannerSlider\Api\Data\SliderBannerLinkInterfaceFactory;
use MageINIC\BannerSlider\Api\SliderLinkManagementInterface;
use MageINIC\BannerSlider\Api\SliderLinkRepositoryInterface;
use MageINIC\BannerSlider\Api\SliderRepositoryInterface;
use MageINIC\BannerSlider\Model\ResourceModel\Banner;
use MageINIC\BannerSlider\Model\ResourceModel\Banner\Collection;

/**
 * BannerSlider Class SliderLinkManagement
 */
class SliderLinkManagement implements SliderLinkManagementInterface
{
    /**
     * @var SliderRepositoryInterface
     */
    protected SliderRepositoryInterface $sliderRepository;

    /**
     * @var BannerRepositoryInterface
     */
    protected BannerRepositoryInterface $bannerRepository;

    /**
     * @var ResourceModel\Banner
     */
    protected Banner $bannerResource;

    /**
     * @var SliderLinkRepositoryInterface
     */
    protected SliderLinkRepositoryInterface $sliderLinkRepository;

    /**
     * @var SliderBannerLinkInterfaceFactory
     */
    protected SliderBannerLinkInterfaceFactory $bannerLinkFactory;

    /**
     * @param SliderRepositoryInterface        $sliderRepository
     * @param SliderBannerLinkInterfaceFactory $bannerLinkFactory
     * @param BannerRepositoryInterface        $bannerRepository
     * @param Banner                           $bannerResource
     * @param SliderLinkRepositoryInterface    $sliderLinkRepository
     */
    public function __construct(
        SliderRepositoryInterface $sliderRepository,
        SliderBannerLinkInterfaceFactory $bannerLinkFactory,
        BannerRepositoryInterface $bannerRepository,
        Banner $bannerResource,
        SliderLinkRepositoryInterface $sliderLinkRepository
    ) {
        $this->sliderRepository = $sliderRepository;
        $this->bannerLinkFactory = $bannerLinkFactory;
        $this->bannerRepository = $bannerRepository;
        $this->bannerResource = $bannerResource;
        $this->sliderLinkRepository = $sliderLinkRepository;
    }

    /**
     * @inheritdoc
     */
    public function getAssignedBanners($sliderId): mixed
    {
        $slider = $this->sliderRepository->getById($sliderId);

        /** @var Collection $banners */
        $banners = $slider->getBannerCollection();
        $banners->addFieldToSelect('position');

        /** @var SliderBannerLinkInterface[] $links */
        $links = [];

        /** @var \MageINIC\BannerSlider\Model\Banner $banner */
        foreach ($banners->getItems() as $banner) {
            /** @var SliderBannerLinkInterface $link*/
            $link = $this->bannerLinkFactory->create();
            $link->setId($banner->getId())
                ->setPosition($banner->getData('cat_index_position'))
                ->setSliderId($slider->getId());
            $links[] = $link;
        }
        return $links;
    }

    /**
     * Assign banner to given sliders
     *
     * @param  string $bannerId
     * @param  int[] $sliderIds
     * @return bool
     */
    public function assignBannerToSliders($bannerId, array $sliderIds): mixed
    {
        $banner = $this->bannerRepository->getById($bannerId);
        $assignedSliders = $this->bannerResource->getSliderIds($banner);

        if (count(array_diff($assignedSliders, $sliderIds)) > 0) {
            foreach (array_diff($assignedSliders, $sliderIds) as $sliderId) {
                $this->sliderLinkRepository->deleteByIds($sliderId, $bannerId);
            }
        }

        if (count(array_diff($sliderIds, $assignedSliders)) > 0) {
            foreach (array_diff($sliderIds, $assignedSliders) as $sliderId) {
                /** @var SliderBannerLinkInterface $sliderBannerLink */
                $sliderBannerLink = $this->bannerLinkFactory->create();
                $sliderBannerLink->setId($bannerId);
                $sliderBannerLink->setSliderId($sliderId);
                $sliderBannerLink->setPosition(0);
                $this->sliderLinkRepository->save($sliderBannerLink);
            }
        }
        return true;
    }
}
