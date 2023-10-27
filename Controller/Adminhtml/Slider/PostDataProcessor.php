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
use Magento\Framework\Filter\FilterInput;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Model\Layout\Update\Validator;
use Magento\Framework\View\Model\Layout\Update\ValidatorFactory;
use MageINIC\BannerSlider\Model\Slider\DomValidationState;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Config\Dom\ValidationSchemaException;

/**
 * BannerSlider Class PostDataProcessor
 */
class PostDataProcessor
{
    /**
     * @var Date
     */
    protected Date $dateFilter;

    /**
     * @var ValidatorFactory
     */
    protected ValidatorFactory $validatorFactory;

    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManager;

    /**
     * @var DomValidationState
     */
    private $validationState;

    /**
     * @param Date $dateFilter
     * @param ManagerInterface $messageManager
     * @param ValidatorFactory $validatorFactory
     * @param DomValidationState|null $validationState
     */
    public function __construct(
        Date               $dateFilter,
        ManagerInterface   $messageManager,
        ValidatorFactory   $validatorFactory,
        DomValidationState $validationState = null
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
        $this->validatorFactory = $validatorFactory;
        $this->validationState = $validationState;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    public function filter($data): array
    {
        $filterRules = [];
        foreach (['custom_theme_from', 'custom_theme_to'] as $dateField) {
            if (!empty($data[$dateField])) {
                $inputFilter = new FilterInput(
                    [$dateField],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
            }
        }
        return $data;
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool Return FALSE if some item is invalid
     */
    public function validate(array $data): bool
    {
        if (!empty($data['layout_update_xml']) || !empty($data['custom_layout_update_xml'])) {
            /** @var $layoutXmlValidator Validator */
            $layoutXmlValidator = $this->validatorFactory->create(
                ['validationState' => $this->validationState]
            );

            if (!$this->validateData($data, $layoutXmlValidator)) {
                $validatorMessages = $layoutXmlValidator->getMessages();
                foreach ($validatorMessages as $message) {
                    $this->messageManager->addErrorMessage($message);
                }
                return false;
            }
        }
        return true;
    }

    /**
     * Check if required fields is not empty
     *
     * @param array $data
     * @return bool
     */
    public function validateRequireEntry(array $data): bool
    {
        $requiredFields = [
            'title' => __('Page Title'),
            'stores' => __('Store View'),
            'status' => __('Status')
        ];
        $errorNo = true;
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($requiredFields)) && $value == '') {
                $errorNo = false;
                $this->messageManager->addError(
                    __('To apply changes you should fill in hidden required "%1" field', $requiredFields[$field])
                );
            }
        }
        return $errorNo;
    }

    /**
     * Validate data, avoid cyclomatic complexity
     *
     * @param array $data
     * @param Validator $layoutXmlValidator
     * @return bool
     */
    private function validateData(array $data, Validator $layoutXmlValidator): bool
    {
        try {
            if (!empty($data['layout_update_xml']) && !$layoutXmlValidator->isValid($data['layout_update_xml'])) {
                return false;
            }
            if (!empty($data['custom_layout_update_xml']) &&
                !$layoutXmlValidator->isValid($data['custom_layout_update_xml'])
            ) {
                return false;
            }
        } catch (ValidationException $e) {
            return false;
        } catch (ValidationSchemaException $e) {
            return false;
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e);
            return false;
        }
        return true;
    }
}
