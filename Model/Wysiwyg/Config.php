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

namespace MageINIC\BannerSlider\Model\Wysiwyg;

use Magento\Backend\Model\UrlInterface;
use Magento\Cms\Model\Wysiwyg\CompositeConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;
use Magento\Variable\Model\Variable\Config as VariableConfig;
use Magento\Widget\Model\Widget\Config as WidgetConfig;

/**
 * Wysiwyg Config for Editor HTML Element
 */
class Config extends DataObject implements ConfigInterface
{
    public const WYSIWYG_ENABLED = 'enabled';
    public const WYSIWYG_STATUS_CONFIG_PATH = 'cms/wysiwyg/enabled';
    public const WYSIWYG_SKIN_IMAGE_PLACEHOLDER_ID = 'MageINIC_BannerSlider::images/wysiwyg_skin_image.png';
    public const WYSIWYG_HIDDEN = 'hidden';

    /**
     * @var AuthorizationInterface
     */
    protected AuthorizationInterface $_authorization;

    /**
     * @var Repository
     */
    protected Repository $_assetRepo;

    /**
     * @var VariableConfig
     */
    protected VariableConfig $_variableConfig;

    /**
     * @var WidgetConfig
     */
    protected WidgetConfig $_widgetConfig;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $_eventManager;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $_scopeConfig;

    /**
     * @var array
     */
    protected $_windowSize;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $_backendUrl;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var CompositeConfigProvider
     */
    private $configProvider;

    /**
     * @param UrlInterface $backendUrl
     * @param ManagerInterface $eventManager
     * @param AuthorizationInterface $authorization
     * @param Repository $assetRepo
     * @param VariableConfig $variableConfig
     * @param WidgetConfig $widgetConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param array $windowSize
     * @param array $data
     * @param CompositeConfigProvider|null $configProvider
     */
    public function __construct(
        UrlInterface            $backendUrl,
        ManagerInterface        $eventManager,
        AuthorizationInterface  $authorization,
        Repository              $assetRepo,
        VariableConfig          $variableConfig,
        WidgetConfig            $widgetConfig,
        ScopeConfigInterface    $scopeConfig,
        StoreManagerInterface   $storeManager,
        Filesystem              $filesystem,
        array                   $windowSize = [],
        array                   $data = [],
        CompositeConfigProvider $configProvider = null
    ) {
        $this->_backendUrl = $backendUrl;
        $this->_eventManager = $eventManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_authorization = $authorization;
        $this->_assetRepo = $assetRepo;
        $this->_variableConfig = $variableConfig;
        $this->_widgetConfig = $widgetConfig;
        $this->_windowSize = $windowSize;
        $this->_storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->configProvider = $configProvider ?: ObjectManager::getInstance()
            ->get(CompositeConfigProvider::class);
        parent::__construct($data);
    }

    /**
     * Return Wysiwyg config as \Magento\Framework\DataObject
     *
     * @param  array|DataObject $data
     * @return DataObject
     */
    public function getConfig($data = []): DataObject
    {
        $config = new DataObject();
        $config->setData(
            [
                'enabled' => $this->isEnabled(),
                'hidden' => $this->isHidden(),
                'baseStaticUrl' => $this->_assetRepo->getStaticViewFileContext()->getBaseUrl(),
                'baseStaticDefaultUrl' => str_replace('index.php/', '', $this->_backendUrl->getBaseUrl())
                    . $this->filesystem->getUri(DirectoryList::STATIC_VIEW) . '/',
                'directives_url' => $this->_backendUrl->getUrl('cms/wysiwyg/directive'),
                'use_container' => false,
                'add_variables' => true,
                'add_widgets' => true,
                'no_display' => false,
                'add_directives' => true,
                'width' => '100%',
                'height' => '500px',
                'plugins' => [],
            ]
        );
        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));
        if (is_array($data)) {
            $config->addData($data);
        }
        if ($this->_authorization->isAllowed('MageINIC_BannerSlider::media_gallery')) {
            $this->configProvider->processGalleryConfig($config);
            $config->addData(
                [
                    'files_browser_window_width' => $this->_windowSize['width'],
                    'files_browser_window_height' => $this->_windowSize['height'],
                ]
            );
        }
        if ($config->getData('add_widgets')) {
            $this->configProvider->processWidgetConfig($config);
        }
        if ($config->getData('add_variables')) {
            $this->configProvider->processVariableConfig($config);
        }
        return $this->configProvider->processWysiwygConfig($config);
    }

    /**
     * Return path for skin images placeholder
     *
     * @return string
     */
    public function getSkinImagePlaceholderPath(): string
    {
        $staticPath = $this->_storeManager->getStore()->getBaseStaticDir();
        $placeholderPath = $this->_assetRepo->createAsset(self::WYSIWYG_SKIN_IMAGE_PLACEHOLDER_ID)->getPath();
        return $staticPath . '/' . $placeholderPath;
    }

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        $wysiwygState = $this->_scopeConfig->getValue(
            self::WYSIWYG_STATUS_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        return in_array($wysiwygState, [self::WYSIWYG_ENABLED, self::WYSIWYG_HIDDEN]);
    }

    /**
     * Check whether Wysiwyg is loaded on demand or not
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        $status = $this->_scopeConfig->getValue(self::WYSIWYG_STATUS_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
        return $status == self::WYSIWYG_HIDDEN;
    }
}
