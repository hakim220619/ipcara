<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    BookOpen,
    Check,
    ClipboardCheck,
    FileText,
    Hand,
    Lock,
    PlayCircle,
    ShieldCheck,
    Shirt,
    Sparkles,
    Stethoscope,
    Trash2,
    Trophy,
    Video,
    Wind,
} from '@lucide/vue';
import { computed } from 'vue';
import { index as materiIndex, show as materiShow } from '@/routes/materi';
import {
    index as postTestIndex,
    result as postTestResult,
} from '@/routes/post-test';

type MaterialItem = {
    id: number;
    title: string;
    slug: string;
    type: 'teks' | 'video' | 'kasus';
    status: 'belum' | 'sedang' | 'selesai';
    has_practice: boolean;
    mastery: 'belum' | 'penguatan' | 'remedial' | 'mastery';
    recommended: boolean;
};

type TopicItem = {
    id: number;
    code: string;
    title: string;
    description: string | null;
    recommended: boolean;
    category: 'rendah' | 'sedang' | 'tinggi' | null;
    materials: MaterialItem[];
};

const props = defineProps<{
    topics: TopicItem[];
    postTest: {
        completed: number;
        total: number;
        unlocked: boolean;
        done: boolean;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Materi Belajar', href: materiIndex() }],
    },
});

const page = usePage();
const firstName = computed(
    () => page.props.auth.user?.name?.split(' ')[0] ?? 'Mahasiswa',
);

const overallPercent = computed(() =>
    props.postTest.total
        ? Math.round((props.postTest.completed / props.postTest.total) * 100)
        : 0,
);

// Materi berikutnya: utamakan rekomendasi yang belum selesai, lalu urutan topik.
const nextMaterial = computed(() => {
    const all = props.topics.flatMap((t) => t.materials);
    const pending = all.filter((m) => !isDone(m));

    return (
        pending.find((m) => m.status !== 'belum') ??
        pending.find((m) => m.recommended) ??
        props.topics
            .filter((t) => t.recommended)
            .flatMap((t) => t.materials)
            .find((m) => !isDone(m)) ??
        pending[0] ??
        null
    );
});

// Materi tuntas bila sudah dibaca dan (bila ada latihan) mencapai mastery.
function isDone(m: MaterialItem) {
    return (
        m.status === 'selesai' && (!m.has_practice || m.mastery === 'mastery')
    );
}

function isActive(m: MaterialItem) {
    return !isDone(m) && m.status !== 'belum';
}

const practiceNote: Record<MaterialItem['mastery'], string> = {
    belum: 'lanjutkan ke latihan',
    penguatan: 'perlu penguatan',
    remedial: 'perlu remedial',
    mastery: 'mastery',
};

function topicDone(topic: TopicItem) {
    return topic.materials.filter(isDone).length;
}

const topicIcon: Record<string, unknown> = {
    'PPI-01': ShieldCheck,
    'PPI-02': Hand,
    'PPI-03': Shirt,
    'PPI-04': Wind,
    'PPI-05': Trash2,
};
const palette = [
    'var(--color-chart-1)',
    'var(--color-chart-2)',
    'var(--color-chart-3)',
    'var(--color-chart-4)',
    'var(--color-chart-5)',
];

// Kategori hasil pre-test (adaptive learning).
const categoryMeta = {
    rendah: {
        label: 'Perlu dipelajari',
        class: 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
    },
    sedang: {
        label: 'Perlu penguatan',
        class: 'bg-amber-500/15 text-amber-600 dark:text-amber-400',
    },
    tinggi: {
        label: 'Sudah dikuasai',
        class: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    },
};

const typeIcon = { teks: FileText, video: Video, kasus: Stethoscope };
const typeLabel = { teks: 'Bacaan', video: 'Video', kasus: 'Studi kasus' };
</script>

