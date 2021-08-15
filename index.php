<!doctype html>
<html lang="pt-br" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Produtos</title>
    <link rel="shortcut icon" href="img/favicon.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="http://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <style>
        .img-thumb-produto {
            text-align: center;
        }

        .img-thumb-produto img {
            border: 0;
            height: 200px;
        }

        .produto-carrinho img {
            height: 80px
        }

        form label {
            font-weight: bold;
        }
    </style>
    <?php define('DIR', $_SERVER['DOCUMENT_ROOT'] . "/" . explode("/", $_SERVER['REQUEST_URI'])[1]); ?>
    <script>
        var DIR = '<?php echo DIR; ?>'
    </script>
</head>

<body>

    <nav class="navbar navbar-expand bg-primary fixed-top">
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown show">
                <button onclick="$('#carrinho').toggle()" data-toggle="dropdown" class="btn btn-info mt-1 nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart mr-2 mt-0" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                    </svg> 
                    Carrinho <span id="bt-valor-total-produtos-carrinho">vazio</span></button>
                <div id="carrinho" class="dropdown-menu dropdown-menu-lg dropdown-menu-right pr-3 pl-3 produto-carrinho overflow-auto" style="width: 170%; left: inherit; right: 0px;max-height: 400px;">
                </div>
            </li>

        </ul>
    </nav>
    <div class="container p-3 mt-5">

        <div class="row" id="catalogo">

        </div>
        <hr>
        <hr>
        <hr>
        <form id="cadastro-produto" action="rota.php?acao=cadastro" enctype="multipart/form-data">
            <input type="hidden" name="id">
            <h3 class="form-title">Cadastrar Produto</h3>
            <hr>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" name="titulo" class="form-control" placeholder="digite o titulo" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" class="form-control" placeholder="digite uma descrição">
                </div>
                <div class="col-md-4 form-group">
                    <label for="valor">Valor *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                        </div>
                        <input required type="text" id="valor" name="valor" class="form-control" onkeydown="MascaraGenerica(this,'MOEDA')" placeholder="digite um valor">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="desconto">Desconto</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">%</span>
                        </div>
                        <input type="text" name="desconto" class="form-control" placeholder="digite o valor percentual do desconto" onkeydown="MascaraGenerica(this,'INTEIRO')">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label for="parcelamento">Parcelamento</label>
                    <input type="text" name="parcelamento" class="form-control" placeholder="digite a descrição do parcelamento">
                </div>
                <div class="col-md-4 form-group">
                    <label for="imagem">Imagem *</label>
                    <input required type="file" name="imagem" class="form-control" placeholder="escolha a imagem do produto">
                </div>
            </div>
            <div class="form-footer">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" onclick="resetForm()" class="btn btn-danger">Cancelar</button>
            </div>
        </form>
        <hr>
        <div class="table-responsive">
            <table id="grid" class="table table-striped table-bordered" style="width: 99%;">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>ID</th>
                        <th style="width:150px">Produto</th>
                        <th style="width:150px">Descrição</th>
                        <th style="width:90px" align="center">Valor</th>
                        <th>Desconto</th>
                        <th>Parcelamento</th>
                        <th style="width:100px">Dt cadastro</th>
                        <th style="width:90px" align="center">Ação</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="http://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="javascript.js"></script>
    <script>
        $(document).ready(function() {
            setTimeout(function(){
            $('#grid').DataTable();
        },1000)
        });
    </script>
</body>

</html>