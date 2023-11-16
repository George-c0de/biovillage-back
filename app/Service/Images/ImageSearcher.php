<?php

namespace App\Service\Images;


/**
 * Class ImageSearcher
 * @package App\Service\Images
 */
class ImageSearcher extends BaseSearcher {
    protected $table = 'images';
    protected $tableSlug = 'i';
    protected $join = '';

    public function where($params = [])
    {
        // Поиск по удаленным записям
        if(array_key_exists('isTrashed', $params) && is_bool($params['isTrashed'])) {
            if ($params['isTrashed']) {
                $this->where .= ' AND i."deletedAt" IS NOT NULL';
            } else {
                $this->where .= ' AND i."deletedAt" IS NULL';
            }
        } else {
            $this->where .= ' AND i."deletedAt" IS NULL';
        }

        // Поиск по удаленным
        if(array_key_exists('isDeleted', $params) && is_bool($params['isDeleted'])) {
            $this->where .= ' AND i."isDeleted" = ?';
            $this->binds[] = $params['isDeleted'];
        } else {
            $this->where .= ' AND i."isDeleted" = ?';
            $this->binds[] = false;
        }

        // Поиск по entityIds
        if(boolval($params['ids'] ?? false)){
            $this->where .= ' AND ( 1=1 ';
            foreach ($params['ids'] as $id) {
                $this->where .= ' OR i."entityId" = ?';
                $this->binds[] = $id;
            }
            $this->where .= ') ';
        }

        return $this;
    }

    /**
     * Добавление кастомных джойнов
     * @param $join
     * @return $this
     */
    public function join($join) {
        $this->join .= $join;
        return $this;
    }
}
