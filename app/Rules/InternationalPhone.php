<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InternationalPhone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        $validated = false;

        // Remove common separators
        $number = preg_replace("/[\s\-\(\)]+/", "", $value);

        $patterns = [
            '/^\+1\d{10}$/',              // USA, Canada
            '/^\+44\d{10}$/',             // UK
            '/^\+61[2-478]\d{8}$/',       // Australia
            '/^\+81\d{9,10}$/',           // Japan
            '/^\+91[6-9]\d{9}$/',         // India
            '/^\+86(1[3-9])\d{8}$/',      // China mobile
            '/^\+33[67]\d{8}$/',          // France mobile
            '/^\+49(1[5-7])\d{8,9}$/',    // Germany mobile
            '/^\+39\d{9,10}$/',           // Italy
            '/^\+7\d{10}$/',              // Russia
            '/^\+9665\d{8}$/',            // Saudi Arabia
            '/^\+234[789][01]\d{8}$/',    // Nigeria
            '/^\+55\d{10,11}$/',          // Brazil
            '/^\+27\d{9}$/',              // South Africa
            '/^\+8801[3-9]\d{8}$/',       // Bangladesh
            '/^\+3519[1236]\d{7}$/',      // Portugal mobile
            '/^\+9715[024568]\d{7}$/',    // UAE mobile
            '/^\+90(5\d{9})$/',           // Turkey
            '/^\+34[67]\d{8}$/',          // Spain mobile
            '/^\+62[8123]\d{6,10}$/',     // Indonesia
            '/^\+63\d{10}$/',             // Philippines
            '/^\+9779[78]\d{8}$/',        // Nepal
            '/^\+[1-9]\d{6,14}$/',        // E.164 fallback
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $number)) {
                $validated = true;
                break;
            }
        }

        if (!$validated) {
            $fail("The :attribute must be a valid international phone number.");
        }
    }
}
