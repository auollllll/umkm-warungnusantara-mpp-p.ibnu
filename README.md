warung-nusantara/
│
├── index.php                 # Halaman utama (pakai PHP karena DB)
├── script.js                 # Script utama frontend
├── styles.css                # Style utama (global)
│
├── assets/                   # Folder aset statis
│   ├── css/
│   │   └── style.css         # CSS tambahan (opsional)
│   ├── js/
│   └── img/
│
├── admin/                    # Folder khusus admin panel
│   ├── index.php             # Login admin
│   ├── dashboard.php         # Dashboard admin
│   ├── add-product.php       # Tambah produk
│   ├── edit-product.php      # Edit produk
│   ├── delete-product.php    # Hapus produk
│   └── logout.php            # Logout admin
│
├── api/                      # Backend API untuk AJAX
│   ├── config.php            # Koneksi database
│   ├── products.php          # Endpoint produk (GET/POST/DELETE)
│   ├── orders.php            # Endpoint order
│   └── upload.php            # Handler upload file gambar
│
├── uploads/                  # Tempat menyimpan gambar upload
│   └── (gambar.jpg)
│
├── database.sql              # Backup struktur database & data awal
│
├── README.md                 # Dokumentasi proyek
└── .htaccess                 # URL rewrite untuk API dan keamanan
