package main

import (
	"fmt"
	"log"
	"os"

	"github.com/PuerkitoBio/goquery"
)

func verileriCek(url, dosyaAdi, veriTipi, secici, baslikSecici, linkSecici, aciklamaSecici, tarihSecici string) {
	doc, err := goquery.NewDocument(url)
	if err != nil {
		log.Fatalf("%s verisi alınamadı: %v", veriTipi, err)
	}

	dosya, err := os.Create(dosyaAdi)
	if err != nil {
		log.Fatalf("%s dosyası oluşturulamadı: %v", veriTipi, err)
	}
	defer dosya.Close()

	_, err = dosya.WriteString(veriTipi + " Haber Başlıkları\n\n")
	if err != nil {
		log.Printf("%s dosyasına yazılamadı: %v", veriTipi, err)
	}

	doc.Find(secici).Each(func(index int, element *goquery.Selection) {
		baslik := element.Find(baslikSecici).Text()
		link, varMi := element.Find(linkSecici).Attr("href")
		if !varMi {
			link = "Bağlantı bulunamadı"
		}
		aciklama := element.Find(aciklamaSecici).Text()
		tarih := element.Find(tarihSecici).Text()

		_, err := dosya.WriteString(fmt.Sprintf("Başlık: %s\nTarih: %s\nURL: %s\nAçıklama: %s\n\n", baslik, tarih, link, aciklama))
		if err != nil {
			log.Printf("%s dosyasına yazılamadı: %v", veriTipi, err)
		}
	})

	fmt.Printf("%s verileri %s dosyasına kaydedildi.\n", veriTipi, dosyaAdi)
}

func main() {
	for {
		fmt.Println("\n--- Menü ---")
		fmt.Println("1: The Hacker News'ten veri çek")
		fmt.Println("2: Cyber Security News'ten veri çek")
		fmt.Println("3: Cyber Security Dive'den veri çek")
		fmt.Println("4: Çıkış yap")
		fmt.Print("Seçiminizi girin: ")

		var secim int
		fmt.Scan(&secim)

		switch secim {
		case 1:
			verileriCek("https://thehackernews.com/", "hackernews.txt", "The Hacker News",
				".body-post.clear", ".home-title", ".story-link", ".home-desc", ".h-datetime")
		case 2:
			verileriCek("https://cybersecuritynews.com/", "cybersecuritynews.txt", "Cyber Security News",
				".td-block-span12 .td_module_10", ".entry-title a", ".entry-title a", ".td-excerpt", ".td-module-meta-info .entry-date")
		case 3:
			verileriCek("https://www.cybersecuritydive.com/", "cybersecuritydive.txt", "Cyber Security Dive",
				"li.row.feed__item", ".feed__title a", ".feed__title a", ".feed__description", ".feed__date")
		case 4:
			fmt.Println("Çıkış yapılıyor...")
			return
		default:
			fmt.Println("Geçersiz seçim, tekrar deneyin.")
		}
	}
}
