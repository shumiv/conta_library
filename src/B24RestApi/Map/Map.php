<?php

namespace conta\B24RestApi\Map;

use Bitrix24\Bitrix24;

class Map
{
    const LIST_FIELD_OPTIONS_PROPERTY = "DISPLAY_VALUES_FORM";

    const CRM_LIST_FIELD_OPTIONS_PROPERTY = "items";

    const TASKS_LIST_FIELD_OPTIONS_PROPERTY = "values";

    private Bitrix24 $b24App;

    /**
     * @var array [
     *      $listId1 => [
     *          "fields" => [
     *              $fieldId1 => $fieldName1,
     *              $fieldName1 => $fieldId1
     *          ],
     *          "properties" => [
     *              $fieldId1 => [
     *                  $propertyId1 => $propertyName1,
     *                  $propertyName1 => $propertyId1
     *              ]
     *          ]
     *      ]
     *  ]
     */
    private array $lists;

    private array $crmDeal;

    private array $tasksTask;

    /**
     * @param array<array> $listMap a list contains associative arrays with 'view' and 'key' properties
     * @return array<array> an associative array which contains $listMap's 'view' and 'key' properties either
     *      as properties and as keys [$key1 => $view1, $view1 => $key1, $key2 => $view2, $view2 => $key2]
     */
    static public function composeAssociativePropertyMap(array $listMap): array
    {
        foreach ($listMap as $fieldId => $properties) {
            foreach ($properties as $property) {
                $associativeMap[$fieldId][$property['key']] = $property['view'];
                $associativeMap[$fieldId][$property['view']] = $property['key'];
            }
        }
        return $associativeMap ?? [];
    }

    public function __construct(Bitrix24 $b24App)
    {
        $this->b24App = $b24App;
    }

    public function getListMap(int $listId): array
    {
        return $this->lists[$listId] ??= $this->extractListMap($listId);
    }

    public function getBPListMap(int $listId): array
    {
        return $this->lists[$listId] ??= $this->extractBPListMap($listId);
    }

    public function getCrmDealListMap(): array
    {
        return $this->crmDeal ??= $this->extractCrmDealListMap();
    }

    public function getTasksTaskListMap(): array
    {
        return $this->tasksTask ??= $this->extractTasksTaskListMap();
    }

    private function extractListMap($listId): array
    {
        $fields = $this->extractListFields($listId);
        return $this->makeListMap($fields);
    }

    private function extractListFields(int $listId): array
    {
        $method = "lists.field.get";
        $params = ["IBLOCK_TYPE_ID" => "lists", "IBLOCK_ID" => $listId];
        return $this->b24App->call($method, $params)['result'];
    }

    private function extractBPListMap($listId): array
    {
        $fields = $this->extractBPListFields($listId);
        return $this->makeListMap($fields);
    }

    private function extractBPListFields(int $listId): array
    {
        $method = "lists.field.get";
        $params = ["IBLOCK_TYPE_ID" => "bitrix_processes", "IBLOCK_ID" => $listId];
        return $this->b24App->call($method, $params)['result'];
    }

    private function makeListMap(array $fields): array
    {
        $fieldsMap = [];
        $propertiesMap = [];
        foreach ($fields as $fieldId => $field) {
            $name = $field['NAME'];
            $fieldsMap[] = ['key' => $fieldId, 'view' => $name];
            if ($this->isThereProperties($field, self::LIST_FIELD_OPTIONS_PROPERTY)) {
                foreach ($field[self::LIST_FIELD_OPTIONS_PROPERTY] as $propertyId => $propertyName) {
                    $propertiesMap[$fieldId][] = ['key' => $propertyId, 'view' => $propertyName];
                }
            }
        }
        return ["fields" => $fieldsMap, "properties" => $propertiesMap];
    }

    private function isThereProperties(array $field, string $property): bool
    {
        return isset($field[$property])
            && gettype($field[$property]) === "array"
            && ! empty($field[$property]);
    }

    private function extractCrmDealListMap(): array
    {
        $fields = $this->extractCrmDealListFields();
        $maps = $this->makeCrmMaps($fields);
        $maps['properties'] = $maps['properties'] + $this->extractExtraCrmProperties();
        return $maps;
    }

