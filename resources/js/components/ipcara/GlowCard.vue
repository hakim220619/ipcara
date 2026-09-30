<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    title?: string;
    subtitle?: string;
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <section
        :class="
            cn(
                'bg-card text-card-foreground glow-card flex flex-col gap-4 rounded-2xl border p-5',
                props.class,
            )
        "
    >
        <header
            v-if="title || $slots.action"
            class="flex items-start justify-between gap-3"
        >
            <div>
                <h3 class="text-sm font-semibold tracking-tight">
                    {{ title }}
                </h3>
                <p v-if="subtitle" class="text-muted-foreground mt-0.5 text-xs">
                    {{ subtitle }}
                </p>
            </div>
            <slot name="action" />
        </header>
        <slot />
    </section>
</template>
