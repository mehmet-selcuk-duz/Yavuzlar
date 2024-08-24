document.getElementById('soruEklemeFormu').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const soruIndex = document.getElementById('soruIndex').value;
    const soru = document.getElementById('soru').value;
    const zorluk = document.getElementById('zorluk').value;
    const siklar = [
        document.getElementById('sik1').value,
        document.getElementById('sik2').value,
        document.getElementById('sik3').value,
        document.getElementById('sik4').value,
    ];
    const dogruSik = document.getElementById('dogruSik').value;

    const sorular = JSON.parse(localStorage.getItem('sorular')) || [];

    if (soruIndex === "") {
        sorular.push({ soru, zorluk, siklar, dogruSik });
    } else {
        sorular[soruIndex] = { soru, zorluk, siklar, dogruSik };
    }
    
    localStorage.setItem('sorular', JSON.stringify(sorular));

    this.reset();
    sorulariListele();
});

document.getElementById('soruArama').addEventListener('input', function() {
    sorulariListele(this.value.toLowerCase());
});

function sorulariListele(aramaKelimesi = '') {
    const sorular = JSON.parse(localStorage.getItem('sorular')) || [];
    const soruListesiDiv = document.getElementById('soruListesi');
    soruListesiDiv.innerHTML = '';

    sorular.forEach((soru, index) => {
        if (soru.soru.toLowerCase().includes(aramaKelimesi)) {
            const soruItem = document.createElement('div');
            soruItem.className = 'soru-item';
            soruItem.innerHTML = `
                <p><strong>Soru:</strong> ${soru.soru}</p>
                <p><strong>Zorluk:</strong> ${soru.zorluk}</p>
                <p><strong>Şıklar:</strong> ${soru.siklar.join(', ')}</p>
                <p><strong>Doğru Şık:</strong> ${soru.dogruSik}</p>
                <button onclick="soruDuzenle(${index})">Düzenle</button>
                <button onclick="soruSil(${index})">Sil</button>
            `;
            soruListesiDiv.appendChild(soruItem);
        }
    });
}

function soruDuzenle(index) {
    const sorular = JSON.parse(localStorage.getItem('sorular')) || [];
    const soru = sorular[index];

    document.getElementById('soruIndex').value = index;
    document.getElementById('soru').value = soru.soru;
    document.getElementById('zorluk').value = soru.zorluk;
    document.getElementById('sik1').value = soru.siklar[0];
    document.getElementById('sik2').value = soru.siklar[1];
    document.getElementById('sik3').value = soru.siklar[2];
    document.getElementById('sik4').value = soru.siklar[3];
    document.getElementById('dogruSik').value = soru.dogruSik;
}

function soruSil(index) {
    const sorular = JSON.parse(localStorage.getItem('sorular')) || [];
    sorular.splice(index, 1);
    localStorage.setItem('sorular', JSON.stringify(sorular));
    sorulariListele();
}

sorulariListele();
