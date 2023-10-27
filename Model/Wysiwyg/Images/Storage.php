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

namespace MageINIC\BannerSlider\Model\Wysiwyg\Images;

use MageINIC\BannerSlider\Helper\Wysiwyg\Images;
use MageINIC\BannerSlider\Model\Wysiwyg\Images\Storage\CollectionFactory;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Config\Element;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\Filesystem as DataFilesystem;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Storage\DatabaseFactory;
use Magento\MediaStorage\Model\File\Storage\Directory\DatabaseFactory as DirectoryDatabaseFactory;
use Magento\MediaStorage\Model\File\Storage\File;
use Magento\MediaStorage\Model\File\Storage\FileFactory;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Cms\Helper\Wysiwyg\Images as CmsImages;

/**
 * Wysiwyg Images model
 */
class Storage extends DataObject
{
    public const DIRECTORY_NAME_REGEXP = '/^[a-z0-9\-\_]+$/si';
    public const THUMBS_DIRECTORY_NAME = '.thumbs';
    public const THUMB_PLACEHOLDER_PATH_SUFFIX = 'MageINIC_BannerSlider::images/placeholder_thumbnail.jpg';

    /**
     * Config object
     *
     * @var Element
     */
    protected Element $config;

    /**
     * @var Write
     */
    protected $directory;

    /**
     * @var AdapterFactory
     */
    protected AdapterFactory $imageFactory;

    /**
     * @var Repository
     */
    protected Repository $assetRepo;

    /**
     * Core file storage database
     *
     * @var Database
     */
    protected $coreFileStorageDb = null;

    /**
     * @var null
     */
    protected $cmsWysiwygImages = null;

    /**
     * @var array
     */
    protected $resizeParameters;

    /**
     * @var array
     */
    protected $extensions;

    /**
     * @var array
     */
    protected $dirs;

    /**
     * @var UrlInterface
     */
    protected $backendUrl;

    /**
     * @var Session
     */
    protected Session $session;

    /**
     * @var DirectoryDatabaseFactory
     */
    protected DirectoryDatabaseFactory $directoryDatabaseFactory;

    /**
     * @var DatabaseFactory
     */
    protected DatabaseFactory $storageDatabaseFactory;

    /**
     * @var FileFactory
     */
    protected FileFactory $storageFileFactory;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $storageCollectionFactory;

    /**
     * @var UploaderFactory
     */
    protected UploaderFactory $uploaderFactory;

    /**
     * @param Session $session
     * @param UrlInterface $backendUrl
     * @param \Magento\Cms\Helper\Wysiwyg\Images $cmsWysiwygImages
     * @param Database $coreFileStorageDb
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param Repository $assetRepo
     * @param CollectionFactory $storageCollectionFactory
     * @param FileFactory $storageFileFactory
     * @param DatabaseFactory $storageDatabaseFactory
     * @param DirectoryDatabaseFactory $directoryDatabaseFactory
     * @param UploaderFactory $uploaderFactory
     * @param array $resizeParameters
     * @param array $extensions
     * @param array $dirs
     * @param array $data
     * @throws FileSystemException
     */
    public function __construct(
        Session $session,
        UrlInterface $backendUrl,
        CmsImages $cmsWysiwygImages,
        Database $coreFileStorageDb,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        Repository $assetRepo,
        CollectionFactory $storageCollectionFactory,
        FileFactory $storageFileFactory,
        DatabaseFactory $storageDatabaseFactory,
        DirectoryDatabaseFactory $directoryDatabaseFactory,
        UploaderFactory $uploaderFactory,
        array $resizeParameters = [],
        array $extensions = [],
        array $dirs = [],
        array $data = []
    ) {
        $this->session = $session;
        $this->backendUrl = $backendUrl;
        $this->_cmsWysiwygImages = $cmsWysiwygImages;
        $this->coreFileStorageDb = $coreFileStorageDb;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory = $imageFactory;
        $this->assetRepo = $assetRepo;
        $this->storageCollectionFactory = $storageCollectionFactory;
        $this->storageFileFactory = $storageFileFactory;
        $this->storageDatabaseFactory = $storageDatabaseFactory;
        $this->directoryDatabaseFactory = $directoryDatabaseFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->resizeParameters = $resizeParameters;
        $this->extensions = $extensions;
        $this->dirs = $dirs;
        parent::__construct($data);
    }

