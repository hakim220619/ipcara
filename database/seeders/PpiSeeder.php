<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Question;
use App\Models\Test;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PpiSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::query()->where('role', 'admin')->value('id');

        $preTest = Test::create([
            'topic_id' => null,
            'kind' => Test::KIND_PRE_TEST,
            'title' => 'Pre-Test IPCARA',
            'description' => 'Tes awal untuk memetakan kemampuan Anda pada lima topik Pencegahan dan Pengendalian Infeksi (PPI).',
            'duration_minutes' => 60,
            'passing_score' => 70,
            'max_attempts' => 1,
            'created_by' => $authorId,
        ]);

        $order = 0;

        foreach ($this->topics() as $i => $data) {
            $topic = Topic::create([
                'code' => $data['code'],
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'description' => $data['description'],
                'learning_objectives' => $data['objectives'],
                'order' => $i + 1,
                'is_published' => true,
            ]);

            foreach ($data['materials'] as $m => $material) {
                Material::create([
                    'topic_id' => $topic->id,
                    'title' => $material['title'],
                    'slug' => Str::slug($data['code'].' '.$material['title']),
                    'type' => $material['type'],
                    'level' => ['dasar', 'menengah', 'lanjut'][$m],
                    'summary' => $material['summary'],
                    'content' => $material['content'],
                    'video_url' => $material['video'] ?? null,
                    'estimated_minutes' => $material['minutes'],
                    'order' => $m + 1,
                    'is_published' => true,
                    'created_by' => $authorId,
                ]);
            }

            foreach ($data['questions'] as $q) {
                $question = Question::create([
                    'topic_id' => $topic->id,
                    'type' => 'pilihan_ganda',
                    'difficulty' => $q['level'],
                    'question_text' => $q['text'],
                    'explanation' => $q['explanation'],
                    'points' => 1,
                    'created_by' => $authorId,
                ]);

                foreach ($q['options'] as $idx => $optionText) {
                    $question->options()->create([
                        'label' => chr(65 + $idx),
                        'option_text' => $optionText,
                        'is_correct' => $idx === $q['answer'],
                        'order' => $idx + 1,
                    ]);
                }

                $preTest->questions()->attach($question->id, ['order' => ++$order]);
            }
        }

        $preTest->keepBalancedQuestions();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function topics(): array
    {
        return [
            [
                'code' => 'PPI-01',
                'title' => 'Pengertian PPI',
                'description' => 'Konsep dasar PPI, HAIs, rantai penularan infeksi, dan kewaspadaan standar.',
                'objectives' => 'Mahasiswa mampu menjelaskan pengertian dan tujuan PPI, jenis HAIs, rantai penularan infeksi, serta komponen kewaspadaan standar.',
                'materials' => [
                    [
                        'title' => 'Konsep Dasar PPI dan HAIs',
                        'type' => 'teks',
                        'minutes' => 10,
                        'summary' => 'Definisi PPI, tujuan, dan jenis Healthcare Associated Infections (HAIs).',
                        'content' => <<<'HTML'
<h2>Pengertian PPI</h2>
<p>Pencegahan dan Pengendalian Infeksi (PPI) adalah upaya untuk mencegah dan meminimalkan terjadinya infeksi pada pasien, petugas, pengunjung, dan masyarakat sekitar fasilitas pelayanan kesehatan (Permenkes RI No. 27 Tahun 2017).</p>
<h2>Tujuan PPI</h2>
<ul>
<li>Melindungi pasien, petugas, dan pengunjung dari risiko infeksi.</li>
<li>Menurunkan angka kejadian infeksi terkait pelayanan kesehatan.</li>
<li>Meningkatkan mutu pelayanan dan keselamatan pasien.</li>
</ul>
<h2>Healthcare Associated Infections (HAIs)</h2>
<p>HAIs adalah infeksi yang terjadi pada pasien selama perawatan di rumah sakit atau fasilitas kesehatan lain, yang tidak ditemukan dan tidak dalam masa inkubasi saat pasien masuk. Infeksi umumnya dikategorikan HAIs bila muncul <strong>48 jam atau lebih</strong> setelah pasien dirawat.</p>
<h3>Jenis HAIs yang sering dipantau</h3>
<ul>
<li><strong>VAP</strong> – pneumonia terkait penggunaan ventilator.</li>
<li><strong>IADP</strong> – infeksi aliran darah primer terkait kateter intravaskular.</li>
<li><strong>ISK</strong> – infeksi saluran kemih terkait kateter urin (CAUTI).</li>
<li><strong>IDO</strong> – infeksi daerah operasi.</li>
</ul>
HTML,
                    ],
                    [
                        'title' => 'Rantai Penularan Infeksi',
                        'type' => 'teks',
                        'minutes' => 15,
                        'summary' => 'Enam komponen rantai penularan dan cara memutusnya.',
                        'content' => <<<'HTML'
<h2>Enam Komponen Rantai Penularan</h2>
<ol>
<li><strong>Agen infeksi</strong> – mikroorganisme penyebab infeksi (bakteri, virus, jamur, parasit).</li>
<li><strong>Reservoir</strong> – tempat agen hidup dan berkembang biak (manusia, alat, air, lingkungan).</li>
<li><strong>Pintu keluar</strong> – jalan keluar agen dari reservoir (saluran napas, darah, luka, feses).</li>
<li><strong>Cara penularan</strong> – kontak, droplet, udara (airborne), vehikulum, dan vektor.</li>
<li><strong>Pintu masuk</strong> – jalan masuk agen ke pejamu (luka, selaput lendir, alat invasif).</li>
<li><strong>Pejamu rentan</strong> – orang dengan daya tahan tubuh rendah, lansia, bayi, pasien dengan alat invasif.</li>
</ol>
<h2>Memutus Rantai Penularan</h2>
<p>Infeksi dapat dicegah dengan memutus minimal satu mata rantai. Contohnya, kebersihan tangan dan APD memutus <em>cara penularan</em>, sterilisasi alat menghilangkan <em>agen infeksi</em>, dan imunisasi melindungi <em>pejamu rentan</em>.</p>
HTML,
                    ],
                    [
                        'title' => 'Kewaspadaan Standar dan Berbasis Transmisi',
                        'type' => 'kasus',
                        'minutes' => 20,
                        'summary' => 'Sebelas komponen kewaspadaan standar dan penerapannya dalam studi kasus.',
                        'content' => <<<'HTML'
<h2>Kewaspadaan Standar</h2>
<p>Kewaspadaan standar diterapkan pada <strong>semua pasien</strong> tanpa memandang diagnosis atau status infeksinya. Komponennya:</p>
<ol>
<li>Kebersihan tangan</li>
<li>Alat pelindung diri (APD)</li>
<li>Dekontaminasi peralatan perawatan pasien</li>
<li>Pengendalian lingkungan</li>
<li>Pengelolaan limbah</li>
<li>Penatalaksanaan linen</li>
<li>Perlindungan kesehatan petugas</li>
<li>Penempatan pasien</li>
<li>Kebersihan pernapasan / etika batuk dan bersin</li>
<li>Praktik menyuntik yang aman</li>
<li>Praktik lumbal pungsi yang aman</li>
</ol>
<h2>Kewaspadaan Berbasis Transmisi</h2>
<p>Diterapkan sebagai tambahan kewaspadaan standar pada pasien yang diketahui atau dicurigai terinfeksi, yaitu kewaspadaan <strong>kontak</strong>, <strong>droplet</strong>, dan <strong>udara (airborne)</strong>.</p>
<h2>Studi Kasus</h2>
<p>Tn. A (65 tahun) dirawat dengan stroke dan terpasang kateter urin. Pada hari ke-4 perawatan, pasien demam, urin keruh, dan kultur urin positif. Karena saat masuk tidak ada tanda infeksi, kondisi ini termasuk HAIs jenis ISK terkait kateter. <em>Refleksi:</em> komponen kewaspadaan standar mana yang perlu diperkuat oleh tim perawat?</p>
HTML,
                    ],
                ],
                'questions' => [
                    [
                        'level' => 'mudah',
                        'text' => 'Menurut Permenkes RI No. 27 Tahun 2017, Pencegahan dan Pengendalian Infeksi (PPI) adalah upaya untuk ...',
                        'options' => [
                            'Mengobati pasien yang terkena infeksi di rumah sakit',
                            'Mencegah dan meminimalkan terjadinya infeksi pada pasien, petugas, pengunjung, dan masyarakat sekitar fasilitas pelayanan kesehatan',
                            'Mengisolasi semua pasien dengan penyakit menular',
                            'Memberikan antibiotik profilaksis kepada seluruh pasien',
                        ],
                        'answer' => 1,
                        'explanation' => 'PPI berfokus pada pencegahan dan meminimalkan infeksi bagi pasien, petugas, pengunjung, dan masyarakat sekitar fasyankes.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Healthcare Associated Infections (HAIs) adalah infeksi yang ...',
                        'options' => [
                            'Sudah ada saat pasien masuk rumah sakit',
                            'Didapat pasien di komunitas sebelum dirawat',
                            'Terjadi pada pasien selama perawatan di fasyankes dan tidak ditemukan maupun dalam masa inkubasi saat pasien masuk',
                            'Hanya dapat terjadi pada petugas kesehatan',
                        ],
                        'answer' => 2,
                        'explanation' => 'HAIs didapat selama perawatan dan tidak ada atau tidak dalam masa inkubasi saat pasien masuk.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Suatu infeksi umumnya dikategorikan sebagai HAIs bila muncul setelah pasien dirawat minimal ...',
                        'options' => ['12 jam', '24 jam', '48 jam', '7 hari'],
                        'answer' => 2,
                        'explanation' => 'Infeksi yang muncul ≥ 48 jam setelah pasien dirawat umumnya dikategorikan sebagai HAIs.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Urutan komponen rantai penularan infeksi yang benar adalah ...',
                        'options' => [
                            'Agen infeksi → reservoir → pintu keluar → cara penularan → pintu masuk → pejamu rentan',
                            'Reservoir → agen infeksi → pintu masuk → pejamu rentan → pintu keluar → cara penularan',
                            'Pejamu rentan → agen infeksi → reservoir → cara penularan → pintu keluar → pintu masuk',
                            'Cara penularan → agen infeksi → pintu keluar → reservoir → pejamu rentan → pintu masuk',
                        ],
                        'answer' => 0,
                        'explanation' => 'Rantai penularan dimulai dari agen infeksi hingga pejamu rentan.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Kebersihan tangan petugas kesehatan merupakan upaya memutus rantai penularan infeksi pada komponen ...',
                        'options' => ['Agen infeksi', 'Reservoir', 'Cara penularan (transmisi)', 'Pejamu rentan'],
                        'answer' => 2,
                        'explanation' => 'Tangan petugas adalah media utama penularan kontak, sehingga kebersihan tangan memutus cara penularan.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Berikut ini yang termasuk jenis HAIs yang sering dipantau di rumah sakit adalah ...',
                        'options' => [
                            'Demam berdarah, malaria, dan tifoid',
                            'Hipertensi, diabetes melitus, dan stroke',
                            'Asma, PPOK, dan pneumonia komunitas',
                            'VAP, IADP, ISK terkait kateter, dan IDO',
                        ],
                        'answer' => 3,
                        'explanation' => 'VAP, IADP, ISK, dan IDO adalah jenis HAIs yang menjadi indikator surveilans PPI.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Kewaspadaan standar diterapkan pada ...',
                        'options' => [
                            'Pasien dengan HIV saja',
                            'Pasien yang sudah terdiagnosis infeksi saja',
                            'Semua pasien tanpa memandang diagnosis atau status infeksinya',
                            'Pasien di ruang isolasi saja',
                        ],
                        'answer' => 2,
                        'explanation' => 'Kewaspadaan standar berlaku untuk semua pasien karena status infeksi tidak selalu diketahui.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Berikut ini yang BUKAN merupakan komponen kewaspadaan standar adalah ...',
                        'options' => [
                            'Kebersihan tangan',
                            'Penggunaan alat pelindung diri',
                            'Pemberian antibiotik profilaksis rutin kepada semua pasien',
                            'Pengelolaan limbah',
                        ],
                        'answer' => 2,
                        'explanation' => 'Antibiotik profilaksis rutin bukan komponen kewaspadaan standar dan justru dapat memicu resistansi.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Kewaspadaan berbasis transmisi terdiri atas kewaspadaan ...',
                        'options' => [
                            'Oral, parenteral, dan topikal',
                            'Kontak, droplet, dan udara (airborne)',
                            'Standar, universal, dan isolasi',
                            'Vertikal, horizontal, dan silang',
                        ],
                        'answer' => 1,
                        'explanation' => 'Kewaspadaan berbasis transmisi meliputi kontak, droplet, dan airborne.',
                    ],
                    [
                        'level' => 'sulit',
                        'text' => 'Seorang pasien masuk rumah sakit tanpa tanda infeksi. Pada hari ke-4 setelah pemasangan kateter urin, pasien demam, urin keruh, dan kultur urin positif. Kondisi ini paling tepat disebut ...',
                        'options' => [
                            'Infeksi yang didapat dari komunitas',
                            'HAIs berupa infeksi saluran kemih terkait kateter (CAUTI)',
                            'Reaksi alergi terhadap bahan kateter',
                            'Infeksi bawaan sejak lahir',
                        ],
                        'answer' => 1,
                        'explanation' => 'Infeksi muncul > 48 jam setelah dirawat dan berhubungan dengan kateter urin, sehingga termasuk CAUTI.',
                    ],
                ],
            ],
            [
                'code' => 'PPI-02',
                'title' => 'Hand Hygiene',
                'description' => 'Lima momen kebersihan tangan WHO, teknik handrub dan handwash.',
                'objectives' => 'Mahasiswa mampu melakukan kebersihan tangan dengan teknik yang benar pada lima momen WHO.',
                'materials' => [
                    [
                        'title' => 'Handrub dan Handwash',
                        'type' => 'teks',
                        'minutes' => 10,
                        'summary' => 'Perbedaan handrub berbasis alkohol dan cuci tangan dengan sabun serta kapan menggunakannya.',
                        'content' => <<<'HTML'
<h2>Pengertian</h2>
<p>Kebersihan tangan (<em>hand hygiene</em>) adalah upaya membersihkan tangan untuk menghilangkan kotoran dan mengurangi mikroorganisme. Ini adalah cara paling sederhana dan efektif untuk mencegah HAIs.</p>
<h2>Dua Metode</h2>
<ul>
<li><strong>Handrub</strong> – menggosok tangan dengan cairan berbasis alkohol selama <strong>20–30 detik</strong>. Digunakan bila tangan tidak tampak kotor.</li>
<li><strong>Handwash</strong> – mencuci tangan dengan sabun dan air mengalir selama <strong>40–60 detik</strong>.</li>
</ul>
<h2>Wajib Handwash bila</h2>
<ul>
<li>Tangan tampak kotor atau terkena darah/cairan tubuh.</li>
<li>Setelah dari toilet.</li>
<li>Setelah merawat pasien dengan infeksi pembentuk spora (mis. <em>Clostridioides difficile</em>).</li>
</ul>
<h2>Persiapan</h2>
<p>Kuku pendek dan bersih, tidak memakai cat kuku, cincin, gelang, atau jam tangan saat merawat pasien.</p>
HTML,
                    ],
                    [
                        'title' => 'Lima Momen Kebersihan Tangan WHO',
                        'type' => 'teks',
                        'minutes' => 15,
                        'summary' => 'Kapan tepatnya perawat wajib melakukan kebersihan tangan.',
                        'content' => <<<'HTML'
<h2>Lima Momen</h2>
<ol>
<li><strong>Sebelum kontak dengan pasien</strong> – melindungi pasien dari kuman di tangan petugas.</li>
<li><strong>Sebelum tindakan aseptik</strong> – mis. memasang infus, kateter urin, merawat luka.</li>
<li><strong>Setelah terpapar cairan tubuh pasien</strong> – mis. setelah mengambil darah atau membuang urin.</li>
<li><strong>Setelah kontak dengan pasien</strong> – mis. setelah mengukur tanda vital.</li>
<li><strong>Setelah kontak dengan lingkungan sekitar pasien</strong> – mis. setelah merapikan tempat tidur, walau tidak menyentuh pasien.</li>
</ol>
<h2>Sarung Tangan Tidak Menggantikan Kebersihan Tangan</h2>
<p>Lakukan kebersihan tangan sebelum memakai dan segera setelah melepas sarung tangan.</p>
HTML,
                    ],
                    [
                        'title' => 'Enam Langkah dan Studi Kasus Kepatuhan',
                        'type' => 'kasus',
                        'minutes' => 20,
                        // Video demonstrasi 6 langkah cuci tangan WHO (Adi Husada Cancer Center).
                        'video' => 'https://www.youtube.com/watch?v=5BqEhu1Tuow',
                        'summary' => 'Teknik enam langkah WHO dan analisis kasus kepatuhan kebersihan tangan.',
                        'content' => <<<'HTML'
<h2>Enam Langkah WHO</h2>
<ol>
<li>Tuang cairan/sabun, gosok kedua telapak tangan.</li>
<li>Gosok punggung tangan dan sela-sela jari bergantian.</li>
<li>Gosok telapak tangan dan sela-sela jari dengan jari saling mengait.</li>
<li>Gosok punggung jari dengan posisi jari saling mengunci.</li>
<li>Gosok ibu jari secara berputar dalam genggaman tangan lainnya.</li>
<li>Gosok ujung jari pada telapak tangan dengan gerakan memutar.</li>
</ol>
<h2>Studi Kasus</h2>
<p>Saat audit, perawat B terlihat langsung memasang kateter urin setelah menulis catatan di nurse station, lalu merapikan linen pasien dan keluar ruangan tanpa kebersihan tangan. <em>Analisis:</em> perawat melewatkan <strong>momen 2</strong> (sebelum tindakan aseptik) dan <strong>momen 5</strong> (setelah kontak dengan lingkungan pasien). Rencanakan edukasi dan umpan balik audit untuk meningkatkan kepatuhan.</p>
HTML,
                    ],
                ],
                'questions' => [
                    [
                        'level' => 'mudah',
                        'text' => 'Jumlah momen kebersihan tangan menurut WHO adalah ...',
                        'options' => ['3 momen', '4 momen', '5 momen', '6 momen'],
                        'answer' => 2,
                        'explanation' => 'WHO menetapkan lima momen kebersihan tangan.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Berikut ini yang termasuk salah satu dari lima momen kebersihan tangan WHO adalah ...',
                        'options' => [
                            'Sebelum masuk kantin rumah sakit',
                            'Setelah menulis di nurse station yang jauh dari pasien',
                            'Sebelum menyalakan komputer',
                            'Sebelum kontak dengan pasien',
                        ],
                        'answer' => 3,
                        'explanation' => 'Momen 1 adalah sebelum kontak dengan pasien.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Durasi kebersihan tangan dengan cairan berbasis alkohol (handrub) yang dianjurkan adalah ...',
                        'options' => ['5–10 detik', '20–30 detik', '40–60 detik', '2–3 menit'],
                        'answer' => 1,
                        'explanation' => 'Handrub dilakukan selama 20–30 detik hingga tangan kering.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Durasi mencuci tangan dengan sabun dan air mengalir (handwash) yang dianjurkan adalah ...',
                        'options' => ['10–15 detik', '20–30 detik', '40–60 detik', '5 menit'],
                        'answer' => 2,
                        'explanation' => 'Handwash dilakukan selama 40–60 detik.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Kondisi yang mengharuskan perawat mencuci tangan dengan sabun dan air mengalir, bukan handrub, adalah ...',
                        'options' => [
                            'Tangan tampak kotor atau terkena cairan tubuh pasien',
                            'Sebelum kontak dengan pasien dan tangan tampak bersih',
                            'Setelah menyentuh tempat tidur pasien',
                            'Setelah mengukur tekanan darah pasien',
                        ],
                        'answer' => 0,
                        'explanation' => 'Alkohol tidak efektif mengangkat kotoran dan bahan organik, sehingga tangan yang kotor harus dicuci dengan sabun.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Langkah pertama dari enam langkah kebersihan tangan WHO adalah ...',
                        'options' => [
                            'Menggosok punggung tangan',
                            'Menuang cairan/sabun lalu menggosok kedua telapak tangan',
                            'Menggosok ibu jari secara berputar',
                            'Menggosok ujung jari pada telapak tangan',
                        ],
                        'answer' => 1,
                        'explanation' => 'Langkah 1 adalah meratakan cairan dengan menggosok kedua telapak tangan.',
                    ],
                    [
                        'level' => 'sulit',
                        'text' => 'Perawat akan memasang kateter urin pada pasien. Momen kebersihan tangan yang harus dilakukan tepat sebelum tindakan ini adalah ...',
                        'options' => [
                            'Momen 1: sebelum kontak dengan pasien',
                            'Momen 2: sebelum tindakan aseptik',
                            'Momen 3: setelah terpapar cairan tubuh',
                            'Momen 5: setelah kontak dengan lingkungan pasien',
                        ],
                        'answer' => 1,
                        'explanation' => 'Pemasangan kateter adalah tindakan aseptik sehingga termasuk momen 2.',
                    ],
                    [
                        'level' => 'sulit',
                        'text' => 'Perawat merapikan linen dan meja di sekitar tempat tidur pasien tanpa menyentuh pasien. Kebersihan tangan setelah kegiatan ini termasuk momen ...',
                        'options' => [
                            'Momen 1: sebelum kontak dengan pasien',
                            'Momen 3: setelah terpapar cairan tubuh',
                            'Momen 4: setelah kontak dengan pasien',
                            'Momen 5: setelah kontak dengan lingkungan sekitar pasien',
                        ],
                        'answer' => 3,
                        'explanation' => 'Menyentuh lingkungan sekitar pasien tanpa menyentuh pasien termasuk momen 5.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Hal berikut yang mendukung efektivitas kebersihan tangan adalah ...',
                        'options' => [
                            'Kuku panjang dan dicat',
                            'Memakai cincin dan jam tangan saat bertugas',
                            'Kuku pendek, bersih, dan tanpa perhiasan',
                            'Memakai sarung tangan tanpa kebersihan tangan',
                        ],
                        'answer' => 2,
                        'explanation' => 'Kuku panjang dan perhiasan menjadi tempat mikroorganisme berkumpul.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Pernyataan yang benar tentang penggunaan sarung tangan dan kebersihan tangan adalah ...',
                        'options' => [
                            'Sarung tangan dapat menggantikan kebersihan tangan',
                            'Kebersihan tangan tetap dilakukan sebelum memakai dan setelah melepas sarung tangan',
                            'Kebersihan tangan hanya perlu dilakukan sebelum memakai sarung tangan',
                            'Kebersihan tangan hanya perlu bila sarung tangan tampak kotor',
                        ],
                        'answer' => 1,
                        'explanation' => 'Sarung tangan bisa bocor atau terkontaminasi saat dilepas, sehingga kebersihan tangan tetap wajib.',
                    ],
                ],
            ],
            [
                'code' => 'PPI-03',
                'title' => 'Penggunaan APD',
                'description' => 'Jenis, pemilihan, urutan pemakaian, dan pelepasan alat pelindung diri.',
                'objectives' => 'Mahasiswa mampu memilih, memakai, dan melepas APD dengan benar sesuai risiko paparan.',
                'materials' => [
                    [
                        'title' => 'Jenis dan Pemilihan APD',
                        'type' => 'teks',
                        'minutes' => 10,
                        'summary' => 'Macam-macam APD dan prinsip pemilihannya berdasarkan penilaian risiko.',
                        'content' => <<<'HTML'
<h2>Pengertian</h2>
<p>Alat Pelindung Diri (APD) adalah perlengkapan yang melindungi kulit dan selaput lendir petugas dari paparan darah, cairan tubuh, dan agen infeksius.</p>
<h2>Jenis APD</h2>
<ul>
<li><strong>Sarung tangan</strong> – steril (tindakan aseptik) dan non-steril (pemeriksaan).</li>
<li><strong>Masker</strong> – masker bedah untuk droplet; respirator N95 untuk penularan udara.</li>
<li><strong>Pelindung mata/wajah</strong> – goggle atau face shield untuk risiko percikan.</li>
<li><strong>Gaun/apron</strong> – melindungi kulit dan pakaian.</li>
<li><strong>Penutup kepala dan pelindung kaki</strong>.</li>
</ul>
<h2>Prinsip Pemilihan</h2>
<p>APD dipilih berdasarkan <strong>penilaian risiko</strong>: jenis tindakan, kemungkinan paparan darah/cairan tubuh, dan cara penularan penyakit pasien.</p>
HTML,
                    ],
                    [
                        'title' => 'Urutan Memakai dan Melepas APD',
                        'type' => 'teks',
                        'minutes' => 15,
                        'summary' => 'Langkah donning dan doffing yang aman untuk mencegah kontaminasi diri.',
                        'content' => <<<'HTML'
<h2>Memakai APD (Donning)</h2>
<ol>
<li>Kebersihan tangan</li>
<li>Gaun</li>
<li>Masker atau respirator</li>
<li>Goggle atau face shield</li>
<li>Sarung tangan (menutupi ujung lengan gaun)</li>
</ol>
<h2>Melepas APD (Doffing)</h2>
<ol>
<li>Sarung tangan</li>
<li>Goggle atau face shield</li>
<li>Gaun</li>
<li>Masker atau respirator (dilepas terakhir, di luar ruang pasien)</li>
<li>Kebersihan tangan</li>
</ol>
<p>Prinsip: bagian luar APD dianggap terkontaminasi, jadi jangan menyentuhnya dengan tangan tanpa pelindung.</p>
HTML,
                    ],
                    [
                        'title' => 'APD Sesuai Transmisi dan Studi Kasus',
                        'type' => 'kasus',
                        'minutes' => 20,
                        'summary' => 'Memilih APD untuk kewaspadaan kontak, droplet, dan airborne.',
                        'content' => <<<'HTML'
<h2>APD Berdasarkan Transmisi</h2>
<ul>
<li><strong>Kontak</strong> (mis. MRSA): sarung tangan dan gaun.</li>
<li><strong>Droplet</strong> (mis. influenza): masker bedah saat berada dalam jarak dekat.</li>
<li><strong>Airborne</strong> (mis. TB paru, campak): respirator N95, pasien di ruang bertekanan negatif.</li>
</ul>
<h2>Seal Check Respirator</h2>
<p>Setiap kali memakai respirator N95, lakukan <em>seal check</em>: tutup permukaan respirator dengan kedua tangan, lalu tarik dan embuskan napas untuk memastikan tidak ada udara bocor di tepinya.</p>
<h2>Studi Kasus</h2>
<p>Perawat C akan melakukan pengisapan lendir (suction) terbuka pada pasien pneumonia. Tindakan ini berisiko percikan, sehingga perawat perlu sarung tangan, gaun, masker, dan pelindung mata/face shield.</p>
HTML,
                    ],
                ],
                'questions' => [
                    [
                        'level' => 'mudah',
                        'text' => 'Tujuan utama penggunaan APD bagi petugas kesehatan adalah ...',
                        'options' => [
                            'Melindungi kulit dan selaput lendir petugas dari paparan darah, cairan tubuh, dan agen infeksius',
                            'Menjadi seragam identitas petugas',
                            'Mempercepat pelaksanaan tindakan',
                            'Menggantikan kebersihan tangan',
                        ],
                        'answer' => 0,
                        'explanation' => 'APD adalah penghalang fisik antara petugas dan sumber infeksi.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Pemilihan jenis APD yang digunakan didasarkan pada ...',
                        'options' => [
                            'Selera pribadi petugas',
                            'Penilaian risiko paparan dan jenis tindakan yang akan dilakukan',
                            'Warna APD yang tersedia',
                            'Jabatan petugas di ruangan',
                        ],
                        'answer' => 1,
                        'explanation' => 'APD dipilih sesuai risiko paparan dan cara penularan penyakit.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Urutan memakai APD yang benar adalah ...',
                        'options' => [
                            'Sarung tangan → gaun → masker → goggle',
                            'Masker → sarung tangan → gaun → goggle',
                            'Kebersihan tangan → gaun → masker/respirator → goggle/face shield → sarung tangan',
                            'Goggle → masker → sarung tangan → gaun',
                        ],
                        'answer' => 2,
                        'explanation' => 'Sarung tangan dipakai terakhir agar menutupi ujung lengan gaun.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Urutan melepas APD yang benar adalah ...',
                        'options' => [
                            'Masker → gaun → sarung tangan → goggle → kebersihan tangan',
                            'Gaun → masker → goggle → sarung tangan → kebersihan tangan',
                            'Goggle → sarung tangan → masker → gaun → kebersihan tangan',
                            'Sarung tangan → goggle/face shield → gaun → masker/respirator → kebersihan tangan',
                        ],
                        'answer' => 3,
                        'explanation' => 'Sarung tangan yang paling terkontaminasi dilepas lebih dulu, masker terakhir.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'APD yang dilepas paling akhir karena melindungi saluran pernapasan adalah ...',
                        'options' => ['Sarung tangan', 'Gaun', 'Masker/respirator', 'Pelindung kaki'],
                        'answer' => 2,
                        'explanation' => 'Masker/respirator dilepas terakhir, sebaiknya di luar ruang pasien.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Saat merawat pasien TB paru aktif (penularan melalui udara), perawat harus menggunakan ...',
                        'options' => ['Masker bedah', 'Respirator N95', 'Masker kain', 'Face shield saja'],
                        'answer' => 1,
                        'explanation' => 'Penularan airborne membutuhkan respirator N95 yang menyaring partikel sangat kecil.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'APD yang tepat untuk perawat saat berada dalam jarak dekat dengan pasien influenza (penularan droplet) adalah ...',
                        'options' => ['Masker bedah', 'Sepatu boot', 'Apron plastik saja', 'Penutup kepala saja'],
                        'answer' => 0,
                        'explanation' => 'Kewaspadaan droplet memerlukan masker bedah.',
                    ],
                    [
                        'level' => 'sulit',
                        'text' => 'Setiap kali selesai memakai respirator N95, petugas harus melakukan ...',
                        'options' => [
                            'Fit test di laboratorium',
                            'Seal check (pemeriksaan kerapatan) untuk memastikan tidak ada kebocoran',
                            'Mencuci respirator dengan air',
                            'Menyemprot respirator dengan alkohol',
                        ],
                        'answer' => 1,
                        'explanation' => 'Seal check dilakukan setiap kali memakai respirator, sedangkan fit test dilakukan secara berkala.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Sarung tangan harus diganti ketika ...',
                        'options' => [
                            'Robek saja',
                            'Satu kali setiap shift',
                            'Sudah dipakai untuk lima pasien',
                            'Berpindah dari satu pasien ke pasien lain',
                        ],
                        'answer' => 3,
                        'explanation' => 'Satu pasang sarung tangan hanya untuk satu pasien agar tidak terjadi kontaminasi silang.',
                    ],
                    [
                        'level' => 'sulit',
                        'text' => 'Perawat akan melakukan pengisapan lendir (suction) terbuka yang berisiko menimbulkan percikan. APD yang paling tepat adalah ...',
                        'options' => [
                            'Sarung tangan saja',
                            'Sarung tangan, gaun, masker, dan pelindung mata/face shield',
                            'Penutup kepala saja',
                            'Tidak perlu APD karena tindakan singkat',
                        ],
                        'answer' => 1,
                        'explanation' => 'Risiko percikan ke wajah dan tubuh memerlukan perlindungan mata, mulut, hidung, tangan, dan tubuh.',
                    ],
                ],
            ],
            [
                'code' => 'PPI-04',
                'title' => 'Etika Batuk',
                'description' => 'Kebersihan pernapasan serta etika batuk dan bersin di fasilitas kesehatan.',
                'objectives' => 'Mahasiswa mampu menerapkan dan mengedukasi etika batuk dan bersin kepada pasien, pengunjung, dan petugas.',
                'materials' => [
                    [
                        'title' => 'Pengertian dan Cara Etika Batuk',
                        'type' => 'teks',
                        'minutes' => 10,
                        'summary' => 'Tata cara batuk dan bersin yang benar untuk mencegah penularan.',
                        'content' => <<<'HTML'
<h2>Pengertian</h2>
<p>Etika batuk dan bersin adalah tata cara batuk atau bersin yang benar untuk mencegah penyebaran mikroorganisme melalui droplet dan udara. Etika batuk termasuk komponen kewaspadaan standar, yaitu <strong>kebersihan pernapasan</strong> (<em>respiratory hygiene</em>).</p>
<h2>Cara yang Benar</h2>
<ol>
<li>Tutup hidung dan mulut dengan tisu saat batuk atau bersin.</li>
<li>Bila tidak ada tisu, tutup dengan <strong>lengan atas bagian dalam</strong>, bukan telapak tangan.</li>
<li>Buang tisu ke tempat sampah (di fasyankes: tempat sampah infeksius).</li>
<li>Lakukan kebersihan tangan setelahnya.</li>
<li>Gunakan masker bila sedang batuk atau pilek.</li>
</ol>
HTML,
                    ],
                    [
                        'title' => 'Fasilitas dan Edukasi Etika Batuk',
                        'type' => 'teks',
                        'minutes' => 15,
                        'summary' => 'Sarana pendukung dan edukasi bagi pasien serta pengunjung.',
                        'content' => <<<'HTML'
<h2>Fasilitas Pendukung</h2>
<p>Fasilitas pelayanan kesehatan perlu menyediakan tisu, masker, handrub atau wastafel, tempat sampah, serta poster etika batuk di area tunggu dan pintu masuk.</p>
<h2>Jaga Jarak</h2>
<p>Droplet umumnya jatuh dalam jarak sekitar 1 meter, sehingga pasien dengan gejala batuk ditempatkan berjarak <strong>minimal 1 meter</strong> dari pasien lain.</p>
<h2>Edukasi</h2>
<p>Perawat berperan mengedukasi pasien dan pengunjung dengan bahasa sederhana dan contoh langsung. Petugas yang batuk atau pilek tetap memakai masker bedah, menerapkan etika batuk, dan menjaga kebersihan tangan.</p>
HTML,
                    ],
                    [
                        'title' => 'Penerapan di Fasyankes dan Studi Kasus',
                        'type' => 'kasus',
                        'minutes' => 20,
                        'summary' => 'Skrining pasien batuk, pemisahan area, dan kasus edukasi pengunjung.',
                        'content' => <<<'HTML'
<h2>Penerapan di Fasyankes</h2>
<ul>
<li>Skrining gejala batuk di pintu masuk atau triase.</li>
<li>Pasien batuk diberi masker dan diarahkan ke area tunggu terpisah.</li>
<li>Pasien suspek TB diprioritaskan pemeriksaannya untuk mengurangi waktu tunggu.</li>
</ul>
<h2>Studi Kasus</h2>
<p>Seorang pengunjung batuk-batuk di bangsal, menutup mulut dengan telapak tangan, lalu memegang gagang pintu. Perawat mendekat dengan sopan, memberikan masker, mengajarkan cara menutup batuk dengan tisu atau lengan atas bagian dalam, menunjukkan tempat sampah, dan mengajak pengunjung membersihkan tangan dengan handrub.</p>
HTML,
                    ],
                ],
                'questions' => [
                    [
                        'level' => 'mudah',
                        'text' => 'Etika batuk adalah ...',
                        'options' => [
                            'Tata cara batuk dan bersin yang benar untuk mencegah penyebaran mikroorganisme melalui droplet dan udara',
                            'Cara meredakan batuk dengan obat',
                            'Teknik batuk efektif untuk mengeluarkan dahak',
                            'Larangan batuk di area rumah sakit',
                        ],
                        'answer' => 0,
                        'explanation' => 'Etika batuk bertujuan mencegah penularan, berbeda dengan batuk efektif yang bertujuan mengeluarkan dahak.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Saat akan batuk dan tidak tersedia tisu, cara yang benar adalah menutup mulut dan hidung dengan ...',
                        'options' => ['Telapak tangan', 'Lengan atas bagian dalam', 'Tidak perlu ditutup', 'Ujung jari'],
                        'answer' => 1,
                        'explanation' => 'Lengan atas bagian dalam jarang dipakai menyentuh benda sehingga mengurangi penyebaran kuman.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Di fasilitas pelayanan kesehatan, tisu bekas menutup batuk sebaiknya dibuang ke ...',
                        'options' => [
                            'Lantai',
                            'Saku baju',
                            'Tempat sampah infeksius (kantong kuning)',
                            'Wastafel',
                        ],
                        'answer' => 2,
                        'explanation' => 'Tisu bekas mengandung sekret pernapasan sehingga diperlakukan sebagai limbah infeksius.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Tindakan yang harus dilakukan setelah batuk atau bersin adalah ...',
                        'options' => [
                            'Langsung makan',
                            'Mengusap wajah',
                            'Berjabat tangan dengan orang lain',
                            'Melakukan kebersihan tangan',
                        ],
                        'answer' => 3,
                        'explanation' => 'Tangan dapat terkontaminasi sekret saat batuk sehingga harus dibersihkan.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Pasien dengan gejala batuk yang sedang menunggu di ruang tunggu poliklinik sebaiknya ...',
                        'options' => [
                            'Dibiarkan duduk di mana saja',
                            'Disuruh pulang tanpa diperiksa',
                            'Diberi masker dan ditempatkan berjarak minimal 1 meter dari pasien lain',
                            'Ditempatkan bersama pasien anak-anak',
                        ],
                        'answer' => 2,
                        'explanation' => 'Masker dan jarak minimal 1 meter mengurangi penularan droplet.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Etika batuk dan bersin termasuk dalam komponen kewaspadaan standar, yaitu ...',
                        'options' => [
                            'Pengelolaan limbah',
                            'Kebersihan pernapasan (respiratory hygiene)',
                            'Penatalaksanaan linen',
                            'Praktik menyuntik yang aman',
                        ],
                        'answer' => 1,
                        'explanation' => 'Etika batuk adalah bagian dari kebersihan pernapasan.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Fasilitas yang perlu disediakan di area tunggu untuk mendukung etika batuk adalah ...',
                        'options' => [
                            'Televisi dan majalah',
                            'Kipas angin',
                            'Pengharum ruangan',
                            'Tisu, masker, handrub/wastafel, dan tempat sampah',
                        ],
                        'answer' => 3,
                        'explanation' => 'Sarana tersebut memudahkan pasien dan pengunjung menerapkan etika batuk.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Jarak minimal dari orang yang batuk untuk mengurangi risiko penularan droplet adalah ...',
                        'options' => ['30 cm', '50 cm', '1 meter', 'Tidak perlu menjaga jarak'],
                        'answer' => 2,
                        'explanation' => 'Droplet umumnya jatuh dalam radius sekitar 1 meter.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Perawat yang sedang batuk-pilek ringan tetapi tetap bertugas sebaiknya ...',
                        'options' => [
                            'Tidak memakai masker agar suara terdengar jelas',
                            'Memakai masker bedah, menerapkan etika batuk, dan menjaga kebersihan tangan',
                            'Memakai sarung tangan saja',
                            'Cukup minum obat batuk',
                        ],
                        'answer' => 1,
                        'explanation' => 'Petugas yang bergejala berpotensi menulari pasien sehingga wajib memakai masker.',
                    ],
                    [
                        'level' => 'sulit',
                        'text' => 'Seorang pengunjung batuk di bangsal sambil menutup mulut dengan telapak tangan, lalu memegang gagang pintu. Tindakan perawat yang paling tepat adalah ...',
                        'options' => [
                            'Membiarkannya karena hanya pengunjung',
                            'Memberi masker, mengajarkan menutup batuk dengan tisu/lengan atas bagian dalam, membuang tisu ke tempat sampah, dan melakukan kebersihan tangan',
                            'Meminta pengunjung keluar tanpa penjelasan',
                            'Membersihkan gagang pintu saja',
                        ],
                        'answer' => 1,
                        'explanation' => 'Edukasi langsung disertai sarana (masker, handrub) memutus penularan dari sumbernya.',
                    ],
                ],
            ],
            [
                'code' => 'PPI-05',
                'title' => 'Pengelolaan Limbah',
                'description' => 'Pemilahan limbah fasyankes, benda tajam, dan penanganan pajanan.',
                'objectives' => 'Mahasiswa mampu memilah limbah sesuai jenisnya, mengelola benda tajam, dan menangani pajanan dengan benar.',
                'materials' => [
                    [
                        'title' => 'Jenis Limbah dan Kode Warna',
                        'type' => 'teks',
                        'minutes' => 10,
                        'summary' => 'Penggolongan limbah fasyankes dan warna kantong/wadahnya.',
                        'content' => <<<'HTML'
<h2>Jenis Limbah Fasyankes</h2>
<ul>
<li><strong>Infeksius</strong> – terkontaminasi darah/cairan tubuh: kasa bekas, sarung tangan bekas → kantong <strong>kuning</strong>.</li>
<li><strong>Non-infeksius (domestik)</strong> – kertas, plastik bersih, sisa makanan → kantong <strong>hitam</strong>.</li>
<li><strong>Benda tajam</strong> – jarum, pisau bedah, ampul → <strong>wadah tahan tusuk (safety box)</strong>.</li>
<li><strong>Sitotoksik</strong> – sisa obat kemoterapi → kantong <strong>ungu</strong>.</li>
<li><strong>Radioaktif</strong> → kantong <strong>merah</strong>.</li>
<li><strong>Farmasi dan kimia</strong> – obat kedaluwarsa → kantong <strong>coklat</strong>.</li>
</ul>
HTML,
                    ],
                    [
                        'title' => 'Pemilahan dan Pengelolaan Benda Tajam',
                        'type' => 'teks',
                        'minutes' => 15,
                        'summary' => 'Pemilahan di sumber, penggunaan safety box, dan alur pengelolaan limbah.',
                        'content' => <<<'HTML'
<h2>Pemilahan di Sumber</h2>
<p>Limbah dipilah <strong>di tempat limbah dihasilkan</strong> (mis. di troli tindakan atau ruang pasien), bukan di tempat penampungan.</p>
<h2>Benda Tajam</h2>
<ul>
<li>Letakkan safety box dekat area tindakan.</li>
<li><strong>Jangan menutup kembali jarum (no recapping)</strong> dengan dua tangan.</li>
<li>Tutup dan ganti safety box bila sudah terisi <strong>3/4 bagian</strong>.</li>
</ul>
<h2>Alur Pengelolaan</h2>
<p>Pemilahan → pewadahan → pengangkutan internal dengan troli tertutup → penyimpanan sementara (TPS) → pengolahan/pemusnahan (insinerator atau autoclave) oleh pihak berizin.</p>
HTML,
                    ],
                    [
                        'title' => 'Penanganan Pajanan dan Tumpahan',
                        'type' => 'kasus',
                        'minutes' => 20,
                        'summary' => 'Langkah setelah tertusuk jarum dan penanganan tumpahan darah.',
                        'content' => <<<'HTML'
<h2>Tertusuk Jarum Bekas Pasien</h2>
<ol>
<li>Segera cuci luka dengan sabun dan air mengalir. Jangan dipencet atau dihisap.</li>
<li>Bila terpercik ke mata atau mulut, bilas dengan air mengalir.</li>
<li>Laporkan ke atasan dan Tim PPI.</li>
<li>Evaluasi status sumber pajanan (HIV, hepatitis B, hepatitis C).</li>
<li>Profilaksis pasca pajanan (PPP) HIV diberikan sesegera mungkin, idealnya <strong>&lt; 4 jam</strong> dan tidak lebih dari <strong>72 jam</strong>.</li>
</ol>
<h2>Tumpahan Darah</h2>
<p>Gunakan <em>spill kit</em>: pakai APD, serap tumpahan dengan bahan penyerap, bersihkan, lalu disinfeksi dengan larutan klorin 0,5%. Buang bahan bekas ke kantong kuning.</p>
<h2>Studi Kasus</h2>
<p>Perawat D tertusuk jarum setelah menyuntik pasien dengan status hepatitis B yang tidak diketahui. Susun langkah penanganan yang tepat sesuai prosedur di atas.</p>
HTML,
                    ],
                ],
                'questions' => [
                    [
                        'level' => 'mudah',
                        'text' => 'Warna kantong untuk limbah infeksius adalah ...',
                        'options' => ['Hitam', 'Kuning', 'Ungu', 'Coklat'],
                        'answer' => 1,
                        'explanation' => 'Limbah infeksius ditampung dalam kantong kuning.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Limbah non-infeksius seperti kertas bungkus dan sisa makanan dibuang ke kantong berwarna ...',
                        'options' => ['Kuning', 'Merah', 'Hitam', 'Ungu'],
                        'answer' => 2,
                        'explanation' => 'Limbah domestik/non-infeksius ditampung dalam kantong hitam.',
                    ],
                    [
                        'level' => 'mudah',
                        'text' => 'Jarum suntik bekas pakai harus dibuang ke ...',
                        'options' => [
                            'Kantong kuning',
                            'Kantong hitam',
                            'Tempat sampah biasa',
                            'Wadah tahan tusuk (safety box)',
                        ],
                        'answer' => 3,
                        'explanation' => 'Benda tajam dapat menembus kantong plastik sehingga harus dibuang ke wadah tahan tusuk.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Safety box harus ditutup dan diganti bila sudah terisi ...',
                        'options' => ['Penuh sampai atas', '3/4 bagian', '1/4 bagian', '1/2 bagian'],
                        'answer' => 1,
                        'explanation' => 'Mengisi lebih dari 3/4 meningkatkan risiko tertusuk saat memasukkan jarum.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Tindakan yang dilarang setelah menyuntik pasien adalah ...',
                        'options' => [
                            'Membuang jarum langsung ke safety box',
                            'Menutup kembali jarum dengan dua tangan (recapping)',
                            'Menempatkan safety box dekat area tindakan',
                            'Melapor bila tertusuk jarum',
                        ],
                        'answer' => 1,
                        'explanation' => 'Recapping dengan dua tangan adalah penyebab utama cedera tertusuk jarum.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Pemilahan limbah fasilitas pelayanan kesehatan seharusnya dilakukan ...',
                        'options' => [
                            'Di tempat penampungan akhir',
                            'Di sumber atau tempat limbah dihasilkan',
                            'Oleh petugas kebersihan di TPS',
                            'Setelah limbah diangkut keluar rumah sakit',
                        ],
                        'answer' => 1,
                        'explanation' => 'Pemilahan di sumber mencegah limbah infeksius tercampur dengan limbah domestik.',
                    ],
                    [
                        'level' => 'sulit',
                        'text' => 'Kantong berwarna ungu digunakan untuk menampung limbah ...',
                        'options' => ['Radioaktif', 'Domestik', 'Farmasi', 'Sitotoksik'],
                        'answer' => 3,
                        'explanation' => 'Limbah sitotoksik (mis. sisa kemoterapi) ditampung dalam kantong ungu.',
                    ],
                    [
                        'level' => 'sedang',
                        'text' => 'Tindakan pertama yang harus dilakukan perawat setelah tertusuk jarum bekas pasien adalah ...',
                        'options' => [
                            'Menghisap luka tusukan',
                            'Memencet luka agar darah keluar sebanyak mungkin',
                            'Segera mencuci luka dengan sabun dan air mengalir',
                            'Mengabaikannya bila luka kecil',
                        ],
                        'answer' => 2,
                        'explanation' => 'Luka dicuci dengan sabun dan air mengalir tanpa dipencet atau dihisap.',
                    ],
                    [
                        'level' => 'sulit',
                        'text' => 'Profilaksis pasca pajanan (PPP) HIV setelah tertusuk jarum sebaiknya diberikan ...',
                        'options' => [
                            'Sesegera mungkin, idealnya kurang dari 4 jam dan tidak lebih dari 72 jam setelah pajanan',
                            'Satu minggu setelah pajanan',
                            'Satu bulan setelah pajanan',
                            'Setelah hasil tes HIV 3 bulan keluar',
                        ],
                        'answer' => 0,
                        'explanation' => 'Efektivitas PPP menurun seiring waktu dan tidak dianjurkan setelah 72 jam.',
                    ],
                    [
                        'level' => 'sulit',
                        'text' => 'Tumpahan darah di lantai ruang perawatan sebaiknya ditangani dengan cara ...',
                        'options' => [
                            'Dilap dengan kain pel biasa',
                            'Menggunakan spill kit: memakai APD, menyerap tumpahan, lalu disinfeksi dengan larutan klorin 0,5%',
                            'Ditutup dengan koran',
                            'Dibiarkan mengering sendiri',
                        ],
                        'answer' => 1,
                        'explanation' => 'Spill kit dan disinfeksi klorin 0,5% mencegah penularan patogen melalui darah.',
                    ],
                ],
            ],
        ];
    }
}
