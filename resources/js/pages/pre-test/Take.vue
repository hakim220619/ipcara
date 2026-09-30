<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import TestRunner, {
    type TestQuestion,
} from '@/components/ipcara/TestRunner.vue';
import {
    index as preTestIndex,
    submit as preTestSubmit,
    take as preTestTake,
} from '@/routes/pre-test';

defineProps<{
    attemptId: number;
    title: string;
    deadline: string | null;
    questions: TestQuestion[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Pre-Test', href: preTestIndex() },
            { title: 'Pengerjaan', href: preTestTake() },
        ],
    },
});
</script>

<template>
    <Head title="Mengerjakan Pre-Test" />

    <TestRunner
        :attempt-id="attemptId"
        :title="title"
        :deadline="deadline"
        :questions="questions"
        label="pre-test"
        :submit-url="preTestSubmit.url()"
    />
</template>
