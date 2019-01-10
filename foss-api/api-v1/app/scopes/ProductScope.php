<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Model;

class ProductScope implements ScopeInterface {

  public function apply(Builder $builder, Model $model) {
    $builder->where('post_type', '=', 'product');
  }

  public function remove(Builder $builder, Model $model) { // you don't need this }
  }

}
