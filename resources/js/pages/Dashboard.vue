<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ClockingDataTable from '@/components/ClockingDataTable.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Reactive data
const totalRecords = ref(0);
const todayEntries = ref(0);
const currentlyLoggedIn = ref(0);
const loggedInEmployees = ref([]);
const loading = ref(false);

// Methods
const fetchStats = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/dashboard-stats');
        totalRecords.value = response.data.total_records;
        todayEntries.value = response.data.today_entries;
        currentlyLoggedIn.value = response.data.currently_logged_in;
        loggedInEmployees.value = response.data.logged_in_employees;
    } catch (error) {
        console.error('Error fetching stats:', error);
    } finally {
        loading.value = false;
    }
};

// Lifecycle
onMounted(() => {
    fetchStats();
});

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Currently Logged In Card -->
                <Card class="border-l-4 border-l-red-500">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">
                            Currently Logged In
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">
                            {{ loading ? '...' : currentlyLoggedIn }}
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Employees without clock out
                        </p>
                        <!-- Show list of logged in employees -->
                        <div v-if="loggedInEmployees.length > 0" class="mt-3 space-y-1">
                            <div 
                                v-for="employee in loggedInEmployees.slice(0, 3)" 
                                :key="employee.AC_No"
                                class="flex items-center justify-between text-xs"
                            >
                                <span class="font-medium">{{ employee.Name }}</span>
                                <Badge variant="outline" class="text-xs">
                                    {{ employee.Clock_In }}
                                </Badge>
                            </div>
                            <div v-if="loggedInEmployees.length > 3" class="text-xs text-muted-foreground">
                                +{{ loggedInEmployees.length - 3 }} more
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Today's Entries Card -->
                <Card class="border-l-4 border-l-blue-500">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">
                            Today's Entries
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ loading ? '...' : todayEntries }}
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">
                            Clock entries for today
                        </p>
                    </CardContent>
                </Card>

                <!-- Total Records Card -->
                <Card class="border-l-4 border-l-green-500">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">
                            Total Records
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">
                            {{ loading ? '...' : totalRecords }}
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">
                            All time clocking records
                        </p>
                    </CardContent>
                </Card>
            </div>
       
            <div class="flex-1">
                <ClockingDataTable />
            </div>
        </div>
    </AppLayout>
</template>


