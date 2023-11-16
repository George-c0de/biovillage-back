<?php

namespace App\Service\Images;
use App\Helpers\Utils;
use Illuminate\Http\Request;
use App\Components\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Базовый класс для кастомного поиска
 */
class BaseSearcher {
    //
    protected $DEFAULT_LIMIT; // Устанавливается в setCount
    protected $DEFAULT_OFFSET = 0;

    protected $connection = 'main';

    // Сортировка
    protected $DEFAULT_SORT;
    protected $DEFAULT_SORT_DIRECT = 'desc';
    protected $addSort = '';

    protected $isPaginate = true;

    protected $with = '';

    protected $result;
    protected $table;
    protected $tableSlug = 'main';
    protected $select = '*';
    protected $where = ' WHERE 1=1 ';
    protected $join = ' ';
    protected $binds = [];
    protected $sortBinds = [];
    protected $count;
    protected $pager;
    protected $sort;

    //Число элементов
    protected function setCount() {
        $sql = $this->with;
        $sql .= $this->getSqlCount();

        $sql .= $this->join;
        $sql .= $this->where;
        $binds = $this->binds;

        $row = (array)DB::connection($this->connection)->selectOne($sql, $binds);
        $this->count = intval($row['cnt'] ?? 0);

        $this->DEFAULT_LIMIT = $this->DEFAULT_LIMIT ? $this->DEFAULT_LIMIT : $this->count;
    }

    public function getSqlCount()
    {
        $sql = '
            SELECT COUNT('.$this->tableSlug.'.id) as cnt 
            FROM '.$this->table.' AS '.$this->tableSlug.'
        ';

        return $sql;
    }

    //Пагинатор
    private function makePager()
    {
        $this->pager = new Paginator(
            $this->count
        );
    }

    //Сортировка
    private function makeSort(&$sql, &$binds, $sortBinds)
    {
        if($this->sort){
            $sql .= ' '.$this->sort.' ';
            $binds = array_merge($binds, $sortBinds);
        }
    }

    //Устанавливаем LIMIT и OFFSET
    private function makeLimit(&$sql, &$binds)
    {
        $sql .= ' LIMIT ? OFFSET ? ';

        $binds[] = (int) $this->DEFAULT_LIMIT;
        $binds[] = (int) $this->DEFAULT_OFFSET;
    }

    //Поиск
    public function search() {

        if($this->isPaginate){
            $this->setCount();
            $this->makePager();
        }


        $sql = $this->with;
        $sql .= '
            SELECT 
                '.$this->select.'
            FROM "'.$this->table.'" AS '.$this->tableSlug.'
        ';

        $sql .= $this->join;

        $sql .= $this->where;
        $binds = $this->binds;

        $this->makeSort($sql, $binds, $this->sortBinds);
        if($this->isPaginate){
            $this->makeLimit($sql, $binds);
        }

        $self = clone $this;
        $self->result = DB::connection($this->connection)->select($sql, $binds);

        return $self;
    }

    //Поиск по id
    public function searchOne($id) {

        $sql = '
            SELECT 
                '.$this->select.'
            FROM '.$this->table.' '.$this->tableSlug.'
        ';
        $sql .= $this->join;
        $sql .= ' WHERE '.$this->tableSlug.'.id = ' . $id;

        $item = DB::connection($this->connection)->select($sql);

        $self = clone $this;
        $self->result = $item;

        return $self;
    }

    //Устанавливаем with
    public function with($str)
    {
        $this->with = ' '.$str.' ';
        return $this;
    }

    //Устанавливаем binds
    public function addBinds($value)
    {
        if(is_array($value)){
            $this->binds = array_merge($this->binds, $value);
        } else {
            $this->binds[] = $value;
        }

        return $this;
    }

    //Устанавливаем фильтрацию
    public function where($any)
    {
        $this->where = ' WHERE 1=1 ' . $any;
        return $this;
    }

    //Устанавливаем офсет
    public function offset($offset){
        $this->DEFAULT_OFFSET = $offset;
        return $this;
    }

    //Устанавливаем лимит
    public function limit($limit){
        $this->DEFAULT_LIMIT = $limit;
        return $this;
    }

    //Устанавливаем выбор нужных полей
    public function select($select)
    {
        $this->select = $select;

        return $this;
    }

    // Устанавливаем имя и слаг таблицы
    public function table($table, $tableSlug) {
        $this->table = $table;
        $this->tableSlug = $tableSlug;

        return $this;
    }

    //Устанавливаем выбор нужных полей
    public function addSelect($select)
    {
        if($this->select != '*'){
            $this->select .= ',
            '.$select;
        }

        $this->select = $select;

        return $this;
    }

    //Возвращаем результаты поиска
    public function get()
    {
        $result = $this->result;
        $result = Utils::objectToArray($result);
        return $result;
    }

    //Возвращаем первый результат поиска
    public function first()
    {
        $result = Arr::has($this->result, 0) ? $this->result[0] : [];
        $result = Utils::objectToArray($result);
        return $result;
    }

    /**
     * Возвращаем объект пагинатора
     * @return Paginator
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * @param bool $value
     * @return BaseSearcher
     */
    public function setIsPaginate(bool $value) {
        $this->isPaginate = $value;
        return $this;
    }

    /**
     * Возвращаем пагинатор в массиве для API
     * @return Paginator
     */
    public function getApiPager()
    {
        return $this->pager->toArray();
    }

    /**
     * Возвращаем число записей c учетом where
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
