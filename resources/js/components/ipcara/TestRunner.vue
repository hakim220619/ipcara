<script lang="ts">
export type TestQuestion = {
    id: number;
    text: string;
    topic: string | null;
    options: { id: number; label: string; text: string }[];
};
</script>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Clock, Send } from '@lucide/vue';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const props = defineProps<{
    attemptId: number;
    title: string;
    deadline: string | null;
    questions: TestQuestion[];
    /** Nama tes untuk teks dialog, mis. "pre-test". */
    label: string;
    submitUrl: string;
}>();

const storageKey = `ipcara:${props.label}:${props.attemptId}`;
const answers = ref<Record<number, number>>({});
const current = ref(0);
const confirmOpen = ref(false);
const submitting = ref(false);
const now = ref(Date.now());
let timer: ReturnType<typeof setInterval> | undefined;

try {
    const saved = localStorage.getItem(storageKey);
    if (saved) answers.value = JSON.parse(saved);
} catch {
    answers.value = {};
}

watch(
    answers,
    (value) => {
        try {
            localStorage.setItem(storageKey, JSON.stringify(value));
        } catch {
            // Penyimpanan draf hanya pelengkap.
        }
    },
    { deep: true },
);

const question = computed(() => props.questions[current.value]);
const answeredCount = computed(
    () => props.questions.filter((q) => answers.value[q.id]).length,
);
const unanswered = computed(() => props.questions.length - answeredCount.value);
const progress = computed(() =>
    Math.round((answeredCount.value / props.questions.length) * 100),
);

const deadlineMs = props.deadline ? new Date(props.deadline).getTime() : null;
const remaining = computed(() =>
    deadlineMs
        ? Math.max(0, Math.floor((deadlineMs - now.value) / 1000))
        : null,
);
const remainingLabel = computed(() => {
    if (remaining.value === null) return null;
    const m = Math.floor(remaining.value / 60);
    const s = remaining.value % 60;
    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
});

function choose(optionId: number) {
    answers.value = { ...answers.value, [question.value.id]: optionId };
}

function go(index: number) {
    current.value = Math.min(Math.max(index, 0), props.questions.length - 1);
}

function submit() {
    if (submitting.value) return;
    submitting.value = true;
    router.post(
        props.submitUrl,
        { answers: answers.value },
        {
            onSuccess: () => {
                try {
                    localStorage.removeItem(storageKey);
                } catch {
                    // Abaikan.
                }
            },
            onFinish: () => {
                submitting.value = false;
                confirmOpen.value = false;
            },
        },
    );
}

onMounted(() => {
    timer = setInterval(() => {
        now.value = Date.now();
        if (remaining.value === 0) {
            clearInterval(timer);
            submit();
        }
    }, 1000);
});

onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div
            class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight">{{ title }}</h1>
                <p class="text-muted-foreground text-sm">
                    {{ answeredCount }} dari {{ questions.length }} soal
                    terjawab
                </p>
            </div>
            <div
                v-if="remainingLabel"
                class="bg-card inline-flex w-fit items-center gap-2 rounded-xl border px-4 py-2 font-mono text-lg font-semibold tabular-nums"
                :class="
                    remaining !== null && remaining < 300
                        ? 'border-destructive text-destructive'
                        : 'text-primary'
                "
            >
                <Clock class="size-5" /> {{ remainingLabel }}
            </div>
        </div>

        <div class="bg-secondary h-2 overflow-hidden rounded-full">
            <div
                class="bg-primary h-full rounded-full transition-all"
                :style="{ width: `${progress}%` }"
            ></div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1fr_20rem]">
            <GlowCard class="gap-5 md:p-7">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span
                        class="bg-primary text-primary-foreground rounded-full px-3 py-1 text-xs font-semibold"
                    >
                        Soal {{ current + 1 }} / {{ questions.length }}
                    </span>
                    <span
                        v-if="question.topic"
                        class="text-muted-foreground text-xs"
                        >{{ question.topic }}</span
                    >
                </div>

                <p class="text-lg leading-relaxed font-medium">
                    {{ question.text }}
                </p>

                <div class="flex flex-col gap-3">
                    <button
                        v-for="option in question.options"
                        :key="option.id"
                        type="button"
                        class="group flex items-start gap-3 rounded-xl border p-4 text-left transition"
                        :class="
                            answers[question.id] === option.id
                                ? 'border-primary bg-accent shadow-[0_0_14px_var(--color-glow)]'
                                : 'hover:border-primary/50 hover:bg-secondary/60'
                        "
                        @click="choose(option.id)"
                    >
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-lg border text-sm font-semibold transition"
                            :class="
                                answers[question.id] === option.id
                                    ? 'bg-primary text-primary-foreground border-primary'
                                    : 'bg-card group-hover:border-primary/50'
                            "
                            >{{ option.label }}</span
                        >
                        <span class="pt-1 text-sm leading-relaxed">{{
                            option.text
                        }}</span>
                    </button>
                </div>

                <div class="flex items-center justify-between gap-3 pt-2">
                    <Button
                        variant="outline"
                        :disabled="current === 0"
                        @click="go(current - 1)"
                    >
                        <ChevronLeft /> Sebelumnya
                    </Button>
                    <Button
                        v-if="current < questions.length - 1"
                        @click="go(current + 1)"
                    >
                        Berikutnya <ChevronRight />
                    </Button>
                    <Button
                        v-else
                        class="shadow-[0_0_18px_var(--color-glow)]"
                        @click="confirmOpen = true"
                    >
                        <Send /> Selesai
                    </Button>
                </div>
            </GlowCard>

            <GlowCard
                title="Navigasi Soal"
                subtitle="Biru = sudah dijawab"
                class="h-fit lg:sticky lg:top-4"
            >
                <div class="grid grid-cols-10 gap-1.5 lg:grid-cols-5">
                    <button
                        v-for="(q, i) in questions"
                        :key="q.id"
                        type="button"
                        class="flex aspect-square items-center justify-center rounded-lg border text-xs font-medium transition"
                        :class="[
                            answers[q.id]
                                ? 'bg-primary text-primary-foreground border-primary'
                                : 'bg-card hover:border-primary/50',
                            i === current
                                ? 'ring-primary ring-2 ring-offset-2'
                                : '',
                        ]"
                        :aria-label="`Soal ${i + 1}`"
                        @click="go(i)"
                    >
                        {{ i + 1 }}
                    </button>
                </div>
                <Button
                    class="w-full shadow-[0_0_18px_var(--color-glow)]"
                    @click="confirmOpen = true"
                >
                    <Send /> Kirim Jawaban
                </Button>
            </GlowCard>
        </div>

        <Dialog v-model:open="confirmOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Kirim jawaban {{ label }}?</DialogTitle>
                    <DialogDescription>
                        <template v-if="unanswered > 0">
                            Masih ada
                            <strong class="text-destructive"
                                >{{ unanswered }} soal</strong
                            >
                            yang belum dijawab dan akan dihitung salah.
                        </template>
                        <template v-else> Semua soal sudah dijawab. </template>
                        Jawaban tidak dapat diubah setelah dikirim.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <Button variant="outline" @click="confirmOpen = false"
                        >Periksa lagi</Button
                    >
                    <Button :disabled="submitting" @click="submit">
                        <Send /> Ya, kirim
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
