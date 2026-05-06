<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{ post: any }>();
</script>

<template>

    <Head title="Post" />
    <AppLayout>
        <h1>{{ post.data.title }}</h1>
        <p>{{ post.data.content }}</p>
    </AppLayout>
</template>
