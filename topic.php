<?php
// topic.php
// index.php içinde login durumunda include edilir
// $page_number, $selected_juz, $user_id değişkenleri index.php'den gelir

$kitap_id  = isset($_GET['book']) ? (int)$_GET['book'] : 1;
$page_number_topic = isset($page_number) ? (int)$page_number : 0;
?>

<!-- ═══════════════════════════════════════════════
     TOPIC BAR — topicSelect seçili değilse gizli
     ═══════════════════════════════════════════════ -->
<div id="topic-bar-icerik" style="display:none">

    <!-- Yanıp sönen uyarı -->
    <div id="topic-uyari" style="text-align:center;padding:4px 0">
        <span id="topic-uyari-metin" style="
            color:#FFE0B2;font-size:12px;font-weight:500;cursor:pointer;
            animation: yanip-son 2s infinite;
        "
        title="Okuma tikini koymak için sayfayı bitirmeyi beklemeyin. Niyetine girdiyseniz tik işaretleyin. Sayfayı okuyamadan ayrılacaksanız sayfayı refresh yapın ve tiki kaldırın."
        onclick="tikAktifYap()">
            ☞ Okuma tikini işaretleyin
        </span>
    </div>

    <!-- Cüz tablosu -->
    <div id="topic-tablo-wrap" style="overflow-x:auto">
        <table id="topic-tablo" border="0" bgcolor="#97582B" cellspacing="0" cellpadding="0"
               style="border-collapse:collapse;white-space:nowrap">
            <tr id="topic-baslik-satir">
                <!-- PHP tarafından doldurulur -->
            </tr>
            <tr id="topic-cb-satir">
                <!-- PHP tarafından doldurulur -->
            </tr>
        </table>
    </div>

    <!-- Mevcut sayfa tik -->
    <div id="aktif-sayfa-tik" style="display:none;padding:6px 12px;background:#D79C71">
        <label style="color:#fff;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:8px">
            <input type="checkbox" id="aktif-sayfa-cb" style="width:16px;height:16px;cursor:pointer;accent-color:#FFE0B2">
            <span id="aktif-sayfa-etiket">Sayfa <strong id="aktif-sayfa-no"></strong> — Bu sayfayı okudum</span>
        </label>
    </div>

</div>

<!-- Yeni topic modal -->
<div id="topic-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
     background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:28px;width:400px;max-width:95vw;position:relative">
        <button onclick="topicModalKapat()"
                style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:20px;cursor:pointer">&times;</button>
        <h5 style="margin:0 0 16px;color:#97582B">Yeni Okuma Planı</h5>

        <div style="margin-bottom:12px">
            <label style="font-size:13px;color:#555;display:block;margin-bottom:4px">Başlık</label>
            <input type="text" id="nt-baslik" value="Kendi hatmim"
                   style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box">
        </div>

        <div style="margin-bottom:12px">
            <label style="font-size:13px;color:#555;display:block;margin-bottom:4px">Kitap</label>
            <select id="nt-kitap" onchange="ntKitapDegis()"
                    style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box">
                <!-- JS ile doldurulur -->
            </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
            <div>
                <label style="font-size:13px;color:#555;display:block;margin-bottom:4px">Başlangıç sayfası</label>
                <input type="number" id="nt-bas" min="0"
                       style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box">
            </div>
            <div>
                <label style="font-size:13px;color:#555;display:block;margin-bottom:4px">Bitiş sayfası</label>
                <input type="number" id="nt-son" min="0"
                       style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box">
            </div>
        </div>

        <div style="margin-bottom:20px">
            <label style="font-size:13px;color:#555;display:block;margin-bottom:4px">Son tarih (opsiyonel)</label>
            <input type="datetime-local" id="nt-tarih"
                   style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box">
        </div>

        <button onclick="topicKaydet()"
                style="width:100%;padding:11px;background:#97582B;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:15px;font-weight:600">
            Oluştur
        </button>
    </div>
