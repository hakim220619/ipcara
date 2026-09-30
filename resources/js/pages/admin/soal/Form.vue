<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Save } from '@lucide/vue';
import { computed, watch } from 'vue';
import FormSelect from '@/components/ipcara/FormSelect.vue';
import FormTextarea from '@/components/ipcara/FormTextarea.vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { dashboard as adminDashboard } from '@/routes/admin';
import {
    index as questionsIndex,
    store as questionsStore,
    update as questionsUpdate,
} from '@/routes/admin/questions';

type QuestionData = {
    id: number;
    topic_id: number;
    difficulty: string;
    question_text: string;
    explanation: string | null;
    is_active: boolean;
    in_pre_test: boolean;
    in_post_test: boolean;
    material_id: number | null;
    indicator_id: number | null;
    practice_role: 'utama' | 'penguatan' | 'remedial' | null;
    options: string[];
    correct_option: number;
};

const props = defineProps<{
    question: QuestionData | null;
    topics: { id: number; title: string }[];
    materials: {
        id: number;
        topic_id: number;
        title: string;
        indicators: { id: number; description: string }[];
    }[];
    tes: 'pre_test' | 'post_test' | 'latihan' | null;
}>();

const backHref = computed(() =>
    questionsIndex(props.tes ? { query: { tes: props.tes } } : undefined),
);

const isEdit = computed(() => props.question !== null);

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: adminDashboard() },
            { title: 'Bank Soal', href: questionsIndex() },
            { title: 'Form Soal', href: questionsIndex() },
        ],
    },
});

const form = useForm({
    topic_id: props.question?.topic_id ?? props.topics[0]?.id ?? '',
    difficulty: props.question?.difficulty ?? 'sedang',
    question_text: props.question?.question_text ?? '',
    explanation: props.question?.explanation ?? '',
    options: props.question?.options ?? ['', '', '', ''],
    correct_option: props.question?.correct_option ?? 0,
    is_active: props.question?.is_active ?? true,
    in_pre_test:
        (props.question?.in_pre_test ?? !props.tes) || props.tes === 'pre_test',
    in_post_test: props.question?.in_post_test ?? props.tes === 'post_test',
    practice_role: (props.question?.practice_role ??
        (props.tes === 'latihan' ? 'utama' : '')) as string,
    material_id: (props.question?.material_id ?? '') as number | string,
    indicator_id: (props.question?.indicator_id ?? '') as number | string,
    tes: props.tes,
});

const topicMaterials = computed(() =>
    props.materials.filter((m) => m.topic_id === Number(form.topic_id)),
);
const materialIndicators = computed(
    () =>
        props.materials.find((m) => m.id === Number(form.material_id))
            ?.indicators ?? [],
);

watch(
    () => form.topic_id,
    () => {
        if (
            !topicMaterials.value.some((m) => m.id === Number(form.material_id))
        )
            form.material_id = '';
    },
);
watch(
    () => form.material_id,
    () => {
        if (
            !materialIndicators.value.some(
                (i) => i.id === Number(form.indicator_id),
            )
        )
            form.indicator_id = '';
    },
);

const labels = ['A', 'B', 'C', 'D'];

const errors = computed(
    () => form.errors as Record<string, string | undefined>,
);

