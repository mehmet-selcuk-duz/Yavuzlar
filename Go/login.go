package main

import (
	"encoding/json"
	"fmt"
	"os"
	"strings"
	"time"
)

type Kullanici struct {
	KullaniciAdi  string `json:"kullanici_adi"`
	Sifre         string `json:"sifre"`
	KullaniciTipi int    `json:"kullanici_tipi"`
}

var kullanicilar []Kullanici
var logDosyasi *os.File
var aktifKullanici *Kullanici

func init() {
	var err error
	logDosyasi, err = os.OpenFile("log.txt", os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0644)
	if err != nil {
		fmt.Println("Log dosyası açılamadı:", err)
		os.Exit(1)
	}

	kullanicilar = yükleKullanicilar()
}

func logGirdisi(mesaj string) {
	logDosyasi.WriteString(time.Now().Format(time.RFC3339) + " - " + mesaj + "\n")
}

func yükleKullanicilar() []Kullanici {
	var kullanicilar []Kullanici

	dosya, err := os.Open("users.json")
	if err != nil {
		fmt.Println("Kullanıcı dosyası bulunamadı. Yeni dosya oluşturuluyor.")
		return kullanicilar
	}
	defer dosya.Close()

	jsonDecoder := json.NewDecoder(dosya)
	err = jsonDecoder.Decode(&kullanicilar)
	if err != nil {
		fmt.Println("Kullanıcı dosyası okunamadı:", err)
		return kullanicilar
	}

	return kullanicilar
}

func kaydetKullanicilar() {
	dosya, err := os.Create("users.json")
	if err != nil {
		fmt.Println("Kullanıcı dosyası oluşturulamadı:", err)
		return
	}
	defer dosya.Close()

	jsonEncoder := json.NewEncoder(dosya)
	jsonEncoder.SetIndent("", "  ")
	err = jsonEncoder.Encode(kullanicilar)
	if err != nil {
		fmt.Println("Kullanıcı dosyası kaydedilemedi:", err)
	}
}

func giris() {
	var kullaniciAdi, sifre string

	fmt.Print("Kullanıcı Adı: ")
	fmt.Scan(&kullaniciAdi)
	fmt.Print("Şifre: ")
	fmt.Scan(&sifre)

	kullaniciAdi = strings.TrimSpace(kullaniciAdi)
	sifre = strings.TrimSpace(sifre)

	for i := range kullanicilar {
		if kullanicilar[i].KullaniciAdi == kullaniciAdi && kullanicilar[i].Sifre == sifre {
			logGirdisi("Başarılı giriş: " + kullaniciAdi)
			aktifKullanici = &kullanicilar[i]
			if aktifKullanici.KullaniciTipi == 0 {
				adminMenusu()
			} else {
				musteriMenusu()
			}
			return
		}
	}
	logGirdisi("Hatalı giriş: " + kullaniciAdi)
	fmt.Println("Hatalı giriş!")
}

func adminMenusu() {
	for {
		fmt.Println("\nAdmin Menüsü")
		fmt.Println("1. Müşteri Ekle")
		fmt.Println("2. Müşteri Sil")
		fmt.Println("3. Log Listeleme")
		fmt.Println("0. Çıkış")

		var secim int
		fmt.Print("Seçim: ")
		fmt.Scan(&secim)

		switch secim {
		case 1:
			musteriEkle()
		case 2:
			musteriSil()
		case 3:
			logListele()
		case 0:
			fmt.Println("Çıkış yapılıyor...")
			logGirdisi("Admin çıkışı yapıldı.")
			return
		default:
			fmt.Println("Geçersiz seçim! Lütfen geçerli bir sayı girin.")
		}
	}
}

func musteriMenusu() {
	for {
		fmt.Println("\nMüşteri Menüsü")
		fmt.Println("1. Profil Görüntüle")
		fmt.Println("2. Şifre Değiştir")
		fmt.Println("0. Çıkış")

		var secim int
		fmt.Print("Seçim: ")
		fmt.Scan(&secim)

		switch secim {
		case 1:
			profilGoruntule()
		case 2:
			sifreDegistir()
		case 0:
			fmt.Println("Çıkış yapılıyor...")
			logGirdisi("Müşteri çıkışı yapıldı.")
			return
		default:
			fmt.Println("Geçersiz seçim! Lütfen geçerli bir sayı girin.")
		}
	}
}

func musteriEkle() {
	var kullaniciAdi, sifre string

	fmt.Print("Yeni Müşteri Kullanıcı Adı: ")
	fmt.Scan(&kullaniciAdi)
	fmt.Print("Yeni Müşteri Şifresi: ")
	fmt.Scan(&sifre)

	yeniKullanici := Kullanici{
		KullaniciAdi:  strings.TrimSpace(kullaniciAdi),
		Sifre:         strings.TrimSpace(sifre),
		KullaniciTipi: 1,
	}

	for _, kullanici := range kullanicilar {
		if kullanici.KullaniciAdi == yeniKullanici.KullaniciAdi {
			fmt.Println("Bu kullanıcı adı zaten mevcut!")
			return
		}
	}

	kullanicilar = append(kullanicilar, yeniKullanici)
	logGirdisi("Yeni müşteri eklendi: " + yeniKullanici.KullaniciAdi)
	fmt.Println("Müşteri eklendi:", yeniKullanici.KullaniciAdi)
	kaydetKullanicilar()
}

func musteriSil() {
	var kullaniciAdi string

	fmt.Print("Silinecek Müşteri Kullanıcı Adı: ")
	fmt.Scan(&kullaniciAdi)

	for i, kullanici := range kullanicilar {
		if kullanici.KullaniciAdi == strings.TrimSpace(kullaniciAdi) && kullanici.KullaniciTipi == 1 {
			kullanicilar = append(kullanicilar[:i], kullanicilar[i+1:]...)
			logGirdisi("Müşteri silindi: " + kullanici.KullaniciAdi)
			fmt.Println("Müşteri silindi:", kullanici.KullaniciAdi)
			kaydetKullanicilar()
			return
		}
	}
	fmt.Println("Müşteri bulunamadı!")
}

func logListele() {
	data, err := os.ReadFile("log.txt")
	if err != nil {
		fmt.Println("Log dosyası okunamadı:", err)
		return
	}
	fmt.Println("Loglar:\n", string(data))
}

func profilGoruntule() {
	if aktifKullanici != nil {
		fmt.Printf("Kullanıcı Adı: %s\n", aktifKullanici.KullaniciAdi)
	} else {
		fmt.Println("Aktif kullanıcı bulunamadı!")
	}
}

func sifreDegistir() {
	var yeniSifre string

	fmt.Print("Yeni Şifre: ")
	fmt.Scan(&yeniSifre)

	if aktifKullanici != nil {
		yeniSifre = strings.TrimSpace(yeniSifre)
		if yeniSifre == "" {
			fmt.Println("Yeni şifre boş olamaz!")
			return
		}

		for i := range kullanicilar {
			if kullanicilar[i].KullaniciAdi == aktifKullanici.KullaniciAdi {
				kullanicilar[i].Sifre = yeniSifre
				break
			}
		}

		logGirdisi("Şifre değiştirildi: " + aktifKullanici.KullaniciAdi)
		fmt.Println("Şifre başarıyla değiştirildi:", aktifKullanici.KullaniciAdi)

		kaydetKullanicilar()
	} else {
		fmt.Println("Aktif kullanıcı bulunamadı!")
	}
}

func main() {
	for {
		giris()
	}
}