</div>

<style>
@keyframes yanip-son {
    0%,100% { opacity:1; }
    50%      { opacity:.3; }
}
#topic-tablo td { padding:2px 3px; }
.topic-cb-hucre { text-align:center; }
.topic-no-hucre { text-align:center; color:#fff; font-size:11px; white-space:nowrap; }
.topic-no-hucre.aktif-cuz { background:#D79C71; }
.topic-bilgi-hucre { background:#D79C71; color:#fff; font-size:11px; padding:3px 8px !important; white-space:nowrap; }
</style>

<script>
// ─── Durum ───────────────────────────────────────────────────
var TOPIC_AKTIF = null;   // Seçili topic objesi
var TIK_AKTIF   = false;  // Kullanıcı tika tıkladı mı
var OKUNANLAR   = {};     // { sayfa_no: true }
var KITAP_ID    = <?= (int)$kitap_id ?>;
var SAYFA_NO    = <?= (int)$page_number_topic ?>;

// ─── API yardımcısı ──────────────────────────────────────────
function okumaApi(action, data, cb) {
    fetch('okuma_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(Object.assign({ action: action }, data))
    }).then(r => r.json()).then(cb).catch(function(e){ console.error(e); });
}

// ─── topicSelect değişince ───────────────────────────────────
function topicSecildi(val) {
    var bar = document.getElementById('topic-bar-icerik');
    TIK_AKTIF = false;

    if (!val || val === '0') {
        bar.style.display = 'none';
        TOPIC_AKTIF = null;
        return;
    }

    // Topic listesinden bul
    okumaApi('topic_liste', {}, function(res) {
        if (!res.basari) return;
        var topic = res.liste.find(function(t){ return t.id == val; });
        if (!topic) return;

        TOPIC_AKTIF = topic;
        KITAP_ID    = parseInt(topic.kitap_id);
        bar.style.display = 'block';

        // Tabloyu oluştur
        topicTabloOlustur(topic);

        // Okunanları yükle
        okumaApi('okunanlar', {
            kitap_id:  KITAP_ID,
            sayfa_bas: parseInt(topic.sayfa_bas),
            sayfa_son: parseInt(topic.sayfa_son)
        }, function(res2) {
            OKUNANLAR = {};
            (res2.okunanlar || []).forEach(function(s){ OKUNANLAR[s] = true; });
            okunanIsaretle();
            aktifSayfaTikGoster();
        });
    });
}

// ─── Tablo oluştur ───────────────────────────────────────────
function topicTabloOlustur(topic) {
    var bas    = parseInt(topic.sayfa_bas);
    var son    = parseInt(topic.sayfa_son);
    var tarih  = topic.son_tarih
        ? 'Son tarih: ' + new Date(topic.son_tarih).toLocaleString('tr-TR', {dateStyle:'short', timeStyle:'short'})
        : topic.baslik;

    var baslikSatir = document.getElementById('topic-baslik-satir');
    var cbSatir     = document.getElementById('topic-cb-satir');
    baslikSatir.innerHTML = '';
    cbSatir.innerHTML     = '';

    // Sol bilgi hücresi — başlık satırı
    var td0 = document.createElement('td');
    td0.className   = 'topic-bilgi-hucre';
    td0.colSpan     = 2;
    td0.style.minWidth = '120px';
    td0.innerHTML   = '<b>' + (topic.baslik||'') + '</b><br><small>' + tarih + '</small>';
    baslikSatir.appendChild(td0);

    // Sol bilgi hücresi — cb satırı
    var td0b = document.createElement('td');
    td0b.className = 'topic-bilgi-hucre';
    td0b.colSpan   = 2;
    td0b.innerHTML = '<span id="topic-oran">0/' + (son - bas + 1) + '</span>';
    cbSatir.appendChild(td0b);

    // Her sayfa için hücre
    for (var s = bas; s <= son; s++) {
        (function(sayfa) {
            // Başlık satırı — sayfa numarası
            var tdNo = document.createElement('td');
            tdNo.className = 'topic-no-hucre' + (sayfa === SAYFA_NO ? ' aktif-cuz' : '');
            tdNo.style.color = sayfa === SAYFA_NO ? '#97582B' : '#fff';
            tdNo.textContent = sayfa;
            baslikSatir.appendChild(tdNo);

            // CB satırı — checkbox
            var tdCb = document.createElement('td');
            tdCb.className = 'topic-cb-hucre';
            var cb = document.createElement('input');
            cb.type    = 'checkbox';
            cb.id      = 'cb-' + sayfa;
            cb.dataset.sayfa = sayfa;
            cb.disabled = true; // Başlangıçta pasif
            cb.style.cursor = 'not-allowed';
            cb.style.accentColor = '#D79C71';
            cb.addEventListener('change', function() {
                sayfaIsaretle(sayfa, this.checked);
            });
            tdCb.appendChild(cb);
            cbSatir.appendChild(tdCb);
        })(s);
    }
}

// ─── Okunanları işaretle ─────────────────────────────────────
function okunanIsaretle() {
    Object.keys(OKUNANLAR).forEach(function(s) {
        var cb = document.getElementById('cb-' + s);
        if (cb) cb.checked = true;
    });
    oranGuncelle();
}

// ─── Oran güncelle ───────────────────────────────────────────
function oranGuncelle() {
    if (!TOPIC_AKTIF) return;
    var toplam  = parseInt(TOPIC_AKTIF.sayfa_son) - parseInt(TOPIC_AKTIF.sayfa_bas) + 1;
    var okunan  = Object.keys(OKUNANLAR).length;
    var oranEl  = document.getElementById('topic-oran');
    if (oranEl) oranEl.textContent = okunan + '/' + toplam + ' (%' + Math.round(okunan/toplam*100) + ')';
}

// ─── Tik aktif yap (yanıp sönen yazıya tıklayınca) ──────────
function tikAktifYap() {
    if (!TOPIC_AKTIF) return;
    TIK_AKTIF = true;

    // Uyarı yazısını gizle
    var uyari = document.getElementById('topic-uyari');
    if (uyari) uyari.style.display = 'none';

    // Tüm checkbox'ları aktif et
    var bas = parseInt(TOPIC_AKTIF.sayfa_bas);
    var son = parseInt(TOPIC_AKTIF.sayfa_son);
    for (var s = bas; s <= son; s++) {
        var cb = document.getElementById('cb-' + s);
        if (cb) {
            cb.disabled = false;
            cb.style.cursor = 'pointer';
        }
    }

    // Aktif sayfa cb'sini aktif et
    var aktifCb = document.getElementById('aktif-sayfa-cb');
    if (aktifCb) aktifCb.disabled = false;

    // Tabloyu gizle — sadece aktif sayfa cb'si görünsün
    document.getElementById('topic-tablo-wrap').style.display = 'none';
    document.getElementById('aktif-sayfa-tik').style.display  = 'block';
}

// ─── Aktif sayfa tik göster ──────────────────────────────────
function aktifSayfaTikGoster() {
    if (!TOPIC_AKTIF) return;
    var bas = parseInt(TOPIC_AKTIF.sayfa_bas);
    var son = parseInt(TOPIC_AKTIF.sayfa_son);
    if (SAYFA_NO < bas || SAYFA_NO > son) return;

    var noEl = document.getElementById('aktif-sayfa-no');
    if (noEl) noEl.textContent = SAYFA_NO;

    var aktifCb = document.getElementById('aktif-sayfa-cb');
    if (aktifCb) {
        aktifCb.checked  = !!OKUNANLAR[SAYFA_NO];
        aktifCb.disabled = !TIK_AKTIF;
        aktifCb.addEventListener('change', function() {
            sayfaIsaretle(SAYFA_NO, this.checked);
            // Aynı zamanda tablodaki cb'yi de güncelle
            var tabloCb = document.getElementById('cb-' + SAYFA_NO);
            if (tabloCb) tabloCb.checked = this.checked;
        });
    }
}

// ─── Sayfa işaretle → API ────────────────────────────────────
function sayfaIsaretle(sayfa, durum) {
    okumaApi('sayfa_isle', {
        kitap_id: KITAP_ID,
        sayfa_no: sayfa,
        islem:    durum ? 'oku' : 'geri_al'
    }, function(res) {
        if (!res.basari) return;
        if (durum) {
            OKUNANLAR[sayfa] = true;
        } else {
            delete OKUNANLAR[sayfa];
        }
        oranGuncelle();
    });
}

// ─── topicSelect change olayını dinle ───────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('topicSelect');
    if (sel) {
        sel.addEventListener('change', function() {
            topicSecildi(this.value);
        });
        // Sayfa yüklenince aktif topic varsa uygula
        if (sel.value && sel.value !== '0') {
            topicSecildi(sel.value);
        }
    }

    // + butonu
    var plusBtn = document.getElementById('plusButton');
    if (plusBtn) {
        plusBtn.addEventListener('click', function() {
            topicModalAc();
        });
    }
});

