<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutDashboard } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import UserInfo from '@/components/UserInfo.vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { dashboard } from '@/routes';
import type { User } from '@/types';

const page = usePage();
const user = computed(() => page.props.auth.user as User | undefined);
const isAdmin = computed(() => user.value?.role?.name === 'admin');
</script>

<template>
    <DropdownMenu v-if="user">
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="relative h-9 w-9 rounded-full">
                <UserInfo :user="user" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-56" align="end" :side-offset="8">
            <UserMenuContent :user="user" />
            <template v-if="isAdmin">
                <DropdownMenuSeparator />
                <DropdownMenuItem :as-child="true">
                    <Link :href="dashboard()" class="flex w-full items-center">
                        <LayoutDashboard class="mr-2 h-4 w-4" />
                        Dashboard
                    </Link>
                </DropdownMenuItem>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
