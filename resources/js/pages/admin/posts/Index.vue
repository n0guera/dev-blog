<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    posts: {
        data: Array<{
            id: number;
            title: string;
            slug: string;
            user: {
                name: string;
            };
            status: {
                name: string;
            };
            votes_count: number;
        }>;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

function destroy(id: number) {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(`/admin/posts/${id}`, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Manage Posts" />
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-foreground">Posts</h1>
            <Link
                href="/admin/posts/create"
                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90"
            >
                Create Post
            </Link>
        </div>

        <table class="min-w-full divide-y divide-border border border-border">
            <thead class="bg-muted">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-foreground">Title</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-foreground">Author</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-foreground">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-foreground">Votes</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-foreground">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="post in posts.data" :key="post.id" class="border-t border-border">
                    <td class="px-4 py-2 text-sm text-foreground">{{ post.title }}</td>
                    <td class="px-4 py-2 text-sm text-foreground">{{ post.user.name }}</td>
                    <td class="px-4 py-2 text-sm text-muted-foreground">{{ post.status.name }}</td>
                    <td class="px-4 py-2 text-sm text-foreground">{{ post.votes_count }}</td>
                    <td class="space-x-2 px-4 py-2">
                        <Link
                            :href="`/admin/posts/${post.slug}/edit`"
                            class="text-sm text-primary hover:underline"
                        >
                            Edit
                        </Link>
                        <button
                            type="button"
                            class="text-sm text-destructive hover:underline"
                            @click="destroy(post.id)"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <p v-if="posts.data.length === 0" class="py-8 text-center text-sm text-muted-foreground">
            No posts yet.
        </p>

        <div
            v-if="posts.total > 0"
            class="flex items-center justify-between text-sm text-muted-foreground"
        >
            <span>Showing {{ posts.from }}–{{ posts.to }} of {{ posts.total }}</span>
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