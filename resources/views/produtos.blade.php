@extends('layout.app', ["current"=> "produtos"])

@section('body')
    <div class="card border">
        <div class="card-body">
            <h5>Cadastro de produtos</h5>
            <table class="table table-ordered table-hover" id="tabelaProdutos">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Categoria</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
            
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button class="btn btn-sm btn-primary" role="button" onclick="novoProduto()">Novo produto</button>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" id="dlgProduto">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form class="form-horizontal" id="formProduto">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Produto</h5>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" id="id" class="form-control">
                        <div class="form-group">
                            <label for="nomeProduto" class="control-label">Nome do Produto</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="nomeProduto" 
                                placeholder="Nome do produto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="qtedeProduto" class="control-label">Quantidade</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="qtdeProduto" 
                                placeholder="Quantidade do produto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="precoProduto" class="control-label">Preço</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="precoProduto" 
                                placeholder="Preço do produto">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="categoriaProduto" class="control-label">Categoria</label>
                            <div class="input-group">
                                <select class="form-control" id="categoriaProduto">
                                    <option value="Escolha">Escolher uma opção</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <button type="cancel" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        
        $.ajaxSetup({ // headers colocado aqui ja serve de modelo para todos os métodos de requisição, POST,PUT,DELETE
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });


        function novoProduto(){

            $('#id').val('');
            $('#nomeProduto').val('')
            $('#qtdeProduto').val('')
            $('#precoProduto').val('')
            $('#categoriaProduto').val('Escolha')
            $('#dlgProduto').modal('show')
        }

        function carregarCategorias(){ // Função para retornar todas as categorias
            // Vai receber os dados Json lá do /api/categorias e precisa tratar para jogar eles dentro do select
            $.getJSON('/api/categorias', function(data) { // Não precisa usar parse,getJSON já faz a conversão

                for(i=0;i<data.length;i++){ // For criado, percorre até o total do data que é 4, incrementando
                    // Var opcao recebe a tag <option> com value do id dos dados e o nome dos dados
                    opcao = '<option value= "' + data[i].id + '">' + data[i].nome + '</option>';

                    $('#categoriaProduto').append(opcao);  // Vai utilizar o Jquery e jogar os valores que estão dentro de opcao
                }

            });
        }

        
        // Essa função é chamada dentro da função carregarProdutos()
        function montarLinha(p){ // Função montarLinha passando objeto como parâmetro para forar a linha inteira com os dados
            // Cria a váriavel linha que vai armazenar as linhas,os dados armazenados e os buttons
            var linha = "<tr>" +
            "<td>" + p.id + "</td>" +
            "<td>" + p.nome + "</td>" +
            "<td>" + p.Stock + "</td>" +
            "<td>" + p.preco + "</td>" +
            "<td>" + p.categoria_id + "</td>" +
            "<td>" + 
                '<button class="btn btn-sm btn-primary" onclick="editar('+ p.id + ')"> Editar </button>' +
                ' <button class="btn btn-sm btn-danger" onclick="remover('+ p.id + ')"> Apagar </button> ' + 
            "</td>" + 
            "</tr>";
                
            return linha;

        }

        function editar(id){
            $.getJSON('/api/produtos/' + id, function(data) { // Não precisa usar parse,getJSON já faz a conversão
                console.log(data);

            $('#id').val(data.id); // Campo com o valor do id do produto
            $('#nomeProduto').val(data.nome) // Campo com o valor do nome do produto
            $('#qtdeProduto').val(data.Stock) // Campo com o valor do estoque do produto
            $('#precoProduto').val(data.preco) // Campo com o valor do preco do produto
            $('#categoriaProduto').val(data.categoria_id); // Campo com o valor da categoria do produto
            $('#dlgProduto').modal('show')
            });
        }

        function remover(id){ // Função remover passa o id
            // Função ajax apartir do jquery
            $.ajax({
               type:"DELETE", // define o tipo de método requisitado
               url: '/api/produtos/' + id, // Passa a url
               context: this, // Tipo de contexto
               // Caso ache o id e consiga deletar
               success: function(){ 
                    console.log('Apagou OK');
                    linhas = $('#tabelaProdutos>tbody>tr'); // linhas recebe todas as linhas da tabela produto
                    // utiliza função filter() do jquery, passa 2 param, indice e elemento
                    // Retorna o elemento cujo a célula seja 0 (primeira coluna) e o textContent seja o id
                    e = linhas.filter( function(i, elemento) { 
                        return elemento.cells[0].textContent == id; 
                    });
                    if (e) // Se encontrou, ele remove
                        e.remove();
               },
               // Se não conseguir encontrar, retorna erro
               error: function(error){ 
                   console.log(error);
               }

            });
        }

        function carregarProdutos(){ // Função para retornar todos os produtos
        // Vai receber os dados Json lá do /api/produtos e precisa tratar para montar a linha da tabela de todos os dados
            $.getJSON('/api/produtos', function(produtos){ // Não precisa usar parse,getJSON já faz a conversão

                for(i=0;i<produtos.length;i++){ // For criado, percorre até o total do data que é 2, incrementando
                    
                    linha = montarLinha(produtos[i]); // Essa variável recebe a função com todos os produtos dentro

                    $('#tabelaProdutos>tbody').append(linha); // Vai utilizar o Jquery e jogar os valores que estão dentro de linha
                }
            });
        }
        function criarProduto(){ // Cria,salva e atualiza um produto na tabela
            // Objeto seta os campos da tabela pegando o valor pelo id
            prod = { 
            nome: $('#nomeProduto').val(),
            Stock: $('#qtdeProduto').val(), 
            preco: $('#precoProduto').val(), 
            categoria_id: $('#categoriaProduto').val()
            };
            // Faz a requisição post convertendo data que está em String utilizando parse
            $.post('/api/produtos', prod, function(data) {
                produto = JSON.parse(data);

                // Monta a linha e atualiza a tabela
                linha = montarLinha(produto); 
                $('#tabelaProdutos>tbody').append(linha);
            });
        }

        function salvarProduto(){
            prod = { // Objeto com os campos da tabela que está pegando pelo id no formulário
                id: $("#id").val(),
                nome: $("#nomeProduto").val(),
                Stock: $("#qtdeProduto").val(),
                preco: $("#precoProduto").val(),
                categoria_id: $("#categoriaProduto").val()
            };
            $.ajax({
               type:"PUT", // define o tipo de método requisitado
               url: '/api/produtos/' + prod.id, // Passa a url
               context: this, // Tipo de contexto
               data: prod,
               // Caso ache o id 
               success: function(data){ // Passou o data aqui pq lá no controlador, no método update ta retornando json_encode
                   prod = JSON.parse(data); // Transformou o data em objeto pq estava no formato string
                   linhas = $("#tabelaProdutos>tbody>tr");
                   e = linhas.filter(function(i, e){
	                    return (e.cells[0].textContent == prod.id);
                });
               if (e){ // Se o e foi setado, conseguiu encontrar, ele vai atualizar célula por célula da tabela
                    e[0].cells[0].textContent = prod.id;
                    e[0].cells[1].textContent = prod.nome;
                    e[0].cells[2].textContent = prod.Stock;
                    e[0].cells[3].textContent = prod.preco;
                    e[0].cells[4].textContent = prod.categoria_id;
                }
            },
               // Se não conseguir encontrar, retorna erro
               error: function(error){ 
                   console.log(error);
               }

            });
        }


        // Bloqueia o formulário quando clica no botão 'Salvar'
        // Chama a função que cria,salva e atualiza os produtos
        // Depois que adicionar os produtos, esconde o formulário
        $('#formProduto').submit( function() {
            event.preventDefault();
            if($(id).val() != '')
                salvarProduto();
            else{
                criarProduto();
            }
            $('#dlgProduto').modal('hide');
        });

        $(function(){ // Função própria do Jquery
                carregarCategorias(); // Vai chamar a função carregarCategorias()
                carregarProdutos(); // Vai chamar a função carregarProdutos()
            });
    </script>
@endsection