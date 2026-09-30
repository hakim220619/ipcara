<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CircleHelp,
    ClipboardCheck,
    Clock,
    Layers,
    ListChecks,
    PlayCircle,
} from '@lucide/vue';
import { ref } from 'vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import { Button } from '@/components/ui/button';
import {
    index as preTestIndex,
    start as preTestStart,
    take as preTestTake,
} from '@/routes/pre-test';

defineProps<{
    test: {
        title: string;
        description: string | null;
        duration_minutes: number | null;
        question_count: number;
        topics: { title: string; total: number }[];
    };
    inProgress: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Pre-Test', href: preTestIndex() }],
    },
});

const starting = ref(false);

function start() {
    router.post(
        preTestStart.url(),
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
    'Pre-test hanya dapat dikerjakan satu kali.',
];
</script>

<template>
    <Head title="Pre-Test" />

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
                    <ClipboardCheck class="size-3.5" /> Langkah 1 dari 3
                </span>
                <h1 class="text-3xl font-bold tracking-tight md:text-4xl">
                    {{ test.title }}
                </h1>
                <p class="text-white/85">{{ test.description }}</p>
                <div class="flex flex-wrap gap-3">
                    <Button
                        v-if="inProgress"
                        size="lg"
                        variant="secondary"
                        as-child
                    >
                        <Link :href="preTestTake()">
                            <PlayCircle /> Lanjutkan Pre-Test
                        </Link>
                    </Button>
                    <Button
                        v-else
                        size="lg"
                        variant="secondary"
                        :disabled="starting"
                        @click="start"
                    >
                        <PlayCircle /> Mulai Pre-Test
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
                    topik, lalu sistem merekomendasikan materi yang perlu
                    dipelajari lebih dulu.
                </p>
            </GlowCard>
        </div>
    </div>
</template>
