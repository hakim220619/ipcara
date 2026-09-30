<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();

const fallbackColor = 'var(--color-primary)';
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel
            class="text-[11px] font-semibold tracking-wider uppercase"
            >Menu Utama</SidebarGroupLabel
        >
        <SidebarMenu class="gap-1">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <!-- Menu utama yang menonjol (Dashboard) -->
                <SidebarMenuButton
                    v-if="item.highlight"
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="from-primary h-11 bg-gradient-to-r to-[hsl(212_90%_45%)] font-semibold text-white shadow-[0_6px_18px_-6px_var(--color-glow)] transition hover:text-white hover:brightness-110 active:text-white data-[active=true]:text-white group-data-[collapsible=icon]:p-1!"
                >
                    <Link :href="item.href">
                        <span
                            class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-white/20"
                        >
                            <component :is="item.icon" class="size-4" />
                        </span>
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>

                <SidebarMenuButton
                    v-else
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="group/nav h-10 transition group-data-[collapsible=icon]:p-0.5!"
                >
                    <Link :href="item.href">
                        <span
                            class="flex size-7 shrink-0 items-center justify-center rounded-lg transition group-hover/nav:scale-105"
                            :style="
                                isCurrentUrl(item.href)
                                    ? {
                                          backgroundColor:
                                              item.color ?? fallbackColor,
                                          color: 'white',
                                      }
                                    : {
                                          backgroundColor: `color-mix(in oklab, ${item.color ?? fallbackColor} 15%, transparent)`,
                                          color: item.color ?? fallbackColor,
                                      }
                            "
                        >
                            <component :is="item.icon" class="size-4" />
                        </span>
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
