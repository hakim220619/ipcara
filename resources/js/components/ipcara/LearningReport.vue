<script lang="ts">
export type Mastery = 'belum' | 'penguatan' | 'remedial' | 'mastery';

export type LearningReportData = {
    student: { name: string; nim: string | null; program_studi: string | null };
    preTest: {
        score: number;
        correct: number;
        total: number;
        finished_at: string | null;
    } | null;
    postTest: {
        score: number;
        correct: number;
        total: number;
        is_passed: boolean;
        finished_at: string | null;
    } | null;
    passingScore: number;
    nGain: { value: number; category: 'Tinggi' | 'Sedang' | 'Rendah' } | null;
    topics: {
        code: string;
        title: string;
        pre: number | null;
        post: number | null;
    }[];
    practice: {
        mastered: number;
        total: number;
        first_try_mastery: number;
        with_penguatan: number;
        with_remedial: number;
        attempts: number;
        avg_first_score: number | null;
        duration_seconds: number;
        materials: {
            topic: string | null;
            title: string;
            slug: string;
            mastery_status: Mastery;
            attempts: number;
            first_score: number | null;
            last_score: number | null;
            rounds: { penguatan: number; remedial: number };
            duration_seconds: number;
        }[];
    };
    conclusion: {
        verdict: string;
        tone: 'excellent' | 'good' | 'fair' | 'low' | 'neutral';
        summary: string;
        points: string[];
        recommendation: string;
    };
};
</script>

<script setup lang="ts">
import {
    Award,
    BookOpenCheck,
    ChevronDown,
    ClipboardCheck,
    Clock,
    Flag,
    GraduationCap,
    Lightbulb,
    PenLine,
    Sparkles,
    Trophy,
} from '@lucide/vue';
import { computed } from 'vue';

const props = defineProps<{ report: LearningReportData }>();

const fmt = (n: number | null | undefined) =>
    n === null || n === undefined
        ? '-'
        : Number.isInteger(n)
          ? String(n)
          : n.toFixed(1);

const signed = (n: number | null) =>
    n === null ? '-' : `${n > 0 ? '+' : ''}${fmt(n)}`;

const minutes = (s: number) =>
    s >= 60 ? `${Math.floor(s / 60)} mnt ${s % 60} dtk` : `${s} dtk`;

const change = computed(() =>
    props.report.preTest && props.report.postTest
        ? Math.round(
              (props.report.postTest.score - props.report.preTest.score) * 10,
          ) / 10
        : null,
);

const masteryPercent = computed(() =>
    props.report.practice.total
        ? Math.round(
              (props.report.practice.mastered / props.report.practice.total) *
                  100,
          )
        : 0,
);

const toneGradient = computed(
    () =>
        ({
            excellent: 'from-emerald-400 to-cyan-400',
            good: 'from-sky-400 to-indigo-400',
            fair: 'from-amber-300 to-orange-500',
            low: 'from-rose-400 to-pink-500',
            neutral: 'from-slate-300 to-slate-500',
        })[props.report.conclusion.tone],
);

// Lingkaran skor: keliling r=42 ≈ 263.9
const ring = (value: number | null | undefined) =>
    `${((value ?? 0) / 100) * 263.9} 263.9`;

const masteryMeta: Record<Mastery, { label: string; class: string }> = {
    belum: { label: 'Belum', class: 'bg-white/10 text-white/70' },
    penguatan: { label: 'Penguatan', class: 'bg-amber-400/20 text-amber-300' },
    remedial: { label: 'Remedial', class: 'bg-rose-400/20 text-rose-300' },
    mastery: { label: 'Mastery', class: 'bg-lime-400/20 text-lime-300' },
};
</script>

