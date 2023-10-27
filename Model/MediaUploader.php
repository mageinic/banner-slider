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

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Model MediaUploader
 */
class MediaUploader
{
    /**
     * @var string
     */
    public $baseTmpPath;

    /**
     * @var string
     */
    public $basePath;

    /**
     * @var string[]
     */
    public $allowedExtensions;

    /**
     * @var Database
     */
    private $coreFileStorageDatabase;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var UploaderFactory
     */
    private UploaderFactory $uploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * MediaUploader constructor.
     *
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @throws FileSystemException
     */
    public function __construct(
        Database              $coreFileStorageDatabase,
        Filesystem            $filesystem,
        UploaderFactory       $uploaderFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface       $logger
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->baseTmpPath = "MageINIC/bannerslider/tmp";
        $this->basePath = "MageINIC/bannerslider";
        $this->allowedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'mp4', '3gp', 'wmv', 'flv', 'avi'];
    }

    /**
     * Move File From Tmp
     *
     * @param mixed $imageName
     * @return mixed
     * @throws LocalizedException
     */
    public function moveFileFromTmp(mixed $imageName): mixed
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath = $this->getBasePath();
        $baseImagePath = $this->getFilePath($basePath, $imageName);
        $baseTmpImagePath = $this->getFilePath($baseTmpPath, $imageName);
        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpImagePath,
                $baseImagePath
            );
            $this->mediaDirectory->renameFile(
                $baseTmpImagePath,
                $baseImagePath
            );
        } catch (Exception $e) {
            throw new LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }
        return $imageName;
    }

    /**
     * @inheritdoc
     */
    public function getBaseTmpPath(): string
    {
        return $this->baseTmpPath;
    }

    /**
     * @inheritdoc
     */
    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }

    /**
     * @inheritdoc
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @inheritdoc
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Get File Path
     *
     * @param mixed $path
     * @param mixed $imageName
     * @return string
     */
    public function getFilePath(mixed $path, mixed $imageName): string
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Save File To Tmp Dir
     *
     * @param mixed $fileId
     * @return array|bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function saveFileToTmpDir($fileId): bool|array
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions($fileId));
        $uploader->setAllowRenameFiles(true);
        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
        if (!$result) {
            throw new LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] = $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($baseTmpPath, $result['file']);
        $result['name'] = $result['file'];
        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (Exception $e) {
                $this->logger->critical($e);
                throw new LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }
        return $result;
    }

    /**
     * Get Allowed Extensions
     *
     * @param mixed $fileId
     * @return string[]
     */
    public function getAllowedExtensions(mixed $fileId): array
    {
        if ($fileId == 'vide_media') {
            $this->allowedExtensions = ['mp4', '3gp', 'wmv', 'flv', 'avi'];
        }
        if ($fileId == 'image_media') {
            $this->allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        }
        return $this->allowedExtensions;
    }

    /**
     * Set Allowed Extensions
     *
     * @param mixed $allowedExtensions
     */
    public function setAllowedExtensions(mixed $allowedExtensions): void
    {
        $this->allowedExtensions = $allowedExtensions;
    }
}
