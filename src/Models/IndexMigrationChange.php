<?php

namespace robertgarrigos\CodeGenerator\Models;

use robertgarrigos\CodeGenerator\Models\Bases\MigrationChangeBase;
use robertgarrigos\CodeGenerator\Support\Contracts\ChangeDetector;
use robertgarrigos\CodeGenerator\Support\Contracts\JsonWriter;

class IndexMigrationChange extends MigrationChangeBase implements JsonWriter, ChangeDetector
{
    /**
     * The field to be deleted or added
     *
     * @var robertgarrigos\CodeGenerator\Models\Index
     */
    public $index;

    /**
     * Create a new field migration change instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Check whether or not the object has change value
     *
     * @return bool
     */
    public function hasChange()
    {
        foreach ($this as $key => $value) {
            if ($this->isAdded || $this->isDeleted) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get new migration change from the given index
     *
     * @param robertgarrigos\CodeGenerator\Models\Index $index
     *
     * @return robertgarrigos\CodeGenerator\Models\IndexMigrationChange
     */
    public static function getAdded(Index $index)
    {
        $change = new IndexMigrationChange();
        $change->index = $index;
        $change->isAdded = true;

        return $change;
    }

    /**
     * Get new migration change from the given index
     *
     * @param robertgarrigos\CodeGenerator\Models\Index $index
     *
     * @return robertgarrigos\CodeGenerator\Models\IndexMigrationChange
     */
    public static function getDeleted(Index $index)
    {
        $change = new IndexMigrationChange();

        $change->isDeleted = true;
        $change->index = $index;

        return $change;
    }
    /**
     * Get the migration change after comparing two given fields
     *
     * @param robertgarrigos\CodeGenerator\Models\Field $fieldA
     * @param robertgarrigos\CodeGenerator\Models\Field $fieldB
     *
     * @return robertgarrigos\CodeGenerator\Models\FieldMigrationChange
     */
    public static function compare(Field $fieldA, Field $fieldB)
    {
        $change = new FieldMigrationChange();
        $change->fromField = $fieldA;
        $change->toField = $fieldB;

        return $change;
    }

    /**
     * Return current object as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'index' => $this->getRawProperty('index'),
            'is-deleted' => $this->isDeleted,
            'is-added' => $this->isAdded,
        ];
    }
}
