<script setup lang="ts">
const activities = [
    {
        id: 1,
        type: 'user',
        action: 'John Doe registered',
        timestamp: '2 hours ago',
        icon: 'user-add',
    },
    {
        id: 2,
        type: 'bank',
        action: 'First National Bank added',
        timestamp: '4 hours ago',
        icon: 'building-add',
    },
    {
        id: 3,
        type: 'user',
        action: 'Jane Smith updated profile',
        timestamp: '6 hours ago',
        icon: 'user-edit',
    },
    {
        id: 4,
        type: 'system',
        action: 'System backup completed',
        timestamp: '8 hours ago',
        icon: 'check',
    },
    {
        id: 5,
        type: 'bank',
        action: 'Chase Bank verified',
        timestamp: '1 day ago',
        icon: 'check',
    },
]

const getActivityColor = (type: string) => {
    switch (type) {
        case 'user':
            return 'bg-gradient-to-br from-blue-500/20 to-blue-600/20 text-blue-400'
        case 'bank':
            return 'bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 text-emerald-400'
        case 'system':
            return 'bg-gradient-to-br from-purple-500/20 to-purple-600/20 text-purple-400'
        default:
            return 'bg-gradient-to-br from-slate-500/20 to-slate-600/20 text-slate-400'
    }
}

const getActivityIcon = (icon: string) => {
    const icons: Record<string, string> = {
        'user-add': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>`,
        'building-add': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>`,
        'user-edit': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>`,
        check: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>`,
    }
    return icons[icon] || icons.check
}
</script>

<template>
    <div class="bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-xl p-6 border border-slate-700/50 backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-transparent bg-gradient-to-r from-slate-100 to-slate-300 bg-clip-text mb-4">Recent Activity</h3>
        <div class="space-y-4">
            <div
                v-for="activity in activities"
                :key="activity.id"
                class="flex items-start gap-4 pb-4 border-b border-slate-700/50 last:border-b-0 transition-colors hover:bg-slate-700/20 px-2 py-1 rounded"
            >
                <!-- Avatar -->
                <div
                    :class="['p-2 rounded-lg flex-shrink-0', getActivityColor(activity.type)]"
                    v-html="getActivityIcon(activity.icon)"
                />

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-100">{{ activity.action }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ activity.timestamp }}</p>
                </div>
            </div>
        </div>

        <!-- View All Link -->
        <button class="w-full mt-4 text-center text-sm font-medium text-blue-400 hover:text-blue-300 py-2 transition-colors">
            View all activity →
        </button>
    </div>
</template>
