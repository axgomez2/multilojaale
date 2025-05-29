@component('emails.layouts.base')
    @slot('title')
        Vinil Disponível: {{ $vinyl->title }}
    @endslot
    
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="color: #4F46E5; font-size: 24px; margin-bottom: 8px;">Boa notícia!</h1>
        <h2 style="color: #1F2937; font-size: 20px; margin-bottom: 16px;">Um vinil da sua lista de interesse está disponível</h2>
    </div>
    
    <div style="background-color: #F9FAFB; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td width="30%" style="vertical-align: top; padding-right: 16px;">
                    @if($vinyl->cover_image)
                        <img src="{{ asset('storage/' . $vinyl->cover_image) }}" alt="{{ $vinyl->title }}" style="width: 100%; border-radius: 4px;">
                    @else
                        <div style="width: 100%; height: 120px; background-color: #E5E7EB; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: #9CA3AF;">Sem imagem</span>
                        </div>
                    @endif
                </td>
                <td width="70%" style="vertical-align: top;">
                    <h3 style="color: #111827; font-size: 18px; margin-top: 0; margin-bottom: 8px;">{{ $vinyl->title }}</h3>
                    <p style="color: #4B5563; margin-top: 0; margin-bottom: 8px;">{{ $artistNames }}</p>
                    
                    @if($vinyl->vinylSecs->isNotEmpty() && $vinyl->vinylSecs->where('active', true)->isNotEmpty())
                        @php
                            $price = $vinyl->vinylSecs->where('active', true)->first()->price ?? 0;
                        @endphp
                        <p style="color: #4F46E5; font-weight: bold; font-size: 16px; margin-top: 16px; margin-bottom: 8px;">
                            R$ {{ number_format($price, 2, ',', '.') }}
                        </p>
                    @endif
                    
                    <p style="color: #059669; font-weight: bold; margin-top: 16px; margin-bottom: 0;">
                        Disponível agora!
                    </p>
                </td>
            </tr>
        </table>
    </div>
    
    <div style="text-align: center; margin-bottom: 32px;">
        <p style="color: #4B5563; margin-bottom: 20px;">
            Este item da sua lista de interesse está disponível para compra. 
            Não perca a oportunidade, a quantidade pode ser limitada!
        </p>
        
        <a href="{{ $url }}" style="display: inline-block; background-color: #4F46E5; color: #ffffff; text-decoration: none; text-align: center; border-radius: 6px; padding: 12px 24px; font-weight: 600; margin-bottom: 16px;">
            Ver Vinil
        </a>
        
        <p style="color: #6B7280; font-size: 14px;">
            Se você não conseguir clicar no botão acima, copie e cole o link abaixo no seu navegador:
        </p>
        
        <p style="color: #4F46E5; font-size: 14px; word-break: break-all;">
            {{ $url }}
        </p>
    </div>
    
    <div style="border-top: 1px solid #E5E7EB; padding-top: 16px; text-align: center;">
        <p style="color: #6B7280; font-size: 14px;">
            Você está recebendo este e-mail porque adicionou este vinil à sua lista de interesse.
            Se você não quer mais receber notificações deste tipo, remova o item da sua Wantlist.
        </p>
    </div>
@endcomponent
