<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import TestRunner, {
    type TestQuestion,
} from '@/components/ipcara/TestRunner.vue';
import {
    index as postTestIndex,
    submit as postTestSubmit,
    take as postTestTake,
} from '@/routes/post-test';

defineProps<{
    attemptId: number;
    title: string;
    deadline: string | null;
    questions: TestQuestion[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Post-Test', href: postTestIndex() },
            { title: 'Pengerjaan', href: postTestTake() },
        ],
    },
});
</script>

<template>
    <Head title="Mengerjakan Post-Test" />

    <TestRunner
        :attempt-id="attemptId"
        :title="title"
        :deadline="deadline"
        :questions="questions"
        label="post-test"
        :submit-url="postTestSubmit.url()"
    />
</template>