    /**
     * Return one-level child directories for specified path
     *
     * @param string $path
     * @return DataFilesystem
     */
    public function getDirsCollection($path): DataFilesystem
    {
        $this->createSubDirectories($path);
        $collection = $this->getCollection($path)
            ->setCollectDirs(true)
            ->setCollectFiles(false)
            ->setCollectRecursively(false);
        $conditions = $this->getConditionsForExcludeDirs();
        return $this->removeItemFromCollection($collection, $conditions);
    }

    /**
     * Create subdirectories if DB storage is used
     *
     * @param  string $path
     * @return void
     */
    protected function createSubDirectories($path): void
    {
        if ($this->coreFileStorageDb->checkDbUsage()) {
            /** @var Database $subDirectories */
            $subDirectories = $this->directoryDatabaseFactory->create();
            $directories = $subDirectories->getSubdirectories($path);
            foreach ($directories as $directory) {
                $fullPath = rtrim($path, '/') . '/' . $directory['name'];
                $this->directory->create($fullPath);
            }
        }
    }

    /**
     * Storage collection
     *
     * @param  string $path Path to the directory
     * @return Storage\Collection
     */
    public function getCollection($path = null): Storage\Collection
    {
        /** @var Storage\Collection $collection */
        $collection = $this->storageCollectionFactory->create();
        if ($path !== null) {
            $collection->addTargetDir($path);
        }
        return $collection;
    }

    /**
     * Prepare and get conditions for exclude directories
     *
     * @return array
     */
    protected function getConditionsForExcludeDirs()
    {
        $conditions = ['reg_exp' => [], 'plain' => []];

        if ($this->dirs['exclude']) {
            foreach ($this->dirs['exclude'] as $dir) {
                $conditions[!empty($dir['regexp']) ? 'reg_exp' : 'plain'][$dir['name']] = true;
            }
        }

        if ($this->dirs['include']) {
            foreach ($this->dirs['include'] as $dir) {
                unset($conditions['reg_exp'][$dir['name']], $conditions['plain'][$dir['name']]);
            }
        }
        return $conditions;
    }

    /**
     * Remove excluded directories from collection
     *
     * @param DataFilesystem $collection
     * @param array $conditions
     * @return DataFilesystem
     */
    protected function removeItemFromCollection($collection, $conditions): DataFilesystem
    {
        $regExp = $conditions['reg_exp'] ? '~' . implode('|', array_keys($conditions['reg_exp'])) . '~i' : null;
        $storageRootLength = strlen($this->cmsWysiwygImages->getStorageRoot());

        foreach ($collection as $key => $value) {
            $rootChildParts = explode('/', substr($value->getFilename(), $storageRootLength));

            if (array_key_exists($rootChildParts[1], $conditions['plain'])
                || ($regExp && preg_match($regExp, $value->getFilename()))
            ) {
                $collection->removeItemByKey($key);
            }
        }
        return $collection;
    }

