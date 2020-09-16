<?php

namespace Cmpsr\Http\Requests\Api;

use Cmpsr\Rules\ValidComposerJson;
use Illuminate\Foundation\Http\FormRequest;

class CreatePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge(['hash' => $this->dataHash()]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data' => ['required', new ValidComposerJson],
            'hash' => ['required'],
        ];
    }

    /**
     * @return string
     */
    public function dataHash(): string
    {
        return md5($this->data());
    }

    /**
     * @return string
     */
    public function data(): string
    {
        return (string) $this->get('data');
    }
}
