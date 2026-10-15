<?php
require __DIR__ . '/lang.php';
$ref = isset($_GET['ref']) ? preg_replace('/[^A-Za-z0-9-]/', '', $_GET['ref']) : 'SA';
?>
<!DOCTYPE html><html lang="<?= e($SA_LANG) ?>"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= h('thanks_t') ?></title>
<link rel="icon" type="image/svg+xml" href="favicon.svg">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head><body>
<main class="wrap" style="text-align:center;padding:80px 22px">
  <div style="font-size:44px;color:#E62E86">♥</div>
  <h1 style="font-size:32px;margin-top:12px"><?= h('thanks_t') ?></h1>
  <p style="color:#6E5C7E;margin-top:14px;max-width:52ch;margin-left:auto;margin-right:auto"><?= e($SA_LANG === 'fr' ? 'Nous vous contacterons dans les prochaines 24 Heures.' : t('thanks_lead')) ?></p>
  <p style="margin-top:22px"><b><?= h('ref') ?> :</b> <?= e($ref) ?></p>
  <a class="btn btn-p" style="margin-top:26px" href="index.html?lang=<?= e($SA_LANG) ?>"><?= h('back_home') ?></a>
</main>
</body></html>