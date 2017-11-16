<?php

namespace InetStudio\Ingredients\Models;

use Spatie\Tags\HasTags;
use Cocur\Slugify\Slugify;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\Media;
use Phoenix\EloquentMeta\MetaTrait;
use InetStudio\Tags\Models\TagModel;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use InetStudio\Products\Traits\HasProducts;
use InetStudio\Comments\Models\Traits\HasComments;
use InetStudio\Statuses\Models\StatusModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Venturecraft\Revisionable\RevisionableTrait;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use InetStudio\Classifiers\Models\Traits\HasClassifiers;
use InetStudio\SimpleCounters\Traits\HasSimpleCountersTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

/**
 * InetStudio\Ingredients\Models\IngredientModel.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $content
 * @property int|null $status_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Contracts\Routing\UrlGenerator|string $href
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Media[] $media
 * @property-read \Illuminate\Database\Eloquent\Collection|\Phoenix\EloquentMeta\Meta[] $meta
 * @property \Illuminate\Database\Eloquent\Collection|\InetStudio\Products\Models\ProductModel[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property \Illuminate\Database\Eloquent\Collection|\InetStudio\Tags\Models\TagModel[] $tags
 * @property-read \InetStudio\Statuses\Models\StatusModel $status
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel findSimilarSlugs(\Illuminate\Database\Eloquent\Model $model, $attribute, $config, $slug)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Ingredients\Models\IngredientModel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel withAllProducts($products, $column = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel withAllTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel withAnyProducts($products, $column = 'id')
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel withAnyTags($tags, $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel withProducts($products, $column = 'id')
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Ingredients\Models\IngredientModel withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel withoutAnyProducts()
 * @method static \Illuminate\Database\Eloquent\Builder|\InetStudio\Ingredients\Models\IngredientModel withoutProducts($products, $column = 'id')
 * @method static \Illuminate\Database\Query\Builder|\InetStudio\Ingredients\Models\IngredientModel withoutTrashed()
 * @mixin \Eloquent
 */
class IngredientModel extends Model implements HasMediaConversions
{
    use HasTags;
    use MetaTrait;
    use Sluggable;
    use Searchable;
    use HasComments;
    use HasProducts;
    use SoftDeletes;
    use HasMediaTrait;
    use HasClassifiers;
    use RevisionableTrait;
    use SluggableScopeHelpers;
    use HasSimpleCountersTrait;

    const HREF = '/ingredient/';

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
    ];

    protected $revisionCreationsEnabled = true;

    /**
     * Отношение "один к одному" с моделью статуса.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne(StatusModel::class, 'id', 'status_id');
    }

    /**
     * Настройка полей для поиска.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $arr = array_only($this->toArray(), ['id', 'title', 'description', 'content']);

        $arr['tags'] = $this->tags->map(function ($item) {
            return array_only($item->toSearchableArray(), ['id', 'name']);
        })->toArray();

        $arr['products'] = $this->products->map(function ($item) {
            return array_only($item->toSearchableArray(), ['id', 'title']);
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
            ],
        ];
    }

    /**
     * Правила для транслита.
     *
     * @param Slugify $engine
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

    /**
     * Ссылка на ингредиент.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getHrefAttribute()
    {
        return url(self::HREF.(! empty($this->slug) ? $this->slug : $this->id));
    }

    /**
     * Возвращаем класс модели тега.
     *
     * @return string
     */
    public static function getTagClassName()
    {
        return TagModel::class;
    }

    /**
     * Регистрируем преобразования изображений.
     *
     * @param Media|null $media
     */
    public function registerMediaConversions(Media $media = null)
    {
        $quality = (config('ingredients.images.quality')) ? config('ingredients.images.quality') : 75;

        if (config('ingredients.images.conversions')) {
            foreach (config('ingredients.images.conversions') as $collection => $image) {
                foreach ($image as $crop) {
                    foreach ($crop as $conversion) {
                        $imageConversion = $this->addMediaConversion($conversion['name']);

                        if (isset($conversion['size']['width'])) {
                            $imageConversion->width($conversion['size']['width']);
                        }

                        if (isset($conversion['size']['height'])) {
                            $imageConversion->height($conversion['size']['height']);
                        }

                        if (isset($conversion['fit']['width']) && isset($conversion['fit']['height'])) {
                            $imageConversion->fit('max', $conversion['fit']['width'], $conversion['fit']['height']);
                        }

                        if (isset($conversion['quality'])) {
                            $imageConversion->quality($conversion['quality']);
                            $imageConversion->optimize();
                        } else {
                            $imageConversion->quality($quality);
                        }

                        $imageConversion->performOnCollections($collection);
                    }
                }
            }
        }
    }
}
