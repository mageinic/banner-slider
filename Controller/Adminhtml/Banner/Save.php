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

namespace MageINIC\BannerSlider\Controller\Adminhtml\Banner;

use Exception;
use MageINIC\BannerSlider\Api\SliderLinkManagementInterface;
use MageINIC\BannerSlider\Model\ResourceModel\Banner;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use MageINIC\BannerSlider\Controller\Adminhtml\Banner as SaveBanner;

/**
 * BannerSlider Save Class
 */
class Save extends SaveBanner implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_BannerSlider::banner_save';

    /**
     * @var SliderLinkManagementInterface
     */
    protected SliderLinkManagementInterface $sliderLinkManagement;

    /**
     * Save Action
     *
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws LocalizedException
     * @throws FileSystemException
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        unset($data['media']);
        if ($data) {
            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Banner::STATUS_ENABLED;
            }
            if (empty($data['banner_id'])) {
                $data['banner_id'] = null;
            }
            if (isset($data['media_type']) && $data['media_type'] == 1) {
                if (isset($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                    $data['image'] = $data['image'][0]['name'];

                    $this->mediaUploader->moveFileFromTmp($data['image']);
                } elseif (isset($data['image'][0]['name']) && !isset($data['image'][0]['tmp_name'])) {
                    $data['image'] = $data['image'][0]['name'];
                } else {
                    if (isset($data['image']) && isset($data['image']['value'])) {
                        if (isset($data['image']['delete'])) {
                            $data['image'] = null;
                            $data['delete_image'] = true;
                        } elseif (isset($data['image']['value'])) {
                            $data['image'] = $data['image']['value'];
                        } else {
                            $data['image'] = null;
                        }
                    }
                }
                $data['media'] = $data['image'];
            } else {
                if (isset($data['video_media'][0]['name']) && isset($data['video_media'][0]['tmp_name'])) {
                    $data['video_media'] = $data['video_media'][0]['name'];

                    $this->mediaUploader->moveFileFromTmp($data['video_media']);
                } elseif (isset($data['video_media'][0]['media']) && !isset($data['video_media'][0]['tmp_name'])) {
                    $data['video_media'] = $data['video_media'][0]['media'];
                } else {
                    if (isset($data['video_media']) && isset($data['video_media']['value'])) {
                        if (isset($data['video_media']['delete'])) {
                            $data['video_media'] = null;
                            $data['delete_video_media'] = true;
                        } elseif (isset($data['media']['value'])) {
                            $data['video_media'] = $data['video_media']['value'];
                        } else {
                            $data['video_media'] = null;
                        }
                    }
                }
                $data['media'] = $data['video_media'];
            }
            /** @var Banner $model */
            $model = $this->bannerFactory->create();
            $id = $this->getRequest()->getParam('banner_id');
            if ($id) {
                try {
                    $model = $this->bannerRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This banner no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $model->setData($data);
            if (!empty($data['slider_id'])) {
                $model->setSliderId(implode(',', $data['slider_id']));
            }
            try {
                $model->beforeSave();
                $this->bannerRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the banner.'));
                $this->dataPersistor->clear('mageinic_banner');
                if (!empty($data['slider_id'])) {
                    $this->sliderLinkManagement->assignBannerToSliders(
                        $model->getId(),
                        $data['slider_id']
                    );
                }
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['banner_id' => $model->getBannerId()]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the banner.')
                );
            }
            $this->dataPersistor->set('mageinic_banner', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['banner_id' => $this->getRequest()->getParam('banner_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
