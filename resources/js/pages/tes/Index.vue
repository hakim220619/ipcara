<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Award,
    BookOpen,
    CheckCircle2,
    ChevronDown,
    ClipboardCheck,
    Lock,
    PenLine,
    Trophy,
} from '@lucide/vue';
import { show as materiShow } from '@/routes/materi';
import {
    result as practiceResult,
    take as practiceTake,
} from '@/routes/materi/latihan';
import {
    index as postTestIndex,
    result as postTestResult,
} from '@/routes/post-test';
import { result as preTestResult } from '@/routes/pre-test';
import { index as tesIndex } from '@/routes/tes';

type Mastery = 'belum' | 'penguatan' | 'remedial' | 'mastery';

type PracticeItem = {
    title: string;
    slug: string;
    read: boolean;
    mastery: Mastery;
    attempts: number;
    last_score: number | null;
    in_progress: boolean;
    last_attempt_id: number | null;
};

const props = defineProps<{
    preTest: { done: boolean; score: number | null };
    latihan: { title: string; materials: PracticeItem[] }[];
    postTest: {
        completed: number;
        total: number;
        unlocked: boolean;
        done: boolean;
        score: number | null;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Tes & Latihan', href: tesIndex() }],
    },
});

const masteryMeta: Record<Mastery, { label: string; class: string }> = {
    belum: { label: 'Belum', class: 'bg-secondary text-muted-foreground' },
    penguatan: { label: 'Penguatan', class: 'bg-warning/15 text-warning' },
    remedial: {
        label: 'Remedial',
        class: 'bg-destructive/10 text-destructive',
    },
    mastery: { label: 'Mastery', class: 'bg-success/15 text-success' },
};

const masteredCount = props.latihan
    .flatMap((t) => t.materials)
    .filter((m) => m.mastery === 'mastery').length;
const practiceTotal = props.latihan.flatMap((t) => t.materials).length;

function topicMastered(topic: { materials: PracticeItem[] }) {
    return topic.materials.filter((m) => m.mastery === 'mastery').length;
}

function practiceHref(m: PracticeItem) {
    if (m.in_progress) return practiceTake(m.slug);
    if (m.last_attempt_id && m.mastery !== 'mastery')
        return practiceResult([m.slug, m.last_attempt_id]);
    return materiShow(m.slug);
}

function practiceLabel(m: PracticeItem) {
    if (m.in_progress) return 'Lanjutkan';
    if (!m.read) return 'Baca materi';
    if (m.mastery === 'mastery') return 'Lihat';
    if (m.attempts) return 'Tindak lanjut';
    return 'Mulai';
}
</script>

