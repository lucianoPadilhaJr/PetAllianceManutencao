const API_URL_PAGAMENTOS = '/backEnd/pagamento.php';

async function obterHistoricoPagamentos() {
    const response = await fetch(`${API_URL_PAGAMENTOS}?route=historico`, {
        method: 'GET',
        credentials: 'same-origin'
    });
    const data = await response.json();
    return data;
}

function carregarHistoricoPagamentos() {
    const container = document.getElementById('historico-pagamentos');
    if (!container) return;

    obterHistoricoPagamentos().then(pagamentos => {
        if (!pagamentos || pagamentos.length === 0) {
            container.innerHTML = '<div class="notif-empty">Nenhum pagamento registrado.</div>';
            return;
        }

        var statusLabel = {
            'PAID': 'Pago',
            'PENDING': 'Pendente',
            'EXPIRED': 'Expirado',
            'REFUNDED': 'Reembolsado',
            'CANCELLED': 'Cancelado'
        };

        var statusCor = {
            'PAID': '#2e7d32',
            'PENDING': '#f57c00',
            'EXPIRED': '#d32f2f',
            'REFUNDED': '#d32f2f',
            'CANCELLED': '#666'
        };

        var statusBg = {
            'PAID': '#2e7d321a',
            'PENDING': '#f57c001a',
            'EXPIRED': '#d32f2f1a',
            'REFUNDED': '#d32f2f1a',
            'CANCELLED': '#6666661a'
        };

        var html = '<table class="pag-table"><thead><tr><th>Data</th><th>Status</th><th>Valor</th></tr></thead><tbody>';

        pagamentos.forEach(pgto => {
            var label = statusLabel[pgto.status_pagamento] || pgto.status_pagamento;
            var cor = statusCor[pgto.status_pagamento] || '#666';
            var bg = statusBg[pgto.status_pagamento] || '#6666661a';
            html += '<tr>' +
                '<td class="pag-data">' + new Date(pgto.criado_em).toLocaleDateString('pt-BR') + '</td>' +
                '<td><span class="pag-status" style="background:' + bg + ';color:' + cor + ';">' + label + '</span></td>' +
                '<td class="pag-valor">R$ ' + parseFloat(pgto.valor || 0).toFixed(2) + '</td>' +
            '</tr>';
        });

        html += '</tbody></table>';
        container.innerHTML = html;
    }).catch(error => {
        container.innerHTML = '<div class="notif-empty">Erro ao carregar hist&oacute;rico.</div>';
        console.error(error);
    });
}