(() => {
  const tr = key => (window.VD && window.VD.t(key)) || key;
  const form = document.getElementById('docForm');
  const inputs = { recto: document.getElementById('recto'), verso: document.getElementById('verso') };
  const labels = {
    recto: { preview: document.getElementById('frontPreview'), status: document.getElementById('frontStatus'), quality: document.getElementById('frontQuality') },
    verso: { preview: document.getElementById('backPreview'), status: document.getElementById('backStatus'), quality: document.getElementById('backQuality') }
  };
  const backCard = document.getElementById('backCard');
  const backInput = inputs.verso;
  const frontTitle = document.getElementById('frontTitle');
  const err = document.getElementById('formError');
  const submit = document.getElementById('submitBtn');
  let objectUrls = {};

  function selectedType() {
    return form.querySelector('input[name="document_type"]:checked').value;
  }
  function updateType() {
    const passport = selectedType() === 'passeport';
    backCard.hidden = passport;
    backInput.required = !passport;
    frontTitle.textContent = passport ? tr('pageIdentity') : tr('front');
    inputs.recto.setAttribute('aria-label', passport ? tr('passportPhoto') : tr('frontPhoto'));
  }
  form.querySelectorAll('input[name="document_type"]').forEach(el => el.addEventListener('change', updateType));

  function checkImage(file) {
    return new Promise(resolve => {
      const img = new Image();
      const url = URL.createObjectURL(file);
      img.onload = () => {
        const canvas = document.createElement('canvas');
        const max = 500;
        const scale = Math.min(1, max / Math.max(img.naturalWidth, img.naturalHeight));
        canvas.width = Math.max(1, Math.round(img.naturalWidth * scale));
        canvas.height = Math.max(1, Math.round(img.naturalHeight * scale));
        const ctx = canvas.getContext('2d', {willReadFrequently:true});
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        const data = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
        let sum = 0, sumSq = 0, count = 0;
        for (let i = 0; i < data.length; i += 16) {
          const lum = .2126 * data[i] + .7152 * data[i+1] + .0722 * data[i+2];
          sum += lum; sumSq += lum * lum; count++;
        }
        const mean = sum / count;
        const variance = Math.max(0, sumSq / count - mean * mean);
        const ratio = img.naturalWidth / img.naturalHeight;
        const likelyDocumentShape = ratio > 1.15 && ratio < 2.3;
        const messages = [];
        if (file.size < 35 * 1024) messages.push(tr('fileLight'));
        if (Math.min(img.naturalWidth, img.naturalHeight) < 650) messages.push(tr('lowResolution'));
        if (mean < 55) messages.push(tr('darkImage'));
        else if (mean > 232) messages.push(tr('brightImage'));
        if (variance < 350) messages.push(tr('blurryImage'));
        if (!likelyDocumentShape) messages.push(tr('unusualFormat'));
        if (!messages.length) messages.push(tr('qualityOk'));
        URL.revokeObjectURL(url);
        resolve({messages, ok: messages.length === 1 && messages[0].startsWith('Contrôle indicatif'), width:img.naturalWidth,height:img.naturalHeight});
      };
      img.onerror = () => { URL.revokeObjectURL(url); resolve({messages:[tr('unreadableImage')],ok:false}); };
      img.src = url;
    });
  }

  async function showFile(key, file) {
    if (!file) return;
    const ui = labels[key];
    if (!file.type.startsWith('image/')) {
      ui.quality.textContent = tr('chooseImage');
      ui.quality.className = 'quality warn';
      inputs[key].value = '';
      return;
    }
    if (objectUrls[key]) URL.revokeObjectURL(objectUrls[key]);
    objectUrls[key] = URL.createObjectURL(file);
    ui.preview.replaceChildren();
    const img = document.createElement('img');
    img.src = objectUrls[key];
    img.alt = 'Aperçu du document sélectionné';
    ui.preview.appendChild(img);
    ui.status.textContent = tr('analyzing');
    ui.status.classList.remove('ok');
    ui.quality.textContent = tr('checkingImage');
    const result = await checkImage(file);
    ui.quality.textContent = result.messages.join(' ');
    ui.quality.className = 'quality ' + (result.ok ? 'good' : 'warn');
    ui.status.textContent = result.ok ? tr('statusReview') : tr('statusCheck');
    ui.status.classList.toggle('ok', result.ok);
  }

  Object.keys(inputs).forEach(key => {
    inputs[key].addEventListener('change', () => showFile(key, inputs[key].files[0]));
  });
  document.querySelectorAll('.capture-btn').forEach(btn => btn.addEventListener('click', () => inputs[btn.dataset.target].click()));

  form.addEventListener('submit', e => {
    err.hidden = true;
    const requiredKeys = selectedType() === 'passeport' ? ['recto'] : ['recto','verso'];
    const missing = requiredKeys.filter(key => !inputs[key].files || !inputs[key].files.length);
    if (missing.length) {
      e.preventDefault(); err.textContent = tr('selectAllPhotos'); err.hidden = false; return;
    }
    const consent = document.getElementById('consent');
    if (!consent.checked) { e.preventDefault(); err.textContent = tr('consentRequired'); err.hidden = false; return; }
    submit.disabled = true; submit.innerHTML = tr('sending');
  });
  updateType();
  document.addEventListener('verifdocs:languagechange', () => {
    updateType();
    Object.keys(inputs).forEach(key => {
      const file = inputs[key].files && inputs[key].files[0];
      if (file) showFile(key, file);
    });
  });
})();