<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ArrowRight, CheckCheck, Flag } from '@lucide/vue';
import { computed, onMounted, ref, watch } from 'vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import LatihanQuestion, {
    type LatihanQuestionData,
} from '@/components/ipcara/LatihanQuestion.vue';
import { Button } from '@/components/ui/button';
import { show as materiShow, index as materiIndex } from '@/routes/materi';
import {
    answer as practiceAnswer,
    finish as practiceFinish,
} from '@/routes/materi/latihan';

type Round = 'utama' | 'penguatan' | 'remedial';

const props = defineProps<{
    material: { title: string; slug: string; topic: string | null };
    attempt: { id: number; round: Round; attempt_number: number };
    questions: LatihanQuestionData[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Materi Belajar', href: materiIndex() },
            { title: 'Latihan Materi', href: materiIndex() },
        ],
    },
});

const roundMeta: Record<Round, { label: string; hint: string; class: string }> =
    {
        utama: {
            label: 'Latihan Utama',
            hint: '3 soal untuk mengukur penguasaan materi ini.',
            class: 'bg-primary/10 text-primary',
        },
        penguatan: {
            label: 'Soal Penguatan',
            hint: '2 soal untuk memantapkan konsep yang belum kuat.',
            class: 'bg-warning/15 text-warning',
        },
        remedial: {
            label: 'Soal Remedial',
            hint: '3 soal baru untuk indikator yang sama setelah mengulang materi.',
            class: 'bg-destructive/10 text-destructive',
        },
    };

const firstOpen = props.questions.findIndex((q) => !q.feedback);
const current = ref(firstOpen === -1 ? props.questions.length - 1 : firstOpen);
const selected = ref<number | null>(null);
const checking = ref(false);
const finishing = ref(false);
const shownAt = ref(Date.now());

const question = computed(() => props.questions[current.value]);
const answeredCount = computed(
    () => props.questions.filter((q) => q.feedback).length,
);
const allAnswered = computed(
    () => answeredCount.value === props.questions.length,
);

watch(current, () => {
    selected.value = null;
    shownAt.value = Date.now();
});
onMounted(() => (shownAt.value = Date.now()));

function check() {
    if (!selected.value || checking.value) return;
    router.post(
        practiceAnswer.url(props.material.slug),
        {
            question_id: question.value.id,
            option_id: selected.value,
            time_spent: Math.round((Date.now() - shownAt.value) / 1000),
        },
        {
            preserveState: true,
            preserveScroll: true,
            onStart: () => (checking.value = true),
            onFinish: () => (checking.value = false),
        },
    );
}

function next() {
    const open = props.questions.findIndex((q) => !q.feedback);
    if (open !== -1) current.value = open;
}

function finish() {
    router.post(
        practiceFinish.url(props.material.slug),
        {},
        {
            onStart: () => (finishing.value = true),
            onFinish: () => (finishing.value = false),
        },
    );
}
</script>

<template>
    <Head :title="`Latihan ${material.title}`" />

    <div class="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-5 p-4 md:p-6">
        <div class="flex flex-col gap-2">
            <span
                class="w-fit rounded-full px-3 py-1 text-xs font-semibold"
                :class="roundMeta[attempt.round].class"
                >{{ roundMeta[attempt.round].label }} · percobaan ke-{{
                    attempt.attempt_number
                }}</span
            >
            <h1 class="text-2xl font-bold tracking-tight">
                {{ material.title }}
            </h1>
            <p class="text-muted-foreground text-sm">
                {{ roundMeta[attempt.round].hint }}
            </p>
        </div>

        <!-- Progres soal -->
        <div class="flex items-center gap-3">
            <div class="flex gap-2">
                <button
                    v-for="(q, i) in questions"
                    :key="q.id"
                    type="button"
                    class="flex size-10 items-center justify-center rounded-full border-2 text-sm font-semibold transition"
                    :class="[
                        q.feedback
                            ? q.feedback.is_correct
                                ? 'bg-success border-success text-white'
                                : 'bg-destructive border-destructive text-white'
                            : 'bg-card',
                        i === current
                            ? 'ring-primary ring-2 ring-offset-2'
                            : '',
                    ]"
                    :aria-label="`Soal ${i + 1}`"
                    @click="current = i"
                >
                    {{ i + 1 }}
                </button>
            </div>
            <span class="text-muted-foreground text-sm tabular-nums"
                >{{ answeredCount }}/{{ questions.length }} dijawab</span
            >
        </div>

        <GlowCard class="gap-5 md:p-7">
            <LatihanQuestion
                :question="question"
                :number="current + 1"
                :selected="selected"
                @select="(id) => (selected = id)"
            />

            <div class="flex justify-end gap-2 border-t pt-4">
                <Button
                    v-if="!question.feedback"
                    :disabled="!selected || checking"
                    @click="check"
                >
                    <CheckCheck /> Periksa Jawaban
                </Button>
                <Button v-else-if="!allAnswered" @click="next">
                    Soal berikutnya <ArrowRight />
                </Button>
                <Button
                    v-else
                    size="lg"
                    class="shadow-[0_0_18px_var(--color-glow)]"
                    :disabled="finishing"
                    @click="finish"
                >
                    <Flag /> Lihat Skor &amp; Langkah Berikutnya
                </Button>
            </div>
        </GlowCard>

        <p class="text-muted-foreground text-center text-xs">
            Nilai latihan hanya untuk memantau penguasaan materi dan tidak
            memengaruhi nilai post-test.
            <a
                :href="materiShow(material.slug).url"
                class="hover:text-primary underline underline-offset-4"
                >Buka materi</a
            >
        </p>
    </div>
</template>
