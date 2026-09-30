<?php

/**
 * Latihan per materi topik PPI-04: 3 indikator × (utama, penguatan, remedial).
 * Format soal: [tingkat, teks soal, pilihan A–D, indeks kunci 0–3, pembahasan].
 */
return [
    'Pengertian dan Cara Etika Batuk' => [
        [
            'indicator' => 'Menjelaskan pengertian etika batuk sebagai bagian dari kebersihan pernapasan',
            'section' => 'Pengertian',
            'reinforcement' => 'Etika batuk dan bersin adalah tata cara batuk atau bersin yang benar untuk mencegah penyebaran mikroorganisme melalui droplet dan udara. Etika batuk merupakan bagian dari komponen kewaspadaan standar, yaitu kebersihan pernapasan (respiratory hygiene). Tujuannya melindungi orang lain dan lingkungan, bukan meredakan batuk.',
            'questions' => [
                'utama' => ['mudah', 'Istilah respiratory hygiene dalam kewaspadaan standar paling tepat diartikan sebagai ...', [
                    'Latihan pernapasan untuk memperkuat paru',
                    'Kebersihan pernapasan, termasuk etika batuk dan bersin',
                    'Pembersihan alat bantu napas setelah dipakai',
                    'Pemberian oksigen pada pasien sesak',
                ], 1, 'Respiratory hygiene adalah kebersihan pernapasan yang mencakup etika batuk dan bersin untuk mencegah penyebaran droplet. Latihan pernapasan adalah intervensi terapi, bukan upaya pencegahan penularan.'],
                'penguatan' => ['sedang', 'Seorang mahasiswa menyatakan bahwa etika batuk sama dengan batuk efektif. Tanggapan yang paling tepat adalah ...', [
                    'Benar, keduanya bertujuan mengeluarkan dahak',
                    'Benar, keduanya hanya dilakukan oleh pasien rawat inap',
                    'Kurang tepat, etika batuk bertujuan mencegah penyebaran mikroorganisme, sedangkan batuk efektif bertujuan mengeluarkan dahak',
                    'Kurang tepat, etika batuk hanya berlaku untuk petugas kesehatan',
                ], 2, 'Etika batuk berfokus pada pencegahan penularan melalui droplet dan udara, sedangkan batuk efektif adalah teknik untuk membersihkan jalan napas dari dahak. Anggapan bahwa etika batuk hanya untuk petugas keliru karena berlaku bagi siapa pun yang bergejala.'],
                'remedial' => ['mudah', 'Seorang pasien bertanya mengapa ia diminta menerapkan etika batuk. Jawaban perawat yang paling tepat adalah ...', [
                    'Agar kuman dari batuk atau bersin tidak menyebar ke orang lain',
                    'Agar batuknya cepat sembuh',
                    'Agar suaranya tidak mengganggu pasien lain',
                    'Agar tidak perlu minum obat batuk',
                ], 0, 'Etika batuk dilakukan untuk mencegah mikroorganisme dalam droplet menyebar ke orang dan lingkungan sekitar. Etika batuk tidak mempercepat penyembuhan batuk.'],
            ],
        ],
        [
            'indicator' => 'Mempraktikkan cara menutup mulut dan hidung yang benar saat batuk atau bersin',
            'section' => 'Cara yang Benar',
            'reinforcement' => 'Saat batuk atau bersin, tutup hidung dan mulut dengan tisu. Bila tidak ada tisu, gunakan lengan atas bagian dalam, bukan telapak tangan, karena telapak tangan akan menyentuh benda dan orang lain sehingga memindahkan mikroorganisme.',
            'questions' => [
                'utama' => ['mudah', 'Urutan pilihan penutup yang dianjurkan saat seseorang akan bersin adalah ...', [
                    'Telapak tangan, lalu lengan atas bagian dalam',
                    'Lengan atas bagian dalam, lalu telapak tangan',
                    'Ujung jari, lalu tisu',
                    'Tisu, lalu lengan atas bagian dalam bila tisu tidak tersedia',
                ], 3, 'Tisu adalah pilihan utama; bila tidak ada, gunakan lengan atas bagian dalam. Telapak tangan tidak dianjurkan karena mudah memindahkan kuman ke benda yang disentuh.'],
                'penguatan' => ['sedang', 'Seorang pasien di ruang tunggu tiba-tiba bersin sambil memegang tas dan ponsel dengan kedua tangannya, tanpa tisu. Cara menutup bersin yang paling tepat adalah ...', [
                    'Membiarkan bersin terbuka karena tangan sedang penuh',
                    'Menunduk dan menutup hidung serta mulut dengan lengan atas bagian dalam',
                    'Meletakkan barang lalu menutup dengan kedua telapak tangan',
                    'Menoleh ke arah pasien di sebelahnya',
                ], 1, 'Tanpa tisu, lengan atas bagian dalam adalah penutup yang tepat dan tetap bisa dilakukan meski tangan memegang barang. Menutup dengan telapak tangan justru mencemari tangan yang kemudian menyentuh tas, ponsel, dan benda lain.'],
                'remedial' => ['mudah', 'Bagian tubuh yang dianjurkan untuk menutup batuk ketika tidak ada tisu adalah ...', [
                    'Punggung tangan',
                    'Telapak tangan',
                    'Lengan atas bagian dalam',
                    'Jari-jari tangan',
                ], 2, 'Lengan atas bagian dalam jarang dipakai menyentuh benda sehingga risiko penyebaran melalui kontak lebih kecil. Punggung tangan, telapak, dan jari tetap bagian tangan yang sering menyentuh permukaan.'],
            ],
        ],
        [
            'indicator' => 'Menentukan tindakan lanjutan setelah batuk atau bersin',
            'section' => 'Cara yang Benar',
            'reinforcement' => 'Setelah batuk atau bersin, tisu bekas langsung dibuang ke tempat sampah (di fasyankes ke tempat sampah infeksius), lalu lakukan kebersihan tangan. Orang yang sedang batuk atau pilek juga dianjurkan memakai masker agar droplet tertahan sejak sumbernya.',
            'questions' => [
                'utama' => ['mudah', 'Langkah-langkah etika batuk berikut yang urutannya benar adalah ...', [
                    'Tutup dengan tisu, buang tisu ke tempat sampah, lalu lakukan kebersihan tangan',
                    'Tutup dengan tisu, lakukan kebersihan tangan, lalu simpan tisu di saku',
                    'Tutup dengan telapak tangan, lalu lap tangan ke baju',
                    'Lakukan kebersihan tangan, tutup dengan tisu, lalu letakkan tisu di meja',
                ], 0, 'Tisu dipakai menutup batuk, segera dibuang, kemudian tangan dibersihkan karena bisa terkontaminasi sekret. Menyimpan tisu di saku membuat droplet tetap terbawa dan mencemari tangan lagi.'],
                'penguatan' => ['sedang', 'Seorang pasien rawat jalan sudah dua hari batuk dan pilek, lalu datang untuk kontrol. Selain menutup batuk dengan tisu, anjuran perawat yang paling tepat adalah ...', [
                    'Memakai sarung tangan selama berada di fasyankes',
                    'Menahan batuk selama berada di ruang tunggu',
                    'Berkumur dengan air hangat sebelum masuk ruang periksa',
                    'Memakai masker selama berada di fasyankes dan membersihkan tangan setelah batuk',
                ], 3, 'Orang yang batuk atau pilek dianjurkan memakai masker dan menjaga kebersihan tangan agar droplet tidak menyebar. Sarung tangan tidak menahan droplet dari saluran pernapasan sehingga bukan anjuran yang tepat.'],
                'remedial' => ['mudah', 'Seorang keluarga pasien selesai bersin menggunakan tisu di bangsal. Tempat yang tepat untuk membuang tisu tersebut adalah ...', [
                    'Tempat sampah non-infeksius di ruang perawat',
                    'Tempat sampah infeksius',
                    'Lantai di bawah tempat tidur',
                    'Kantong plastik milik keluarga',
                ], 1, 'Di fasyankes, tisu bekas berisi sekret pernapasan dibuang ke tempat sampah infeksius. Tempat sampah non-infeksius diperuntukkan bagi limbah domestik yang tidak terkontaminasi.'],
            ],
        ],
    ],
    'Fasilitas dan Edukasi Etika Batuk' => [
        [
            'indicator' => 'Mengidentifikasi fasilitas pendukung etika batuk di fasyankes',
            'section' => 'Fasilitas Pendukung',
            'reinforcement' => 'Fasyankes perlu menyediakan tisu, masker, handrub atau wastafel, tempat sampah, serta poster etika batuk. Sarana ini diletakkan di area tunggu dan pintu masuk agar mudah dijangkau pasien dan pengunjung sejak awal kedatangan.',
            'questions' => [
                'utama' => ['mudah', 'Berikut yang BUKAN termasuk fasilitas pendukung etika batuk di fasyankes adalah ...', [
                    'Poster etika batuk',
                    'Handrub atau wastafel',
                    'Alat nebulizer',
                    'Masker dan tisu',
                ], 2, 'Nebulizer adalah alat terapi pernapasan, bukan sarana pendukung etika batuk. Poster, handrub/wastafel, masker, tisu, dan tempat sampah termasuk fasilitas yang perlu disediakan.'],
                'penguatan' => ['sedang', 'Saat supervisi, perawat menemukan ruang tunggu poli paru sudah memiliki poster etika batuk dan tisu, tetapi tidak ada tempat sampah dan handrub. Dampak yang paling mungkin terjadi adalah ...', [
                    'Pasien tidak dapat membuang tisu bekas dan membersihkan tangan sehingga penularan melalui kontak tetap berisiko',
                    'Poster menjadi tidak terbaca oleh pasien',
                    'Pasien tidak akan batuk karena ada poster',
                    'Tidak ada dampak karena tisu sudah tersedia',
                ], 0, 'Etika batuk memerlukan rangkaian lengkap: menutup batuk, membuang tisu, dan membersihkan tangan. Tisu saja tidak cukup karena tisu bekas dan tangan yang terkontaminasi tetap dapat menyebarkan mikroorganisme.'],
                'remedial' => ['mudah', 'Lokasi yang paling tepat untuk meletakkan dispenser masker dan handrub bagi pasien adalah ...', [
                    'Ruang penyimpanan logistik',
                    'Ruang istirahat petugas',
                    'Ruang administrasi keuangan',
                    'Pintu masuk dan area tunggu',
                ], 3, 'Pintu masuk dan area tunggu adalah titik pertama dan paling ramai dilalui pasien serta pengunjung. Sarana di ruang logistik atau ruang petugas tidak terjangkau oleh pasien.'],
            ],
        ],
        [
            'indicator' => 'Menerapkan jaga jarak minimal 1 meter pada pasien dengan gejala batuk',
            'section' => 'Jaga Jarak',
            'reinforcement' => 'Droplet dari batuk atau bersin umumnya jatuh dalam jarak sekitar 1 meter. Karena itu pasien dengan gejala batuk ditempatkan berjarak minimal 1 meter dari pasien lain untuk mengurangi risiko penularan droplet.',
            'questions' => [
                'utama' => ['mudah', 'Pasien bergejala batuk ditempatkan minimal 1 meter dari pasien lain karena ...', [
                    'Suara batuk tidak terdengar dari jarak tersebut',
                    'Droplet umumnya jatuh dalam jarak sekitar 1 meter',
                    'Aturan tata letak kursi ruang tunggu',
                    'Pasien batuk membutuhkan ruang lebih luas untuk bernapas',
                ], 1, 'Jarak minimal 1 meter didasarkan pada jangkauan droplet yang umumnya jatuh dalam radius sekitar 1 meter. Alasannya adalah pencegahan penularan, bukan kenyamanan atau tata letak ruangan.'],
                'penguatan' => ['sedang', 'Kursi di ruang tunggu puskesmas tersusun rapat tanpa jarak. Tiga pasien datang dengan batuk. Pengaturan yang paling tepat adalah ...', [
                    'Mendudukkan ketiganya di kursi paling depan agar cepat dipanggil',
                    'Meminta pasien batuk berdiri di dekat meja pendaftaran',
                    'Memberi masker dan mengatur tempat duduk pasien batuk berjarak minimal 1 meter dari pasien lain',
                    'Mendudukkan pasien batuk berselang satu kursi kosong dengan pasien lain yang jaraknya kurang dari 1 meter',
                ], 2, 'Masker menahan droplet dan jarak minimal 1 meter mengurangi paparan pada pasien lain. Menyisakan satu kursi kosong tampak menjaga jarak, tetapi bila jaraknya kurang dari 1 meter belum memenuhi anjuran.'],
                'remedial' => ['mudah', 'Seorang ibu yang batuk bertanya seberapa jauh ia sebaiknya duduk dari pasien lain di ruang tunggu. Jawaban perawat yang tepat adalah ...', [
                    'Minimal 1 meter',
                    'Cukup 30 sentimeter',
                    'Boleh berdampingan asalkan tidak berbicara',
                    'Tidak perlu berjarak asal memakai jaket',
                ], 0, 'Pasien dengan gejala batuk sebaiknya berjarak minimal 1 meter dari orang lain. Duduk berdampingan tetap berisiko karena droplet dapat keluar saat batuk meski tidak berbicara.'],
            ],
        ],
        [
            'indicator' => 'Menerapkan edukasi etika batuk serta perilaku petugas yang bergejala',
            'section' => 'Edukasi',
            'reinforcement' => 'Perawat mengedukasi pasien dan pengunjung dengan bahasa sederhana dan contoh langsung agar mudah dipahami dan ditiru. Petugas yang sedang batuk atau pilek tetap wajib memakai masker bedah, menerapkan etika batuk, dan menjaga kebersihan tangan.',
            'questions' => [
                'utama' => ['mudah', 'Metode edukasi etika batuk yang paling dianjurkan bagi pasien dan pengunjung adalah ...', [
                    'Membagikan jurnal ilmiah tentang transmisi droplet',
                    'Menggunakan istilah medis agar terdengar meyakinkan',
                    'Menegur pasien yang melanggar di depan umum',
                    'Menggunakan bahasa sederhana disertai contoh langsung',
                ], 3, 'Edukasi dengan bahasa sederhana dan demonstrasi langsung lebih mudah dipahami dan ditiru oleh masyarakat awam. Istilah medis justru menyulitkan pemahaman pasien dan pengunjung.'],
                'penguatan' => ['sedang', 'Seorang perawat bangsal mulai pilek dan sesekali bersin, tetapi merasa cukup sehat untuk bertugas. Tindakan yang paling tepat adalah ...', [
                    'Bertugas tanpa masker karena gejalanya ringan',
                    'Bertugas dengan masker bedah, menerapkan etika batuk, dan menjaga kebersihan tangan',
                    'Bertugas dengan sarung tangan tanpa masker',
                    'Hanya menghindari pasien anak dan lansia',
                ], 1, 'Petugas yang bergejala tetap dapat menularkan droplet sehingga harus memakai masker bedah, menerapkan etika batuk, dan menjaga kebersihan tangan. Sarung tangan tidak menahan droplet dari saluran pernapasan.'],
                'remedial' => ['sedang', 'Perawat akan mengajarkan etika batuk kepada sekelompok pengunjung lansia. Cara yang paling efektif adalah ...', [
                    'Memperagakan cara menutup batuk dengan tisu dan lengan atas bagian dalam, lalu meminta mereka menirukannya',
                    'Memberikan brosur berhuruf kecil tanpa penjelasan',
                    'Menjelaskan patofisiologi infeksi saluran napas secara rinci',
                    'Menempelkan poster lalu meninggalkan mereka',
                ], 0, 'Contoh langsung yang ditirukan membuat materi mudah dipahami dan diingat. Penjelasan patofisiologi yang rinci terlalu teknis dan tidak langsung mengajarkan perilaku yang benar.'],
            ],
        ],
    ],
    'Penerapan di Fasyankes dan Studi Kasus' => [
        [
            'indicator' => 'Menjelaskan skrining gejala batuk dan pemisahan area tunggu',
            'section' => 'Penerapan di Fasyankes',
            'reinforcement' => 'Skrining gejala batuk dilakukan sejak di pintu masuk atau triase agar pasien bergejala dikenali lebih awal. Pasien yang batuk diberi masker dan diarahkan ke area tunggu terpisah sehingga paparan terhadap pasien lain berkurang.',
            'questions' => [
                'utama' => ['mudah', 'Skrining gejala batuk pada pasien yang datang ke fasyankes sebaiknya dilakukan di ...', [
                    'Ruang rawat inap setelah pasien dirawat',
                    'Apotek saat pengambilan obat',
                    'Pintu masuk atau triase',
                    'Kasir saat pembayaran',
                ], 2, 'Skrining di pintu masuk atau triase membuat pasien bergejala segera dikenali dan diberi masker sebelum bercampur dengan pasien lain. Skrining di apotek atau kasir sudah terlambat karena pasien telah melewati area ramai.'],
                'penguatan' => ['sedang', 'Petugas skrining di pintu masuk rumah sakit menemukan pengunjung yang batuk dan bersin-bersin. Tindak lanjut yang paling tepat adalah ...', [
                    'Mencatat keluhannya lalu mempersilakannya ke ruang tunggu umum',
                    'Memberinya masker dan mengarahkannya ke area tunggu terpisah',
                    'Menolak pasien karena berisiko menularkan',
                    'Memberinya obat batuk agar tidak batuk di ruang tunggu',
                ], 1, 'Pasien batuk hasil skrining diberi masker dan diarahkan ke area tunggu terpisah untuk mengurangi paparan pada orang lain. Menolak pasien tidak tepat karena ia tetap berhak mendapat pelayanan.'],
                'remedial' => ['mudah', 'Tujuan utama menyediakan area tunggu terpisah bagi pasien batuk adalah ...', [
                    'Mengurangi paparan droplet terhadap pasien lain',
                    'Memberi kenyamanan agar pasien bisa berbaring',
                    'Mempercepat proses administrasi',
                    'Memudahkan pasien batuk bertemu dokter spesialis paru',
                ], 0, 'Area tunggu terpisah memisahkan sumber droplet dari pasien lain sehingga risiko penularan berkurang. Tujuannya pencegahan infeksi, bukan kenyamanan atau percepatan administrasi.'],
            ],
        ],
        [
            'indicator' => 'Menentukan prioritas pelayanan pada pasien suspek TB',
            'section' => 'Penerapan di Fasyankes',
            'reinforcement' => 'Pasien suspek TB diprioritaskan pemeriksaannya agar waktu tunggunya di fasyankes lebih singkat. Semakin singkat waktu tunggu, semakin kecil paparan terhadap pasien dan petugas lain. Pasien juga tetap diberi masker dan ditempatkan di area terpisah.',
            'questions' => [
                'utama' => ['sedang', 'Pasien suspek TB diprioritaskan pemeriksaannya di poliklinik dengan tujuan ...', [
                    'Memberikan pelayanan istimewa kepada pasien TB',
                    'Mengurangi waktu tunggu sehingga paparan terhadap orang lain berkurang',
                    'Agar pasien segera mendapat rujukan ke rumah sakit lain',
                    'Mengurangi jumlah pasien yang harus diskrining',
                ], 1, 'Prioritas pemeriksaan memperpendek waktu pasien suspek TB berada di ruang tunggu sehingga paparan kepada orang lain berkurang. Prioritas ini adalah upaya pengendalian infeksi, bukan bentuk pelayanan istimewa.'],
                'penguatan' => ['sulit', 'Di poli umum terdapat antrean panjang. Seorang pasien nomor antrean 25 mengeluh batuk lama disertai keringat malam dan sudah memakai masker. Keputusan perawat yang paling tepat adalah ...', [
                    'Tetap menunggu sesuai nomor antrean agar adil bagi pasien lain',
                    'Meminta pasien menunggu di luar gedung hingga antrean habis',
                    'Menempatkan pasien di area tunggu terpisah dan memprioritaskan pemeriksaannya',
                    'Mengganti masker pasien dengan dua lapis masker lalu mengembalikannya ke antrean umum',
                ], 2, 'Pasien dengan gejala yang mengarah ke TB ditempatkan terpisah dan diprioritaskan pemeriksaannya untuk mengurangi waktu tunggu. Menunggu sesuai antrean memang terasa adil, tetapi memperpanjang paparan terhadap banyak orang.'],
                'remedial' => ['mudah', 'Seorang pasien suspek TB sudah diberi masker di triase. Langkah berikutnya yang sesuai penerapan etika batuk di fasyankes adalah ...', [
                    'Menempatkannya di ruang tunggu umum bersama pasien lain',
                    'Meminta pasien melepas masker saat menunggu agar tidak sesak',
                    'Menunda pemeriksaan hingga semua pasien lain selesai',
                    'Mengarahkannya ke area tunggu terpisah dan mendahulukan pemeriksaannya',
                ], 3, 'Setelah diberi masker, pasien suspek TB diarahkan ke area tunggu terpisah dan pemeriksaannya diprioritaskan. Menunda pemeriksaan justru memperpanjang waktu tunggu dan risiko paparan.'],
            ],
        ],
        [
            'indicator' => 'Menganalisis kasus pelanggaran etika batuk dan menentukan intervensi perawat',
            'section' => 'Studi Kasus',
            'reinforcement' => 'Menutup batuk dengan telapak tangan lalu memegang benda seperti gagang pintu dapat memindahkan mikroorganisme ke permukaan yang disentuh orang lain. Intervensi perawat dilakukan dengan sopan: memberikan masker, mengajarkan cara menutup batuk yang benar, menunjukkan tempat sampah, dan mengajak membersihkan tangan dengan handrub.',
            'questions' => [
                'utama' => ['sedang', 'Pengunjung menutup batuk dengan telapak tangan lalu memegang gagang pintu bangsal. Risiko utama dari perilaku ini adalah ...', [
                    'Gagang pintu menjadi rusak',
                    'Pengunjung menjadi lebih sering batuk',
                    'Mikroorganisme berpindah ke gagang pintu dan dapat menulari orang lain yang menyentuhnya',
                    'Tidak ada risiko karena mulut sudah ditutup',
                ], 2, 'Droplet yang menempel di telapak tangan berpindah ke gagang pintu, lalu ke tangan orang lain yang menyentuhnya. Anggapan tidak ada risiko keliru karena menutup dengan telapak tangan tetap mencemari tangan.'],
                'penguatan' => ['sulit', 'Perawat mendapati seorang pengunjung batuk-batuk tanpa masker di dekat tempat tidur pasien pascaoperasi. Urutan intervensi yang paling tepat adalah ...', [
                    'Mendekat dengan sopan, memberikan masker, mengajarkan cara menutup batuk dengan tisu atau lengan atas bagian dalam, menunjukkan tempat sampah, lalu mengajak membersihkan tangan',
                    'Meminta satpam mengeluarkan pengunjung dari bangsal',
                    'Memberikan masker tanpa penjelasan lalu kembali bekerja',
                    'Mengajarkan etika batuk dari meja perawat dengan suara keras',
                ], 0, 'Intervensi yang lengkap dan santun mencakup pemberian masker, edukasi cara menutup batuk, pembuangan tisu, dan kebersihan tangan. Memberi masker tanpa edukasi tidak mengubah perilaku sehingga pengunjung tetap berisiko menularkan melalui tangan.'],
                'remedial' => ['mudah', 'Setelah mengedukasi pengunjung yang batuk ke telapak tangan, perawat mengajaknya membersihkan tangan. Sarana yang paling praktis digunakan di bangsal adalah ...', [
                    'Kain lap di meja perawat',
                    'Handrub berbasis alkohol',
                    'Tisu kering saja',
                    'Air minum dalam botol',
                ], 1, 'Handrub berbasis alkohol praktis dan efektif untuk kebersihan tangan bila tangan tidak tampak kotor. Tisu kering hanya menyeka permukaan tanpa membunuh mikroorganisme.'],
            ],
        ],
    ],
];
