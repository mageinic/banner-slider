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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use MageINIC\BannerSlider\Api\BannerRepositoryInterface;

/**
 * Media Delete Controller.
 */
class Delete extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_BannerSlider::banner_delete';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var BannerRepositoryInterface
     */
    protected BannerRepositoryInterface $bannerRepository;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param BannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        Context                   $context,
        PageFactory               $resultPageFactory,
        BannerRepositoryInterface $bannerRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->bannerRepository = $bannerRepository;
        parent::__construct($context);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('banner_id');

        if ($id) {
            try {
                $this->bannerRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the banner.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['banner_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a banner to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
