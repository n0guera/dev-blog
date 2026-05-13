<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import MarkdownRenderer from '@/components/MarkdownRenderer.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{ post: any }>();
</script>

<template>

    <Head :title="post.title" />
    <AppLayout>
        <h1 class="mb-6 text-3xl font-bold">
            {{ post.data.title }}
        </h1>
        <MarkdownRenderer :content="post.data.content" />
    </AppLayout>
</template>
