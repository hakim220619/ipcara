<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Award,
    CheckCircle2,
    Clock,
    BookOpen,
    Lightbulb,
    Sparkles,
    XCircle,
} from '@lucide/vue';
import { computed } from 'vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import ProgressBar from '@/components/ipcara/ProgressBar.vue';
import StatRing from '@/components/ipcara/StatRing.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { index as materiIndex, show as materiShow } from '@/routes/materi';
import { result as preTestResult } from '@/routes/pre-test';

type Level = 'rendah' | 'sedang' | 'tinggi';

const props = defineProps<{
    result: {
        correct: number;
        total: number;
        total_points: number;
        max_points: number;
        percent: number;
        passing_score: number;
        is_passed: boolean;
        duration_seconds: number | null;
        finished_at: string | null;
    };
    topics: {
        id: number;
        code: string;
        title: string;
        correct: number | null;
        total: number | null;
        score: number;
        percent: number;
        level: Level;
        recommended: boolean;
        category_note: string | null;
        start_material: { title: string; slug: string } | null;
    }[];
    criteria: {
        level: Level;
        label: string;
        min_score: number;
        max_score: number;
    }[];
    overallLevel: Level;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Hasil Pre-Test', href: preTestResult() }],
    },
});

const levelColor: Record<Level, string> = {
    rendah: 'var(--color-destructive)',
    sedang: 'var(--color-warning)',
    tinggi: 'var(--color-success)',
};

const levelMeta = computed(() => {
    const meta: Record<Level, { label: string; color: string }> = {
        rendah: { label: 'Rendah', color: levelColor.rendah },
        sedang: { label: 'Sedang', color: levelColor.sedang },
        tinggi: { label: 'Tinggi', color: levelColor.tinggi },
    };
    props.criteria.forEach((c) => (meta[c.level].label = c.label));
    return meta;
});

const rangeLabel = (c: { min_score: number; max_score: number }) =>
    `(${Math.round(c.min_score)}–${Math.floor(c.max_score)}%)`;

const recommendedTopics = computed(() =>
    props.topics
        .filter((t) => t.recommended)
        .sort((a, b) => a.percent - b.percent),
);

// Rencana belajar adaptif: <60% perlu dipelajari, 60–79% perlu penguatan, ≥80% sudah dikuasai.
const planGroups = computed(() =>
    (
        [
            {
                level: 'rendah',
                title: 'Perlu Dipelajari',
                rule: 'Nilai < 60%',
                hint: 'Prioritas utama. Pelajari dari materi dasar.',
                icon: BookOpen,
                box: 'border-rose-500/40 bg-rose-500/5',
                chip: 'bg-rose-500 text-white',
                text: 'text-rose-600 dark:text-rose-400',
            },
            {
                level: 'sedang',
                title: 'Perlu Penguatan',
                rule: 'Nilai 60–79%',
                hint: 'Prioritas berikutnya. Perkuat pada materi menengah.',
                icon: Lightbulb,
                box: 'border-amber-500/40 bg-amber-500/5',
                chip: 'bg-amber-500 text-white',
                text: 'text-amber-600 dark:text-amber-400',
            },
            {
                level: 'tinggi',
                title: 'Sudah Dikuasai',
                rule: 'Nilai ≥ 80%',
                hint: 'Tidak menjadi prioritas. Boleh dibaca untuk pendalaman.',
                icon: CheckCircle2,
                box: 'border-emerald-500/40 bg-emerald-500/5',
                chip: 'bg-emerald-500 text-white',
                text: 'text-emerald-600 dark:text-emerald-400',
            },
        ] as const
    ).map((group) => ({
        ...group,
        topics: props.topics
            .filter((t) => t.level === group.level)
            .sort((a, b) => a.percent - b.percent),
    })),
);

const priorityNumber = (topicId: number) =>
    recommendedTopics.value.findIndex((t) => t.id === topicId) + 1;

const countByLevel = (level: Level) =>
    props.topics.filter((t) => t.level === level).length;

const duration = computed(() => {
    const s = props.result.duration_seconds ?? 0;
    return `${Math.floor(s / 60)} mnt ${s % 60} dtk`;
});

const formatNumber = (value: number) =>
    Number.isInteger(value) ? String(value) : value.toFixed(1);

const formatPercent = (value: number) => `${formatNumber(value)}%`;
</script>

