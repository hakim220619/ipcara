<?php

/**
 * Latihan per materi topik PPI-05: 3 indikator × (utama, penguatan, remedial).
 * Format soal: [tingkat, teks soal, pilihan A–D, indeks kunci 0–3, pembahasan].
 */
return [
    'Jenis Limbah dan Kode Warna' => [
        [
            'indicator' => 'Membedakan limbah infeksius dan non-infeksius beserta kantongnya',
            'section' => 'Jenis Limbah Fasyankes',
            'reinforcement' => 'Limbah infeksius adalah limbah yang terkontaminasi darah atau cairan tubuh, misalnya kasa dan sarung tangan bekas, dan dibuang ke kantong kuning. Limbah non-infeksius (domestik) seperti kertas, plastik bersih, dan sisa makanan dibuang ke kantong hitam. Penentunya adalah ada tidaknya kontaminasi darah/cairan tubuh, bukan jenis bahannya.',
            'questions' => [
                'utama' => ['mudah', 'Suatu limbah fasyankes digolongkan sebagai limbah infeksius apabila limbah tersebut ...', [
                    'Berasal dari ruang administrasi',
                    'Terbuat dari bahan plastik',
                    'Terkontaminasi darah atau cairan tubuh pasien',
                    'Berukuran besar dan berat',
                ], 2, 'Ciri limbah infeksius adalah terkontaminasi darah atau cairan tubuh sehingga berpotensi menularkan infeksi. Bahan plastik tidak otomatis infeksius; plastik bersih justru termasuk limbah domestik.'],
                'penguatan' => ['sedang', 'Setelah memasang infus, sarung tangan perawat terkena darah pasien. Sarung tangan bekas tersebut dibuang ke ...', [
                    'Kantong kuning',
                    'Kantong hitam',
                    'Wadah tahan tusuk (safety box)',
                    'Kantong coklat',
                ], 0, 'Sarung tangan yang terkena darah termasuk limbah infeksius sehingga dibuang ke kantong kuning. Safety box hanya untuk benda tajam seperti jarum, bukan sarung tangan.'],
                'remedial' => ['mudah', 'Setelah makan siang di nurse station, perawat akan membuang bungkus nasi dan kertas tisu bersih. Kantong yang tepat adalah kantong berwarna ...', [
                    'Kuning',
                    'Ungu',
                    'Merah',
                    'Hitam',
                ], 3, 'Bungkus makanan dan tisu bersih tidak terkontaminasi darah/cairan tubuh sehingga termasuk limbah domestik (kantong hitam). Membuangnya ke kantong kuning keliru dan menambah beban pengolahan limbah infeksius.'],
            ],
        ],
        [
            'indicator' => 'Menentukan wadah yang tepat untuk limbah benda tajam',
            'section' => 'Jenis Limbah Fasyankes',
            'reinforcement' => 'Benda tajam seperti jarum, pisau bedah, dan ampul dibuang ke wadah tahan tusuk (safety box), bukan ke kantong plastik. Kantong plastik mudah tertembus sehingga dapat melukai petugas yang menanganinya. Bila satu tindakan menghasilkan benda tajam dan limbah infeksius lain, keduanya dipisahkan ke wadah masing-masing.',
            'questions' => [
                'utama' => ['mudah', 'Ampul obat yang telah dipatahkan dan dikosongkan setelah menyiapkan injeksi harus dibuang ke ...', [
                    'Kantong hitam',
                    'Kantong kuning',
                    'Wadah tahan tusuk (safety box)',
                    'Kantong coklat',
                ], 2, 'Pecahan ampul termasuk benda tajam sehingga dibuang ke safety box. Kantong kuning tidak tepat karena pecahan kaca dapat merobek plastik dan melukai petugas.'],
                'penguatan' => ['sedang', 'Alasan utama benda tajam bekas tidak boleh dibuang ke kantong plastik kuning adalah ...', [
                    'Kantong kuning khusus untuk limbah sitotoksik',
                    'Benda tajam dapat menembus kantong plastik dan melukai petugas yang menangani limbah',
                    'Benda tajam tidak termasuk limbah berbahaya',
                    'Kantong kuning harganya lebih mahal',
                ], 1, 'Wadah benda tajam harus tahan tusuk karena jarum atau pisau dapat menembus plastik dan menyebabkan cedera tusuk. Kantong kuning adalah untuk limbah infeksius, bukan sitotoksik (ungu).'],
                'remedial' => ['mudah', 'Setelah menjahit luka, perawat memiliki jarum jahit bekas dan kasa berdarah. Cara pembuangan yang tepat adalah ...', [
                    'Keduanya dibuang ke kantong kuning',
                    'Keduanya dibuang ke safety box',
                    'Jarum ke kantong hitam, kasa ke kantong kuning',
                    'Jarum ke safety box, kasa berdarah ke kantong kuning',
                ], 3, 'Jarum jahit adalah benda tajam (safety box), sedangkan kasa berdarah adalah limbah infeksius (kantong kuning). Memasukkan keduanya ke kantong kuning berisiko jarum menembus kantong.'],
            ],
        ],
        [
            'indicator' => 'Mencocokkan kode warna limbah sitotoksik, radioaktif, dan farmasi/kimia',
            'section' => 'Jenis Limbah Fasyankes',
            'reinforcement' => 'Selain kuning (infeksius) dan hitam (domestik), terdapat kode warna untuk limbah khusus: ungu untuk sitotoksik (sisa obat kemoterapi), merah untuk radioaktif, dan coklat untuk farmasi dan kimia (misalnya obat kedaluwarsa). Warna yang tepat memastikan limbah diolah dengan metode yang sesuai bahayanya.',
            'questions' => [
                'utama' => ['mudah', 'Pasangan jenis limbah dan warna kantong yang BENAR adalah ...', [
                    'Radioaktif – ungu',
                    'Farmasi dan kimia – coklat',
                    'Sitotoksik – merah',
                    'Domestik – kuning',
                ], 1, 'Limbah farmasi dan kimia ditampung dalam kantong coklat. Pilihan lain tertukar: radioaktif seharusnya merah, sitotoksik ungu, dan domestik hitam.'],
                'penguatan' => ['sedang', 'Di ruang kemoterapi, perawat akan membuang botol dan selang infus bekas yang berisi sisa obat kemoterapi. Kantong yang tepat berwarna ...', [
                    'Kuning',
                    'Hitam',
                    'Ungu',
                    'Merah',
                ], 2, 'Sisa obat kemoterapi termasuk limbah sitotoksik yang ditampung dalam kantong ungu. Kantong kuning kurang tepat karena limbah sitotoksik memerlukan penanganan khusus terpisah dari limbah infeksius biasa.'],
                'remedial' => ['sedang', 'Sisa reagen kimia dari laboratorium rumah sakit yang tidak terpakai lagi dibuang ke kantong berwarna ...', [
                    'Coklat',
                    'Hitam',
                    'Merah',
                    'Ungu',
                ], 0, 'Kantong coklat digunakan untuk limbah farmasi dan kimia, termasuk reagen kimia. Kantong merah khusus untuk limbah radioaktif, bukan bahan kimia biasa.'],
            ],
        ],
    ],
    'Pemilahan dan Pengelolaan Benda Tajam' => [
        [
            'indicator' => 'Menerapkan prinsip pemilahan limbah di sumber',
            'section' => 'Pemilahan di Sumber',
            'reinforcement' => 'Limbah dipilah di tempat limbah dihasilkan, misalnya di troli tindakan atau ruang pasien, bukan di tempat penampungan. Karena itu sarana pemilahan (kantong kuning, kantong hitam, dan safety box) harus tersedia di lokasi tindakan. Pemilahan di sumber mencegah limbah infeksius tercampur dan menghindarkan petugas dari memilah ulang limbah berbahaya.',
            'questions' => [
                'utama' => ['mudah', 'Contoh penerapan pemilahan limbah di sumber yang benar adalah ...', [
                    'Semua limbah ruangan dikumpulkan dalam satu kantong, lalu dipilah di TPS',
                    'Petugas kebersihan memilah limbah saat mengosongkan tempat sampah',
                    'Limbah dipilah ketika akan diangkut ke luar rumah sakit',
                    'Perawat langsung membuang kasa berdarah ke kantong kuning di troli tindakan setelah merawat luka',
                ], 3, 'Pemilahan di sumber berarti limbah dipilah di tempat dihasilkan, seperti troli tindakan. Memilah di TPS terlambat karena limbah sudah tercampur dan berisiko bagi petugas.'],
                'penguatan' => ['sedang', 'Alasan utama pemilahan limbah harus dilakukan di sumber adalah ...', [
                    'Mencegah limbah infeksius tercampur dengan limbah domestik dan menghindari petugas memilah ulang limbah berbahaya',
                    'Agar limbah dapat diangkut lebih cepat',
                    'Agar tempat sampah di ruangan lebih sedikit',
                    'Karena TPS tidak memiliki tempat sampah',
                ], 0, 'Pemilahan di sumber menjaga limbah infeksius tetap terpisah sejak awal sehingga tidak ada yang perlu dipilah ulang dengan risiko tertusuk atau terpajan. Kecepatan pengangkutan bukan tujuan utama pemilahan.'],
                'remedial' => ['mudah', 'Agar pemilahan di sumber dapat dilakukan saat tindakan di ruang pasien, troli tindakan sebaiknya dilengkapi dengan ...', [
                    'Satu kantong hitam besar untuk semua limbah',
                    'Kantong kuning, kantong hitam, dan safety box',
                    'Kantong kuning saja',
                    'Kardus bekas untuk jarum',
                ], 1, 'Tersedianya kantong kuning, kantong hitam, dan safety box di troli memungkinkan setiap limbah langsung dibuang ke wadah yang sesuai. Kantong kuning saja tidak cukup karena benda tajam perlu wadah tahan tusuk.'],
            ],
        ],
        [
            'indicator' => 'Menerapkan penggunaan safety box yang aman',
            'section' => 'Benda Tajam',
            'reinforcement' => 'Safety box diletakkan dekat area tindakan agar jarum bekas bisa langsung dibuang tanpa dibawa berpindah tempat. Jarum tidak boleh ditutup kembali dengan dua tangan (no recapping). Safety box ditutup dan diganti bila sudah terisi 3/4 bagian, tanpa menekan atau memadatkan isinya.',
            'questions' => [
                'utama' => ['mudah', 'Penempatan safety box yang dianjurkan adalah ...', [
                    'Di gudang agar tidak mengganggu',
                    'Di ruang perawat di ujung lorong',
                    'Dekat area tindakan',
                    'Di luar gedung dekat TPS',
                ], 2, 'Safety box diletakkan dekat area tindakan agar jarum bekas langsung dibuang setelah dipakai. Menaruhnya di ruang perawat membuat jarum terbuka harus dibawa berjalan sehingga risiko tertusuk meningkat.'],
                'penguatan' => ['sedang', 'Perawat melihat safety box di ruang tindakan sudah terisi sekitar 3/4 bagian, tetapi masih tampak ada ruang kosong. Tindakan yang tepat adalah ...', [
                    'Menutup safety box tersebut dan menggantinya dengan yang baru',
                    'Menekan isinya agar muat lebih banyak jarum',
                    'Tetap memakainya sampai benar-benar penuh',
                    'Memindahkan sebagian isinya ke kantong kuning',
                ], 0, 'Safety box yang terisi 3/4 harus ditutup dan diganti. Menekan isinya atau mengisi sampai penuh justru meningkatkan risiko tertusuk jarum yang mencuat.'],
                'remedial' => ['sedang', 'Safety box berada di ruang perawat yang jauh, sehingga perawat menutup kembali jarum dengan dua tangan agar aman dibawa. Penilaian terhadap tindakan ini adalah ...', [
                    'Benar, karena jarum yang tertutup lebih aman dibawa',
                    'Salah; safety box seharusnya diletakkan dekat area tindakan dan jarum langsung dibuang tanpa recapping',
                    'Benar, asalkan perawat memakai sarung tangan',
                    'Salah; jarum seharusnya dibuang ke kantong kuning di ruang pasien',
                ], 1, 'Recapping dengan dua tangan dilarang karena sering menyebabkan cedera tusuk; solusinya adalah mendekatkan safety box ke area tindakan. Sarung tangan tidak mencegah tusukan jarum, dan kantong kuning bukan wadah benda tajam.'],
            ],
        ],
        [
            'indicator' => 'Mengurutkan alur pengelolaan limbah fasyankes',
            'section' => 'Alur Pengelolaan',
            'reinforcement' => 'Alur pengelolaan limbah adalah pemilahan, pewadahan, pengangkutan internal dengan troli tertutup, penyimpanan sementara di TPS, lalu pengolahan/pemusnahan (insinerator atau autoclave) oleh pihak berizin. Setiap tahap harus berurutan agar limbah tetap terkendali sampai dimusnahkan.',
            'questions' => [
                'utama' => ['sedang', 'Urutan alur pengelolaan limbah fasyankes yang benar adalah ...', [
                    'Pengangkutan → pemilahan → pewadahan → TPS → pemusnahan',
                    'Pewadahan → TPS → pemilahan → pengangkutan → pemusnahan',
                    'Pemilahan → pewadahan → pengangkutan internal → TPS → pengolahan/pemusnahan',
                    'Pemilahan → TPS → pewadahan → pemusnahan → pengangkutan',
                ], 2, 'Limbah dipilah dan diwadahi di sumber, diangkut ke TPS, lalu diolah/dimusnahkan oleh pihak berizin. Urutan yang menempatkan pengangkutan sebelum pemilahan menyebabkan limbah tercampur sejak awal.'],
                'penguatan' => ['sedang', 'Pengangkutan internal limbah dari ruang perawatan menuju tempat penyimpanan sementara (TPS) dilakukan menggunakan ...', [
                    'Kantong yang dijinjing dengan tangan',
                    'Troli terbuka agar mudah dibongkar',
                    'Kursi roda pasien yang kosong',
                    'Troli tertutup',
                ], 3, 'Troli tertutup mencegah limbah tumpah atau tercecer dan membatasi kontak dengan orang di sepanjang jalur. Troli terbuka memudahkan bongkar muat tetapi berisiko menyebarkan kontaminasi.'],
                'remedial' => ['mudah', 'Tahap akhir pengelolaan limbah infeksius dari fasyankes adalah ...', [
                    'Pengolahan/pemusnahan dengan insinerator atau autoclave oleh pihak berizin',
                    'Dibuang ke tempat pembuangan akhir bersama sampah kota',
                    'Dibakar sendiri di halaman belakang rumah sakit',
                    'Disimpan di TPS tanpa batas waktu',
                ], 0, 'Limbah infeksius harus diolah atau dimusnahkan dengan insinerator atau autoclave oleh pihak berizin. TPS hanya penyimpanan sementara, bukan tahap akhir.'],
            ],
        ],
    ],
    'Penanganan Pajanan dan Tumpahan' => [
        [
            'indicator' => 'Melakukan pertolongan pertama setelah pajanan darah',
            'section' => 'Tertusuk Jarum Bekas Pasien',
            'reinforcement' => 'Luka tusuk segera dicuci dengan sabun dan air mengalir, tanpa dipencet atau dihisap. Percikan ke mata atau mulut dibilas dengan air mengalir. Tindakan cepat dan sederhana ini mengurangi jumlah patogen di area pajanan.',
            'questions' => [
                'utama' => ['mudah', 'Tindakan yang TIDAK boleh dilakukan pada luka akibat tertusuk jarum bekas pasien adalah ...', [
                    'Mencuci luka dengan sabun',
                    'Memencet atau menghisap luka',
                    'Membilas luka dengan air mengalir',
                    'Melaporkan kejadian ke atasan',
                ], 1, 'Luka tidak boleh dipencet atau dihisap karena dapat memperluas kerusakan jaringan dan tidak terbukti mengurangi risiko infeksi. Mencuci dengan sabun dan air mengalir justru merupakan langkah yang benar.'],
                'penguatan' => ['sedang', 'Saat memasang infus, darah pasien terpercik ke mulut perawat. Tindakan pertama yang tepat adalah ...', [
                    'Berkumur dengan alkohol 70%',
                    'Menelan ludah dan melanjutkan tindakan',
                    'Menunggu sampai tindakan selesai lalu mencuci muka',
                    'Segera membilas mulut dengan air mengalir',
                ], 3, 'Percikan ke mulut atau mata segera dibilas dengan air mengalir. Alkohol tidak dianjurkan untuk mukosa karena mengiritasi dan tidak lebih efektif daripada pembilasan air.'],
                'remedial' => ['mudah', 'Bahan yang digunakan untuk mencuci luka segera setelah tertusuk jarum bekas pasien adalah ...', [
                    'Sabun dan air mengalir',
                    'Larutan klorin 0,5%',
                    'Air dalam baskom yang sama untuk berkali-kali bilas',
                    'Tidak perlu dicuci, cukup ditutup plester',
                ], 0, 'Sesuai prosedur, luka dicuci dengan sabun dan air mengalir. Larutan klorin 0,5% dipakai untuk disinfeksi permukaan tumpahan darah, bukan untuk kulit yang luka.'],
            ],
        ],
        [
            'indicator' => 'Menjelaskan tindak lanjut pajanan: pelaporan, evaluasi sumber, dan PPP',
            'section' => 'Tertusuk Jarum Bekas Pasien',
            'reinforcement' => 'Setelah pertolongan pertama, kejadian pajanan dilaporkan ke atasan dan Tim PPI. Status sumber pajanan dievaluasi untuk HIV, hepatitis B, dan hepatitis C. Profilaksis pasca pajanan (PPP) HIV diberikan sesegera mungkin, idealnya kurang dari 4 jam dan tidak lebih dari 72 jam.',
            'questions' => [
                'utama' => ['mudah', 'Setelah mencuci luka tusuk jarum, langkah berikutnya yang harus dilakukan perawat adalah ...', [
                    'Melanjutkan pekerjaan dan melupakannya',
                    'Menunggu gejala muncul terlebih dahulu',
                    'Melaporkan kejadian ke atasan dan Tim PPI',
                    'Membeli obat antivirus sendiri di apotek',
                ], 2, 'Pelaporan ke atasan dan Tim PPI diperlukan agar risiko pajanan dinilai dan tindak lanjut (evaluasi sumber, PPP) dapat diberikan tepat waktu. Menunggu gejala muncul akan melewatkan batas waktu PPP.'],
                'penguatan' => ['sulit', 'Evaluasi status sumber pajanan setelah perawat tertusuk jarum bekas pasien mencakup pemeriksaan ...', [
                    'HIV saja',
                    'HIV, hepatitis B, dan hepatitis C',
                    'Tuberkulosis dan influenza',
                    'Golongan darah dan kadar gula darah',
                ], 1, 'Patogen utama yang ditularkan melalui darah adalah HIV, hepatitis B, dan hepatitis C, sehingga ketiganya dievaluasi. Memeriksa HIV saja tidak cukup karena hepatitis B justru lebih mudah menular melalui tusukan jarum.'],
                'remedial' => ['sedang', 'Perawat tertusuk jarum bekas pasien pukul 08.00 dan melapor ke Tim PPI pukul 10.00. Terkait PPP HIV, pernyataan yang tepat adalah ...', [
                    'Sudah terlambat karena melewati 1 jam',
                    'PPP baru diberikan setelah 72 jam',
                    'PPP tidak diperlukan bila luka tidak berdarah',
                    'Masih dalam waktu ideal (< 4 jam), sehingga evaluasi dan PPP bila diindikasikan harus segera dilakukan',
                ], 3, 'Dua jam setelah pajanan masih dalam rentang ideal kurang dari 4 jam, sehingga PPP bila diindikasikan harus segera dimulai. Batas 72 jam adalah batas maksimal, bukan waktu mulai pemberian.'],
            ],
        ],
        [
            'indicator' => 'Menangani tumpahan darah menggunakan spill kit',
            'section' => 'Tumpahan Darah',
            'reinforcement' => 'Tumpahan darah ditangani dengan spill kit: pakai APD, serap tumpahan dengan bahan penyerap, bersihkan, lalu disinfeksi dengan larutan klorin 0,5%. Semua bahan bekas pembersihan dibuang ke kantong kuning karena termasuk limbah infeksius.',
            'questions' => [
                'utama' => ['mudah', 'Konsentrasi larutan klorin yang digunakan untuk disinfeksi area tumpahan darah adalah ...', [
                    '70%',
                    '5%',
                    '0,05%',
                    '0,5%',
                ], 3, 'Disinfeksi tumpahan darah menggunakan larutan klorin 0,5%. Angka 70% adalah konsentrasi alkohol, bukan klorin, sehingga sering menjadi pengecoh.'],
                'penguatan' => ['sedang', 'Urutan penanganan tumpahan darah dengan spill kit yang benar adalah ...', [
                    'Siram klorin 0,5% → pakai APD → buang bahan ke kantong hitam',
                    'Serap tumpahan dengan tangan → pakai APD → bersihkan',
                    'Pakai APD → serap tumpahan dengan bahan penyerap → bersihkan → disinfeksi klorin 0,5% → buang bahan bekas ke kantong kuning',
                    'Bersihkan dengan kain pel → disinfeksi → pakai APD',
                ], 2, 'APD dipakai lebih dulu untuk melindungi petugas, lalu tumpahan diserap, dibersihkan, dan didisinfeksi dengan klorin 0,5%. Memakai APD setelah menyentuh tumpahan tidak lagi melindungi petugas.'],
                'remedial' => ['mudah', 'Bahan penyerap dan tisu bekas yang digunakan untuk membersihkan tumpahan darah dibuang ke ...', [
                    'Kantong kuning',
                    'Kantong hitam',
                    'Kantong coklat',
                    'Wastafel',
                ], 0, 'Bahan bekas pembersihan terkontaminasi darah sehingga termasuk limbah infeksius dan dibuang ke kantong kuning. Kantong hitam hanya untuk limbah domestik yang tidak terkontaminasi.'],
            ],
        ],
    ],
];