    private function extractExtraCrmProperties(): array
    {
        $categoriesRaw = $this->extractCrmDealCategories();
        $category = $this->makeCrmExtraPropertiesMap($categoriesRaw, 'ID', 'NAME');
        $categoriesIds = array_map(fn ($categoryRaw) => $categoryRaw['ID'], $categoriesRaw);
        $stage = $this->extractCrmDealCategoriesStages($categoriesIds);
        $type = $this->extractCrmDealCategoriesTypes();
        return [
            'CATEGORY_ID' => $category,
            'STAGE_ID' => $stage,
            'TYPE_ID' => $type
        ];
    }

    private function extractCrmDealCategories(): array
    {
        $method = 'crm.dealcategory.list';
        $params = [
            'order' => ["SORT" => "ASC"],
            'filter' => ["IS_LOCKED" => "N"],
		    'select' => ["ID", "NAME", "SORT" ],
        ];
        return $this->b24App->call($method, $params)['result'];
    }

    private function extractCrmDealCategoriesStages(array $categoriesIds): array
    {
        $categoriesIds[] = 0;
        $stages = [];
        $method = 'crm.dealcategory.stage.list';
        foreach ($categoriesIds as $id) {
            $params = ['id' => $id];
            $stages = [
                ...$stages,
                ...$this->b24App->call($method, $params)['result']
            ];
        }
        return $this->makeCrmExtraPropertiesMap($stages, 'STATUS_ID', 'NAME');
    }

    private function extractCrmDealCategoriesTypes() {
        $method = 'crm.status.list';
        $params = [
            'filter' => ["ENTITY_ID" => "DEAL_TYPE"]
        ];
        $types = $this->b24App->call($method, $params)['result'];
        return $this->makeCrmExtraPropertiesMap($types, 'STATUS_ID', 'NAME');
    }

    private function makeCrmExtraPropertiesMap(array $properties, string $key, string $view): array
    {
        return array_map(
            fn ($property) => ['key' => $property[$key], 'view' => $property[$view]],
            $properties
        );
    }

    private function extractCrmDealListFields()
    {
        $method = "crm.deal.fields";
        return $this->b24App->call($method)['result'];
    }

    private function makeCrmMaps(array $fields): array
    {
        $fieldsMap = [];
        $propertiesMap = [];
        foreach ($fields as $fieldId => $field) {
            $name = $field['title'];
            $fieldsMap[] = ['key' => $fieldId, 'view' => $name];
            if ($this->isThereProperties($field, self::CRM_LIST_FIELD_OPTIONS_PROPERTY)) {
                foreach ($field[self::CRM_LIST_FIELD_OPTIONS_PROPERTY] as $property) {
                    $propertiesMap[$fieldId][] = ['key' => $property['ID'], 'view' => $property['VALUE']];
                }
            }
        }
        return ["fields" => $fieldsMap, "properties" => $propertiesMap];
    }

    private function extractTasksTaskListMap()
    {
        $fields = $this->extractTasksTaskListFields();
        return $this->makeTasksMap($fields);
    }

    private function extractTasksTaskListFields(): array
    {
        $method = "tasks.task.getFields";
        return $this->b24App->call($method)['result']['fields'];
    }

    private function makeTasksMap(array $fields): array
    {
        $fieldsMap = [];
        $propertiesMap = [];
        foreach ($fields as $snakeStyleId => $field) {
            if (! isset($field['title'])) continue;
            $camelStyleId = $this->snakeToCamel($snakeStyleId);
            $name = $field['title'];
            $fieldsMap[] = ['key' => $camelStyleId, 'view' => $name];
            if ($this->isThereProperties($field, self::TASKS_LIST_FIELD_OPTIONS_PROPERTY)) {
                foreach ($field[self::TASKS_LIST_FIELD_OPTIONS_PROPERTY] as $id => $view) {
                    $propertiesMap[$camelStyleId][] = ['key' => $id, 'view' => $view];
                }
            }
        }
        return ["fields" => $fieldsMap, "properties" => $propertiesMap];
    }

    private function snakeToCamel(string $snake): string
    {
        $underlineAndLetter = "/(_)(\\w)/";
        return preg_replace_callback(
            $underlineAndLetter,
            fn ($matches) => strtoupper($matches[2]),
            strtolower($snake)
        );
    }
}