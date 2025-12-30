<?php

namespace App\Filters;

/**
 * ======================================================================
 * Helper to filter queries
 * 
 * Examples
 * 
 * CURL
 * path/to/endpoint?created_at[gt]=2023-05-01&created_at[lt]=2023-05-23
 * 
 * JAVASCRIPT
 * axios.get('/endpoint', {
 *    params: {
 *     created_at: { gte: '2011-10-25', lte: '2015-01-01' },
 *     order_by: { desc: ['name', 'type'] },
 *     name: { lk: 'Joe' },
 *     likes: [{status: 'active'}, {name: 'John'}]],
 *    }
 * })
 * 
 * ORDER BY
 * ?order_by[desc]=created_at
 * ?order_by[desc][0]=number&order_by[desc][1]=id
 * 
 * LIMIT
 * ?limit=10
 * ?limit=5,10
 * ======================================================================
 */

use Illuminate\Contracts\Database\Eloquent\Builder;
use \Illuminate\Http\Request;

class Filter
{
  protected $requestParams;
  protected $builder;
  protected $take = null;
  protected $safeParams = [];
  protected $columnMap = [];
  protected $operatorMap = [
    'eq' => '=',
    'lt' => '<',
    'lte' => '<=',
    'gt' => '>',
    'gte' => '>=',
    'in' => 'in',
    'lk' => 'like',
  ];

  /**
   * ApiFilter constructor
   * @param \Illuminate\Http\Request              $request
   * @return void
   */
  public function __construct(Request $request, $model)
  {
    $this->requestParams = $request->query();
    $this->builder = $model instanceof Builder ? $model : $model::query();
    $this->wheres();
    $this->likes();
    $this->nulls();
    $this->orderBy();
    $this->limit();
    $this->trash();
  }

  /**
   * Get builder
   * @return \Illuminate\Database\Eloquent\Builder;
   */
  public function getBuilder()
  {
    return $this->builder->paginate($this->take);
  }

  /**
   * Add wheres params to builder
   * @return void
   */
  public function wheres(): void
  {
    foreach ($this->safeParams as $param => $operators) {

      if (isset($this->requestParams[$param])) {
        $query = $this->requestParams[$param];        // ex.: ['gt' => '2023-05-01', 'lt' => '2023-05-23']
        $column = $this->columnMap[$param] ?? $param; // ex.: created_at

        foreach ($operators as $operator) {           // ex.: ['eq', 'lt']
          if (isset($query[$operator])) {
            if ($operator === 'in') {
              $this->builder->whereIn($column, $query[$operator]);
            } else {
              $this->builder->where(
                $column,
                $this->operatorMap[$operator],
                $operator === 'lk' ? "%{$query[$operator]}%" : $query[$operator]
              );
            }
          }
        }
      }
    }
  }

  public function likes(): void
  {
    $params = $this->requestParams['likes'] ?? [];
    if (count($params) && array_values($params[0])[0]) {
      $this->builder->where(function ($query) use ($params) {
        foreach ($params as $param) {
          list($key, $value) = array_map(fn($key, $value) => [$key, $value], array_keys($param), $param)[0];

          if ($value) {
            $query->orWhere($key, 'like', "%{$value}%");
          }
        }
      });
    }
  }

  /**
   * Add Null and Not Null to builder
   * @return void
   */
  public function nulls(): void
  {
    $params = $this->requestParams['null'] ?? [];
    if (!is_array($params)) {
      $params = [$params];
    };

    foreach ($params as $values) {
      if (!is_array($values)) {
        $values = [$values];
      };

      foreach ($values as $value) {
        if (isset($this->safeParams[$value])) {
          $this->builder->whereNull($value);
        }
      }
    }

    $params = $this->requestParams['not_null'] ?? [];
    if (!is_array($params)) {
      $params = [$params];
    };

    foreach ($params as $values) {
      if (!is_array($values)) {
        $values = [$values];
      };

      foreach ($values as $value) {
        if (isset($this->safeParams[$value])) {
          $this->builder->whereNotNull($value);
        }
      }
    }
  }

  /**
   * Add order by to builder
   * @return  void
   */
  public function orderBy(): void
  {
    $params = $this->requestParams['order_by'] ?? [];

    foreach ($params as $key => $values) {
      if (!is_array($values)) {
        $values = [$values];
      };

      foreach ($values as $value) {
        if (isset($this->safeParams[$value])) {
          $this->builder->orderBy($value, $key);
        }
      }
    }
  }

  /**
   * Add limit to builder
   * @return  void
   */
  public function limit(): void
  {
    if (isset($this->requestParams['limit'])) {
      [$skip, $take] = explode(',', $this->requestParams['limit']) + [null, null];

      if (empty($take)) {
        $take = $skip;
        $skip = 0;
      }

      $this->take = $take;
      $this->builder->skip($skip)->take($take);
    }
  }

  /**
   * Add trash to builder
   * @return  void
   */
  public function trash(): void
  {
    if (isset($this->requestParams['trash'])) {
      $this->builder->withTrashed();
    }
  }
}
