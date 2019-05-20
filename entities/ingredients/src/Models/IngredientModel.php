<?php

namespace InetStudio\IngredientsPackage\Ingredients\Models;

use Illuminate\Support\Arr;
use Laravel\Scout\Searchable;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;
use InetStudio\Rating\Models\Traits\Rateable;
use InetStudio\Uploads\Models\Traits\HasImages;
use InetStudio\Widgets\Models\Traits\HasWidgets;
use InetStudio\Comments\Models\Traits\HasComments;
use InetStudio\Favorites\Models\Traits\Favoritable;
use InetStudio\MetaPackage\Meta\Models\Traits\HasMeta;
use InetStudio\TagsPackage\Tags\Models\Traits\HasTags;
use InetStudio\Classifiers\Models\Traits\HasClassifiers;
use InetStudio\AdminPanel\Base\Models\Traits\SluggableTrait;
use InetStudio\StatusesPackage\Statuses\Models\Traits\Status;
use InetStudio\AdminPanel\Base\Models\Traits\Scopes\BuildQueryScopeTrait;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;

/**
 * Class IngredientModel.
 */
class IngredientModel extends Model implements IngredientModelContract
{
    use HasTags;
    use HasMeta;
    use Rateable;
    use Auditable;
    use Sluggable;
    use HasImages;
    use HasWidgets;
    use Searchable;
    use Favoritable;
    use HasComments;
    use SoftDeletes;
    use HasClassifiers;
    use SluggableTrait;
    use BuildQueryScopeTrait;

    /**
     * Тип сущности.
     */
    const ENTITY_TYPE = 'ingredient';

    /**
     * Тип материала.
     */
    const BASE_MATERIAL_TYPE = 'ingredient';

    /**
     * Should the timestamps be audited?
     *
     * @var bool
     */
    protected $auditTimestamps = true;

    /**
     * Настройки для генерации изображений.
     *
     * @var array
     */
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
        'title',
        'slug',
        'description',
        'content',
        'publish_date',
        'webmaster_id',
        'status_id',
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
     * Настройка полей для поиска.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $arr = Arr::only($this->toArray(), ['id', 'title', 'description', 'content']);

        $arr['tags'] = $this->tags->map(
            function ($item) {
                return Arr::only($item->toSearchableArray(), ['id', 'name']);
            }
        )->toArray();

        $arr['products'] = $this->products->map(
            function ($item) {
                return Arr::only($item->toSearchableArray(), ['id', 'title']);
            }
        )->toArray();

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
     * Загрузка модели.
     */
    protected static function boot()
    {
        parent::boot();

        self::$buildQueryScopeDefaults['columns'] = [
            'id',
            'slug',
            'title',
        ];

        self::$buildQueryScopeDefaults['relations'] = [
            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(
                    [
                        'id',
                        'model_id',
                        'model_type',
                        'collection_name',
                        'file_name',
                        'disk',
                        'mime_type',
                        'custom_properties',
                        'responsive_images',
                    ]
                );
            },

            'tags' => function ($query) {
                $query->select(['id', 'name', 'slug']);
            },

            'counters' => function ($query) {
                $query->select(['countable_id', 'countable_type', 'type', 'counter']);
            },

            'products' => function ($query) {
                $query->select(['id', 'title', 'brand'])
                    ->with(
                        [
                            'media' => function ($query) {
                                $query->select(
                                    ['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']
                                );
                            },
                            'links' => function ($linksQuery) {
                                $linksQuery->select(['id', 'product_id', 'link']);
                            },
                        ]
                    );
            },

            'status' => function ($query) {
                $query->select(['id', 'name', 'alias', 'color_class']);
            },
        ];
    }

    /**
     * Сеттер атрибута title.
     *
     * @param $value
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута slug.
     *
     * @param $value
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = trim(strip_tags($value));
    }

    /**
     * Сеттер атрибута description.
     *
     * @param $value
     */
    public function setDescriptionAttribute($value)
    {
        $value = (isset($value['text'])) ? $value['text'] : (! is_array($value) ? $value : '');

        $this->attributes['description'] = trim(str_replace('&nbsp;', ' ', strip_tags($value)));
    }

    /**
     * Сеттер атрибута content.
     *
     * @param $value
     */
    public function setContentAttribute($value)
    {
        $value = (isset($value['text'])) ? $value['text'] : (! is_array($value) ? $value : '');

        $this->attributes['content'] = trim(str_replace('&nbsp;', ' ', $value));
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
        $this->attributes['webmaster_id'] = trim(strip_tags($value));
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
     * Геттер атрибута type.
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return self::ENTITY_TYPE;
    }

    /**
     * Геттер атрибута href.
     *
     * @return string
     */
    public function getHrefAttribute()
    {
        return url($this->material_type.'/'.(! empty($this->slug) ? $this->slug : $this->id));
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
}