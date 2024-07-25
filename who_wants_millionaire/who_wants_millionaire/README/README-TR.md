# Who Wants to Be a Millionaire?

Bu proje, "Who Wants to Be a Millionaire?" adlı oyunun web tabanlı bir versiyonudur. Oyuncular çeşitli sorulara cevap vererek büyük ödülü kazanmaya çalışırlar. Bu README dosyası, projeyi kurmanız ve çalıştırmanız için gereken adımları ve projenin işlevlerini detaylı bir şekilde açıklamaktadır.

## İçindekiler

- [Kurulum](#kurulum)
- [Kullanım](#kullanım)
- [Dosya Yapısı](#dosya-yapısı)
- [Özellikler](#özellikler)
- [Teknik Detaylar](#teknik-detaylar)
- [Sorun Giderme](#sorun-giderme)
- [Projeyi Geliştiren](#projeyi-geliştiren)

## Kurulum

### Gereksinimler

- PHP 7.4 veya üstü
- MySQL
- Web sunucusu (Apache veya Nginx önerilir)
- Tarayıcı (Google Chrome, Firefox, Safari, vb.)

### Adımlar

1. **Depoyu Klonlayın:**

   ```sh
   git clone https://github.com/kullaniciadi/millionaire.git
   cd millionaire
   ```

2. **Veritabanını Kurun:**

   - MySQL veritabanınızı oluşturun ve `who_wants_to_be_a_millionaire_db` adını verin.
   - `database.sql` dosyasını kullanarak veritabanını doldurun.

   ```sh
   mysql -u root -p who_wants_to_be_a_millionaire_db < database.sql
   ```

3. **Veritabanı Bağlantısını Ayarlayın:**

   `config.php` dosyasını açın ve veritabanı bağlantı ayarlarını yapın.

   ```php
   <?php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "who_wants_to_be_a_millionaire_db";
   $port = 3307;

   try {
       $conn = new PDO("mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4", $username, $password);
       $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch(PDOException $e) {
       echo "Bağlantı hatası: " . $e->getMessage();
   }
   ?>
   ```

4. **Web Sunucusunu Başlatın:**

   Proje dizininde bir PHP yerleşik web sunucusu başlatın.

   ```sh
   php -S localhost:8000
   ```

5. **Tarayıcıda Açın:**

   Tarayıcınızı açın ve `http://localhost:8000` adresine gidin.

## Kullanım

1. **Başlangıç Sayfası:**

   - Oyuncular isimlerini girdikten sonra oyuna başlamak için `Devam` butonuna tıklayacaklar.
   - `Credits` butonuna tıklayarak yapımcı bilgilerine ulaşabilirler.

2. **Oyun Ekranı:**

   - Sorular ve seçenekler görüntülenecek.
   - Oyuncular doğru cevabı seçmeye çalışacaklar.
   - `Lifelines` (Jokerler) kullanılabilir: 50:50, Double Dip, Switch the Question.

3. **Oyun Sonu:**

   - Oyuncu kazandığı para miktarını görecek ve oyunu yeniden başlatabilecektir.

## Dosya Yapısı

- `index.php` : Oyunun başlangıç sayfası.
- `welcome.php` : Oyuncu bilgileri ve giriş diyalogları.
- `game.php` : Oyun ekranı ve soru-cevap mekanizması.
- `end_game.php` : Oyun bitiş ekranı.
- `config.php` : Veritabanı bağlantı ayarları.
- `questions.php` : Soruların listesi.
- `welcome.css` : Welcome sayfasının stilleri.
- `game.css` : Oyun sayfasının stilleri.
- `credits.php` : Yapımcı bilgileri sayfası.
- `credits.css` : Credits sayfasının stilleri.
- `intro.mp4` : Oyunun başında oynatılan giriş videosu.
- `question.m4a` : Sorular sırasında oynatılan ses.
- `time.mp3` : Zaman sayacı için oynatılan ses.
- `win.mp3` : Doğru cevap verildiğinde oynatılan ses.
- `lose.mp3` : Yanlış cevap verildiğinde oynatılan ses.
- `farewell.mp3` : Oyun bittiğinde oynatılan ses.
- `unmuted.png` : Ses açıkken gösterilen ikon.
- `muted.png` : Ses kapalıyken gösterilen ikon.

## Özellikler

- Otomatik video oynatma ve kullanıcı giriş diyalogları.
- Sorulara cevap verme ve kazançları izleme.
- Joker kullanma (50:50, Double Dip, Switch the Question).
- Oyunun sonunda kazanç gösterimi.
- Ses açma/kapatma butonu.

## Teknik Detaylar

- **Backend:** PHP, MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Veritabanı:** Sorular ve kullanıcı bilgileri MySQL veritabanında saklanır.

## Sorun Giderme

- **Veritabanı Bağlantı Hatası:**

  Veritabanı bağlantı ayarlarını `config.php` dosyasında kontrol edin.

- **Ses Oynatma Sorunu:**

  Tarayıcınızın otomatik ses oynatma izinlerini kontrol edin. Kullanıcı etkileşimi gerektirebilir.

- **Stil Sorunları:**

  Tarayıcı önbelleğini temizleyin ve sayfayı yeniden yükleyin.

- **Video Oynatma Sorunu:**

  Tarayıcınızın otomatik video oynatma izinlerini kontrol edin. Kullanıcı etkileşimi gerektirebilir.

## Projeyi Geliştiren

Bu projeyi geliştiren ve katkıda bulunan kişi:

**Muhammed Murat Yanaşoğlu**

- Yakın Doğu Üniversitesi Yazılım Mühendisliği (İngilizce) son sınıf(4.sınıf) öğrencisi.
- Web geliştirme üzerine çalışmalar yapmaktadır.
- [LinkedIn](https://www.linkedin.com/in/muratyanasoglu/)
- [GitHub](https://github.com/muratyanasoglu)
