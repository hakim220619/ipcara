<script setup lang="ts">
import { computed } from 'vue';

export type LinePoint = { label: string; value: number };

const props = withDefaults(
    defineProps<{
        data: LinePoint[];
        height?: number;
        color?: string;
        max?: number;
    }>(),
    { height: 120, color: 'var(--color-chart-1)', max: 100 },
);

const width = 300;
const pad = 8;

const points = computed(() => {
    const n = props.data.length;
    if (n === 0) return [];
    const stepX = n > 1 ? (width - pad * 2) / (n - 1) : 0;
    return props.data.map((d, i) => ({
        x: pad + i * stepX,
        y:
            pad +
            (1 - Math.min(d.value, props.max) / props.max) *
                (props.height - pad * 2),
        ...d,
    }));
});

const linePath = computed(() =>
    points.value.map((p, i) => `${i === 0 ? 'M' : 'L'}${p.x},${p.y}`).join(' '),
);
const areaPath = computed(() => {
    if (points.value.length === 0) return '';
    const first = points.value[0];
    const last = points.value[points.value.length - 1];
    return `${linePath.value} L${last.x},${props.height - pad} L${first.x},${props.height - pad} Z`;
});
const gradientId = `grad-${Math.random().toString(36).slice(2, 8)}`;
</script>

<template>
    <div class="w-full">
        <svg
            :viewBox="`0 0 ${width} ${height}`"
            class="h-auto w-full overflow-visible"
            preserveAspectRatio="none"
        >
            <defs>
                <linearGradient :id="gradientId" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" :stop-color="color" stop-opacity="0.45" />
                    <stop offset="100%" :stop-color="color" stop-opacity="0" />
                </linearGradient>
            </defs>
            <line
                v-for="f in [0.25, 0.5, 0.75]"
                :key="f"
                :x1="pad"
                :x2="width - pad"
                :y1="pad + f * (height - pad * 2)"
                :y2="pad + f * (height - pad * 2)"
                class="stroke-border"
                stroke-dasharray="3 4"
            />
            <path v-if="areaPath" :d="areaPath" :fill="`url(#${gradientId})`" />
            <path
                v-if="linePath"
                :d="linePath"
                fill="none"
                :stroke="color"
                stroke-width="2.5"
                stroke-linejoin="round"
                stroke-linecap="round"
                :style="{ filter: `drop-shadow(0 0 5px ${color})` }"
            />
            <g v-for="p in points" :key="p.label">
                <circle
                    :cx="p.x"
                    :cy="p.y"
                    r="3.5"
                    :fill="color"
                    class="stroke-card"
                    stroke-width="2"
                >
                    <title>{{ p.label }}: {{ p.value }}</title>
                </circle>
            </g>
        </svg>
        <div
            class="text-muted-foreground mt-1 flex justify-between text-[10px]"
        >
            <span v-for="p in data" :key="p.label">{{ p.label }}</span>
        </div>
    </div>
</template>
