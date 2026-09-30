<?php

/**
 * Latihan per materi topik PPI-03: 3 indikator × (utama, penguatan, remedial).
 * Format soal: [tingkat, teks soal, pilihan A–D, indeks kunci 0–3, pembahasan].
 */
return [
    'Jenis dan Pemilihan APD' => [
        [
            'indicator' => 'Menjelaskan pengertian dan tujuan APD',
            'section' => 'Pengertian',
            'reinforcement' => 'APD adalah perlengkapan yang menjadi penghalang antara petugas dan sumber infeksi. Sasarannya adalah melindungi kulit dan selaput lendir (mata, hidung, mulut) dari paparan darah, cairan tubuh, dan agen infeksius. Karena itu setiap bagian tubuh yang berisiko terpapar memerlukan APD yang sesuai.',
            'questions' => [
                'utama' => ['mudah', 'Bagian tubuh petugas yang terutama dilindungi oleh APD dari paparan darah dan cairan tubuh adalah ...', [
                    'Tulang dan sendi',
                    'Organ dalam seperti hati dan ginjal',
                    'Kulit dan selaput lendir (mata, hidung, mulut)',
                    'Rambut dan kuku saja',
                ], 2, 'APD melindungi kulit dan selaput lendir karena keduanya menjadi pintu masuk agen infeksius dari darah dan cairan tubuh. Rambut memang dapat ditutup penutup kepala, tetapi bukan sasaran utama perlindungan APD.'],
                'penguatan' => ['sedang', 'Seorang mahasiswa berpendapat, "Selama saya memakai sarung tangan, saya sudah terlindung dari percikan darah ke mata." Tanggapan yang paling tepat adalah ...', [
                    'Benar, karena sarung tangan sudah cukup untuk semua tindakan',
                    'Benar, karena mata jarang terkena percikan',
                    'Keliru, karena sarung tangan hanya diperlukan saat tindakan steril',
                    'Keliru, karena sarung tangan hanya melindungi tangan; selaput lendir mata memerlukan goggle atau face shield',
                ], 3, 'Setiap jenis APD melindungi bagian tubuh tertentu, sehingga selaput lendir mata yang berisiko terkena percikan perlu goggle atau face shield. Pilihan bahwa sarung tangan hanya untuk tindakan steril juga keliru karena sarung tangan non-steril dipakai untuk pemeriksaan.'],
                'remedial' => ['mudah', 'Contoh paparan yang dicegah dengan penggunaan APD adalah ...', [
                    'Kelelahan akibat jam kerja yang panjang',
                    'Percikan darah pasien ke mata perawat',
                    'Kebisingan di ruang perawatan',
                    'Kesalahan dalam pencatatan rekam medis',
                ], 1, 'APD mencegah kontak darah, cairan tubuh, dan agen infeksius dengan kulit serta selaput lendir, termasuk percikan ke mata. Kelelahan kerja adalah masalah keselamatan kerja lain yang tidak dicegah oleh APD.'],
            ],
        ],
        [
            'indicator' => 'Mengidentifikasi fungsi setiap jenis APD',
            'section' => 'Jenis APD',
            'reinforcement' => 'Sarung tangan steril dipakai untuk tindakan aseptik, sedangkan non-steril untuk pemeriksaan. Masker bedah melindungi dari droplet, respirator N95 dari penularan udara, goggle/face shield dari percikan ke mata dan wajah, serta gaun/apron melindungi kulit dan pakaian.',
            'questions' => [
                'utama' => ['mudah', 'Sarung tangan non-steril tepat digunakan pada tindakan ...', [
                    'Operasi atau pembedahan',
                    'Pemasangan kateter urin',
                    'Perawatan luka bakar luas secara aseptik',
                    'Pemeriksaan pasien yang berisiko kontak dengan cairan tubuh',
                ], 3, 'Sarung tangan non-steril digunakan untuk pemeriksaan, sedangkan tindakan aseptik seperti operasi dan pemasangan kateter urin memerlukan sarung tangan steril. Pemasangan kateter tampak sederhana, tetapi masuk ke saluran kemih yang steril sehingga harus steril.'],
                'penguatan' => ['sedang', 'Perawat akan membersihkan muntahan pasien dalam jumlah banyak yang berisiko membasahi seragamnya. Selain sarung tangan, APD yang melindungi kulit dan pakaiannya adalah ...', [
                    'Masker bedah',
                    'Penutup kepala',
                    'Gaun atau apron',
                    'Respirator N95',
                ], 2, 'Gaun atau apron berfungsi melindungi kulit dan pakaian dari darah serta cairan tubuh. Masker bedah melindungi saluran napas dari droplet, bukan melindungi pakaian.'],
                'remedial' => ['mudah', 'Pasangan jenis APD dan fungsinya yang tepat adalah ...', [
                    'Goggle – melindungi pakaian dari cairan tubuh',
                    'Respirator N95 – melindungi dari penularan melalui udara',
                    'Gaun – melindungi mata dari percikan',
                    'Masker bedah – melindungi dari partikel airborne seperti kuman TB',
                ], 1, 'Respirator N95 dipakai untuk penyakit yang menular melalui udara. Masker bedah hanya melindungi dari droplet, sehingga tidak memadai untuk partikel airborne seperti kuman TB.'],
            ],
        ],
        [
            'indicator' => 'Menentukan APD berdasarkan penilaian risiko',
            'section' => 'Prinsip Pemilihan',
            'reinforcement' => 'APD tidak dipakai seragam untuk semua tindakan. Pemilihannya didasarkan pada penilaian risiko: jenis tindakan, kemungkinan paparan darah/cairan tubuh, dan cara penularan penyakit pasien. Tindakan tanpa risiko paparan tidak memerlukan APD tambahan, sedangkan tindakan berisiko tinggi memerlukan APD lebih lengkap.',
            'questions' => [
                'utama' => ['mudah', 'Tiga hal yang dipertimbangkan dalam penilaian risiko untuk memilih APD adalah ...', [
                    'Jabatan petugas, lama shift, dan jumlah stok APD',
                    'Usia pasien, kelas perawatan, dan biaya APD',
                    'Jenis tindakan, kemungkinan paparan darah/cairan tubuh, dan cara penularan penyakit pasien',
                    'Diagnosis dokter, jam kunjungan, dan kenyamanan petugas',
                ], 2, 'Penilaian risiko menilai tindakan apa yang dilakukan, seberapa mungkin terjadi paparan darah/cairan tubuh, dan bagaimana penyakit pasien menular. Jumlah stok dan jabatan petugas tidak menentukan besarnya risiko paparan.'],
                'penguatan' => ['sedang', 'Perawat akan mengukur tekanan darah pasien yang sadar, kulitnya utuh, dan tidak menderita penyakit menular. Berdasarkan penilaian risiko, keputusan yang tepat adalah ...', [
                    'Tidak perlu APD khusus, cukup melakukan kebersihan tangan karena tidak ada risiko paparan darah/cairan tubuh',
                    'Memakai APD lengkap (gaun, respirator N95, goggle, sarung tangan) untuk semua pasien',
                    'Memakai sarung tangan steril',
                    'Memakai respirator N95 dan face shield',
                ], 0, 'Tanpa risiko paparan darah atau cairan tubuh dan tanpa penyakit menular, APD tidak diperlukan; kebersihan tangan tetap wajib. Memakai APD lengkap untuk semua pasien tidak sesuai penilaian risiko dan justru memboroskan APD.'],
                'remedial' => ['mudah', 'Mengapa APD yang dipakai perawat tidak selalu sama untuk setiap tindakan?', [
                    'Karena stok APD di ruangan selalu terbatas',
                    'Karena bergantung pada kenyamanan masing-masing petugas',
                    'Karena APD lengkap hanya boleh dipakai dokter',
                    'Karena risiko paparan setiap tindakan dan cara penularan penyakit pasien berbeda-beda',
                ], 3, 'APD dipilih melalui penilaian risiko, sehingga jenisnya menyesuaikan tindakan dan cara penularan penyakit. Kenyamanan petugas bukan dasar pemilihan APD.'],
            ],
        ],
    ],
    'Urutan Memakai dan Melepas APD' => [
        [
            'indicator' => 'Mengurutkan langkah memakai APD (donning)',
            'section' => 'Memakai APD (Donning)',
            'reinforcement' => 'Urutan memakai APD: kebersihan tangan, gaun, masker atau respirator, goggle atau face shield, lalu sarung tangan. Sarung tangan dipakai paling akhir agar menutupi ujung lengan gaun.',
            'questions' => [
                'utama' => ['mudah', 'Dalam urutan memakai APD, APD yang dipakai paling akhir adalah ...', [
                    'Gaun',
                    'Masker atau respirator',
                    'Sarung tangan',
                    'Goggle atau face shield',
                ], 2, 'Sarung tangan dipakai paling akhir agar dapat menutupi ujung lengan gaun. Goggle atau face shield dipakai sebelum sarung tangan, bukan terakhir.'],
                'penguatan' => ['sedang', 'Perawat sudah melakukan kebersihan tangan dan memakai gaun. Langkah berikutnya yang benar adalah memakai ...', [
                    'Sarung tangan',
                    'Goggle atau face shield',
                    'Penutup kepala',
                    'Masker atau respirator',
                ], 3, 'Setelah gaun, urutan berikutnya adalah masker atau respirator, kemudian goggle/face shield, dan terakhir sarung tangan. Goggle dipakai setelah masker agar dapat terpasang dengan baik di atasnya.'],
                'remedial' => ['mudah', 'Alasan sarung tangan dipakai setelah gaun adalah ...', [
                    'Agar sarung tangan dapat menutupi ujung lengan gaun',
                    'Agar sarung tangan tidak cepat robek',
                    'Agar lebih hemat sarung tangan',
                    'Karena sarung tangan harus dipakai sebelum kebersihan tangan',
                ], 0, 'Sarung tangan yang menutupi ujung lengan gaun mencegah pergelangan tangan terpapar. Kebersihan tangan justru dilakukan pertama, sebelum memakai APD apa pun.'],
            ],
        ],
        [
            'indicator' => 'Mengurutkan langkah melepas APD (doffing)',
            'section' => 'Melepas APD (Doffing)',
            'reinforcement' => 'Urutan melepas APD: sarung tangan, goggle atau face shield, gaun, masker atau respirator, lalu kebersihan tangan. Masker atau respirator dilepas terakhir di luar ruang pasien agar saluran napas tetap terlindung selama berada di area terkontaminasi.',
            'questions' => [
                'utama' => ['mudah', 'APD yang dilepas pertama kali saat doffing adalah ...', [
                    'Masker atau respirator',
                    'Sarung tangan',
                    'Gaun',
                    'Goggle atau face shield',
                ], 1, 'Sarung tangan paling terkontaminasi sehingga dilepas pertama. Masker atau respirator justru dilepas paling akhir, di luar ruang pasien.'],
                'penguatan' => ['sedang', 'Perawat telah melepas sarung tangan, lalu goggle. Langkah berikutnya yang benar adalah ...', [
                    'Melepas masker atau respirator',
                    'Melakukan kebersihan tangan lalu selesai',
                    'Melepas gaun',
                    'Memakai sarung tangan baru',
                ], 2, 'Setelah sarung tangan dan goggle/face shield, gaun dilepas, kemudian masker atau respirator, lalu kebersihan tangan. Melepas masker sebelum gaun keliru karena masker harus dilepas terakhir.'],
                'remedial' => ['sedang', 'Perawat melepas respirator N95 di dalam ruang isolasi pasien TB sebelum melepas gaunnya. Kesalahan tindakan ini adalah ...', [
                    'Tidak ada kesalahan asalkan respirator langsung dibuang',
                    'Gaun seharusnya dilepas paling akhir',
                    'Respirator seharusnya dilepas pertama kali',
                    'Respirator seharusnya dilepas terakhir, di luar ruang pasien, agar saluran napas tetap terlindung',
                ], 3, 'Di ruang pasien TB udara masih mengandung kuman, sehingga respirator dilepas terakhir setelah keluar ruangan. Gaun tidak dilepas paling akhir; urutannya gaun lalu masker/respirator.'],
            ],
        ],
        [
            'indicator' => 'Menerapkan prinsip mencegah kontaminasi diri saat melepas APD',
            'section' => 'Melepas APD (Doffing)',
            'reinforcement' => 'Bagian luar APD yang sudah dipakai dianggap terkontaminasi, sehingga tidak boleh disentuh dengan tangan tanpa pelindung. Lepaskan APD dengan memegang bagian yang lebih bersih (tali atau bagian belakang) dan akhiri dengan kebersihan tangan.',
            'questions' => [
                'utama' => ['mudah', 'Langkah yang wajib dilakukan setelah seluruh APD dilepas adalah ...', [
                    'Memakai masker baru',
                    'Mencatat tindakan di rekam medis',
                    'Melakukan kebersihan tangan',
                    'Mencuci sarung tangan bekas untuk dipakai lagi',
                ], 2, 'Tangan dapat terkontaminasi saat melepas APD, sehingga doffing selalu diakhiri dengan kebersihan tangan. Mencatat di rekam medis boleh dilakukan setelah tangan bersih, bukan menggantikannya.'],
                'penguatan' => ['sulit', 'Saat melepas face shield setelah merawat pasien, cara yang sesuai prinsip pencegahan kontaminasi diri adalah ...', [
                    'Memegang tali atau bagian belakang kepala, lalu mengangkatnya menjauhi wajah',
                    'Memegang bagian depan pelindung karena paling mudah dijangkau',
                    'Mengusap bagian depan pelindung dengan tangan sebelum dilepas',
                    'Melepasnya bersamaan dengan masker agar lebih cepat',
                ], 0, 'Bagian depan face shield dianggap terkontaminasi, sehingga yang dipegang adalah tali atau bagian belakangnya. Memegang bagian depan memang praktis, tetapi memindahkan mikroorganisme ke tangan.'],
                'remedial' => ['sedang', 'Saat melepas gaun, perawat menggulungnya sehingga bagian luar gaun berada di dalam gulungan. Tujuan tindakan ini adalah ...', [
                    'Agar gaun dapat dipakai kembali',
                    'Agar bagian luar yang terkontaminasi tidak menyentuh tubuh, pakaian, dan lingkungan',
                    'Agar gaun lebih cepat kering',
                    'Agar tempat sampah tidak cepat penuh',
                ], 1, 'Bagian luar gaun dianggap terkontaminasi, sehingga digulung ke dalam agar tidak mencemari tubuh dan sekitarnya. Menghemat tempat sampah bukan tujuan utama; yang utama adalah mencegah kontaminasi.'],
            ],
        ],
    ],
    'APD Sesuai Transmisi dan Studi Kasus' => [
        [
            'indicator' => 'Memilih APD sesuai jenis transmisi penyakit',
            'section' => 'APD Berdasarkan Transmisi',
            'reinforcement' => 'Kewaspadaan kontak (mis. MRSA) memerlukan sarung tangan dan gaun. Kewaspadaan droplet (mis. influenza) memerlukan masker bedah saat berada dalam jarak dekat. Kewaspadaan airborne (mis. TB paru, campak) memerlukan respirator N95 dan pasien dirawat di ruang bertekanan negatif.',
            'questions' => [
                'utama' => ['mudah', 'APD yang diperlukan pada kewaspadaan kontak, misalnya saat merawat pasien MRSA, adalah ...', [
                    'Respirator N95 saja',
                    'Masker bedah saja',
                    'Face shield saja',
                    'Sarung tangan dan gaun',
                ], 3, 'MRSA menular melalui kontak dengan pasien dan lingkungannya, sehingga tangan dan tubuh dilindungi dengan sarung tangan dan gaun. Respirator N95 diperlukan untuk penularan airborne, bukan kontak.'],
                'penguatan' => ['sedang', 'Perawat akan merawat anak yang menderita campak. APD pernapasan dan penempatan pasien yang tepat adalah ...', [
                    'Masker bedah dan pasien dirawat di ruang biasa bersama pasien lain',
                    'Respirator N95 dan pasien dirawat di ruang bertekanan negatif',
                    'Sarung tangan dan gaun saja',
                    'Masker kain dan pasien dirawat di ruang tunggu',
                ], 1, 'Campak menular melalui udara (airborne), sehingga petugas memakai respirator N95 dan pasien ditempatkan di ruang bertekanan negatif. Masker bedah hanya untuk droplet dan tidak menyaring partikel airborne.'],
                'remedial' => ['mudah', 'Pasangan jenis transmisi dan APD berikut yang tepat adalah ...', [
                    'Kontak – respirator N95 saja',
                    'Airborne – masker bedah sudah cukup',
                    'Droplet – masker bedah saat berada dalam jarak dekat dengan pasien',
                    'Kontak – face shield saja',
                ], 2, 'Penularan droplet dicegah dengan masker bedah saat berada dekat pasien. Airborne tidak cukup dengan masker bedah karena memerlukan respirator N95.'],
            ],
        ],
        [
            'indicator' => 'Melakukan seal check respirator N95',
            'section' => 'Seal Check Respirator',
            'reinforcement' => 'Seal check dilakukan setiap kali memakai respirator N95. Caranya, tutup permukaan respirator dengan kedua tangan, lalu tarik dan embuskan napas untuk memastikan tidak ada udara bocor di tepinya. Bila bocor, perbaiki posisi respirator dan ulangi sebelum masuk ruang pasien.',
            'questions' => [
                'utama' => ['mudah', 'Cara melakukan seal check respirator N95 yang benar adalah ...', [
                    'Menyemprotkan cairan berasa pahit di laboratorium lalu menilai apakah rasanya tercium',
                    'Menutup permukaan respirator dengan kedua tangan, lalu menarik dan mengembuskan napas untuk merasakan kebocoran di tepinya',
                    'Menarik tali karet sekencang mungkin tanpa perlu bernapas',
                    'Meniup bagian dalam respirator sebelum dipakai',
                ], 1, 'Seal check dilakukan dengan menutup respirator menggunakan kedua tangan sambil menarik dan mengembuskan napas untuk mendeteksi udara bocor di tepi. Penyemprotan cairan berasa pahit adalah bagian dari fit test berkala, bukan seal check.'],
                'penguatan' => ['sedang', 'Saat melakukan seal check, perawat merasakan udara keluar dari tepi respirator di sekitar hidung. Tindakan yang tepat adalah ...', [
                    'Tetap masuk ke ruang pasien karena kebocoran kecil tidak berpengaruh',
                    'Menambahkan masker bedah di atas respirator lalu masuk',
                    'Mengganti respirator dengan masker bedah',
                    'Memperbaiki posisi respirator dan menekan klip hidung, lalu mengulang seal check sebelum masuk ruang pasien',
                ], 3, 'Kebocoran di tepi membuat udara terhirup tanpa melalui filter, sehingga posisi respirator harus diperbaiki dan seal check diulang sampai tidak bocor. Menambah masker bedah di atasnya tidak menutup celah di tepi respirator.'],
                'remedial' => ['mudah', 'Tujuan dilakukannya seal check pada respirator N95 adalah ...', [
                    'Memastikan tidak ada udara bocor di tepi respirator sehingga udara terhirup melalui filter',
                    'Menguji kemampuan filter dalam membunuh virus',
                    'Memastikan respirator masih steril',
                    'Menentukan ukuran wajah untuk memesan respirator',
                ], 0, 'Seal check memastikan respirator menempel rapat sehingga udara yang dihirup tersaring. Respirator tidak membunuh virus dan tidak harus steril, jadi pilihan tersebut keliru.'],
            ],
        ],
        [
            'indicator' => 'Menganalisis kebutuhan APD pada tindakan berisiko percikan',
            'section' => 'Studi Kasus',
            'reinforcement' => 'Tindakan yang berisiko menimbulkan percikan, seperti suction terbuka, memerlukan perlindungan menyeluruh: sarung tangan, gaun, masker, dan pelindung mata/face shield. Analisis kebutuhan APD mempertimbangkan cara penularan penyakit sekaligus risiko percikan dari tindakan.',
            'questions' => [
                'utama' => ['sedang', 'Pada studi kasus suction terbuka pasien pneumonia, alasan perawat C perlu memakai pelindung mata/face shield adalah ...', [
                    'Agar pasien tidak melihat ekspresi wajah perawat',
                    'Untuk menggantikan fungsi masker',
                    'Karena tindakan berisiko menimbulkan percikan sekret ke selaput lendir mata',
                    'Karena pelindung mata wajib dipakai pada setiap tindakan keperawatan',
                ], 2, 'Suction terbuka dapat memercikkan sekret, sehingga mata perlu dilindungi goggle atau face shield. Pelindung mata tidak wajib untuk semua tindakan, melainkan dipilih bila ada risiko percikan.'],
                'penguatan' => ['sulit', 'Perawat akan mengirigasi luka bernanah pada pasien MRSA; cairan irigasi berisiko memercik ke wajah dan tubuh. APD yang paling tepat adalah ...', [
                    'Sarung tangan, gaun, masker, dan pelindung mata/face shield',
                    'Sarung tangan dan gaun saja',
                    'Respirator N95 dan sarung tangan',
                    'Masker bedah saja',
                ], 0, 'Kewaspadaan kontak MRSA memerlukan sarung tangan dan gaun, lalu risiko percikan menambah kebutuhan masker dan pelindung mata. Sarung tangan dan gaun saja belum melindungi mulut, hidung, dan mata dari percikan.'],
                'remedial' => ['sedang', 'Dalam kasus suction terbuka pasien pneumonia, perawat hanya memakai sarung tangan dan masker. APD yang masih kurang adalah ...', [
                    'Penutup kepala dan sepatu boot',
                    'Gaun dan pelindung mata/face shield',
                    'Sarung tangan steril rangkap dua',
                    'Tidak ada, APD sudah lengkap',
                ], 1, 'Sesuai studi kasus, suction terbuka memerlukan sarung tangan, gaun, masker, dan pelindung mata/face shield, sehingga gaun dan pelindung mata masih kurang. Sarung tangan rangkap tidak melindungi tubuh dan mata dari percikan.'],
            ],
        ],
    ],
];
