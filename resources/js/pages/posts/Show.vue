<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import MarkdownRenderer from '@/components/MarkdownRenderer.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{ post: any }>();
</script>

<template>

    <Head :title="post.data.title" />
    <AppLayout>
        <div class="prose prose-neutral dark:prose-invert">
            <h1>
                {{ post.data.title }}
            </h1>
            <MarkdownRenderer :content="post.data.content" />
        </div>
    </AppLayout>
</template>
