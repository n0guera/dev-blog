<script setup lang="ts">
import CommentItem from './CommentItem.vue';
import CommentForm from './CommentForm.vue';

defineProps<{
    postSlug: string;
    comments: Array<{
        id: number;
        content: string;
        created_at: string;
        user: { id: number; name: string };
        replies: any[];
        vote_score: number;
        user_vote: 'up' | 'down' | null;
    }>;
}>();
</script>

<template>
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-foreground">
            Comments ({{ comments.length }})
        </h3>

        <!-- New Comment Form -->
        <CommentForm :post-slug="postSlug" />

        <!-- Comments List -->
        <div class="divide-y divide-border">
            <CommentItem
                v-for="comment in comments"
                :key="comment.id"
                :comment="comment"
                :post-slug="postSlug"
            />
        </div>

        <p
            v-if="comments.length === 0"
            class="py-8 text-center text-sm text-muted-foreground"
        >
            No comments yet. Be the first to share your thoughts!
        </p>
    </div>
</template>
