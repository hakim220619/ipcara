<script setup lang="ts">
withDefaults(
    defineProps<{
        value: number;
        label?: string;
        color?: string;
        showValue?: boolean;
    }>(),
    { color: 'var(--color-chart-1)', showValue: true },
);
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <div
            v-if="label || showValue"
            class="flex items-center justify-between text-xs"
        >
            <span class="truncate">{{ label }}</span>
            <span v-if="showValue" class="text-muted-foreground tabular-nums"
                >{{ Math.round(value) }}%</span
            >
        </div>
        <div class="bg-muted h-2 w-full overflow-hidden rounded-full">
            <div
                class="h-full rounded-full transition-all duration-700"
                :style="{
                    width: `${Math.min(100, Math.max(0, value))}%`,
                    background: color,
                    boxShadow: `0 0 8px ${color}`,
                }"
            />
        </div>
    </div>
</template>