function submit() {
    if (props.question) {
        form.put(questionsUpdate.url(props.question.id));
    } else {
        form.post(questionsStore.url());
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Ubah Soal' : 'Tambah Soal'" />

    <form
        class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6"
        @submit.prevent="submit"
    >
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    {{ isEdit ? 'Ubah Soal' : 'Tambah Soal' }}
                </h1>
                <p class="text-muted-foreground text-sm">
                    Soal pilihan ganda dengan empat pilihan jawaban (A–D).
                </p>
            </div>
            <Button variant="outline" as-child>
                <Link :href="backHref"><ArrowLeft /> Kembali</Link>
            </Button>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1fr_20rem]">
            <div class="flex flex-col gap-5">
                <GlowCard title="Pertanyaan">
                    <div class="flex flex-col gap-2">
                        <Label for="question_text">Teks soal</Label>
                        <FormTextarea
                            id="question_text"
                            v-model="form.question_text"
                            :rows="4"
                            placeholder="Tulis pertanyaan..."
                            :invalid="!!form.errors.question_text"
                        />
                        <InputError :message="form.errors.question_text" />
                    </div>
                </GlowCard>

                <GlowCard
                    title="Pilihan Jawaban"
                    subtitle="Klik huruf untuk menandai kunci jawaban"
                >
                    <div
                        v-for="(label, idx) in labels"
                        :key="label"
                        class="flex flex-col gap-1"
                    >
                        <div class="flex items-start gap-3">
                            <button
                                type="button"
                                class="mt-1 flex size-9 shrink-0 items-center justify-center rounded-lg border text-sm font-semibold transition"
                                :class="
                                    form.correct_option === idx
                                        ? 'bg-success border-success text-white shadow-[0_0_12px_var(--color-success)]'
                                        : 'hover:border-primary'
                                "
                                :aria-label="`Jadikan ${label} kunci jawaban`"
                                @click="form.correct_option = idx"
                            >
                                <CheckCircle2
                                    v-if="form.correct_option === idx"
                                    class="size-4"
                                />
                                <template v-else>{{ label }}</template>
                            </button>
                            <FormTextarea
                                v-model="form.options[idx]"
                                :rows="2"
                                :placeholder="`Pilihan ${label}`"
                                :invalid="!!errors[`options.${idx}`]"
                            />
                        </div>
                        <InputError
                            class="pl-12"
                            :message="errors[`options.${idx}`]"
                        />
                    </div>
                    <InputError :message="form.errors.correct_option" />
                </GlowCard>

                <GlowCard
                    title="Latihan Materi"
                    subtitle="Opsional. Soal latihan muncul setelah mahasiswa menyelesaikan materi"
                >
                    <div class="grid gap-3 md:grid-cols-3">
                        <div class="flex flex-col gap-2">
                            <Label for="practice_role">Peran soal</Label>
                            <FormSelect
                                id="practice_role"
                                v-model="form.practice_role"
                                placeholder="Tidak dipakai di latihan"
                                :options="[
                                    { value: 'utama', label: 'Soal utama' },
                                    {
                                        value: 'penguatan',
                                        label: 'Soal penguatan',
                                    },
                                    {
                                        value: 'remedial',
                                        label: 'Soal remedial',
                                    },
                                ]"
                            />
                            <InputError :message="errors.practice_role" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="material_id">Materi</Label>
                            <FormSelect
                                id="material_id"
                                v-model="form.material_id"
                                placeholder="Pilih materi"
                                :disabled="!form.practice_role"
                                :options="
                                    topicMaterials.map((m) => ({
                                        value: m.id,
                                        label: m.title,
                                    }))
                                "
                            />
                            <InputError :message="errors.material_id" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="indicator_id"
                                >Indikator kompetensi</Label
                            >
                            <FormSelect
                                id="indicator_id"
                                v-model="form.indicator_id"
                                placeholder="Pilih indikator"
                                :disabled="!form.practice_role"
                                :options="
                                    materialIndicators.map((i) => ({
                                        value: i.id,
                                        label: i.description,
                                    }))
                                "
                            />
                            <InputError :message="errors.indicator_id" />
                        </div>
                    </div>
                    <p class="text-muted-foreground text-xs">
                        Setiap indikator memiliki satu soal utama, satu soal
                        penguatan, dan satu soal remedial yang berbeda tetapi
                        menguji kompetensi yang sama.
                    </p>
                </GlowCard>

                <GlowCard
                    title="Pembahasan"
                    subtitle="Ditampilkan sebagai umpan balik setelah mahasiswa menjawab latihan"
                >
                    <FormTextarea
                        v-model="form.explanation"
                        :rows="3"
                        placeholder="Penjelasan mengapa jawaban tersebut benar..."
                        :invalid="!!form.errors.explanation"
                    />
                    <InputError :message="form.errors.explanation" />
                </GlowCard>
            </div>

            <div class="flex flex-col gap-5">
                <GlowCard title="Pengaturan" class="lg:sticky lg:top-4">
                    <div class="flex flex-col gap-2">
                        <Label for="topic_id">Topik</Label>
                        <FormSelect
                            id="topic_id"
                            v-model="form.topic_id"
                            :options="
                                topics.map((t) => ({
                                    value: t.id,
                                    label: t.title,
                                }))
                            "
                            :invalid="!!form.errors.topic_id"
                        />
                        <InputError :message="form.errors.topic_id" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="difficulty">Tingkat kesulitan</Label>
                        <FormSelect
                            id="difficulty"
                            v-model="form.difficulty"
                            :options="[
                                { value: 'mudah', label: 'Mudah' },
                                { value: 'sedang', label: 'Sedang' },
                                { value: 'sulit', label: 'Sulit' },
                            ]"
                        />
                        <InputError :message="form.errors.difficulty" />
                    </div>
                    <Label class="flex items-center gap-2 font-normal">
                        <Checkbox v-model="form.in_pre_test" />
                        Masukkan ke pre-test
                    </Label>
                    <Label class="flex items-center gap-2 font-normal">
                        <Checkbox v-model="form.in_post_test" />
                        Masukkan ke post-test
                    </Label>

                    <Label class="flex items-center gap-2 font-normal">
                        <Checkbox v-model="form.is_active" />
                        Soal aktif
                    </Label>
                    <Button
                        type="submit"
                        class="w-full shadow-[0_0_18px_var(--color-glow)]"
                        :disabled="form.processing"
                    >
                        <Save />
                        {{ isEdit ? 'Simpan Perubahan' : 'Simpan Soal' }}
                    </Button>
                </GlowCard>
            </div>
        </div>
    </form>
</template>
