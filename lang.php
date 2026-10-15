<?php
/* Dictionnaire PHP (mêmes clés que le JS) + helpers de traduction. */
$SA_LANGS = ['es', 'fr', 'en', 'de', 'pt'];   // ← 'de' ajouté
$SA_DEFAULT = 'es';

$SA_LANG = isset($_POST['lang']) ? $_POST['lang'] : (isset($_GET['lang']) ? $_GET['lang'] : $SA_DEFAULT);
if (!in_array($SA_LANG, $SA_LANGS, true)) { $SA_LANG = $SA_DEFAULT; }

$SA_T = [
  'es' => [
    'recap_t' => 'Revisa tus datos', 'recap_lead' => 'Comprueba la información antes de confirmar. Podrás corregirla si es necesario.',
    'recap_ok' => 'Confirmar y continuar', 'recap_edit' => 'Modificar',
    'civilite' => 'Civilité', 'nom' => 'Nom & prénom(s)', 'naissance' => 'Date de naissance',
    'pays' => 'Pays de résidence', 'ville' => 'Ville de résidence', 'adresse' => 'Adresse de résidence',
    'telephone' => 'Téléphone', 'marital' => 'Marié(e)', 'profession' => 'Profession',
    'enfants' => 'Avez-vous des enfants', 'nb_enfants' => 'Nombre d’enfants', 'montant' => '¿De qué cantidad necesita?',
    'source' => 'Comment avez-vous entendu parler de la fondation ?', 'mode_paiement' => 'Forma de recibir la ayuda',
    'doc_t' => 'Verificación de documentos', 'doc_lead' => 'Último paso: adjunta los documentos justificativos.',
    'doc_send' => 'Enviar mi expediente', 'thanks_t' => '¡Solicitud registrada!',
    'thanks_lead' => 'Hemos recibido tu expediente. Te contactaremos en las próximas 24 horas.',
    'ref' => 'Referencia', 'back_home' => 'Volver al inicio', 'recap_link' => 'Solicitud',
    'err' => 'Por favor, complete correctamente los campos obligatorios.'
  ],
  'de' => [
    'recap_t' => 'Prüfen Sie Ihre Angaben', 'recap_lead' => 'Kontrollieren Sie die Angaben vor der Bestätigung.',
    'recap_ok' => 'Bestätigen und weiter', 'recap_edit' => 'Ändern',
    'civilite' => 'Anrede', 'nom' => 'Name & Vorname(n)', 'naissance' => 'Geburtsdatum',
    'pays' => 'Wohnsitzland', 'ville' => 'Wohnort', 'adresse' => 'Wohnadresse',
    'telephone' => 'Telefon', 'marital' => 'Verheiratet', 'profession' => 'Beruf',
    'enfants' => 'Haben Sie Kinder', 'nb_enfants' => 'Anzahl der Kinder', 'montant' => 'Welchen Betrag benötigen Sie?',
    'source' => 'Wie haben Sie von der Stiftung erfahren?',
    'mode_paiement' => 'Auszahlungsart', 'doc_t' => 'Dokumentenprüfung', 'doc_lead' => 'Letzter Schritt: Fügen Sie die Nachweise bei.',
    'doc_send' => 'Meinen Antrag senden', 'thanks_t' => 'Antrag registriert!',
    'thanks_lead' => 'Wir haben Ihren Antrag erhalten. Wir kontaktieren Sie innerhalb der nächsten 24 Stunden.',
    'ref' => 'Referenz', 'back_home' => 'Zur Startseite', 'recap_link' => 'Antrag',
    'err' => 'Bitte füllen Sie die Pflichtfelder korrekt aus.'
  ],
  'fr' => [
    'recap_t' => 'Relisez vos informations', 'recap_lead' => 'Vérifiez les informations avant de confirmer. Vous pourrez les corriger si besoin.',
    'recap_ok' => 'Confirmer et continuer', 'recap_edit' => 'Modifier',
    'civilite' => 'Civilité', 'nom' => 'Nom & prénom(s)', 'naissance' => 'Date de naissance',
    'pays' => 'Pays de résidence', 'ville' => 'Ville de résidence', 'adresse' => 'Adresse de résidence',
    'telephone' => 'Téléphone', 'marital' => 'Marié(e)', 'profession' => 'Profession',
    'enfants' => 'Avez-vous des enfants', 'nb_enfants' => 'Nombre d’enfants', 'montant' => 'De quel nombre avez-vous besoin',
    'source' => 'Comment avez-vous entendu parler de la fondation ?', 'mode_paiement' => 'Mode de versement',
    'doc_t' => 'Vérification des documents', 'doc_lead' => 'Dernière étape : joignez les pièces justificatives.',
    'doc_send' => 'Envoyer mon dossier', 'thanks_t' => 'Demande enregistrée !',
    'thanks_lead' => 'Nous avons reçu votre dossier. Nous vous contacterons dans les prochaines 24 Heures.',
    'ref' => 'Référence', 'back_home' => 'Retour à l’accueil', 'recap_link' => 'Demande',
    'err' => 'Merci de compléter correctement les champs obligatoires.'
  ],
  'en' => [
    'recap_t' => 'Review your details', 'recap_lead' => 'Check the information before confirming. You can correct it if needed.',
    'recap_ok' => 'Confirm and continue', 'recap_edit' => 'Edit',
    'civilite' => 'Civilité', 'nom' => 'Nom & prénom(s)', 'naissance' => 'Date of birth',
    'pays' => 'Pays de résidence', 'ville' => 'Ville de résidence', 'adresse' => 'Adresse de résidence',
    'telephone' => 'Téléphone', 'marital' => 'Marié(e)', 'profession' => 'Profession',
    'enfants' => 'Avez-vous des enfants', 'nb_enfants' => 'Nombre d’enfants', 'montant' => 'How much do you need?',
    'source' => 'Comment avez-vous entendu parler de la fondation ?', 'mode_paiement' => 'Payment method',
    'doc_t' => 'Document verification', 'doc_lead' => 'Last step: attach your supporting documents.',
    'doc_send' => 'Send my file', 'thanks_t' => 'Application registered!',
    'thanks_lead' => 'We received your file. We will contact you within the next 24 hours.',
    'ref' => 'Reference', 'back_home' => 'Back to home', 'recap_link' => 'Application',
    'err' => 'Please fill in the required fields correctly.'
  ],
  'pt' => [
    'recap_t' => 'Reveja os seus dados', 'recap_lead' => 'Verifique as informações antes de confirmar.',
    'recap_ok' => 'Confirmar e continuar', 'recap_edit' => 'Alterar',
    'civilite' => 'Civilité', 'nom' => 'Nom & prénom(s)', 'naissance' => 'Date de naissance',
    'pays' => 'Pays de résidence', 'ville' => 'Ville de résidence', 'adresse' => 'Adresse de résidence',
    'telephone' => 'Téléphone', 'marital' => 'Marié(e)', 'profession' => 'Profession',
    'enfants' => 'Avez-vous des enfants', 'nb_enfants' => 'Nombre d’enfants', 'montant' => 'De que quantia precisa?',
    'source' => 'Comment avez-vous entendu parler de la fondation ?', 'mode_paiement' => 'Forma de recebimento',
    'doc_t' => 'Verificação de documentos', 'doc_lead' => 'Último passo: anexe os documentos comprovativos.',
    'doc_send' => 'Enviar o meu processo', 'thanks_t' => 'Pedido registado!',
    'thanks_lead' => 'Recebemos o seu processo. Entraremos em contacto nas próximas 24 horas.',
    'ref' => 'Referência', 'back_home' => 'Voltar ao início', 'recap_link' => 'Pedido',
    'err' => 'Preencha corretamente os campos obrigatórios.'
  ]
];

