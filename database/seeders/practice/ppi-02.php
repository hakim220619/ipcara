<?php

/**
 * Latihan per materi topik PPI-02: 3 indikator × (utama, penguatan, remedial).
 * Format soal: [tingkat, teks soal, pilihan A–D, indeks kunci 0–3, pembahasan].
 */
return [
    'Handrub dan Handwash' => [
        [
            'indicator' => 'Membedakan penggunaan handrub dan handwash',
            'section' => 'Dua Metode',
            'reinforcement' => 'Handrub memakai cairan berbasis alkohol tanpa air selama 20–30 detik dan dipakai bila tangan tidak tampak kotor. Handwash memakai sabun dan air mengalir selama 40–60 detik. Pilih metode sesuai kondisi tangan, bukan sesuai kebiasaan.',
            'questions' => [
                'utama' => ['mudah', 'Perbedaan utama antara handrub dan handwash adalah ...', [
                    'Handrub memakai sabun dan air, handwash memakai cairan berbasis alkohol',
                    'Handrub memakai cairan berbasis alkohol tanpa air, handwash memakai sabun dan air mengalir',
                    'Handrub hanya untuk pasien, handwash hanya untuk petugas',
                    'Keduanya sama, hanya berbeda nama',
                ], 1, 'Handrub menggunakan cairan berbasis alkohol yang digosokkan tanpa air, sedangkan handwash menggunakan sabun dan air mengalir. Pilihan A tertukar antara kedua metode.'],
                'penguatan' => ['sedang', 'Tangan perawat tampak bersih dan ia akan mengukur suhu pasien berikutnya. Metode dan durasi kebersihan tangan yang paling tepat adalah ...', [
                    'Handwash dengan sabun dan air selama 10 detik',
                    'Handrub selama 5 detik lalu dikibaskan',
                    'Handrub berbasis alkohol selama 20–30 detik',
                    'Tidak perlu kebersihan tangan karena tangan tampak bersih',
                ], 2, 'Tangan yang tidak tampak kotor cukup dibersihkan dengan handrub selama 20–30 detik. Tangan yang tampak bersih tetap membawa mikroorganisme, sehingga pilihan "tidak perlu" salah.'],
                'remedial' => ['mudah', 'Seorang mahasiswa mencuci tangan dengan sabun dan air mengalir hanya selama 15 detik. Penilaian yang tepat atas tindakan ini adalah ...', [
                    'Belum sesuai, karena handwash dianjurkan selama 40–60 detik',
                    'Sudah sesuai, karena 15 detik sudah cukup untuk handwash',
                    'Sudah sesuai, asalkan memakai sabun antiseptik',
                    'Belum sesuai, karena handwash harus dilakukan minimal 5 menit',
                ], 0, 'Durasi handwash yang dianjurkan adalah 40–60 detik agar seluruh permukaan tangan tergosok. Lima menit bukan standar kebersihan tangan rutin, sehingga pilihan D juga salah.'],
            ],
        ],
        [
            'indicator' => 'Menentukan kondisi yang mewajibkan handwash',
            'section' => 'Wajib Handwash bila',
            'reinforcement' => 'Handwash wajib bila tangan tampak kotor atau terkena darah/cairan tubuh, setelah dari toilet, dan setelah merawat pasien dengan infeksi pembentuk spora seperti Clostridioides difficile. Alkohol tidak efektif mengangkat kotoran organik dan tidak membunuh spora.',
            'questions' => [
                'utama' => ['mudah', 'Berikut ini yang BUKAN merupakan kondisi wajib handwash adalah ...', [
                    'Setelah dari toilet',
                    'Tangan terkena darah pasien',
                    'Setelah merawat pasien dengan infeksi Clostridioides difficile',
                    'Sebelum menyentuh pasien dengan tangan yang tampak bersih',
                ], 3, 'Tangan yang tampak bersih sebelum kontak dengan pasien cukup dibersihkan dengan handrub. Setelah dari toilet justru termasuk kondisi wajib handwash, jadi pilihan A bukan jawabannya.'],
                'penguatan' => ['sedang', 'Perawat baru keluar dari toilet dan akan langsung masuk ke ruang pasien. Tindakan kebersihan tangan yang benar adalah ...', [
                    'Cukup handrub di depan pintu ruang pasien',
                    'Mencuci tangan dengan sabun dan air mengalir sebelum ke ruang pasien',
                    'Memakai sarung tangan tanpa membersihkan tangan',
                    'Mengelap tangan dengan tisu kering',
                ], 1, 'Setelah dari toilet, kebersihan tangan wajib dilakukan dengan sabun dan air mengalir. Handrub tidak menggantikan handwash pada kondisi ini, sehingga pilihan A kurang tepat.'],
                'remedial' => ['mudah', 'Tangan perawat terkena percikan muntahan pasien saat membantu pasien. Alasan perawat harus memilih handwash, bukan handrub, adalah ...', [
                    'Handrub lebih mahal daripada sabun',
                    'Handrub membuat kulit tangan kering',
                    'Alkohol tidak efektif mengangkat kotoran dan bahan organik dari tangan',
                    'Handwash lebih cepat daripada handrub',
                ], 2, 'Cairan tubuh seperti muntahan adalah bahan organik yang harus diangkat secara mekanis dengan sabun dan air mengalir. Handwash justru lebih lama (40–60 detik) daripada handrub, sehingga pilihan D salah.'],
            ],
        ],
        [
            'indicator' => 'Menerapkan persiapan tangan sebelum merawat pasien',
            'section' => 'Persiapan',
            'reinforcement' => 'Sebelum merawat pasien, pastikan kuku pendek dan bersih, tanpa cat kuku, serta lepaskan cincin, gelang, dan jam tangan. Area di bawah kuku panjang dan perhiasan sulit dibersihkan sehingga mikroorganisme tetap tertinggal.',
            'questions' => [
                'utama' => ['mudah', 'Sebelum berdinas di bangsal, mahasiswa menyadari kukunya masih memakai cat kuku. Tindakan yang tepat adalah ...', [
                    'Membiarkannya karena cat kuku tidak berpengaruh',
                    'Menutupinya dengan sarung tangan selama dinas',
                    'Menambah durasi handrub menjadi 2 menit',
                    'Menghapus cat kuku dan memastikan kuku pendek serta bersih',
                ], 3, 'Persiapan kebersihan tangan mensyaratkan kuku pendek, bersih, dan tanpa cat kuku. Sarung tangan tidak menggantikan persiapan ini karena tidak dipakai sepanjang waktu dan dapat bocor.'],
                'penguatan' => ['sedang', 'Perawat memakai jam tangan dan berencana hanya menggesernya ke atas pergelangan saat mencuci tangan. Tanggapan yang tepat adalah ...', [
                    'Jam tangan sebaiknya dilepas saat merawat pasien agar seluruh tangan dan pergelangan dapat dibersihkan',
                    'Sudah benar asalkan jam tangan tidak basah',
                    'Sudah benar karena jam tangan dibutuhkan untuk menghitung nadi',
                    'Jam tangan boleh dipakai asalkan tali jamnya dari logam',
                ], 0, 'Jam tangan termasuk perhiasan yang tidak boleh dipakai saat merawat pasien karena menghalangi pembersihan pergelangan dan menjadi tempat mikroorganisme. Kebutuhan menghitung nadi dapat dipenuhi dengan jam dinding atau jam saku, sehingga pilihan C tidak tepat.'],
                'remedial' => ['mudah', 'Empat mahasiswa bersiap dinas. Mahasiswa yang persiapan tangannya sudah sesuai adalah ...', [
                    'Mahasiswa A: kuku pendek, tetapi memakai cincin',
                    'Mahasiswa B: kuku pendek, bersih, dan tanpa perhiasan',
                    'Mahasiswa C: tanpa perhiasan, tetapi kuku panjang',
                    'Mahasiswa D: kuku pendek, tetapi memakai gelang',
                ], 1, 'Persiapan yang benar memenuhi semua syarat: kuku pendek, bersih, tanpa cat, dan tanpa cincin, gelang, atau jam tangan. Mahasiswa A tampak hampir benar, tetapi cincin tetap menjadi tempat mikroorganisme tertinggal.'],
            ],
        ],
    ],
    'Lima Momen Kebersihan Tangan WHO' => [
        [
            'indicator' => 'Menyebutkan lima momen kebersihan tangan WHO',
            'section' => 'Lima Momen',
            'reinforcement' => 'Lima momen WHO: (1) sebelum kontak dengan pasien, (2) sebelum tindakan aseptik, (3) setelah terpapar cairan tubuh pasien, (4) setelah kontak dengan pasien, (5) setelah kontak dengan lingkungan sekitar pasien. Momen 1 dan 2 melindungi pasien, momen 3–5 melindungi petugas dan lingkungan.',
            'questions' => [
                'utama' => ['mudah', 'Momen ketiga dari lima momen kebersihan tangan WHO adalah ...', [
                    'Sebelum tindakan aseptik',
                    'Setelah kontak dengan pasien',
                    'Setelah terpapar cairan tubuh pasien',
                    'Setelah kontak dengan lingkungan sekitar pasien',
                ], 2, 'Momen 3 adalah setelah terpapar cairan tubuh pasien. Sebelum tindakan aseptik adalah momen 2, sehingga pilihan A tertukar urutannya.'],
                'penguatan' => ['sedang', 'Tujuan utama kebersihan tangan pada momen 1 (sebelum kontak dengan pasien) adalah ...', [
                    'Melindungi petugas dari kuman pasien',
                    'Melindungi pasien dari kuman yang terbawa di tangan petugas',
                    'Membersihkan lingkungan sekitar pasien',
                    'Menggantikan pemakaian sarung tangan',
                ], 1, 'Momen 1 dilakukan sebelum menyentuh pasien sehingga tujuannya melindungi pasien dari kuman di tangan petugas. Melindungi petugas adalah tujuan momen setelah kontak (momen 3–5), sehingga pilihan A kurang tepat.'],
                'remedial' => ['mudah', 'Berikut ini yang TIDAK termasuk lima momen kebersihan tangan WHO adalah ...', [
                    'Sebelum tindakan aseptik',
                    'Setelah kontak dengan lingkungan sekitar pasien',
                    'Setelah terpapar cairan tubuh pasien',
                    'Sebelum mengambil formulir di ruang administrasi',
                ], 3, 'Mengambil formulir di ruang administrasi tidak berkaitan dengan pasien atau lingkungannya, sehingga bukan salah satu dari lima momen. Setelah kontak dengan lingkungan sekitar pasien tetap termasuk momen 5 walau tidak menyentuh pasien.'],
            ],
        ],
        [
            'indicator' => 'Menentukan momen kebersihan tangan pada situasi klinis',
            'section' => 'Lima Momen',
            'reinforcement' => 'Kenali momen dari jenis kegiatannya: tindakan aseptik (infus, kateter, rawat luka) adalah momen 2; kontak dengan darah, urin, atau cairan tubuh lain adalah momen 3; menyentuh pasien adalah momen 4; menyentuh benda di sekitar pasien tanpa menyentuh pasien adalah momen 5.',
            'questions' => [
                'utama' => ['sedang', 'Perawat baru selesai membuang urin dari kantong urin pasien. Kebersihan tangan yang dilakukan setelahnya termasuk momen ...', [
                    'Momen 3: setelah terpapar cairan tubuh pasien',
                    'Momen 1: sebelum kontak dengan pasien',
                    'Momen 2: sebelum tindakan aseptik',
                    'Momen 5: setelah kontak dengan lingkungan sekitar pasien',
                ], 0, 'Membuang urin berisiko paparan cairan tubuh sehingga termasuk momen 3. Walaupun kantong urin berada di sekitar pasien, risiko paparan cairan tubuh lebih menentukan, sehingga momen 5 kurang tepat.'],
                'penguatan' => ['sulit', 'Perawat masuk ke kamar, mengatur tetesan infus pada tiang infus dan memegang pagar tempat tidur, lalu keluar tanpa menyentuh pasien. Momen kebersihan tangan yang berlaku saat keluar adalah ...', [
                    'Momen 2: sebelum tindakan aseptik',
                    'Momen 4: setelah kontak dengan pasien',
                    'Momen 5: setelah kontak dengan lingkungan sekitar pasien',
                    'Tidak ada momen karena pasien tidak disentuh',
                ], 2, 'Tiang infus dan pagar tempat tidur adalah lingkungan sekitar pasien, sehingga berlaku momen 5 walau pasien tidak disentuh. Momen 4 hanya berlaku bila perawat menyentuh pasien.'],
                'remedial' => ['mudah', 'Perawat akan mengganti balutan luka operasi pasien. Kebersihan tangan tepat sebelum tindakan ini termasuk momen ...', [
                    'Momen 4: setelah kontak dengan pasien',
                    'Momen 3: setelah terpapar cairan tubuh pasien',
                    'Momen 5: setelah kontak dengan lingkungan sekitar pasien',
                    'Momen 2: sebelum tindakan aseptik',
                ], 3, 'Merawat luka adalah tindakan aseptik sehingga kebersihan tangan sebelumnya termasuk momen 2. Momen 3 baru berlaku setelah perawat terpapar cairan luka, bukan sebelum tindakan.'],
            ],
        ],
        [
            'indicator' => 'Menjelaskan kebersihan tangan saat menggunakan sarung tangan',
            'section' => 'Sarung Tangan Tidak Menggantikan Kebersihan Tangan',
            'reinforcement' => 'Sarung tangan tidak menggantikan kebersihan tangan. Lakukan kebersihan tangan sebelum memakai sarung tangan dan segera setelah melepasnya, karena sarung tangan dapat bocor dan tangan dapat terkontaminasi saat melepasnya.',
            'questions' => [
                'utama' => ['mudah', 'Perawat baru saja melepas sarung tangan setelah mengganti balutan luka. Tindakan berikutnya yang benar adalah ...', [
                    'Langsung menulis catatan keperawatan',
                    'Segera melakukan kebersihan tangan',
                    'Memakai sarung tangan baru tanpa kebersihan tangan',
                    'Menunggu sampai tangan terlihat kotor',
                ], 1, 'Kebersihan tangan harus dilakukan segera setelah melepas sarung tangan. Menunggu tangan tampak kotor salah karena mikroorganisme tidak terlihat dengan mata.'],
                'penguatan' => ['sedang', 'Alasan kebersihan tangan tetap wajib dilakukan setelah melepas sarung tangan adalah ...', [
                    'Agar sarung tangan dapat dipakai ulang',
                    'Karena sarung tangan membuat tangan berkeringat saja',
                    'Karena sarung tangan dapat bocor dan tangan dapat terkontaminasi saat melepasnya',
                    'Karena aturan rumah sakit tanpa alasan klinis',
                ], 2, 'Sarung tangan dapat memiliki kebocoran kecil dan permukaan luarnya terkontaminasi, sehingga tangan bisa tercemar saat melepasnya. Keringat bukan alasan utama, sehingga pilihan B kurang tepat.'],
                'remedial' => ['sedang', 'Perawat mengambil sarung tangan dari kotak dan langsung memakainya untuk mengambil darah pasien tanpa membersihkan tangan terlebih dahulu. Kesalahan tindakan ini adalah ...', [
                    'Tidak melakukan kebersihan tangan sebelum memakai sarung tangan',
                    'Seharusnya tidak memakai sarung tangan saat mengambil darah',
                    'Seharusnya memakai dua lapis sarung tangan',
                    'Tidak ada kesalahan karena sarung tangan sudah melindungi pasien',
                ], 0, 'Kebersihan tangan wajib dilakukan sebelum memakai sarung tangan agar kuman dari tangan tidak mencemari sarung tangan. Pilihan D keliru karena sarung tangan tidak menggantikan kebersihan tangan.'],
            ],
        ],
    ],
    'Enam Langkah dan Studi Kasus Kepatuhan' => [
        [
            'indicator' => 'Mengurutkan enam langkah kebersihan tangan WHO',
            'section' => 'Enam Langkah WHO',
            'reinforcement' => 'Urutan enam langkah: (1) gosok kedua telapak tangan, (2) punggung tangan dan sela jari bergantian, (3) telapak dan sela jari dengan jari saling mengait, (4) punggung jari dengan jari saling mengunci, (5) ibu jari berputar dalam genggaman, (6) ujung jari memutar pada telapak tangan.',
            'questions' => [
                'utama' => ['mudah', 'Langkah kelima dari enam langkah kebersihan tangan WHO adalah ...', [
                    'Menggosok punggung jari dengan posisi jari saling mengunci',
                    'Menggosok kedua telapak tangan',
                    'Menggosok ujung jari pada telapak tangan dengan gerakan memutar',
                    'Menggosok ibu jari secara berputar dalam genggaman tangan lainnya',
                ], 3, 'Langkah 5 adalah menggosok ibu jari secara berputar dalam genggaman tangan lainnya. Menggosok punggung jari dengan jari saling mengunci adalah langkah 4, sehingga pilihan A tertukar urutannya.'],
                'penguatan' => ['sedang', 'Perawat sudah menggosok kedua telapak tangan, lalu punggung tangan dan sela-sela jari bergantian. Langkah yang harus dilakukan berikutnya adalah ...', [
                    'Menggosok ibu jari secara berputar',
                    'Menggosok telapak tangan dan sela-sela jari dengan jari saling mengait',
                    'Menggosok ujung jari pada telapak tangan',
                    'Membilas tangan dengan air',
                ], 1, 'Setelah langkah 1 dan 2, langkah 3 adalah menggosok telapak dan sela jari dengan jari saling mengait. Menggosok ibu jari adalah langkah 5, sehingga pilihan A melompati dua langkah.'],
                'remedial' => ['mudah', 'Gerakan menggosok punggung jari dengan posisi jari kedua tangan saling mengunci merupakan langkah ke- ...', [
                    '2',
                    '3',
                    '4',
                    '6',
                ], 2, 'Punggung jari digosok dengan jari saling mengunci pada langkah 4. Langkah 3 juga melibatkan jari, tetapi yang digosok adalah telapak dan sela jari dengan jari saling mengait.'],
            ],
        ],
        [
            'indicator' => 'Menilai kelengkapan pelaksanaan enam langkah',
            'section' => 'Enam Langkah WHO',
            'reinforcement' => 'Setiap langkah membersihkan area tangan yang berbeda, sehingga melewatkan satu langkah berarti ada area yang tidak bersih. Area yang paling sering terlewat adalah sela jari, ibu jari, dan ujung jari. Keenam langkah dilakukan lengkap dalam durasi handrub 20–30 detik atau handwash 40–60 detik.',
            'questions' => [
                'utama' => ['sedang', 'Saat observasi, perawat hanya menggosok telapak tangan dan punggung tangan, lalu selesai. Area tangan yang berisiko tidak bersih adalah ...', [
                    'Sela jari, ibu jari, dan ujung jari',
                    'Hanya telapak tangan',
                    'Hanya punggung tangan',
                    'Tidak ada, karena telapak dan punggung sudah mewakili seluruh tangan',
                ], 0, 'Enam langkah dirancang agar sela jari, punggung jari, ibu jari, dan ujung jari ikut tergosok. Telapak dan punggung tangan sudah digosok dalam kasus ini, jadi pilihan B dan C salah, dan pilihan D keliru karena area lain terlewat.'],
                'penguatan' => ['sulit', 'Perawat melakukan handrub dengan enam langkah lengkap, tetapi seluruhnya selesai dalam 8 detik. Penilaian yang paling tepat adalah ...', [
                    'Sudah benar karena keenam langkah sudah dilakukan',
                    'Sudah benar asalkan cairan yang dipakai banyak',
                    'Belum benar karena handrub harus diganti handwash',
                    'Belum benar karena enam langkah handrub dilakukan selama 20–30 detik',
                ], 3, 'Kelengkapan langkah harus disertai durasi yang cukup, yaitu 20–30 detik untuk handrub, agar cairan bekerja di seluruh permukaan tangan. Pilihan A tampak benar, tetapi gerakan yang terlalu cepat membuat gosokan tidak efektif.'],
                'remedial' => ['mudah', 'Mahasiswa selalu lupa melakukan langkah keenam. Area tangan yang paling mungkin tidak bersih adalah ...', [
                    'Punggung tangan',
                    'Pergelangan tangan bagian luar',
                    'Ujung jari dan area kuku',
                    'Telapak tangan bagian tengah',
                ], 2, 'Langkah 6 menggosok ujung jari pada telapak tangan dengan gerakan memutar, sehingga ujung jari dan area kuku akan terlewat bila langkah ini tidak dilakukan. Punggung tangan sudah dibersihkan pada langkah 2.'],
            ],
        ],
        [
            'indicator' => 'Menganalisis kepatuhan kebersihan tangan pada studi kasus',
            'section' => 'Studi Kasus',
            'reinforcement' => 'Analisis kepatuhan dilakukan dengan mengikuti urutan kegiatan petugas lalu mencocokkannya dengan lima momen untuk menemukan momen yang terlewat. Hasil audit ditindaklanjuti dengan edukasi dan umpan balik kepada petugas agar kepatuhan meningkat.',
            'questions' => [
                'utama' => ['sedang', 'Saat audit, perawat C mengambil sampel darah pasien, melepas sarung tangan, lalu langsung menulis di rekam medis tanpa kebersihan tangan. Momen yang terlewat adalah ...', [
                    'Momen 1: sebelum kontak dengan pasien',
                    'Momen 3: setelah terpapar cairan tubuh pasien',
                    'Momen 2: sebelum tindakan aseptik',
                    'Tidak ada, karena perawat sudah memakai sarung tangan',
                ], 1, 'Setelah mengambil darah dan melepas sarung tangan, perawat wajib melakukan kebersihan tangan momen 3. Pilihan D keliru karena sarung tangan tidak menggantikan kebersihan tangan.'],
                'penguatan' => ['sedang', 'Hasil audit menunjukkan beberapa perawat sering melewatkan momen 2 dan momen 5. Tindak lanjut yang paling tepat adalah ...', [
                    'Memberi sanksi tanpa penjelasan',
                    'Mengurangi jumlah dispenser handrub',
                    'Memberikan edukasi dan umpan balik hasil audit kepada perawat',
                    'Menghentikan audit karena hasilnya kurang baik',
                ], 2, 'Studi kasus menekankan edukasi dan umpan balik audit untuk meningkatkan kepatuhan. Sanksi tanpa penjelasan tidak membantu perawat memahami momen yang terlewat, sehingga pilihan A kurang tepat.'],
                'remedial' => ['mudah', 'Perawat D selesai menelepon di nurse station, lalu langsung membantu pasien duduk tanpa kebersihan tangan. Momen yang terlewat adalah ...', [
                    'Momen 1: sebelum kontak dengan pasien',
                    'Momen 4: setelah kontak dengan pasien',
                    'Momen 3: setelah terpapar cairan tubuh pasien',
                    'Momen 5: setelah kontak dengan lingkungan sekitar pasien',
                ], 0, 'Membantu pasien duduk adalah kontak langsung dengan pasien, sehingga kebersihan tangan harus dilakukan sebelumnya (momen 1). Momen 4 berlaku setelah kontak, bukan sebelum kontak.'],
            ],
        ],
    ],
];
