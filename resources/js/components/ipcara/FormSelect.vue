<script setup lang="ts">
const model = defineModel<string | number | null>();

defineProps<{
    options: { value: string | number; label: string }[];
    placeholder?: string;
    invalid?: boolean;
}>();
</script>

<template>
    <select
        v-model="model"
        class="border-input focus-visible:border-ring focus-visible:ring-ring/50 h-9 w-full rounded-md border bg-transparent px-3 text-sm shadow-xs outline-none focus-visible:ring-[3px]"
        :class="invalid ? 'border-destructive' : ''"
        :aria-invalid="invalid || undefined"
    >
        <option v-if="placeholder" :value="''">{{ placeholder }}</option>
        <option v-for="o in options" :key="o.value" :value="o.value">
            {{ o.label }}
        </option>
    </select>
</template>
