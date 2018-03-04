<?php

namespace InetStudio\Ingredients\Http\Requests\Back;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use InetStudio\Uploads\Validation\Rules\CropSize;
use InetStudio\Ingredients\Contracts\Http\Requests\Back\SaveIngredientRequestContract;

/**
 * Class SaveIngredientRequest.
 */
class SaveIngredientRequest extends FormRequest implements SaveIngredientRequestContract
{
    /**
     * Определить, авторизован ли пользователь для этого запроса.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Сообщения об ошибках.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'meta.title.max' => 'Поле «Title» не должно превышать 255 символов',
            'meta.description.max' => 'Поле «Description» не должно превышать 255 символов',
            'meta.keywords.max' => 'Поле «Keywords» не должно превышать 255 символов',

            'meta.og:title.max' => 'Поле «og:itle» не должно превышать 255 символов',
            'meta.og:description.max' => 'Поле «og:description» не должно превышать 255 символов',

            'og_image.crop.default.json' => 'Область отображения должна быть представлена в виде JSON',

            'title.required' => 'Поле «Заголовок» обязательно для заполнения',
            'title.max' => 'Поле «Заголовок» не должно превышать 255 символов',
            'title.unique' => 'Такое значение поля «Заголовок» уже существует',

            'slug.required' => 'Поле «URL» обязательно для заполнения',
            'slug.alpha_dash' => 'Поле «URL» может содержать только латинские символы, цифры, дефисы и подчеркивания',
            'slug.max' => 'Поле «URL» не должно превышать 255 символов',
            'slug.unique' => 'Такое значение поля «URL» уже существует',

            'preview.crop.default.required' => 'Необходимо выбрать область отображения',
            'preview.crop.default.json' => 'Область отображения должна быть представлена в виде JSON',
            'preview.description.max' => 'Поле «Описание» не должно превышать 255 символов',
            'preview.copyright.max' => 'Поле «Copyright» не должно превышать 255 символов',
            'preview.alt.required' => 'Поле «Alt» обязательно для заполнения',
            'preview.alt.max' => 'Поле «Alt» не должно превышать 255 символов',

            'tags.array' => 'Поле «Теги» должно содержать значение в виде массива',

            'publish_date.date_format' => 'Поле «Время публикации» должно быть в формате дд.мм.гггг чч:мм',
        ];
    }

    /**
     * Правила проверки запроса.
     *
     * @param Request $request
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'meta.title' => 'max:255',
            'meta.description' => 'max:255',
            'meta.keywords' => 'max:255',
            'meta.og:title' => 'max:255',
            'meta.og:description' => 'max:255',

            'og_image.crop.default' => [
                'nullable', 'json',
                new CropSize(968, 475, 'min', ''),
            ],

            'title' => 'required|max:255|unique:ingredients,title,'.$request->get('ingredient_id'),
            'slug' => 'required|alpha_dash|max:255|unique:ingredients,slug,'.$request->get('ingredient_id'),

            'preview.crop.default' => [
                'required', 'nullable', 'json',
                new CropSize(300, 280, 'min', 'По умолчанию'),
            ],
            'preview.crop.3_2' => [
                'nullable', 'json',
                new CropSize(768, 512, 'min', '3x2'),
            ],
            'preview.crop.3_4' => [
                'nullable', 'json',
                new CropSize(384, 512, 'min', '3x4'),
            ],
            'preview.description' => 'max:255',
            'preview.copyright' => 'max:255',
            'preview.alt' => 'required|max:255',

            'tags' => 'array',

            'publish_date' => 'nullable|date_format:d.m.Y H:i',
        ];
    }
}
