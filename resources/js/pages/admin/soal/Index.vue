<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2 } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import ConfirmDialog from '@/components/ipcara/ConfirmDialog.vue';
import FormSelect from '@/components/ipcara/FormSelect.vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import Pagination from '@/components/ipcara/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { dashboard as adminDashboard } from '@/routes/admin';
import {
    create as questionsCreate,
    destroy as questionsDestroy,
    edit as questionsEdit,
    index as questionsIndex,
} from '@/routes/admin/questions';

type QuestionRow = {
    id: number;
    question_text: string;
    topic: string | null;
    difficulty: 'mudah' | 'sedang' | 'sulit';
    correct: string | null;
    is_active: boolean;
    in_pre_test: boolean;
    in_post_test: boolean;
    practice_role: 'utama' | 'penguatan' | 'remedial' | null;
    material: string | null;
};

const props = defineProps<{
    questions: {
        data: QuestionRow[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number | null;
        to: number | null;
        total: number;
    };
    topics: { id: number; title: string }[];
    filters: {
        search?: string;
        topic?: string;
        difficulty?: string;
        tes?: string;
    };
    preTestCount: number;
    postTestCount: number;
    latihanCount: number;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: adminDashboard() },
            { title: 'Bank Soal', href: questionsIndex() },
        ],
    },
});

const search = ref(props.filters.search ?? '');
const topic = ref(props.filters.topic ?? '');
const difficulty = ref(props.filters.difficulty ?? '');
const tes = ref(props.filters.tes ?? '');

const tabs = computed(() => [
    { value: '', label: 'Semua soal', count: null },
    { value: 'pre_test', label: 'Pre-test', count: props.preTestCount },
    { value: 'latihan', label: 'Latihan', count: props.latihanCount },
    { value: 'post_test', label: 'Post-test', count: props.postTestCount },
]);
const tesQuery = computed(() => (tes.value ? { tes: tes.value } : undefined));

let debounce: ReturnType<typeof setTimeout> | undefined;
watch([search, topic, difficulty, tes], () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(
            questionsIndex.url(),
            {
                search: search.value || undefined,
                topic: topic.value || undefined,
                difficulty: difficulty.value || undefined,
                tes: tes.value || undefined,
            },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }, 300);
});

const deleting = ref<QuestionRow | null>(null);
const deleteOpen = ref(false);
const processing = ref(false);

function askDelete(row: QuestionRow) {
    deleting.value = row;
    deleteOpen.value = true;
}

function confirmDelete() {
    if (!deleting.value) return;
    router.delete(questionsDestroy.url(deleting.value.id), {
        preserveScroll: true,
        onStart: () => (processing.value = true),
        onFinish: () => {
            processing.value = false;
            deleteOpen.value = false;
        },
    });
}

const difficultyColor: Record<QuestionRow['difficulty'], string> = {
    mudah: 'var(--color-success)',
    sedang: 'var(--color-warning)',
    sulit: 'var(--color-destructive)',
};
</script>

