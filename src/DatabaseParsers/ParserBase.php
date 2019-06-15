<?php
namespace robertgarrigos\CodeGenerator\DatabaseParsers;

use App;
use robertgarrigos\CodeGenerator\Models\Field;
use robertgarrigos\CodeGenerator\Models\FieldMapper;
use robertgarrigos\CodeGenerator\Models\Resource;
use robertgarrigos\CodeGenerator\Support\Config;
use robertgarrigos\CodeGenerator\Support\FieldsOptimizer;
use robertgarrigos\CodeGenerator\Support\Helpers;
use robertgarrigos\CodeGenerator\Support\ResourceMapper;
use robertgarrigos\CodeGenerator\Support\Str;
use robertgarrigos\CodeGenerator\Traits\CommonCommand;
use robertgarrigos\CodeGenerator\Traits\ModelTrait;
use Exception;

abstract class ParserBase
{
    use CommonCommand, ModelTrait;

    /**
     * List of fields to be excluded from all views.
     *
     * @var array
     */
    protected $exclude = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The table name.
     *
     * @var array
     */
    protected $tableName;

    /**
     * The databasename
     *
     * @var array
     */
    protected $databaseName;

    /**
     * The locale value
     *
     * @var array
     */
    protected $locale;

    /**
     * The final fields.
     *
     * @var array
     */
    protected $fields;

    /**
     * The languages to create labels form.
     *
     * @var array
     */
    protected $languages;

    /**
     * Creates a new field instance.
     *
     * @param string $tableName
     * @param string $databaseName
     * @param array $languages
     *
     * @return void
     */
    public function __construct($tableName, $databaseName, array $languages = [])
    {
        $this->tableName = $tableName;
        $this->databaseName = $databaseName;
        $this->languages = $languages;
        $this->locale = App::getLocale();
    }

    /**
     * Gets the final fields.
     *
     * @return array
     */
    protected function getFields()
    {
        if (is_null($this->fields)) {
            $columns = $this->getColumns();

            if (empty($columns)) {
                throw new Exception('The table ' . $this->tableName . ' was not found in the ' . $this->databaseName . ' database.');
            }

            $this->fields = $this->transfer($columns);
        }

        return $this->fields;
    }

    /**
     * Gets the final resource.
     *
     * @return robertgarrigos\CodeGenerator\Models\Resource
     */
    public function getResource()
    {
        $fields = $this->getFields();
        $autoManage = $this->containsUpdateAtAndCreatedAt($fields);
        $resource = new Resource($fields, $this->getRelations(), $this->getIndexes(), $autoManage);
        $resource->setTableName($this->tableName);

        return $resource;
    }

    /**
     * Check if the given fields contains autoManagedFields
     *
     * @return robertgarrigos\CodeGenerator\Models\Resource
     */
    protected function containsUpdateAtAndCreatedAt($fields)
    {
        $autoManagedFields = array_filter($fields, function ($field) {
            return $field->isAutoManagedOnUpdate();
        });

        return count($autoManagedFields) == 2;
    }
    /**
     * Gets the final resource.
     *
     * @return robertgarrigos\CodeGenerator\Models\Resource
     */
    public function getResourceAsJson()
    {
        $resource = $this->getResource();

        return Helpers::prettifyJson($resource->toArray());
    }

    /**
     * Gets array of field after transfering each column meta into field.
     *
     * @param array $fields
     *
     * @return array
     */
    protected function transfer(array $fields)
    {
        $mappers = array_map(function ($field) {
            return new FieldMapper($field);
        }, $this->getTransfredFields($fields));

        $optimizer = new FieldsOptimizer($mappers);

        return $optimizer->optimize()->getFields();
    }

    /**
     * Get the html type for a given field.
     *
     * @param robertgarrigos\CodeGenerator\Models\Field $field
     * @param string $type
     *
     * @return string
     */
    protected function getHtmlType($type)
    {
        $map = Config::getEloquentToHtmlMap();

        if (array_key_exists($type, $map)) {
            return $map[$type];
        }

        return 'text';
    }

    /**
     * Set the html type for a given field.
     *
     * @param robertgarrigos\CodeGenerator\Models\Field $field
     * @param string $type
     *
     * @return $this
     */
    protected function setHtmlType(array &$fields)
    {
        foreach ($fields as $field) {
            $field->htmlType = $this->getHtmlType($field->getEloquentDataMethod());
        }

        return $this;
    }

    /**
     * Gets the model's name from a given table name
     *
     * @param string $tableName
     *
     * @return string
     */
    protected function getModelName($tableName)
    {
        $modelName = ResourceMapper::pluckFirst($tableName, 'table-name', 'model-name');

        return $modelName ?: $this->makeModelName($tableName);
    }

    /**
     * Make a model name from the given table name
     *
     * @param string $tableName
     *
     * @return string
     */
    protected function makeModelName($tableName)
    {
        $name = Str::singular($tableName);

        return ucfirst(camel_case($name));
    }

    /**
     * Gets column meta info from the information schema.
     *
     * @return array
     */
    abstract protected function getColumns();

    /**
     * Transfers every column in the given array to a collection of fields.
     *
     * @return array of robertgarrigos\CodeGenerator\Models\Field;
     */
    abstract protected function getTransfredFields(array $columns);

    /**
     * Get all available indexed
     *
     * @return array of robertgarrigos\CodeGenerator\Models\Index;
     */
    abstract protected function getIndexes();

    /**
     * Get all available relations
     *
     * @return array of robertgarrigos\CodeGenerator\Models\ForeignRelationship;
     */
    abstract protected function getRelations();
}
