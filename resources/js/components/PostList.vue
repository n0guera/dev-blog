<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PostCard from './PostCard.vue';

defineProps<{
    posts: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            excerpt: string | null;
            published_at: string | null;
            user: { id: number; name: string };
            tags: Array<{ id: number; name: string; slug: string }>;
            vote_score: number;
            comments_count: number;
        }>;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
    showTag?: string;
}>();
</script>

<template>
    <div>
        <h2 v-if="showTag" class="mb-4 text-xl font-semibold text-foreground">
            Posts tagged: <span class="text-primary">{{ showTag }}</span>
        </h2>

        <div v-if="posts.data.length" class="grid gap-4 sm:grid-cols-2">
            <PostCard v-for="post in posts.data" :key="post.id" :post="post" />
        </div>

        <p v-else class="py-12 text-center text-sm text-muted-foreground">
            No posts found.
        </p>

        <div
            v-if="posts.total > 0"
            class="flex items-center justify-between pt-4 text-sm text-muted-foreground"
        >
            <span>
                Showing {{ posts.from }}–{{ posts.to }} of {{ posts.total }}
            </span>
            <div class="flex gap-2">
                <Link
                    v-if="posts.prev_page_url"
                    :href="posts.prev_page_url"
                    class="rounded-md bg-secondary px-3 py-1.5 text-secondary-foreground transition-opacity hover:opacity-80"
                >
                    Prev
                </Link>
                <Link
                    v-if="posts.next_page_url"
                    :href="posts.next_page_url"
                    class="rounded-md bg-secondary px-3 py-1.5 text-secondary-foreground transition-opacity hover:opacity-80"
                >
                    Next
                </Link>
            </div>
        </div>
    </div>
</template>
