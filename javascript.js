
// $(function(){
// $("#cadastro-produto").submit(function(e){
//     e.preventDefault();
//     console.log("entrou")
// })
// })
// carregarGrid();

// $("#cadastro-produto").ajaxSubmit({
//     success:function(res){
//          alert('entrou');
//         // var myObject = JSON.parse(res);
//         // if(myObject.results==true){
//         //     carregarGrid();
//         // }
//     }
// })

// function carregarGrid(params){
//     carregando();
//            $.ajax({
//             url: BASE_URL + "admin/noticia/carregar-grid",
//             data: {params:params},
//             success:function(resp){
//             $('#grid').html(resp).show();
//             }
//         }); 
// }

$(function () {
    $("#cadastro-produto").submit(function (e) {
        e.preventDefault();
            cadastro();
        })
})
carregarGrid();
function carregarGrid(){ 
           $.ajax({
            url: "rota.php?acao=carregarGrid",
            beforeSend: ()=>carregamentoIniciar(),
            complete: ()=>carregamentoFinalizar(),
            success:function(resp){
               
                $('table#grid tbody').html(resp);
            }
        }); 
}
carregarCatalogo()
function carregarCatalogo() {
        $.ajax({
            url: "rota.php?acao=carregarCatalogo",
            beforeSend: ()=>carregamentoIniciar(),
            complete: ()=>carregamentoFinalizar(),
            success:function(resp){
                $('div#catalogo').html(resp);
            }
        }); 
}
carregarCarrinho();
function carregarCarrinho() {
    $.ajax({
        url: "rota.php?acao=carregarCarrinho",
        beforeSend: ()=>carregamentoIniciar(),
        complete: ()=>carregamentoFinalizar(),
        success:function(resp){
            $('div#carrinho').html(resp);
            if(!$("#valor-total-produtos-carrinho").text()){
                $("#bt-valor-total-produtos-carrinho").text('vazio');
            }else{
                $("#bt-valor-total-produtos-carrinho").text("R$ "+$("#valor-total-produtos-carrinho").text())
            }
        }
    }); 
}

function cadastro(){
    var imagem = "&imagem=";
    if($("[name='imagem']").val()){
        var respUp = uploadArquivo("cadastro-produto");
        if(respUp.results==false){alert(respUp.msg); return false;}else{imagem = "&imagem="+respUp.nome_arquivo;}
    }
    let data = $("#cadastro-produto").serialize()+imagem;

    $.ajax({
        url:  $("#cadastro-produto").attr('action'),
        data: data,
        type: "post",
        dataType:"json",
        beforeSend: ()=>carregamentoIniciar(),
        complete: ()=>carregamentoFinalizar(),
        success: function (resp) {
            if(resp.results==true){
                carregarGrid()
                carregarCatalogo();
                resetForm();
            }
            alert(resp.msg);
        }
    })
}

function edit(id){
    $('html, body').animate({ scrollTop: $("#cadastro-produto").offset()['top']}, 500); 
    $("#cadastro-produto").attr('action','rota.php?acao=update')
    $("[name='imagem']").removeAttr('required'); ///na edição  não é obrigatório mudar imagem

    $.ajax({
        url: "rota.php?acao=edit",
        data: {id:id},
        //type: "post",
        dataType:"json",
         beforeSend: ()=>carregamentoIniciar(),
         //complete: ()=>carregamentoFinalizar(),
        success: function (resp) {
            
        $("#cadastro-produto").trigger("reset");
        carregamentoFinalizar()
        setTimeout(function(){MascaraMoeda(valor)},500);
            for (var chave in resp) {
                $("form#cadastro-produto [name=" + chave + "]").val(resp[chave]);
            }     
           
           
        }
    })
}

