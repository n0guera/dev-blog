<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PostForm from '@/components/PostForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    post: any;
    statuses: any;
}>();

const form = useForm({
    title: props.post.data.title,
    excerpt: props.post.data.excerpt ?? '',
    content: props.post.data.content,
    status_id: props.post.data.status?.id ?? '',
});

const submit = () => {
    form.put(`/admin/posts/${props.post.data.id}`);
};
</script>

<template>
    <Head title="Edit Post" />
    <AppLayout>
        <h1 class="mb-2">Edit Post</h1>
        <form @submit.prevent="submit">
            <PostForm :form="form" :statuses="statuses" />
            <button
                type="submit"
                :disabled="form.processing"
                class="mt-4 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
            >
                Update
            </button>
        </form>
    </AppLayout>
</template>
