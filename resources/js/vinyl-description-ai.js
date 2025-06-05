/**
 * Vinyl Description AI Helper
 * 
 * Este script adiciona funcionalidades de IA para gerar descrições 
 * e traduções para os vinis na área administrativa.
 */
document.addEventListener('DOMContentLoaded', function() {
    const generateDescriptionBtn = document.getElementById('generate-description');
    const translateDescriptionBtn = document.getElementById('translate-description');
    const notesTextarea = document.getElementById('notes');
    const loadingIndicator = document.getElementById('ai-loading');
    
    if (!generateDescriptionBtn || !translateDescriptionBtn || !notesTextarea) {
        return;
    }
    
    // Botão para gerar descrição usando IA
    generateDescriptionBtn.addEventListener('click', async function() {
        // Coletar dados do formulário para enviar para a API
        const title = document.getElementById('title')?.value || '';
        const artists = document.getElementById('artists')?.value || '';
        const year = document.getElementById('release_year')?.value || '';
        const genre = document.getElementById('genre')?.value || '';
        const condition = document.getElementById('condition')?.value || '';
        const buyPrice = document.getElementById('buy_price')?.value || '';
        const promoPrice = document.getElementById('promotional_price')?.value || '';
        
        // Verificar se temos informações suficientes
        if (!title || !artists) {
            alert('Por favor, informe pelo menos o título e o artista para gerar uma descrição.');
            return;
        }
        
        try {
            loadingIndicator.classList.remove('hidden');
            
            const response = await fetch('/api/admin/vinyls/generate-description', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title,
                    artists,
                    year,
                    genre,
                    condition,
                    buyPrice,
                    promoPrice
                })
            });
            
            if (!response.ok) {
                throw new Error('Erro ao gerar descrição');
            }
            
            const data = await response.json();
            
            if (data.description) {
                notesTextarea.value = data.description;
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Não foi possível gerar a descrição. Por favor, tente novamente mais tarde.');
        } finally {
            loadingIndicator.classList.add('hidden');
        }
    });
    
    // Botão para traduzir descrição para português
    translateDescriptionBtn.addEventListener('click', async function() {
        const currentText = notesTextarea.value.trim();
        
        if (!currentText) {
            alert('Por favor, adicione algum texto para traduzir.');
            return;
        }
        
        try {
            loadingIndicator.classList.remove('hidden');
            
            const response = await fetch('/api/admin/vinyls/translate-description', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    text: currentText
                })
            });
            
            if (!response.ok) {
                throw new Error('Erro ao traduzir texto');
            }
            
            const data = await response.json();
            
            if (data.translation) {
                notesTextarea.value = data.translation;
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Não foi possível traduzir o texto. Por favor, tente novamente mais tarde.');
        } finally {
            loadingIndicator.classList.add('hidden');
        }
    });
});
