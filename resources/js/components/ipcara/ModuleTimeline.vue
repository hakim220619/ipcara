<script setup lang="ts">
export type TimelineStep = {
    title: string;
    status: 'selesai' | 'aktif' | 'terkunci' | 'belum';
    subtitle?: string;
};

defineProps<{ steps: TimelineStep[] }>();
</script>

<template>
    <ol class="relative flex w-full items-start justify-between gap-2">
        <li
            v-for="(step, i) in steps"
            :key="step.title"
            class="relative flex flex-1 flex-col items-center text-center"
        >
            <div
                v-if="i < steps.length - 1"
                class="absolute top-3 left-1/2 h-0.5 w-full"
                :class="step.status === 'selesai' ? 'bg-primary' : 'bg-border'"
            />
            <div
                class="relative z-10 flex size-6 items-center justify-center rounded-full border-2 text-[10px] font-bold"
                :class="{
                    'border-primary bg-primary text-primary-foreground':
                        step.status === 'selesai',
                    'border-primary bg-card text-primary shadow-[0_0_12px_var(--color-glow)]':
                        step.status === 'aktif',
                    'border-border bg-card text-muted-foreground':
                        step.status === 'belum' || step.status === 'terkunci',
                }"
            >
                <span v-if="step.status === 'selesai'">✓</span>
                <span v-else>{{ i + 1 }}</span>
            </div>
            <span
                class="mt-2 line-clamp-2 text-[11px] leading-tight"
                :class="
                    step.status === 'aktif'
                        ? 'text-primary font-semibold'
                        : 'text-muted-foreground'
                "
                >{{ step.title }}</span
            >
            <span
                v-if="step.subtitle"
                class="text-muted-foreground mt-0.5 text-[10px]"
                >{{ step.subtitle }}</span
            >
        </li>
    </ol>
</template>
