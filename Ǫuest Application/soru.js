const sorular = JSON.parse(localStorage.getItem('sorular')) || [];
let suankiSoruIndex = 0;
let dogruSayisi = 0;
let yanlisSayisi = 0;
let toplamPuan = 0;

const zorlukPuanlari = {
    kolay: 3,
    orta: 6,
    zor: 9
};

function cevabiKontrolEt(dogruMu, zorluk) {
    if (dogruMu) {
        dogruSayisi++;
        toplamPuan += zorlukPuanlari[zorluk];
    } else {
        yanlisSayisi++;
    }
    suankiSoruIndex++;
    if (suankiSoruIndex < sorular.length) {
        sonrakiSoruGoster();
    } else {
        quizBitir();
    }
}

function sonrakiSoruGoster() {
    const soru = sorular[suankiSoruIndex];
    document.getElementById('soruMetni').textContent = soru.soru;
    const siklarDiv = document.getElementById('siklar');
    siklarDiv.innerHTML = '';
    soru.siklar.forEach((sik, index) => {
        const dogruMu = (index + 1) == soru.dogruSik;
        const btn = document.createElement('button');
        btn.className = 'cevap-btn';
        btn.textContent = sik;
        btn.onclick = () => cevabiKontrolEt(dogruMu, soru.zorluk);
        siklarDiv.appendChild(btn);
    });
}

function quizBitir() {
    document.getElementById('quiz-container').style.display = 'none';
    document.getElementById('sonuc').style.display = 'block';
    document.getElementById('dogruSayisi').textContent = dogruSayisi;
    document.getElementById('yanlisSayisi').textContent = yanlisSayisi;
    document.getElementById('toplamPuan').textContent = toplamPuan;
}

sonrakiSoruGoster();
