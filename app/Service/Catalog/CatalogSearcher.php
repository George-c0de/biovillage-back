<?php

namespace App\Service\Catalog;

use App\Service\BaseSearcher;
use Illuminate\Support\Arr;

/**
 * Класс поиска по каталогу
 */
class CatalogSearcher extends BaseSearcher {

    protected $isPaginate = false;
    protected $table = 'nodes';
    protected $tableSlug = 'n';

    protected $sort = '
        ORDER BY
            n."level" ASC,
            n."order" ASC,
            n."isGroup" DESC
    ';

    public function __construct($parentId = NULL) {
        $parentIdQuery = $parentId ? ' = '. $parentId .' ' : ' IS NULL ';

        $this->with = '
            WITH RECURSIVE nodes AS (
                SELECT
                    "id",
                    "name",
                    "slug",
                    "price",
                    "parentId",
                    "isGroup",
                    "order",
                    "createdAt",
                    "updatedAt",
                    "deletedAt",
                    
                    NULL AS "parentSlug",
                    "slug" AS "pathSlug",
                    ARRAY["id"] AS "pathId",
                    1 AS "level"
                    
                FROM "catalog" AS c
                WHERE "parentId" '. $parentIdQuery .'
                UNION ALL
            
                SELECT 
                    c."id",
                    c."name",
                    c."slug",
                    c."price",
                    c."parentId",
                    c."isGroup",
                    c."order",
                    c."createdAt",
                    c."updatedAt",
                    c."deletedAt",
                    
                    n."slug",
                    concat("pathSlug", \'/\', c."slug"),
                    "pathId" || c."id",
                    n."level" + 1 AS "level"
    
                FROM "catalog" AS c
                JOIN "nodes" AS n ON c."parentId" = n."id"
            )
        ';
    }

    public function where($params)
    {
        // Сортировка по дефолту
        $this->where .= 'AND n."deletedAt" IS NULL ';

        if(boolval($params['pathSlug'] ?? false)){
            $this->where .= ' AND n."pathSlug" = ? ';
            $this->binds[] = $params['pathSlug'];
        }

        if(boolval($params['id'] ?? false)){
            $this->where .= ' AND n."id" = ? ';
            $this->binds[] = $params['id'];
        }

        // Ищем по полю slug
        if(boolval($params['slug'] ?? false)){
            $this->where .= ' AND n."slug" = ? ';
            $this->binds[] = $params['slug'];
        }

        // Ищем по полю parentId
        if(Arr::has($params, 'parentId')){
            if ($params['parentId'] == null) {
                $this->where .= ' AND n."parentId" IS NULL ';
            } else {
                $this->where .= ' AND n."parentId" = ? ';
                $this->binds[] = $params['parentId'];
            }
        }

        // Ищем по полю parentSlug
        if(boolval($params['parentSlug'] ?? false)){
            $this->where .= ' AND n."parentSlug" = ? ';
            $this->binds[] = $params['parentSlug'];
        }

        // Ищем только категории или продукты
        if(Arr::has($params, 'isGroup')){
            $this->where .= ' AND  n."isGroup" = ? ';
            $this->binds[] = $params['isGroup'];
        }

        // Ищем по массиву id
        if(boolval($params['ids'] ?? false)){
            $this->where .= ' AND  n."id" IN ('.implode(", ", $params['ids']).') ';
        }

        return $this;
    }

    /**
     * Проверка правильности пути элемента каталога
     * @param $path
     * @return bool
     */
    static function hasItemPath($path) {
        $slugLast = self::getLastSlug($path);

        $catalogSearcher = new CatalogSearcher();
        // Ищем снизу вверх по каталогу начинаем с последнего слага в пути
        $with = '
            WITH RECURSIVE nodes AS (
                SELECT
                    "id",
                    "slug",
                    "parentId",
                    "deletedAt",
                    
                    "slug" AS "pathSlug"
                    
                FROM "catalog" AS c
                WHERE "slug" = ?
                UNION ALL
            
                SELECT 
                    c."id",
                    c."slug",
                    c."parentId",
                    c."deletedAt",
                    
                    concat("pathSlug", \'/\', c."slug")
                    
                FROM "catalog" AS c
                JOIN "nodes" AS n ON c."id" = n."parentId"
            )
        ';

        $result = $catalogSearcher->with($with)
            ->addBinds($slugLast)
            ->search()
            ->get();

        // Последний элемент это корневой переворачиваем его pathSlug и проверяем
        $lastElement = $result[count($result) - 1];
        $pathSlug = $lastElement->pathSlug;
        $pathSlug = explode('/', $pathSlug);
        $pathSlug = array_reverse($pathSlug);
        $pathSlug = implode('/', $pathSlug);

        return $pathSlug == $path;
    }

    /**
     * Возвращаем последний слаг в пути path
     * @param $path
     * @return string
     */
    static function getLastSlug($path) {
        $slugArr = array_reverse(explode('/', $path));
        return $slugArr[0];
    }
}
