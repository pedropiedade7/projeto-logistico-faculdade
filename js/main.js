// LogiSystem - Scripts frontend

function applyMask(el, maskFn) {
    el.addEventListener('input', function () {
        const start = this.selectionStart;
        this.value = maskFn(this.value);
        this.setSelectionRange(start, start);
    });
}

function maskCep(v) {
    return v.replace(/\D/g, '').slice(0, 8).replace(/^(\d{5})(\d{1,3})$/, '$1-$2');
}

function maskCpfCnpj(v) {
    const digits = v.replace(/\D/g, '').slice(0, 14);
    if (digits.length <= 11) {
        return digits
            .replace(/^(\d{3})(\d)/, '$1.$2')
            .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
            .replace(/\.(\d{3})(\d)/, '.$1-$2');
    }
    return digits
        .replace(/^(\d{2})(\d)/, '$1.$2')
        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1/$2')
        .replace(/(\d{4})(\d)/, '$1-$2');
}

function maskPhone(v) {
    const d = v.replace(/\D/g, '').slice(0, 11);
    if (d.length <= 10) {
        return d.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
    }
    return d.replace(/^(\d{2})(\d{5})(\d{0,4})$/, '($1) $2-$3');
}

async function fetchCep(cep, form) {
    const digits = cep.replace(/\D/g, '');
    if (digits.length !== 8) return;

    try {
        const res = await fetch(`https://viacep.com.br/ws/${digits}/json/`);
        const data = await res.json();

        if (data.erro) {
            showToast('CEP não encontrado.', 'warning');
            return;
        }

        const setVal = (name, val) => {
            const el = form ? form.querySelector(`[name="${name}"]`) : document.querySelector(`[name="${name}"]`);
            if (el && val) el.value = val;
        };

        setVal('logradouro', data.logradouro);
        setVal('bairro', data.bairro);
        setVal('cidade', data.localidade);
        setVal('uf', data.uf);

        showToast('Endereço preenchido pelo CEP!', 'info');

        const numEl = form ? form.querySelector('[name="numero"]') : document.querySelector('[name="numero"]');
        if (numEl) numEl.focus();

    } catch (err) {
        showToast('Erro ao consultar o CEP.', 'danger');
    }
}

function showToast(msg, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${type} border-0 show`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${msg}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    container.appendChild(toastEl);
    setTimeout(() => {
        toastEl.classList.remove('show');
        setTimeout(() => toastEl.remove(), 300);
    }, 3500);
}

function confirmarExclusao(url, itemNome) {
    const modalEl = document.getElementById('modalExcluir');
    if (!modalEl) {
        if (confirm(`Tem certeza que deseja excluir "${itemNome}"?`)) {
            window.location.href = url;
        }
        return;
    }

    const descEl = document.getElementById('modalExcluirDesc');
    const btnEl  = document.getElementById('modalExcluirBtn');

    if (descEl) descEl.textContent = `Tem certeza que deseja excluir "${itemNome}"? Esta ação não pode ser desfeita.`;
    if (btnEl)  btnEl.href = url;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function initFreteForm() {
    const btnAdd    = document.getElementById('btn-add-produto');
    const tbody     = document.getElementById('lista-produtos-body');
    const tmpl      = document.getElementById('tmpl-linha-produto');
    const formFrete = document.getElementById('form-frete');

    if (!btnAdd || !tbody || !tmpl) return;

    let rowCount = 0;

    function addRow() {
        const clone = tmpl.content.cloneNode(true);
        const row   = clone.querySelector('tr');

        row.querySelectorAll('[name*="INDEX"]').forEach(input => {
            input.name = input.name.replace('INDEX', rowCount);
        });

        row.querySelector('.btn-remover-linha').addEventListener('click', function () {
            if (tbody.querySelectorAll('tr').length <= 1) {
                showToast('O frete precisa ter ao menos um produto.', 'warning');
                return;
            }
            row.remove();
            recalcTotals();
        });

        const selectProd = row.querySelector('.select-produto');
        const inputQtd   = row.querySelector('.input-qtd');

        function updateRow() {
            const opt   = selectProd.options[selectProd.selectedIndex];
            const peso  = parseFloat(opt.dataset.peso  || 0);
            const valor = parseFloat(opt.dataset.valor || 0);
            const qtd   = parseInt(inputQtd.value, 10) || 0;

            const colPeso  = row.querySelector('.col-peso');
            const colValor = row.querySelector('.col-valor');
            const colSub   = row.querySelector('.col-subtotal');

            if (selectProd.value) {
                colPeso.textContent  = (peso * qtd).toFixed(3).replace('.', ',') + ' kg';
                colValor.textContent = 'R$ ' + valor.toFixed(2).replace('.', ',');
                colSub.textContent   = 'R$ ' + (valor * qtd).toFixed(2).replace('.', ',');
            } else {
                colPeso.textContent  = '—';
                colValor.textContent = '—';
                colSub.textContent   = '—';
            }

            recalcTotals();
        }

        selectProd.addEventListener('change', updateRow);
        inputQtd.addEventListener('input', updateRow);

        tbody.appendChild(clone);
        rowCount++;
        recalcTotals();
    }

    function recalcTotals() {
        let totalValor = 0;

        tbody.querySelectorAll('tr').forEach(row => {
            const sel = row.querySelector('.select-produto');
            const qtd = parseInt(row.querySelector('.input-qtd')?.value, 10) || 0;
            if (sel && sel.value) {
                const opt   = sel.options[sel.selectedIndex];
                const valor = parseFloat(opt.dataset.valor || 0);
                totalValor += valor * qtd;
            }
        });

        const resumoEl = document.getElementById('resumo-valor-total');
        if (resumoEl) {
            resumoEl.textContent = 'R$ ' + totalValor.toFixed(2).replace('.', ',');
        }
    }

    btnAdd.addEventListener('click', addRow);
    addRow();

    if (formFrete) {
        formFrete.addEventListener('submit', function (e) {
            const linhas = tbody.querySelectorAll('tr');
            if (linhas.length === 0) {
                e.preventDefault();
                showToast('Adicione ao menos um produto ao frete.', 'danger');
                return;
            }
            let valid = true;
            linhas.forEach(row => {
                const sel = row.querySelector('.select-produto');
                if (!sel.value) {
                    sel.classList.add('is-invalid');
                    valid = false;
                } else {
                    sel.classList.remove('is-invalid');
                }
            });
            if (!valid) {
                e.preventDefault();
                showToast('Selecione um produto para cada linha.', 'danger');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        applyMask(cepInput, maskCep);
        cepInput.addEventListener('blur', function () {
            fetchCep(this.value, this.closest('form'));
        });
        const btnBusca = document.getElementById('btn-buscar-cep');
        if (btnBusca) {
            btnBusca.addEventListener('click', function () {
                fetchCep(cepInput.value, cepInput.closest('form'));
            });
        }
    }

    const cpfCnpjInput = document.getElementById('cpf_cnpj');
    if (cpfCnpjInput) {
        applyMask(cpfCnpjInput, maskCpfCnpj);
    }

    const telInput = document.getElementById('telefone');
    if (telInput) {
        applyMask(telInput, maskPhone);
    }

    initFreteForm();
});
