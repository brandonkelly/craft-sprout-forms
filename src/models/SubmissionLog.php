<?php

namespace barrelstrength\sproutforms\models;

use barrelstrength\sproutforms\base\Integration;
use barrelstrength\sproutforms\SproutForms;
use craft\base\Model;
use Craft;
use craft\errors\MissingComponentException;
use yii\base\InvalidConfigException;

/**
 *
 * @property Integration|null $integration
 */
class SubmissionLog extends Model
{
    /**
     * @var int|null ID
     */
    public $id;

    /**
     * @var int|null
     */
    public $entryId;

    /**
     * @var int
     */
    public $integrationId;

    /**
     * @var bool
     */
    public $success;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $dateCreated;

    /**
     * @var string
     */
    public $dateUpdated;

    /**
     * @var string
     */
    public $uid;

    /**
     * Use the translated section name as the string representation.
     *
     * @inheritdoc
     */
    public function __toString()
    {
        return Craft::t('sprout-forms', $this->id);
    }

    /**
     * @return Integration|null
     * @throws MissingComponentException
     * @throws InvalidConfigException
     */
    public function getIntegration()
    {
        return SproutForms::$app->integrations->getIntegrationById($this->integrationId);
    }
}