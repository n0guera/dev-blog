<script setup lang="ts">
/* eslint-disable vue/no-mutating-props */
import MarkdownEditor from './MarkdownEditor.vue';
import TagInput from './TagInput.vue';

defineProps<{
    form: any;
    statuses: Array<any>;
    tags?: Array<{ id: number; name: string; slug: string }>;
    availableTags?: Array<{ id: number; name: string; slug: string }>;
}>();
</script>

<template>
    <div class="space-y-5">
        <!-- Title -->
        <div>
            <label for="post-title" class="mb-1.5 block text-sm font-medium text-foreground">
                Title
            </label>
            <input
                id="post-title"
                v-model="form.title"
                type="text"
                placeholder="Enter post title"
                class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p v-if="form.errors.title" class="mt-1 text-sm text-destructive">{{ form.errors.title }}</p>
        </div>

        <!-- Excerpt -->
        <div>
            <label for="post-excerpt" class="mb-1.5 block text-sm font-medium text-foreground">
                Excerpt
            </label>
            <input
                id="post-excerpt"
                v-model="form.excerpt"
                type="text"
                placeholder="Brief description of the post"
                class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"
            />
            <p v-if="form.errors.excerpt" class="mt-1 text-sm text-destructive">{{ form.errors.excerpt }}</p>
        </div>

        <!-- Tags -->
        <div v-if="availableTags">
            <label class="mb-1.5 block text-sm font-medium text-foreground">
                Tags
            </label>
            <TagInput
                v-model="form.tags"
                :available-tags="availableTags"
            />
            <p v-if="form.errors.tags" class="mt-1 text-sm text-destructive">{{ form.errors.tags }}</p>
        </div>

        <!-- Status -->
        <div>
            <label for="post-status" class="mb-1.5 block text-sm font-medium text-foreground">
                Status
            </label>
            <select
                id="post-status"
                v-model.number="form.status_id"
                class="w-full rounded-md border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-ring"
            >
                <option v-for="status in statuses" :key="status.id" :value="status.id">
                    {{ status.name }}
                </option>
            </select>
            <p v-if="form.errors.status_id" class="mt-1 text-sm text-destructive">{{ form.errors.status_id }}</p>
        </div>

        <!-- Content -->
        <div>
            <label class="mb-1.5 block text-sm font-medium text-foreground">
                Content
            </label>
            <MarkdownEditor v-model="form.content" />
            <p v-if="form.errors.content" class="mt-1 text-sm text-destructive">{{ form.errors.content }}</p>
        </div>
    </div>
</template>