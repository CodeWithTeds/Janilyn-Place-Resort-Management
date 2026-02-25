<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case PAYMONGO = 'paymongo';
    case BANK_TRANSFER = 'bank_transfer';
}
