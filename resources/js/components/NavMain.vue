<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { Home, FileText, Info } from 'lucide-vue-next';
import PostController from '@/actions/App/Http/Controllers/PostController';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
} from '@/components/ui/navigation-menu';
import { cn } from '@/lib/utils';
import { dashboard, login, register, logout } from '@/routes';
import { home } from '@/routes';

const page = usePage();

const canRegister = page.props.canRegister;

const navItems = [
    {
        title: 'Home',
        href: home().url,
        icon: Home,
    },
    {
        title: 'Posts',
        href: PostController.index().url,
        icon: FileText,
    },
    {
        title: 'About',
        href: '/about',
        icon: Info,
    },
];
</script>

<template>
    <NavigationMenu>
        <NavigationMenuList class="space-x-8">
            <NavigationMenuItem v-for="item in navItems" :key="item.title">
                <NavigationMenuLink :class="cn(
                    'group relative inline-flex h-9 w-max items-center justify-center px-0.5 py-2 text-sm font-medium',
                    'before:absolute before:inset-x-0 before:bottom-0 before:h-0.5 before:scale-x-0 before:bg-primary before:transition-transform',
                    'hover:text-accent-foreground hover:before:scale-x-100',
                    'focus:text-accent-foreground focus:outline-none focus:before:scale-x-100',
                )
                    ">
                    <a :href="item.href" class="flex items-center gap-2.5">
                        <component :is="item.icon" class="h-5 w-5 shrink-0" />
                        {{ item.title }}
                    </a>
                </NavigationMenuLink>
            </NavigationMenuItem>
            <Link v-if="$page.props.auth.user" :href="logout()"
                class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                Log out
            </Link>
            <template v-else>
                <Link :href="login()"
                    class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                    Log in
                </Link>
                <Link v-if="canRegister" :href="register()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                    Register
                </Link>
            </template>
        </NavigationMenuList>
    </NavigationMenu>
</template>
