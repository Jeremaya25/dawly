<?php $this->extend('layouts/base_layout') ?>

<?php $this->section('content')?> 
  <div class="card m-auto p-3" style="max-width: 750px">
    <div class="card-body">
      <h1 class="card-title text-center mb-4">La teva URL està llesta</h1>
        <div class="input-group mb-3">
            <input id="yourUrl" type="text" class="form-control" value="<?=$route?>" disabled="disabled">
            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">Copiar</button>
        </div>
    </div>
  </div>
  <script>
    function copyToClipboard() {
        const el = document.getElementById('yourUrl');
        el.select();
        el.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(el.value);
        alert('URL copiada al porta-retalls');
    }
  </script>
<?php $this->endSection() ?>