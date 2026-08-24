<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import PostForm from '@/components/PostForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    post: any;
    statuses: any;
    tags: Array<{ id: number; name: string; slug: string }>;
}>();

const form = useForm({
    title: props.post.data.title,
    excerpt: props.post.data.excerpt ?? '',
    content: props.post.data.content,
    status_id: props.post.data.status?.id ?? '',
    tags: props.post.data.tags ?? [] as Array<{ id: number; name: string; slug: string }>,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        tags: data.tags.map((t) => t.name),
    })).put(`/admin/posts/${props.post.data.slug}`, {
        preserveState: true,
    });
};
</script>

<template>
    <Head title="Edit Post" />
    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6">
            <h1 class="text-2xl font-bold text-foreground">Edit Post</h1>
            <form @submit.prevent="submit" class="space-y-6 rounded-lg border border-border bg-card p-6">
                <PostForm
                    :form="form"
                    :statuses="statuses"
                    :available-tags="tags"
                />
                <div class="flex justify-end gap-3 border-t border-border pt-4">
                    <Link
                        href="/admin/posts"
                        class="rounded-md border border-border bg-background px-4 py-2 text-sm font-medium text-foreground transition-opacity hover:opacity-80"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Update Post
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>