// ─── Yeni topic modal ────────────────────────────────────────
function topicModalAc() {
    // Kitap listesini doldur
    var kitapSel = document.getElementById('nt-kitap');
    fetch('okuma_api.php?action=kitap_listesi_public')
        .then(r => r.json())
        .then(function(res) {
            kitapSel.innerHTML = '';
            (res.kitaplar || [{id:1,baslik:"Kur'an-ı Kerim",sayfa_bas:0,sayfa_son:604}]).forEach(function(k) {
                var opt = document.createElement('option');
                opt.value = k.id;
                opt.dataset.bas = k.sayfa_bas;
                opt.dataset.son = k.sayfa_son;
                opt.textContent = k.baslik;
                kitapSel.appendChild(opt);
            });
            ntKitapDegis();
        }).catch(function() {
            kitapSel.innerHTML = "<option value='1'>Kur'an-ı Kerim</option>";
            document.getElementById('nt-bas').value = 0;
            document.getElementById('nt-son').value = 604;
        });

    document.getElementById('topic-modal').style.display = 'flex';
}

function topicModalKapat() {
    document.getElementById('topic-modal').style.display = 'none';
}

function ntKitapDegis() {
    var sel = document.getElementById('nt-kitap');
    var opt = sel.options[sel.selectedIndex];
    document.getElementById('nt-bas').value = opt ? (opt.dataset.bas || 0)   : 0;
    document.getElementById('nt-son').value = opt ? (opt.dataset.son || 604) : 604;
}

function topicKaydet() {
    var baslik    = document.getElementById('nt-baslik').value.trim() || 'Kendi hatmim';
    var kitap_id  = parseInt(document.getElementById('nt-kitap').value) || 1;
    var sayfa_bas = parseInt(document.getElementById('nt-bas').value);
    var sayfa_son = parseInt(document.getElementById('nt-son').value);
    var son_tarih = document.getElementById('nt-tarih').value || null;

    if (sayfa_son < sayfa_bas) { alert('Bitiş sayfası başlangıçtan küçük olamaz.'); return; }

    okumaApi('topic_olustur', { baslik, kitap_id, sayfa_bas, sayfa_son, son_tarih }, function(res) {
        if (!res.basari) { alert(res.hata || 'Hata oluştu.'); return; }
        topicModalKapat();

        // topicSelect'e ekle
        var sel = document.getElementById('topicSelect');
        var opt = document.createElement('option');
        opt.value       = res.topic_id;
        opt.textContent = '📖 ' + baslik;
        opt.selected    = true;
        sel.appendChild(opt);
        if (window.$ && $.fn.select2) $(sel).trigger('change.select2');

        // Hemen uygula
        topicSecildi(res.topic_id);
    });
}
</script>