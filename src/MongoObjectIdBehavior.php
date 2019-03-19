<?php

namespace rise\mongoObjectBehavior;

use MongoDB\BSON\ObjectId;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;

/**
 * Class MongoObjectIdBehavior
 * @package common\behaviors
 */
class MongoObjectIdBehavior extends Behavior
{
    public $attribute;
    public $skipValue;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeInsert'
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->attribute)) {
            throw new InvalidConfigException('Either "attribute" property must be specified.');
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeInsert()
    {
        /** @var BaseActiveRecord $owner */
        $owner = $this->owner;
        foreach ((array)$this->attribute as $attribute) {
            $attributeValue = $owner->getAttribute($attribute);
            if ($attributeValue === $this->skipValue) {
                continue;
            }
            if (is_array($attributeValue)) {
                foreach ($attributeValue as $key => $value) {
                    $attributeValue[$key] = new ObjectId($value);
                }
                if ($attributeValue) {
                    $owner->setAttribute($attribute, $attributeValue);
                }
            } else {
                if ($value = $owner->getAttribute($attribute)) {
                    $owner->setAttribute($attribute, new ObjectId($value));
                }
            }
        }
    }
}