    /**
     * Return files
     *
     * @param  string $path Parent directory path
     * @param  string $type Type of storage, e.g. image, media etc.
     * @return DataFilesystem
     */
    public function getFilesCollection($path, $type = null): DataFilesystem
    {
        if ($this->coreFileStorageDb->checkDbUsage()) {
            $files = $this->storageDatabaseFactory->create()->getDirectoryFiles($path);

            /** @var File $fileStorageModel */
            $fileStorageModel = $this->storageFileFactory->create();
            foreach ($files as $file) {
                $fileStorageModel->saveFile($file);
            }
        }
        $collection = $this->getCollection($path)
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(false)
            ->setOrder('mtime',Collection::SORT_ORDER_ASC);
        if ($allowed = $this->getAllowedExtensions($type))
        {
            $collection->setFilesFilter('/\.(' . implode('|', $allowed) . ')$/i');
        }

        foreach ($collection as $item)
        {
            $item->setId($this->cmsWysiwygImages->idEncode($item->getBasename()));
            $item->setName($item->getBasename());
            $item->setShortName($this->_cmsWysiwygImages->getShortFilename($item->getBasename()));
            $item->setUrl($this->cmsWysiwygImages->getCurrentUrl() . $item->getBasename());
            if ($this->isImage($item->getBasename())) {
                $thumbUrl = $this->getThumbnailUrl($item->getFilename(), true);
                if (!$thumbUrl) {
                    $thumbUrl = $this->backendUrl->getUrl('cms/*/thumbnail', ['file' => $item->getId()]);
                }
            } else {
                $thumbUrl = $this->assetRepo->getUrl(self::THUMB_PLACEHOLDER_PATH_SUFFIX);
            }
            $item->setThumbUrl($thumbUrl);
        }
        return $collection;
    }

    /**
     * Prepare allowed_extensions config settings
     *
     * @param  string $type Type of storage, e.g. image, media etc.
     * @return array Array of allowed file extensions
     */
    public function getAllowedExtensions($type = null)
    {
        if (is_string($type) && array_key_exists("{$type}_allowed", $this->extensions)) {
            $allowed = $this->extensions["{$type}_allowed"];
        } else {
            $allowed = $this->extensions['allowed'];
        }
        return array_keys(array_filter($allowed));
    }

    /**
     * Simple way to check whether file is image or not based on extension
     *
     * @param  string $filename
     * @return bool
     */
    public function isImage($filename)
    {
        if (!$this->hasData('_image_extensions'))
        {
            $this->setData('_image_extensions', $this->getAllowedExtensions('image'));
        }
        $ext = strtolower(Magento\Framework\Filesystem\Io\File::getPathInfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $this->_getData('_image_extensions'));
    }

    /**
     * Thumbnail URL getter
     *
     * @param  string $filePath
     * @param  bool   $checkFile
     * @return string|false
     */
    public function getThumbnailUrl($filePath, $checkFile = false): bool|string
    {
        $mediaRootDir = $this->cmsWysiwygImages->getStorageRoot();
        if (strpos($filePath, $mediaRootDir) === 0) {
            $thumbSuffix = self::THUMBS_DIRECTORY_NAME . substr($filePath, strlen($mediaRootDir));
            if (!$checkFile || $this->directory->isExist(
                $this->directory->getRelativePath($mediaRootDir . '/' . $thumbSuffix)
            )
            ) {
                $thumbSuffix = substr($mediaRootDir, strlen($this->directory->getAbsolutePath())) . '/' . $thumbSuffix;
                $randomIndex = '?rand=' . time();
                return str_replace('\\', '/', $this->cmsWysiwygImages->getBaseUrl() . $thumbSuffix
                ) . $randomIndex;
            }
        }
        return false;
    }

    /**
     * Create new directory in storage
     *
     * @param  string $name New directory name
     * @param  string $path Parent directory path
     * @return array New directory info
     * @throws LocalizedException
     */
    public function createDirectory($name, $path)
    {
        if (!preg_match(self::DIRECTORY_NAME_REGEXP, $name)) {
            throw new LocalizedException(
                __('Please rename the folder using only letters, numbers, underscores and dashes.')
            );
        }
        $relativePath = $this->directory->getRelativePath($path);
        if (!$this->directory->isDirectory($relativePath) || !$this->directory->isWritable($relativePath)) {
            $path = $this->cmsWysiwygImages->getStorageRoot();
        }
        $newPath = $path . '/' . $name;
        $relativeNewPath = $this->directory->getRelativePath($newPath);
        if ($this->directory->isDirectory($relativeNewPath)) {
            throw new LocalizedException(
                __('We found a directory with the same name. Please try another folder name.')
            );
        }
        $this->directory->create($relativeNewPath);
        try {
            if ($this->coreFileStorageDb->checkDbUsage()) {
                $relativePath = $this->coreFileStorageDb->getMediaRelativePath($newPath);
                $this->directoryDatabaseFactory->create()->createRecursive($relativePath);
            }

            $result = [
                'name' => $name,
                'short_name' => $this->cmsWysiwygImages->getShortFilename($name),
                'path' => $newPath,
                'id' => $this->cmsWysiwygImages->convertPathToId($newPath),
            ];
            return $result;
        } catch (FileSystemException $e) {
            throw new LocalizedException(__('We cannot create a new directory.'));
        }
    }

