    </main>

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Mini ERP</h5>
                    <p>Sistema de vendas online com gestão de estoque e pedidos.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 Mini ERP. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Verificar se jQuery carregou
        if (typeof jQuery === 'undefined') {
            console.error('❌ jQuery não foi carregado!');
            alert('Erro: jQuery não foi carregado. Recarregue a página.');
        } else {
            console.log('✅ jQuery carregado com sucesso!', jQuery.fn.jquery);
        }
        
        // Funções globais para carrinho
        function adicionarAoCarrinho(produtoId, variacaoId, quantidade = 1) {
            console.log('🛒 adicionarAoCarrinho() chamada');
            console.log('📦 Parâmetros:', {produtoId, variacaoId, quantidade});
            
            if (typeof $ === 'undefined') {
                console.error('❌ jQuery não está disponível');
                alert('Erro: jQuery não está disponível. Recarregue a página.');
                return;
            }
            
            console.log('✅ jQuery está disponível');
            
            const url = '/mini_erp/?controller=Carrinho&action=adicionar';
            const data = {
                produto_id: produtoId,
                variacao_id: variacaoId,
                quantidade: quantidade
            };
            
            console.log('🌐 URL:', url);
            console.log('📊 Dados:', data);
            console.log('🚀 Enviando requisição...');
            
            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                dataType: 'json'
            })
            .done(function(response) {
                console.log('✅ Resposta recebida:', response);
                if (response && response.success) {
                    console.log('🎉 Sucesso!');
                    showAlert('success', response.message);
                    atualizarContadorCarrinho(response.carrinho_quantidade);
                } else {
                    console.log('❌ Falha na operação:', response);
                    showAlert('danger', response.message || 'Erro desconhecido');
                }
            })
            .fail(function(xhr, status, error) {
                console.error('💥 Erro AJAX:', {xhr, status, error});
                console.error('📝 Response text:', xhr.responseText);
                console.error('🔢 Status code:', xhr.status);
                showAlert('danger', 'Erro ao adicionar produto ao carrinho: ' + error);
            })
            .always(function() {
                console.log('🏁 Requisição finalizada');
            });
        }

        function removerDoCarrinho(produtoId, variacaoId) {
            $.post('/mini_erp/?controller=Carrinho&action=remover', {
                produto_id: produtoId,
                variacao_id: variacaoId
            })
            .done(function(response) {
                if (response && response.success) {
                    showAlert('success', response.message);
                    atualizarContadorCarrinho(response.carrinho_quantidade);
                    location.reload(); // Recarregar página do carrinho
                } else {
                    showAlert('danger', response.message || 'Erro ao remover produto');
                }
            })
            .fail(function(xhr, status, error) {
                showAlert('danger', 'Erro ao remover produto do carrinho: ' + error);
            });
        }

        function atualizarQuantidade(produtoId, variacaoId, quantidade) {
            $.post('/mini_erp/?controller=Carrinho&action=atualizar', {
                produto_id: produtoId,
                variacao_id: variacaoId,
                quantidade: quantidade
            })
            .done(function(response) {
                if (response && response.success) {
                    atualizarContadorCarrinho(response.carrinho_quantidade);
                    $('#subtotal').text('R$ ' + response.subtotal.toFixed(2).replace('.', ','));
                    $('#frete').text('R$ ' + response.frete.toFixed(2).replace('.', ','));
                    $('#total').text('R$ ' + response.total.toFixed(2).replace('.', ','));
                } else {
                    showAlert('danger', response.message || 'Erro ao atualizar quantidade');
                }
            })
            .fail(function(xhr, status, error) {
                showAlert('danger', 'Erro ao atualizar carrinho: ' + error);
            });
        }

        function atualizarContadorCarrinho(quantidade) {
            const badge = $('.cart-badge');
            if (quantidade > 0) {
                if (badge.length) {
                    badge.text(quantidade);
                } else {
                    $('.nav-link:contains("Carrinho")').append(`<span class="cart-badge">${quantidade}</span>`);
                }
            } else {
                badge.remove();
            }
        }

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('main').prepend(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }

        // Buscar CEP
        function buscarCep(cep) {
            if (cep.length === 8) {
                $.get('/mini_erp/?controller=Api&action=buscarCep&cep=' + cep)
                .done(function(response) {
                    if (response && response.success) {
                        const data = response.data;
                        $('#endereco').val(data.logradouro);
                        $('#bairro').val(data.bairro);
                        $('#cidade').val(data.localidade);
                        $('#uf').val(data.uf);
                        $('#numero').focus();
                    } else {
                        showAlert('warning', response.message || 'CEP não encontrado');
                    }
                })
                .fail(function(xhr, status, error) {
                    showAlert('danger', 'Erro ao buscar CEP: ' + error);
                });
            }
        }

        // Aplicar máscara de CEP
        $(document).ready(function() {
            $('input[name="cep"]').on('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length <= 8) {
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    this.value = value;
                    
                    // Buscar CEP automaticamente quando tiver 8 dígitos
                    const cepLimpo = value.replace(/\D/g, '');
                    if (cepLimpo.length === 8) {
                        buscarCep(cepLimpo);
                    }
                }
            });
            
            // Script específico para página de produto
            if ($('#form-comprar').length > 0) {
                console.log('🛒 Página de produto detectada');
                initProdutoPage();
            }
        });
        
        // Funções específicas da página de produto
        function initProdutoPage() {
            // Obter preço base da página
            const precoText = $('.price').text().replace('R$ ', '').replace(',', '.');
            const precoBase = parseFloat(precoText) || 0;
            
            console.log('💰 Preço base detectado:', precoBase);
            
            // Verificar se as funções existem
            if (typeof showAlert !== 'function') {
                console.error('❌ Função showAlert não encontrada');
                window.showAlert = function(type, message) {
                    alert(message);
                };
            }
            
            // Aguardar um pouco para garantir que o DOM está pronto
            setTimeout(function() {
                // Verificar se é produto com variação padrão única (campo hidden)
                if ($('#variacao-hidden').length > 0) {
                    console.log('🎯 Produto padrão único detectado');
                    // Extrair estoque do texto da div alert
                    const estoqueText = $('.alert-info').text();
                    const estoqueMatch = estoqueText.match(/(\d+)\s*unidades/);
                    const estoque = estoqueMatch ? parseInt(estoqueMatch[1]) : 0;
                    
                    console.log('📦 Estoque detectado:', estoque);
                    
                    // Configurar interface
                    $('#estoque-disponivel').text(estoque);
                    $('#quantidade').attr('max', estoque).val(1);
                    
                    // Habilitar botão se há estoque
                    const botao = $('#form-comprar button[type="submit"]');
                    if (estoque > 0) {
                        botao.prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                        console.log('✅ Botão habilitado automaticamente - estoque:', estoque);
                    } else {
                        botao.prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                        console.log('❌ Botão desabilitado - sem estoque');
                    }
                    return;
                }
                
                // Verificar quantas variações existem no dropdown
                const variacoes = $('#variacao option:not([disabled]):not([value=""])');
                
                if (variacoes.length === 1) {
                    // Se há apenas uma variação no dropdown, selecionar automaticamente
                    console.log('🎯 Apenas uma variação disponível - selecionando automaticamente');
                    $('#variacao').val(variacoes.first().val()).trigger('change');
                } else if (variacoes.length > 1) {
                    // Se há múltiplas variações, verificar se existe uma "Padrão"
                    const variacaoPadrao = variacoes.filter(function() {
                        return $(this).text().toLowerCase().includes('padrão');
                    });
                    
                    if (variacaoPadrao.length > 0) {
                        console.log('🎯 Selecionando variação padrão automaticamente');
                        $('#variacao').val(variacaoPadrao.first().val()).trigger('change');
                    } else {
                        console.log('📝 Múltiplas variações - usuário deve escolher');
                    }
                } else {
                    console.warn('⚠️ Nenhuma variação disponível encontrada');
                }
            }, 100);
            
            // Event handler para mudança de variação
            $('#variacao').off('change').on('change', function() {
                console.log('📝 Variação selecionada');
                const option = $(this).find('option:selected');
                const variacaoId = option.val();
                const precoAdicional = parseFloat(option.data('preco-adicional')) || 0;
                const estoque = parseInt(option.data('estoque')) || 0;
                
                console.log('📊 Dados da variação:', {
                    variacaoId,
                    precoAdicional, 
                    estoque,
                    optionText: option.text()
                });
                
                if (!variacaoId) {
                    console.log('❌ Nenhuma variação selecionada');
                    $('#form-comprar button[type="submit"]').prop('disabled', true);
                    return;
                }
                
                // Atualizar preço
                const precoTotal = precoBase + precoAdicional;
                $('.price').html('R$ ' + precoTotal.toFixed(2).replace('.', ',') + 
                                (precoAdicional > 0 ? ' <span class="text-muted" style="font-size: 1rem;">(+R$ ' + 
                                 precoAdicional.toFixed(2).replace('.', ',') + ')</span>' : ''));
                
                // Atualizar estoque
                $('#estoque-disponivel').text(estoque);
                $('#quantidade').attr('max', estoque).val(1);
                
                // Habilitar/desabilitar botão
                const botao = $('#form-comprar button[type="submit"]');
                if (estoque > 0) {
                    botao.prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                    console.log('✅ Botão habilitado - estoque:', estoque);
                } else {
                    botao.prop('disabled', true).removeClass('btn-success').addClass('btn-secondary');
                    console.log('❌ Botão desabilitado - sem estoque');
                }
            });
            
            // Event handler para submit do formulário
            $('#form-comprar').off('submit').on('submit', function(e) {
                e.preventDefault();
                console.log('🎯 Formulário submetido');
                
                const produtoId = $('input[name="produto_id"]').val();
                // Verificar se é dropdown ou campo hidden
                const variacaoId = $('#variacao').val() || $('#variacao-hidden').val();
                const quantidade = parseInt($('#quantidade').val());
                
                console.log('📦 Dados do formulário:', {produtoId, variacaoId, quantidade});
                
                if (!variacaoId) {
                    console.warn('⚠️ Nenhuma variação encontrada');
                    showAlert('warning', 'Erro: Variação não encontrada');
                    return;
                }
                
                if (!produtoId) {
                    console.error('❌ Produto ID não encontrado');
                    showAlert('danger', 'Erro: Produto não identificado');
                    return;
                }
                
                console.log('🚀 Chamando adicionarAoCarrinho...');
                adicionarAoCarrinho(produtoId, variacaoId, quantidade);
            });
        }
        
        // Função para alterar quantidade (chamada pelos botões + e -)
        function alterarQuantidade(delta) {
            if (typeof $ === 'undefined') return;
            
            const input = $('#quantidade');
            const atual = parseInt(input.val()) || 1;
            const max = parseInt(input.attr('max')) || 1;
            const min = parseInt(input.attr('min')) || 1;
            
            const novo = atual + delta;
            
            if (novo >= min && novo <= max) {
                input.val(novo);
            }
        }
        
        // Tornar funções globais
        window.alterarQuantidade = alterarQuantidade;
    </script>
</body>
</html> 