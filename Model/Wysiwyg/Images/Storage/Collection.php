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

namespace MageINIC\BannerSlider\Model\Wysiwyg\Images\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\Data\Collection\Filesystem as CollectionFilesystem;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;

/**
 * Wysiwyg Images storage collection
 */
class Collection extends CollectionFilesystem
{
    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @param EntityFactory $entityFactory
     * @param Filesystem    $filesystem
     */
    public function __construct(
        EntityFactory $entityFactory,
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
        parent::__construct($entityFactory);
    }

    /**
     * Generate Row
     *
     * @param string $filename
     * @return array
     * @throws FileSystemException
     */
    protected function _generateRow($filename)
    {
        $filename = $filename !== null ?
            preg_replace('~[/\\\]+(?<![htps?]://)~', '/', $filename) : '';
        $path = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        try {
            $mtime = $path->stat($path->getRelativePath($filename))['mtime'];
        } catch (FileSystemException $e) {
            $mtime = 0;
        }
        return [
            'filename' => rtrim($filename, '/'),
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            'basename' => basename($filename),
            'mtime' => $mtime
        ];
    }
}
