<script setup lang="ts">
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import CommentForm from './CommentForm.vue';
import VoteButton from './VoteButton.vue';

interface Comment {
    id: number;
    content: string;
    created_at: string;
    user: { id: number; name: string };
    replies: Comment[];
    vote_score: number;
    user_vote: 'up' | 'down' | null;
}

const props = defineProps<{
    comment: Comment;
    postSlug: string;
    depth?: number;
}>();

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);
const isOwner = computed(() => currentUser.value?.id === props.comment.user.id);
const isAdmin = computed(() => currentUser.value?.role?.name === 'admin');
const canDelete = computed(() => isOwner.value || isAdmin.value);

const showReplyForm = ref(false);
const collapsed = ref(false);

function timeAgo(dateStr: string): string {
    const now = new Date();
    const date = new Date(dateStr);
    const seconds = Math.floor((now.getTime() - date.getTime()) / 1000);
    if (seconds < 60) return 'just now';
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return `${days}d ago`;
}
</script>

<template>
    <div :class="depth && depth > 0 ? 'ml-6 border-l-2 border-border pl-4' : ''">
        <div class="space-y-2 py-3">
            <!-- Header -->
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-foreground">
                    {{ comment.user.name }}
                </span>
                <span class="text-xs text-muted-foreground">
                    {{ timeAgo(comment.created_at) }}
                </span>
            </div>

            <!-- Content -->
            <p class="text-sm leading-relaxed text-foreground">
                {{ comment.content }}
            </p>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <VoteButton
                    :votable-type="'comment'"
                    :votable-id="comment.id"
                    :initial-score="comment.vote_score"
                    :initial-vote="comment.user_vote"
                    orientation="horizontal"
                />

                <button
                    v-if="currentUser"
                    type="button"
                    class="text-xs text-muted-foreground transition-colors hover:text-foreground"
                    @click="showReplyForm = !showReplyForm"
                >
                    Reply
                </button>

                <button
                    v-if="canDelete"
                    type="button"
                    class="text-xs text-muted-foreground transition-colors hover:text-destructive"
                    @click="$inertia.delete(`/comments/${comment.id}`, { preserveState: true })"
                >
                    Delete
                </button>

                <button
                    v-if="comment.replies?.length"
                    type="button"
                    class="text-xs text-muted-foreground transition-colors hover:text-foreground"
                    @click="collapsed = !collapsed"
                >
                    {{ collapsed ? `Show replies (${comment.replies.length})` : `Hide replies` }}
                </button>
            </div>

            <!-- Reply Form -->
            <div v-if="showReplyForm" class="mt-2">
                <CommentForm
                    :post-slug="postSlug"
                    :parent-id="comment.id"
                    @submit="showReplyForm = false"
                />
            </div>
        </div>

        <!-- Nested Replies -->
        <div v-if="!collapsed && comment.replies?.length">
            <CommentItem
                v-for="reply in comment.replies"
                :key="reply.id"
                :comment="reply"
                :post-slug="postSlug"
                :depth="(depth ?? 0) + 1"
            />
        </div>
    </div>
</template>
