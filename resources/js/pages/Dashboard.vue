<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PostController from '@/actions/App/Http/Controllers/Admin/PostController';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const page = usePage();

const user = computed(() => page.props.auth.user);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

const links = [
    {
        name: "Create Post",
        href: PostController.create.url(),
    },
    {
        name: "Posts",
        href: PostController.index.url(),
    },
];
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="user.role?.name === 'admin'">
            <Link v-for="link in links" v-bind:key="link.name" :href="link.href"
                class="m-2 border inline-flex rounded-sm p-2 text-lg">
                <p>{{ link.name }}</p>
            </Link>
        </div>
    </AppLayout>
</template>
