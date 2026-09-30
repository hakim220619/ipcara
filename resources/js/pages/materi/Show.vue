<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Award,
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    PenLine,
    PlayCircle,
    RotateCcw,
} from '@lucide/vue';
import { computed, nextTick, onMounted, ref } from 'vue';
import GlowCard from '@/components/ipcara/GlowCard.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { index as postTestIndex } from '@/routes/post-test';
import {
    complete as materiComplete,
    index as materiIndex,
    show as materiShow,
} from '@/routes/materi';
import {
    result as practiceResult,
    start as practiceStart,
    take as practiceTake,
} from '@/routes/materi/latihan';

const props = defineProps<{
    material: {
        id: number;
        title: string;
        slug: string;
        type: 'teks' | 'video' | 'kasus';
        summary: string | null;
        content: string | null;
        video_url: string | null;
        topic: { id: number; title: string; code: string };
    };
    progress: {
        status: string;
        progress_percent: number;
        last_position_seconds: number | null;
    };
    next: { title: string; slug: string } | null;
    prev: { title: string; slug: string } | null;
    postTestUnlocked: boolean;
    practice: {
        mastery: 'belum' | 'penguatan' | 'remedial' | 'mastery';
        attempts: number;
        last_score: number | null;
        in_progress: boolean;
        next_round: 'utama' | 'penguatan' | 'remedial';
        last_attempt_id: number | null;
    } | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard() },
            { title: 'Materi Belajar', href: materiIndex() },
        ],
    },
});

const embedUrl = computed(() => {
    const url = props.material.video_url;
    if (!url) return null;
    const yt = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/);
    return yt ? `https://www.youtube.com/embed/${yt[1]}` : url;
});

const markComplete = () => {
    router.post(
        materiComplete(props.material.slug).url,
        {},
        { preserveScroll: true },
    );
};

const startingPractice = ref(false);
const startPractice = () => {
    router.post(
        practiceStart.url(props.material.slug),
        {},
        {
            onStart: () => (startingPractice.value = true),
            onFinish: () => (startingPractice.value = false),
        },
    );
};

const roundLabel = {
    utama: 'Mulai 3 Soal Latihan',
    penguatan: 'Kerjakan 2 Soal Penguatan',
    remedial: 'Kerjakan 3 Soal Remedial',
};
const masteryMeta = {
    belum: {
        label: 'Belum dikerjakan',
        class: 'bg-secondary text-muted-foreground',
    },
    penguatan: {
        label: 'Perlu penguatan',
        class: 'bg-warning/15 text-warning',
    },
    remedial: {
        label: 'Perlu remedial',
        class: 'bg-destructive/10 text-destructive',
    },
    mastery: { label: 'Mastery', class: 'bg-success/15 text-success' },
};

// Beri id pada judul bagian agar tautan "bagian yang belum dikuasai" dapat menuju ke sana.
const article = ref<HTMLElement | null>(null);
const slugify = (text: string) =>
    text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

onMounted(async () => {
    await nextTick();
    const headings = article.value?.querySelectorAll('h2, h3') ?? [];
    headings.forEach((h) => (h.id = slugify(h.textContent ?? '')));

    const target = new URLSearchParams(window.location.search).get('bagian');
    const el = target ? document.getElementById(target) : null;
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        el.classList.add(
            'bg-warning/15',
            'rounded-lg',
            'px-2',
            '-mx-2',
            'py-1',
            'ring-2',
            'ring-warning/50',
        );
    }
});

const typeLabel = {
    teks: 'Materi Teks',
    video: 'Video Pembelajaran',
    kasus: 'Studi Kasus',
};
</script>

