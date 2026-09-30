<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { RotateCcw, Save } from '@lucide/vue';
import { computed, onBeforeUnmount, watch } from 'vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import ProgressBar from '@/components/ipcara/ProgressBar.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { applyThemeCss } from '@/lib/theme';
import { dashboard as adminDashboard } from '@/routes/admin';
import {
    index as appearanceIndex,
    update as appearanceUpdate,
} from '@/routes/admin/appearance';

type ColorField = {
    key: string;
    label: string;
    group: string;
    vars: string[];
    default: string;
    value: string;
};

const props = defineProps<{ colors: ColorField[] }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: adminDashboard() },
            { title: 'Pengaturan Tampilan', href: appearanceIndex() },
        ],
    },
});

const page = usePage();

const form = useForm({
    colors: Object.fromEntries(
        props.colors.map((c) => [c.key, c.value]),
    ) as Record<string, string>,
});

const groups = computed(() => {
    const map = new Map<string, ColorField[]>();
    props.colors.forEach((c) => {
        if (!map.has(c.group)) map.set(c.group, []);
        map.get(c.group)!.push(c);
    });
    return [...map.entries()];
});

const isHex = (v: string) => /^#[0-9a-fA-F]{6}$/.test(v);

const previewCss = computed(() => {
    const vars: string[] = [];
    props.colors.forEach((c) => {
        const value = form.colors[c.key];
        if (value && isHex(value) && value.toLowerCase() !== c.default) {
            c.vars.forEach((v) => vars.push(`${v}:${value}`));
        }
    });
    return vars.length ? `:root:root{${vars.join(';')}}` : '';
});

watch(previewCss, (css) => applyThemeCss(css), { immediate: true });

onBeforeUnmount(() => applyThemeCss(page.props.themeCss));

const changedCount = computed(
    () =>
        props.colors.filter(
            (c) => form.colors[c.key]?.toLowerCase() !== c.default,
        ).length,
);

const errors = computed(
    () => form.errors as Record<string, string | undefined>,
);

function resetOne(c: ColorField) {
    form.colors[c.key] = c.default;
}

function resetAll() {
    props.colors.forEach((c) => (form.colors[c.key] = c.default));
}

function onHexInput(key: string, value: string | number) {
    let v = String(value).trim();
    if (v && !v.startsWith('#')) v = `#${v}`;
    form.colors[key] = v;
}

function submit() {
    form.put(appearanceUpdate.url(), { preserveScroll: true });
}
</script>

<template>
    <Head title="Pengaturan Tampilan" />

    <form
        class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6"
        @submit.prevent="submit"
    >
        <div
            class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
        >
            <div>
                <h1 class="text-2xl font-bold tracking-tight">
                    Pengaturan Tampilan
                </h1>
                <p class="text-muted-foreground text-sm">
                    Atur warna aplikasi. Perubahan langsung terlihat sebagai
                    pratinjau dan berlaku untuk semua pengguna setelah disimpan.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <Button type="button" variant="outline" @click="resetAll">
                    <RotateCcw /> Kembalikan semua ke bawaan
                </Button>
                <Button
                    type="submit"
                    class="shadow-[0_0_18px_var(--color-glow)]"
                    :disabled="form.processing"
                >
                    <Save /> Simpan
                </Button>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-[1fr_22rem]">
            <div class="grid content-start gap-5 2xl:grid-cols-2">
                <GlowCard
                    v-for="[group, items] in groups"
                    :key="group"
                    :title="group"
                >
                    <div
                        v-for="c in items"
                        :key="c.key"
                        class="flex flex-col gap-1"
                    >
                        <div class="flex items-center gap-3">
                            <label
                                class="relative size-10 shrink-0 cursor-pointer overflow-hidden rounded-lg border shadow-xs"
                                :style="{
                                    background: isHex(form.colors[c.key])
                                        ? form.colors[c.key]
                                        : c.default,
                                }"
                                :title="`Pilih warna ${c.label}`"
                            >
                                <input
                                    v-model="form.colors[c.key]"
                                    type="color"
                                    class="absolute inset-0 size-full cursor-pointer opacity-0"
                                    :aria-label="c.label"
                                />
                            </label>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm leading-tight font-medium">
                                    {{ c.label }}
                                </p>
                                <p class="text-muted-foreground text-[11px]">
                                    Bawaan {{ c.default.toUpperCase() }}
                                </p>
                            </div>
                            <Input
                                :model-value="form.colors[c.key]"
                                class="w-24 px-2 font-mono text-xs uppercase"
                                maxlength="7"
                                @update:model-value="
                                    (v) => onHexInput(c.key, v)
                                "
                            />
                            <Button
                                type="button"
                                size="icon"
                                variant="ghost"
                                :disabled="
                                    form.colors[c.key]?.toLowerCase() ===
                                    c.default
                                "
                                :aria-label="`Kembalikan ${c.label}`"
                                @click="resetOne(c)"
                            >
                                <RotateCcw />
                            </Button>
                        </div>
                        <InputError :message="errors[`colors.${c.key}`]" />
                    </div>
                </GlowCard>
            </div>

            <GlowCard
                title="Pratinjau"
                :subtitle="`${changedCount} warna berbeda dari bawaan`"
                class="h-fit xl:sticky xl:top-4"
            >
                <div class="overflow-hidden rounded-xl border">
                    <div class="flex">
                        <div
                            class="bg-sidebar text-sidebar-foreground w-28 p-3"
                        >
                            <div class="mb-3 flex items-center gap-1.5">
                                <span
                                    class="bg-sidebar-primary size-5 rounded"
                                ></span>
                                <span class="text-xs font-bold">IPCARA</span>
                            </div>
                            <p
                                class="bg-sidebar-accent text-sidebar-accent-foreground mb-1 rounded px-2 py-1 text-[11px]"
                            >
                                Dashboard
                            </p>
                            <p class="px-2 py-1 text-[11px]">Materi</p>
                            <p class="px-2 py-1 text-[11px]">Progres</p>
                        </div>
                        <div class="bg-background flex-1 p-3">
                            <div class="bg-card rounded-lg border p-3">
                                <p class="text-sm font-semibold">Judul kartu</p>
                                <p class="text-muted-foreground text-xs">
                                    Teks keterangan
                                </p>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <Button size="sm" type="button"
                                        >Tombol</Button
                                    >
                                    <Badge
                                        class="bg-accent text-accent-foreground"
                                        >Label</Badge
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <ProgressBar
                        :value="90"
                        label="Tinggi"
                        color="var(--color-success)"
                    />
                    <ProgressBar
                        :value="65"
                        label="Sedang"
                        color="var(--color-warning)"
                    />
                    <ProgressBar
                        :value="35"
                        label="Rendah"
                        color="var(--color-destructive)"
                    />
                </div>
                <p class="bg-secondary rounded-lg p-3 text-xs">
                    Latar sekunder dipakai untuk kotak ringkasan dan baris
                    tabel.
                </p>
            </GlowCard>
        </div>
    </form>
</template>
