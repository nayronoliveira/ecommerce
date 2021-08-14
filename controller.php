<?php
require_once "configuracoes.php";
require_once "conexao.php";

class Controller
{
    private $bd;

    public function __construct()
    {
        $this->bd = ConexaoBd::conexao();
    }

    public function upload()
    {
        $nome_arquivo = $_FILES['file']['name'];
        $tmp_name = $_FILES['file']['tmp_name'];
        $d = explode("/", $_FILES['file']["type"]);
        
       
        $ArqPermitidos = array('jpg','jpeg','png');
        //print_r(in_array($d[1],$ArqPermitidos));die();
        if(in_array($d[1],$ArqPermitidos)){
            if (is_dir(DIR . "/upload")) {
                $upload = move_uploaded_file($tmp_name, DIR . "/upload/$nome_arquivo");
                if ($upload == true) {
                    //exit("UPLOAD REALIZADO COM SUCESSO");
                    return array('results' => true, "nome_arquivo" => $nome_arquivo);
                } else {
                    return array('results' => false, "msg" => "ERRO AO FAZER UPLOAD");
                }
            } else {
                return array('results' => false, "msg" => "DIRETÓRIO DE DESTINO NÃO ENCONTRADO");
            }
        }else{
            return array('results' => false, "msg" => "So é permitido o upload de imagens!");
        }
    }

    public function cadastro()
    {
        $titulo = addslashes($_POST['titulo']);
        $descricao = addslashes($_POST['descricao']);
        $valor = str_replace(",",".",str_replace(".","",$_POST['valor']));
        $desconto = $_POST['desconto'];
        $parcelamento = $_POST['parcelamento'];
        $imagem = $_POST['imagem'];


        $sql = "INSERT INTO produto (titulo, descricao, valor, desconto, parcelamento, imagem) VALUES ('$titulo', '$descricao', '$valor', '$desconto','$parcelamento','$imagem')";

        if (mysqli_query($this->bd, $sql)) {
            $return = array('msg' => "Registro inserido com sucesso!", "results" => true);
        } else {
            $return = array('msg' => "Error: " . $sql . "<br>" . mysqli_error($this->bd), "results" => false);
        }
        mysqli_close($this->bd);
        return $return;
    }

    public function edit()
    {
        $id = $_REQUEST['id'];
        $sql = "select * from produto WHERE id=$id";

        if ($resp = $this->bd->query($sql)) {
            $dados = $resp->fetch_object();
            mysqli_close($this->bd);
            return $dados;
        }
    }

    public function destroy()
    {
        $id = $_POST['id'];
        $sql2 = "select * from produto WHERE id=$id";
        $resp = $this->bd->query($sql2);
        $dados = $resp->fetch_object();

        $sql = "DELETE FROM produto WHERE id=$id";

        if (mysqli_query($this->bd, $sql)) {
            $return = array('msg' => "Registro excluído com sucesso!", "results" => true);

            //print_r($dados);die();
            unlink(DIR . "/upload/$dados->imagem");
        } else {
            $return = array('msg' => "Error: " . $sql . "<br>" . mysqli_error($this->bd), "results" => false);
        }
        mysqli_close($this->bd);
        return $return;
    }