<template>
    <Head title="Tes & Latihan" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Tes &amp; Latihan</h1>
            <p class="text-muted-foreground text-sm">
                Pre-test untuk memetakan kemampuan, latihan per topik dengan
                pembahasan, dan post-test sebagai evaluasi akhir.
            </p>
        </div>

        <!-- 1. Pre-test -->
        <section
            class="bg-card glow-card flex flex-wrap items-center justify-between gap-4 rounded-2xl border p-5"
        >
            <div class="flex items-center gap-4">
                <div
                    class="bg-primary/10 text-primary flex size-12 items-center justify-center rounded-2xl"
                >
                    <ClipboardCheck class="size-6" />
                </div>
                <div>
                    <p class="text-muted-foreground text-xs font-medium">
                        Langkah 1
                    </p>
                    <h2 class="font-semibold">Pre-Test</h2>
                    <p class="text-muted-foreground text-sm">
                        <template v-if="preTest.done"
                            >Selesai · nilai
                            <strong class="text-foreground">{{
                                Math.round(preTest.score ?? 0)
                            }}</strong></template
                        >
                        <template v-else>Belum dikerjakan</template>
                    </p>
                </div>
            </div>
            <Link
                :href="preTestResult()"
                class="text-primary inline-flex items-center gap-1 text-sm font-medium hover:underline"
                >Lihat hasil <ArrowRight class="size-4"
            /></Link>
        </section>

        <!-- 2. Soal latihan -->
        <section class="flex flex-col gap-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-12 items-center justify-center rounded-2xl bg-[color-mix(in_oklab,var(--color-chart-3)_16%,transparent)] text-[var(--color-chart-3)]"
                    >
                        <PenLine class="size-6" />
                    </div>
                    <div>
                        <p class="text-muted-foreground text-xs font-medium">
                            Langkah 2
                        </p>
                        <h2 class="font-semibold">Soal Latihan</h2>
                        <p class="text-muted-foreground text-sm">
                            Pilih topik untuk melihat latihan setiap materinya.
                        </p>
                    </div>
                </div>
                <span
                    class="bg-success/15 text-success inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold"
                >
                    <Award class="size-4" /> {{ masteredCount }}/{{
                        practiceTotal
                    }}
                    Mastery
                </span>
            </div>

            <div class="flex flex-col gap-2">
                <details
                    v-for="topic in latihan"
                    :key="topic.title"
                    class="group bg-card rounded-2xl border transition open:shadow-md"
                >
                    <summary
                        class="flex cursor-pointer list-none items-center gap-3 p-4 [&::-webkit-details-marker]:hidden"
                    >
                        <span
                            class="flex size-9 shrink-0 items-center justify-center rounded-xl"
                            :class="
                                topicMastered(topic) === topic.materials.length
                                    ? 'bg-success text-white'
                                    : 'bg-secondary text-muted-foreground'
                            "
                        >
                            <Award
                                v-if="
                                    topicMastered(topic) ===
                                    topic.materials.length
                                "
                                class="size-4"
                            />
                            <BookOpen v-else class="size-4" />
                        </span>
                        <span class="flex-1 font-semibold">{{
                            topic.title
                        }}</span>
                        <span class="text-muted-foreground text-sm tabular-nums"
                            >{{ topicMastered(topic) }}/{{
                                topic.materials.length
                            }}
                            Mastery</span
                        >
                        <ChevronDown
                            class="text-muted-foreground size-5 transition group-open:rotate-180"
                        />
                    </summary>

                    <ul class="divide-y border-t px-4">
                        <li
                            v-for="m in topic.materials"
                            :key="m.slug"
                            class="flex items-center gap-3 py-3"
                        >
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm">{{
                                    m.title
                                }}</span>
                                <span class="text-muted-foreground text-xs">
                                    <template v-if="m.attempts"
                                        >{{ m.attempts }}× · skor
                                        {{
                                            Math.round(m.last_score ?? 0)
                                        }}</template
                                    >
                                    <template v-else-if="!m.read"
                                        >Materi belum dibaca</template
                                    >
                                    <template v-else>Belum dikerjakan</template>
                                </span>
                            </span>
                            <span
                                class="shrink-0 rounded-full px-2 py-0.5 text-[11px] font-medium"
                                :class="masteryMeta[m.mastery].class"
                                >{{ masteryMeta[m.mastery].label }}</span
                            >
                            <Link
                                :href="practiceHref(m)"
                                class="text-primary w-24 shrink-0 text-right text-xs font-medium hover:underline"
                                >{{ practiceLabel(m) }}</Link
                            >
                        </li>
                    </ul>
                </details>
            </div>

            <p
                v-if="!latihan.length"
                class="text-muted-foreground bg-card rounded-2xl border p-6 text-center text-sm"
            >
                Belum ada soal latihan.
            </p>
        </section>

        <!-- 3. Post-test -->
        <section
            class="bg-card glow-card flex flex-wrap items-center justify-between gap-4 rounded-2xl border p-5"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex size-12 items-center justify-center rounded-2xl bg-[color-mix(in_oklab,var(--color-chart-4)_16%,transparent)] text-[var(--color-chart-4)]"
                >
                    <Trophy v-if="postTest.done" class="size-6" />
                    <CheckCircle2
                        v-else-if="postTest.unlocked"
                        class="size-6"
                    />
                    <Lock v-else class="size-6" />
                </div>
                <div>
                    <p class="text-muted-foreground text-xs font-medium">
                        Langkah 3
                    </p>
                    <h2 class="font-semibold">Post-Test</h2>
                    <p class="text-muted-foreground text-sm">
                        <template v-if="postTest.done"
                            >Selesai · nilai
                            <strong class="text-foreground">{{
                                Math.round(postTest.score ?? 0)
                            }}</strong></template
                        >
                        <template v-else-if="postTest.unlocked"
                            >Semua materi selesai, post-test siap
                            dikerjakan.</template
                        >
                        <template v-else
                            >Terkunci · {{ postTest.completed }}/{{
                                postTest.total
                            }}
                            materi tuntas (mastery)</template
                        >
                    </p>
                </div>
            </div>
            <Link
                v-if="postTest.done"
                :href="postTestResult()"
                class="text-primary inline-flex items-center gap-1 text-sm font-medium hover:underline"
                >Lihat hasil <ArrowRight class="size-4"
            /></Link>
            <Link
                v-else-if="postTest.unlocked"
                :href="postTestIndex()"
                class="bg-primary text-primary-foreground inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium"
                >Kerjakan <ArrowRight class="size-4"
            /></Link>
        </section>
    </div>
</template>
