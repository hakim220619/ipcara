<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Save, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import ConfirmDialog from '@/components/ipcara/ConfirmDialog.vue';
import FormTextarea from '@/components/ipcara/FormTextarea.vue';
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
    destroy as topicsDestroy,
    index as topicsIndex,
    store as topicsStore,
    update as topicsUpdate,
} from '@/routes/admin/topics';

type TopicRow = {
    id: number;
    code: string;
    title: string;
    slug: string;
    description: string | null;
    learning_objectives: string | null;
    order: number;
    is_published: boolean;
    materials_count: number;
    questions_count: number;
};

const props = defineProps<{ topics: TopicRow[]; nextOrder: number }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: adminDashboard() },
            { title: 'Topik', href: topicsIndex() },
        ],
    },
});

const editing = ref<TopicRow | null>(null);
const formOpen = ref(false);

const form = useForm({
    code: '',
    title: '',
    description: '',
    learning_objectives: '',
    order: 1 as number | string,
    is_published: true,
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.code = `PPI-${String(props.nextOrder).padStart(2, '0')}`;
    form.order = props.nextOrder;
    formOpen.value = true;
}

function openEdit(t: TopicRow) {
    editing.value = t;
    form.clearErrors();
    form.code = t.code;
    form.title = t.title;
    form.description = t.description ?? '';
    form.learning_objectives = t.learning_objectives ?? '';
    form.order = t.order;
    form.is_published = t.is_published;
    formOpen.value = true;
}

function submit() {
    const options = {
        preserveScroll: true,
        onSuccess: () => (formOpen.value = false),
    };
    if (editing.value) {
        form.put(topicsUpdate.url(editing.value.id), options);
    } else {
        form.post(topicsStore.url(), options);
    }
}

const deleting = ref<TopicRow | null>(null);
const deleteOpen = ref(false);
const deleteProcessing = ref(false);

function askDelete(t: TopicRow) {
    deleting.value = t;
    deleteOpen.value = true;
}

function confirmDelete() {
    if (!deleting.value) return;
    router.delete(topicsDestroy.url(deleting.value.id), {
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
    <Head title="Topik" />

    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div
            class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Topik</h1>
                <p class="text-muted-foreground text-sm">
                    {{ topics.length }} topik PPI · topik yang tidak
                    dipublikasikan tidak tampil di menu materi mahasiswa.
                </p>
            </div>
            <Button
                class="shadow-[0_0_18px_var(--color-glow)]"
                @click="openCreate"
            >
                <Plus /> Tambah Topik
            </Button>
        </div>

        <GlowCard>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm">
                    <thead>
                        <tr
                            class="text-muted-foreground border-b text-left text-xs"
                        >
                            <th class="w-16 py-2 pr-3 font-medium">Urutan</th>
                            <th class="py-2 pr-3 font-medium">Topik</th>
                            <th class="py-2 pr-3 text-center font-medium">
                                Materi
                            </th>
                            <th class="py-2 pr-3 text-center font-medium">
                                Soal
                            </th>
                            <th class="py-2 pr-3 font-medium">Status</th>
                            <th class="py-2 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="t in topics"
                            :key="t.id"
                            class="hover:bg-secondary/40 border-b last:border-0"
                        >
                            <td class="py-3 pr-3">
                                <span
                                    class="bg-primary text-primary-foreground inline-flex size-7 items-center justify-center rounded-full text-xs font-semibold"
                                    >{{ t.order }}</span
                                >
                            </td>
                            <td class="max-w-md py-3 pr-3">
                                <p class="text-muted-foreground text-xs">
                                    {{ t.code }}
                                </p>
                                <p class="font-medium">{{ t.title }}</p>
                                <p
                                    v-if="t.description"
                                    class="text-muted-foreground line-clamp-1 text-xs"
                                >
                                    {{ t.description }}
                                </p>
                            </td>
                            <td class="py-3 pr-3 text-center tabular-nums">
                                {{ t.materials_count }}
                            </td>
                            <td class="py-3 pr-3 text-center tabular-nums">
                                {{ t.questions_count }}
                            </td>
                            <td class="py-3 pr-3">
                                <Badge
                                    :variant="
                                        t.is_published ? 'default' : 'outline'
                                    "
                                    >{{
                                        t.is_published ? 'Terbit' : 'Draf'
                                    }}</Badge
                                >
                            </td>
                            <td class="py-3">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        aria-label="Ubah topik"
                                        @click="openEdit(t)"
                                    >
                                        <Pencil />
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="text-destructive hover:text-destructive"
                                        aria-label="Hapus topik"
                                        @click="askDelete(t)"
                                    >
                                        <Trash2 />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!topics.length">
                            <td
                                colspan="6"
                                class="text-muted-foreground py-10 text-center"
                            >
                                Belum ada topik.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </GlowCard>

        <Dialog v-model:open="formOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{
                        editing ? 'Ubah Topik' : 'Tambah Topik'
                    }}</DialogTitle>
                    <DialogDescription
                        >Topik mengelompokkan materi dan soal
                        pre-test.</DialogDescription
                    >
                </DialogHeader>
                <form class="grid gap-4" @submit.prevent="submit">
                    <div class="grid gap-4 sm:grid-cols-[1fr_7rem]">
                        <div class="flex flex-col gap-2">
                            <Label for="code">Kode</Label>
                            <Input
                                id="code"
                                v-model="form.code"
                                placeholder="PPI-06"
                            />
                            <InputError :message="form.errors.code" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="order">Urutan</Label>
                            <Input
                                id="order"
                                v-model="form.order"
                                type="number"
                                min="0"
                            />
                            <InputError :message="form.errors.order" />
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="title">Judul topik</Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            placeholder="mis. Sterilisasi Alat"
                        />
                        <InputError :message="form.errors.title" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="description">Deskripsi singkat</Label>
                        <FormTextarea
                            id="description"
                            v-model="form.description"
                            :rows="2"
                            :invalid="!!form.errors.description"
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <Label for="learning_objectives"
                            >Tujuan pembelajaran</Label
                        >
                        <FormTextarea
                            id="learning_objectives"
                            v-model="form.learning_objectives"
                            :rows="3"
                            :invalid="!!form.errors.learning_objectives"
                        />
                        <InputError
                            :message="form.errors.learning_objectives"
                        />
                    </div>
                    <Label class="flex items-center gap-2 font-normal">
                        <Checkbox v-model="form.is_published" />
                        Terbitkan (tampil untuk mahasiswa)
                    </Label>
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
            :title="`Hapus topik ${deleting?.title ?? ''}?`"
            :description="`${deleting?.materials_count ?? 0} materi dan ${deleting?.questions_count ?? 0} soal di topik ini ikut terhapus, termasuk dari pre-test. Tindakan ini tidak dapat dibatalkan.`"
            :processing="deleteProcessing"
            @confirm="confirmDelete"
        />
    </div>
</template>
