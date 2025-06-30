<template>
  <div class="space-y-4">
    <!-- Filters -->
    <Card>
      <CardHeader>
        <CardTitle>Filters</CardTitle>
      </CardHeader>
      <CardContent>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <Label for="name-filter">Name</Label>
            <Input
              id="name-filter"
              v-model="filters.name"
              placeholder="Search by name..."
              @input="debouncedSearch"
            />
          </div>
          <div>
            <Label for="ac-no-filter">AC No</Label>
            <Input
              id="ac-no-filter"
              v-model="filters.ac_no"
              placeholder="Search by AC No..."
              @input="debouncedSearch"
            />
          </div>
          <div>
            <Label for="date-from">Date From</Label>
            <Input
              id="date-from"
              v-model="filters.date_from"
              type="date"
              @change="fetchData"
            />
          </div>
          <div>
            <Label for="date-to">Date To</Label>
            <Input
              id="date-to"
              v-model="filters.date_to"
              type="date"
              @change="fetchData"
            />
          </div>
        </div>
        <div class="flex justify-between items-center mt-4">
          <Button @click="clearFilters" variant="outline">Clear Filters</Button>
          <Button @click="openCreateDialog">Add New Record</Button>
        </div>
      </CardContent>
    </Card>

    <!-- Data Table -->
    <Card>
      <CardContent class="p-0">
        <div class="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Record ID</TableHead>
                <TableHead @click="sort('AC_No')" class="cursor-pointer hover:bg-muted/50">
                  AC No
                  <span v-if="sortBy === 'AC_No'" class="ml-1">
                    {{ sortOrder === 'asc' ? '↑' : '↓' }}
                  </span>
                </TableHead>
                <TableHead @click="sort('Name')" class="cursor-pointer hover:bg-muted/50">
                  Name
                  <span v-if="sortBy === 'Name'" class="ml-1">
                    {{ sortOrder === 'asc' ? '↑' : '↓' }}
                  </span>
                </TableHead>
                <TableHead @click="sort('Date')" class="cursor-pointer hover:bg-muted/50">
                  Date
                  <span v-if="sortBy === 'Date'" class="ml-1">
                    {{ sortOrder === 'asc' ? '↑' : '↓' }}
                  </span>
                </TableHead>
                <TableHead>Clock In</TableHead>
                <TableHead>Clock Out</TableHead>

                <TableHead class="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="loading">
                <TableCell colspan="7" class="text-center py-8">
                  <div class="flex items-center justify-center space-x-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                    <span>Loading...</span>
                  </div>
                </TableCell>
              </TableRow>
              <TableRow v-else-if="data.length === 0">
                <TableCell colspan="7" class="text-center py-8 text-muted-foreground">
                  No records found
                </TableCell>
              </TableRow>
              <TableRow v-else v-for="record in data" :key="record.id">
                <TableCell>{{ record.id || '-' }}</TableCell>
                <TableCell>{{ record.AC_No }}</TableCell>
                <TableCell>{{ record.Name }}</TableCell>
                <TableCell>{{ formatDate(record.Date) }}</TableCell>
                <TableCell>{{ record.Clock_In || '-' }}</TableCell>
                <TableCell>{{ record.Clock_Out || '-' }}</TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end space-x-2">
                    <Button @click="openEditDialog(record)" size="sm" variant="outline">
                      Edit
                    </Button>
                    <Button @click="deleteRecord(record.id)" size="sm" variant="destructive">
                      Delete
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="flex items-center justify-between px-4 py-4 border-t">
          <div class="text-sm text-muted-foreground">
            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
          </div>
          <div class="flex items-center space-x-2">
            <Button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page <= 1"
              size="sm"
              variant="outline"
            >
              Previous
            </Button>
            <span class="text-sm">
              Page {{ pagination.current_page }} of {{ pagination.last_page }}
            </span>
            <Button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page >= pagination.last_page"
              size="sm"
              variant="outline"
            >
              Next
            </Button>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Edit/Create Dialog -->
    <Dialog v-model:open="dialogOpen">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>{{ editingRecord ? 'Edit Record' : 'Create New Record' }}</DialogTitle>
        </DialogHeader>
        <form @submit.prevent="saveRecord" class="space-y-4">
          <!-- In the dialog template -->
          <div>
            <Label for="ac-no">AC No</Label>
            <Input
              id="ac-no"
              v-model.number="form.AC_No"
              type="number"
              placeholder="Enter AC No..."
              required
            />
          </div>

          <div v-if="!editingRecord">
            <Label for="entry-id">Entry ID</Label>
            <Input
                id="entry-id"
                v-model.number="form.Entry_ID"
                type="number"
                placeholder="Enter Entry ID..."
            />
          </div>

          <div>
            <Label for="clock-in">Clock In</Label>
            <Input
              id="clock-in"
              v-model="form.Clock_In"
              type="time"
              step="1"
            />
          </div>

          <div>
            <Label for="clock-out">Clock Out</Label>
            <Input
              id="clock-out"
              v-model="form.Clock_Out"
              type="time"
              step="1"
            />
          </div>
          <div>
            <Label for="name">Name *</Label>
            <Input id="name" v-model="form.Name" required />
          </div>
          <div>
            <Label for="date">Date *</Label>
            <Input id="date" v-model="form.Date" type="date" required />
          </div>
          <DialogFooter>
            <Button type="button" @click="dialogOpen = false" variant="outline">
              Cancel
            </Button>
            <Button type="submit" :disabled="saving">
              {{ saving ? 'Saving...' : (editingRecord ? 'Update' : 'Create') }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { debounce } from 'lodash-es'
import axios from 'axios'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table'

interface ClockingRecord {
  id: number
  AC_No: string
  Name: string
  Date: string
  Clock_In: string | null
  Clock_Out: string | null
  Entry_ID: string | null
  created_at: string
  updated_at: string
}

interface PaginationData {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

const data = ref<ClockingRecord[]>([])
const loading = ref(false)
const saving = ref(false)
const dialogOpen = ref(false)
const editingRecord = ref<ClockingRecord | null>(null)

const pagination = ref<PaginationData>({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0
})

const filters = reactive({
  name: '',
  ac_no: '',
  date_from: '',
  date_to: ''
})

const sortBy = ref('Date')
const sortOrder = ref('desc')

const form = reactive({
  AC_No: '',
  Name: '',
  Date: '',
  Clock_In: '',
  Clock_Out: '',
  Entry_ID: ''
})

const fetchData = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: pagination.value.per_page,
      sort_by: sortBy.value,
      sort_order: sortOrder.value,
      ...filters
    }

    const response = await axios.get('/api/clocking', { params })
    data.value = response.data.data
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to
    }
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    loading.value = false
  }
}

