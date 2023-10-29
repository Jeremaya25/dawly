<?php $this->extend('layouts/base_layout') ?>

<?php $this->section('content')?>
  <script src="<?=base_url('ckeditor/ckeditor.js')?>"></script>
  <div class="card m-auto p-3" style="max-width: 750px">
    <div class="card-body">
      <div class="text-center">
          <a href="<?=base_url("/private")?>">
              <button class="btn btn-outline-secondary active">Url</button>
          </a>
          <a href="<?=base_url("/private/files")?>">
              <button class="btn btn-outline-secondary">File</button>
          </a>
          <a href="<?=base_url("/2fa")?>">
              <button class="btn btn-outline-secondary">2FA</button>
          </a>
      </div>
      <h1 class="card-title text-center mb-4">Introdueix la URL a escurçar</h1>
      <form action="<?=base_url()?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="url" value="<?=old("url")?>" placeholder="URL" aria-label="URL" aria-describedby="basic-addon2">
            <input class="btn btn-outline-secondary" type="submit" value="Escurçar">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon2"><?=base_url()?></span>
            <input type="text" class="form-control" name="route" value="<?=old("route")?>" placeholder="Ruta (Opcional)" aria-label="Ruta" aria-describedby="basic-addon2">
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" name="shortname" value="<?=old("shortname")?>" placeholder="Nom">
            <label for="floatingInput">Nom (Opcional)</label>
          </div>
          <div class="mb-3">
            <input type="date" class="form-control w-50" name="expiration" value="<?=old("expiration")?>" placeholder="Data d'expiració (Opcional)">
            <p style="color:grey">La URL expirarà automàticament a la data indicada</p>
          </div>
          <div class="mb-3">
            <textarea id="description" class="form-control" name="description" value="<?=old("description")?>" placeholder="Descripció (Opcional)" rows="4"></textarea>
            <p style="color:grey">La descripció es mostrarà juntament amb l'enllaç quan algú hi intenti accedir (WYSIWYG)</p>
          </div>
          <?php if (session("error")): ?>
            <div class="alert alert-danger" role="alert">
              <?= session("error") ?>
            </div>
          <?php endif ?>
        </div>
      </form>
    </div>
  </div>
  <div class="m-auto mt-3" style="width:fit-content">
    <a href="<?=base_url("/private/dashboard")?>">
      <button class="btn btn-secondary">Manage URLS</button>
    </a>
    <?php if(has_permission("urls.manage")): ?>
      <a href="<?=base_url("/private/users")?>">
        <button class="btn btn-secondary">Manage Users</button>
      </a>
    <?php endif ?>
  </div>
  <script>
    ClassicEditor
        .create( document.querySelector( '#description' ) )
        .then(editor => {
           
            editor.editing.view.change(writer => {
                writer.setStyle('min-height', '150px', editor.editing.view.document.getRoot());
            });

        })
        .catch( error => {
            console.error( error );
        } );
  </script>
<?php $this->endSection() ?>
