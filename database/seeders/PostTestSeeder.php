<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Post-test: 25 soal (5 per topik) yang berbeda dari soal pre-test,
 * dikerjakan setelah mahasiswa menyelesaikan seluruh materi.
 */
class PostTestSeeder extends Seeder
{
    public function run(): void
    {
        if (Test::query()->where('kind', Test::KIND_POST_TEST)->exists()) {
            return;
        }

        $authorId = User::query()->where('role', 'admin')->value('id');

        $postTest = Test::create([
            'topic_id' => null,
            'kind' => Test::KIND_POST_TEST,
            'title' => 'Post-Test IPCARA',
            'description' => 'Tes akhir untuk mengukur peningkatan kemampuan Anda setelah mempelajari seluruh materi PPI.',
            'duration_minutes' => 60,
            'passing_score' => 70,
            'max_attempts' => 1,
            'created_by' => $authorId,
        ]);

        $order = 0;

        foreach ($this->questions() as $code => $questions) {
            $topicId = Topic::query()->where('code', $code)->value('id');

            if (! $topicId) {
                continue;
            }

            foreach ($questions as [$level, $text, $options, $answer, $explanation]) {
                $question = Question::create([
                    'topic_id' => $topicId,
                    'type' => 'pilihan_ganda',
                    'difficulty' => $level,
                    'question_text' => $text,
                    'explanation' => $explanation,
                    'points' => 1,
                    'created_by' => $authorId,
                ]);

                foreach ($options as $idx => $optionText) {
                    $question->options()->create([
                        'label' => chr(65 + $idx),
                        'option_text' => $optionText,
                        'is_correct' => $idx === $answer,
                        'order' => $idx + 1,
                    ]);
                }

                $postTest->questions()->attach($question->id, ['order' => ++$order]);
            }
        }

        $postTest->keepBalancedQuestions();
    }

