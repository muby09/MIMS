<?php

namespace Tests\Unit;

use Tests\TestCase;

class NormalizeGsmNumberTest extends TestCase
{
    public function test_normalize_gsm_number_restores_missing_leading_zero_for_10_digit_numeric_input(): void
    {
        $this->assertSame('08012345678', normalizeGsmNumber('8012345678'));
        $this->assertSame('00000000000', normalizeGsmNumber('0000000000'));
        $this->assertSame('00000000000', normalizeGsmNumber('0'));
        $this->assertSame('08012345678', normalizeGsmNumber('08012345678'));
    }
}
