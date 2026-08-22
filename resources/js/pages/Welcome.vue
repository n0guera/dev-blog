<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{
    latestPosts?: Array<{
        id: number;
        title: string;
        slug: string;
        excerpt: string | null;
        user: { name: string };
        tags: Array<{ id: number; name: string; slug: string }>;
    }>;
}>();
</script>

<template>

    <Head title="Welcome" />
    <AppLayout>
        <!-- Hero -->
        <section class="py-16 text-center">
            <h1 class="mb-4 text-4xl font-bold text-foreground">n0guera's dev blog</h1>
            <p class="mx-auto mb-8 max-w-xl text-lg text-muted-foreground">
                Sharing thoughts on code, architecture, and
                building things that matter.
            </p>
            <div class="flex items-center justify-center gap-3">
                <Link href="/posts"
                    class="rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90">
                    Browse Posts
                </Link>
                <Link href="/search"
                    class="rounded-md border border-border bg-card px-5 py-2.5 text-sm font-medium text-foreground transition-opacity hover:opacity-80">
                    Search
                </Link>
            </div>
        </section>

        <!-- Latest Posts -->
        <section v-if="latestPosts?.length" class="pb-16">
            <h2 class="mb-6 text-2xl font-semibold text-foreground">Latest Posts</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link v-for="post in latestPosts" :key="post.id" :href="`/posts/${post.slug}`"
                    class="block rounded-lg border border-border bg-card p-5 transition-shadow hover:shadow-md">
                    <h3 class="mb-1 text-lg font-semibold text-foreground">{{ post.title }}</h3>
                    <p v-if="post.excerpt" class="mb-3 line-clamp-2 text-sm text-muted-foreground">
                        {{ post.excerpt }}
                    </p>
                    <div class="text-xs text-muted-foreground">{{ post.user.name }}</div>
                </Link>
            </div>
        </section>
    </AppLayout>
</template>
