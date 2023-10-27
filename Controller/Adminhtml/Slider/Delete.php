<?php

/**
 * MageINIC
 * Copyright (C) 2019 MageINIC <info@magentocoders.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    MageINIC
 * @package     MageINIC_Bannerslider
 * @copyright   Copyright (c) 2012 MageINIC (http://www.magentocoders.com/)
 * @license     http://www.magentocoders.com/license-agreement.html
 * @author      MageINIC <info@magentocoders.com>
 */

namespace MageINIC\BannerSlider\Controller\Adminhtml\Slider;

use MageINIC\BannerSlider\Model\Slider;
use MageINIC\BannerSlider\Model\SliderFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_BannerSlider::slider_delete';

    /**
     * @var Slider
     */
    protected Slider|SliderFactory $sliderModelFactory;

    /**
     * @param Context $context
     * @param Slider $sliderModelFactory
     */
    public function __construct(
        Context $context,
        SliderFactory $sliderModelFactory
    ) {
        $this->sliderModelFactory = $sliderModelFactory;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('slider_id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                $model = $this->sliderModelFactory->create();
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                $this->messageManager->addSuccess(__('The slider has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_cmspage_on_delete',
                    ['title' => $title, 'status' => 'success']
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_cmspage_on_delete',
                    ['title' => $title, 'status' => 'fail']
                );
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['slider_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a page to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
