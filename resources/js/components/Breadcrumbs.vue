<script setup lang="ts">
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Link } from '@inertiajs/vue3';
import type { HTMLAttributes } from 'vue'


interface BreadcrumbItemType {
    title: string;
    href?: string;
}

const props = defineProps<{
    breadcrumbs: BreadcrumbItemType[];
    class?: HTMLAttributes['class']
}>();
</script>

<template>
    <Breadcrumb
        :class="props.class"
    >
        <BreadcrumbList>
            <template v-for="(item, index) in props.breadcrumbs" :key="index">
                <BreadcrumbItem>
                    <template v-if="index === props.breadcrumbs.length - 1">
                        <BreadcrumbPage class="font-bold text-red-800">{{ item.title }}</BreadcrumbPage>
                    </template>
                    <template v-else>
                        <BreadcrumbLink as-child>
                            <Link 
                                class="text-gray-800 hover:text-red-800"
                                :href="item.href ?? '#'">{{
                                item.title
                            }}</Link>
                        </BreadcrumbLink>
                    </template>
                </BreadcrumbItem>
                <BreadcrumbSeparator v-if="index !== props.breadcrumbs.length - 1" />
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
