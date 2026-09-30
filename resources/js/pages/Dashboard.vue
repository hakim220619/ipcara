<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    AlertTriangle,
    ArrowRight,
    BookOpen,
    CircleCheckBig,
    ClipboardCheck,
    KeyRound,
    PlayCircle,
    Sparkles,
    UserPen,
} from '@lucide/vue';
import BarChart from '@/components/ipcara/BarChart.vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import LineChart from '@/components/ipcara/LineChart.vue';
import ModuleTimeline, {
    type TimelineStep,
} from '@/components/ipcara/ModuleTimeline.vue';
import ProgressBar from '@/components/ipcara/ProgressBar.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { index as materiIndex, show as materiShow } from '@/routes/materi';
import { take as latihanTake } from '@/routes/materi/latihan';
import {
    index as preTestIndex,
    result as preTestResult,
} from '@/routes/pre-test';
import { edit as profileEdit } from '@/routes/profile';
import { edit as securityEdit } from '@/routes/security';

type Point = { label: string; value: number };
type MaterialRef = {
    id: number;
    title: string;
    slug: string;
    type: string;
    level: string;
};

defineProps<{
    stats: {
        preTestDone: boolean;
    };
    moduleCompletion: {
        id: number;
        title: string;
        code: string;
        percent: number;
        level: string | null;
        score: number | null;
    }[];
    weeklyActivity: Point[];
    scoreHistory: Point[];
    recommendations: {
        id: number;
        material: MaterialRef | null;
        topic: string | null;
        reason: string | null;
        status: string;
    }[];
    remedials: {
        id: number;
        material: string | null;
        slug: string | null;
        score: number;
        status: 'penguatan' | 'remedial';
    }[];
    preTestSummary: {
        total_points: number;
        max_points: number;
        correct: number;
        total: number;
        percent: number;
        is_passed: boolean;
        finished_at: string | null;
        recommended_topics: string[];
    } | null;
    nextMaterial: { title: string; slug: string } | null;
    timeline: TimelineStep[];
}>();

