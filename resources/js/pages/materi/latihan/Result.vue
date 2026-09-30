<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowRight,
    Award,
    BookOpen,
    Lightbulb,
    RotateCcw,
    Target,
    TriangleAlert,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import LatihanQuestion, {
    type LatihanQuestionData,
} from '@/components/ipcara/LatihanQuestion.vue';
import { index as materiIndex, show as materiShow } from '@/routes/materi';
import { start as practiceStart } from '@/routes/materi/latihan';
import { index as postTestIndex } from '@/routes/post-test';

type Outcome = 'mastery' | 'penguatan' | 'remedial' | 'ulang_materi';

const props = defineProps<{
    material: { title: string; slug: string; topic: string | null };
    result: {
        round: 'utama' | 'penguatan' | 'remedial';
        attempt_number: number;
        correct: number;
        total: number;
        score: number;
        outcome: Outcome;
        duration_seconds: number | null;
        mastery_status: string;
    };
    isLatest: boolean;
    focus: {
        description: string;
        section: string | null;
        anchor: string | null;
        reinforcement: string | null;
    }[];
    next: {
        type: 'materi' | 'post-test' | 'materi-index';
        title: string | null;
        slug: string | null;
    };
    questions: LatihanQuestionData[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Materi Belajar', href: materiIndex() },
            { title: 'Hasil Latihan', href: materiIndex() },
        ],
    },
});

const meta = computed(() => {
    const r = props.result;
    return {
        mastery: {
            title: 'Status Mastery tercapai!',
            text: 'Semua jawaban benar. Anda menguasai materi ini, silakan lanjut ke materi berikutnya.',
            gradient: 'from-emerald-500 to-teal-600',
            icon: Award,
        },
        penguatan: {
            title: 'Hampir menguasai, perkuat sedikit lagi',
            text: `Benar ${r.correct} dari ${r.total}. Baca materi penguatan singkat di bawah, lalu kerjakan 2 soal penguatan.`,
            gradient: 'from-amber-500 to-orange-600',
            icon: Lightbulb,
        },
        remedial: {
            title: 'Perlu remedial',
            text: `Benar ${r.correct} dari ${r.total}. Pelajari kembali bagian materi yang belum dikuasai, lalu kerjakan 3 soal remedial.`,
            gradient: 'from-rose-500 to-pink-600',
            icon: TriangleAlert,
        },
        ulang_materi: {
            title: 'Ulangi materi dari awal',
            text: `Benar ${r.correct} dari ${r.total} pada soal remedial. Baca ulang seluruh materi dengan teliti, lalu kerjakan kembali latihan utama.`,
            gradient: 'from-rose-600 to-red-700',
            icon: BookOpen,
        },
    }[r.outcome];
});

const nextHref = computed(() => {
    if (props.next.type === 'post-test') return postTestIndex();
    if (props.next.type === 'materi' && props.next.slug)
        return materiShow(props.next.slug);
    return materiIndex();
});
const nextLabel = computed(() => {
    if (props.next.type === 'post-test') return 'Lanjut ke Post-Test';
    if (props.next.type === 'materi')
        return `Lanjut: ${props.next.title ?? 'materi berikutnya'}`;
    return 'Kembali ke Materi Belajar';
});

const actionLabel = computed(
    () =>
        ({
            penguatan: 'Kerjakan 2 Soal Penguatan',
            remedial: 'Kerjakan 3 Soal Remedial',
            ulang_materi: 'Ulangi Latihan Utama',
            mastery: 'Latihan lagi',
        })[props.result.outcome],
);

const starting = ref(false);
function startNext() {
    router.post(
        practiceStart.url(props.material.slug),
        {},
        {
            onStart: () => (starting.value = true),
            onFinish: () => (starting.value = false),
        },
    );
}

const sectionHref = (anchor: string | null) =>
    materiShow(props.material.slug, {
        query: anchor ? { bagian: anchor } : undefined,
    });
</script>

