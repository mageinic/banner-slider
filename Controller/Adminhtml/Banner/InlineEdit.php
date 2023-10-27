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
use MageINIC\BannerSlider\Api\BannerRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class BannerSlider InlineEdit
 */
class InlineEdit extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_BannerSlider::banner_edit';

    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var BannerRepositoryInterface
     */
    private BannerRepositoryInterface $bannerRepository;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param BannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        Context                   $context,
        JsonFactory               $jsonFactory,
        BannerRepositoryInterface $bannerRepository
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * Inline edit action
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelId) {
                    try {
                        $this->saveData($modelId, $postItems);
                    } catch (Exception $e) {
                        $messages[] = "[Banner ID: {$modelId}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }
        return $resultJson->setData(['messages' => $messages,'error' => $error]);
    }

    /**
     * Save Data
     *
     * @param int $modelId
     * @param array $postItems
     * @return void
     * @throws LocalizedException
     */
    public function saveData(int $modelId, array $postItems): void
    {
        $model = $this->bannerRepository->getById($modelId);
        $model->setData(array_merge($model->getData(), $postItems[$modelId]));
        $this->bannerRepository->save($model);
    }
}