/* Libellés d'affichage des tranches de montant (clés m1..m9) */
$SA_MONTANT = [
  'es' => ['m1'=>'10 000 €','m2'=>'20 000 €','m3'=>'30 000 €','m4'=>'40 000 €','m5'=>'50 000 €','m6'=>'60 000 €','m7'=>'70 000 €','m8'=>'80 000 €','m9'=>'90 000 €','m10'=>'100 000 €'],
  'fr' => ['m1'=>'10 000 €','m2'=>'20 000 €','m3'=>'30 000 €','m4'=>'40 000 €','m5'=>'50 000 €','m6'=>'60 000 €','m7'=>'70 000 €','m8'=>'80 000 €','m9'=>'90 000 €','m10'=>'100 000 €'],
  'en' => ['m1'=>'€10,000','m2'=>'€20,000','m3'=>'€30,000','m4'=>'€40,000','m5'=>'€50,000','m6'=>'€60,000','m7'=>'€70,000','m8'=>'€80,000','m9'=>'€90,000','m10'=>'€100,000'],
  'de' => ['m1'=>'10.000 €','m2'=>'20.000 €','m3'=>'30.000 €','m4'=>'40.000 €','m5'=>'50.000 €','m6'=>'60.000 €','m7'=>'70.000 €','m8'=>'80.000 €','m9'=>'90.000 €','m10'=>'100.000 €'],
  'pt' => ['m1'=>'10 000 €','m2'=>'20 000 €','m3'=>'30 000 €','m4'=>'40 000 €','m5'=>'50 000 €','m6'=>'60 000 €','m7'=>'70 000 €','m8'=>'80 000 €','m9'=>'90 000 €','m10'=>'100 000 €']

];

function montant_nom($k, $lang) {
  global $SA_MONTANT;
  return $SA_MONTANT[$lang][$k] ?? ($SA_MONTANT['es'][$k] ?? $k);
}

function t($k) { global $SA_T, $SA_LANG, $SA_DEFAULT; return $SA_T[$SA_LANG][$k] ?? ($SA_T[$SA_DEFAULT][$k] ?? $k); }
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function h($k) { return e(t($k)); }