<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpenCheck,
    ClipboardCheck,
    ClipboardList,
    GraduationCap,
    LayoutGrid,
    LineChart,
    Palette,
    Layers,
    Scale,
    UserRound,
    UsersRound,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { dashboard as adminDashboard } from '@/routes/admin';
import { index as appearanceIndex } from '@/routes/admin/appearance';
import { index as criteriaIndex } from '@/routes/admin/criteria';
import { index as questionsIndex } from '@/routes/admin/questions';
import { index as scoresIndex } from '@/routes/admin/scores';
import { index as topicsIndex } from '@/routes/admin/topics';
import { index as usersIndex } from '@/routes/admin/users';
import { index as materiIndex } from '@/routes/materi';
import { index as postTestIndex } from '@/routes/post-test';
import { index as preTestIndex } from '@/routes/pre-test';
import { index as tesIndex } from '@/routes/tes';
import { edit as profileEdit } from '@/routes/profile';
import type { NavItem } from '@/types';

const page = usePage();

const preTestItems: NavItem[] = [
    {
        title: 'Pre-Test',
        href: preTestIndex(),
        icon: ClipboardCheck,
        highlight: true,
    },
];

const learningItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        highlight: true,
    },
    {
        title: 'Profil Saya',
        href: profileEdit(),
        icon: UserRound,
        color: 'var(--color-chart-2)',
    },
    {
        title: 'Materi Belajar',
        href: materiIndex(),
        icon: BookOpenCheck,
        color: 'var(--color-chart-1)',
    },
    {
        title: 'Tes & Latihan',
        href: tesIndex(),
        icon: ClipboardList,
        color: 'var(--color-chart-3)',
    },
    {
        title: 'Post-Test',
        href: postTestIndex(),
        icon: ClipboardCheck,
        color: 'var(--color-chart-4)',
    },
    {
        title: 'Progres Saya',
        href: dashboard.url() + '#progres',
        icon: LineChart,
        color: 'var(--color-chart-5)',
    },
];

const adminItems: NavItem[] = [
    {
        title: 'Dashboard Admin',
        href: adminDashboard(),
        icon: LayoutGrid,
        highlight: true,
    },
    { title: 'Topik', href: topicsIndex(), icon: Layers },
    { title: 'Bank Soal', href: questionsIndex(), icon: ClipboardList },
    {
        title: 'Soal Post-Test',
        href: questionsIndex({ query: { tes: 'post_test' } }),
        icon: ClipboardCheck,
    },
    { title: 'Kriteria Penilaian', href: criteriaIndex(), icon: Scale },
    { title: 'Nilai Mahasiswa', href: scoresIndex(), icon: GraduationCap },
    { title: 'Pengguna', href: usersIndex(), icon: UsersRound },
    {
        title: 'Pengaturan Tampilan',
        href: appearanceIndex(),
        icon: Palette,
    },
];

const isAdmin = computed(() => page.props.auth.user?.role === 'admin');

const mainNavItems = computed(() => {
    if (isAdmin.value) return adminItems;
    return page.props.preTestDone ? learningItems : preTestItems;
});

const homeHref = computed(() => {
    if (isAdmin.value) return adminDashboard();
    return page.props.preTestDone ? dashboard() : preTestIndex();
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="homeHref">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
