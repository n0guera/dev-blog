<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Home, FileText, Info, Menu } from 'lucide-vue-next';
import { computed } from 'vue';
import PostController from '@/actions/App/Http/Controllers/PostController';
import { Button } from '@/components/ui/button';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import UserMenu from '@/components/UserMenu.vue';
import { cn } from '@/lib/utils';
import { home, login, register } from '@/routes';

const page = usePage();

const user = computed(() => page.props.auth.user);

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
    <div>
        <!-- Mobile navigation -->
        <div class="flex items-center justify-between md:hidden">
            <Sheet>
                <SheetTrigger as-child>
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label="Open navigation menu"
                    >
                        <Menu class="size-5" />
                    </Button>
                </SheetTrigger>
                <SheetContent side="left">
                    <SheetHeader>
                        <SheetTitle class="text-left">Menu</SheetTitle>
                    </SheetHeader>
                    <nav class="flex flex-col gap-1 px-2">
                        <SheetClose
                            v-for="item in navItems"
                            :key="item.title"
                            as-child
                        >
                            <Link
                                :href="item.href"
                                class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm font-medium text-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                            >
                                <component
                                    :is="item.icon"
                                    class="h-5 w-5 shrink-0"
                                />
                                {{ item.title }}
                            </Link>
                        </SheetClose>
                    </nav>
                </SheetContent>
            </Sheet>

            <div class="flex items-center gap-2">
                <UserMenu v-if="user" />
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </div>
        </div>

        <!-- Desktop navigation -->
        <NavigationMenu class="mx-auto hidden md:flex">
            <NavigationMenuList class="gap-8">
                <NavigationMenuItem v-for="item in navItems" :key="item.title">
                    <NavigationMenuLink
                        :class="
                            cn(
                                'group relative inline-flex h-9 w-max items-center justify-center px-0.5 py-2 text-sm font-medium',
                                'before:absolute before:inset-x-0 before:bottom-0 before:h-0.5 before:scale-x-0 before:bg-primary before:transition-transform',
                                'hover:text-accent-foreground hover:before:scale-x-100',
                                'focus:text-accent-foreground focus:outline-none focus:before:scale-x-100',
                            )
                        "
                    >
                        <Link
                            :href="item.href"
                            class="flex items-center gap-2.5"
                        >
                            <component
                                :is="item.icon"
                                class="h-5 w-5 shrink-0"
                            />
                            {{ item.title }}
                        </Link>
                    </NavigationMenuLink>
                </NavigationMenuItem>
                <UserMenu v-if="user" />
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </NavigationMenuList>
        </NavigationMenu>
    </div>
</template>
