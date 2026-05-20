<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PostController from '@/actions/App/Http/Controllers/PostController';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{ posts: any }>();
</script>

<template>

    <Head title="Posts" />
    <AppLayout>
        <div v-for="post in posts.data" :key="post.id" class="w-full h-25 border-y flex flex-col justify-center">
            <Link :href="PostController.show(post.slug)">
                <p class="text-2xl">{{ post.title }}</p>
            </Link>
            <p class="text-lg">{{ post.excerpt }}</p>
        </div>
    </AppLayout>
</template>