defineOptions({
    layout: { breadcrumbs: [{ title: 'Dashboard', href: dashboard() }] },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const initials = computed(() =>
    user.value.name
        .split(' ')
        .map((w) => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase(),
);
const profileRows = computed(() => [
    { label: 'Nama', value: user.value.name },
    { label: 'NIM', value: user.value.nim },
    {
        label: 'Semester',
        value: user.value.semester ? `Semester ${user.value.semester}` : null,
    },
    { label: 'Username', value: user.value.username },
    { label: 'Email', value: user.value.email },
    { label: 'Program studi', value: user.value.program_studi },
]);

const levelColor: Record<string, string> = {
    dasar: 'var(--color-chart-5)',
    menengah: 'var(--color-chart-3)',
    lanjut: 'var(--color-chart-2)',
};

const barColors = [
    'var(--color-chart-1)',
    'var(--color-chart-3)',
    'var(--color-chart-2)',
    'var(--color-chart-4)',
    'var(--color-chart-5)',
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="bg-grid-glow flex h-full flex-1 flex-col gap-5 p-4 md:p-6">
        <!-- Sapaan + CTA -->
        <section
            class="flex flex-col justify-between gap-4 md:flex-row md:items-center"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    Hai,
                    <span class="text-primary glow-text">{{
                        $page.props.auth.user.name
                    }}</span
                    >!
                </h1>
                <p class="text-muted-foreground text-sm">
                    {{
                        stats.preTestDone
                            ? 'Berikut rekomendasi belajar yang disesuaikan dengan hasil pre-test Anda.'
                            : 'Mulai dengan pre-test agar sistem dapat menyusun rekomendasi materi untuk Anda.'
                    }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <Button
                    v-if="!stats.preTestDone"
                    class="shadow-[0_0_18px_var(--color-glow)]"
                    as-child
                >
                    <Link :href="preTestIndex()"
                        ><ClipboardCheck /> Mulai Pre-Test</Link
                    >
                </Button>
                <Button
                    v-else-if="nextMaterial"
                    class="shadow-[0_0_18px_var(--color-glow)]"
                    as-child
                >
                    <Link :href="materiShow(nextMaterial.slug)"
                        ><PlayCircle /> Lanjutkan:
                        {{ nextMaterial.title }}</Link
                    >
                </Button>
                <Button variant="outline" as-child>
                    <Link :href="materiIndex()"><BookOpen /> Semua Materi</Link>
                </Button>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-[22rem_1fr]">
            <GlowCard title="Profil Saya">
                <template #action>
                    <Badge variant="secondary">Mahasiswa</Badge>
                </template>
                <div class="flex items-center gap-3">
                    <span
                        class="bg-primary text-primary-foreground flex size-12 shrink-0 items-center justify-center rounded-full text-base font-bold"
                        >{{ initials }}</span
                    >
                    <div class="min-w-0">
                        <p class="truncate font-semibold">{{ user.name }}</p>
                        <p class="text-muted-foreground truncate text-xs">
                            {{ user.nim ?? 'NIM belum diisi' }}
                        </p>
                    </div>
                </div>
                <dl class="divide-y text-sm">
                    <div
                        v-for="row in profileRows"
                        :key="row.label"
                        class="flex items-start justify-between gap-3 py-2"
                    >
                        <dt class="text-muted-foreground shrink-0">
                            {{ row.label }}
                        </dt>
                        <dd
                            class="min-w-0 truncate text-right font-medium"
                            :class="
                                row.value ? '' : 'text-muted-foreground italic'
                            "
                        >
                            {{ row.value ?? 'Belum diisi' }}
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-3 py-2">
                        <dt class="text-muted-foreground">Password</dt>
                        <dd class="font-medium tracking-widest">••••••••</dd>
                    </div>
                </dl>
                <div class="mt-auto grid grid-cols-2 gap-2">
                    <Button size="sm" variant="outline" as-child>
                        <Link :href="profileEdit()"
                            ><UserPen /> Ubah profil</Link
                        >
                    </Button>
                    <Button size="sm" variant="outline" as-child>
                        <Link :href="securityEdit()"
                            ><KeyRound /> Ubah password</Link
                        >
                    </Button>
                </div>
            </GlowCard>

            <div
                v-if="preTestSummary"
                class="border-success/40 bg-success/5 flex flex-col gap-4 rounded-2xl border p-5 md:flex-row md:items-center"
            >
                <div
                    class="bg-success/15 text-success flex size-12 shrink-0 items-center justify-center rounded-xl"
                >
                    <CircleCheckBig class="size-6" />
                </div>
                <div class="flex flex-1 flex-col gap-1.5">
                    <p class="font-semibold">Anda sudah melakukan pre-test</p>
                    <p class="text-muted-foreground text-sm">
                        <template v-if="preTestSummary.finished_at"
                            >Diselesaikan pada
                            {{ preTestSummary.finished_at }} ·
                        </template>
                        Total nilai
                        <strong class="text-foreground"
                            >{{ Math.round(preTestSummary.total_points) }}/{{
                                preTestSummary.max_points
                            }}</strong
                        >
                        · Persentase
                        <strong class="text-foreground"
                            >{{ Math.round(preTestSummary.percent) }}%</strong
                        >
                    </p>
                    <div
                        v-if="preTestSummary.recommended_topics.length"
                        class="flex flex-wrap items-center gap-1.5 text-xs"
                    >
                        <span class="text-muted-foreground"
                            >Fokus belajar:</span
                        >
                        <Badge
                            v-for="topic in preTestSummary.recommended_topics"
                            :key="topic"
                            variant="outline"
                            class="border-primary/40 text-primary"
                            >{{ topic }}</Badge
                        >
                    </div>
                </div>
                <Button variant="outline" as-child>
                    <Link :href="preTestResult()"
                        >Lihat hasil pre-test <ArrowRight
                    /></Link>
                </Button>
            </div>
        </section>

        <!-- Baris statistik utama -->
        <section class="grid gap-4 md:grid-cols-2">
            <GlowCard
                title="Kepatuhan Belajar Mingguan"
                subtitle="Jumlah aktivitas 7 hari terakhir"
            >
                <BarChart :data="weeklyActivity" :height="130" />
            </GlowCard>

            <GlowCard
                title="Penyelesaian Modul"
                subtitle="Progres materi per topik PPI"
            >
                <div v-if="moduleCompletion.length" class="flex flex-col gap-3">
                    <ProgressBar
                        v-for="(m, i) in moduleCompletion"
                        :key="m.id"
                        :label="m.title"
                        :value="m.percent"
                        :color="barColors[i % barColors.length]"
                    />
                </div>
                <p v-else class="text-muted-foreground text-sm">
                    Belum ada topik yang dipublikasikan.
                </p>
            </GlowCard>
        </section>

        <!-- Rekomendasi + Peringatan -->
        <section class="grid gap-4 lg:grid-cols-3">
            <GlowCard
                title="Rekomendasi Adaptif"
                subtitle="Materi yang disarankan sistem untuk Anda"
                class="lg:col-span-2"
            >
                <template #action>
                    <Sparkles class="text-primary size-4" />
                </template>
                <ul v-if="recommendations.length" class="flex flex-col gap-2">
                    <li v-for="r in recommendations" :key="r.id">
                        <Link
                            v-if="r.material"
                            :href="materiShow(r.material.slug)"
                            class="bg-secondary/50 hover:bg-accent hover:border-primary/40 flex items-center justify-between gap-3 rounded-xl border border-transparent p-3 transition"
                        >
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">
                                    {{ r.material.title }}
                                </p>
                                <p
                                    class="text-muted-foreground truncate text-xs"
                                >
                                    {{ r.topic }} ·
                                    {{
                                        r.reason ??
                                        'Sesuai hasil penilaian kemampuan'
                                    }}
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <Badge
                                    variant="outline"
                                    :style="{
                                        borderColor:
                                            levelColor[r.material.level],
                                        color: levelColor[r.material.level],
                                    }"
                                    >{{ r.material.level }}</Badge
                                >
                                <ArrowRight
                                    class="text-muted-foreground size-4"
                                />
                            </div>
                        </Link>
                    </li>
                </ul>
                <div
                    v-else
                    class="text-muted-foreground flex flex-col items-start gap-2 text-sm"
                >
                    <p>
                        Belum ada rekomendasi. Rekomendasi akan muncul setelah
                        Anda menyelesaikan pre-test.
                    </p>
                    <Button size="sm" variant="outline" as-child
                        ><Link :href="materiIndex()"
                            >Jelajahi semua materi</Link
                        ></Button
                    >
                </div>
            </GlowCard>

            <GlowCard
                title="Peringatan & Remedial"
                subtitle="Materi yang latihannya belum mastery"
            >
                <ul v-if="remedials.length" class="flex flex-col gap-2">
                    <li v-for="r in remedials" :key="r.id">
                        <Link
                            :href="r.slug ? latihanTake(r.slug) : materiIndex()"
                            class="flex items-start gap-3 rounded-xl border p-3 transition hover:brightness-105"
                            :class="
                                r.status === 'remedial'
                                    ? 'border-destructive/40 bg-destructive/10'
                                    : 'border-warning/40 bg-warning/10'
                            "
                        >
                            <AlertTriangle
                                class="mt-0.5 size-4 shrink-0"
                                :class="
                                    r.status === 'remedial'
                                        ? 'text-destructive'
                                        : 'text-warning'
                                "
                            />
                            <div class="text-sm">
                                <p class="font-medium">{{ r.material }}</p>
                                <p class="text-muted-foreground text-xs">
                                    Skor latihan {{ Math.round(r.score) }} ·
                                    {{
                                        r.status === 'remedial'
                                            ? 'Perlu remedial'
                                            : 'Perlu penguatan'
                                    }}
                                </p>
                            </div>
                        </Link>
                    </li>
                </ul>
                <div
                    v-else
                    class="bg-success/10 border-success/30 text-success rounded-xl border p-3 text-sm"
                >
                    Tidak ada remedial aktif. Pertahankan!
                </div>
            </GlowCard>
        </section>

        <!-- Timeline + Riwayat skor -->
        <section id="progres" class="grid gap-4 lg:grid-cols-3">
            <GlowCard
                title="Modul Pembelajaran Adaptif"
                subtitle="Urutan topik PPI dan posisi Anda saat ini"
                class="lg:col-span-2"
            >
                <ModuleTimeline v-if="timeline.length" :steps="timeline" />
                <p v-else class="text-muted-foreground text-sm">
                    Belum ada modul.
                </p>
            </GlowCard>

            <GlowCard
                title="Riwayat Skor Tes"
                subtitle="Perkembangan nilai dari waktu ke waktu"
            >
                <LineChart
                    v-if="scoreHistory.length"
                    :data="scoreHistory"
                    :height="130"
                />
                <p v-else class="text-muted-foreground text-sm">
                    Belum ada tes yang diselesaikan.
                </p>
            </GlowCard>
        </section>
    </div>
</template>
