<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PostController from '@/actions/App/Http/Controllers/PostController';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{ posts: any }>();
</script>

<template>

    <Head title="Posts" />
    <AppLayout>
        <Link v-for="post in posts.data" :key="post.id" :href="PostController.show(post.slug)">
            {{ post.title }}
            <p>{{ post.excerpt }}</p>
        </Link>
    </AppLayout>
</template>
