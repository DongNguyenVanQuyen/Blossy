<!-- Includes/head.php -->
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $title ?? 'Blossy' ?></title>
  <!-- Các link css khác -->
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
  <!-- CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/reset.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/style.css?v=<?= time(); ?>" />
</head>
