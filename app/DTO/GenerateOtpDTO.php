<?php

namespace App\DTO;

use App\Models\OtpCode;

class GenerateOtpDTO
{
    public function __construct(
        public readonly string $otp_code,
        public readonly OtpCode $otp_record,
    ){}
}
