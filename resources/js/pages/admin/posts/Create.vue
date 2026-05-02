<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import MarkdownEditor from '@/components/MarkdownEditor.vue';
import AppLayout from '@/layouts/AppLayout.vue';
defineProps<{ statuses: any }>();
</script>

<template>

    <Head title="Create Post" />
    <AppLayout>
        <h1 class="mb-2">Create Post</h1>
        <MarkdownEditor />
    </AppLayout>
</template>
