<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DAWLY</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
  <style>
    body {
      background-color: #f5f5f5;
      padding: 20px;
    }
  </style>
  <?= $this->renderSection('style') ?>
</head>
<body>
  <?= view('templates/header'); ?>
  <main>
    <?= $this->renderSection('content') ?>
  </main>
  <footer class="fixed-bottom">
    <div class="text-center">
      <p class="text-secondary">
        &copy; 2023 DAWLY
      </p>
    </div>
  </footer>
</body>
</html>