<template>
    <Head :title="material.title" />

    <div class="flex flex-1 flex-col p-4 md:p-6">
        <GlowCard class="mx-auto w-full max-w-3xl">
            <div>
                <p class="text-muted-foreground text-xs">
                    {{ material.topic.title }} · {{ typeLabel[material.type] }}
                </p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight">
                    {{ material.title }}
                </h1>
                <p v-if="material.summary" class="text-muted-foreground mt-1">
                    {{ material.summary }}
                </p>
            </div>

            <div v-if="embedUrl" class="flex flex-col gap-2">
                <p
                    class="text-primary flex items-center gap-2 text-sm font-semibold"
                >
                    <PlayCircle class="size-4" /> Video Demonstrasi
                </p>
                <div
                    class="aspect-video w-full overflow-hidden rounded-xl border"
                >
                    <iframe
                        :src="embedUrl"
                        :title="`Video: ${material.title}`"
                        class="h-full w-full"
                        allow="
                            accelerometer;
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            gyroscope;
                            picture-in-picture;
                        "
                        allowfullscreen
                    />
                </div>
            </div>

            <!-- Konten dibuat oleh dosen melalui editor; diasumsikan HTML tepercaya -->
            <article
                v-if="material.content"
                ref="article"
                class="[&_h2]:text-primary [&_a]:text-primary text-sm leading-relaxed [&_a]:underline [&_h2]:mt-5 [&_h2]:text-lg [&_h2]:font-semibold [&_h3]:mt-4 [&_h3]:font-semibold [&_img]:my-3 [&_img]:rounded-xl [&_ol]:mt-2 [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mt-2 [&_ul]:mt-2 [&_ul]:list-disc [&_ul]:pl-5"
                v-html="material.content"
            />

            <!-- Latihan materi -->
            <div
                v-if="practice && progress.status === 'selesai'"
                class="flex flex-col gap-3 rounded-2xl border p-4 sm:flex-row sm:items-center sm:justify-between"
                :class="
                    practice.mastery === 'mastery'
                        ? 'border-success/40 bg-success/5'
                        : 'border-primary/30 bg-accent/40'
                "
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl"
                        :class="
                            practice.mastery === 'mastery'
                                ? 'bg-success text-white'
                                : 'bg-primary text-primary-foreground'
                        "
                    >
                        <Award
                            v-if="practice.mastery === 'mastery'"
                            class="size-5"
                        />
                        <PenLine v-else class="size-5" />
                    </div>
                    <div>
                        <p class="flex items-center gap-2 font-semibold">
                            Latihan Materi
                            <span
                                class="rounded-full px-2 py-0.5 text-[11px] font-medium"
                                :class="masteryMeta[practice.mastery].class"
                                >{{ masteryMeta[practice.mastery].label }}</span
                            >
                        </p>
                        <p class="text-muted-foreground text-xs">
                            <template v-if="practice.attempts"
                                >{{ practice.attempts }}× dikerjakan · skor
                                terakhir
                                {{
                                    Math.round(practice.last_score ?? 0)
                                }}</template
                            >
                            <template v-else
                                >3 soal untuk memastikan Anda menguasai materi
                                ini.</template
                            >
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Button
                        v-if="practice.last_attempt_id"
                        variant="ghost"
                        size="sm"
                        as-child
                    >
                        <Link
                            :href="
                                practiceResult([
                                    material.slug,
                                    practice.last_attempt_id,
                                ])
                            "
                            >Hasil terakhir</Link
                        >
                    </Button>
                    <Button v-if="practice.in_progress" size="sm" as-child>
                        <Link :href="practiceTake(material.slug)"
                            >Lanjutkan latihan <ArrowRight
                        /></Link>
                    </Button>
                    <Button
                        v-else-if="practice.mastery === 'mastery'"
                        size="sm"
                        variant="outline"
                        :disabled="startingPractice"
                        @click="startPractice"
                    >
                        <RotateCcw /> Latihan lagi
                    </Button>
                    <Button
                        v-else
                        size="sm"
                        :disabled="startingPractice"
                        @click="startPractice"
                    >
                        <PenLine /> {{ roundLabel[practice.next_round] }}
                    </Button>
                </div>
            </div>

            <div
                class="flex flex-wrap items-center justify-between gap-3 border-t pt-4"
            >
                <Button v-if="prev" variant="ghost" as-child>
                    <Link :href="materiShow(prev.slug)"
                        ><ArrowLeft /> Sebelumnya</Link
                    >
                </Button>
                <span v-else />
                <div class="flex items-center gap-2">
                    <Button
                        v-if="progress.status !== 'selesai'"
                        @click="markComplete"
                    >
                        <CheckCircle2 />
                        {{
                            practice
                                ? 'Selesai & Mulai Latihan'
                                : 'Tandai Selesai'
                        }}
                    </Button>
                    <Badge v-else class="bg-success text-primary-foreground"
                        ><CheckCircle2 class="size-3" /> Selesai</Badge
                    >
                    <Button v-if="next" variant="outline" as-child>
                        <Link :href="materiShow(next.slug)"
                            >Berikutnya <ArrowRight
                        /></Link>
                    </Button>
                    <Button v-if="!next && postTestUnlocked" as-child>
                        <Link :href="postTestIndex()"
                            >Kerjakan Post-Test <ArrowRight
                        /></Link>
                    </Button>
                </div>
            </div>
        </GlowCard>
    </div>
</template>
