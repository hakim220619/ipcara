<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Save, Search, Trash2 } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import ConfirmDialog from '@/components/ipcara/ConfirmDialog.vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import Pagination from '@/components/ipcara/Pagination.vue';
import FormSelect from '@/components/ipcara/FormSelect.vue';
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
    destroy as usersDestroy,
    index as usersIndex,
    store as usersStore,
    update as usersUpdate,
} from '@/routes/admin/users';

type Role = 'mahasiswa' | 'admin';

type UserRow = {
    id: number;
    name: string;
    username: string | null;
    email: string;
    role: Role;
    nim: string | null;
    semester: number | null;
    program_studi: string | null;
    pre_test_score: number | null;
    is_self: boolean;
    created_at: string | null;
};

const props = defineProps<{
    users: {
        data: UserRow[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number | null;
        to: number | null;
        total: number;
    };
    filters: { search?: string; role?: string };
    counts: { all: number; mahasiswa: number; admin: number };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: adminDashboard() },
            { title: 'Pengguna', href: usersIndex() },
        ],
    },
});

const roleOptions = [
    { value: 'mahasiswa', label: 'Mahasiswa' },
    { value: 'admin', label: 'Admin' },
];

const search = ref(props.filters.search ?? '');
const role = ref(props.filters.role ?? '');
let debounce: ReturnType<typeof setTimeout> | undefined;