<template>
    <section
        class="relative overflow-hidden rounded-[2rem] bg-[#0b1026] p-5 text-white shadow-[0_40px_90px_-40px_rgba(34,211,238,0.55)] md:p-10"
    >
        <!-- Cahaya latar -->
        <div
            class="pointer-events-none absolute -top-32 -left-24 size-96 rounded-full bg-cyan-500/25 blur-3xl"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute top-1/3 -right-32 size-96 rounded-full bg-fuchsia-500/20 blur-3xl"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-32 left-1/3 size-96 rounded-full bg-amber-400/15 blur-3xl"
            aria-hidden="true"
        ></div>

        <div class="relative flex flex-col gap-8 md:gap-10">
            <!-- Kepala -->
            <header
                class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between"
            >
                <div class="flex flex-col gap-2">
                    <span
                        class="inline-flex w-fit items-center gap-2 rounded-full border border-cyan-300/30 bg-cyan-300/10 px-3 py-1 text-xs font-semibold tracking-[0.2em] text-cyan-200 uppercase"
                    >
                        <GraduationCap class="size-4" /> Rapor Pembelajaran PPI
                    </span>
                    <h2
                        class="bg-gradient-to-r from-white via-cyan-100 to-fuchsia-200 bg-clip-text text-3xl font-extrabold tracking-tight text-transparent md:text-5xl"
                    >
                        Laporan Keseluruhan Hasil Belajar
                    </h2>
                    <p class="text-white/70">
                        {{ report.student.name }}
                        <template v-if="report.student.nim"
                            >· NIM {{ report.student.nim }}</template
                        >
                        <template v-if="report.student.program_studi">
                            · {{ report.student.program_studi }}</template
                        >
                    </p>
                </div>
                <div
                    class="flex shrink-0 flex-col items-start gap-1 md:items-end"
                >
                    <span
                        class="text-xs tracking-widest text-white/60 uppercase"
                        >Predikat</span
                    >
                    <span
                        class="rounded-2xl bg-gradient-to-r px-5 py-2.5 text-2xl font-extrabold text-slate-950 shadow-lg md:text-3xl"
                        :class="toneGradient"
                        >{{ report.conclusion.verdict }}</span
                    >
                </div>
            </header>

            <!-- Perjalanan belajar: pre-test → latihan → post-test -->
            <div class="relative grid gap-4 md:grid-cols-3">
                <div
                    class="absolute top-1/2 right-[16%] left-[16%] hidden h-0.5 -translate-y-1/2 bg-gradient-to-r from-amber-400 via-fuchsia-400 to-cyan-400 opacity-60 md:block"
                    aria-hidden="true"
                ></div>

                <div
                    v-for="step in [
                        {
                            label: 'Pre-Test',
                            icon: ClipboardCheck,
                            value: report.preTest?.score ?? null,
                            display: fmt(report.preTest?.score),
                            sub: report.preTest
                                ? `${report.preTest.correct}/${report.preTest.total} benar`
                                : 'Belum dikerjakan',
                            color: '#fbbf24',
                            glow: 'shadow-[0_0_40px_-10px_rgba(251,191,36,0.7)]',
                        },
                        {
                            label: 'Latihan Materi',
                            icon: PenLine,
                            value: masteryPercent,
                            display: `${report.practice.mastered}/${report.practice.total}`,
                            sub: 'materi Mastery',
                            color: '#e879f9',
                            glow: 'shadow-[0_0_40px_-10px_rgba(232,121,249,0.7)]',
                        },
                        {
                            label: 'Post-Test',
                            icon: Trophy,
                            value: report.postTest?.score ?? null,
                            display: fmt(report.postTest?.score),
                            sub: report.postTest
                                ? `${report.postTest.correct}/${report.postTest.total} benar · ${report.postTest.is_passed ? 'Lulus KKM' : 'Belum lulus'}`
                                : 'Belum dikerjakan',
                            color: '#22d3ee',
                            glow: 'shadow-[0_0_40px_-10px_rgba(34,211,238,0.7)]',
                        },
                    ]"
                    :key="step.label"
                    class="relative flex flex-col items-center gap-3 rounded-3xl border border-white/10 bg-white/5 p-6 text-center backdrop-blur"
                    :class="step.glow"
                >
                    <p
                        class="flex items-center gap-2 text-sm font-semibold tracking-wide uppercase"
                        :style="{ color: step.color }"
                    >
                        <component :is="step.icon" class="size-4" />
                        {{ step.label }}
                    </p>
                    <div class="relative size-36">
                        <svg viewBox="0 0 100 100" class="size-36 -rotate-90">
                            <circle
                                cx="50"
                                cy="50"
                                r="42"
                                fill="none"
                                stroke="rgba(255,255,255,0.1)"
                                stroke-width="8"
                            />
                            <circle
                                cx="50"
                                cy="50"
                                r="42"
                                fill="none"
                                :stroke="step.color"
                                stroke-width="8"
                                stroke-linecap="round"
                                :stroke-dasharray="ring(step.value)"
                                class="transition-[stroke-dasharray] duration-1000"
                            />
                        </svg>
                        <span
                            class="absolute inset-0 flex items-center justify-center text-4xl font-extrabold tabular-nums"
                            >{{ step.display }}</span
                        >
                    </div>
                    <p class="text-sm text-white/70">{{ step.sub }}</p>
                </div>
            </div>

            <!-- Angka kunci -->
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div
                    class="rounded-2xl border border-lime-300/30 bg-lime-300/10 p-4"
                >
                    <p class="text-xs text-lime-200/80 uppercase">N-Gain</p>
                    <p
                        class="text-3xl font-extrabold text-lime-300 tabular-nums"
                    >
                        {{ report.nGain ? report.nGain.value.toFixed(2) : '-' }}
                    </p>
                    <p class="text-xs text-white/60">
                        {{
                            report.nGain
                                ? `Kategori ${report.nGain.category}`
                                : 'Setelah post-test'
                        }}
                    </p>
                </div>
                <div
                    class="rounded-2xl border border-cyan-300/30 bg-cyan-300/10 p-4"
                >
                    <p class="text-xs text-cyan-200/80 uppercase">Perubahan</p>
                    <p
                        class="text-3xl font-extrabold text-cyan-300 tabular-nums"
                    >
                        {{ signed(change) }}
                    </p>
                    <p class="text-xs text-white/60">poin pre → post</p>
                </div>
                <div
                    class="rounded-2xl border border-fuchsia-300/30 bg-fuchsia-300/10 p-4"
                >
                    <p class="text-xs text-fuchsia-200/80 uppercase">
                        Percobaan latihan
                    </p>
                    <p
                        class="text-3xl font-extrabold text-fuchsia-300 tabular-nums"
                    >
                        {{ report.practice.attempts }}
                    </p>
                    <p class="text-xs text-white/60">
                        rata-rata skor awal
                        {{ fmt(report.practice.avg_first_score) }}
                    </p>
                </div>
                <div
                    class="rounded-2xl border border-amber-300/30 bg-amber-300/10 p-4"
                >
                    <p class="text-xs text-amber-200/80 uppercase">
                        Waktu latihan
                    </p>
                    <p
                        class="text-2xl font-extrabold text-amber-300 tabular-nums md:text-3xl"
                    >
                        {{ minutes(report.practice.duration_seconds) }}
                    </p>
                    <p class="text-xs text-white/60">total pengerjaan</p>
                </div>
            </div>

            <!-- Per topik -->
            <div
                class="flex flex-col gap-5 rounded-3xl border border-white/10 bg-white/5 p-5 md:p-6"
            >
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-lg font-bold">Perkembangan per Topik</h3>
                    <div class="flex gap-4 text-xs text-white/70">
                        <span class="inline-flex items-center gap-1.5"
                            ><span
                                class="size-2.5 rounded-full bg-amber-400"
                            ></span
                            >Pre-test</span
                        >
                        <span class="inline-flex items-center gap-1.5"
                            ><span
                                class="size-2.5 rounded-full bg-cyan-400"
                            ></span
                            >Post-test</span
                        >
                    </div>
                </div>
                <div class="grid gap-x-8 gap-y-5 md:grid-cols-2">
                    <div
                        v-for="t in report.topics"
                        :key="t.code"
                        class="flex flex-col gap-1.5"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold">{{ t.title }}</span>
                            <span
                                v-if="t.pre !== null && t.post !== null"
                                class="rounded-full px-2 py-0.5 text-xs font-bold tabular-nums"
                                :class="
                                    t.post - t.pre > 0
                                        ? 'bg-lime-400/20 text-lime-300'
                                        : t.post - t.pre < 0
                                          ? 'bg-rose-400/20 text-rose-300'
                                          : 'bg-white/10 text-white/70'
                                "
                                >{{ signed(t.post - t.pre) }}</span
                            >
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="h-2.5 flex-1 overflow-hidden rounded-full bg-white/10"
                            >
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-amber-300 to-amber-500 transition-all duration-1000"
                                    :style="{ width: `${t.pre ?? 0}%` }"
                                ></div>
                            </div>
                            <span
                                class="w-9 text-right text-xs text-amber-200 tabular-nums"
                                >{{ fmt(t.pre) }}</span
                            >
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="h-2.5 flex-1 overflow-hidden rounded-full bg-white/10"
                            >
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-cyan-300 to-cyan-500 transition-all duration-1000"
                                    :style="{ width: `${t.post ?? 0}%` }"
                                ></div>
                            </div>
                            <span
                                class="w-9 text-right text-xs text-cyan-200 tabular-nums"
                                >{{ fmt(t.post) }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kesimpulan -->
            <div
                class="relative overflow-hidden rounded-3xl bg-white p-6 text-slate-900 md:p-8"
            >
                <div
                    class="absolute inset-y-0 left-0 w-2 bg-gradient-to-b"
                    :class="toneGradient"
                    aria-hidden="true"
                ></div>
                <div class="flex flex-col gap-4 pl-2">
                    <h3
                        class="flex items-center gap-2 text-2xl font-extrabold tracking-tight"
                    >
                        <Sparkles class="size-6 text-fuchsia-500" /> Kesimpulan
                    </h3>
                    <p class="text-base leading-relaxed md:text-lg">
                        {{ report.conclusion.summary }}
                    </p>
                    <ul
                        v-if="report.conclusion.points.length"
                        class="grid gap-2 md:grid-cols-2"
                    >
                        <li
                            v-for="(point, i) in report.conclusion.points"
                            :key="point"
                            class="flex items-start gap-3 rounded-2xl bg-slate-100 p-3 text-sm leading-relaxed"
                        >
                            <component
                                :is="
                                    [
                                        Award,
                                        Flag,
                                        BookOpenCheck,
                                        PenLine,
                                        Lightbulb,
                                    ][i % 5]
                                "
                                class="mt-0.5 size-4 shrink-0 text-indigo-500"
                            />
                            {{ point }}
                        </li>
                    </ul>
                    <div
                        class="flex items-start gap-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-fuchsia-600 p-4 text-white"
                    >
                        <Lightbulb class="mt-0.5 size-5 shrink-0" />
                        <p class="text-sm leading-relaxed md:text-base">
                            <strong>Rekomendasi:</strong>
                            {{ report.conclusion.recommendation }}
                        </p>
                    </div>
                    <p class="text-xs text-slate-500">
                        Nilai latihan materi dicatat terpisah untuk memantau
                        penguasaan dan tidak menggantikan nilai post-test.
                    </p>
                </div>
            </div>

            <!-- Rincian latihan per materi -->
            <details
                class="group rounded-3xl border border-white/10 bg-white/5"
            >
                <summary
                    class="flex cursor-pointer list-none items-center justify-between gap-3 p-5 [&::-webkit-details-marker]:hidden"
                >
                    <span class="font-bold">Rincian Latihan per Materi</span>
                    <ChevronDown
                        class="size-5 text-white/70 transition group-open:rotate-180"
                    />
                </summary>
                <div class="overflow-x-auto px-5 pb-5">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead>
                            <tr
                                class="border-b border-white/10 text-left text-xs text-white/60 uppercase"
                            >
                                <th class="py-2 pr-3 font-semibold">Materi</th>
                                <th class="py-2 pr-3 font-semibold">Status</th>
                                <th class="py-2 pr-3 text-center font-semibold">
                                    Percobaan
                                </th>
                                <th class="py-2 pr-3 text-center font-semibold">
                                    Skor awal → akhir
                                </th>
                                <th class="py-2 pr-3 text-center font-semibold">
                                    Penguatan / Remedial
                                </th>
                                <th class="py-2 text-right font-semibold">
                                    <Clock class="ml-auto size-4" />
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="m in report.practice.materials"
                                :key="m.slug"
                                class="border-b border-white/5 last:border-0"
                            >
                                <td class="py-2.5 pr-3">
                                    <p class="font-medium">{{ m.title }}</p>
                                    <p class="text-xs text-white/50">
                                        {{ m.topic }}
                                    </p>
                                </td>
                                <td class="py-2.5 pr-3">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="
                                            masteryMeta[m.mastery_status].class
                                        "
                                        >{{
                                            masteryMeta[m.mastery_status].label
                                        }}</span
                                    >
                                </td>
                                <td
                                    class="py-2.5 pr-3 text-center font-bold tabular-nums"
                                >
                                    {{ m.attempts }}
                                </td>
                                <td
                                    class="py-2.5 pr-3 text-center tabular-nums"
                                >
                                    <template v-if="m.attempts"
                                        >{{ fmt(m.first_score) }} →
                                        <strong class="text-cyan-300">{{
                                            fmt(m.last_score)
                                        }}</strong></template
                                    >
                                    <template v-else>-</template>
                                </td>
                                <td
                                    class="py-2.5 pr-3 text-center text-white/70 tabular-nums"
                                >
                                    {{ m.rounds.penguatan }} /
                                    {{ m.rounds.remedial }}
                                </td>
                                <td
                                    class="py-2.5 text-right text-white/70 tabular-nums"
                                >
                                    {{
                                        m.duration_seconds
                                            ? minutes(m.duration_seconds)
                                            : '-'
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    </section>
</template>
