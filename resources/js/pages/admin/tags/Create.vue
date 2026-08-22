<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    name: '',
});

function submit() {
    form.post('/admin/tags');
}
</script>

<template>
    <Head title="Create Tag" />
    <AppLayout>
        <h1 class="mb-4 text-2xl font-bold text-foreground">Create Tag</h1>

        <form @submit.prevent="submit" class="max-w-md space-y-4">
            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-foreground">
                    Name
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="Tag name"
                    class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-destructive">
                    {{ form.errors.name }}
                </p>
                <p class="mt-1 text-xs text-muted-foreground">
                    Slug will be auto-generated from the name.
                </p>
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-opacity hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Create
                </button>
                <a
                    href="/admin/tags"
                    class="rounded-md border border-border bg-card px-4 py-2 text-sm font-medium text-foreground transition-opacity hover:opacity-80"
                >
                    Cancel
                </a>
            </div>
        </form>
    </AppLayout>
</template>
