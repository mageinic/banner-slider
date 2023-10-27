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

namespace MageINIC\BannerSlider\Controller\Adminhtml;

use MageINIC\BannerSlider\Api\SliderLinkManagementInterface;
use MageINIC\BannerSlider\Model\BannerFactory;
use MageINIC\BannerSlider\Model\BannerRepository;
use MageINIC\BannerSlider\Model\MediaUploader;
use MageINIC\BannerSlider\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * BannerSlider Banner Class
 */
abstract class Banner extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_BannerSlider::banner_save';

    /**
     * @var Registry
     */
    protected Registry $coreRegistry;

    /**
     * @var FileFactory
     */
    protected FileFactory $_fileFactory;

    /**
     * @var SliderLinkManagementInterface
     */
    protected SliderLinkManagementInterface $sliderLinkManagement;

    /**
     * @var BannerRepository
     */
    protected BannerRepository $bannerRepository;

    /**
     * @var MediaUploader
     */
    protected MediaUploader $mediaUploader;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $_bannerCollectionFactory;

    /**
     * @var PageFactory
     */
    protected PageFactory $_resultPageFactory;

    /**
     * @var LayoutFactory
     */
    protected LayoutFactory $_resultLayoutFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @var BannerFactory
     */
    protected BannerFactory $bannerFactory;

    /**
     * @var ForwardFactory
     */
    protected ForwardFactory $_resultForwardFactory;

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param BannerFactory $bannerFactory
     * @param BannerRepository $bannerRepository
     * @param CollectionFactory $bannerCollectionFactory
     * @param FileFactory $fileFactory
     * @param PageFactory $resultPageFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param ForwardFactory $resultForwardFactory
     * @param StoreManagerInterface $storeManager
     * @param DataPersistorInterface $dataPersistor
     * @param MediaUploader $mediaUploader
     * @param SliderLinkManagementInterface $sliderLinkManagement
     */
    public function __construct(
        Context                       $context,
        Registry                      $coreRegistry,
        BannerFactory                 $bannerFactory,
        BannerRepository              $bannerRepository,
        CollectionFactory             $bannerCollectionFactory,
        FileFactory                   $fileFactory,
        PageFactory                   $resultPageFactory,
        LayoutFactory                 $resultLayoutFactory,
        ForwardFactory                $resultForwardFactory,
        StoreManagerInterface         $storeManager,
        DataPersistorInterface        $dataPersistor,
        MediaUploader                 $mediaUploader,
        SliderLinkManagementInterface $sliderLinkManagement
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_storeManager = $storeManager;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->bannerFactory = $bannerFactory;
        $this->bannerRepository = $bannerRepository;
        $this->_bannerCollectionFactory = $bannerCollectionFactory;
        $this->dataPersistor = $dataPersistor;
        $this->mediaUploader = $mediaUploader;
        $this->sliderLinkManagement = $sliderLinkManagement;
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage($resultPage): Page
    {
        $resultPage->setActiveMenu('MageINIC_BannerSlider::mageinic_banner')
            ->addBreadcrumb(__('Banner'), __('Banner'))
            ->addBreadcrumb(__('MageINIC Bannersliders'), __('MageINIC Bannersliders'));
        return $resultPage;
    }
}