    /**
     * Recursively delete directory from storage
     *
     * @param  string $path Target dir
     * @return void
     * @throws LocalizedException
     */
    public function deleteDirectory($path): void
    {
        if ($this->coreFileStorageDb->checkDbUsage()) {
            $this->directoryDatabaseFactory->create()->deleteDirectory($path);
        }
        try {
            $this->_deleteByPath($path);
            $path = $this->getThumbnailRoot() . $this->_getRelativePathToRoot($path);
            $this->_deleteByPath($path);
        } catch (FileSystemException $e) {
            throw new LocalizedException(__('We cannot delete directory %1.', $path));
        }
    }

    /**
     * Delete by path
     *
     * @param  string $path
     * @return void
     */
    protected function _deleteByPath($path): void
    {
        $path = $this->_sanitizePath($path);
        if (!empty($path)) {
            $this->_validatePath($path);
            $this->directory->delete($this->directory->getRelativePath($path));
        }
    }

    /**
     * Sanitize path
     *
     * @param  string $path
     * @return string
     */
    protected function _sanitizePath($path): string
    {
        return rtrim(
            preg_replace(
                '~[/\\\]+~',
                '/',
                $this->directory->getDriver()->getRealPath($path)
            ),
            '/'
        );
    }

    /**
     * Is path under storage root directory
     *
     * @param  string $path
     * @return void
     * @throws LocalizedException
     */
    protected function _validatePath($path): void
    {
        $root = $this->_sanitizePath($this->cmsWysiwygImages->getStorageRoot());
        if ($root == $path) {
            throw new LocalizedException(
                __('We can\'t delete root directory %1 right now.', $path)
            );
        }
        if (strpos($path, $root) !== 0) {
            throw new LocalizedException(
                __('Directory %1 is not under storage root path.', $path)
            );
        }
    }

    /**
     * Thumbnail root directory getter
     *
     * @return string
     */
    public function getThumbnailRoot(): string
    {
        return $this->cmsWysiwygImages->getStorageRoot() . '/' . self::THUMBS_DIRECTORY_NAME;
    }

    /**
     * Get path in root storage dir
     *
     * @param  string $path
     * @return string|bool
     */
    protected function _getRelativePathToRoot($path): bool|string
    {
        return substr(
            $this->_sanitizePath($path),
            strlen($this->_sanitizePath($this->cmsWysiwygImages->getStorageRoot()))
        );
    }

    /**
     * Delete file (and its thumbnail if exists) from storage
     *
     * @param  string $target File path to be deleted
     * @return $this
     */
    public function deleteFile($target)
    {
        $relativePath = $this->directory->getRelativePath($target);
        if ($this->directory->isFile($relativePath)) {
            $this->directory->delete($relativePath);
        }
        $this->coreFileStorageDb->deleteFile($target);
        $thumb = $this->getThumbnailPath($target, true);
        $relativePathThumb = $this->directory->getRelativePath($thumb);
        if ($thumb) {
            if ($this->directory->isFile($relativePathThumb)) {
                $this->directory->delete($relativePathThumb);
            }
            $this->coreFileStorageDb->deleteFile($thumb);
        }
        return $this;
    }

    /**
     * Thumbnail path getter
     *
     * @param  string $filePath  original file path
     * @param  bool   $checkFile OPTIONAL is it necessary to check file availability
     * @return string|false
     */
    public function getThumbnailPath($filePath, $checkFile = false): bool|string
    {
        $mediaRootDir = $this->cmsWysiwygImages->getStorageRoot();

        if (strpos($filePath, $mediaRootDir) === 0) {
            $thumbPath = $this->getThumbnailRoot() . substr($filePath, strlen($mediaRootDir));

            if (!$checkFile || $this->directory->isExist($this->directory->getRelativePath($thumbPath))) {
                return $thumbPath;
            }
        }
        return false;
    }

