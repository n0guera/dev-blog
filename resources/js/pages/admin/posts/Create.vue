<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import PostForm from '@/components/PostForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps<{ statuses: any }>();

const form = useForm({
    title: '',
    content: '',
    status_id: '',
});

const submit = () => {
    form.submit(PostController.store());
}
</script>

<template>

    <Head title="Create Post" />
    <AppLayout>
        <h1 class="mb-2">Create Post</h1>
        <form @submit.prevent="submit">
            <PostForm :form="form" :statuses="statuses" />
            <button type="submit" :disabled="form.processing">Create</button>
        </form>
    </AppLayout>
</template>
