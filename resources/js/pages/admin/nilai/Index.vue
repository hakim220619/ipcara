<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Save, Search, Trash2 } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import ConfirmDialog from '@/components/ipcara/ConfirmDialog.vue';
import FormSelect from '@/components/ipcara/FormSelect.vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import Pagination from '@/components/ipcara/Pagination.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard as adminDashboard } from '@/routes/admin';
import {
    destroy as scoresDestroy,
    index as scoresIndex,
    store as scoresStore,
    update as scoresUpdate,
} from '@/routes/admin/scores';

type Level = 'rendah' | 'sedang' | 'tinggi';

type AttemptRow = {
    id: number;
    user: {
        id: number;
        name: string;
        nim: string | null;
        email: string;
    } | null;
    topic_scores: Record<string, number>;
    topic_levels: Record<string, Level>;
    total_points: number;
    max_points: number;
    percent: number;
    is_passed: boolean;
    level: Level;
    level_label: string;
    is_manual: boolean;
    finished_at: string | null;
    finished_label: string | null;
};

type TopicCol = { id: number; code: string; title: string };

const props = defineProps<{
    attempts: {
        data: AttemptRow[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number | null;
        to: number | null;
        total: number;
    };
    filters: { search?: string; status?: string };
    students: { id: number; name: string; nim: string | null }[];
    topics: TopicCol[];
    passingScore: number;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: adminDashboard() },
            { title: 'Nilai Mahasiswa', href: scoresIndex() },
        ],
    },
});

const levelColor: Record<Level, string> = {
    rendah: 'var(--color-destructive)',
    sedang: 'var(--color-warning)',
    tinggi: 'var(--color-success)',
};

const fmt = (n: number) => (Number.isInteger(n) ? String(n) : n.toFixed(1));

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
let debounce: ReturnType<typeof setTimeout> | undefined;
watch([search, status], () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(
            scoresIndex.url(),
            {
                search: search.value || undefined,
                status: status.value || undefined,
            },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }, 300);
});

const editing = ref<AttemptRow | null>(null);
const formOpen = ref(false);

const nowLocal = () => {
    const d = new Date();
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    return d.toISOString().slice(0, 16);
};

const emptyScores = () =>
    Object.fromEntries(props.topics.map((t) => [t.id, 0])) as Record<
        number,
        number | string
    >;

const form = useForm({
    user_id: '' as number | string,
    topic_scores: emptyScores(),
    finished_at: nowLocal(),
});

const formTotal = computed(() =>
    props.topics.reduce(
        (sum, t) => sum + (Number(form.topic_scores[t.id]) || 0),
        0,
    ),
);
const formMax = computed(() => props.topics.length * 100);
const formPercent = computed(() =>
    formMax.value
        ? Math.round((formTotal.value / formMax.value) * 10000) / 100
        : 0,
);

const errors = computed(
    () => form.errors as Record<string, string | undefined>,
);

function openCreate() {
    editing.value = null;
    form.clearErrors();
    form.user_id = props.students[0]?.id ?? '';
    form.topic_scores = emptyScores();
    form.finished_at = nowLocal();
    formOpen.value = true;
}

function openEdit(row: AttemptRow) {
    editing.value = row;
    form.clearErrors();
    form.user_id = row.user?.id ?? '';
    form.topic_scores = Object.fromEntries(
        props.topics.map((t) => [t.id, row.topic_scores[t.id] ?? 0]),
    );
    form.finished_at = row.finished_at ?? nowLocal();
    formOpen.value = true;
}

function submit() {
    const options = {
        preserveScroll: true,
        onSuccess: () => (formOpen.value = false),
    };
    if (editing.value) {
        form.transform((data) => ({
            topic_scores: data.topic_scores,
            finished_at: data.finished_at,
        })).put(scoresUpdate.url(editing.value.id), options);
    } else {
        form.transform((data) => data).post(scoresStore.url(), options);
    }
}

const deleting = ref<AttemptRow | null>(null);
const deleteOpen = ref(false);
const deleteProcessing = ref(false);

function confirmDelete() {
    if (!deleting.value) return;
    router.delete(scoresDestroy.url(deleting.value.id), {
        preserveScroll: true,
        onStart: () => (deleteProcessing.value = true),
        onFinish: () => {
            deleteProcessing.value = false;
            deleteOpen.value = false;
        },
    });
}
</script>

