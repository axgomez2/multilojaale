<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case PENDING = 'pending';           // Aguardando processamento
    case PROCESSING = 'processing';     // Em processamento
    case READY = 'ready';               // Pronto para envio
    case SHIPPED = 'shipped';           // Enviado
    case IN_TRANSIT = 'in_transit';     // Em trânsito
    case DELIVERED = 'delivered';       // Entregue
    case RETURNED = 'returned';         // Devolvido
    case CANCELED = 'canceled';         // Cancelado
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Aguardando Processamento',
            self::PROCESSING => 'Em Processamento',
            self::READY => 'Pronto para Envio',
            self::SHIPPED => 'Enviado',
            self::IN_TRANSIT => 'Em Trânsito',
            self::DELIVERED => 'Entregue',
            self::RETURNED => 'Devolvido',
            self::CANCELED => 'Cancelado',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::PROCESSING => 'blue',
            self::READY => 'purple',
            self::SHIPPED => 'indigo',
            self::IN_TRANSIT => 'cyan',
            self::DELIVERED => 'green',
            self::RETURNED => 'orange',
            self::CANCELED => 'red',
        };
    }
}
