<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

type AdminPost = {
    id: number;
    title: string;
    slug: string;
    user: { id: number; name: string };
    status: { id: number; name: string };
    vote_score: number;
    up_votes_count: number;
    down_votes_count: number;
};

defineProps<{
    posts: {
        data: AdminPost[];
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

function destroy(slug: string): void {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(PostController.destroy.url({ post: slug }), {
            preserveState: true,
        });
    }
}
</script>

<template>
    <Head title="Manage Posts" />

    <AppLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <Heading
                    title="Posts"
                    description="Manage all blog posts, their status and votes."
                />
                <Button as-child>
                    <Link :href="PostController.create.url()"> Create Post </Link>
                </Button>
            </div>

            <div
                v-if="posts.data.length === 0"
                class="rounded-lg border border-border bg-card p-8 text-center text-sm text-muted-foreground"
            >
                No posts yet.
            </div>

            <div v-else class="space-y-4">
                <div
                    class="overflow-x-auto rounded-lg border border-border bg-card"
                >
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-border bg-muted/50 text-left text-muted-foreground"
                            >
                                <th class="px-4 py-3 font-medium">Title</th>
                                <th class="px-4 py-3 font-medium">Author</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Votes</th>
                                <th class="px-4 py-3 text-right font-medium">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="post in posts.data"
                                :key="post.id"
                                class="transition-colors hover:bg-muted/50"
                            >
                                <td class="px-4 py-3 font-medium">
                                    {{ post.title }}
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ post.user.name }}
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="
                                            post.status.name === 'published'
                                                ? 'default'
                                                : 'secondary'
                                        "
                                    >
                                        {{ post.status.name }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="font-medium"
                                            :class="
                                                post.vote_score > 0
                                                    ? 'text-green-500'
                                                    : post.vote_score < 0
                                                      ? 'text-red-500'
                                                      : 'text-foreground'
                                            "
                                        >
                                            {{ post.vote_score }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-0.5 text-xs text-muted-foreground"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="14"
                                                height="14"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="text-green-500"
                                            >
                                                <path d="M18 15l-6-6-6 6" />
                                            </svg>
                                            {{ post.up_votes_count }}
                                        </span>
                                        <span
                                            class="inline-flex items-center gap-0.5 text-xs text-muted-foreground"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="14"
                                                height="14"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="text-red-500"
                                            >
                                                <path d="M6 9l6 6 6-6" />
                                            </svg>
                                            {{ post.down_votes_count }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            as-child
                                            variant="outline"
                                            size="sm"
                                        >
                                            <Link
                                                :href="
                                                    PostController.edit.url({
                                                        post: post.slug,
                                                    })
                                                "
                                            >
                                                Edit
                                            </Link>
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            @click="destroy(post.slug)"
                                        >
                                            Delete
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex items-center justify-between text-sm text-muted-foreground"
                >
                    <span>
                        Showing {{ posts.from }}–{{ posts.to }} of
                        {{ posts.total }}
                    </span>
                    <div class="flex gap-2">
                        <Button
                            v-if="posts.prev_page_url"
                            as-child
                            variant="outline"
                            size="sm"
                        >
                            <Link :href="posts.prev_page_url"> Prev </Link>
                        </Button>
                        <Button
                            v-if="posts.next_page_url"
                            as-child
                            variant="outline"
                            size="sm"
                        >
                            <Link :href="posts.next_page_url"> Next </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
