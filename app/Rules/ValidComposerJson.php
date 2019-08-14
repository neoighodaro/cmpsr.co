<?php

namespace Cmpsr\Rules;

use JsonSchema\Validator;
use Illuminate\Contracts\Validation\Rule;

class ValidComposerJson implements Rule
{
    /**
     * @var \JsonSchema\Validator
     */
    protected $validator;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->validator = new Validator;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = json_decode($value);

        $filePath = 'file://' . storage_path('app') . '/composer.schema.json';

        $this->validator->validate($value, (object) ['$ref' => $filePath]);

        return $this->validator->isValid();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $error = collect($this->validator->getErrors())->first();

        return sprintf("[%s] %s", $error['property'], $error['message']);
    }
}
