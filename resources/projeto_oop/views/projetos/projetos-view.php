<?php
// Evita acesso direto a este ficheiro
if (!defined('ABSPATH'))
    exit;
?>

<div class="wrap">
    <?php
// Número de posts por página
//$modelo->posts_por_pagina = 10;
// Lista projetos
    $lista = $modelo->listar_projetos();
    ?>
    <h1>Lista de Projetos</h1>
    <?php foreach ($lista as $projeto): ?>

<!-- Título -->
<h1>
	<a href="<?php echo HOME_URI ?>/projetos/index/<?php echo $projeto['idProjecto'] ?>">
		<?php echo $projeto['descricao'] ?>
	</a>
</h1>
        <?php
// Verifica se estamos a visualizar uma única projeto
if (is_numeric(chk_array($modelo->parametros, 0))): // single
            ?>
<?php
   $this->prev_page = true;
	if ($this->prev_page) {
		?>
		<a href="<?php echo HOME_URI ?>/projetos/index/">Voltar</a>
    <?php } ?>
        <p>
	   <?php echo $modelo->inverte_data($projeto['dataExec']); ?> | 
	   <?php echo $projeto['dataExec']; ?> 
            </p>

            <p>
                <img src="<?php echo HOME_URI . '/views/_uploads/' . $projeto['imagem']; ?>">
            </p>

            <?php echo $projeto['link']; ?>

        <?php endif;  // single  ?>

    <?php endforeach; ?>

</div> <!-- .wrap -->
