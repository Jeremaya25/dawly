<header class="p-3">
    <div class="text-center">
      <a class="logo text-decoration-none" href="<?= base_url() ?>">
        <strong class="fs-1 text-secondary">
          DAWLY
        </strong>
      </a>
      <?php if (logged_in()): ?>
        <a class="text-decoration-none" href="<?=base_url("/logout")?>">Logout</a>
      <?php else: ?>
        <a class="text-decoration-none" href="<?=base_url("/login")?>">Login</a>
      <?php endif ?>
    </div>
</header>