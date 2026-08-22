<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import TagPill from './TagPill.vue';

const props = withDefaults(
    defineProps<{
        modelValue: Array<{ id: number; name: string; slug: string }>;
        availableTags?: Array<{ id: number; name: string; slug: string }>;
    }>(),
    { availableTags: () => [] },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: Array<{ id: number; name: string; slug: string }>): void;
}>();

const input = ref('');
const showDropdown = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const selectedIds = computed(() => new Set(props.modelValue.map((t) => t.id)));

const filteredTags = computed(() => {
    const query = input.value.toLowerCase().trim();
    return props.availableTags.filter(
        (tag) =>
            !selectedIds.value.has(tag.id) &&
            (query === '' || tag.name.toLowerCase().includes(query)),
    );
});

function addTag(tag: { id: number; name: string; slug: string }) {
    if (!selectedIds.value.has(tag.id)) {
        emit('update:modelValue', [...props.modelValue, tag]);
    }
    input.value = '';
    showDropdown.value = false;
}

function removeTag(tagId: number) {
    emit(
        'update:modelValue',
        props.modelValue.filter((t) => t.id !== tagId),
    );
}

function onKeydown(e: KeyboardEvent) {
    if (e.key === 'Backspace' && input.value === '' && props.modelValue.length > 0) {
        removeTag(props.modelValue[props.modelValue.length - 1].id);
    }
    if (e.key === 'Enter') {
        e.preventDefault();
        if (filteredTags.value.length > 0) {
            addTag(filteredTags.value[0]);
        }
    }
    if (e.key === 'Escape') {
        showDropdown.value = false;
    }
}

function onClickOutside(e: MouseEvent) {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target as Node)) {
        showDropdown.value = false;
    }
}

onMounted(() => document.addEventListener('click', onClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside));
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <div class="flex flex-wrap items-center gap-1.5 rounded-md border border-border bg-background px-3 py-2">
            <TagPill
                v-for="tag in modelValue"
                :key="tag.id"
                :tag="tag"
                :clickable="false"
            />
            <button
                v-for="tag in modelValue"
                :key="`remove-${tag.id}`"
                type="button"
                class="ml-0.5 text-muted-foreground hover:text-foreground"
                @click="removeTag(tag.id)"
            >
                &times;
            </button>

            <input
                v-model="input"
                type="text"
                placeholder="Add tag..."
                class="min-w-[80px] flex-1 bg-transparent text-sm text-foreground placeholder:text-muted-foreground focus:outline-none"
                @keydown="onKeydown"
                @focus="showDropdown = true"
            />
        </div>

        <ul
            v-if="showDropdown && filteredTags.length > 0"
            class="absolute z-10 mt-1 max-h-40 w-full overflow-y-auto rounded-md border border-border bg-popover shadow-md"
        >
            <li
                v-for="tag in filteredTags"
                :key="tag.id"
                class="cursor-pointer px-3 py-1.5 text-sm text-popover-foreground hover:bg-accent hover:text-accent-foreground"
                @mousedown.prevent="addTag(tag)"
            >
                {{ tag.name }}
            </li>
        </ul>
    </div>
</template>
