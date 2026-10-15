<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#101827">
  <meta name="description" content="Capture sécurisée de documents avec interface multilingue.">
  <title data-i18n="title">Verificación de documento</title>
  <link rel="stylesheet" href="assets/styles.css">
<style>
.header-tools{display:flex;align-items:center;gap:12px;flex-wrap:wrap;justify-content:flex-end}
.language-label{font-size:.85rem;color:inherit}
#languageSelect{max-width:150px;padding:7px 9px;border:1px solid #64748b;border-radius:8px;background:#fff;color:#101827;font:inherit}
@media(max-width:600px){.header-tools{gap:7px}.secure{font-size:.75rem}}
</style>
</head>
<body>
<header class="topbar">
  <div class="brand"><span class="brand-mark">✓</span><span>Vérif<span class="muted">Docs</span></span></div>
  <div class="header-tools">
    <label class="language-label" for="languageSelect" data-i18n="languageLabel">Idioma</label>
    <select id="languageSelect" aria-label="Choisir la langue">
      <option value="auto" data-i18n="autoLanguage">Automático (idioma del navegador)</option>
      <option value="fr">Français</option><option value="en">English</option>
      <option value="es">Español</option><option value="pt">Português</option>
      <option value="de">Deutsch</option><option value="it">Italiano</option>
      <option value="nl">Nederlands</option><option value="ru">Русский</option>
      <option value="ar">العربية</option><option value="zh">中文</option>
      <option value="hi">हिन्दी</option><option value="ja">日本語</option>
      <option value="tr">Türkçe</option><option value="sw">Kiswahili</option>
    </select>
    <span class="secure"><span class="dot"></span><span data-i18n="secure">Se requiere una conexión segura (HTTPS)</span></span>
  </div>
</header>
<main class="shell">
  <section class="intro">
    <span class="eyebrow" data-i18n="eyebrow">VERIFICACIÓN DE DOCUMENTO</span>
    <h1 data-i18n="headline">Fotografíe su<br><span>documento oficial.</span></h1>
    <p data-i18n="intro">Siga el marco en pantalla para obtener una imagen nítida, completa y legible.</p>
    <div class="privacy-note"><span>🔒</span><span data-i18n="privacy">Envíe documentos originales y claramente legibles.</span></div>
  </section>
  <form id="docForm" action="traitement.php" method="post" enctype="multipart/form-data">
    <section class="panel">
      <div class="section-head"><span class="step">01</span><div><h2 data-i18n="documentType">Tipo de documento</h2><p data-i18n="chooseDocument">Elija el documento que desea proporcionar.</p></div></div>
      <div class="type-grid">
        <label class="type-option"><input type="radio" name="document_type" value="cni" checked><span class="type-icon">▤</span><b data-i18n="idCard">Documento de identidad</b><small data-i18n="idShort">DNI</small></label>
        <label class="type-option"><input type="radio" name="document_type" value="permis"><span class="type-icon">▣</span><b data-i18n="driverLicense">Permiso de conducir</b><small data-i18n="frontBack">Anverso y reverso</small></label>
        <label class="type-option"><input type="radio" name="document_type" value="passeport"><span class="type-icon">▤</span><b data-i18n="passport">Pasaporte</b><small data-i18n="identityPage">Página de identidad</small></label>
      </div>
    </section>
    <section class="panel">
      <div class="section-head"><span class="step">02</span><div><h2 data-i18n="takePhotos">Tomar fotografías</h2><p data-i18n="cameraInstructions">Permita el acceso a la cámara, coloque el documento en el marco y tome la foto.</p></div></div>
      <div class="capture-grid" id="captureGrid">
        <div class="capture-card" data-side="recto">
          <div class="capture-top"><b id="frontTitle" data-i18n="front">Anverso</b><span class="status" id="frontStatus" data-i18n="toCapture">Pendiente de captura</span></div>
          <div class="preview" id="frontPreview"><div class="placeholder"><span class="camera-symbol">▣</span><span data-i18n="photoHere">La foto aparecerá aquí</span></div></div>
          <input class="file-input" type="file" name="recto" id="recto" accept="image/*" capture="environment" required>
          <div class="quality" id="frontQuality" data-i18n="frameTips">Encuadre los cuatro bordes y evite los reflejos.</div>
          <button type="button" class="secondary capture-btn" data-target="recto" data-i18n="openCamera">Abrir la cámara</button>
        </div>
        <div class="capture-card" id="backCard" data-side="verso">
          <div class="capture-top"><b data-i18n="back">Reverso</b><span class="status" id="backStatus" data-i18n="toCapture">Pendiente de captura</span></div>
          <div class="preview" id="backPreview"><div class="placeholder"><span class="camera-symbol">▣</span><span data-i18n="photoHere">La foto aparecerá aquí</span></div></div>
          <input class="file-input" type="file" name="verso" id="verso" accept="image/*" capture="environment" required>
          <div class="quality" id="backQuality" data-i18n="frameTips">Encuadre los cuatro bordes y evite los reflejos.</div>
          <button type="button" class="secondary capture-btn" data-target="verso" data-i18n="openCamera">Abrir la cámara</button>
        </div>
      </div>
      <p class="hint" data-i18n="cameraHint">En algunos teléfonos, «Abrir la cámara» inicia directamente la cámara. El sitio no guarda las fotos hasta que envíe el formulario.</p>
    </section>
    <section class="panel">
      <div class="section-head"><span class="step">03</span><div><h2 data-i18n="finalCheck">Verificación final</h2><p data-i18n="finalInstructions">Compruebe que el texto sea legible y que se vea el documento completo.</p></div></div>
      <label class="consent"><input type="checkbox" id="consent" name="consent" value="yes" required><span data-i18n="consent">Confirmo que las fotos corresponden al documento que deseo enviar y autorizo su tratamiento para la finalidad indicada por el responsable del sitio.</span></label>
      <div class="form-error" id="formError" role="alert" hidden></div>
      <button class="primary" type="submit" id="submitBtn"><span data-i18n="sendDocuments">Enviar documentos</span> <span>→</span></button>
      <p class="fineprint" data-i18n="fineprint">Las imágenes se revisan automáticamente de forma orientativa. La calidad no garantiza la autenticidad del documento ni la exactitud de la información.</p>
    </section>
  </form>
  <footer data-i18n="footer">VérifDocs · Envío de documentos confidenciales</footer>
</main>
<script src="assets/js/verifdocs-i18n.js"></script>
<script src="assets/app.js"></script>
</body>
</html>