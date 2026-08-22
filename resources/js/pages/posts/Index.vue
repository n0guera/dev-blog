<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PostList from '@/components/PostList.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{ posts: any; tag?: any }>();
</script>

<template>

    <Head title="Posts" />
    <AppLayout>
        <h1 v-if="tag" class="mb-4 text-2xl font-semibold text-foreground">
            Posts tagged: <span class="text-primary">{{ tag.name }}</span>
        </h1>
        <h1 v-else class="mb-4 text-2xl font-semibold text-foreground">All Posts</h1>
        <PostList :posts="posts" />
    </AppLayout>
</template>
