<script setup lang="ts">
import MarkdownEditor from './MarkdownEditor.vue';

defineProps<{
    form: any,
    statuses: Array<any>,
}>()
</script>

<template>
    <!-- Title -->
    <div>
        <label>
            Title
        </label>

        <input v-model="form.title" type="text" placeholder="Title">
        <p v-if="form.errors.title">{{ form.errors.title }}</p>
    </div>

    <!-- Status -->
    <div>
        <label>
            Status
        </label>

        <select v-model="form.status_id">
            <option v-for="status in statuses" :key="status.id" :value="status.id">
                {{ status.name }}</option>
        </select>

        <p v-if="form.errors.status_id">
            {{ form.errors.status_id }}
        </p>
    </div>

    <!-- Content -->
    <div>
        <label>
            Content
        </label>

        <MarkdownEditor v-model="form.content" />
        <p v-if="form.errors.content"> {{ form.errors.content }}</p>
    </div>
</template>
