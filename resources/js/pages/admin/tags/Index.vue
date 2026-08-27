<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    tags: {
        data: Array<{
            id: number;
            name: string;
            slug: string;
            posts_count: number;
        }>;
        from: number | null;
        to: number | null;
        total: number;
        prev_page_url: string | null;
        next_page_url: string | null;
    };
}>();

function destroy(id: number) {
    if (confirm('Are you sure you want to delete this tag?')) {
        router.delete(`/admin/tags/${id}`, { preserveState: true });
    }
}
</script>

<template>
    <Head title="Manage Tags" />
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-foreground">Tags</h1>
            <Link
                href="/admin/tags/create"
                class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90"
            >
                Create Tag
            </Link>
        </div>

        <table class="min-w-full divide-y divide-gray-200 border border-border">
            <thead class="bg-muted">
                <tr>
                    <th
                        class="px-4 py-2 text-left text-sm font-medium text-foreground"
                    >
                        Name
                    </th>
                    <th
                        class="px-4 py-2 text-left text-sm font-medium text-foreground"
                    >
                        Slug
                    </th>
                    <th
                        class="px-4 py-2 text-left text-sm font-medium text-foreground"
                    >
                        Posts
                    </th>
                    <th
                        class="px-4 py-2 text-left text-sm font-medium text-foreground"
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="tag in tags.data"
                    :key="tag.id"
                    class="border-t border-border"
                >
                    <td class="px-4 py-2 text-sm text-foreground">
                        {{ tag.name }}
                    </td>
                    <td class="px-4 py-2 text-sm text-muted-foreground">
                        {{ tag.slug }}
                    </td>
                    <td class="px-4 py-2 text-sm text-muted-foreground">
                        {{ tag.posts_count }}
                    </td>
                    <td class="space-x-2 px-4 py-2">
                        <Link
                            :href="`/admin/tags/${tag.id}/edit`"
                            class="text-sm text-primary hover:underline"
                        >
                            Edit
                        </Link>
                        <button
                            type="button"
                            class="text-sm text-destructive hover:underline"
                            @click="destroy(tag.id)"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <p
            v-if="tags.data.length === 0"
            class="py-8 text-center text-sm text-muted-foreground"
        >
            No tags yet.
        </p>

        <div
            v-if="tags.total > 0"
            class="flex items-center justify-between text-sm text-muted-foreground"
        >
            <span
                >Showing {{ tags.from }}–{{ tags.to }} of {{ tags.total }}</span
            >
            <div class="flex gap-2">
                <Link
                    v-if="tags.prev_page_url"
                    :href="tags.prev_page_url"
                    class="rounded-md bg-secondary px-3 py-1.5 text-secondary-foreground transition-opacity hover:opacity-80"
                >
                    Prev
                </Link>
                <Link
                    v-if="tags.next_page_url"
                    :href="tags.next_page_url"
                    class="rounded-md bg-secondary px-3 py-1.5 text-secondary-foreground transition-opacity hover:opacity-80"
                >
                    Next
                </Link>
            </div>
        </div>
    </div>
</template>