function applyFilters() {
    router.get(
        usersIndex.url(),
        { search: search.value || undefined, role: role.value || undefined },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

watch(search, () => {
    clearTimeout(debounce);
    debounce = setTimeout(applyFilters, 300);
});
watch(role, applyFilters);

const editing = ref<UserRow | null>(null);
const formOpen = ref(false);

const form = useForm({
    name: '',
    username: '',
    email: '',
    role: 'mahasiswa' as Role,
    nim: '',
    semester: '' as number | string,
    program_studi: 'Keperawatan',
    password: '',
    password_confirmation: '',
});

const isMahasiswa = computed(() => form.role === 'mahasiswa');

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.role = role.value === 'admin' ? 'admin' : 'mahasiswa';
    formOpen.value = true;
}

function openEdit(u: UserRow) {
    editing.value = u;
    form.clearErrors();
    form.name = u.name;
    form.username = u.username ?? '';
    form.email = u.email;
    form.role = u.role;
    form.nim = u.nim ?? '';
    form.semester = u.semester ?? '';
    form.program_studi = u.program_studi ?? '';
    form.password = '';
    form.password_confirmation = '';
    formOpen.value = true;
}

function submit() {
    const options = {
        preserveScroll: true,
        onSuccess: () => (formOpen.value = false),
    };
    if (editing.value) {
        form.put(usersUpdate.url(editing.value.id), options);
    } else {
        form.post(usersStore.url(), options);
    }
}

const deleting = ref<UserRow | null>(null);
const deleteOpen = ref(false);
const deleteProcessing = ref(false);

function confirmDelete() {
    if (!deleting.value) return;
    router.delete(usersDestroy.url(deleting.value.id), {
        preserveScroll: true,
        onStart: () => (deleteProcessing.value = true),
        onFinish: () => {
            deleteProcessing.value = false;
            deleteOpen.value = false;
        },
    });
}

const initials = (name: string) =>
    name
        .split(' ')
        .map((w) => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
</script>

<template>
    <Head title="Pengguna" />

    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div
            class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Pengguna</h1>
                <p class="text-muted-foreground text-sm">
                    Kelola akun mahasiswa dan admin.
                </p>
            </div>
            <Button
                class="shadow-[0_0_18px_var(--color-glow)]"
                @click="openCreate"
            >
                <Plus /> Tambah Pengguna
            </Button>
        </div>

        <div class="flex flex-wrap gap-2">
            <button
                v-for="tab in [
                    { value: '', label: 'Semua', count: counts.all },
                    {
                        value: 'mahasiswa',
                        label: 'Mahasiswa',
                        count: counts.mahasiswa,
                    },
                    { value: 'admin', label: 'Admin', count: counts.admin },
                ]"
                :key="tab.value"
                type="button"
                class="inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-sm transition"
                :class="
                    role === tab.value
                        ? 'bg-primary text-primary-foreground border-primary'
                        : 'bg-card hover:border-primary/50'
                "
                @click="role = tab.value"
            >
                {{ tab.label }}
                <span
                    class="rounded-full px-1.5 text-xs tabular-nums"
                    :class="role === tab.value ? 'bg-white/20' : 'bg-secondary'"
                    >{{ tab.count }}</span
                >
            </button>
        </div>

        <GlowCard>
            <div class="relative">
                <Search
                    class="text-muted-foreground absolute top-1/2 left-3 size-4 -translate-y-1/2"
                />
                <Input
                    v-model="search"
                    placeholder="Cari nama, username, email, atau NIM..."
                    class="pl-9"
                />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-sm">
                    <thead>
                        <tr
                            class="text-muted-foreground border-b text-left text-xs"
                        >
                            <th class="py-2 pr-3 font-medium">Nama</th>
                            <th class="py-2 pr-3 font-medium">Role</th>
                            <th class="py-2 pr-3 font-medium">NIM / Prodi</th>
                            <th class="py-2 pr-3 font-medium">Pre-test</th>
                            <th class="py-2 pr-3 font-medium">Terdaftar</th>
                            <th class="py-2 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="u in users.data"
                            :key="u.id"
                            class="hover:bg-secondary/40 border-b last:border-0"
                        >
                            <td class="py-3 pr-3">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex size-9 shrink-0 items-center justify-center rounded-full text-xs font-semibold"
                                        :class="
                                            u.role === 'admin'
                                                ? 'bg-primary text-primary-foreground'
                                                : 'bg-accent text-accent-foreground'
                                        "
                                        >{{ initials(u.name) }}</span
                                    >
                                    <div class="min-w-0">
                                        <p class="truncate font-medium">
                                            {{ u.name }}
                                            <span
                                                v-if="u.is_self"
                                                class="text-muted-foreground text-xs font-normal"
                                                >(Anda)</span
                                            >
                                        </p>
                                        <p
                                            class="text-muted-foreground truncate text-xs"
                                        >
                                            <template v-if="u.username"
                                                >@{{ u.username }} ·
                                            </template>
                                            {{ u.email }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 pr-3">
                                <Badge
                                    :variant="
                                        u.role === 'admin'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                    class="capitalize"
                                    >{{ u.role }}</Badge
                                >
                            </td>
                            <td class="py-3 pr-3">
                                <template v-if="u.role === 'mahasiswa'">
                                    <p class="tabular-nums">
                                        {{ u.nim ?? '-' }}
                                    </p>
                                    <p class="text-muted-foreground text-xs">
                                        <template v-if="u.semester"
                                            >Semester {{ u.semester }} ·
                                        </template>
                                        {{ u.program_studi ?? '-' }}
                                    </p>
                                </template>
                                <span v-else class="text-muted-foreground"
                                    >-</span
                                >
                            </td>
                            <td class="py-3 pr-3">
                                <template v-if="u.role === 'mahasiswa'">
                                    <span
                                        v-if="u.pre_test_score !== null"
                                        class="font-semibold tabular-nums"
                                        >{{
                                            Math.round(u.pre_test_score)
                                        }}%</span
                                    >
                                    <Badge
                                        v-else
                                        variant="outline"
                                        class="text-[10px]"
                                        >Belum</Badge
                                    >
                                </template>
                                <span v-else class="text-muted-foreground"
                                    >-</span
                                >
                            </td>
                            <td
                                class="text-muted-foreground py-3 pr-3 text-xs whitespace-nowrap"
                            >
                                {{ u.created_at }}
                            </td>
                            <td class="py-3">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        aria-label="Ubah pengguna"
                                        @click="openEdit(u)"
                                    >
                                        <Pencil />
                                    </Button>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        class="text-destructive hover:text-destructive"
                                        aria-label="Hapus pengguna"
                                        :disabled="u.is_self"
                                        @click="
                                            deleting = u;
                                            deleteOpen = true;
                                        "
                                    >
                                        <Trash2 />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.data.length">
                            <td
                                colspan="6"
                                class="text-muted-foreground py-10 text-center"
                            >
                                Tidak ada pengguna yang cocok dengan filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination
                :links="users.links"
                :from="users.from"
                :to="users.to"
                :total="users.total"
            />
        </GlowCard>

        <Dialog v-model:open="formOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{
                        editing ? 'Ubah Pengguna' : 'Tambah Pengguna'
                    }}</DialogTitle>
                    <DialogDescription>
                        {{
                            editing
                                ? 'Kosongkan password bila tidak ingin mengubahnya.'
                                : 'Akun langsung aktif dan dapat dipakai untuk masuk.'
                        }}
                    </DialogDescription>
                </DialogHeader>
                <form class="grid gap-4" @submit.prevent="submit">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex flex-col gap-2 sm:col-span-2">
                            <Label for="name">Nama lengkap</Label>
                            <Input id="name" v-model="form.name" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="username">Username</Label>
                            <Input
                                id="username"
                                v-model="form.username"
                                autocomplete="off"
                            />
                            <InputError :message="form.errors.username" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                            />
                            <InputError :message="form.errors.email" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="role">Role</Label>
                            <FormSelect
                                id="role"
                                v-model="form.role"
                                :options="roleOptions"
                            />
                            <InputError :message="form.errors.role" />
                        </div>
                        <template v-if="isMahasiswa">
                            <div class="flex flex-col gap-2">
                                <Label for="nim">NIM</Label>
                                <Input id="nim" v-model="form.nim" />
                                <InputError :message="form.errors.nim" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <Label for="semester">Semester</Label>
                                <FormSelect
                                    id="semester"
                                    v-model="form.semester"
                                    placeholder="Pilih semester"
                                    :options="
                                        Array.from({ length: 14 }, (_, i) => ({
                                            value: i + 1,
                                            label: `Semester ${i + 1}`,
                                        }))
                                    "
                                />
                                <InputError :message="form.errors.semester" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <Label for="program_studi">Program studi</Label>
                                <Input
                                    id="program_studi"
                                    v-model="form.program_studi"
                                />
                                <InputError
                                    :message="form.errors.program_studi"
                                />
                            </div>
                        </template>
                        <div class="flex flex-col gap-2">
                            <Label for="password">{{
                                editing ? 'Password baru' : 'Password'
                            }}</Label>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                autocomplete="new-password"
                            />
                            <InputError :message="form.errors.password" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <Label for="password_confirmation"
                                >Konfirmasi password</Label
                            >
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                autocomplete="new-password"
                            />
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
            :title="`Hapus pengguna ${deleting?.name ?? ''}?`"
            description="Akun beserta seluruh data belajarnya (nilai pre-test, progres materi, rekomendasi) akan dihapus permanen."
            :processing="deleteProcessing"
            @confirm="confirmDelete"
        />
    </div>
</template>
