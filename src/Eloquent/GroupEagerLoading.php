<?php

namespace Buglerv\LaravelHelpers\Eloquent;

use Illuminate\Support\Collection;

class GroupEagerLoading
{
    /**
     * Загружаем все однотипные модели отношений за
     * одно обращение к базе данных...
     *
     * @param \Illuminate\Support\Collection|Model $baseModels
     * @param string $relModel
     * @param array $relations
     *     Массив вида: [
     *         'Название отношения' => 'Колонка с ID в базе данных',
     *     ]
     *
     * @return void
     */
    public static function load($baseModels, string $relModel, array $relations)
    {
        // Дальше ожидаем коллекцию моделей...
        $baseModels = is_a($baseModels,Collection::class)
                             ? $baseModels
                             : collect([$baseModels]);
                             
        $columns = array_values($relations);
        
        // Получаем все ID моделей отношений...
        $ids = $baseModels->map(function($model) use ($columns){
            return $model->only($columns);
        })->flatten();
        
        // Создаем славарь для последующего распределения по моделям...
        $dictionary = $relModel::find($ids)->keyBy('id');

        // Распределяем модели отношений по базовым моделям...
        foreach($baseModels as $model){
            foreach($relations as $relation => $column){
                if(!$id = $model->{$column}){
                    continue;
                }
                $model->setRelation($relation,$dictionary[$id]);
            }
        }
    }
}