# Workspace Context & Guidelines: FinanceFlow Repository

Repositori ini menaungi **2 proyek website yang berbeda** dalam satu workspace:

---

## 1. Proyek Website Rental Mobil (`rental-website/`)

* **Nama Brand:** 3 Putri Mulya (Rental Mobil Bintan & Tanjungpinang)
* **Stack:** Vue 3, Vite, Tailwind CSS, TypeScript, Vue Router
* **Tujuan:** Website promosi pariwisata, paket tur keliling Bintan, pemesanan armada rental (Avanza, Innova, HiAce), dan kalender event pariwisata daerah.

### Aturan Desain & UI Wajib (`rental-website`):
1. **DILARANG Menggunakan Emoji Berlebihan:**
   * Jangan gunakan emoji (seperti 🎏, 🎊, 🕌, 🚗, dll.) pada badge teks, highlight pill, atau judul card.
   * Gunakan teks bersih dan profesional agar tidak terkesan murahan atau "AI-generated".
2. **Tampilan Mobile (Event Section):**
   * Tampilan card event di layar HP **WAJIB berbentuk horizontal swipe carousel** (geser samping), **BUKAN** daftar kartu bertumpuk ke bawah (*vertical stack*).
   * Setiap card harus memiliki teks atau indikator yang jelas untuk membuka detail (`Lihat detail lengkap →`).
3. **Popup Detail Event (Modal Sheet):**
   * Di mobile, modal tampil sebagai *bottom sheet* dengan *pull handle bar* di bagian atas.
   * Tinggi gambar (*hero image*) harus terukur dan proporsional (`h-48 sm:h-56`) agar tidak menghabiskan separuh layar HP.
   * Scrollbar harus halus dan minimalis (*custom thin scrollbar*), jangan tampilkan scrollbar kotak bawaan browser yang tebal dengan panah atas-bawah.
   * Informasi Tanggal, Lokasi, dan Venue disatukan dalam 1 panel info terpadu dengan pembatas (*divider*).
   * Box Rekomendasi Armada harus ditonjolkan karena ini tujuan konversi rental mobil.
   * Tombol aksi di bawah (*sticky footer*) harus bersih, dengan tombol WhatsApp utama dan tombol Situs Resmi (jika tersedia).
4. **Logika Event Bintan (`src/config/bintanEvents.ts`):**
   * Komposisi tampilan 4 event terdekat di homepage selalu seimbang: **2 Event Budaya Tradisi Daerah + 2 Event Olahraga/Sport Tourism**.
   * Event yang sudah lewat tanggal selesainya otomatis digantikan oleh event berikutnya yang masih aktif.
   * Status (`Live Now`, `H-X Hari`) dan teks WhatsApp booking dihitung otomatis secara *real-time*.

---

## 2. Proyek Aplikasi Keuangan (`frontend/` + `backend/` + `api/`)

* **Nama Sistem:** FinanceFlow
* **Stack:** Vue 3, Pinia, ApexCharts, Tailwind CSS (Frontend) + Laravel/PHP (Backend/API)
* **Tujuan:** Aplikasi dashboard pencatatan keuangan, pembukuan, transaksi, laporan laba-rugi, dan manajemen kas.

---

## Panduan Perintah Terminal (Windows PowerShell):
* Eksekusi script atau perintah shell di workspace ini harus memperhatikan PowerShell execution policy.
* Jika menjalankan script lokal atau server, gunakan format `cmd /c "..."`.
