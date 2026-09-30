<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: { url: string | null; label: string; active: boolean }[];
    from: number | null;
    to: number | null;
    total: number;
}>();

const clean = (label: string) =>
    label
        .replace('&laquo;', '‹')
        .replace('&raquo;', '›')
        .replace(/Previous|Sebelumnya/i, '')
        .replace(/Next|Berikutnya/i, '')
        .trim();
</script>

<template>
    <div
        class="flex flex-col items-center justify-between gap-3 text-sm sm:flex-row"
    >
        <p class="text-muted-foreground">
            Menampilkan {{ from ?? 0 }}–{{ to ?? 0 }} dari {{ total }} data
        </p>
        <nav v-if="links.length > 3" class="flex flex-wrap gap-1">
            <template v-for="(link, i) in links" :key="i">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-scroll
                    preserve-state
                    class="flex h-8 min-w-8 items-center justify-center rounded-md border px-2 transition"
                    :class="
                        link.active
                            ? 'bg-primary text-primary-foreground border-primary'
                            : 'hover:bg-accent'
                    "
                    >{{ clean(link.label) }}</Link
                >
                <span
                    v-else
                    class="text-muted-foreground flex h-8 min-w-8 items-center justify-center rounded-md border px-2 opacity-50"
                    >{{ clean(link.label) }}</span
                >
            </template>
        </nav>
    </div>
</template>
