## IssueLog

IssueLog, kayıt ve takip işlemlerini kolaylaştırmak için geliştirilmiş bir web uygulamasıdır.

### 🚀 Özellikler

- 📌 Kayıt Yönetimi → Kayıt ekleme, düzenleme ve silme

- 🔄 Durum Yönetimi → Kayıtların durum bazlı takibi

- 🔍 Arama & Filtreleme → Gelişmiş kayıt arama ve listeleme

- 👤 Yönetici İşlemleri → Kullanıcı yönetimi, yetkilendirme, tam erişim

- 📧 E-posta İşlemleri → Doğrulama ve bilgilendirme maili gönderimi

- 🔐 Yetki Seviyeleri → Roller bazında işlem kısıtlamaları

### 🛠 Kullanılan Teknolojiler

- Laravel → Backend framework

- Laravel Breeze → Authentication & starter kit

- Livewire → Reactive UI geliştirme

- MySQL → Veritabanı yönetim sistemi

- Node.js & NPM → Frontend build ve bağımlılık yönetimi

### ⚙️ Kurulum

1. **Depoyu klonlayın:**
 ```bash
git clone https://github.com/sinem-aslan/IssueLog.git
```

2. **Proje klasörüne girin:**
 ```bash
cd IssueLog
```

3. **Bağımlılıkları yükleyin:**
 ```bash
composer install
npm install
```

4. **Ortam değişkenlerini ayarlayın:**
Ana dizinde .env dosyası oluşturun ve veritabanı bilgilerinizi girin:
 ```bash
# Veritabanı
DB_HOST=localhost
DB_USER=kullanici_adiniz
DB_PASSWORD=sifreniz
DB_NAME=veritabani_adi

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=mail_kullanici_adiniz
MAIL_PASSWORD=mail_sifreniz
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=destek@issuelog.com
MAIL_FROM_NAME="IssueLog"
```

5. **Veritabanını migrate edin:**
 ```bash
php artisan migrate
```

6. **Uygulamayı çalıştırın:**
 ```bash
php artisan serve
npm run dev
```

> Varsayılan olarak: http://localhost:8000 üzerinden erişebilirsiniz.