<template>
    <Head title="Bank Soal" />

    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div
            class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Bank Soal</h1>
                <p class="text-muted-foreground text-sm">
                    {{ preTestCount }} soal pre-test · {{ postTestCount }} soal
                    post-test
                </p>
            </div>
            <Button class="shadow-[0_0_18px_var(--color-glow)]" as-child>
                <Link :href="questionsCreate({ query: tesQuery })"
                    ><Plus /> Tambah Soal</Link
                >
            </Button>
        </div>

        <div class="bg-secondary/60 flex w-fit gap-1 rounded-xl p-1">
            <button
                v-for="tab in tabs"
                :key="tab.value"
                type="button"
                class="rounded-lg px-3 py-1.5 text-sm font-medium transition"
                :class="
                    tes === tab.value
                        ? 'bg-card text-primary shadow-sm'
                        : 'text-muted-foreground hover:text-foreground'
                "
                @click="tes = tab.value"
            >
                {{ tab.label
                }}<span
                    v-if="tab.count !== null"
                    class="text-muted-foreground ml-1.5 text-xs tabular-nums"
                    >{{ tab.count }}</span
                >
            </button>
        </div>

        <GlowCard>
            <div class="grid gap-3 md:grid-cols-[1fr_14rem_12rem]">
                <div class="relative">
                    <Search
                        class="text-muted-foreground absolute top-1/2 left-3 size-4 -translate-y-1/2"
                    />
                    <Input
                        v-model="search"
                        placeholder="Cari teks soal..."
                        class="pl-9"
                    />
                </div>
                <FormSelect
                    v-model="topic"
                    placeholder="Semua topik"
                    :options="
                        topics.map((t) => ({
                            value: String(t.id),
                            label: t.title,
                        }))
                    "
                />
                <FormSelect
                    v-model="difficulty"
                    placeholder="Semua tingkat"
                    :options="[
                        { value: 'mudah', label: 'Mudah' },
                        { value: 'sedang', label: 'Sedang' },
                        { value: 'sulit', label: 'Sulit' },
                    ]"
                />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm">
                    <thead>
                        <tr
                            class="text-muted-foreground border-b text-left text-xs"
                        >
                            <th class="w-12 py-2 pr-3 font-medium">No</th>
                            <th class="py-2 pr-3 font-medium">Soal</th>
                            <th class="py-2 pr-3 font-medium">Topik</th>
                            <th class="py-2 pr-3 font-medium">Tingkat</th>
                            <th class="py-2 pr-3 text-center font-medium">
                                Kunci
                            </th>
                            <th class="py-2 pr-3 font-medium">Status</th>
                            <th class="py-2 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(q, i) in questions.data"
                            :key="q.id"
                            class="hover:bg-secondary/40 border-b last:border-0"
                        >
                            <td class="text-muted-foreground py-3 pr-3">
                                {{ (questions.from ?? 1) + i }}
                            </td>
                            <td class="max-w-md py-3 pr-3">
                                <p class="line-clamp-2">
                                    {{ q.question_text }}
                                </p>
                            </td>
                            <td class="py-3 pr-3 whitespace-nowrap">
                                {{ q.topic }}
                            </td>
                            <td class="py-3 pr-3">
                                <span
                                    class="text-xs font-medium capitalize"
                                    :style="{
                                        color: difficultyColor[q.difficulty],
                                    }"
                                    >{{ q.difficulty }}</span
                                >
                            </td>
                            <td class="py-3 pr-3 text-center">
                                <span
                                    class="bg-primary text-primary-foreground inline-flex size-7 items-center justify-center rounded-lg text-xs font-semibold"
                                    >{{ q.correct ?? '-' }}</span
                                >
                            </td>
                            <td class="py-3 pr-3">
                                <div class="flex flex-wrap gap-1">
                                    <Badge
                                        v-if="q.in_pre_test && q.is_active"
                                        class="text-[10px]"
                                        >Pre-test</Badge
                                    >
                                    <Badge
                                        v-if="q.in_post_test && q.is_active"
                                        variant="secondary"
                                        class="text-[10px]"
                                        >Post-test</Badge
                                    >
                                    <Badge
                                        v-if="q.practice_role && q.is_active"
                                        variant="outline"
                                        class="border-primary/50 text-primary text-[10px] capitalize"
                                        :title="q.material ?? undefined"
                                        >Latihan · {{ q.practice_role }}</Badge
                                    >
                                    <Badge
                                        v-if="!q.is_active"
                                        variant="outline"
                                        class="text-[10px]"
                                        >Nonaktif</Badge
                                    >
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                questionsEdit(q.id, {
                                                    query: tesQuery,
                                                })
                                            "
                                            aria-label="Ubah soal"
                                            ><Pencil
                                        /></Link>
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="text-destructive hover:text-destructive"
                                        aria-label="Hapus soal"
                                        @click="askDelete(q)"
                                    >
                                        <Trash2 />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!questions.data.length">
                            <td
                                colspan="7"
                                class="text-muted-foreground py-10 text-center"
                            >
                                Tidak ada soal yang cocok dengan filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination
                :links="questions.links"
                :from="questions.from"
                :to="questions.to"
                :total="questions.total"
            />
        </GlowCard>

        <ConfirmDialog
            v-model:open="deleteOpen"
            title="Hapus soal ini?"
            description="Soal akan dihapus dari bank soal, pre-test, dan post-test. Jawaban mahasiswa untuk soal ini ikut terhapus, tetapi nilai yang sudah tercatat tidak berubah."
            :processing="processing"
            @confirm="confirmDelete"
        />
    </div>
</template>
