-- =====================================================
-- data buat demo tabel menus dan daily_menus
-- aman dijalankan berulang kali (tidak menggandakan data)
-- =====================================================

-- katalog menu (21 menu)
INSERT INTO menus (menu_id, menu_name, description, image) VALUES
(1,  'Nasi Uduk Spesial',   'Nasi uduk gurih santan dengan telur, bawang goreng, kerupuk, dan sambal kacang.', 'nasi-lemak.webp'),
(2,  'Ayam Bakar Madu',     'Ayam bakar berbumbu madu yang manis gurih, disajikan dengan nasi putih dan lalapan.', 'nasi-lemak.webp'),
(3,  'Tumis Buncis Sapi',   'Buncis segar ditumis dengan irisan daging sapi, bumbu bawang, dan sedikit saus tiram.', 'nasi-lemak.webp'),
(4,  'Lontong Sayur',       'Lontong dengan kuah sayur santan, telur, tahu, dan taburan bawang goreng.', 'nasi-lemak.webp'),
(5,  'Ikan Goreng Kuning',  'Ikan goreng berbumbu kunyit yang renyah, dengan nasi hangat dan sambal terasi.', 'nasi-lemak.webp'),
(6,  'Sup Ayam Bening',     'Sup ayam kuah bening dengan wortel, kentang, dan seledri, hangat dan ringan.', 'nasi-lemak.webp'),
(7,  'Nasi Kuning Komplit', 'Nasi kuning harum dengan ayam suwir, telur dadar, tempe orek, dan kerupuk.', 'nasi-lemak.webp'),
(8,  'Rendang Sapi',        'Daging sapi dimasak perlahan dalam bumbu rendang kaya rempah, dengan nasi putih.', 'nasi-lemak.webp'),
(9,  'Capcay Seafood',      'Tumis aneka sayuran dengan udang dan cumi dalam kuah kental yang gurih.', 'nasi-lemak.webp'),
(10, 'Bubur Ayam',          'Bubur nasi lembut dengan ayam suwir, cakwe, kacang kedelai, dan kerupuk.', 'nasi-lemak.webp'),
(11, 'Ayam Geprek',         'Ayam goreng tepung renyah yang digeprek dengan sambal bawang pedas, plus nasi putih.', 'nasi-lemak.webp'),
(12, 'Soto Ayam',           'Soto kuah kuning dengan ayam suwir, soun, telur rebus, dan perasan jeruk nipis.', 'nasi-lemak.webp'),
(13, 'Nasi Goreng Kampung', 'Nasi goreng dengan teri, sayuran, dan telur mata sapi, berbumbu khas rumahan.', 'nasi-lemak.webp'),
(14, 'Ikan Bakar Jimbaran',  'Ikan bakar dengan bumbu Jimbaran segar, disajikan dengan nasi dan plecing kangkung.', 'nasi-lemak.webp'),
(15, 'Fu Yung Hai',         'Telur dadar tebal isi sayuran dan udang dengan saus asam manis.', 'nasi-lemak.webp'),
(16, 'Roti Bakar Telur',    'Roti bakar renyah dengan isian telur dan keju, cocok untuk sarapan akhir pekan.', 'nasi-lemak.webp'),
(17, 'Mie Goreng Spesial',  'Mie goreng dengan telur, sayuran, dan irisan ayam, dibumbui kecap manis.', 'nasi-lemak.webp'),
(18, 'Sate Ayam',           'Sate ayam bakar dengan bumbu kacang, lontong, dan acar timun.', 'nasi-lemak.webp'),
(19, 'Pancake Nutella',     'Pancake lembut dengan olesan Nutella dan potongan pisang.', 'nasi-lemak.webp'),
(20, 'Nasi Liwet',          'Nasi liwet gurih dengan ayam, teri, telur pindang, dan lalapan.', 'nasi-lemak.webp'),
(21, 'Bistik Sapi',         'Daging sapi empuk dengan saus kecap bawang, kentang goreng, dan sayuran rebus.', 'nasi-lemak.webp')
ON DUPLICATE KEY UPDATE
    menu_name   = VALUES(menu_name),
    description = VALUES(description),
    image       = VALUES(image);

-- jadwal harian: 19 oktober - 1 november 2026 (14 hari x 3 slot = 42 baris)
-- minggu 1
INSERT INTO daily_menus (menu_id, `date`, slot) VALUES
(1,  '2026-10-19', 'Pagi'), (2,  '2026-10-19', 'Siang'), (3,  '2026-10-19', 'Malam'),
(4,  '2026-10-20', 'Pagi'), (5,  '2026-10-20', 'Siang'), (6,  '2026-10-20', 'Malam'),
(7,  '2026-10-21', 'Pagi'), (8,  '2026-10-21', 'Siang'), (9,  '2026-10-21', 'Malam'),
(10, '2026-10-22', 'Pagi'), (11, '2026-10-22', 'Siang'), (12, '2026-10-22', 'Malam'),
(13, '2026-10-23', 'Pagi'), (14, '2026-10-23', 'Siang'), (15, '2026-10-23', 'Malam'),
(16, '2026-10-24', 'Pagi'), (17, '2026-10-24', 'Siang'), (18, '2026-10-24', 'Malam'),
(19, '2026-10-25', 'Pagi'), (20, '2026-10-25', 'Siang'), (21, '2026-10-25', 'Malam'),
-- minggu 2 (menu diulang dari minggu 1)
(1,  '2026-10-26', 'Pagi'), (2,  '2026-10-26', 'Siang'), (3,  '2026-10-26', 'Malam'),
(4,  '2026-10-27', 'Pagi'), (5,  '2026-10-27', 'Siang'), (6,  '2026-10-27', 'Malam'),
(7,  '2026-10-28', 'Pagi'), (8,  '2026-10-28', 'Siang'), (9,  '2026-10-28', 'Malam'),
(10, '2026-10-29', 'Pagi'), (11, '2026-10-29', 'Siang'), (12, '2026-10-29', 'Malam'),
(13, '2026-10-30', 'Pagi'), (14, '2026-10-30', 'Siang'), (15, '2026-10-30', 'Malam'),
(16, '2026-10-31', 'Pagi'), (17, '2026-10-31', 'Siang'), (18, '2026-10-31', 'Malam'),
(19, '2026-11-01', 'Pagi'), (20, '2026-11-01', 'Siang'), (21, '2026-11-01', 'Malam')
ON DUPLICATE KEY UPDATE
    menu_id = VALUES(menu_id);