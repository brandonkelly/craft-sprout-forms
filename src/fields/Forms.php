<?php

namespace barrelstrength\sproutforms\fields;

use barrelstrength\sproutforms\elements\db\FormQuery;
use barrelstrength\sproutforms\elements\Form as FormElement;
use Craft;
use craft\fields\BaseRelationField;

/**
 * Forms represents a Forms field.
 */
class Forms extends BaseRelationField
{
    // Static
    // =========================================================================

    /**
     * @inheritDoc
     */
    public static function displayName(): string
    {
        return Craft::t('sprout-forms', 'Forms (Sprout Forms)');
    }

    /**
     * @inheritDoc
     */
    protected static function elementType(): string
    {
        return FormElement::class;
    }

    /**
     * @inheritDoc
     */
    public static function defaultSelectionLabel(): string
    {
        return Craft::t('sprout-forms', 'Add a form');
    }

    /**
     * @inheritDoc
     */
    public static function valueType(): string
    {
        return FormQuery::class;
    }
}