const debouncedSearch = debounce(() => {
  fetchData(1)
}, 300)

const sort = (column: string) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortOrder.value = 'asc'
  }
  fetchData(1)
}

const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchData(page)
  }
}

const clearFilters = () => {
  Object.assign(filters, {
    name: '',
    ac_no: '',
    date_from: '',
    date_to: ''
  })
  fetchData(1)
}

const openCreateDialog = () => {
  editingRecord.value = null
  Object.assign(form, {
    AC_No: '',
    Name: '',
    Date: '',
    Clock_In: '',
    Clock_Out: '',
    Entry_ID: ''
  })
  dialogOpen.value = true
}

const openEditDialog = (record: ClockingRecord) => {
  editingRecord.value = record
  Object.assign(form, {
    id: record.id || '',
    AC_No: record.AC_No,
    Name: record.Name,
    Date: record.Date,
    Clock_In: record.Clock_In || '',
    Clock_Out: record.Clock_Out || '',
    Entry_ID: record.Entry_ID,
  })
  dialogOpen.value = true
}

const saveRecord = async () => {
  saving.value = true
  try {
    // Ensure proper data formatting
    const formData = {
      AC_No: parseInt(form.AC_No) || 0,
      Name: form.Name,
      Date: form.Date,
      Clock_In: form.Clock_In || null,
      Clock_Out: form.Clock_Out || null,
      Entry_ID: parseInt(form.Entry_ID) || null,
    }

    if (editingRecord.value) {
      const response = await axios.put(`/api/clocking/${editingRecord.value.id}`, formData)
      console.log('Update response:', response.data)
    } else {
      const response = await axios.post('/api/clocking', formData)
      console.log('Create response:', response.data)
    }
    dialogOpen.value = false
    fetchData(pagination.value.current_page)
  } catch (error) {
    console.error('Error saving record:', error)
    // Add better error handling
    if (error.response) {
      console.error('Response data:', error.response.data)
      console.error('Response status:', error.response.status)
    }
  } finally {
    saving.value = false
  }
}

const deleteRecord = async (id: number) => {
  if (confirm('Are you sure you want to delete this record?')) {
    try {
      await axios.delete(`/api/clocking/${id}`)
      fetchData(pagination.value.current_page)
    } catch (error) {
      console.error('Error deleting record:', error)
    }
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

onMounted(() => {
  fetchData()
})
</script>
