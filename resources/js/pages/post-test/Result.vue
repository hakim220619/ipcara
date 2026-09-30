<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    ArrowRight,
    Award,
    CheckCircle2,
    Clock,
    TrendingUp,
    XCircle,
} from '@lucide/vue';
import { computed } from 'vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import ProgressBar from '@/components/ipcara/ProgressBar.vue';
import StatRing from '@/components/ipcara/StatRing.vue';
import { Button } from '@/components/ui/button';
import LearningReport, {
    type LearningReportData,
} from '@/components/ipcara/LearningReport.vue';
import { result as postTestResult } from '@/routes/post-test';

type Level = 'rendah' | 'sedang' | 'tinggi';

const props = defineProps<{
    result: {
        correct: number;
        total: number;
        total_points: number;
        max_points: number;
        percent: number;
        pre_percent: number | null;
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
        pre_score: number | null;
    }[];
    criteria: {
        level: Level;
        label: string;
        min_score: number;
        max_score: number;
    }[];
    overallLevel: Level;
    report: LearningReportData;
}>();

function scrollToReport() {
    document
        .getElementById('laporan')
        ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Hasil Post-Test', href: postTestResult() }],
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

const delta = (post: number, pre: number | null) =>
    pre === null ? null : Math.round((post - pre) * 10) / 10;

const overallDelta = computed(() =>
    delta(props.result.percent, props.result.pre_percent),
);

const deltaClass = (d: number | null) =>
    d === null || d === 0
        ? 'text-muted-foreground'
        : d > 0
          ? 'text-success'
          : 'text-destructive';

const deltaText = (d: number | null) =>
    d === null ? '-' : `${d > 0 ? '+' : ''}${formatNumber(d)}`;

const duration = computed(() => {
    const s = props.result.duration_seconds ?? 0;
    return `${Math.floor(s / 60)} mnt ${s % 60} dtk`;
});

const formatNumber = (value: number) =>
    Number.isInteger(value) ? String(value) : value.toFixed(1);

const formatPercent = (value: number) => `${formatNumber(value)}%`;
</script>

<template>
    <Head title="Hasil Post-Test" />

    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div>
            <span
                class="bg-accent text-accent-foreground inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium"
            >
                <Award class="size-3.5" /> Langkah 3 dari 3 · Evaluasi Akhir
            </span>
            <h1 class="mt-2 text-2xl font-bold tracking-tight">
                Hasil Post-Test Anda
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
                    <table class="w-full min-w-[680px] text-sm">
                        <thead>
                            <tr
                                class="text-muted-foreground border-b text-left text-xs"
                            >
                                <th class="py-2 pr-3 font-medium">Topik</th>
                                <th class="py-2 pr-3 text-center font-medium">
                                    Benar
                                </th>
                                <th class="py-2 pr-3 text-center font-medium">
                                    Pre-test
                                </th>
                                <th class="py-2 pr-3 text-center font-medium">
                                    Nilai
                                </th>
                                <th class="w-40 py-2 pr-3 font-medium">
                                    Persentase
                                </th>
                                <th class="py-2 pr-3 font-medium">Kategori</th>
                                <th class="py-2 text-right font-medium">
                                    Perubahan
                                </th>
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
                                    class="text-muted-foreground py-3 pr-3 text-center tabular-nums"
                                >
                                    {{
                                        topic.pre_score === null
                                            ? '-'
                                            : formatNumber(topic.pre_score)
                                    }}
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
                                <td class="py-3 pr-3">
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
                                <td
                                    class="py-3 text-right text-sm font-semibold tabular-nums"
                                    :class="
                                        deltaClass(
                                            delta(topic.score, topic.pre_score),
                                        )
                                    "
                                >
                                    {{
                                        deltaText(
                                            delta(topic.score, topic.pre_score),
                                        )
                                    }}
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
                                    class="text-muted-foreground py-3 pr-3 text-center tabular-nums"
                                >
                                    {{
                                        result.pre_percent === null
                                            ? '-'
                                            : formatPercent(result.pre_percent)
                                    }}
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
                                <td class="py-3 pr-3">
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
                                <td
                                    class="rounded-r-xl py-3 pr-3 text-right text-sm tabular-nums"
                                    :class="deltaClass(overallDelta)"
                                >
                                    {{ deltaText(overallDelta) }}
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
                title="Perbandingan dengan Pre-Test"
                subtitle="Peningkatan kemampuan setelah belajar"
            >
                <div
                    class="flex items-center justify-around gap-3 rounded-2xl bg-gradient-to-br from-violet-500/10 to-fuchsia-500/10 p-5"
                >
                    <div class="text-center">
                        <p class="text-muted-foreground text-xs">Pre-test</p>
                        <p class="text-3xl font-bold tabular-nums">
                            {{
                                result.pre_percent === null
                                    ? '-'
                                    : formatNumber(result.pre_percent)
                            }}
                        </p>
                    </div>
                    <TrendingUp class="text-primary size-7" />
                    <div class="text-center">
                        <p class="text-muted-foreground text-xs">Post-test</p>
                        <p class="text-primary text-3xl font-bold tabular-nums">
                            {{ formatNumber(result.percent) }}
                        </p>
                    </div>
                </div>
                <p class="text-center text-sm">
                    Perubahan
                    <strong
                        class="text-lg tabular-nums"
                        :class="deltaClass(overallDelta)"
                        >{{ deltaText(overallDelta) }}</strong
                    >
                    poin
                </p>
                <p class="text-muted-foreground text-xs">
                    Nilai post-test dihitung dari jawaban post-test saja; nilai
                    latihan materi tidak menggantikannya.
                </p>

                <Button
                    size="lg"
                    class="mt-auto w-full bg-gradient-to-r from-cyan-500 to-fuchsia-600 text-white shadow-[0_0_18px_rgba(34,211,238,0.45)] hover:brightness-110"
                    @click="scrollToReport"
                >
                    Lihat Laporan &amp; Kesimpulan <ArrowRight />
                </Button>
            </GlowCard>
        </div>

        <LearningReport id="laporan" class="scroll-mt-4" :report="report" />
    </div>
</template>
