<?php
if(($_COOKIE['nom'] == null) || ($_COOKIE['usu'] == null) || ($_COOKIE['lvl'] == null) || ($_COOKIE['ape'] == null)){
  header('location: ../');
}else{
  include 'files/config.php';

  $db = new mysqli(host, usr, pssw, db);
  $db->set_charset('utf8');
  if($db->connect_errno){
    echo $db->connect_error;
    exit();
  }

  $limit = 10;

  if(isset($_POST['id'])){
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
  }else{
    $id = 1; //if there's no page number, set it to 1
  }

  $today = date('Y-d-m');
  $ns = isset($_POST['ns']) ? $_POST['ns'] : '';

  $page_position = (($id-1) * $limit);
  
  //$query = "SELECT ventas.fecha, ventas.id AS id_ven, ventas.id_prod, ventas.cant, ventas.valor, ventas.t_pago, ventas.c_pago, ventas.cliente, productos.id, productos.nom, negocios.nom AS cl_nom, negocios.rif FROM (ventas INNER JOIN productos ON ventas.id_prod = productos.id) INNER JOIN negocios ON ventas.cliente = negocios.rif WHERE ventas.fecha LIKE '%$fe%' ORDER BY ventas.fecha ASC LIMIT $page_position, $limit";

  //$query = "SELECT ventas.fecha, ANY_VALUE(negocios.nom) AS nom, ANY_VALUE(negocios.rif) AS rif FROM ventas INNER JOIN negocios ON ventas.cliente = negocios.rif WHERE ventas.fecha LIKE '%$fe%' GROUP BY ventas.fecha LIMIT $page_position, $limit";

  $query = "SELECT ventas.cliente, negocios.nom, negocios.rif FROM ventas INNER JOIN negocios ON ventas.cliente = negocios.rif WHERE negocios.nom LIKE '%$ns%' OR negocios.rif LIKE '%$ns%' GROUP BY ventas.cliente LIMIT $page_position, $limit";


  if(!$results = $db->query($query)){
    echo $db->error;
    exit();
  }
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

</head>

<body>
<div class="box">
<div class="field has-addons has-addons-centered">
  <p class="control">
    <input id="ns" class="input" type="search" placeholder="Buscar Nombre/R.I.F" value="<?php echo $ns ?>">
  </p>
  <p class="control">
    <button id="search" class="button is-info">
      <i class="fa fa-search"></i>
    </button>
  </p>
</div>
<?php if($results->num_rows) : ?>
  <table width="100%" class="table is-bordered is-small">
    <thead>
      <td>Cliente</td>
      <td>RIF</td>
      <td class="has-text-centered">Ver Facturas</td>
    </thead>
    <?php while ($row = $results->fetch_assoc()) : ?>
    <tbody>
      <td><?php echo $row['nom'] ?></td>
      <td><?php echo $row['rif'] ?></td>
      <td class="has-text-centered">
        <button class="button is-small is-success" id="wat_fac" value="<?php echo $row['rif'] ?>">
          <span class="icon is-small">
            <i class="fa fa-book"></i>
          </span>
        </button>
      </td>
      <?php endwhile;
      $squery = "SELECT ventas.cliente, negocios.nom, negocios.rif FROM ventas INNER JOIN negocios ON ventas.cliente = negocios.rif GROUP BY ventas.cliente";
      $fields = $db->query($squery);
      $total_rows = $fields->num_rows;
      $total = ceil($total_rows/$limit);
      ?>
      <tfoot>
          <td colspan="8">
            <nav class="pagination is-centered is-small"><?php
              if($id > 1) :?>
              <a data-page="<?php echo ($id-1); ?>" class="pagination-previous">Anterior</a>
              <?php else : ?>
              <a class="pagination-previous" disabled>Anterior</a><?php endif;
              
              if($id != $total) : ?>
              <a class="pagination-next" data-page="<?php echo ($id+1); ?>">Siguiente</a>
              <?php else : ?>
              <a class="pagination-next" disabled>Siguiente</a><?php endif;
              
              for($i = 1; $i <= $total; $i++) : ?>
              <ul class="pagination-list">
              <?php if($id == $i) : ?>
                <li>
                  <a class="pagination-link is-current"><?php echo $i ?></a>
                </li>
                <?php else : ?>
                <li><?php if($i == $id) : ?>
                  <a class="pagination-link is-current"><?php echo $i ?></a>
                </li><?php else : ?>
                <li>
                  <a class="pagination-link" data-page="<?php echo $i; ?>"><?php echo $i ?></a>
                </li><?php endif;
              endif; ?>
              </ul>
              <?php endfor; ?>
            </nav>
          </td>
        </tfoot>
      </table><?php else : ?>
    <article class="message is-dark">
      <div class="message-header">
        <p>Tabla Vacia</p>
      </div>
      <div class="message-body has-text-centered">
        <strong>No hay resultados</strong>
      </div>
    </article>
  <?php endif; ?>
</div>
<div id="content"></div>
</body>
</html>

<?php } ?>