<template>
    <Head title="Nilai Mahasiswa" />

    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div
            class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    Nilai Mahasiswa
                </h1>
                <p class="text-muted-foreground text-sm">
                    Nilai per topik (0–100), total nilai, dan persentase
                    keseluruhan · KKM {{ passingScore }}%
                </p>
            </div>
            <Button
                class="shadow-[0_0_18px_var(--color-glow)]"
                :disabled="!students.length"
                @click="openCreate"
            >
                <Plus /> Input Nilai
            </Button>
        </div>

        <GlowCard>
            <div class="grid gap-3 md:grid-cols-[1fr_14rem]">
                <div class="relative">
                    <Search
                        class="text-muted-foreground absolute top-1/2 left-3 size-4 -translate-y-1/2"
                    />
                    <Input
                        v-model="search"
                        placeholder="Cari nama atau NIM..."
                        class="pl-9"
                    />
                </div>
                <FormSelect
                    v-model="status"
                    placeholder="Semua status"
                    :options="[
                        { value: 'tuntas', label: 'Tuntas' },
                        { value: 'belum', label: 'Belum tuntas' },
                    ]"
                />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] text-sm">
                    <thead>
                        <tr
                            class="text-muted-foreground border-b text-left text-xs"
                        >
                            <th class="py-2 pr-3 font-medium" rowspan="2">
                                Mahasiswa
                            </th>
                            <th
                                class="border-b py-1.5 text-center font-medium"
                                :colspan="topics.length"
                            >
                                Nilai per Topik
                            </th>
                            <th
                                class="py-2 pr-3 pl-3 text-center font-medium"
                                rowspan="2"
                            >
                                Total Nilai
                            </th>
                            <th class="py-2 pr-3 font-medium" rowspan="2">
                                Persentase
                            </th>
                            <th class="py-2 pr-3 font-medium" rowspan="2">
                                Status
                            </th>
                            <th class="py-2 text-right font-medium" rowspan="2">
                                Aksi
                            </th>
                        </tr>
                        <tr class="text-muted-foreground border-b text-xs">
                            <th
                                v-for="t in topics"
                                :key="t.id"
                                class="px-1.5 py-1.5 text-center font-medium"
                                :title="t.title"
                            >
                                {{ t.title }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="a in attempts.data"
                            :key="a.id"
                            class="hover:bg-secondary/40 border-b last:border-0"
                        >
                            <td class="py-3 pr-3">
                                <p class="font-medium">{{ a.user?.name }}</p>
                                <p class="text-muted-foreground text-xs">
                                    {{ a.user?.nim ?? a.user?.email }} ·
                                    {{ a.finished_label }}
                                </p>
                            </td>
                            <td
                                v-for="t in topics"
                                :key="t.id"
                                class="px-1.5 py-3 text-center font-semibold tabular-nums"
                                :style="{
                                    color: a.topic_levels[t.id]
                                        ? levelColor[a.topic_levels[t.id]]
                                        : undefined,
                                }"
                            >
                                {{
                                    a.topic_scores[t.id] !== undefined
                                        ? fmt(a.topic_scores[t.id])
                                        : '-'
                                }}
                            </td>
                            <td
                                class="py-3 pr-3 pl-3 text-center font-semibold tabular-nums"
                            >
                                {{ fmt(a.total_points)
                                }}<span
                                    class="text-muted-foreground text-xs font-normal"
                                    >/{{ a.max_points }}</span
                                >
                            </td>
                            <td class="py-3 pr-3">
                                <p class="font-bold tabular-nums">
                                    {{ fmt(a.percent) }}%
                                </p>
                                <p
                                    class="text-xs font-medium"
                                    :style="{ color: levelColor[a.level] }"
                                >
                                    {{ a.level_label }}
                                </p>
                            </td>
                            <td class="py-3 pr-3">
                                <div class="flex flex-col items-start gap-1">
                                    <Badge
                                        :variant="
                                            a.is_passed ? 'default' : 'outline'
                                        "
                                        >{{
                                            a.is_passed
                                                ? 'Tuntas'
                                                : 'Belum tuntas'
                                        }}</Badge
                                    >
                                    <Badge
                                        v-if="a.is_manual"
                                        variant="secondary"
                                        class="text-[10px]"
                                        >Input manual</Badge
                                    >
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        aria-label="Ubah nilai"
                                        @click="openEdit(a)"
                                    >
                                        <Pencil />
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="text-destructive hover:text-destructive"
                                        aria-label="Hapus nilai"
                                        @click="
                                            deleting = a;
                                            deleteOpen = true;
                                        "
                                    >
                                        <Trash2 />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!attempts.data.length">
                            <td
                                :colspan="topics.length + 5"
                                class="text-muted-foreground py-10 text-center"
                            >
                                Belum ada nilai yang cocok dengan filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination
                :links="attempts.links"
                :from="attempts.from"
                :to="attempts.to"
                :total="attempts.total"
            />
        </GlowCard>

        <Dialog v-model:open="formOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{
                        editing ? 'Ubah Nilai' : 'Input Nilai Pre-Test'
                    }}</DialogTitle>
                    <DialogDescription>
                        <template v-if="editing"
                            >{{ editing.user?.name }} — rekomendasi materi ikut
                            diperbarui sesuai nilai baru.</template
                        >
                        <template v-else
                            >Isi nilai 0–100 untuk setiap topik. Total dan
                            persentase dihitung otomatis.</template
                        >
                    </DialogDescription>
                </DialogHeader>
                <form class="grid gap-4" @submit.prevent="submit">
                    <div v-if="!editing" class="flex flex-col gap-2">
                        <Label for="user_id">Mahasiswa</Label>
                        <FormSelect
                            id="user_id"
                            v-model="form.user_id"
                            :options="
                                students.map((s) => ({
                                    value: s.id,
                                    label: s.nim
                                        ? `${s.name} (${s.nim})`
                                        : s.name,
                                }))
                            "
                        />
                        <InputError :message="form.errors.user_id" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <Label>Nilai per topik</Label>
                        <div class="flex flex-col gap-2">
                            <div
                                v-for="t in topics"
                                :key="t.id"
                                class="flex flex-col gap-1"
                            >
                                <div class="flex items-center gap-3">
                                    <label
                                        :for="`topic-${t.id}`"
                                        class="flex-1 text-sm"
                                        >{{ t.title }}</label
                                    >
                                    <Input
                                        :id="`topic-${t.id}`"
                                        v-model="form.topic_scores[t.id]"
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        class="w-24 text-right"
                                    />
                                </div>
                                <InputError
                                    :message="errors[`topic_scores.${t.id}`]"
                                />
                            </div>
                        </div>
                        <InputError :message="form.errors.topic_scores" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <Label for="finished_at">Tanggal pengerjaan</Label>
                        <Input
                            id="finished_at"
                            v-model="form.finished_at"
                            type="datetime-local"
                        />
                        <InputError :message="form.errors.finished_at" />
                    </div>

                    <div
                        class="bg-secondary/60 grid grid-cols-2 gap-2 rounded-xl px-4 py-3 text-sm"
                    >
                        <div>
                            <p class="text-muted-foreground text-xs">
                                Total nilai
                            </p>
                            <p class="font-bold tabular-nums">
                                {{ fmt(formTotal) }} / {{ formMax }}
                            </p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs">
                                Persentase keseluruhan
                            </p>
                            <p
                                class="font-bold tabular-nums"
                                :class="
                                    formPercent >= passingScore
                                        ? 'text-success'
                                        : 'text-destructive'
                                "
                            >
                                {{ fmt(formPercent) }}% ·
                                {{
                                    formPercent >= passingScore
                                        ? 'Tuntas'
                                        : 'Belum tuntas'
                                }}
                            </p>
                        </div>
                    </div>

                    <DialogFooter class="gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            @click="formOpen = false"
                            >Batal</Button
                        >
                        <Button type="submit" :disabled="form.processing">
                            <Save /> Simpan
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <ConfirmDialog
            v-model:open="deleteOpen"
            :title="`Hapus nilai ${deleting?.user?.name ?? ''}?`"
            description="Nilai per topik, total nilai, dan rekomendasi materi mahasiswa ini akan dihapus. Mahasiswa dapat mengerjakan pre-test kembali."
            :processing="deleteProcessing"
            @confirm="confirmDelete"
        />
    </div>
</template>
