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
use MageINIC\BannerSlider\Api\SliderRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Ui\Component\MassAction\Filter;
use MageINIC\BannerSlider\Model\ResourceModel\Slider\CollectionFactory;
use Magento\Backend\App\Action\Context;

/**
 * Class BannerSlider MassDelete
 */
class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_BannerSlider::slider_delete';

    /**
     * @var Filter $filter
     */
    private Filter $filter;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var SliderRepositoryInterface
     */
    private SliderRepositoryInterface $sliderRepository;

    /**
     * MassDelete constructor.
     *
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Context $context
     * @param SliderRepositoryInterface $sliderRepository
     */
    public function __construct(
        Filter                    $filter,
        CollectionFactory         $collectionFactory,
        Context                   $context,
        SliderRepositoryInterface $sliderRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->sliderRepository = $sliderRepository;
        parent::__construct($context);
    }

    /**
     * Mass Delete Action
     *
     * @return Redirect
     */
    public function execute()
    {
        try {
            $logCollection = $this->filter->getCollection($this->collectionFactory->create());
            $itemsDeleted = 0;
            foreach ($logCollection as $item) {
                $this->sliderRepository->delete($item);
                $itemsDeleted++;
            }
            $this->messageManager->addSuccess(__('A total of %1 Slider(s) were deleted.', $itemsDeleted));
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
