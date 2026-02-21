<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueDomain implements ValidationRule
{
    public function __construct(
        protected $ignoreId = null
    ) {}

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $fullDomain = $value . config('tenancy.central_domain_suffix');

        $query = DB::table('domains')
            ->where('domain', $fullDomain);

        if ($this->ignoreId) {
            $query->where('tenant_id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail(__('This domain is already taken.'));
        }
    }
}
