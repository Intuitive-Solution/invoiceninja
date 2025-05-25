<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2024. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Http\Requests\Resource;

use App\Http\Requests\Request;
use App\Models\Resource;
use App\Utils\Traits\ChecksEntityStatus;
use App\Utils\Traits\MakesHash;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends Request
{
    use MakesHash;
    use ChecksEntityStatus;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user->can('edit', $this->resource);
    }

    public function rules()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $rules = [];

        $rules['name'] = 'sometimes|required|string|max:255';
        $rules['description'] = 'nullable|string';
        $rules['rate'] = ['sometimes', 'bail', 'nullable', 'numeric', 'min:0', 'max:99999999999999'];

        return $this->globalRules($rules);
    }

    public function prepareForValidation()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $input = $this->all();

        $input = $this->decodePrimaryKeys($input);

        if (array_key_exists('rate', $input) && is_null($input['rate'])) {
            $input['rate'] = 0;
        }

        $this->replace($input);
    }

    public function messages()
    {
        return [
            'name.required' => 'The resource name is required.',
            'rate.numeric' => 'The rate must be a valid number.',
            'rate.min' => 'The rate must be at least 0.',
        ];
    }
} 