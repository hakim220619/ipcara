<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        value: number;
        max?: number;
        size?: number;
        stroke?: number;
        label?: string;
        suffix?: string;
        color?: string;
    }>(),
    {
        max: 100,
        size: 112,
        stroke: 10,
        suffix: '%',
        color: 'var(--color-chart-1)',
    },
);

const radius = computed(() => (props.size - props.stroke) / 2);
const circumference = computed(() => 2 * Math.PI * radius.value);
const ratio = computed(() => Math.min(Math.max(props.value / props.max, 0), 1));
const dashOffset = computed(() => circumference.value * (1 - ratio.value));
</script>

<template>
    <div class="flex flex-col items-center gap-2">
        <div
            class="relative"
            :style="{ width: `${size}px`, height: `${size}px` }"
        >
            <svg :width="size" :height="size" class="-rotate-90">
                <circle
                    :cx="size / 2"
                    :cy="size / 2"
                    :r="radius"
                    fill="none"
                    class="stroke-muted"
                    :stroke-width="stroke"
                />
                <circle
                    :cx="size / 2"
                    :cy="size / 2"
                    :r="radius"
                    fill="none"
                    :stroke="color"
                    :stroke-width="stroke"
                    stroke-linecap="round"
                    :stroke-dasharray="circumference"
                    :stroke-dashoffset="dashOffset"
                    class="transition-[stroke-dashoffset] duration-700 ease-out"
                    :style="{ filter: `drop-shadow(0 0 6px ${color})` }"
                />
            </svg>
            <div
                class="absolute inset-0 flex flex-col items-center justify-center"
            >
                <span class="text-2xl font-bold tabular-nums"
                    >{{ Math.round(value) }}{{ suffix }}</span
                >
            </div>
        </div>
        <span v-if="label" class="text-muted-foreground text-center text-xs">{{
            label
        }}</span>
    </div>
</template>
