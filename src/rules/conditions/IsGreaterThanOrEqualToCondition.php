<?php

namespace barrelstrength\sproutforms\rules\conditions;

use barrelstrength\sproutforms\base\Condition;
use Craft;

/**
 *
 * @property string $label
 */
class IsGreaterThanOrEqualToCondition extends Condition
{
    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return 'is greater than or equal to';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['inputValue'], 'runValidation']
        ];
    }

    /**
     * @inheritDoc
     */
    public function runValidation()
    {
        if (!($this->inputValue >= $this->ruleValue)) {
            $this->addError('inputValue', Craft::t('sprout-forms', 'Does not validate'));
        }
    }
}