    /**
     * Format: [tingkat, teks soal, pilihan A–D, indeks kunci, pembahasan].
     *
     * @return array<string, list<array{0: string, 1: string, 2: list<string>, 3: int, 4: string}>>
     */
    private function questions(): array
    {
        return [
            'PPI-01' => [
                ['mudah', 'Sasaran perlindungan program PPI di fasilitas pelayanan kesehatan adalah ...', [
                    'Pasien saja',
                    'Petugas kesehatan saja',
                    'Pasien, petugas, pengunjung, dan masyarakat sekitar fasyankes',
                    'Dokter dan perawat',
                ], 2, 'PPI melindungi pasien, petugas, pengunjung, dan masyarakat sekitar fasilitas pelayanan kesehatan.'],
                ['mudah', 'Singkatan IDO dalam surveilans HAIs adalah ...', [
                    'Infeksi Darah Operasi',
                    'Infeksi Daerah Operasi',
                    'Infeksi Dalam Organ',
                    'Infeksi Dini Operasi',
                ], 1, 'IDO adalah Infeksi Daerah Operasi, yaitu infeksi pada lokasi pembedahan.'],
                ['mudah', 'Mikroorganisme penyebab infeksi dalam rantai penularan disebut ...', [
                    'Reservoir',
                    'Pejamu rentan',
                    'Agen infeksi',
                    'Pintu keluar',
                ], 2, 'Agen infeksi adalah mikroorganisme (bakteri, virus, jamur, parasit) yang menyebabkan infeksi.'],
                ['sedang', 'Tempat mikroorganisme hidup, tumbuh, dan berkembang biak dalam rantai penularan infeksi disebut ...', [
                    'Reservoir',
                    'Pintu masuk',
                    'Cara transmisi',
                    'Pejamu rentan',
                ], 0, 'Reservoir adalah tempat agen infeksi hidup dan berkembang biak, misalnya manusia, hewan, air, atau peralatan.'],
                ['sedang', 'Pemberian imunisasi hepatitis B pada petugas kesehatan memutus rantai penularan infeksi pada komponen ...', [
                    'Agen infeksi',
                    'Reservoir',
                    'Cara transmisi',
                    'Pejamu rentan',
                ], 3, 'Imunisasi meningkatkan kekebalan sehingga petugas tidak lagi menjadi pejamu yang rentan.'],
                ['sedang', 'Pneumonia yang terjadi pada pasien yang menggunakan ventilator mekanik lebih dari 48 jam disebut ...', [
                    'ISK',
                    'VAP',
                    'IADP',
                    'IDO',
                ], 1, 'VAP (Ventilator Associated Pneumonia) adalah pneumonia terkait penggunaan ventilator.'],
                ['sedang', 'Tujuan penerapan kewaspadaan standar adalah ...', [
                    'Melindungi pasien yang sudah terdiagnosis penyakit menular saja',
                    'Mencegah penularan infeksi dari sumber yang diketahui maupun tidak diketahui',
                    'Menggantikan kewaspadaan berbasis transmisi',
                    'Mengurangi biaya perawatan pasien',
                ], 1, 'Kewaspadaan standar diterapkan pada semua pasien karena sumber infeksi sering tidak diketahui.'],
                ['sedang', 'Campak dan varisela memerlukan kewaspadaan berbasis transmisi jenis ...', [
                    'Kontak',
                    'Droplet',
                    'Udara (airborne)',
                    'Melalui makanan',
                ], 2, 'Campak dan varisela menular melalui partikel kecil di udara sehingga memerlukan kewaspadaan airborne.'],
                ['sulit', 'Pasien dengan infeksi MRSA memiliki luka terbuka yang mengeluarkan cairan. Kewaspadaan yang perlu diterapkan selain kewaspadaan standar adalah ...', [
                    'Kewaspadaan kontak',
                    'Kewaspadaan droplet',
                    'Kewaspadaan udara',
                    'Tidak perlu kewaspadaan tambahan',
                ], 0, 'MRSA menular melalui kontak langsung maupun tidak langsung sehingga memerlukan kewaspadaan kontak.'],
                ['sulit', 'Pasien pascaoperasi laparotomi hari ke-5 mengalami kemerahan, nyeri, dan keluar pus dari luka insisi. Kondisi ini paling tepat dikategorikan sebagai ...', [
                    'Infeksi yang didapat dari komunitas',
                    'HAIs berupa infeksi daerah operasi (IDO)',
                    'Infeksi saluran kemih',
                    'Reaksi alergi terhadap plester',
                ], 1, 'Tanda infeksi pada luka operasi setelah pasien dirawat menunjukkan IDO yang termasuk HAIs.'],
            ],
            'PPI-02' => [
                ['mudah', 'Tindakan yang paling sederhana dan efektif untuk mencegah HAIs adalah ...', [
                    'Pemberian antibiotik',
                    'Kebersihan tangan',
                    'Isolasi semua pasien',
                    'Fogging ruangan setiap hari',
                ], 1, 'Kebersihan tangan merupakan cara paling sederhana dan efektif untuk mencegah HAIs.'],
                ['mudah', 'Handrub adalah membersihkan tangan menggunakan ...', [
                    'Sabun dan air mengalir',
                    'Cairan berbasis alkohol',
                    'Air hangat saja',
                    'Tisu kering',
                ], 1, 'Handrub menggunakan cairan antiseptik berbasis alkohol tanpa air.'],
                ['mudah', 'Momen pertama dari lima momen kebersihan tangan WHO adalah ...', [
                    'Sebelum kontak dengan pasien',
                    'Setelah kontak dengan pasien',
                    'Setelah terpapar cairan tubuh pasien',
                    'Sebelum tindakan aseptik',
                ], 0, 'Urutan lima momen diawali dengan kebersihan tangan sebelum kontak dengan pasien.'],
                ['sedang', 'Perawat melepas sarung tangan setelah mengambil sampel darah pasien. Kebersihan tangan yang dilakukan setelahnya termasuk momen ...', [
                    'Sebelum kontak dengan pasien',
                    'Sebelum tindakan aseptik',
                    'Setelah terpapar cairan tubuh pasien',
                    'Setelah menyentuh lingkungan pasien',
                ], 2, 'Pengambilan darah berisiko paparan cairan tubuh sehingga termasuk momen setelah terpapar cairan tubuh.'],
                ['sedang', 'Langkah terakhir dari enam langkah kebersihan tangan WHO adalah ...', [
                    'Menggosok punggung tangan',
                    'Menggosok sela-sela jari',
                    'Menggosok ujung jari dengan memutar di telapak tangan',
                    'Menggosok ibu jari secara berputar',
                ], 2, 'Langkah keenam adalah menggosok ujung jari dengan gerakan memutar pada telapak tangan.'],
                ['sedang', 'Kondisi kuku petugas kesehatan yang mendukung kebersihan tangan adalah ...', [
                    'Panjang dan dicat',
                    'Pendek dan tanpa kuku palsu',
                    'Memakai kuku palsu yang bersih',
                    'Panjang asalkan bersih',
                ], 1, 'Kuku panjang dan kuku palsu menjadi tempat mikroorganisme berkumpul dan sulit dibersihkan.'],
                ['sedang', 'Setelah mencuci tangan dengan sabun dan air, tangan sebaiknya dikeringkan dengan ...', [
                    'Handuk kain yang dipakai bersama',
                    'Tisu atau handuk sekali pakai',
                    'Mengibaskan tangan',
                    'Menyeka pada pakaian kerja',
                ], 1, 'Tisu atau handuk sekali pakai mencegah kontaminasi ulang pada tangan yang sudah bersih.'],
                ['sedang', 'Tujuan audit kepatuhan kebersihan tangan di rumah sakit adalah ...', [
                    'Memberi hukuman kepada petugas yang tidak patuh',
                    'Menilai dan meningkatkan kepatuhan petugas',
                    'Mengurangi pemakaian handrub',
                    'Menggantikan pelatihan PPI',
                ], 1, 'Audit dipakai untuk memantau kepatuhan dan menjadi dasar perbaikan serta umpan balik.'],
                ['sulit', 'Setelah merawat pasien diare akibat Clostridioides difficile, kebersihan tangan yang dianjurkan adalah ...', [
                    'Handrub berbasis alkohol saja',
                    'Mencuci tangan dengan sabun dan air mengalir',
                    'Cukup mengganti sarung tangan',
                    'Menyemprotkan disinfektan ke sarung tangan',
                ], 1, 'Spora C. difficile tidak mati oleh alkohol sehingga perlu dibersihkan secara mekanis dengan sabun dan air.'],
                ['sulit', 'Perawat selesai mengukur tekanan darah pasien A lalu akan menyuntikkan obat intravena pada pasien B. Kebersihan tangan yang harus dilakukan adalah ...', [
                    'Hanya setelah kontak dengan pasien A',
                    'Hanya sebelum tindakan aseptik pada pasien B',
                    'Setelah kontak dengan pasien A dan sebelum tindakan aseptik pada pasien B',
                    'Tidak perlu jika perawat memakai sarung tangan',
                ], 2, 'Dua momen berlaku: setelah kontak pasien A dan sebelum tindakan aseptik pada pasien B.'],
            ],
            'PPI-03' => [
                ['mudah', 'Kepanjangan APD adalah ...', [
                    'Alat Pelindung Diri',
                    'Alat Pengaman Dokter',
                    'Alat Pelindung Darurat',
                    'Alat Pencegah Debu',
                ], 0, 'APD adalah Alat Pelindung Diri.'],
                ['mudah', 'APD yang melindungi mata dari percikan darah atau cairan tubuh adalah ...', [
                    'Masker bedah',
                    'Kacamata pelindung (goggles) atau pelindung wajah (face shield)',
                    'Gaun pelindung',
                    'Sepatu boot',
                ], 1, 'Goggles atau face shield melindungi mukosa mata dari percikan.'],
                ['mudah', 'Masker bedah terutama digunakan untuk melindungi dari penularan melalui ...', [
                    'Droplet dan percikan',
                    'Partikel airborne seperti kuman TB',
                    'Kontak kulit',
                    'Makanan dan minuman',
                ], 0, 'Masker bedah melindungi dari droplet dan percikan; partikel airborne memerlukan respirator N95.'],
                ['sedang', 'Sarung tangan steril digunakan pada tindakan ...', [
                    'Membersihkan lantai ruangan',
                    'Pemasangan kateter urin dan tindakan pembedahan',
                    'Mengukur suhu tubuh',
                    'Membagikan makanan pasien',
                ], 1, 'Tindakan aseptik yang masuk ke jaringan steril tubuh memerlukan sarung tangan steril.'],
                ['sedang', 'Tindakan yang harus dilakukan tepat sebelum memakai APD adalah ...', [
                    'Memakai sarung tangan',
                    'Melakukan kebersihan tangan',
                    'Memakai masker',
                    'Melepas alas kaki',
                ], 1, 'Kebersihan tangan dilakukan sebelum memakai APD agar APD tidak terkontaminasi dari tangan.'],
                ['sedang', 'Bagian depan gaun dan bagian luar sarung tangan setelah digunakan merawat pasien dianggap ...', [
                    'Bersih',
                    'Steril',
                    'Terkontaminasi',
                    'Aman disentuh dengan tangan telanjang',
                ], 2, 'Permukaan luar APD yang telah dipakai dianggap terkontaminasi sehingga dilepas tanpa menyentuhnya.'],
                ['sedang', 'Gaun pelindung dipakai terutama bila ...', [
                    'Ada risiko percikan cairan tubuh ke pakaian atau kulit',
                    'Mengisi rekam medis',
                    'Memberikan obat oral',
                    'Berbicara dengan keluarga pasien',
                ], 0, 'Gaun melindungi kulit dan pakaian petugas dari percikan darah atau cairan tubuh.'],
                ['sedang', 'APD sekali pakai yang sudah digunakan harus ...', [
                    'Dicuci lalu dipakai kembali',
                    'Dibuang ke tempat limbah infeksius',
                    'Disimpan di loker untuk dipakai besok',
                    'Dibuang ke tempat sampah domestik',
                ], 1, 'APD sekali pakai yang terkontaminasi termasuk limbah infeksius.'],
                ['sulit', 'Perawat keluar dari ruang isolasi masih memakai sarung tangan dan gaun, lalu menulis catatan di meja perawat. Tindakan ini ...', [
                    'Benar, karena APD tetap melindungi perawat',
                    'Salah, karena APD harus dilepas sebelum meninggalkan area pasien agar lingkungan tidak terkontaminasi',
                    'Benar, selama sarung tangan tidak robek',
                    'Salah, hanya bila pasien menderita penyakit airborne',
                ], 1, 'APD dilepas di area pasien/anteroom agar mikroorganisme tidak terbawa ke lingkungan lain.'],
                ['sulit', 'Saat melakukan intubasi pada pasien suspek COVID-19 (tindakan yang menghasilkan aerosol), APD yang paling tepat adalah ...', [
                    'Masker bedah dan sarung tangan',
                    'Respirator N95, pelindung mata atau face shield, gaun, dan sarung tangan',
                    'Gaun dan sarung tangan saja',
                    'Masker kain dan face shield',
                ], 1, 'Tindakan penghasil aerosol memerlukan respirator N95 serta perlindungan mata, tubuh, dan tangan.'],
            ],
            'PPI-04' => [
                ['mudah', 'Tujuan utama etika batuk adalah ...', [
                    'Meredakan batuk',
                    'Mencegah penyebaran mikroorganisme melalui droplet',
                    'Menghangatkan tenggorokan',
                    'Mempercepat penyembuhan batuk',
                ], 1, 'Etika batuk mencegah droplet yang mengandung mikroorganisme menyebar ke orang lain dan lingkungan.'],
                ['mudah', 'Saat batuk atau bersin, mulut dan hidung sebaiknya ditutup dengan ...', [
                    'Tisu',
                    'Telapak tangan',
                    'Tidak perlu ditutup',
                    'Ujung jilbab atau kerah baju',
                ], 0, 'Tisu adalah pilihan utama, lalu dibuang dan diikuti kebersihan tangan.'],
                ['mudah', 'Pasien dengan gejala batuk yang datang ke fasilitas kesehatan sebaiknya diberikan ...', [
                    'Masker',
                    'Obat batuk sebelum diperiksa',
                    'Sarung tangan',
                    'Gaun pelindung',
                ], 0, 'Masker pada pasien bergejala menahan droplet sejak awal kedatangan.'],
                ['sedang', 'Menutup batuk dengan telapak tangan kurang dianjurkan karena ...', [
                    'Dianggap tidak sopan',
                    'Tangan dapat memindahkan mikroorganisme ke benda dan orang lain',
                    'Membuat batuk lebih keras',
                    'Menyebabkan iritasi kulit',
                ], 1, 'Tangan yang terkena droplet akan mencemari permukaan yang disentuh berikutnya.'],
                ['sedang', 'Poster etika batuk di fasilitas kesehatan sebaiknya dipasang di ...', [
                    'Ruang direksi',
                    'Pintu masuk dan ruang tunggu',
                    'Gudang farmasi',
                    'Dapur gizi',
                ], 1, 'Poster dipasang di area yang pertama dan paling banyak dilalui pasien serta pengunjung.'],
                ['sedang', 'Jenis transmisi yang terutama dicegah dengan etika batuk adalah ...', [
                    'Kontak langsung dengan darah',
                    'Droplet',
                    'Melalui vektor nyamuk',
                    'Melalui makanan',
                ], 1, 'Batuk dan bersin menghasilkan droplet sehingga etika batuk terutama mencegah transmisi droplet.'],
                ['sedang', 'Etika batuk perlu diterapkan oleh ...', [
                    'Pasien TB saja',
                    'Petugas kesehatan saja',
                    'Semua orang dengan gejala pernapasan, baik pasien, pengunjung, maupun petugas',
                    'Anak-anak saja',
                ], 2, 'Etika batuk berlaku untuk siapa pun yang memiliki gejala infeksi saluran pernapasan.'],
                ['sedang', 'Perawat memberikan edukasi etika batuk. Pesan yang TIDAK tepat adalah ...', [
                    'Tutup mulut dan hidung dengan tisu saat batuk',
                    'Gunakan lengan atas bagian dalam bila tidak ada tisu',
                    'Batuk menghadap orang lain agar lekas lega',
                    'Lakukan kebersihan tangan setelah batuk',
                ], 2, 'Batuk harus menjauh dari orang lain dan ditutup untuk mencegah droplet menyebar.'],
                ['sulit', 'Di ruang tunggu IGD terdapat pasien dengan batuk berdahak lebih dari dua minggu dan penurunan berat badan. Tindakan perawat yang paling tepat adalah ...', [
                    'Membiarkan pasien menunggu antrean seperti biasa',
                    'Memberikan masker, menempatkan pasien terpisah di area berventilasi baik, dan memprioritaskan pemeriksaan',
                    'Meminta pasien pulang dan kembali besok',
                    'Langsung memberikan antibiotik',
                ], 1, 'Pasien suspek TB perlu masker, dipisahkan di area berventilasi baik, dan diperiksa lebih dulu untuk mengurangi paparan.'],
                ['sulit', 'Seorang anak batuk-batuk di ruang tunggu poli anak dan ibunya tidak memahami etika batuk. Tindakan perawat yang paling tepat adalah ...', [
                    'Menegur ibu di depan pasien lain',
                    'Mengajarkan etika batuk, menyediakan masker sesuai usia dan tisu, serta menunjukkan fasilitas kebersihan tangan',
                    'Meminta anak menunggu di luar gedung',
                    'Membiarkan karena anak sulit diatur',
                ], 1, 'Edukasi disertai penyediaan sarana (masker, tisu, kebersihan tangan) adalah pendekatan yang efektif dan santun.'],
            ],
            'PPI-05' => [
                ['mudah', 'Kantong plastik berwarna hitam digunakan untuk limbah ...', [
                    'Infeksius',
                    'Non-infeksius (domestik)',
                    'Sitotoksik',
                    'Radioaktif',
                ], 1, 'Kantong hitam untuk limbah non-infeksius seperti kertas, plastik bungkus, dan sisa makanan.'],
                ['mudah', 'Contoh limbah benda tajam adalah ...', [
                    'Kasa bekas',
                    'Jarum suntik dan pisau bedah bekas',
                    'Botol infus plastik kosong',
                    'Kertas resep',
                ], 1, 'Jarum, pisau bedah, dan ampul pecah termasuk benda tajam yang dibuang ke wadah tahan tusuk.'],
                ['mudah', 'Kasa bekas balutan luka pasien termasuk limbah ...', [
                    'Domestik',
                    'Infeksius',
                    'Radioaktif',
                    'Daur ulang',
                ], 1, 'Kasa yang terkena darah atau cairan tubuh pasien termasuk limbah infeksius.'],
                ['sedang', 'Limbah farmasi seperti obat kedaluwarsa dibuang ke kantong berwarna ...', [
                    'Hitam',
                    'Coklat',
                    'Merah',
                    'Ungu',
                ], 1, 'Kantong coklat digunakan untuk limbah farmasi dan kimia.'],
                ['sedang', 'Kantong berwarna merah digunakan untuk limbah ...', [
                    'Radioaktif',
                    'Farmasi',
                    'Domestik',
                    'Sitotoksik',
                ], 0, 'Kantong merah untuk limbah radioaktif; sitotoksik menggunakan kantong ungu.'],
                ['sedang', 'Tempat sampah di ruang perawatan sebaiknya memiliki ...', [
                    'Tutup yang dibuka dengan pedal kaki',
                    'Tanpa tutup agar mudah digunakan',
                    'Tutup yang harus dibuka dengan tangan',
                    'Lubang di bagian bawah',
                ], 0, 'Tutup dengan pedal kaki mencegah kontaminasi tangan dan menjaga limbah tetap tertutup.'],
                ['sedang', 'Kantong limbah infeksius sebaiknya diikat dan diangkut bila sudah terisi ...', [
                    'Penuh hingga meluap',
                    'Tiga perempat bagian',
                    'Seperempat bagian',
                    'Setelah satu minggu, berapa pun isinya',
                ], 1, 'Kantong diikat saat terisi 3/4 agar mudah ditutup dan tidak tumpah saat diangkut.'],
                ['sedang', 'APD yang diperlukan petugas pengangkut limbah infeksius adalah ...', [
                    'Tidak memerlukan APD',
                    'Sarung tangan tebal, masker, apron, dan sepatu tertutup atau boot',
                    'Topi saja',
                    'Masker kain saja',
                ], 1, 'Petugas limbah berisiko tertusuk dan terpercik sehingga memerlukan APD yang memadai.'],
                ['sulit', 'Perawat membuang jarum suntik bekas ke kantong plastik kuning karena safety box sudah penuh. Tindakan ini ...', [
                    'Benar, karena kantong kuning untuk limbah infeksius',
                    'Salah, karena benda tajam harus masuk wadah tahan tusuk; safety box yang penuh harus segera diganti',
                    'Benar, asalkan jarum ditutup kembali',
                    'Salah, karena seharusnya dibuang ke kantong hitam',
                ], 1, 'Kantong plastik dapat tertembus jarum dan menyebabkan cedera tusuk pada petugas lain.'],
                ['sulit', 'Mata perawat terkena percikan darah pasien saat melakukan tindakan. Pertolongan pertama yang tepat adalah ...', [
                    'Menggosok mata dengan tangan',
                    'Membilas mata dengan air mengalir atau NaCl 0,9%, lalu melapor untuk tindak lanjut pajanan',
                    'Meneteskan alkohol ke mata',
                    'Menunggu hingga akhir shift lalu melapor',
                ], 1, 'Mukosa yang terpajan segera dibilas, kemudian dilaporkan agar dapat dinilai risiko dan tindak lanjutnya.'],
            ],
        ];
    }
}
