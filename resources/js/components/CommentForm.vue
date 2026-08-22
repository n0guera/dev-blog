<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    postId: number;
    parentId?: number | null;
    onSubmit?: () => void;
}>();

const form = useForm({
    content: '',
    parent_id: props.parentId ?? null,
});

function submit() {
    form.post(`/posts/${props.postId}/comments`, {
        preserveState: true,
        onSuccess: () => {
            form.reset('content');
            props.onSubmit?.();
        },
    });
}
</script>

<template>
    <form @submit.prevent="submit" class="space-y-3">
        <textarea
            v-model="form.content"
            :placeholder="parentId ? 'Write a reply...' : 'Write a comment...'"
            rows="3"
            class="w-full resize-none rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"
        />
        <p v-if="form.errors.content" class="text-sm text-destructive">
            {{ form.errors.content }}
        </p>
        <div class="flex justify-end">
            <button
                type="submit"
                :disabled="form.processing || !form.content.trim()"
                class="rounded-md bg-primary px-4 py-1.5 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
            >
                {{ parentId ? 'Reply' : 'Comment' }}
            </button>
        </div>
    </form>
</template>
