<?php
session_start();
require 'config.php';

// Cek login
if (!isset($_SESSION['logged_in']) && !isset($_POST['username'])) {
    // Tidak ada sesi login, tampilkan halaman login
} elseif (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        echo "<script>alert('Username atau password salah!');</script>";
    }
} elseif (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        .sidebar-hidden {
            transform: translateX(-100%);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php if (!isset($_SESSION['logged_in'])): ?>
    <!-- Login Page -->
    <div id="login-page" class="flex items-center justify-center min-h-screen bg-gray-800">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6">Login Sistem Inventory</h2>
            <form id="login-form">
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" class="mt-1 p-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" class="mt-1 p-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Login</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <!-- Main App -->
    <div id="main-app">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 w-64 bg-blue-900 text-white sidebar">
            <div class="p-4">
                <h2 class="text-xl font-bold">Sistem Inventory</h2>
            </div>
            <nav class="mt-4">
                <ul>
                    <li><a href="#" data-page="dashboard" class="block p-4 hover:bg-blue-800">Dashboard</a></li>
                    <li><a href="#" data-page="barang-masuk" class="block p-4 hover:bg-blue-800">Barang Masuk</a></li>
                    <li><a href="#" data-page="barang-keluar" class="block p-4 hover:bg-blue-800">Barang Keluar</a></li>
                    <li><a href="#" data-page="sisa-stok" class="block p-4 hover:bg-blue-800">Sisa Stok</a></li>
                    <li><a href="#" id="logout" class="block p-4 hover:bg-blue-800">Logout</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64 p-6">
            <!-- Dashboard -->
            <div id="dashboard" class="page">
                <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Barang Masuk</h3>
                        <p id="total-masuk" class="text-2xl font-bold">0</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Barang Keluar</h3>
                        <p id="total-keluar" class="text-2xl font-bold">0</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Sisa Stok</h3>
                        <p id="sisa-stok" class="text-2xl font-bold">0</p>
                    </div>
                </div>
                <!-- Rincian Sisa Stok -->
                <h2 class="text-2xl font-bold mb-4">Rincian Sisa Stok</h2>
                <table class="bg-white rounded-lg shadow">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">No Urut</th>
                            <th class="p-3 text-left">Nama Barang</th>
                            <th class="p-3 text-left">Kode Barang</th>
                            <th class="p-3 text-left">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-rincian-stok"></tbody>
                </table>
            </div>

            <!-- Barang Masuk -->
            <div id="barang-masuk" class="page hidden">
                <h1 class="text-3xl font-bold mb-6">Barang Masuk</h1>
                <form id="form-barang-masuk" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="nama-barang-masuk" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                            <input type="text" id="nama-barang-masuk" name="nama_barang" class="mt-1 p-2 w-full border rounded-md" required>
                        </div>
                        <div>
                            <label for="kode-barang-masuk" class="block text-sm font-medium text-gray-700">Kode Barang</label>
                            <input type="text" id="kode-barang-masuk" name="kode_barang" class="mt-1 p-2 w-full border rounded-md" required>
                        </div>
                        <div>
                            <label for="jumlah-masuk" class="block text-sm font-medium text-gray-700">Jumlah</label>
                            <input type="number" id="jumlah-masuk" name="jumlah" class="mt-1 p-2 w-full border rounded-md" required min="1">
                        </div>
                    </div>
                    <button type="submit" class="mt-4 bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Tambah</button>
                </form>
                <table class="bg-white rounded-lg shadow">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">No Urut</th>
                            <th class="p-3 text-left">Nama Barang</th>
                            <th class="p-3 text-left">Kode Barang</th>
                            <th class="p-3 text-left">Tanggal Masuk</th>
                            <th class="p-3 text-left">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-barang-masuk"></tbody>
                </table>
            </div>

            <!-- Barang Keluar -->
            <div id="barang-keluar" class="page hidden">
                <h1 class="text-3xl font-bold mb-6">Barang Keluar</h1>
                <form id="form-barang-keluar" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="nama-barang-keluar" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                            <input type="text" id="nama-barang-keluar" name="nama_barang" class="mt-1 p-2 w-full border rounded-md" required>
                        </div>
                        <div>
                            <label for="kode-barang-keluar" class="block text-sm font-medium text-gray-700">Kode Barang</label>
                            <input type="text" id="kode-barang-keluar" name="kode_barang" class="mt-1 p-2 w-full border rounded-md" required>
                        </div>
                        <div>
                            <label for="jumlah-keluar" class="block text-sm font-medium text-gray-700">Jumlah</label>
                            <input type="number" id="jumlah-keluar" name="jumlah" class="mt-1 p-2 w-full border rounded-md" required min="1">
                        </div>
                    </div>
                    <button type="submit" class="mt-4 bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Tambah</button>
                </form>
                <table class="bg-white rounded-lg shadow">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">No Urut</th>
                            <th class="p-3 text-left">Nama Barang</th>
                            <th class="p-3 text-left">Kode Barang</th>
                            <th class="p-3 text-left">Tanggal Keluar</th>
                            <th class="p-3 text-left">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-barang-keluar"></tbody>
                </table>
            </div>

            <!-- Sisa Stok -->
            <div id="sisa-stok" class="page hidden">
                <h1 class="text-3xl font-bold mb-6">Sisa Stok</h1>
                <table class="bg-white rounded-lg shadow">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 text-left">No Urut</th>
                            <th class="p-3 text-left">Nama Barang</th>
                            <th class="p-3 text-left">Kode Barang</th>
                            <th class="p-3 text-left">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-sisa-stok"></tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Fungsi untuk mengambil data dari server
        function fetchData(url, callback) {
            fetch(url)
                .then(response => response.json())
                .then(data => callback(data))
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengambil data dari server!');
                });
        }

        // Login
        document.getElementById('login-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            fetch('index.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            });
        });

        // Logout
        document.getElementById('logout')?.addEventListener('click', () => {
            window.location.href = 'index.php?logout=true';
        });

        // Navigasi menu
        document.querySelectorAll('nav a[data-page]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = e.target.getAttribute('data-page');
                document.querySelectorAll('.page').forEach(p => p.classList.add('hidden'));
                document.getElementById(page).classList.remove('hidden');
            });
        });

        // Barang Masuk
        document.getElementById('form-barang-masuk')?.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            fetch('barang_masuk.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    updateTabelBarangMasuk();
                    updateSisaStok();
                    updateDashboard();
                    document.getElementById('form-barang-masuk').reset();
                } else {
                    alert(data.message);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Gagal menambah barang masuk!');
            });
        });

        // Barang Keluar
        document.getElementById('form-barang-keluar')?.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            fetch('barang_keluar.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    updateTabelBarangKeluar();
                    updateSisaStok();
                    updateDashboard();
                    document.getElementById('form-barang-keluar').reset();
                } else {
                    alert(data.message);
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Gagal menambah barang keluar!');
            });
        });

        // Update tabel barang masuk
        function updateTabelBarangMasuk() {
            fetchData('barang_masuk.php', (data) => {
                const tabel = document.getElementById('tabel-barang-masuk');
                tabel.innerHTML = '';
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="p-3">${item.no_urut}</td>
                        <td class="p-3">${item.nama_barang}</td>
                        <td class="p-3">${item.kode_barang}</td>
                        <td class="p-3">${item.tanggal_masuk}</td>
                        <td class="p-3">${item.jumlah}</td>
                    `;
                    tabel.appendChild(row);
                });
            });
        }

        // Update tabel barang keluar
        function updateTabelBarangKeluar() {
            fetchData('barang_keluar.php', (data) => {
                const tabel = document.getElementById('tabel-barang-keluar');
                tabel.innerHTML = '';
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="p-3">${item.no_urut}</td>
                        <td class="p-3">${item.nama_barang}</td>
                        <td class="p-3">${item.kode_barang}</td>
                        <td class="p-3">${item.tanggal_keluar}</td>
                        <td class="p-3">${item.jumlah}</td>
                    `;
                    tabel.appendChild(row);
                });
            });
        }

        // Update tabel sisa stok
        function updateSisaStok() {
            fetchData('sisa_stok.php', (data) => {
                const tabel = document.getElementById('tabel-sisa-stok');
                tabel.innerHTML = '';
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="p-3">${item.no_urut}</td>
                        <td class="p-3">${item.nama_barang}</td>
                        <td class="p-3">${item.kode_barang}</td>
                        <td class="p-3">${item.jumlah}</td>
                    `;
                    tabel.appendChild(row);
                });
                // Update tabel rincian stok di dashboard
                const tabelRincian = document.getElementById('tabel-rincian-stok');
                tabelRincian.innerHTML = '';
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="p-3">${item.no_urut}</td>
                        <td class="p-3">${item.nama_barang}</td>
                        <td class="p-3">${item.kode_barang}</td>
                        <td class="p-3">${item.jumlah}</td>
                    `;
                    tabelRincian.appendChild(row);
                });
            });
        }

        // Update dashboard
        function updateDashboard() {
            // Hitung total masuk
            fetchData('barang_masuk.php', (masukData) => {
                const totalMasuk = masukData.reduce((sum, item) => sum + parseInt(item.jumlah), 0);
                document.getElementById('total-masuk').textContent = totalMasuk;
            });
            // Hitung total keluar
            fetchData('barang_keluar.php', (keluarData) => {
                const totalKeluar = keluarData.reduce((sum, item) => sum + parseInt(item.jumlah), 0);
                document.getElementById('total-keluar').textContent = totalKeluar;
            });
            // Hitung total sisa stok
            fetchData('sisa_stok.php', (stokData) => {
                const sisaStok = stokData.reduce((sum, item) => sum + parseInt(item.jumlah), 0);
                document.getElementById('sisa-stok').textContent = sisaStok;
            });
        }

        // Inisialisasi tampilan
        <?php if (isset($_SESSION['logged_in'])): ?>
        updateTabelBarangMasuk();
        updateTabelBarangKeluar();
        updateSisaStok();
        updateDashboard();
        <?php endif; ?>
    </script>
</body>
</html>