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
    (
        e: 'update:modelValue',
        value: Array<{ id: number; name: string; slug: string }>,
    ): void;
}>();

const input = ref('');
const showDropdown = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const selectedNames = computed(
    () => new Set(props.modelValue.map((t) => t.name.toLowerCase())),
);

const filteredTags = computed(() => {
    const query = input.value.toLowerCase().trim();

    return props.availableTags.filter(
        (tag) =>
            !selectedNames.value.has(tag.name.toLowerCase()) &&
            (query === '' || tag.name.toLowerCase().includes(query)),
    );
});

function addTag(tag: { id: number; name: string; slug: string }) {
    if (!selectedNames.value.has(tag.name.toLowerCase())) {
        emit('update:modelValue', [...props.modelValue, tag]);
    }

    input.value = '';
    showDropdown.value = false;
}

function addNewTag(name: string) {
    const trimmed = name.trim();

    if (trimmed === '' || selectedNames.value.has(trimmed.toLowerCase())) {
        return;
    }

    // Check if it matches an available tag (case-insensitive)
    const existing = props.availableTags.find(
        (t) => t.name.toLowerCase() === trimmed.toLowerCase(),
    );

    if (existing) {
        addTag(existing);
    } else {
        // New tag — send with a temp negative ID and the name
        // Backend will find-or-create by name
        emit('update:modelValue', [
            ...props.modelValue,
            {
                id: -(Date.now() + Math.random()),
                name: trimmed,
                slug: trimmed.toLowerCase().replace(/\s+/g, '-'),
            },
        ]);
    }
}

function removeTag(tagId: number) {
    emit(
        'update:modelValue',
        props.modelValue.filter((t) => t.id !== tagId),
    );
}

function processInput() {
    const text = input.value;

    if (text.includes(',')) {
        const parts = text.split(',');
        parts.forEach((part, index) => {
            if (index < parts.length - 1 && part.trim() !== '') {
                addNewTag(part);
            }
        });
        // Keep the last part (after the last comma) in the input
        input.value = parts[parts.length - 1] ?? '';
    }
}

function onKeydown(e: KeyboardEvent) {
    if (
        e.key === 'Backspace' &&
        input.value === '' &&
        props.modelValue.length > 0
    ) {
        removeTag(props.modelValue[props.modelValue.length - 1].id);
    }

    if (e.key === 'Enter') {
        e.preventDefault();

        if (filteredTags.value.length > 0) {
            addTag(filteredTags.value[0]);
        } else if (input.value.trim() !== '') {
            addNewTag(input.value);
            input.value = '';
        }
    }

    if (e.key === 'Escape') {
        showDropdown.value = false;
    }

    if (e.key === ',' || e.key === 'Tab') {
        e.preventDefault();

        if (input.value.trim() !== '') {
            addNewTag(input.value);
            input.value = '';
        }
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
        <div
            class="flex flex-wrap items-center gap-1.5 rounded-md border border-border bg-background px-3 py-2"
        >
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
                placeholder="Add tags (comma-separated)..."
                class="min-w-[80px] flex-1 bg-transparent text-sm text-foreground placeholder:text-muted-foreground focus:outline-none"
                @input="processInput"
                @keydown="onKeydown"
                @focus="showDropdown = true"
            />
        </div>

        <ul
            v-if="
                showDropdown &&
                (filteredTags.length > 0 || input.trim().length > 0)
            "
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
            <li
                v-if="
                    input.trim().length > 0 &&
                    !filteredTags.some(
                        (t) =>
                            t.name.toLowerCase() === input.trim().toLowerCase(),
                    )
                "
                class="cursor-pointer px-3 py-1.5 text-sm text-popover-foreground hover:bg-accent hover:text-accent-foreground"
                @mousedown.prevent="
                    addNewTag(input);
                    input = '';
                "
            >
                Create "{{ input.trim() }}"
            </li>
        </ul>
    </div>
</template>
