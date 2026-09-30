<script lang="ts">
export type LatihanFeedback = {
    chosen_option_id: number | null;
    correct_option_id: number | null;
    correct_label: string | null;
    is_correct: boolean;
    explanation: string | null;
};

export type LatihanQuestionData = {
    id: number;
    text: string;
    indicator?: string | null;
    options: { id: number; label: string; text: string }[];
    feedback: LatihanFeedback | null;
};
</script>

<script setup lang="ts">
import { CheckCircle2, Lightbulb, XCircle } from '@lucide/vue';
import { computed } from 'vue';

const props = defineProps<{
    question: LatihanQuestionData;
    number: number;
    selected?: number | null;
}>();

const emit = defineEmits<{ select: [optionId: number] }>();

const feedback = computed(() => props.question.feedback);
const chosenLabel = computed(
    () =>
        props.question.options.find(
            (o) => o.id === feedback.value?.chosen_option_id,
        )?.label ?? null,
);

function optionState(id: number) {
    const f = feedback.value;
    if (!f) return props.selected === id ? 'selected' : 'idle';
    if (id === f.correct_option_id) return 'correct';
    if (id === f.chosen_option_id) return 'wrong';
    return 'muted';
}
</script>

<template>
    <div class="flex flex-col gap-4">
        <p
            v-if="question.indicator"
            class="text-muted-foreground -mb-2 text-xs"
        >
            Indikator: {{ question.indicator }}
        </p>
        <p class="text-lg leading-relaxed font-medium">
            <span class="text-primary mr-1">{{ number }}.</span>
            {{ question.text }}
        </p>

        <div class="flex flex-col gap-2.5">
            <button
                v-for="option in question.options"
                :key="option.id"
                type="button"
                :disabled="!!feedback"
                class="flex items-start gap-3 rounded-xl border p-3.5 text-left text-sm transition disabled:cursor-default"
                :class="{
                    'hover:border-primary/50 hover:bg-secondary/60':
                        optionState(option.id) === 'idle',
                    'border-primary bg-accent shadow-[0_0_14px_var(--color-glow)]':
                        optionState(option.id) === 'selected',
                    'border-success bg-success/10':
                        optionState(option.id) === 'correct',
                    'border-destructive bg-destructive/10':
                        optionState(option.id) === 'wrong',
                    'opacity-60': optionState(option.id) === 'muted',
                }"
                @click="emit('select', option.id)"
            >
                <span
                    class="flex size-7 shrink-0 items-center justify-center rounded-lg border text-xs font-semibold"
                    :class="{
                        'bg-primary text-primary-foreground border-primary':
                            optionState(option.id) === 'selected',
                        'bg-success border-success text-white':
                            optionState(option.id) === 'correct',
                        'bg-destructive border-destructive text-white':
                            optionState(option.id) === 'wrong',
                        'bg-card':
                            optionState(option.id) === 'idle' ||
                            optionState(option.id) === 'muted',
                    }"
                >
                    <CheckCircle2
                        v-if="optionState(option.id) === 'correct'"
                        class="size-4"
                    />
                    <XCircle
                        v-else-if="optionState(option.id) === 'wrong'"
                        class="size-4"
                    />
                    <template v-else>{{ option.label }}</template>
                </span>
                <span class="pt-0.5 leading-relaxed">{{ option.text }}</span>
            </button>
        </div>

        <div
            v-if="feedback"
            class="flex flex-col gap-2 rounded-xl border p-4"
            :class="
                feedback.is_correct
                    ? 'border-success/40 bg-success/5'
                    : 'border-destructive/40 bg-destructive/5'
            "
        >
            <p
                class="flex items-center gap-2 font-semibold"
                :class="
                    feedback.is_correct ? 'text-success' : 'text-destructive'
                "
            >
                <CheckCircle2 v-if="feedback.is_correct" class="size-5" />
                <XCircle v-else class="size-5" />
                <template v-if="feedback.is_correct"
                    >Benar! Jawaban
                    {{ feedback.correct_label }} tepat.</template
                >
                <template v-else-if="chosenLabel"
                    >Kurang tepat. Anda memilih {{ chosenLabel }}, jawaban yang
                    benar {{ feedback.correct_label }}.</template
                >
                <template v-else
                    >Tidak dijawab. Jawaban yang benar
                    {{ feedback.correct_label }}.</template
                >
            </p>
            <p
                v-if="feedback.explanation"
                class="text-muted-foreground flex items-start gap-2 text-sm leading-relaxed"
            >
                <Lightbulb class="text-warning mt-0.5 size-4 shrink-0" />
                <span
                    ><strong class="text-foreground">Mengapa?</strong>
                    {{ feedback.explanation }}</span
                >
            </p>
        </div>
    </div>
</template>
