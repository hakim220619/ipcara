<?php

/**
 * Latihan per materi topik PPI-01: 3 indikator × (utama, penguatan, remedial).
 * Format soal: [tingkat, teks soal, pilihan A–D, indeks kunci 0–3, pembahasan].
 */
return [
    'Konsep Dasar PPI dan HAIs' => [
        [
            'indicator' => 'Menjelaskan pengertian PPI menurut Permenkes 27/2017',
            'section' => 'Pengertian PPI',
            'reinforcement' => 'PPI adalah upaya mencegah dan meminimalkan terjadinya infeksi, bukan upaya mengobati infeksi. Sasarannya luas: pasien, petugas, pengunjung, dan masyarakat sekitar fasilitas pelayanan kesehatan, sebagaimana diatur dalam Permenkes RI No. 27 Tahun 2017.',
            'questions' => [
                'utama' => ['mudah', 'Regulasi yang menjadi acuan pelaksanaan Pencegahan dan Pengendalian Infeksi di fasilitas pelayanan kesehatan di Indonesia adalah ...', [
                    'Undang-Undang Praktik Kedokteran',
                    'Permenkes RI No. 27 Tahun 2017',
                    'Peraturan Presiden tentang Jaminan Kesehatan Nasional',
                    'Keputusan Menteri tentang Standar Gizi Pasien',
                ], 1, 'Pedoman PPI di fasyankes diatur dalam Permenkes RI No. 27 Tahun 2017. Regulasi JKN mengatur pembiayaan layanan, bukan pencegahan infeksi.'],
                'penguatan' => ['sedang', 'Rumah sakit menyediakan cairan handrub di pintu masuk ruang rawat untuk digunakan oleh keluarga yang membesuk. Tindakan ini sesuai dengan pengertian PPI karena ...', [
                    'PPI hanya ditujukan untuk melindungi petugas kesehatan',
                    'PPI merupakan upaya pengobatan infeksi pada pengunjung',
                    'PPI juga bertujuan mencegah dan meminimalkan infeksi pada pengunjung',
                    'PPI hanya berlaku bagi pasien di ruang isolasi',
                ], 2, 'Menurut Permenkes 27/2017, PPI melindungi pasien, petugas, pengunjung, dan masyarakat sekitar fasyankes, sehingga pengunjung termasuk sasaran. Pilihan "hanya petugas" keliru karena mempersempit sasaran PPI.'],
                'remedial' => ['mudah', 'Pernyataan yang paling tepat tentang PPI adalah ...', [
                    'PPI adalah upaya mencegah dan meminimalkan infeksi, termasuk pada petugas kesehatan',
                    'PPI adalah upaya mengobati semua pasien yang terinfeksi',
                    'PPI hanya menjadi tanggung jawab dokter spesialis penyakit infeksi',
                    'PPI hanya diterapkan saat terjadi wabah',
                ], 0, 'Inti PPI adalah pencegahan dan meminimalkan infeksi bagi pasien, petugas, pengunjung, dan masyarakat, dan diterapkan setiap saat. Mengobati pasien terinfeksi adalah tata laksana klinis, bukan pengertian PPI.'],
            ],
        ],
        [
            'indicator' => 'Menentukan suatu infeksi termasuk HAIs berdasarkan kriteria waktu',
            'section' => 'Healthcare Associated Infections (HAIs)',
            'reinforcement' => 'HAIs adalah infeksi yang didapat selama perawatan di fasyankes, yang tidak ditemukan dan tidak dalam masa inkubasi saat pasien masuk. Patokan umumnya, infeksi yang muncul 48 jam atau lebih setelah pasien dirawat dikategorikan HAIs.',
            'questions' => [
                'utama' => ['mudah', 'Salah satu syarat suatu infeksi dikategorikan sebagai HAIs adalah infeksi tersebut ...', [
                    'Sudah dalam masa inkubasi saat pasien masuk rumah sakit',
                    'Didapat pasien dari lingkungan rumahnya',
                    'Ditemukan pada hari pertama pasien masuk',
                    'Tidak ditemukan dan tidak dalam masa inkubasi saat pasien masuk',
                ], 3, 'HAIs didapat selama perawatan, sehingga saat pasien masuk infeksi belum ada dan tidak sedang dalam masa inkubasi. Infeksi yang sudah dalam masa inkubasi saat masuk berasal dari luar fasyankes, bukan HAIs.'],
                'penguatan' => ['sedang', 'Ny. S datang ke IGD dengan demam, batuk berdahak, dan hasil rontgen menunjukkan pneumonia sejak sebelum masuk rumah sakit. Kondisi Ny. S paling tepat dikategorikan sebagai ...', [
                    'HAIs karena pasien sedang dirawat di rumah sakit',
                    'Bukan HAIs karena infeksi sudah ada saat pasien masuk',
                    'HAIs jenis VAP',
                    'HAIs karena pneumonia selalu didapat di rumah sakit',
                ], 1, 'Infeksi Ny. S sudah ada sebelum ia dirawat sehingga termasuk infeksi komunitas, bukan HAIs. Status "sedang dirawat" saja tidak cukup; yang menentukan adalah infeksi tidak ada saat masuk dan muncul selama perawatan.'],
                'remedial' => ['mudah', 'Tn. R masuk rumah sakit tanpa tanda infeksi. Tiga hari (72 jam) kemudian muncul demam dan tanda infeksi baru. Infeksi ini umumnya dikategorikan sebagai ...', [
                    'HAIs, karena muncul lebih dari 48 jam setelah dirawat',
                    'Infeksi komunitas, karena pasien berasal dari rumah',
                    'Bukan infeksi, karena demam adalah hal biasa',
                    'Infeksi bawaan, karena muncul setelah masuk',
                ], 0, 'Infeksi baru yang muncul ≥ 48 jam setelah dirawat pada pasien yang saat masuk tidak terinfeksi umumnya dikategorikan HAIs. Asal pasien dari rumah tidak menjadikannya infeksi komunitas bila infeksi baru timbul selama perawatan.'],
            ],
        ],
        [
            'indicator' => 'Mengidentifikasi jenis HAIs yang sering dipantau',
            'section' => 'Jenis HAIs yang sering dipantau',
            'reinforcement' => 'Empat jenis HAIs yang sering dipantau adalah VAP (terkait ventilator), IADP (terkait kateter intravaskular), ISK/CAUTI (terkait kateter urin), dan IDO (infeksi daerah operasi). Kaitkan setiap jenis HAIs dengan alat atau tindakan yang menjadi faktor risikonya.',
            'questions' => [
                'utama' => ['mudah', 'Infeksi Aliran Darah Primer (IADP) sebagai salah satu jenis HAIs berkaitan dengan penggunaan ...', [
                    'Ventilator mekanik',
                    'Kateter urin',
                    'Kateter intravaskular',
                    'Selang nasogastrik',
                ], 2, 'IADP adalah infeksi aliran darah primer yang terkait kateter intravaskular. Ventilator berkaitan dengan VAP, sedangkan kateter urin berkaitan dengan ISK (CAUTI).'],
                'penguatan' => ['sedang', 'Pasien di ICU terpasang ventilator mekanik sejak 5 hari lalu. Kini sekret trakea menjadi purulen, pasien demam, dan tampak infiltrat baru pada rontgen dada. Jenis HAIs yang paling mungkin adalah ...', [
                    'IDO',
                    'VAP',
                    'IADP',
                    'ISK terkait kateter',
                ], 1, 'Pneumonia yang muncul pada pasien dengan ventilator mekanik termasuk VAP. IADP kurang tepat karena tanda yang muncul adalah infeksi paru, bukan infeksi aliran darah terkait kateter intravaskular.'],
                'remedial' => ['mudah', 'Pasien terpasang kateter vena sentral selama 6 hari, kemudian demam dan hasil kultur darah positif tanpa sumber infeksi lain. Jenis HAIs yang paling tepat adalah ...', [
                    'ISK terkait kateter',
                    'IDO',
                    'VAP',
                    'IADP',
                ], 3, 'Kateter vena sentral adalah kateter intravaskular, sehingga infeksi aliran darah yang terkait dengannya disebut IADP. ISK terkait kateter keliru karena berhubungan dengan kateter urin, bukan kateter pembuluh darah.'],
            ],
        ],
    ],
    'Rantai Penularan Infeksi' => [
        [
            'indicator' => 'Mengidentifikasi komponen reservoir, pintu keluar, dan pintu masuk dalam rantai penularan',
            'section' => 'Enam Komponen Rantai Penularan',
            'reinforcement' => 'Reservoir adalah tempat agen hidup dan berkembang biak (manusia, alat, air, lingkungan). Pintu keluar adalah jalan agen meninggalkan reservoir (saluran napas, darah, luka, feses), sedangkan pintu masuk adalah jalan agen memasuki pejamu (luka, selaput lendir, alat invasif).',
            'questions' => [
                'utama' => ['mudah', 'Lokasi tusukan kanula infus pada kulit pasien dalam rantai penularan infeksi berperan sebagai ...', [
                    'Pintu masuk',
                    'Reservoir',
                    'Agen infeksi',
                    'Pintu keluar',
                ], 0, 'Kanula infus adalah alat invasif yang membuka jalan bagi mikroorganisme masuk ke tubuh pejamu, sehingga berperan sebagai pintu masuk. Pintu keluar keliru karena istilah itu merujuk pada jalan agen meninggalkan reservoir.'],
                'penguatan' => ['sedang', 'Hasil investigasi menemukan bakteri Pseudomonas tumbuh di air humidifier yang tidak diganti secara rutin. Dalam rantai penularan infeksi, air humidifier tersebut berperan sebagai ...', [
                    'Pejamu rentan',
                    'Pintu masuk',
                    'Reservoir',
                    'Cara penularan',
                ], 2, 'Air humidifier menjadi tempat bakteri hidup dan berkembang biak, sehingga berperan sebagai reservoir. Cara penularan kurang tepat karena itu adalah mekanisme perpindahan agen, bukan tempat agen tumbuh.'],
                'remedial' => ['mudah', 'Pada pasien hepatitis B, darah yang keluar dari luka merupakan jalan virus meninggalkan tubuh pasien. Komponen rantai penularan ini disebut ...', [
                    'Pintu masuk',
                    'Pintu keluar',
                    'Pejamu rentan',
                    'Agen infeksi',
                ], 1, 'Darah dari luka adalah jalan agen meninggalkan reservoir (pasien), sehingga disebut pintu keluar. Agen infeksi keliru karena yang dimaksud agen adalah virus hepatitis B itu sendiri, bukan darahnya.'],
            ],
        ],
        [
            'indicator' => 'Menjelaskan jenis-jenis cara penularan infeksi',
            'section' => 'Enam Komponen Rantai Penularan',
            'reinforcement' => 'Cara penularan adalah mekanisme berpindahnya agen dari reservoir ke pejamu, meliputi kontak, droplet, udara (airborne), vehikulum, dan vektor. Vehikulum adalah perantara benda seperti makanan, air, atau obat yang tercemar, sedangkan vektor adalah perantara makhluk hidup seperti nyamuk atau lalat.',
            'questions' => [
                'utama' => ['mudah', 'Berikut ini yang seluruhnya termasuk cara penularan infeksi adalah ...', [
                    'Bakteri, virus, jamur, dan parasit',
                    'Luka, selaput lendir, dan alat invasif',
                    'Lansia, bayi, dan pasien imunokompromais',
                    'Kontak, droplet, udara (airborne), vehikulum, dan vektor',
                ], 3, 'Cara penularan meliputi kontak, droplet, udara, vehikulum, dan vektor. Bakteri, virus, jamur, dan parasit adalah contoh agen infeksi, bukan cara penularan.'],
                'penguatan' => ['sedang', 'Beberapa pasien mengalami diare setelah mengonsumsi makanan dari dapur rumah sakit yang tercemar bakteri. Cara penularan pada kejadian ini adalah ...', [
                    'Vehikulum',
                    'Vektor',
                    'Droplet',
                    'Udara (airborne)',
                ], 0, 'Makanan yang tercemar adalah benda perantara, sehingga penularannya melalui vehikulum. Vektor keliru karena vektor adalah perantara makhluk hidup seperti nyamuk atau lalat.'],
                'remedial' => ['mudah', 'Penularan demam berdarah dengue melalui gigitan nyamuk Aedes aegypti merupakan contoh cara penularan ...', [
                    'Kontak',
                    'Vehikulum',
                    'Vektor',
                    'Droplet',
                ], 2, 'Nyamuk adalah makhluk hidup yang membawa agen dari satu orang ke orang lain, sehingga termasuk penularan melalui vektor. Vehikulum keliru karena perantaranya berupa benda tak hidup seperti makanan atau air.'],
            ],
        ],
        [
            'indicator' => 'Menentukan upaya pemutusan rantai penularan infeksi',
            'section' => 'Memutus Rantai Penularan',
            'reinforcement' => 'Infeksi dapat dicegah dengan memutus minimal satu mata rantai penularan. Kebersihan tangan dan APD memutus cara penularan, sterilisasi alat menghilangkan agen infeksi, dan imunisasi melindungi pejamu rentan.',
            'questions' => [
                'utama' => ['mudah', 'Agar infeksi dapat dicegah, jumlah minimal mata rantai penularan yang perlu diputus adalah ...', [
                    'Seluruh enam mata rantai',
                    'Satu mata rantai',
                    'Tiga mata rantai',
                    'Lima mata rantai',
                ], 1, 'Rantai penularan hanya berjalan bila semua komponen tersambung, sehingga memutus satu mata rantai saja sudah dapat mencegah infeksi. Memutus seluruh enam mata rantai bukan syarat, meskipun upaya berlapis tetap dianjurkan.'],
                'penguatan' => ['sedang', 'Instrumen bedah yang telah dipakai dibersihkan lalu disterilkan sebelum digunakan pada pasien berikutnya. Komponen rantai penularan yang diputus melalui tindakan ini adalah ...', [
                    'Pejamu rentan',
                    'Pintu keluar',
                    'Agen infeksi',
                    'Pintu masuk',
                ], 2, 'Sterilisasi membunuh seluruh mikroorganisme pada alat, sehingga menghilangkan agen infeksi. Pintu masuk kurang tepat karena luka operasi tetap ada; yang dihilangkan adalah mikroorganismenya.'],
                'remedial' => ['mudah', 'Perawat memakai masker dan sarung tangan saat merawat pasien. Penggunaan APD ini memutus rantai penularan pada komponen ...', [
                    'Cara penularan',
                    'Agen infeksi',
                    'Reservoir',
                    'Pejamu rentan melalui peningkatan kekebalan',
                ], 0, 'APD menjadi penghalang perpindahan mikroorganisme dari pasien ke petugas atau sebaliknya, sehingga memutus cara penularan. APD tidak meningkatkan kekebalan tubuh; itu adalah peran imunisasi pada komponen pejamu rentan.'],
            ],
        ],
    ],
    'Kewaspadaan Standar dan Berbasis Transmisi' => [
        [
            'indicator' => 'Mengidentifikasi komponen kewaspadaan standar dalam praktik keperawatan',
            'section' => 'Kewaspadaan Standar',
            'reinforcement' => 'Kewaspadaan standar terdiri atas sebelas komponen yang diterapkan pada semua pasien, antara lain kebersihan tangan, APD, penatalaksanaan linen, pengelolaan limbah, dan praktik menyuntik yang aman. Setiap tindakan keperawatan sehari-hari dapat dikaitkan dengan salah satu komponen tersebut.',
            'questions' => [
                'utama' => ['mudah', 'Jumlah komponen kewaspadaan standar menurut pedoman PPI adalah ...', [
                    'Lima komponen',
                    'Tujuh komponen',
                    'Sebelas komponen',
                    'Enam komponen',
                ], 2, 'Kewaspadaan standar memiliki sebelas komponen, mulai dari kebersihan tangan hingga praktik lumbal pungsi yang aman. Enam adalah jumlah komponen rantai penularan infeksi, bukan kewaspadaan standar.'],
                'penguatan' => ['sedang', 'Setelah menyuntikkan obat, seorang perawat menutup kembali jarum bekas dengan kedua tangan (recapping). Komponen kewaspadaan standar yang dilanggar adalah ...', [
                    'Penatalaksanaan linen',
                    'Penempatan pasien',
                    'Kebersihan pernapasan / etika batuk',
                    'Praktik menyuntik yang aman',
                ], 3, 'Menutup kembali jarum bekas dengan dua tangan berisiko menimbulkan luka tusuk jarum, sehingga melanggar praktik menyuntik yang aman. Penatalaksanaan linen tidak berkaitan karena mengatur penanganan kain/linen, bukan benda tajam.'],
                'remedial' => ['mudah', 'Perawat memasukkan sprei yang terkena darah pasien ke kantong khusus linen infeksius tanpa mengibaskannya. Tindakan ini merupakan penerapan komponen kewaspadaan standar ...', [
                    'Penatalaksanaan linen',
                    'Praktik lumbal pungsi yang aman',
                    'Dekontaminasi peralatan perawatan pasien',
                    'Penempatan pasien',
                ], 0, 'Penanganan sprei kotor termasuk komponen penatalaksanaan linen. Dekontaminasi peralatan kurang tepat karena komponen itu ditujukan untuk alat perawatan pasien, bukan linen.'],
            ],
        ],
        [
            'indicator' => 'Menentukan jenis kewaspadaan berbasis transmisi yang sesuai',
            'section' => 'Kewaspadaan Berbasis Transmisi',
            'reinforcement' => 'Kewaspadaan berbasis transmisi (kontak, droplet, dan udara/airborne) diterapkan sebagai tambahan, bukan pengganti, kewaspadaan standar pada pasien yang diketahui atau dicurigai terinfeksi. Pemilihannya disesuaikan dengan cara penularan penyakit.',
            'questions' => [
                'utama' => ['mudah', 'Kewaspadaan berbasis transmisi diterapkan dengan ketentuan ...', [
                    'Menggantikan kewaspadaan standar pada pasien infeksi',
                    'Hanya pada pasien yang sudah terbukti positif melalui laboratorium',
                    'Sebagai tambahan kewaspadaan standar pada pasien yang diketahui atau dicurigai terinfeksi',
                    'Pada semua pasien tanpa memandang diagnosis',
                ], 2, 'Kewaspadaan berbasis transmisi adalah lapisan tambahan di atas kewaspadaan standar dan sudah diterapkan sejak pasien dicurigai terinfeksi. Pilihan "menggantikan" keliru karena kewaspadaan standar tetap wajib bagi semua pasien.'],
                'penguatan' => ['sedang', 'Seorang pasien dirawat dengan tuberkulosis paru BTA positif. Selain kewaspadaan standar, jenis kewaspadaan berbasis transmisi yang perlu diterapkan adalah ...', [
                    'Kewaspadaan kontak',
                    'Kewaspadaan udara (airborne)',
                    'Kewaspadaan droplet',
                    'Tidak perlu kewaspadaan tambahan',
                ], 1, 'Mycobacterium tuberculosis menular melalui droplet nuclei yang melayang di udara, sehingga memerlukan kewaspadaan airborne. Kewaspadaan droplet tidak memadai karena partikel TB dapat bertahan lama di udara dan menempuh jarak jauh.'],
                'remedial' => ['mudah', 'Pasien dengan influenza menularkan virus melalui percikan saat batuk dan bersin dalam jarak dekat. Selain kewaspadaan standar, kewaspadaan yang diterapkan adalah ...', [
                    'Kewaspadaan kontak saja',
                    'Kewaspadaan udara (airborne)',
                    'Tidak perlu kewaspadaan tambahan',
                    'Kewaspadaan droplet',
                ], 3, 'Influenza terutama menular melalui percikan (droplet) berjarak dekat, sehingga diterapkan kewaspadaan droplet. Kewaspadaan airborne ditujukan untuk penyakit dengan partikel sangat kecil yang melayang lama di udara, seperti TB dan campak.'],
            ],
        ],
        [
            'indicator' => 'Menganalisis studi kasus HAIs dan kaitannya dengan kewaspadaan standar',
            'section' => 'Studi Kasus',
            'reinforcement' => 'Pada kasus Tn. A, infeksi dikategorikan HAIs karena tidak ada tanda infeksi saat masuk dan infeksi muncul pada hari ke-4 perawatan. Kateter urin menjadi pintu masuk agen, sehingga kebersihan tangan dan teknik aseptik saat merawat kateter perlu diperkuat.',
            'questions' => [
                'utama' => ['sedang', 'Pada studi kasus Tn. A (65 tahun, stroke, terpasang kateter urin), alasan utama kondisi demam dan kultur urin positif pada hari ke-4 dikategorikan sebagai HAIs adalah ...', [
                    'Pasien berusia lanjut',
                    'Pasien menderita stroke',
                    'Kultur urin menunjukkan hasil positif',
                    'Tidak ada tanda infeksi saat masuk dan infeksi muncul lebih dari 48 jam setelah dirawat',
                ], 3, 'Penentuan HAIs didasarkan pada waktu: infeksi tidak ada saat masuk dan muncul ≥ 48 jam selama perawatan. Kultur positif hanya membuktikan adanya infeksi, tetapi tidak menunjukkan infeksi itu didapat di rumah sakit.'],
                'penguatan' => ['sulit', 'Untuk mencegah kejadian serupa pada pasien lain yang terpasang kateter urin seperti Tn. A, komponen kewaspadaan standar yang paling perlu diperkuat oleh tim perawat adalah ...', [
                    'Kebersihan tangan sebelum dan sesudah perawatan kateter',
                    'Kebersihan pernapasan / etika batuk',
                    'Praktik lumbal pungsi yang aman',
                    'Penempatan pasien di ruang bertekanan negatif',
                ], 0, 'ISK terkait kateter umumnya terjadi akibat masuknya mikroorganisme melalui kateter, sering melalui tangan petugas, sehingga kebersihan tangan saat merawat kateter paling berperan. Ruang bertekanan negatif ditujukan untuk penyakit airborne, bukan pencegahan ISK.'],
                'remedial' => ['mudah', 'Dalam kasus Tn. A, kateter urin yang terpasang berperan dalam rantai penularan infeksi sebagai ...', [
                    'Agen infeksi',
                    'Pintu masuk',
                    'Pintu keluar',
                    'Cara penularan vektor',
                ], 1, 'Kateter urin adalah alat invasif yang membuka jalan bagi mikroorganisme masuk ke saluran kemih, sehingga berperan sebagai pintu masuk. Agen infeksi keliru karena agen adalah mikroorganismenya, bukan alatnya.'],
            ],
        ],
    ],
];
