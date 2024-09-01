<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NationalCode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Ensure the input is exactly 10 digits
        if (!preg_match('/^\d{10}$/', $value)) {
            $fail('کد ملی باید دقیقاً ۱۰ رقم باشد.');
            return;
        }

        // Check if all digits are the same
        if (preg_match('/(\d)\1{9}/', $value)) {
            $fail('فیلد نمی‌تواند از تمام ارقام مشابه تشکیل شده باشد.');
            return;
        }

        // Calculate the sum of the first 9 digits multiplied by their respective weights
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += ((int) $value[$i]) * (10 - $i);
        }

        // Calculate the remainder of the sum mod 11
        $remainder = $sum % 11;

        // Get the last digit of the code
        $lastDigit = (int) $value[9];

        // Validate the national code based on the remainder
//        if (($remainder < 2 && $lastDigit != $remainder) || ($remainder >= 2 && $lastDigit != (11 - $remainder))) {
//            $fail('فیلد یک کد ملی معتبر ایرانی نیست.');
//        }
    }


}
