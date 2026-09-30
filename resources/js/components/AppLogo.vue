<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div
        class="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-8 items-center justify-center rounded-md shadow-[0_0_14px_var(--color-glow)]"
    >
        <AppLogoIcon class="size-5" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-bold tracking-wide"
            >IPCARA</span
        >
        <span
            class="text-sidebar-foreground/60 truncate text-[10px] leading-tight"
            >Adaptive IPC Learning</span
        >
    </div>
</template>
