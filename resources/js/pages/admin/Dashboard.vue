<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    BookCheck,
    ClipboardList,
    GraduationCap,
    Layers,
    Scale,
    TrendingUp,
    Users,
} from '@lucide/vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import ProgressBar from '@/components/ipcara/ProgressBar.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard as adminDashboard } from '@/routes/admin';
import { index as criteriaIndex } from '@/routes/admin/criteria';
import { index as questionsIndex } from '@/routes/admin/questions';
import { index as scoresIndex } from '@/routes/admin/scores';
import { index as topicsIndex } from '@/routes/admin/topics';

type Level = 'rendah' | 'sedang' | 'tinggi';

const props = defineProps<{
    stats: {
        questions: number;
        activeQuestions: number;
        students: number;
        completed: number;
        average: number | null;
        passed: number;
        passingScore: number | null;
    };
    distribution: { level: Level; label: string; count: number }[];
    topicAverages: { title: string; average: number }[];
    recent: {
        id: number;
        name: string | null;
        nim: string | null;
        score: number;
        is_passed: boolean;
        finished_at: string | null;
    }[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard Admin', href: adminDashboard() }],
    },
});

const levelColor: Record<Level, string> = {
    rendah: 'var(--color-destructive)',
    sedang: 'var(--color-warning)',
    tinggi: 'var(--color-success)',
};

const percentOf = (count: number) =>
    props.stats.completed
        ? Math.round((count / props.stats.completed) * 100)
        : 0;
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Dashboard Admin</h1>
            <p class="text-muted-foreground text-sm">
                Kelola bank soal, kriteria penilaian, dan nilai pre-test
                mahasiswa.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <GlowCard>
                <div class="flex items-center gap-3">
                    <div
                        class="bg-accent text-primary flex size-11 items-center justify-center rounded-xl"
                    >
                        <ClipboardList class="size-5" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ stats.questions }}</p>
                        <p class="text-muted-foreground text-xs">
                            Soal di bank ·
                            {{ stats.activeQuestions }} aktif di pre-test
                        </p>
                    </div>
                </div>
            </GlowCard>
            <GlowCard>
                <div class="flex items-center gap-3">
                    <div
                        class="bg-accent text-primary flex size-11 items-center justify-center rounded-xl"
                    >
                        <Users class="size-5" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ stats.students }}</p>
                        <p class="text-muted-foreground text-xs">
                            Mahasiswa terdaftar
                        </p>
                    </div>
                </div>
            </GlowCard>
            <GlowCard>
                <div class="flex items-center gap-3">
                    <div
                        class="bg-accent text-primary flex size-11 items-center justify-center rounded-xl"
                    >
                        <BookCheck class="size-5" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold">
                            {{ stats.completed }}
                        </p>
                        <p class="text-muted-foreground text-xs">
                            Sudah pre-test · {{ stats.passed }} tuntas
                        </p>
                    </div>
                </div>
            </GlowCard>
            <GlowCard>
                <div class="flex items-center gap-3">
                    <div
                        class="bg-accent text-primary flex size-11 items-center justify-center rounded-xl"
                    >
                        <TrendingUp class="size-5" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold">
                            {{ stats.average ?? '-'
                            }}<span v-if="stats.average !== null">%</span>
                        </p>
                        <p class="text-muted-foreground text-xs">
                            Rata-rata nilai · KKM {{ stats.passingScore }}%
                        </p>
                    </div>
                </div>
            </GlowCard>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <Link
                v-for="item in [
                    {
                        href: topicsIndex(),
                        icon: Layers,
                        title: 'Topik',
                        text: 'Kelola topik PPI beserta urutannya.',
                    },
                    {
                        href: questionsIndex(),
                        icon: ClipboardList,
                        title: 'Bank Soal',
                        text: 'Tambah, ubah, dan hapus soal pre-test.',
                    },
                    {
                        href: criteriaIndex(),
                        icon: Scale,
                        title: 'Kriteria Penilaian',
                        text: 'Atur rentang nilai, KKM, dan durasi.',
                    },
                    {
                        href: scoresIndex(),
                        icon: GraduationCap,
                        title: 'Nilai Mahasiswa',
                        text: 'Kelola skor pre-test setiap mahasiswa.',
                    },
                ]"
                :key="item.title"
                :href="item.href"
                class="bg-card glow-card group hover:border-primary/60 flex items-center gap-4 rounded-2xl border p-5 transition"
            >
                <div
                    class="bg-primary text-primary-foreground flex size-11 shrink-0 items-center justify-center rounded-xl"
                >
                    <component :is="item.icon" class="size-5" />
                </div>
                <div class="flex-1">
                    <p class="group-hover:text-primary font-semibold">
                        {{ item.title }}
                    </p>
                    <p class="text-muted-foreground text-xs">
                        {{ item.text }}
                    </p>
                </div>
                <ArrowRight
                    class="text-muted-foreground group-hover:text-primary size-4 transition group-hover:translate-x-0.5"
                />
            </Link>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <GlowCard
                title="Sebaran Kemampuan"
                subtitle="Berdasarkan nilai total pre-test"
            >
                <ul class="flex flex-col gap-4">
                    <li v-for="d in distribution" :key="d.level">
                        <div
                            class="mb-1.5 flex items-center justify-between text-sm"
                        >
                            <span class="font-medium">{{ d.label }}</span>
                            <span class="text-muted-foreground tabular-nums"
                                >{{ d.count }} mahasiswa</span
                            >
                        </div>
                        <ProgressBar
                            :value="percentOf(d.count)"
                            :show-value="false"
                            :color="levelColor[d.level]"
                        />
                    </li>
                </ul>
            </GlowCard>

            <GlowCard
                title="Rata-rata per Topik"
                subtitle="Topik dengan nilai terendah perlu perhatian"
            >
                <ul class="flex flex-col gap-3">
                    <li v-for="t in topicAverages" :key="t.title">
                        <ProgressBar :value="t.average" :label="t.title" />
                    </li>
                </ul>
            </GlowCard>

            <GlowCard title="Pre-Test Terbaru">
                <template #action>
                    <Button size="sm" variant="ghost" as-child>
                        <Link :href="scoresIndex()">Semua</Link>
                    </Button>
                </template>
                <ul v-if="recent.length" class="flex flex-col gap-2">
                    <li
                        v-for="r in recent"
                        :key="r.id"
                        class="bg-secondary/50 flex items-center justify-between gap-2 rounded-xl px-3 py-2.5"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium">
                                {{ r.name }}
                            </p>
                            <p class="text-muted-foreground text-xs">
                                {{ r.nim ?? '-' }} · {{ r.finished_at }}
                            </p>
                        </div>
                        <Badge
                            :variant="r.is_passed ? 'default' : 'outline'"
                            class="tabular-nums"
                            >{{ Math.round(r.score) }}%</Badge
                        >
                    </li>
                </ul>
                <p v-else class="text-muted-foreground text-sm">
                    Belum ada mahasiswa yang mengerjakan pre-test.
                </p>
            </GlowCard>
        </div>
    </div>
</template>