<template>
    <Head title="Hasil Pre-Test" />

    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div>
            <span
                class="bg-accent text-accent-foreground inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium"
            >
                <Award class="size-3.5" /> Langkah 2 dari 3
            </span>
            <h1 class="mt-2 text-2xl font-bold tracking-tight">
                Hasil Pre-Test Anda
            </h1>
            <p v-if="result.finished_at" class="text-muted-foreground text-sm">
                Diselesaikan pada {{ result.finished_at }}
            </p>
        </div>

        <GlowCard class="md:p-7">
            <div
                class="flex flex-col items-center gap-6 md:flex-row md:items-center md:gap-10"
            >
                <StatRing
                    :value="result.percent"
                    :size="168"
                    :stroke="14"
                    :color="levelMeta[props.overallLevel].color"
                    label="Persentase keseluruhan"
                />
                <div
                    class="grid w-full flex-1 gap-3 sm:grid-cols-2 xl:grid-cols-4"
                >
                    <div class="bg-secondary/60 rounded-2xl p-4">
                        <p class="text-muted-foreground text-xs">Total Nilai</p>
                        <p class="mt-1 text-3xl font-bold tabular-nums">
                            {{ formatNumber(result.total_points)
                            }}<span class="text-muted-foreground text-lg"
                                >/{{ result.max_points }}</span
                            >
                        </p>
                        <p class="text-muted-foreground text-xs">
                            jumlah nilai {{ topics.length }} topik
                        </p>
                    </div>
                    <div class="bg-secondary/60 rounded-2xl p-4">
                        <p class="text-muted-foreground text-xs">
                            Persentase Keseluruhan
                        </p>
                        <p class="mt-1 text-3xl font-bold tabular-nums">
                            {{ formatPercent(result.percent) }}
                        </p>
                        <p
                            class="text-xs font-medium"
                            :style="{
                                color: levelMeta[props.overallLevel].color,
                            }"
                        >
                            Kemampuan {{ levelMeta[props.overallLevel].label }}
                        </p>
                    </div>
                    <div class="bg-secondary/60 rounded-2xl p-4">
                        <p class="text-muted-foreground text-xs">
                            Jawaban Benar
                        </p>
                        <p class="mt-1 text-3xl font-bold tabular-nums">
                            {{ result.correct
                            }}<span class="text-muted-foreground text-lg"
                                >/{{ result.total }}</span
                            >
                        </p>
                        <p class="text-muted-foreground text-xs">soal</p>
                    </div>
                    <div class="bg-secondary/60 rounded-2xl p-4">
                        <p class="text-muted-foreground text-xs">Status</p>
                        <p
                            class="mt-1 flex items-center gap-1.5 text-lg font-semibold"
                            :class="
                                result.is_passed
                                    ? 'text-success'
                                    : 'text-destructive'
                            "
                        >
                            <CheckCircle2
                                v-if="result.is_passed"
                                class="size-5"
                            />
                            <XCircle v-else class="size-5" />
                            {{ result.is_passed ? 'Tuntas' : 'Belum tuntas' }}
                        </p>
                        <p
                            class="text-muted-foreground mt-1 flex items-center gap-1 text-xs"
                        >
                            <Clock class="size-3" /> {{ duration }} · KKM
                            {{ result.passing_score }}%
                        </p>
                    </div>
                </div>
            </div>
        </GlowCard>

        <div class="grid gap-5 lg:grid-cols-[1.6fr_1fr]">
            <GlowCard
                title="Penilaian per Topik"
                subtitle="Nilai tiap topik 0–100 dan persentasenya, beserta total seluruh topik"
            >
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-sm">
                        <thead>
                            <tr
                                class="text-muted-foreground border-b text-left text-xs"
                            >
                                <th class="py-2 pr-3 font-medium">Topik</th>
                                <th class="py-2 pr-3 text-center font-medium">
                                    Benar
                                </th>
                                <th class="py-2 pr-3 text-center font-medium">
                                    Nilai
                                </th>
                                <th class="w-40 py-2 pr-3 font-medium">
                                    Persentase
                                </th>
                                <th class="py-2 font-medium">Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="topic in topics"
                                :key="topic.id"
                                class="border-b"
                            >
                                <td class="py-3 pr-3">
                                    <p class="font-medium">{{ topic.title }}</p>
                                    <Badge
                                        v-if="topic.recommended"
                                        class="mt-1 text-[10px]"
                                        >Direkomendasikan</Badge
                                    >
                                </td>
                                <td
                                    class="text-muted-foreground py-3 pr-3 text-center tabular-nums"
                                >
                                    <template v-if="topic.total"
                                        >{{ topic.correct }}/{{
                                            topic.total
                                        }}</template
                                    >
                                    <template v-else>-</template>
                                </td>
                                <td
                                    class="py-3 pr-3 text-center text-base font-bold tabular-nums"
                                    :style="{
                                        color: levelMeta[topic.level].color,
                                    }"
                                >
                                    {{ formatNumber(topic.score) }}
                                </td>
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2">
                                        <ProgressBar
                                            class="flex-1"
                                            :value="topic.percent"
                                            :show-value="false"
                                            :color="
                                                levelMeta[topic.level].color
                                            "
                                        />
                                        <span
                                            class="w-12 text-right text-xs tabular-nums"
                                            >{{
                                                formatPercent(topic.percent)
                                            }}</span
                                        >
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span
                                        class="text-xs font-medium"
                                        :style="{
                                            color: levelMeta[topic.level].color,
                                        }"
                                        >{{
                                            levelMeta[topic.level].label
                                        }}</span
                                    >
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-secondary/60 font-semibold">
                                <td class="rounded-l-xl px-3 py-3">
                                    Total Keseluruhan
                                </td>
                                <td class="py-3 pr-3 text-center tabular-nums">
                                    {{ result.correct }}/{{ result.total }}
                                </td>
                                <td
                                    class="py-3 pr-3 text-center text-base tabular-nums"
                                >
                                    {{ formatNumber(result.total_points)
                                    }}<span
                                        class="text-muted-foreground text-xs font-normal"
                                        >/{{ result.max_points }}</span
                                    >
                                </td>
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2">
                                        <ProgressBar
                                            class="flex-1"
                                            :value="result.percent"
                                            :show-value="false"
                                            :color="
                                                levelMeta[props.overallLevel]
                                                    .color
                                            "
                                        />
                                        <span
                                            class="w-12 text-right text-xs tabular-nums"
                                            >{{
                                                formatPercent(result.percent)
                                            }}</span
                                        >
                                    </div>
                                </td>
                                <td class="rounded-r-xl py-3 pr-3">
                                    <span
                                        class="text-xs"
                                        :style="{
                                            color: levelMeta[props.overallLevel]
                                                .color,
                                        }"
                                        >{{
                                            levelMeta[props.overallLevel].label
                                        }}</span
                                    >
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <p class="text-muted-foreground text-xs">
                    Persentase keseluruhan = total nilai ÷ nilai maksimum ({{
                        result.max_points
                    }}) × 100%.
                </p>
                <div class="text-muted-foreground flex flex-wrap gap-4 text-xs">
                    <span
                        v-for="c in criteria"
                        :key="c.level"
                        class="inline-flex items-center gap-1.5"
                    >
                        <span
                            class="size-2.5 rounded-full"
                            :style="{ background: levelColor[c.level] }"
                        ></span>
                        {{ c.label }} {{ rangeLabel(c) }}
                    </span>
                </div>
            </GlowCard>

            <GlowCard
                title="Hasil Analisis Adaptif"
                subtitle="Kategori setiap topik berdasarkan nilai pre-test"
            >
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-2xl bg-rose-500/10 p-3">
                        <p
                            class="text-2xl font-bold text-rose-600 tabular-nums dark:text-rose-400"
                        >
                            {{ countByLevel('rendah') }}
                        </p>
                        <p
                            class="text-muted-foreground text-[11px] leading-tight"
                        >
                            Perlu dipelajari
                        </p>
                    </div>
                    <div class="rounded-2xl bg-amber-500/10 p-3">
                        <p
                            class="text-2xl font-bold text-amber-600 tabular-nums dark:text-amber-400"
                        >
                            {{ countByLevel('sedang') }}
                        </p>
                        <p
                            class="text-muted-foreground text-[11px] leading-tight"
                        >
                            Perlu penguatan
                        </p>
                    </div>
                    <div class="rounded-2xl bg-emerald-500/10 p-3">
                        <p
                            class="text-2xl font-bold text-emerald-600 tabular-nums dark:text-emerald-400"
                        >
                            {{ countByLevel('tinggi') }}
                        </p>
                        <p
                            class="text-muted-foreground text-[11px] leading-tight"
                        >
                            Sudah dikuasai
                        </p>
                    </div>
                </div>

                <ol v-if="recommendedTopics.length" class="flex flex-col gap-2">
                    <li
                        v-for="(topic, i) in recommendedTopics"
                        :key="topic.id"
                        class="bg-secondary/50 flex items-center gap-3 rounded-xl px-4 py-3"
                    >
                        <span
                            class="bg-primary text-primary-foreground flex size-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold"
                            >{{ i + 1 }}</span
                        >
                        <span class="flex-1 text-sm font-medium">{{
                            topic.title
                        }}</span>
                        <span
                            class="text-sm font-bold tabular-nums"
                            :style="{ color: levelMeta[topic.level].color }"
                            >{{ formatPercent(topic.percent) }}</span
                        >
                    </li>
                </ol>
                <div
                    v-else
                    class="bg-accent text-accent-foreground flex items-start gap-3 rounded-xl p-4 text-sm"
                >
                    <Sparkles class="mt-0.5 size-4 shrink-0" />
                    Luar biasa! Semua topik sudah dikuasai (≥ 80%). Tidak ada
                    topik prioritas; materi tetap dapat dibuka untuk pendalaman.
                </div>

                <Button
                    size="lg"
                    class="mt-auto w-full shadow-[0_0_18px_var(--color-glow)]"
                    as-child
                >
                    <Link
                        :href="
                            recommendedTopics[0]?.start_material
                                ? materiShow(
                                      recommendedTopics[0].start_material.slug,
                                  )
                                : materiIndex()
                        "
                    >
                        {{
                            recommendedTopics.length
                                ? 'Mulai dari Prioritas 1'
                                : 'Lihat Materi'
                        }}
                        <ArrowRight />
                    </Link>
                </Button>
            </GlowCard>
        </div>

        <!-- Rencana belajar adaptif -->
        <GlowCard
            title="Rencana Belajar Adaptif"
            subtitle="Urutan belajar disusun otomatis dari hasil pre-test Anda"
            class="md:p-7"
        >
            <div class="grid gap-4 lg:grid-cols-3">
                <section
                    v-for="group in planGroups"
                    :key="group.level"
                    class="flex flex-col gap-3 rounded-2xl border-2 p-4"
                    :class="group.box"
                >
                    <div class="flex items-start gap-3">
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl"
                            :class="group.chip"
                        >
                            <component :is="group.icon" class="size-5" />
                        </span>
                        <div>
                            <p class="font-bold" :class="group.text">
                                {{ group.title }}
                            </p>
                            <p class="text-muted-foreground text-xs">
                                {{ group.rule }} · {{ group.hint }}
                            </p>
                        </div>
                    </div>

                    <ul v-if="group.topics.length" class="flex flex-col gap-2">
                        <li
                            v-for="topic in group.topics"
                            :key="topic.id"
                            class="bg-card flex flex-col gap-1.5 rounded-xl border p-3"
                        >
                            <div
                                class="flex items-center justify-between gap-2"
                            >
                                <span
                                    class="flex items-center gap-2 font-medium"
                                >
                                    <span
                                        v-if="priorityNumber(topic.id)"
                                        class="flex size-5 items-center justify-center rounded-full text-[10px] font-bold"
                                        :class="group.chip"
                                        >{{ priorityNumber(topic.id) }}</span
                                    >
                                    {{ topic.title }}
                                </span>
                                <span
                                    class="text-sm font-bold tabular-nums"
                                    :class="group.text"
                                    >{{ formatPercent(topic.percent) }}</span
                                >
                            </div>
                            <Link
                                v-if="topic.start_material"
                                :href="materiShow(topic.start_material.slug)"
                                class="text-muted-foreground hover:text-primary inline-flex items-center gap-1 text-xs"
                            >
                                {{
                                    group.level === 'tinggi'
                                        ? 'Pendalaman (opsional):'
                                        : 'Mulai dari:'
                                }}
                                <span class="font-medium">{{
                                    topic.start_material.title
                                }}</span>
                                <ArrowRight class="size-3" />
                            </Link>
                        </li>
                    </ul>
                    <p
                        v-else
                        class="text-muted-foreground rounded-xl border border-dashed p-3 text-center text-xs"
                    >
                        Tidak ada topik pada kategori ini.
                    </p>
                </section>
            </div>
        </GlowCard>
    </div>
</template>
