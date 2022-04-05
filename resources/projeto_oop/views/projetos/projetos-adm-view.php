<?php
// Evita acesso direto a este ficheiro
if (!defined('ABSPATH'))
    exit;
// Configura as URLs
$adm_uri = HOME_URI . '/projetos/adm/';
$edit_uri = $adm_uri . 'edit/';
$delete_uri = $adm_uri . 'del/';
// Carrega o método para obter uma projetos
$modelo->obtem_projetos();
// Carrega o método para inserir uma projetos
$modelo->insere_projeto();
// Carrega o método para apagar a projetos
$modelo->form_confirma = $modelo->apaga_projeto();

// Remove o limite de valores da lista de projetos
$modelo->sem_limite = false;
// Número de posts por página
?>
<div class="wrap">
    <?php
    // Mensagem de configuração caso o user tente apagar algo
    echo $modelo->form_confirma;
    ?>
    <!-- Formulário de edição das projetos -->
    <form method="post" action="" enctype="multipart/form-data">
        <table class="form-table">
            <tr><td>
	Descricao: <br>
	<input type="text" name="descricao" value="<?php
	echo htmlentities(chk_array($modelo->form_data, 'descricao'));
                    ?>" />
                </td>
            </tr>
          <tr>
			<td>
	Imagem: <br>
	<input type="file" name="imagem" value="" />
            </td>
          </tr>
            <tr>
                <td>
	Data: <br>
	<input type="text" name="dataExec" value="<?php
	$data = chk_array($modelo->form_data, 'dataExec');
	if ($data && $data != '0000-00-00 00:00:00')
	echo date('d-m-Y H:i:s', strtotime($data));
		?>" />
                </td>
            </tr>
            <tr>
                <td>
	Link: <br>
	<input type="text" name="link" value="<?php
	echo htmlentities(chk_array($modelo->form_data, 'link'));
	?>" />
                </td>
            </tr>
<tr>
	<td colspan="2">
		<?php echo $modelo->form_msg; ?>
		<input type="submit" value="Save" />
		<a href="<?php echo HOME_URI . '/projetos/adm'; ?>">New projet</a>
   </td>
 </tr>
        </table>
        <input type="hidden" name="insere_projeto" value="1" />
    </form>
    <!-- LISTA os projetos -->
    <?php 
	$lista = $modelo->listar_projetos(); ?>
    <h1>Lista de Projetos</h1>
    <table id="tbl-projeto" class="list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>desc</th>
                <th>data</th>
                <th>link</th>
                <th>img</th>
                <th>edit</th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($lista as $projeto): ?>
<tr>
	<td><a href="<?php echo HOME_URI ?>/projetos/index/<?php echo $projeto['idProjecto'] ?>"><?php echo $projeto['idProjecto'] ?></a></td>
	<td><?php echo $projeto['descricao'] ?></td>
	<td><?php echo $projeto['dataExec'] ?></td>
	<td><?php echo $projeto['link'] ?></td>
	<td><p>
	<img src="<?php echo HOME_URI . '/views/_uploads/' . $projeto['imagem']; ?>" width="30px">
			</p></td>
<td>
	<a href="<?php echo $edit_uri . $projeto['idProjecto'] ?>">
		Editar
	</a> 
	  &nbsp;&nbsp;
	<a href="<?php echo $delete_uri . $projeto['idProjecto'] ?>">
        Apagar
    </a>
 </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>

    <?php //$modelo->paginacao(); ?>
</div> <!-- .wrap -->
