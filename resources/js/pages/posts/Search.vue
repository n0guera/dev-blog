<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PostList from '@/components/PostList.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    posts: any;
    query: string;
}>();

const searchQuery = ref(props.query);

function search() {
    router.get('/search', { q: searchQuery.value }, { preserveState: true });
}
</script>

<template>
    <Head title="Search" />
    <AppLayout>
        <h1 class="mb-6 text-2xl font-semibold text-foreground">
            Search Posts
        </h1>

        <form @submit.prevent="search" class="mb-8 flex gap-2">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search by title or content..."
                class="flex-1 rounded-md border border-border bg-background px-4 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:ring-1 focus:ring-ring focus:outline-none"
            />
            <button
                type="submit"
                class="rounded-md bg-primary px-5 py-2 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90"
            >
                Search
            </button>
        </form>

        <PostList v-if="query" :posts="posts" />

        <p v-else class="py-12 text-center text-sm text-muted-foreground">
            Enter a search term to find posts.
        </p>
    </AppLayout>
</template>
