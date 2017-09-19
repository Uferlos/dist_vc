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

  $page_position = (($id-1) * $limit);

  $query = "SELECT * FROM negocios ORDER BY nom ASC LIMIT $page_position, $limit";

  $results = $db->query($query);
?>

  <div class="box">
    <section class="section">
    <?php if($results->num_rows) : ?>
      <table width="100%" class="table is-narrow is-striped">
        <thead>
          <th>Nombre</th>
          <th>RIF</th>
          <th>Dirección</th>
          <th>Opciones</th>
        </thead>
        <?php while ($row = $results->fetch_assoc()) : ?>
        <tbody>
          <td><?php echo $row['nom'] ?></td>
          <td><?php echo $row['rif'] ?></td>
          <td><?php echo $row['dire'] ?></td>
          <td style="padding-left: 35px;">
            <button title="Editar" id="updNe" class="button is-small is-info" value="<?php echo $row['rif'] ?>">
              <span class="icon is-small">
                <i class="fa fa-pencil"></i>
              </span>
            </button>
          </td>
          <?php endwhile;
        $squery = "SELECT * FROM negocios";
        $fields = $db->query($squery);
        $total_rows = $fields->num_rows;
        $total = ceil($total_rows/$limit);
        ?>
        <tfoot>
          <td colspan="5">
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
    </section>
  </div>
<div id="content"></div>
<?php } ?>