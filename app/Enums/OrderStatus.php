<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';           // Pedido criado, aguardando pagamento
    case PAYMENT_APPROVED = 'payment_approved'; // Pagamento aprovado
    case PREPARING = 'preparing';       // Em preparação
    case SHIPPED = 'shipped';           // Enviado
    case DELIVERED = 'delivered';       // Entregue
    case CANCELED = 'canceled';         // Cancelado
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Aguardando Pagamento',
            self::PAYMENT_APPROVED => 'Pagamento Aprovado',
            self::PREPARING => 'Em Preparação',
            self::SHIPPED => 'Enviado',
            self::DELIVERED => 'Entregue',
            self::CANCELED => 'Cancelado',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::PAYMENT_APPROVED => 'blue',
            self::PREPARING => 'purple',
            self::SHIPPED => 'indigo',
            self::DELIVERED => 'green',
            self::CANCELED => 'red',
        };
    }
}
