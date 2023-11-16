<?php

namespace App\Components;

use Countable;
use ArrayAccess;
use JsonSerializable;
use IteratorAggregate;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorContract;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Facades\Request;


/**
 * Переписанный класс стандартного пагинатора
 * Добавил два метода getOffset и getLimit
 * Class Paginator
 * @package App\Components
 */
class Paginator extends AbstractPaginator implements Arrayable, ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Jsonable, LengthAwarePaginatorContract
{
    const DFEAULT_PER_PAGE = 25;

    /**
     * The total number of items before slicing.
     *
     * @var int
     */
    protected $total;

    /**
     * The last available page.
     *
     * @var int
     */
    protected $lastPage;

    /**
     * Create a new paginator instance.
     *
     * @param $total
     * @param  int $perPage
     * @param  int|null $currentPage
     * @param  array $options (path, query, fragment, pageName)
     */
    public function __construct($total, $perPage = self::DFEAULT_PER_PAGE, $currentPage = null, array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }

        $this->total = $total;
        $this->perPage = (int)$perPage;
        $this->lastPage = max((int) ceil($total / $perPage), 1);

        $this->path = '/' . Request::path();
        $this->path = $this->path !== '/' ? rtrim($this->path, '/') : $this->path;

        $this->currentPage = $this->setCurrentPage((int)$currentPage, $this->pageName);
    }

    /**
     * Get the current page for the request.
     *
     * @param  int  $currentPage
     * @param  string  $pageName
     * @return int
     */
    protected function setCurrentPage($currentPage, $pageName)
    {
        $currentPage = $currentPage ?: static::resolveCurrentPage($pageName);

        return $this->isValidPageNumber($currentPage) ? (int) $currentPage : 1;
    }

    /**
     * Render the paginator using the given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return \Illuminate\Support\HtmlString
     */
    public function links($view = null, $data = [])
    {
        return $this->render($view, $data);
    }

    /**
     * Render the paginator using the given view.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @return \Illuminate\Support\HtmlString
     */
    public function render($view = null, $data = [])
    {
        return new HtmlString(static::viewFactory()->make($view ?: static::$defaultView, array_merge($data, [
            'paginator' => $this,
            'elements' => $this->elements(),
        ]))->render());
    }

    /**
     * Get the array of elements to pass to the view.
     *
     * @return array
     */
    protected function elements()
    {
        $window = UrlWindow::make($this);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }

    /**
     * Get the total number of items being paginated.
     *
     * @return int
     */
    public function total()
    {
        return $this->total;
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return $this->currentPage() < $this->lastPage();
    }

    /**
     * Get the URL for the next page.
     *
     * @return string|null
     */
    public function nextPageUrl()
    {
        if ($this->lastPage() > $this->currentPage()) {
            return $this->url($this->currentPage() + 1);
        }
    }

    /**
     * Get the last page.
     *
     * @return int
     */
    public function lastPage()
    {
        return $this->lastPage;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'currentPage' => $this->currentPage(),
            //'firstPageUrl' => $this->url(1),
            'lastPage' => $this->lastPage(),
            //'lastPageUrl' => $this->url($this->lastPage()),
            //'nextPageUrl' => $this->nextPageUrl(),
            'perPage' => $this->perPage(),
            //'prevPageUrl' => $this->previousPageUrl(),
            'total' => $this->total(),
            'hasMorePages' => $this->hasMorePages()
        ];
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function getOffset()
    {
        $pageSize = $this->perPage();
        return $pageSize < 1 ? 0 : ($this->currentPage()-1) * $pageSize;
    }

    public function getLimit()
    {
        $pageSize = $this->perPage();
        return  $pageSize < 1 ? -1 : (int)$pageSize;
    }

}
