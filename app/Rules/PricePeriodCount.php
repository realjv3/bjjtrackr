<?php

namespace App\Rules;

use App\Enums\PricePeriod;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class PricePeriodCount implements Rule, DataAwareRule
{
    /**
     * All the data under validation.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $valid = false;
        foreach ($this->data['prices'] as $price) {

            $valid = match($price['period']) {

                PricePeriod::DAY->value => $value <= 365,
                PricePeriod::WEEK->value => $value <= 52,
                PricePeriod::MONTH->value => $value <= 12,
                PricePeriod::YEAR->value => $value === 1,
                default => false,
            };
        }
        return $valid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The billing period must be less than 1 year.';
    }
}
