<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    votableType: 'post' | 'comment';
    votableId: number;
    initialScore: number;
    initialVote?: 'up' | 'down' | null;
}>();

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const score = ref(props.initialScore);
const currentVote = ref<'up' | 'down' | null>(props.initialVote ?? null);

const upvoteClasses = computed(() =>
    currentVote.value === 'up'
        ? 'text-green-500'
        : 'text-muted-foreground hover:text-green-500',
);

const downvoteClasses = computed(() =>
    currentVote.value === 'down'
        ? 'text-red-500'
        : 'text-muted-foreground hover:text-red-500',
);

async function vote(direction: 'up' | 'down') {
    if (!isAuthenticated.value) return;

    const endpoint =
        currentVote.value === direction
            ? `/votes/${props.votableType}/${props.votableId}`
            : `/votes/${props.votableType}/${props.votableId}/${direction}`;

    const method = currentVote.value === direction ? 'DELETE' : 'POST';

    const response = await fetch(endpoint, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute('content') ?? '',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    if (response.ok) {
        const data = await response.json();
        score.value = data.score;
        currentVote.value =
            currentVote.value === direction ? null : direction;
    }
}
</script>

<template>
    <div class="flex flex-col items-center gap-1">
        <button
            type="button"
            :disabled="!isAuthenticated"
            :class="[
                upvoteClasses,
                isAuthenticated ? 'cursor-pointer' : 'cursor-not-allowed opacity-40',
            ]"
            class="transition-colors"
            @click="vote('up')"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M18 15l-6-6-6 6" />
            </svg>
        </button>

        <span class="min-w-[2ch] text-center text-sm font-medium text-foreground">
            {{ score }}
        </span>

        <button
            type="button"
            :disabled="!isAuthenticated"
            :class="[
                downvoteClasses,
                isAuthenticated ? 'cursor-pointer' : 'cursor-not-allowed opacity-40',
            ]"
            class="transition-colors"
            @click="vote('down')"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M6 9l6 6 6-6" />
            </svg>
        </button>
    </div>
</template>
