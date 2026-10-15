document.addEventListener('DOMContentLoaded', function () {

  /* ---- 1. Langue : détection + sélecteur ---- */
  document.documentElement.lang = SA.lang;
  SA.apply();
  document.querySelectorAll('[data-lang]').forEach(function (b) {
    b.addEventListener('click', function () { SA.setLang(b.dataset.lang); });
  });

  /* ---- 2. Menu mobile ---- */
  var burger = document.querySelector('.burger'), menu = document.querySelector('.nav ul');
  if (burger) burger.addEventListener('click', function () {
    menu.classList.toggle('open');
    burger.setAttribute('aria-expanded', menu.classList.contains('open'));
  });

  /* ---- 3. Logo interactif : clic = battement + retour haut ---- */
  var brand = document.querySelector('.brand');
  if (brand) brand.addEventListener('click', function () {
    document.querySelectorAll('.heart').forEach(function (h) {
      h.style.animationDuration = '.55s';
      setTimeout(function () { h.style.animationDuration = ''; }, 1300);
    });
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  /* ---- 4. Apparition au scroll ---- */
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
  }, { threshold: .12 });
  document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });

  /* ---- 5. Liste des pays (traduite) + indicatif automatique ---- */
  var selPays = document.getElementById('pays');
  var prefix = document.getElementById('prefix');
  var indicatif = document.getElementById('indicatif');

  function buildCountries() {
    if (!selPays) return;
    var dn = new Intl.DisplayNames([SA.lang], { type: 'region' });
    var list = SA.COUNTRIES.map(function (c) {
      var name;
      try { name = dn.of(c) || c; } catch (e) { name = c; }
      return { code: c, name: name };
    }).sort(function (a, b) { return a.name.localeCompare(b.name, SA.lang); });

    var keep = selPays.value;
    selPays.innerHTML = '<option value="">' + SA.t('f_select') + '</option>' +
      list.map(function (o) { return '<option value="' + o.code + '">' + o.name + '</option>'; }).join('');
    if (keep) selPays.value = keep;
    syncPrefix();
  }

  function syncPrefix() {
    if (!selPays || !prefix) return;
    var c = selPays.value;
    var d = c ? SA.DIAL[c] : '';
    prefix.textContent = d ? '+' + d : '+';
    if (indicatif) indicatif.value = d ? '+' + d : '';
  }

  if (selPays) {
    buildCountries();
    selPays.addEventListener('change', syncPrefix);
    document.addEventListener('sa:lang', buildCountries);
    /* pays pré-rempli depuis le navigateur (region) si dispo */
    var reg = (navigator.language || '').split('-')[1];
    if (reg && SA.COUNTRIES.indexOf(reg.toUpperCase()) > -1) { selPays.value = reg.toUpperCase(); syncPrefix(); }
  }

  /* ---- 6. Options des selects traduits ---- */
  function fillOptions() {
    var map = {
      civilite: ['o_sr', 'o_sra', 'o_srta', 'o_otro'],
      marital: ['o_soltero', 'o_casado', 'o_union', 'o_viudo', 'o_div'],
      enfants: ['o_si', 'o_no'],
      source: ['o_tv', 'o_radio', 'o_social', 'o_affiche'],
      mode_paiement: ['o_virement', 'o_cheque']
    };
    Object.keys(map).forEach(function (id) {
      var el = document.getElementById(id);
      if (!el) return;
      var keep = el.value;
      el.innerHTML = '<option value="">' + SA.t('f_select') + '</option>' +
        map[id].map(function (k) {
          var v = k.replace('o_', '');
          if (id === 'mode_paiement') v = (k === 'o_virement' ? 'virement_bancaire' : 'cheque_banque');
          return '<option value="' + v + '">' + SA.t(k) + '</option>';
        }).join('');
      if (keep) el.value = keep;
    });
    var nb = document.getElementById('nb_enfants');
    if (nb && !nb.options.length) {
      var h = '<option value="">' + SA.t('f_select') + '</option>';
      for (var i = 0; i <= 10; i++) h += '<option value="' + i + '">' + i + '</option>';
      nb.innerHTML = h;
    }
    var mt = document.getElementById('montant');
    if (mt) {
      var keep2 = mt.value;
      var arr = SA.MONTANT[SA.lang] || SA.MONTANT[SA.DEFAULT];
      mt.innerHTML = '<option value="">' + SA.t('f_select') + '</option>' +
        arr.map(function (v, i) {
          return '<option value="m' + (i + 1) + '">' + v + '</option>';
        }).join('');
      if (keep2) mt.value = keep2;
    }
  }
  fillOptions();
  document.addEventListener('sa:lang', fillOptions);

  /* ---- 7. Enfants : champ conditionnel ---- */
  var selEnf = document.getElementById('enfants'), wrapNb = document.getElementById('wrap_nb');
  if (selEnf && wrapNb) selEnf.addEventListener('change', function () {
    wrapNb.classList.toggle('hide', selEnf.value !== 'si');
  });

  /* ---- 8. Validation du formulaire ---- */
  var form = document.getElementById('formAide');
  if (form) {
    form.addEventListener('submit', function (ev) {
      var bad = null;
      form.querySelectorAll('[required]').forEach(function (el) {
        var ok = el.value.trim() !== '';
        if (el.type === 'tel') ok = ok && /^[0-9 +().-]{6,}$/.test(el.value);
        el.classList.toggle('err', !ok);
        if (!ok && !bad) bad = el;
      });
      if (bad) {
        ev.preventDefault();
        var box = document.getElementById('errBox');
        box.textContent = SA.t('f_err'); box.classList.remove('hide');
        bad.focus(); bad.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
    form.querySelectorAll('.in').forEach(function (el) {
      el.addEventListener('input', function () { el.classList.remove('err'); });
    });
  }
});