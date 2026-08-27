<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CommentsSection from '@/components/CommentsSection.vue';
import MarkdownRenderer from '@/components/MarkdownRenderer.vue';
import TagPill from '@/components/TagPill.vue';
import VoteButton from '@/components/VoteButton.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{ post: any }>();
</script>

<template>
    <Head :title="post.data.title" />
    <AppLayout>
        <article class="mx-auto w-full max-w-3xl px-4 sm:px-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="mb-3 text-3xl font-bold text-foreground">
                    {{ post.data.title }}
                </h1>
                <div
                    class="mb-4 flex items-center gap-3 text-sm text-muted-foreground"
                >
                    <span v-if="post.data.user">{{ post.data.user.name }}</span>
                    <span v-if="post.data.published_at">&middot;</span>
                    <span v-if="post.data.published_at">
                        {{
                            new Date(post.data.published_at).toLocaleDateString(
                                'en-US',
                                {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                },
                            )
                        }}
                    </span>
                </div>
                <div
                    v-if="post.data.tags?.length"
                    class="flex flex-wrap gap-1.5"
                >
                    <TagPill
                        v-for="tag in post.data.tags"
                        :key="tag.id"
                        :tag="tag"
                    />
                </div>
            </div>

            <!-- Vote + Content -->
            <div class="flex gap-6">
                <div class="sticky top-20 hidden shrink-0 sm:block">
                    <VoteButton
                        votable-type="post"
                        :votable-id="post.data.id"
                        :initial-score="post.data.vote_score"
                        :initial-vote="post.data.user_vote ?? null"
                    />
                </div>
                <div
                    class="prose min-w-0 flex-1 prose-neutral lg:prose-lg dark:prose-invert"
                >
                    <MarkdownRenderer :content="post.data.content" />
                </div>
            </div>

            <!-- Mobile Vote -->
            <div class="mt-4 flex justify-center sm:hidden">
                <VoteButton
                    votable-type="post"
                    :votable-id="post.data.id"
                    :initial-score="post.data.vote_score"
                    :initial-vote="post.data.user_vote ?? null"
                />
            </div>

            <!-- Comments -->
            <div class="mt-12 border-t border-border pt-8">
                <CommentsSection
                    :post-slug="post.data.slug"
                    :comments="post.data.comments ?? []"
                />
            </div>
        </article>
    </AppLayout>
</template>
