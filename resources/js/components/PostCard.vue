<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import TagPill from './TagPill.vue';

defineProps<{
    post: {
        id: number;
        title: string;
        slug: string;
        excerpt: string | null;
        published_at: string | null;
        user: { id: number; name: string };
        tags: Array<{ id: number; name: string; slug: string }>;
        vote_score: number;
        comments_count: number;
    };
}>();

function formatDate(dateStr: string | null): string {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Link
        :href="`/posts/${post.slug}`"
        class="block rounded-lg border border-border bg-card p-5 transition-shadow hover:shadow-md"
    >
        <h2 class="mb-1 text-lg font-semibold text-foreground">
            {{ post.title }}
        </h2>

        <p v-if="post.excerpt" class="mb-3 line-clamp-2 text-sm text-muted-foreground">
            {{ post.excerpt }}
        </p>

        <div v-if="post.tags?.length" class="mb-3 flex flex-wrap gap-1.5">
            <TagPill
                v-for="tag in post.tags"
                :key="tag.id"
                :tag="tag"
                :clickable="false"
            />
        </div>

        <div class="flex items-center gap-2 text-xs text-muted-foreground">
            <span>{{ post.user.name }}</span>
            <span v-if="post.published_at">&middot;</span>
            <span v-if="post.published_at">{{ formatDate(post.published_at) }}</span>
            <span>&middot;</span>
            <span>{{ post.vote_score }} {{ post.vote_score === 1 ? 'vote' : 'votes' }}</span>
            <span>&middot;</span>
            <span>{{ post.comments_count }} {{ post.comments_count === 1 ? 'comment' : 'comments' }}</span>
        </div>
    </Link>
</template>
