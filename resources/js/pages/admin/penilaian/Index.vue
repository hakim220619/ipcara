<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Save, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import ConfirmDialog from '@/components/ipcara/ConfirmDialog.vue';
import FormSelect from '@/components/ipcara/FormSelect.vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
    destroy as criteriaDestroy,
    index as criteriaIndex,
    store as criteriaStore,
    update as criteriaUpdate,
} from '@/routes/admin/criteria';
import { update as settingsUpdate } from '@/routes/admin/settings';

type Level = 'rendah' | 'sedang' | 'tinggi';

type Criterion = {
    id: number;
    level: Level;
    label: string;
    min_score: number;
    max_score: number;
    recommended_material_level: 'dasar' | 'menengah' | 'lanjut';
    is_recommended: boolean;
    description: string | null;
};

const props = defineProps<{
    criteria: Criterion[];
    settings: { duration_minutes: number | null; passing_score: number } | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: adminDashboard() },
            { title: 'Kriteria Penilaian', href: criteriaIndex() },
        ],
    },
});

const levelColor: Record<Level, string> = {
    rendah: 'var(--color-destructive)',
    sedang: 'var(--color-warning)',
    tinggi: 'var(--color-success)',
};

const allLevels: { value: Level; label: string }[] = [
    { value: 'rendah', label: 'Rendah' },
    { value: 'sedang', label: 'Sedang' },
    { value: 'tinggi', label: 'Tinggi' },
];

const materialLevels = [
    { value: 'dasar', label: 'Dasar' },
    { value: 'menengah', label: 'Menengah' },
    { value: 'lanjut', label: 'Lanjut' },
];

const editing = ref<Criterion | null>(null);
const formOpen = ref(false);

const form = useForm({
    level: 'rendah' as Level,
    label: '',
    min_score: 0 as number | string,
    max_score: 0 as number | string,
    recommended_material_level: 'dasar',
    is_recommended: true,
    description: '',
});

const availableLevels = computed(() =>
    allLevels.filter(
        (l) =>
            l.value === editing.value?.level ||
            !props.criteria.some((c) => c.level === l.value),
    ),
);

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.level = availableLevels.value[0]?.value ?? 'rendah';
    formOpen.value = true;
}

function openEdit(c: Criterion) {
    editing.value = c;
    form.clearErrors();
    form.level = c.level;
    form.label = c.label;
    form.min_score = c.min_score;
    form.max_score = c.max_score;
    form.recommended_material_level = c.recommended_material_level;
    form.is_recommended = c.is_recommended;
    form.description = c.description ?? '';
    formOpen.value = true;
}

function submit() {
    const options = {
        preserveScroll: true,
        onSuccess: () => (formOpen.value = false),
    };
    if (editing.value) {
        form.put(criteriaUpdate.url(editing.value.id), options);
    } else {
        form.post(criteriaStore.url(), options);
    }
}

const deleting = ref<Criterion | null>(null);
const deleteOpen = ref(false);
const deleteProcessing = ref(false);

function confirmDelete() {
    if (!deleting.value) return;
    router.delete(criteriaDestroy.url(deleting.value.id), {
        preserveScroll: true,
        onStart: () => (deleteProcessing.value = true),
        onFinish: () => {
            deleteProcessing.value = false;
            deleteOpen.value = false;
        },
    });
}

const settingsForm = useForm({
    duration_minutes: props.settings?.duration_minutes ?? 60,
    passing_score: props.settings?.passing_score ?? 70,
});

function saveSettings() {
    settingsForm.put(settingsUpdate.url(), { preserveScroll: true });
}

const formatScore = (n: number) =>
    Number.isInteger(n) ? String(n) : n.toFixed(2);
</script>

