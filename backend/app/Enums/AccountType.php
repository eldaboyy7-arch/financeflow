<?php

namespace App\Enums;

enum AccountType: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case EWallet = 'e_wallet';
    case CreditCard = 'credit_card';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Tunai',
            self::Bank => 'Bank',
            self::EWallet => 'E-Wallet',
            self::CreditCard => 'Kartu Kredit',
            self::Other => 'Lainnya',
        };
    }
}
