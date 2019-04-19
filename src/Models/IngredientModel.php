<?php

namespace InetStudio\Ingredients\Models;

use Cocur\Slugify\Slugify;
use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Cviebrock\EloquentSluggable\Sluggable;
use InetStudio\Meta\Models\Traits\Metable;
use InetStudio\TagsPackage\Tags\Models\Traits\HasTags;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Rating\Models\Traits\Rateable;
use InetStudio\Statuses\Models\Traits\Status;
use InetStudio\Uploads\Models\Traits\HasImages;
use InetStudio\Widgets\Models\Traits\HasWidgets;
use InetStudio\Comments\Models\Traits\HasComments;
use InetStudio\Products\Models\Traits\HasProducts;
use InetStudio\Favorites\Models\Traits\Favoritable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use InetStudio\Classifiers\Models\Traits\HasClassifiers;
use InetStudio\Meta\Contracts\Models\Traits\MetableContract;
use InetStudio\Rating\Contracts\Models\Traits\RateableContract;
use InetStudio\SimpleCounters\Models\Traits\HasSimpleCountersTrait;
use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;

class IngredientModel extends Model implements IngredientModelContract, MetableContract, HasMedia, FavoritableContract, RateableContract, Auditable
{
    use HasTags;
    use Metable;
    use Rateable;
    use Sluggable;
    use HasImages;
    use HasWidgets;
    use Searchable;
    use Favoritable;
    use HasComments;
    use HasProducts;
    use SoftDeletes;
    use HasClassifiers;
    use \OwenIt\Auditing\Auditable;
    use SluggableScopeHelpers;
    use HasSimpleCountersTrait;

    const ENTITY_TYPE = 'ingredient';
    const BASE_MATERIAL_TYPE = 'ingredient';

    protected $images = [
        'config' => 'ingredients',
        'model' => '',
    ];

    /**
     * Связанная с моделью таблица.
     *
     * @var string
     */
    protected $table = 'ingredients';

    /**
     * Атрибуты, для которых разрешено массовое назначение.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'description', 'content',
        'publish_date', 'webmaster_id', 'status_id',
    ];

    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'publish_date',
    ];

    /**
     * Should the timestamps be audited?
     *
     * @var bool
     */
    protected $auditTimestamps = true;

    /**
     * Сеттер атрибута title.
     *
     * @param $value
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = strip_tags($value);
    }

    /**
     * Сеттер атрибута slug.
     *
     * @param $value
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strip_tags($value);
    }

    /**
     * Сеттер атрибута description.
     *
     * @param $value
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = trim(str_replace("&nbsp;", ' ', strip_tags((isset($value['text'])) ? $value['text'] : (! is_array($value) ? $value : ''))));
    }

    /**
     * Сеттер атрибута content.
     *
     * @param $value
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = trim(str_replace("&nbsp;", ' ', (isset($value['text'])) ? $value['text'] : (! is_array($value) ? $value : '')));
    }

    /**
     * Сеттер атрибута publish_date.
     *
     * @param $value
     */
    public function setPublishDateAttribute($value)
    {
        $this->attributes['publish_date'] = ($value) ? Carbon::createFromFormat('d.m.Y H:i', $value) : null;
    }

    /**
     * Сеттер атрибута webmaster_id.
     *
     * @param $value
     */
    public function setWebmasterIdAttribute($value)
    {
        $this->attributes['webmaster_id'] = strip_tags($value);
    }

    /**
     * Сеттер атрибута status_id.
     *
     * @param $value
     */
    public function setStatusIdAttribute($value)
    {
        $this->attributes['status_id'] = (! $value) ? 1 : (int) $value;
    }

    /**
     * Сеттер атрибута material_type.
     *
     * @param $value
     */
    public function setMaterialTypeAttribute($value)
    {
        $this->attributes['material_type'] = ($value) ? $value : self::BASE_MATERIAL_TYPE;
        $this->images['model'] = $this->attributes['material_type'];
    }

    /**
     * Геттер атрибута href.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getHrefAttribute()
    {
        return url($this->material_type.'/'.(! empty($this->slug) ? $this->slug : $this->id));
    }

    /**
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Геттер атрибута material_type.
     *
     * @return string
     */
    public function getMaterialTypeAttribute()
    {
        return self::BASE_MATERIAL_TYPE;
    }

    use Status;

    /**
     * Настройка полей для поиска.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $arr = Arr::only($this->toArray(), ['id', 'title', 'description', 'content']);

        $arr['tags'] = $this->tags->map(function ($item) {
            return Arr::only($item->toSearchableArray(), ['id', 'name']);
        })->toArray();

        $arr['products'] = $this->products->map(function ($item) {
            return Arr::only($item->toSearchableArray(), ['id', 'title']);
        })->toArray();

        return $arr;
    }

    /**
     * Возвращаем конфиг для генерации slug модели.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
                'unique' => true,
                'includeTrashed' => true,
            ],
        ];
    }

    /**
     * Правила для транслита.
     *
     * @param Slugify $engine
     *
     * @return Slugify
     */
    public function customizeSlugEngine(Slugify $engine)
    {
        $rules = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
            'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'je', 'ю' => 'ju', 'я' => 'ja',
        ];

        $engine->addRules($rules);

        return $engine;
    }
}