<template>
    <Head title="Kriteria Penilaian" />

    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div
            class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    Kriteria Penilaian
                </h1>
                <p class="text-muted-foreground text-sm">
                    Menentukan kategori kemampuan dan materi yang
                    direkomendasikan dari persentase nilai setiap topik.
                </p>
            </div>
            <Button
                class="shadow-[0_0_18px_var(--color-glow)]"
                :disabled="availableLevels.length === 0"
                @click="openCreate"
            >
                <Plus /> Tambah Kriteria
            </Button>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1fr_20rem]">
            <GlowCard>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-sm">
                        <thead>
                            <tr
                                class="text-muted-foreground border-b text-left text-xs"
                            >
                                <th class="py-2 pr-3 font-medium">Kategori</th>
                                <th class="py-2 pr-3 font-medium">
                                    Rentang nilai
                                </th>
                                <th class="py-2 pr-3 font-medium">
                                    Materi awal
                                </th>
                                <th class="py-2 pr-3 font-medium">
                                    Rekomendasi
                                </th>
                                <th class="py-2 text-right font-medium">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="c in criteria"
                                :key="c.id"
                                class="border-b last:border-0"
                            >
                                <td class="py-3 pr-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="size-3 rounded-full"
                                            :style="{
                                                background: levelColor[c.level],
                                            }"
                                        ></span>
                                        <div>
                                            <p class="font-medium">
                                                {{ c.label }}
                                            </p>
                                            <p
                                                v-if="c.description"
                                                class="text-muted-foreground text-xs"
                                            >
                                                {{ c.description }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 pr-3 tabular-nums">
                                    {{ formatScore(c.min_score) }} –
                                    {{ formatScore(c.max_score) }}%
                                </td>
                                <td class="py-3 pr-3 capitalize">
                                    {{ c.recommended_material_level }}
                                </td>
                                <td class="py-3 pr-3">
                                    <Badge
                                        :variant="
                                            c.is_recommended
                                                ? 'default'
                                                : 'outline'
                                        "
                                        >{{
                                            c.is_recommended
                                                ? 'Direkomendasikan'
                                                : 'Tidak'
                                        }}</Badge
                                    >
                                </td>
                                <td class="py-3">
                                    <div class="flex justify-end gap-1">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            aria-label="Ubah kriteria"
                                            @click="openEdit(c)"
                                        >
                                            <Pencil />
                                        </Button>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            class="text-destructive hover:text-destructive"
                                            aria-label="Hapus kriteria"
                                            @click="
                                                deleting = c;
                                                deleteOpen = true;
                                            "
                                        >
                                            <Trash2 />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!criteria.length">
                                <td
                                    colspan="5"
                                    class="text-muted-foreground py-10 text-center"
                                >
                                    Belum ada kriteria. Sistem memakai kriteria
                                    bawaan (Rendah &lt; 60, Sedang 60–79, Tinggi
                                    ≥ 80).
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p
                    class="bg-accent text-accent-foreground rounded-xl p-3 text-xs"
                >
                    Perubahan kriteria berlaku untuk pre-test yang dikerjakan
                    setelahnya. Rekomendasi mahasiswa yang sudah dibuat tidak
                    berubah.
                </p>
            </GlowCard>

            <GlowCard v-if="settings" title="Pengaturan Pre-Test" class="h-fit">
                <form
                    class="flex flex-col gap-4"
                    @submit.prevent="saveSettings"
                >
                    <div class="flex flex-col gap-2">
                        <Label for="passing_score">KKM (%)</Label>
                        <Input
                            id="passing_score"
                            v-model="settingsForm.passing_score"
                            type="number"
                            min="0"
                            max="100"
                        />
                        <InputError
                            :message="settingsForm.errors.passing_score"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="duration_minutes">Durasi (menit)</Label>
                        <Input
                            id="duration_minutes"
                            v-model="settingsForm.duration_minutes"
                            type="number"
                            min="5"
                            max="240"
                        />
                        <InputError
                            :message="settingsForm.errors.duration_minutes"
                        />
                    </div>
                    <Button type="submit" :disabled="settingsForm.processing">
                        <Save /> Simpan Pengaturan
                    </Button>
                </form>
            </GlowCard>
        </div>

        <Dialog v-model:open="formOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{
                        editing ? 'Ubah Kriteria' : 'Tambah Kriteria'
                    }}</DialogTitle>
                    <DialogDescription
                        >Rentang nilai tidak boleh bertabrakan dengan kriteria
                        lain.</DialogDescription
                    >
                </DialogHeader>
                <form class="grid gap-4" @submit.prevent="submit">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <Label for="level">Kategori</Label>
                            <FormSelect
                                id="level"
                                v-model="form.level"
                                :options="availableLevels"
                            />
                            <InputError :message="form.errors.level" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="label">Nama tampilan</Label>
                            <Input
                                id="label"
                                v-model="form.label"
                                placeholder="mis. Rendah"
                            />
                            <InputError :message="form.errors.label" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="min_score">Nilai minimum (%)</Label>
                            <Input
                                id="min_score"
                                v-model="form.min_score"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                            />
                            <InputError :message="form.errors.min_score" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="max_score">Nilai maksimum (%)</Label>
                            <Input
                                id="max_score"
                                v-model="form.max_score"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                            />
                            <InputError :message="form.errors.max_score" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="material">Level materi awal</Label>
                            <FormSelect
                                id="material"
                                v-model="form.recommended_material_level"
                                :options="materialLevels"
                            />
                            <InputError
                                :message="
                                    form.errors.recommended_material_level
                                "
                            />
                        </div>
                        <Label
                            class="flex items-center gap-2 self-end pb-2 font-normal"
                        >
                            <Checkbox v-model="form.is_recommended" />
                            Tampilkan sebagai rekomendasi
                        </Label>
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="description">Keterangan</Label>
                        <Input
                            id="description"
                            v-model="form.description"
                            placeholder="Opsional"
                        />
                        <InputError :message="form.errors.description" />
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
            :title="`Hapus kriteria ${deleting?.label ?? ''}?`"
            description="Nilai yang masuk rentang ini akan memakai kriteria terdekat di bawahnya."
            :processing="deleteProcessing"
            @confirm="confirmDelete"
        />
    </div>
</template>