function destroy(id) {
    var res = confirm("Tem certeza que deseja excluir este registro?");
    if(res==true){
        $.ajax({
            url: "rota.php?acao=destroy",
            data: {id:id},
            type: "post",
            dataType:"json",
            beforeSend: ()=>carregamentoIniciar(),
            complete: ()=>carregamentoFinalizar(),
            success: function (resp) {
                alert(resp.msg);
                if(resp.results==true){
                    carregarGrid();
                    carregarCatalogo();
                } 
            }
        })
    }
}
function addProdutoCarrinho(id){
    $.ajax({
        url: "rota.php?acao=addProdutoCarrinho",
        data: {id:id},
        type: "post",
        dataType:"json",
         beforeSend: ()=>carregamentoIniciar(),
         complete: ()=>carregamentoFinalizar(),
        success: function (resp) {
            carregarCarrinho()
            $('#carrinho').show('500')

            setTimeout(function(){
                $('#carrinho').hide('500')
            },5000)
        }
    })
}
function removerProdutoCarrinho(id){
    $.ajax({
        url: "rota.php?acao=removerProdutoCarrinho",
        data: {id:id},
        type: "post",
        dataType:"json",
         beforeSend: ()=>carregamentoIniciar(),
         complete: ()=>carregamentoFinalizar(),
        success: function (resp) {carregarCarrinho()}
    })
}
function resetForm() {
    $("#cadastro-produto").trigger("reset");
    $("[name='imagem']").attr('required',true);    
    $("#cadastro-produto").attr('action','rota.php?acao=cadastro');
}

function uploadArquivo(idForm) {
    var retorno;
    var data;
    data = new FormData();
    data.append('file', $("#"+idForm+" input[type='file']")[0].files[0]);///ENVIA APENAS UM ARQUIVO
    $.ajax({
        url: "rota.php?acao=upload",
        data: data,
        type: "post",
        processData: false,
        contentType: false,
        async: false,
        dataType:"json",
        success: function (resp) {
            retorno = resp;
        }
    })
    return retorno;
}

function MascaraInteiro(num) {
    var er = /[^0-9]/;
    er.lastIndex = 0;
    var campo = num;
    if (er.test(campo.value)) {///verifica se é string, caso seja então apaga
        var texto = $(campo).val();
        $(campo).val(texto.substring(0, texto.length - 1));
        return false;
    } else {
        return true;
    }
}

function MascaraMoeda(i) {
    var v = i.value.replace(/\D/g, '');
    v = (v / 100).toFixed(2) + '';
    v = v.replace(".", ",");
    v = v.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
    v = v.replace(/(\d)(\d{3}),/g, "$1.$2,");
    i.value = v;
}

function MascaraGenerica(seletor, tipoMascara) {
    setTimeout(function () {
        if (tipoMascara == 'CPFCNPJ') {
            if (seletor.value.length <= 14) { //cpf
                formataCampo(seletor, '000.000.000-00');
            } else { //cnpj
                formataCampo(seletor, '00.000.000/0000-00');
            }
        } else if (tipoMascara == 'DATA') {
            formataCampo(seletor, '00/00/0000');
        } else if (tipoMascara == 'CEP') {
            formataCampo(seletor, '00.000-000');
        } else if (tipoMascara == 'TELEFONE') {
            formataCampo(seletor, '(00) 000000000');
        } else if (tipoMascara == 'INTEIRO') {
            MascaraInteiro(seletor);
        } else if (tipoMascara == 'FLOAT') {
            MascaraFloat(seletor);
        } else if (tipoMascara == 'CPF') {
            formataCampo(seletor, '000.000.000-00');
        } else if (tipoMascara == 'CNPJ') {
            formataCampo(seletor, '00.000.000/0000-00');
        } else if (tipoMascara == 'MOEDA') {
            MascaraMoeda(seletor);
        }
    }, 200);
}

function carregamentoIniciar() {
    $("body").append('<div class="spinner-border div-ajax-carregamento-gif" role="status"><span class="sr-only">Loading...</span></div>');
    $("body").append("<div class='div-ajax-carregamento-pagina'></div>");
    $(".div-ajax-carregamento-pagina").css({
        "position": "fixed",
        "top": "0px",
        "left": "0px",
        "width": "100%",
        "height": "100%",
        "z-index": "99998",
        "opacity": "0",
        "-moz-opacity": "0.80",
        "filter": "alpha(opacity=80)",
        "background": "black",
        "text-align": "center"
    })
    $(".div-ajax-carregamento-gif").css({
        "z-index": "99992",
        "position": "fixed",
        "left": "50%",
        "top": "50%",
        "margin-top": "-42px"
    });
}

function carregamentoFinalizar() {
    $("body .div-ajax-carregamento-pagina").remove(".div-ajax-carregamento-pagina");
    $("body .div-ajax-carregamento-gif").remove(".div-ajax-carregamento-gif");
}
