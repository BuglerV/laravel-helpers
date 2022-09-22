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
                             
        // Получаем все ID моделей отношений...
        $ids = self::getIds($baseModels, $relations);
        
        // Создаем славарь для последующего распределения по моделям...
        $dictionary = $relModel::find($ids)->keyBy('id');

		// Распределяем модели отношений по базовым моделям...
        self::distribution($baseModels, $relations, $dictionary);
    }
	
    /**
     * Загружаем все однотипные модели отношений за одно обращение к базе данных.
	 * При этом подгружаем отношения для каждой загруженной модели.
     *
     * @param \Illuminate\Support\Collection|Model $baseModels
     * @param string $relModel
     * @param array $relations
     *     Массив вида: [
     *         'Название отношения' => 'Колонка с ID в базе данных',
     *     ]
	 * @param string $with
     *
     * @return void
     */
    public static function loadWith($baseModels, string $relModel, array $relations, string $with)
    {
        // Дальше ожидаем коллекцию моделей...
        $baseModels = is_a($baseModels,Collection::class)
                             ? $baseModels
                             : collect([$baseModels]);
        
        // Получаем все ID моделей отношений...
        $ids = self::getIds($baseModels, $relations);
        
        // Создаем славарь для последующего распределения по моделям...
        $dictionary = $relModel::find($ids)->load($with);
        $dictionary = $dictionary->keyBy('id');

		// Распределяем модели отношений по базовым моделям...
        self::distribution($baseModels, $relations, $dictionary);
    }
	
    /**
     * Получаем все ID моделей отношений...
     *
	 * @param  \Illuminate\Support\Collection $baseModels
	 * @param  array $relations
	 *
     * @return void
     */
	protected static function getIds(Collection $baseModels, array $relations)
	{
        $columns = array_values($relations);
        
        return $baseModels->map(function($model) use ($columns){
            return $model->only($columns);
        })->flatten();
	}
	
    /**
     * Распределяем модели отношений по базовым моделям...
     *
	 * @param  \Illuminate\Support\Collection $baseModels
	 * @param  array $relations
	 * @param  \Illuminate\Support\Collection $dictionary
	 *
     * @return void
     */
	protected static function distribution(Collection $baseModels, array $relations, Collection $dictionary)
	{
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