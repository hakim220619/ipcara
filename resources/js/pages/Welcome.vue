<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Activity,
    BookOpenCheck,
    BrainCircuit,
    ShieldPlus,
    Target,
} from '@lucide/vue';
import BarChart from '@/components/ipcara/BarChart.vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import ModuleTimeline from '@/components/ipcara/ModuleTimeline.vue';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';

const features = [
    {
        icon: Target,
        title: 'Pre-Test Diagnostik',
        text: 'Memetakan kemampuan awal mahasiswa pada tiap topik PPI.',
    },
    {
        icon: BrainCircuit,
        title: 'Rekomendasi Adaptif',
        text: 'Materi disesuaikan dengan level: dasar, menengah, atau lanjut.',
    },
    {
        icon: BookOpenCheck,
        title: 'Materi, Video & Kasus',
        text: 'Belajar lewat teks, video, dan studi kasus klinis nyata.',
    },
    {
        icon: Activity,
        title: 'Dashboard Perkembangan',
        text: 'Pantau skor PPI, kepatuhan belajar, dan progres modul.',
    },
];

const demoWeekly = [
    { label: 'Sen', value: 3 },
    { label: 'Sel', value: 5 },
    { label: 'Rab', value: 4 },
    { label: 'Kam', value: 7 },
    { label: 'Jum', value: 6 },
    { label: 'Sab', value: 8 },
    { label: 'Min', value: 9 },
];

const demoTimeline = [
    { title: 'Hand Hygiene', status: 'selesai' as const },
    { title: 'APD', status: 'selesai' as const },
    { title: 'Sterilisasi', status: 'aktif' as const },
    { title: 'Isolasi', status: 'belum' as const },
    { title: 'Limbah', status: 'belum' as const },
];
</script>

<template>
    <Head title="Selamat Datang" />

    <div class="bg-background text-foreground bg-grid-glow min-h-screen">
        <div class="relative isolate overflow-hidden">
            <div
                class="absolute top-10 -right-24 -z-10 size-[26rem] rounded-full bg-[hsl(212_90%_60%/0.15)] blur-3xl"
                aria-hidden="true"
            ></div>

            <header
                class="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-5 sm:px-6"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="bg-primary text-primary-foreground flex size-9 items-center justify-center rounded-lg shadow-[0_0_16px_var(--color-glow)]"
                    >
                        <ShieldPlus class="size-5" />
                    </div>
                    <span class="text-lg font-bold tracking-wide">IPCARA</span>
                </div>
                <nav class="flex items-center gap-2 text-sm">
                    <Button v-if="$page.props.auth.user" as-child>
                        <Link :href="dashboard()">Dashboard</Link>
                    </Button>
                    <template v-else>
                        <Button variant="ghost" as-child>
                            <Link :href="login()">Masuk</Link>
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="register()">Daftar</Link>
                        </Button>
                    </template>
                </nav>
            </header>

            <section
                class="mx-auto grid w-full max-w-6xl items-center gap-8 px-4 pb-12 sm:px-6 lg:grid-cols-[1.1fr_1fr] lg:gap-12 lg:pt-4 lg:pb-20"
            >
                <img
                    src="/images/hero-perawat.webp"
                    alt="Perawat menggunakan aplikasi IPCARA di tablet"
                    width="960"
                    height="768"
                    class="mx-auto w-full max-w-lg lg:max-w-none"
                    fetchpriority="high"
                />

                <div
                    class="flex flex-col gap-6 lg:rounded-3xl lg:bg-white/70 lg:p-9 lg:shadow-[0_24px_60px_-24px_rgba(15,64,107,0.35)] lg:ring-1 lg:ring-white lg:backdrop-blur-md"
                >
                    <h1
                        class="text-primary glow-text text-5xl font-extrabold tracking-tight sm:text-6xl"
                    >
                        IPCARA
                    </h1>
                    <p class="text-xl font-semibold sm:text-2xl">
                        Infection Prevention and Control
                        <br class="hidden sm:block" />
                        Adaptive Recommendation Application for Nursing Students
                    </p>
                    <p class="text-muted-foreground max-w-prose">
                        Sistem pembelajaran berbasis web yang menganalisis hasil
                        pre-test, merekomendasikan materi PPI sesuai kemampuan,
                        lalu memantau perkembangan belajar hingga post-test.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <Button
                            size="lg"
                            class="shadow-[0_0_20px_var(--color-glow)]"
                            as-child
                        >
                            <Link
                                :href="
                                    $page.props.auth.user
                                        ? dashboard()
                                        : register()
                                "
                                >Mulai Belajar</Link
                            >
                        </Button>
                        <Button size="lg" variant="outline" as-child>
                            <Link :href="login()">Sudah punya akun</Link>
                        </Button>
                    </div>
                </div>
            </section>
        </div>

        <main class="mx-auto w-full max-w-6xl px-4 pb-16 sm:px-6">
            <section class="py-6">
                <h2 class="mb-4 text-center text-lg font-semibold sm:text-left">
                    Pratinjau Dashboard Perkembangan
                </h2>
                <GlowCard title="Kepatuhan Mingguan" subtitle="7 hari terakhir">
                    <BarChart :data="demoWeekly" :height="100" />
                </GlowCard>
            </section>

            <section class="grid gap-4 py-6 sm:grid-cols-2 lg:grid-cols-4">
                <GlowCard v-for="f in features" :key="f.title" class="gap-3">
                    <div
                        class="bg-accent text-primary flex size-10 items-center justify-center rounded-xl"
                    >
                        <component :is="f.icon" class="size-5" />
                    </div>
                    <h3 class="font-semibold">{{ f.title }}</h3>
                    <p class="text-muted-foreground text-sm">{{ f.text }}</p>
                </GlowCard>
            </section>

            <section class="py-8">
                <GlowCard
                    title="Alur Belajar IPCARA"
                    subtitle="Login → Pre-Test → Analisis → Rekomendasi → Belajar → Latihan → Remedial → Post-Test → Dashboard"
                >
                    <ol
                        class="grid gap-3 text-sm sm:grid-cols-3 lg:grid-cols-5"
                    >
                        <li
                            v-for="(step, i) in [
                                'Pre-test diagnostik',
                                'Analisis kemampuan per topik',
                                'Rekomendasi materi adaptif',
                                'Belajar & latihan (remedial bila perlu)',
                                'Post-test & dashboard perkembangan',
                            ]"
                            :key="step"
                            class="bg-secondary/60 flex items-start gap-3 rounded-xl p-3"
                        >
                            <span
                                class="bg-primary text-primary-foreground flex size-6 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                >{{ i + 1 }}</span
                            >
                            <span>{{ step }}</span>
                        </li>
                    </ol>
                    <div class="border-border/60 border-t pt-4">
                        <p
                            class="text-muted-foreground mb-3 text-xs font-medium"
                        >
                            Contoh progres modul mahasiswa
                        </p>
                        <ModuleTimeline :steps="demoTimeline" />
                    </div>
                </GlowCard>
            </section>
        </main>

        <footer
            class="text-muted-foreground mx-auto w-full max-w-6xl px-4 py-6 text-center text-xs sm:px-6"
        >
            IPCARA · Aplikasi pembelajaran berbasis web · Pencegahan dan
            Pengendalian Infeksi
        </footer>
    </div>
</template>
