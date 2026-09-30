<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CircleHelp,
    ClipboardCheck,
    Clock,
    Layers,
    ListChecks,
    Lock,
    PlayCircle,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import { Button } from '@/components/ui/button';
import { index as materiIndex } from '@/routes/materi';
import {
    index as postTestIndex,
    start as postTestStart,
    take as postTestTake,
} from '@/routes/post-test';

const props = defineProps<{
    test: {
        title: string;
        description: string | null;
        duration_minutes: number | null;
        question_count: number;
        topics: { title: string; total: number }[];
    };
    materials: { completed: number; total: number };
    unlocked: boolean;
    inProgress: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Post-Test', href: postTestIndex() }],
    },
});

const starting = ref(false);
const materialPercent = computed(() =>
    props.materials.total
        ? Math.round((props.materials.completed / props.materials.total) * 100)
        : 0,
);

function start() {
    router.post(
        postTestStart.url(),
        {},
        {
            onStart: () => (starting.value = true),
            onFinish: () => (starting.value = false),
        },
    );
}

const rules = [
    'Kerjakan secara mandiri tanpa membuka materi atau sumber lain.',
    'Setiap soal memiliki satu jawaban yang paling tepat.',
    'Anda dapat berpindah soal dan mengubah jawaban sebelum mengirim.',
    'Jawaban otomatis dikirim ketika waktu habis.',
    'Post-test hanya dapat dikerjakan satu kali.',
];
</script>

<template>
    <Head title="Post-Test" />

    <div class="bg-grid-glow flex flex-1 flex-col gap-5 p-4 md:p-6">
        <section
            class="from-primary relative overflow-hidden rounded-3xl bg-gradient-to-br to-[hsl(212_90%_45%)] p-6 text-white shadow-[0_24px_60px_-28px_rgba(15,64,107,0.6)] md:p-8"
        >
            <div
                class="absolute -top-16 -right-16 size-56 rounded-full bg-white/10 blur-2xl"
                aria-hidden="true"
            ></div>
            <div class="relative flex flex-col gap-4 md:max-w-2xl">
                <span
                    class="inline-flex w-fit items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-medium"
                >
                    <ClipboardCheck class="size-3.5" /> Langkah 3 dari 3
                </span>
                <h1 class="text-3xl font-bold tracking-tight md:text-4xl">
                    {{ test.title }}
                </h1>
                <p class="text-white/85">{{ test.description }}</p>

                <div
                    v-if="!unlocked"
                    class="flex flex-col gap-2 rounded-2xl bg-white/10 p-4"
                >
                    <p class="flex items-center gap-2 text-sm font-semibold">
                        <Lock class="size-4" /> Post-test terbuka setelah semua
                        materi mencapai Mastery
                    </p>
                    <div class="h-2 overflow-hidden rounded-full bg-white/20">
                        <div
                            class="h-full rounded-full bg-white transition-all duration-700"
                            :style="{ width: `${materialPercent}%` }"
                        ></div>
                    </div>
                    <p class="text-xs text-white/80">
                        {{ materials.completed }} dari {{ materials.total }}
                        materi tuntas
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Button
                        v-if="!unlocked"
                        size="lg"
                        variant="secondary"
                        as-child
                    >
                        <Link :href="materiIndex()"> Lanjut belajar </Link>
                    </Button>
                    <Button
                        v-else-if="inProgress"
                        size="lg"
                        variant="secondary"
                        as-child
                    >
                        <Link :href="postTestTake()">
                            <PlayCircle /> Lanjutkan Post-Test
                        </Link>
                    </Button>
                    <Button
                        v-else
                        size="lg"
                        variant="secondary"
                        :disabled="starting"
                        @click="start"
                    >
                        <PlayCircle /> Mulai Post-Test
                    </Button>
                </div>
            </div>
        </section>

        <div class="grid gap-4 sm:grid-cols-3">
            <GlowCard>
                <div class="flex items-center gap-3">
                    <div
                        class="bg-accent text-primary flex size-11 items-center justify-center rounded-xl"
                    >
                        <ListChecks class="size-5" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold">
                            {{ test.question_count }}
                        </p>
                        <p class="text-muted-foreground text-xs">
                            Soal pilihan ganda
                        </p>
                    </div>
                </div>
            </GlowCard>
            <GlowCard>
                <div class="flex items-center gap-3">
                    <div
                        class="bg-accent text-primary flex size-11 items-center justify-center rounded-xl"
                    >
                        <Clock class="size-5" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold">
                            {{ test.duration_minutes ?? '-' }}
                        </p>
                        <p class="text-muted-foreground text-xs">Menit</p>
                    </div>
                </div>
            </GlowCard>
            <GlowCard>
                <div class="flex items-center gap-3">
                    <div
                        class="bg-accent text-primary flex size-11 items-center justify-center rounded-xl"
                    >
                        <Layers class="size-5" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold">
                            {{ test.topics.length }}
                        </p>
                        <p class="text-muted-foreground text-xs">Topik PPI</p>
                    </div>
                </div>
            </GlowCard>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <GlowCard
                title="Cakupan Materi"
                subtitle="Jumlah soal pada setiap topik"
            >
                <ul class="flex flex-col gap-2">
                    <li
                        v-for="(topic, i) in test.topics"
                        :key="topic.title"
                        class="bg-secondary/50 flex items-center justify-between rounded-xl px-4 py-3"
                    >
                        <span class="flex items-center gap-3 font-medium">
                            <span
                                class="bg-primary text-primary-foreground flex size-7 items-center justify-center rounded-full text-xs"
                                >{{ i + 1 }}</span
                            >
                            {{ topic.title }}
                        </span>
                        <span class="text-muted-foreground text-sm"
                            >{{ topic.total }} soal</span
                        >
                    </li>
                </ul>
            </GlowCard>

            <GlowCard title="Petunjuk Pengerjaan">
                <ul class="flex flex-col gap-3">
                    <li
                        v-for="rule in rules"
                        :key="rule"
                        class="flex items-start gap-3 text-sm"
                    >
                        <CircleHelp
                            class="text-primary mt-0.5 size-4 shrink-0"
                        />
                        {{ rule }}
                    </li>
                </ul>
                <p
                    class="bg-accent text-accent-foreground rounded-xl p-3 text-xs"
                >
                    Setelah selesai, Anda akan melihat skor dan persentase per
                    topik beserta perbandingannya dengan pre-test. Nilai latihan
                    materi tidak memengaruhi nilai post-test.
                </p>
            </GlowCard>
        </div>
    </div>
</template>
