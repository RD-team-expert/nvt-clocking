<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ClockingDataTable from '@/components/ClockingDataTable.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Employee {
  AC_No: string;
  Name: string;
  Clock_In: string;
  Clock_Out: string;
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
];

// Reactive stats
const loading = ref(false);
const totalRecords = ref(0);
const todayEntries = ref(0);

// Logged-In
const currentlyLoggedIn = ref(0);
const loggedInEmployees = ref<Employee[]>([]);

// Logged-Out
const currentlyLoggedOut = ref(0);
const loggedOutEmployees = ref<Employee[]>([]);

const fetchStats = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/dashboard-stats');
    totalRecords.value        = data.total_records;
    todayEntries.value        = data.today_entries;
    currentlyLoggedIn.value   = data.currently_logged_in;
    loggedInEmployees.value   = data.logged_in_employees;
    currentlyLoggedOut.value  = data.currently_logged_out;
    loggedOutEmployees.value  = data.logged_out_employees;
  } catch (error) {
    console.error('Error fetching stats:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchStats);
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Currently Logged In -->
        <Card class="border-l-4 border-l-red-500">
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium text-muted-foreground">
              Without Clock Out
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-red-600">
              {{ loading ? '...' : currentlyLoggedIn }}
            </div>
            <p class="text-xs text-muted-foreground mt-1">
              Employees without clock out:
            </p>
            <div v-if="loggedInEmployees.length" class="mt-3 space-y-1">
              <div
                v-for="emp in loggedInEmployees.slice(0, 3)"
                :key="emp.AC_No"
                class="flex items-center justify-between text-xs"
              >
                <span class="font-medium">{{ emp.Name }}</span>
                <Badge variant="outline" class="text-xs">
                  {{ emp.Clock_In }}
                </Badge>
              </div>
              <div
                v-if="loggedInEmployees.length > 3"
                class="text-xs text-muted-foreground"
              >
                +{{ loggedInEmployees.length - 3 }} more
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Currently Logged Out -->
        <Card class="border-l-4 border-l-blue-500">
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium text-muted-foreground">
              Without Clock In
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold text-blue-600">
              {{ loading ? '...' : currentlyLoggedOut }}
            </div>
            <p class="text-xs text-muted-foreground mt-1">
              Employees without clock In:
            </p>
            <div v-if="loggedOutEmployees.length" class="mt-3 space-y-1">
              <div
                v-for="emp in loggedOutEmployees.slice(0, 3)"
                :key="emp.AC_No"
                class="flex items-center justify-between text-xs"
              >
                <span class="font-medium">{{ emp.Name }}</span>
                <Badge variant="outline" class="text-xs">
                  {{ emp.Clock_Out }}
                </Badge>
              </div>
              <div
                v-if="loggedOutEmployees.length > 3"
                class="text-xs text-muted-foreground"
              >
                +{{ loggedOutEmployees.length - 3 }} more
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Total Records -->
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