    public function update()
    {
        $id = $_POST['id'];
        $titulo = addslashes($_POST['titulo']);
        $descricao = addslashes($_POST['descricao']);
        $valor = str_replace(",",".",str_replace(".","",$_POST['valor']));
        $desconto = $_POST['desconto'];
        $parcelamento = $_POST['parcelamento'];
        $imagem = $_POST['imagem'];


        $sql = "UPDATE produto SET titulo = '$titulo', descricao = '$descricao', valor = '$valor', desconto = '$desconto', parcelamento = '$parcelamento' WHERE id=$id";



        if (mysqli_query($this->bd, $sql)) {
            if ($imagem) {
                
                $sql2 = "select * from produto WHERE id=$id";
                $d = $this->bd->query($sql2)->fetch_object();
               
                $sql2 = "UPDATE produto SET imagem = '$imagem' WHERE id=$id";

                if (mysqli_query($this->bd, $sql2)) {
                     unlink(DIR . "/upload/$d->imagem");
                    $return = array('msg' => "Registro alterado com sucesso!", "results" => true);
                } else {
                    $return = array('msg' => "Error: " . $sql2 . "<br>" . mysqli_error($this->bd), "results" => false);
                }
            } else {
                $return = array('msg' => "Registro alterado com sucesso!", "results" => true);
            }
        } else {
            $return = array('msg' => "Error: " . $sql . "<br>" . mysqli_error($this->bd), "results" => false);
        }
        mysqli_close($this->bd);
        return $return;
    }
    public function carregarCatalogo()
    {
        $html = "";
        $sql = "select * from produto";
        if ($resp = $this->bd->query($sql)) {
            while ($obj = $resp->fetch_object()) {
                if($obj->desconto>0){$valor = ($obj->valor - (($obj->valor*10)/100));}else{$valor =$obj->valor;}
                $html .= "<div class='col-md-3 p-1' id='1' >
                <div class='col-body-produto p-2 mb-1 rounded shadow' style='min-height:490px'>
                    <div class='img-thumb-produto'>
                        <img class='img-thumbnail' src='upload/$obj->imagem' alt=''>
                    </div>
                    <div class='titulo-produto'><b>$obj->titulo</b></div>
                    <div class='descricao-produto mt-1'>
                    $obj->descricao
                    </div>";
                    if($obj->desconto>0){
                        $html .= "<div class='valor-riscado-produto mt-1' style='text-decoration:line-through'><small>R$ ".number_format($obj->valor,2,",",".")."</small></div>";
                    }
                    $html .= "<div class='valor-produto text-info'><b>R$ ".number_format($valor,2,",",".")."</b></div>
                    <div class='parcelamento-produto'><small>$obj->parcelamento</small></div>
                    <div class='footer-col text-center mt-1 mb-4' style='position: absolute;bottom:0;width:90%'>
                    <button class='btn btn-success' onclick='addProdutoCarrinho($obj->id)'>+ Add Carrinho</button>
                    </div>
                </div>
            </div>";
            }
        } else {
            $html = "<tr><td colspan='9'>Nenhum registro encontrado.</td></tr>";
        }
        mysqli_close($this->bd);
        return $html;
    }
    public function carregarGrid()
    {
        $html = "";
        $sql = "select * from produto";
        if ($resp = $this->bd->query($sql)) {
            while ($obj = $resp->fetch_object()) {
                // print_r($obj->titulo);die();
                if($obj->desconto>0){$valor ="R$ ".number_format(($obj->valor - (($obj->valor*10)/100)),2,',','.')."<br><span style='text-decoration:line-through'>R$ ".number_format($obj->valor,2,',','.')."</span>";}else{$valor = "R$ ".number_format($obj->valor,2,',','.');}
                $html .= "<tr>
                    <td><img style='height:100px' src='upload/$obj->imagem' /></td>
                    <td align='center'>$obj->id</td>
                    <td>$obj->titulo</td>
                    <td>$obj->descricao</td>
                    <td align='center'>$valor</td>
                    <td align='center'>$obj->desconto%</td>
                    <td>$obj->parcelamento</td>
                    <td align='center'>{$this->dataAmericanoParaBrasileiro($obj->dt_inc)}</td>
                    <td align='center'>
                    <ul class='list-group list-group-horizontal' style='list-style: none;'>
                    <li class='p-2'>
                    <a class='btn btn-primary' data-toggle='tooltip' data-placement='top' title='editar' onclick='edit($obj->id)'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil-square' viewBox='0 0 16 16'>
  <path d='M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z'/>
  <path fill-rule='evenodd' d='M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z'/>
</svg></a>
</li>
<li class='p-2'>
<a class='btn btn-danger' data-toggle='tooltip' data-placement='top' title='excluir' onclick='destroy($obj->id)'>
<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash' viewBox='0 0 16 16'>
  <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'/>
  <path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'/>
</svg></a>
</li>
</ul>
</td>                
</tr>";
            }
        } else {
            $html = "<tr><td colspan='9'>Nenhum registro encontrado.</td></tr>";
        }
        mysqli_close($this->bd);
        return $html;
    }

    public function addProdutoCarrinho()
    {
        session_start();
        //unset($_SESSION['carrinho']);
        $id = $_POST['id'];
        $sql2 = "select * from produto WHERE id=$id";
        $resp = $this->bd->query($sql2)->fetch_assoc();

        if (isset($_SESSION['carrinho'][$resp['id']])) {

            //$quantidade = $_SESSION['carrinho'][$resp['id']]['quantidade'] + 1;   

            ///atualiza os dados do produto para caso tenha alteração em alguma descrição do mesmo no cadastro e somo a quatidade anterior com + 1
            $_SESSION['carrinho'][$resp['id']][] = $resp;
            // $_SESSION['carrinho'][$resp['id']]['quantidade'] = $quantidade;
        } else {
            $_SESSION['carrinho'][$resp['id']][] = $resp;
            // $_SESSION['carrinho'][$resp['id']]['quantidade'] = 1;
        }
    }