    /**
     * Upload and resize new file
     *
     * @param string $targetPath Target directory
     * @param string|null $type       Type of storage, e.g. image, media etc.
     * @return array File info Array
     * @throws LocalizedException
     */
    public function uploadFile(string $targetPath, string $type = null): array
    {
        /** @var Uploader $uploader */
        $uploader = $this->uploaderFactory->create(['fileId' => 'image']);
        $allowed = $this->getAllowedExtensions($type);
        if ($allowed) {
            $uploader->setAllowedExtensions($allowed);
        }
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $result = $uploader->save($targetPath);

        if (!$result) {
            throw new LocalizedException(__('We can\'t upload the file right now.'));
        }
        $this->resizeFile($targetPath . '/' . $uploader->getUploadedFileName(), true);
        $result['cookie'] = [
            'name' => $this->getSession()->getName(),
            'value' => $this->getSession()->getSessionId(),
            'lifetime' => $this->getSession()->getCookieLifetime(),
            'path' => $this->getSession()->getCookiePath(),
            'domain' => $this->getSession()->getCookieDomain(),
        ];
        return $result;
    }

    /**
     * Create thumbnail for image and save it to thumbnails directory
     *
     * @param  string $source     Image path to be resized
     * @param  bool   $keepRation Keep aspect ratio or not
     * @return bool|string Resized filepath or false if errors were occurred
     */
    public function resizeFile($source, $keepRation = true)
    {
        $realPath = $this->directory->getRelativePath($source);
        if (!$this->directory->isFile($realPath) || !$this->directory->isExist($realPath)) {
            return false;
        }
        $targetDir = $this->getThumbsPath($source);
        $pathTargetDir = $this->directory->getRelativePath($targetDir);
        if (!$this->directory->isExist($pathTargetDir)) {
            $this->directory->create($pathTargetDir);
        }
        if (!$this->directory->isExist($pathTargetDir)) {
            return false;
        }
        $image = $this->imageFactory->create();
        $image->open($source);
        $image->keepAspectRatio($keepRation);
        $image->resize($this->resizeParameters['width'], $this->resizeParameters['height']);
        $dest = $targetDir . '/' . Filesystem\Io\File::getPathInfo($source, PATHINFO_BASENAME);
        $image->save($dest);
        if ($this->directory->isFile($this->directory->getRelativePath($dest))) {
            return $dest;
        }
        return false;
    }

    /**
     * Return thumbnails directory path for file/current directory
     *
     * @param  bool|string $filePath Path to the file
     * @return string
     */
    public function getThumbsPath($filePath = false)
    {
        $mediaRootDir = $this->cmsWysiwygImages->getStorageRoot();
        $thumbnailDir = $this->getThumbnailRoot();

        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            $thumbnailDir .= Magento\Framework\Filesystem\DriverInterface::getParentDirectory(
                substr($filePath, strlen($mediaRootDir)));
        }
        return $thumbnailDir;
    }

    /**
     * Storage session
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Resize images on the fly in controller action
     *
     * @param  string $filename File basename
     * @return bool|string Thumbnail path or false for errors
     */
    public function resizeOnTheFly($filename)
    {
        $path = $this->getSession()->getCurrentPath();
        if (!$path) {
            $path = $this->cmsWysiwygImages->getCurrentPath();
        }
        return $this->resizeFile($path . '/' . $filename);
    }

    /**
     * Get resize width
     *
     * @return int
     */
    public function getResizeWidth(): int
    {
        return $this->resizeParameters['width'];
    }

    /**
     * Get resize height
     *
     * @return int
     */
    public function getResizeHeight(): int
    {
        return $this->resizeParameters['height'];
    }

    /**
     * Get cms wysiwyg images helper
     *
     * @return Images|null
     */
    public function getCmsWysiwygImages(): ?Images
    {
        return $this->cmsWysiwygImages;
    }
}
