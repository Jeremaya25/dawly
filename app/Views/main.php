<?php $this->extend('layouts/base_layout') ?>

<?php $this->section('content')?> 
  <div class="card m-auto p-3" style="max-width: 750px">
    <div class="card-body">
      <h1 class="card-title text-center mb-4">Introdueix la URL a escurçar</h1>
      <form action="<?=base_url()?>" method="post">
        <?= csrf_field() ?>
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="url" placeholder="URL" aria-label="URL" aria-describedby="basic-addon2">
          <input class="btn btn-outline-secondary" type="submit" value="Escurçar">
        </div>
      </form>
    </div>
  </div>
<?php $this->endSection() ?>