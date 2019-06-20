<?php

namespace barrelstrength\sproutforms\base;

use barrelstrength\sproutforms\fields\formfields\Number;
use barrelstrength\sproutforms\fields\formfields\SingleLine;
use barrelstrength\sproutforms\elements\Form;
use barrelstrength\sproutforms\SproutForms;
use Craft;
use craft\base\SavableComponent;
use craft\fields\Date as CraftDate;
use craft\fields\Dropdown as CraftDropdown;
use craft\fields\Number as CraftNumber;
use craft\fields\PlainText as CraftPlainText;
use yii\base\InvalidConfigException;

/**
 * Class IntegrationType
 *
 * @package Craft
 *
 * @property string      $fieldMappingSettingsHtml
 * @property void        $settingsHtml
 * @property array       $sourceFormFields
 * @property void        $customSourceFormFields
 * @property null|string $updateTargetFieldsAction
 * @property string      $updateSourceFieldsAction
 * @property Form        $form
 * @property string      $type
 */
abstract class Integration extends SavableComponent implements IntegrationInterface
{
    // Traits
    // =========================================================================

    use IntegrationTrait;

    protected $successMessage = 'Success';

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return SproutForms::$app->forms->getFormById($this->formId);
    }

    /**
     * @inheritdoc
     */
    public function settingsAttributes(): array
    {
        $attributes = parent::settingsAttributes();
        $attributes[] = 'fieldMapping';

        return $attributes;
    }

    /**
     * @return string|null
     */
    public function getUpdateTargetFieldsAction()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function submit(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getSuccessMessage()
    {
        return $this->successMessage;
    }

    /**
     * This action should return an form fields array
     */
    public function getUpdateSourceFieldsAction()
    {
        return 'sprout-forms/integrations/get-form-fields';
    }

    /**
     * Prepares the $fieldMapping array based on the current form fields and any existing settings
     *
     * @throws InvalidConfigException
     */
    public function prepareFieldMapping()
    {
        $indexedFieldMapping = [];
        $oldFieldMapping = $this->fieldMapping;

        // Update our stored settings to use the sourceFormField handle as the key of our array
        if ($oldFieldMapping !== null) {
            foreach ($oldFieldMapping as $oldFieldMap) {
                $indexedFieldMapping[$oldFieldMap['sourceFormField']] = $oldFieldMap['targetIntegrationField'];
            }
        }

        $newFieldMapping = [];
        $sourceFormFields = $this->getSourceFormFields();

        // Loop through the current list of form fields and match them to any existing fieldMapping settings
        foreach ($sourceFormFields as $sourceFormField) {
            $newFieldMapping[] = [
                'sourceFormField' => $sourceFormField->handle,
                'targetIntegrationField' => $indexedFieldMapping[$sourceFormField->handle] ?? ''
            ];
        }

        $this->fieldMapping = $newFieldMapping;
    }

    /**
     * @inheritDoc
     */
    public function resolveFieldMapping(): array
    {
        return $this->fieldMapping ?? [];
    }

    /**
     * Returns the HTML where a user will prepare a field mapping
     *
     * @return string|null
     */
    public function getFieldMappingSettingsHtml()
    {
        return null;
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getSourceFormFields(): array
    {
        $sourceFormFieldsData = [
            [
                'name' => Craft::t('sprout-forms', 'Form ID'),
                'handle' => 'formId',
                'compatibleCraftFields' => [
                    CraftPlainText::class,
                    CraftDropdown::class,
                    CraftNumber::class
                ],
                'type' => Number::class
            ],
            [
                'name' => Craft::t('sprout-forms', 'Entry ID'),
                'handle' => 'id',
                'compatibleCraftFields' => [
                    CraftPlainText::class,
                    CraftDropdown::class,
                    CraftNumber::class
                ],
                'type' => Number::class
            ],
            [
                'name' => Craft::t('sprout-forms', 'Title'),
                'handle' => 'title',
                'compatibleCraftFields' => [
                    CraftPlainText::class,
                    CraftDropdown::class
                ],
                'type' => SingleLine::class
            ],
            [
                'name' => Craft::t('sprout-forms', 'Date Created'),
                'handle' => 'dateCreated',
                'compatibleCraftFields' => [
                    CraftDate::class
                ],
                'type' => SingleLine::class
            ],
            [
                'name' => Craft::t('sprout-forms', 'IP Address'),
                'handle' => 'ipAddress',
                'compatibleCraftFields' => [
                    CraftPlainText::class,
                    CraftDropdown::class
                ],
                'type' => SingleLine::class
            ],
            [
                'name' => Craft::t('sprout-forms', 'User Agent'),
                'handle' => 'userAgent',
                'compatibleCraftFields' => [
                    CraftPlainText::class
                ],
                'type' => SingleLine::class
            ]
        ];

        $sourceFormFields = [];

        foreach ($sourceFormFieldsData as $sourceFormFieldData) {
            /** @var FormField $fieldInstance */
            $fieldInstance = new $sourceFormFieldData['type']();
            $fieldInstance->name = $sourceFormFieldData['name'];
            $fieldInstance->handle = $sourceFormFieldData['handle'];
            $fieldInstance->setCompatibleCraftFields($sourceFormFieldData['compatibleCraftFields']);
            $sourceFormFields[] = $fieldInstance;
        }

        $fields = $this->getForm()->getFields();

        if (count($fields)) {
            foreach ($fields as $field) {
                $sourceFormFields[] = $field;
            }
        }

        return $sourceFormFields;
    }

    public function getCustomSourceFormFields()
    {

    }

    /**
     * Prepares a list of the Form Fields from the current form that a user can choose to map to an endpoint
     *
     * @param bool $addOptGroup
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function getFormFieldsAsMappingOptions($addOptGroup = false): array
    {
        $options = [];

        if ($addOptGroup) {
            $options[] = ['optgroup' => Craft::t('sprout-forms', 'Default Fields')];
        }

        $options = array_merge($options, [
            [
                'label' => Craft::t('sprout-forms', 'Form ID'),
                'value' => 'formId',
                'compatibleCraftFields' => [
                    CraftPlainText::class,
                    CraftDropdown::class,
                    CraftNumber::class
                ],
                'fieldType' => SingleLine::class
            ],
            [
                'label' => Craft::t('sprout-forms', 'Entry ID'),
                'value' => 'id',
                'compatibleCraftFields' => [
                    CraftPlainText::class,
                    CraftDropdown::class,
                    CraftNumber::class
                ],
                'fieldType' => SingleLine::class
            ],
            [
                'label' => Craft::t('sprout-forms', 'Title'),
                'value' => 'title',
                'compatibleCraftFields' => [
                    CraftPlainText::class,
                    CraftDropdown::class
                ],
                'fieldType' => SingleLine::class
            ],
            [
                'label' => Craft::t('sprout-forms', 'Date Created'),
                'value' => 'dateCreated',
                'compatibleCraftFields' => [
                    CraftDate::class
                ],
                'fieldType' => SingleLine::class
            ],
            [
                'label' => Craft::t('sprout-forms', 'IP Address'),
                'value' => 'ipAddress',
                'compatibleCraftFields' => [
                    CraftPlainText::class,
                    CraftDropdown::class
                ],
                'fieldType' => SingleLine::class
            ],
            [
                'label' => Craft::t('sprout-forms', 'User Agent'),
                'value' => 'userAgent',
                'compatibleCraftFields' => [
                    CraftPlainText::class
                ],
                'fieldType' => SingleLine::class
            ]
        ]);

        $fields = $this->getForm()->getFields();

        if (count($fields)) {
            if ($addOptGroup) {
                $options[] = [
                    'optgroup' => Craft::t('sprout-forms', 'Custom Fields')
                ];
            }

            foreach ($fields as $field) {
                $options[] = [
                    'label' => $field->name,
                    'value' => $field->handle,
                    'compatibleCraftFields' => $field->getCompatibleCraftFieldTypes(),
                    'fieldType' => get_class($field)
                ];
            }
        }

        return $options;
    }
}

