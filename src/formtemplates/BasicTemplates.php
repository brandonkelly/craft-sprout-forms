<?php

namespace barrelstrength\sproutforms\formtemplates;

use barrelstrength\sproutforms\base\FormTemplates;
use Craft;

/**
 * Class BasicTemplates
 */
class BasicTemplates extends FormTemplates
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return Craft::t('sprout-forms', 'Basic Templates (Sprout, Legacy)');
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return Craft::getAlias('@barrelstrength/sproutforms/templates/_components/formtemplates/basic');
    }
}



