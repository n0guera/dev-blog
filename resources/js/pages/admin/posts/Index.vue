<script setup lang="ts">
// Props received from the controller
import { Link } from '@inertiajs/vue3';

defineProps<{ posts: any }>();
</script>

<template>
    <div class="space-y-4">
        <h1 class="text-2xl font-bold">Posts – Admin Panel</h1>
        <table class="min-w-full divide-y divide-gray-200 border">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="post in posts.data" :key="post.id" class="border-t">
                    <td class="px-4 py-2">{{ post.title }}</td>
                    <td class="px-4 py-2">{{ post.status?.name ?? '—' }}</td>
                    <td class="space-x-2 px-4 py-2">
                        <Link
                            :href="`/admin/posts/${post.id}/edit`"
                            class="text-blue-600 hover:underline"
                            >Edit</Link
                        >
                        <form
                            :action="`/admin/posts/${post.id}`"
                            method="POST"
                            class="inline"
                        >
                            <input
                                type="hidden"
                                name="_method"
                                value="DELETE"
                            />
                            <input
                                type="hidden"
                                name="_token"
                                :value="csrfToken"
                            />
                            <button
                                type="submit"
                                class="text-red-600 hover:underline"
                            >
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="flex items-center justify-between">
            <div>
                Showing {{ posts.from }} - {{ posts.to }} of {{ posts.total }}
            </div>
            <Link
                v-if="posts.prev_page_url"
                :href="posts.prev_page_url"
                class="rounded bg-gray-200 px-3 py-1"
                >Prev</Link
            >
            <Link
                v-if="posts.next_page_url"
                :href="posts.next_page_url"
                class="rounded bg-gray-200 px-3 py-1"
                >Next</Link
            >
        </div>
    </div>
</template>

<script lang="ts">
import { usePage } from '@inertiajs/vue3';
const { props } = usePage();
const csrfToken = (props as any).csrfToken ?? '';
</script>
