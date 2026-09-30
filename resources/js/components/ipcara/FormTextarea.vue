<script setup lang="ts">
const model = defineModel<string | null>();

defineProps<{ rows?: number; placeholder?: string; invalid?: boolean }>();
</script>

<template>
    <textarea
        v-model="model"
        :rows="rows ?? 3"
        :placeholder="placeholder"
        class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:ring-[3px]"
        :class="invalid ? 'border-destructive' : ''"
        :aria-invalid="invalid || undefined"
    ></textarea>
</template>
