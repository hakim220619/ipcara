<script setup lang="ts">
import { computed } from 'vue';

export type BarPoint = { label: string; value: number };

const props = withDefaults(
    defineProps<{
        data: BarPoint[];
        height?: number;
        color?: string;
        highlightLast?: boolean;
    }>(),
    { height: 120, color: 'var(--color-chart-1)', highlightLast: true },
);

const max = computed(() => Math.max(1, ...props.data.map((d) => d.value)));
</script>

<template>
    <div class="flex w-full flex-col gap-2">
        <div
            class="flex w-full items-end gap-2"
            :style="{ height: `${height}px` }"
        >
            <div
                v-for="(point, i) in data"
                :key="point.label"
                class="group relative flex flex-1 flex-col items-center justify-end"
                :title="`${point.label}: ${point.value}`"
            >
                <span
                    class="text-muted-foreground mb-1 text-[10px] tabular-nums opacity-0 transition-opacity group-hover:opacity-100"
                    >{{ point.value }}</span
                >
                <div
                    class="w-full rounded-t-md transition-all duration-500"
                    :style="{
                        height: `${Math.max(4, (point.value / max) * (height - 20))}px`,
                        background: color,
                        opacity:
                            highlightLast && i !== data.length - 1 ? 0.55 : 1,
                        boxShadow:
                            highlightLast && i === data.length - 1
                                ? `0 0 12px ${color}`
                                : 'none',
                    }"
                />
            </div>
        </div>
        <div class="flex w-full gap-2">
            <span
                v-for="point in data"
                :key="point.label"
                class="text-muted-foreground flex-1 truncate text-center text-[10px]"
                >{{ point.label }}</span
            >
        </div>
    </div>
</template>
