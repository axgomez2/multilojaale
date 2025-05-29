<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';           // Aguardando pagamento
    case PROCESSING = 'processing';     // Processando pagamento
    case APPROVED = 'approved';         // Pagamento aprovado
    case DECLINED = 'declined';         // Pagamento recusado
    case REFUNDED = 'refunded';         // Pagamento reembolsado
    case CANCELED = 'canceled';         // Pagamento cancelado
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Aguardando Pagamento',
            self::PROCESSING => 'Processando',
            self::APPROVED => 'Aprovado',
            self::DECLINED => 'Recusado',
            self::REFUNDED => 'Reembolsado',
            self::CANCELED => 'Cancelado',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::PROCESSING => 'blue',
            self::APPROVED => 'green',
            self::DECLINED => 'red',
            self::REFUNDED => 'purple',
            self::CANCELED => 'gray',
        };
    }
}
