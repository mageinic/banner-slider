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

namespace MageINIC\BannerSlider\Controller\Adminhtml\Slider;

use Exception;
use Magento\Backend\App\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use MageINIC\BannerSlider\Api\SliderRepositoryInterface;
use MageINIC\BannerSlider\Model\Slider;
use MageINIC\BannerSlider\Model\SliderFactory;

class Save extends AbstractAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_BannerSlider::save';

    /**
     * @var PostDataProcessor
     */
    protected PostDataProcessor $dataProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var SliderFactory
     */
    private SliderFactory $sliderFactory;

    /**
     * @var SliderRepositoryInterface
     */
    private SliderRepositoryInterface $sliderRepository;

    /**
     * @var Registry
     */
    public Registry $registry;

    /**
     * @var Config
     */
    public Config $config;

    /**
     * @var Slider
     */
    private Slider $slider;

    /**
     * @param Context $context
     * @param PostDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $registry
     * @param Config $config
     * @param Slider $slider
     * @param SliderFactory $sliderFactory
     * @param SliderRepositoryInterface $sliderRepository
     */
    public function __construct(
        Context                   $context,
        PostDataProcessor         $dataProcessor,
        DataPersistorInterface    $dataPersistor,
        Registry                  $registry,
        Config                    $config,
        Slider                    $slider,
        SliderFactory             $sliderFactory,
        SliderRepositoryInterface $sliderRepository
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
        $this->sliderFactory = $sliderFactory;
        $this->sliderRepository = $sliderRepository;
        $this->registry = $registry;
        $this->config = $config;
        $this->slider = $slider;
    }

    /**
     * Result Interface
     *
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Slider::STATUS_ENABLED;
            }
            if (empty($data['slider_id'])) {
                $data['slider_id'] = null;
            }
            /** @var Slider $model */
            $model = $this->sliderFactory->create();
            $id = $this->getRequest()->getParam('slider_id');
            if ($id) {
                try {
                    $model = $this->sliderRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This slider no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $model->setData($data);
            $model->setStartDate($data['start_date']);
            $model->setEndDate($data['end_date']);
            $this->_eventManager->dispatch( 'mageinic_slider_prepare_save',
                ['slider' => $model, 'request' => $this->getRequest()]);
            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit',
                    ['slider_id' => $model->getSliderId(),'_current' => true]);
            }
            try {
                $_slider = $this->sliderRepository->save($model);
                if (isset($data['slider_banners'])&& is_string($data['slider_banners']))
                {
                    $banners = json_decode($data['slider_banners'], true);
                    $_slider->setPostedBanners($banners);
                    $this->slider->saveSliderBanners($_slider);
                }
                if (isset($data['posted_banners']) && is_array($data['posted_banners']))
                {
                    $banners = $data['posted_banners'];
                    $_slider->setPostedBanners($banners);
                    $this->slider->saveSliderBanners($_slider);
                }
                $this->messageManager->addSuccessMessage(__('You saved the slider.'));
                $this->dataPersistor->clear('mageinic_slider');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit',
                        ['slider_id' => $model->getSliderId(),'_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while save the slider.'));
            }
            $this->dataPersistor->set('mageinic_slider', $data);
            return $resultRedirect->setPath('*/*/edit',
                ['slider_id' => $this->getRequest()->getParam('slider_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
