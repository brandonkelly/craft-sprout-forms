<?php

namespace barrelstrength\sproutforms\fields\formfields;

use barrelstrength\sproutforms\services\Forms;
use Craft;
use craft\base\ElementInterface;
use craft\fields\Dropdown as CraftDropdown;
use craft\fields\PlainText as CraftPlainText;
use craft\helpers\Template as TemplateHelper;
use craft\base\PreviewableFieldInterface;
use barrelstrength\sproutforms\base\FormField;

/**
 *
 * @property string $svgIconPath
 * @property mixed  $settingsHtml
 * @property array  $compatibleCraftFields
 * @property array  $compatibleCraftFieldTypes
 * @property mixed  $exampleInputHtml
 */
class Invisible extends FormField implements PreviewableFieldInterface
{
    /**
     * @var bool
     */
    public $allowRequired = false;

    /**
     * @var bool
     */
    public $allowEdits;

    /**
     * @var bool
     */
    public $hideValue;

    /**
     * @var string|null
     */
    public $value;

    public static function displayName(): string
    {
        return Craft::t('sprout-forms', 'Invisible');
    }

    /**
     * @return bool
     */
    public function isPlainInput(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getSvgIconPath(): string
    {
        return '@sproutbaseicons/eye-slash.svg';
    }

    /**
     * @inheritdoc
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'sprout-forms/_components/fields/formfields/invisible/settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritdoc
     *
     * @param                       $value
     * @param ElementInterface|null $element
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $name = $this->handle;
        $inputId = Craft::$app->getView()->formatInputId($name);
        $namespaceInputId = Craft::$app->getView()->namespaceInputId($inputId);

        return Craft::$app->getView()->renderTemplate('sprout-base-fields/_components/fields/formfields/invisible/input',
            [
                'id' => $namespaceInputId,
                'name' => $name,
                'value' => $value,
                'field' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getExampleInputHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('sprout-forms/_components/fields/formfields/invisible/example',
            [
                'field' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     *
     * @param            $value
     * @param array|null $renderingOptions
     *
     * @return string
     * @throws \Throwable
     */
    public function getFrontEndInputHtml($value, array $renderingOptions = null): \Twig\Markup
    {
        $this->preProcessInvisibleValue();

        $html = '<input type="hidden" name="'.$this->handle.'">';

        return TemplateHelper::raw($html);
    }

    /**
     * @inheritdoc
     *
     * @param                       $value
     * @param ElementInterface|null $element
     *
     * @return string
     * @throws \Throwable
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\Exception
     */
    public function normalizeValue($value, ElementInterface $element = null): string
    {
        return Craft::$app->getSession()->get($this->handle) ?? '';
    }

    /**
     * @inheritdoc
     */
    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        $hiddenValue = '';

        if ($value != '') {
            $hiddenValue = $this->hideValue ? '&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;' : $value;
        }

        return $hiddenValue;
    }

    /**
     * @return string
     * @throws \Throwable
     */
    private function preProcessInvisibleValue(): string
    {
        $value = '';

        if ($this->value) {
            try {
                $value = Craft::$app->view->renderObjectTemplate($this->value, Forms::getFieldVariables());
                Craft::$app->getSession()->set($this->handle, $value);
            } catch (\Exception $e) {
                Craft::error($e->getMessage(), __METHOD__);
            }
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getCompatibleCraftFieldTypes(): array
    {
        return [
            CraftPlainText::class,
            CraftDropdown::class
        ];
    }
}