    public function carregarCarrinho()
    {
        session_start();
        $html = "";
        // print_r($_SESSION['carrinho']);
        // die();

        if (isset($_SESSION['carrinho']) && count($_SESSION['carrinho']) > 0) {
            $valorTotal = 0;
            foreach ($_SESSION['carrinho'] as $k => $vv) {
                foreach ($vv as $k => $v) {
                    if($v['desconto']>0){$valor = ($v['valor'] - (($v['valor']*10)/100));}else{$valor =$v['valor'];}
                    if ($k == 0) {
                        $html .= "
                <div class='media'>
                    <img src='upload/" . $v['imagem'] . "' alt='celular' class='mr-3 ml-3'>
                    <div class='media-body' style='width: 15em'>
                    <a href='#' class='dropdown-item m-0 p-0 pr-3 pt-2' data-toggle='tooltip' data-placement='top' title='" . $v['titulo'] . "'>
                        <h6 class='dropdown-item-title text-truncate' ><b>" . $v['titulo'] . "</b></h6>
                        <div class='text-sm text-muted'>Quantidade: " . count($vv) . "
                            <span class='float-right text-sm text-muted'>R$ " . number_format($valor,2,',','.') . "</span>
                        </div>
                        </a>
                        <a data-toogle='tooltip' data-placement='top' title='Remover este produto do carrinho' onclick='removerProdutoCarrinho(" . $v['id'] . ")' style='cursor:pointer'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-dash-circle' viewBox='0 0 16 16'>
                        <path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/>
                        <path d='M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z'/>
                      </svg>
                      </a>&nbsp;
                      <a data-toogle='tooltip' data-placement='top' title='Adicionar mais um deste produto' onclick='addProdutoCarrinho(" . $v['id'] . ")' style='cursor:pointer'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-plus-circle' viewBox='0 0 16 16'>
  <path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/>
  <path d='M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z'/>
</svg></a>";
if($v['desconto']>0){
$html .="<span class='float-right text-sm text-muted mr-3' style='text-decoration:line-through'><small>R$ " . number_format($v['valor'],2,',','.') . "</small></span>";
}                      
$html .="            </div>
                </div>
            
            <div class='dropdown-divider'></div>";
                    }
                    $valorTotal = $valorTotal + $valor;
                }
            }
            $html .= '<a href="#" class="dropdown-item dropdown-footer"><b>Total: R$ <span id="valor-total-produtos-carrinho">' . number_format($valorTotal,2,',','.') . '</span></b></a>';
        } else {
            $html = '<a href="#" class="dropdown-item">
            <div class="media">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart mr-2" viewBox="0 0 16 16">
            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
          </svg>
                <div class="media-body" style="width: 15em">
                    <h6 class="dropdown-item-title text-truncate" data-toggle="tooltip" data-placement="top" >Carrinho vazio.</h6>
                </div>
            </div>
        </a>';
        }
        return $html;
    }

    public function removerProdutoCarrinho()
    {
        session_start();
        $id = $_POST['id'];
        //print_r(count($_SESSION['carrinho']));die();
        //unset($_SESSION['carrinho']);
        //print_r($_SESSION['carrinho'][$id]);die();
        unset($_SESSION['carrinho'][$id][count($_SESSION['carrinho'][$id]) - 1]); ///remove sempre o ultimo
        // print_r($_SESSION['carrinho'][$id]);die();
        // print_r(count($_SESSION['carrinho'][$id]));die();
        if (count($_SESSION['carrinho'][$id]) == 0) {
            unset($_SESSION['carrinho'][$id]);
        }
        if (count($_SESSION['carrinho']) == 0) {
            unset($_SESSION['carrinho']);
        }
    }


    public function dataAmericanoParaBrasileiro($dataEHora)
    {
        $hora = explode(" ", $dataEHora);
        $data = explode("-", $hora[0]);

        return $data[2] . "/" . $data[1] . "/" . $data[0] . " " . $hora[1];
    }
}