<template>
    <Head :title="`Hasil Latihan ${material.title}`" />

    <div class="mx-auto flex w-full max-w-4xl flex-1 flex-col gap-5 p-4 md:p-6">
        <section
            class="relative overflow-hidden rounded-3xl bg-gradient-to-br p-6 text-white md:p-8"
            :class="meta.gradient"
        >
            <div
                class="absolute -top-16 -right-16 size-56 rounded-full bg-white/10 blur-2xl"
                aria-hidden="true"
            ></div>
            <div
                class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
            >
                <div class="flex max-w-xl flex-col gap-2">
                    <p class="text-sm text-white/80">
                        {{ material.title }} · percobaan ke-{{
                            result.attempt_number
                        }}
                    </p>
                    <h1
                        class="flex items-center gap-2 text-2xl font-bold tracking-tight md:text-3xl"
                    >
                        <component :is="meta.icon" class="size-7 shrink-0" />
                        {{ meta.title }}
                    </h1>
                    <p class="text-sm text-white/90">{{ meta.text }}</p>

                    <div v-if="isLatest" class="mt-3 flex flex-wrap gap-2">
                        <Link
                            v-if="result.outcome === 'mastery'"
                            :href="nextHref"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-lg transition hover:-translate-y-0.5"
                        >
                            {{ nextLabel }} <ArrowRight class="size-4" />
                        </Link>
                        <button
                            v-else
                            type="button"
                            :disabled="starting"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-lg transition hover:-translate-y-0.5"
                            @click="startNext"
                        >
                            <Target class="size-4" /> {{ actionLabel }}
                        </button>
                        <Link
                            v-if="result.outcome !== 'mastery'"
                            :href="materiShow(material.slug)"
                            class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold transition hover:bg-white/25"
                        >
                            <BookOpen class="size-4" /> Buka materi
                        </Link>
                        <button
                            v-else
                            type="button"
                            :disabled="starting"
                            class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold transition hover:bg-white/25"
                            @click="startNext"
                        >
                            <RotateCcw class="size-4" /> Latihan lagi
                        </button>
                    </div>
                </div>

                <div
                    class="flex shrink-0 flex-col items-center rounded-2xl bg-white/15 px-7 py-5 backdrop-blur"
                >
                    <span class="text-5xl font-bold tabular-nums">{{
                        Math.round(result.score)
                    }}</span>
                    <span class="text-sm text-white/85"
                        >{{ result.correct }}/{{ result.total }} benar</span
                    >
                </div>
            </div>
        </section>

        <!-- Penguatan: materi penguatan singkat -->
        <GlowCard
            v-if="result.outcome === 'penguatan' && focus.length"
            title="Materi Penguatan Singkat"
            subtitle="Konsep yang perlu dimantapkan sebelum soal penguatan"
        >
            <div
                v-for="f in focus"
                :key="f.description"
                class="border-warning/40 bg-warning/5 flex flex-col gap-1.5 rounded-xl border p-4"
            >
                <p class="text-warning flex items-center gap-2 font-semibold">
                    <Lightbulb class="size-4" /> {{ f.description }}
                </p>
                <p class="text-sm leading-relaxed">{{ f.reinforcement }}</p>
                <Link
                    v-if="f.anchor"
                    :href="sectionHref(f.anchor)"
                    class="text-primary w-fit text-xs font-medium hover:underline"
                    >Baca bagian “{{ f.section }}” →</Link
                >
            </div>
        </GlowCard>

        <!-- Remedial / ulang: bagian materi yang belum dikuasai -->
        <GlowCard
            v-if="
                (result.outcome === 'remedial' ||
                    result.outcome === 'ulang_materi') &&
                focus.length
            "
            title="Bagian Materi yang Belum Dikuasai"
            subtitle="Pelajari kembali bagian ini sebelum mengerjakan soal berikutnya"
        >
            <Link
                v-for="(f, i) in focus"
                :key="f.description"
                :href="sectionHref(f.anchor)"
                class="group border-destructive/30 bg-destructive/5 hover:border-destructive/60 flex items-start gap-3 rounded-xl border p-4 transition"
            >
                <span
                    class="bg-destructive flex size-7 shrink-0 items-center justify-center rounded-full text-xs font-bold text-white"
                    >{{ i + 1 }}</span
                >
                <span class="flex-1">
                    <span class="block font-semibold">{{ f.description }}</span>
                    <span
                        v-if="f.section"
                        class="text-muted-foreground block text-sm"
                        >Bagian materi: {{ f.section }}</span
                    >
                </span>
                <ArrowRight
                    class="text-destructive mt-1 size-4 transition group-hover:translate-x-0.5"
                />
            </Link>
        </GlowCard>

        <section class="flex flex-col gap-3">
            <h2 class="font-semibold">Pembahasan Soal</h2>
            <GlowCard v-for="(q, i) in questions" :key="q.id">
                <LatihanQuestion :question="q" :number="i + 1" />
            </GlowCard>
        </section>
    </div>
</template>
