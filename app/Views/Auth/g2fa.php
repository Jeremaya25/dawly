<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<form class="container" action="<?= base_url('2fa') ?>" method="POST">
    <div class="row">
        <div class="col-sm-6 offset-sm-3">
            <div class="card">
                <h2 class="card-header">Escaneja el codi amb la teva aplicació</h2>
                <div class="card-body">
                    <img src="<?= $qrcode_image ?> "/>
                    <br>
                    <label for="field">Introdueix el número donat:</label><br>
                    <input type="text" name="field" id="text-field">
                    <input type="submit" value="Fet">
                </div>
            </div>
        </div>
    </div>
</form>