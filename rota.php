<?php include "controller.php";

$oController = new Controller();



if($_REQUEST['acao']=="upload"){
    exit(json_encode($oController->upload()));
}

if($_REQUEST['acao']=="cadastro"){
    exit(json_encode($oController->cadastro()));
}

if($_REQUEST['acao']=="update"){
    exit(json_encode($oController->update()));
}

if($_REQUEST['acao']=="edit"){
    exit(json_encode($oController->edit()));
}

if($_REQUEST['acao']=="destroy"){
    exit(json_encode($oController->destroy()));
}

if($_REQUEST['acao']=="carregarGrid"){
    exit($oController->carregarGrid());
}

if($_REQUEST['acao']=="carregarCatalogo"){
    exit($oController->carregarCatalogo());
}

if($_REQUEST['acao']=="carregarCarrinho"){
    exit($oController->carregarCarrinho());
}

if($_REQUEST['acao']=="addProdutoCarrinho"){
    exit(json_encode($oController->addProdutoCarrinho()));
}

if($_REQUEST['acao']=="removerProdutoCarrinho"){
    exit(json_encode($oController->removerProdutoCarrinho()));
}