<template>
    <Head title="Materi Belajar" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Sapaan + progres keseluruhan -->
        <section
            class="from-primary relative overflow-hidden rounded-3xl bg-gradient-to-br to-[hsl(212_90%_45%)] p-6 text-white md:p-8"
        >
            <div
                class="absolute -top-20 -right-16 size-64 rounded-full bg-white/10 blur-2xl"
                aria-hidden="true"
            ></div>
            <div
                class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between"
            >
                <div class="flex flex-col gap-3">
                    <p class="text-sm text-white/80">Halo, {{ firstName }}</p>
                    <h1 class="text-2xl font-bold tracking-tight md:text-3xl">
                        <template v-if="postTest.done"
                            >Hebat, perjalanan belajarmu tuntas!</template
                        >
                        <template v-else-if="postTest.unlocked"
                            >Semua materi selesai. Saatnya post-test!</template
                        >
                        <template v-else-if="postTest.completed === 0"
                            >Yuk, mulai belajar hari ini!</template
                        >
                        <template v-else>Ayo lanjutkan belajarmu!</template>
                    </h1>

                    <div class="flex flex-wrap gap-3">
                        <Link
                            v-if="postTest.done"
                            :href="postTestResult()"
                            class="text-primary inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold shadow-lg transition hover:-translate-y-0.5"
                        >
                            <Trophy class="size-4" /> Lihat hasil post-test
                        </Link>
                        <Link
                            v-else-if="postTest.unlocked"
                            :href="postTestIndex()"
                            class="text-primary inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold shadow-lg transition hover:-translate-y-0.5"
                        >
                            <ClipboardCheck class="size-4" /> Kerjakan post-test
                        </Link>
                        <Link
                            v-else-if="nextMaterial"
                            :href="materiShow(nextMaterial.slug)"
                            class="text-primary inline-flex max-w-full items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold shadow-lg transition hover:-translate-y-0.5"
                        >
                            <PlayCircle class="size-4 shrink-0" />
                            <span class="truncate"
                                >{{
                                    postTest.completed ? 'Lanjutkan' : 'Mulai'
                                }}: {{ nextMaterial.title }}</span
                            >
                        </Link>
                    </div>
                </div>

                <div
                    class="flex shrink-0 items-center gap-4 rounded-2xl bg-white/10 p-4 backdrop-blur"
                >
                    <div class="relative size-20">
                        <svg viewBox="0 0 36 36" class="size-20 -rotate-90">
                            <circle
                                cx="18"
                                cy="18"
                                r="15.5"
                                fill="none"
                                stroke="rgb(255 255 255 / 0.2)"
                                stroke-width="3"
                            />
                            <circle
                                cx="18"
                                cy="18"
                                r="15.5"
                                fill="none"
                                stroke="white"
                                stroke-width="3"
                                stroke-linecap="round"
                                :stroke-dasharray="`${overallPercent * 0.974} 100`"
                                class="transition-[stroke-dasharray] duration-700"
                            />
                        </svg>
                        <span
                            class="absolute inset-0 flex items-center justify-center text-lg font-bold tabular-nums"
                            >{{ overallPercent }}%</span
                        >
                    </div>
                    <div class="text-sm">
                        <p class="font-semibold">
                            {{ postTest.completed }} / {{ postTest.total }}
                        </p>
                        <p class="text-white/75">materi tuntas</p>
                        <p
                            v-if="!postTest.unlocked"
                            class="mt-1 inline-flex items-center gap-1 text-xs text-white/75"
                        >
                            <Lock class="size-3" /> Post-test terbuka di 100%
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kartu topik -->
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <article
                v-for="(topic, ti) in topics"
                :key="topic.id"
                class="bg-card glow-card flex flex-col gap-4 rounded-2xl border p-5 transition hover:-translate-y-0.5 hover:shadow-lg"
                :class="
                    topic.category === 'rendah'
                        ? 'border-rose-500/50'
                        : topic.category === 'sedang'
                          ? 'border-amber-500/50'
                          : ''
                "
            >
                <div class="flex items-start gap-3">
                    <div
                        class="flex size-12 shrink-0 items-center justify-center rounded-2xl"
                        :style="{
                            backgroundColor: `color-mix(in oklab, ${palette[ti % palette.length]} 16%, transparent)`,
                            color: palette[ti % palette.length],
                        }"
                    >
                        <component
                            :is="topicIcon[topic.code] ?? BookOpen"
                            class="size-6"
                        />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="leading-tight font-semibold">
                                {{ topic.title }}
                            </h2>
                            <span
                                v-if="
                                    topicDone(topic) ===
                                        topic.materials.length &&
                                    topic.materials.length
                                "
                                class="text-success inline-flex shrink-0 items-center gap-1 text-xs font-medium"
                                ><Check class="size-3.5" /> Tuntas</span
                            >
                            <span
                                v-else-if="topic.category"
                                class="inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium"
                                :class="categoryMeta[topic.category].class"
                                ><Sparkles
                                    v-if="topic.category !== 'tinggi'"
                                    class="size-3"
                                />{{ categoryMeta[topic.category].label }}</span
                            >
                        </div>
                        <p
                            v-if="topic.description"
                            class="text-muted-foreground mt-1 line-clamp-2 text-xs"
                        >
                            {{ topic.description }}
                        </p>
                    </div>
                </div>

                <!-- Langkah materi -->
                <ol class="flex flex-col">
                    <li
                        v-for="(m, mi) in topic.materials"
                        :key="m.id"
                        class="relative"
                    >
                        <span
                            v-if="mi < topic.materials.length - 1"
                            class="absolute top-9 left-[15px] h-[calc(100%-1.5rem)] w-0.5"
                            :class="isDone(m) ? 'bg-success/60' : 'bg-border'"
                            aria-hidden="true"
                        ></span>
                        <Link
                            :href="materiShow(m.slug)"
                            class="group hover:bg-secondary/60 flex items-center gap-3 rounded-xl py-2 pr-2 transition"
                        >
                            <span
                                class="relative z-10 flex size-8 shrink-0 items-center justify-center rounded-full border-2 text-xs font-semibold transition"
                                :class="
                                    isDone(m)
                                        ? 'bg-success border-success text-white'
                                        : isActive(m)
                                          ? 'border-primary text-primary bg-card'
                                          : 'bg-card text-muted-foreground group-hover:border-primary/60'
                                "
                            >
                                <Check v-if="isDone(m)" class="size-4" />
                                <template v-else>{{ mi + 1 }}</template>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span
                                    class="group-hover:text-primary block truncate text-sm font-medium"
                                    >{{ m.title }}</span
                                >
                                <span
                                    class="text-muted-foreground inline-flex items-center gap-1 text-xs"
                                >
                                    <component
                                        :is="typeIcon[m.type]"
                                        class="size-3"
                                    />
                                    {{ typeLabel[m.type] }}
                                    <template v-if="isDone(m) && m.has_practice"
                                        >·
                                        <span class="text-success"
                                            >mastery</span
                                        ></template
                                    >
                                    <template v-else-if="m.status === 'selesai'"
                                        >·
                                        <span
                                            :class="
                                                m.mastery === 'remedial'
                                                    ? 'text-destructive'
                                                    : 'text-warning'
                                            "
                                            >{{ practiceNote[m.mastery] }}</span
                                        ></template
                                    >
                                    <template v-else-if="m.status === 'sedang'"
                                        >·
                                        <span class="text-primary"
                                            >sedang dipelajari</span
                                        ></template
                                    >
                                </span>
                            </span>
                            <ArrowRight
                                class="text-muted-foreground group-hover:text-primary size-4 shrink-0 opacity-0 transition group-hover:opacity-100"
                            />
                        </Link>
                    </li>
                </ol>

                <div class="mt-auto flex flex-col gap-1.5">
                    <div
                        class="text-muted-foreground flex justify-between text-xs"
                    >
                        <span>Progres topik</span>
                        <span class="tabular-nums"
                            >{{ topicDone(topic) }}/{{
                                topic.materials.length
                            }}</span
                        >
                    </div>
                    <div
                        class="bg-secondary h-1.5 overflow-hidden rounded-full"
                    >
                        <div
                            class="h-full rounded-full transition-all duration-700"
                            :style="{
                                width: `${topic.materials.length ? (topicDone(topic) / topic.materials.length) * 100 : 0}%`,
                                backgroundColor: palette[ti % palette.length],
                            }"
                        ></div>
                    </div>
                </div>
            </article>
        </section>

        <div
            v-if="!topics.length"
            class="bg-card text-muted-foreground rounded-2xl border p-8 text-center text-sm"
        >
            Belum ada materi. Silakan cek kembali nanti.
        </div>
    </div>
</template>
