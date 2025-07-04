<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import InputError from '@/components/InputError.vue'
import FileUpload from '@/components/ui/file-upload/FileUpload.vue'
import { LoaderCircle } from 'lucide-vue-next'
import type { BreadcrumbItem } from '@/types'

// breadcrumbs
const breadcrumbs = ref<BreadcrumbItem[]>([
  { title: 'Forms', href: '/forms' },
  { title: 'Create', href: '/forms/create' },
])

// form state
const form = useForm<{ name: string; date: string; file: File | null }>({
  name: '',
  date: '',
  file: null,
})


// front-end only error slot (will override form.errors.file when set)
const fileError = ref<string | null>(null)

//download template
function downloadCsv() {
  // this will trigger a normal GET download
  window.location.href = route('forms.downloadCsv')
}
// file validation handler
function validateFile(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0] ?? form.file

  if (!file) {
    fileError.value = 'Please select a file.'
    form.file = null
    return
  }

  const name = file.name.toLowerCase()
  const parts = name.split('.')
  const ext = parts.pop()
  const allowed = ['csv', 'xls', 'xlsx']
  const doubleExt = /\.(?:csv|xls|xlsx)\.(?:csv|xls|xlsx)$/i

  if (!ext || !allowed.includes(ext) || doubleExt.test(name)) {
    fileError.value = 'Invalid file type. Only CSV, XLS, XLSX allowed.'
    form.file = null
  } else {
    fileError.value = null
    form.file = file
  }
}

// submission
function submit() {
  form.post(route('forms.store'), {
    forceFormData: true,
    onSuccess: () => { /** redirect happens automatically */ },
  })
}
</script>

<template>
  <Head title="Create Form" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="flex flex-col space-y-2">
        <h1 class="text-2xl font-semibold tracking-tight">Create New Form</h1>
        <p class="text-muted-foreground">Fill out the form below to submit your information.</p>
      </div>

      <Card class="max-w-2xl">
        <CardHeader>
          <div class="flex gap-2 justify-between item-base items-end">
            <div>
            <CardTitle>Form Submission</CardTitle>
            <CardDescription>
                Please provide your name, select a date, and optionally upload a file.
            </CardDescription>
            </div>
            <div >
            <Button
            variant="outline"
            size="sm"
            @click="downloadCsv"
            >Download Template</Button>
            </div>
          </div>
          <!-- Download button -->

        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Name -->
            <div class="space-y-2">
              <Label for="name">Name *</Label>
              <Input id="name" v-model="form.name" type="text" placeholder="Enter your full name" required :disabled="form.processing" />
              <InputError :message="form.errors.name" />
            </div>

            <!-- Date -->
            <div class="space-y-2">
              <Label for="date">Date *</Label>
              <Input id="date" v-model="form.date" type="date" required :disabled="form.processing" />
              <InputError :message="form.errors.date" />
            </div>

            <!-- File Upload -->
            <div class="space-y-2">
              <Label>File Upload (Mandatory)</Label>
              <FileUpload
                v-model="form.file"
                :error="fileError ?? form.errors.file"
                accept=".csv,.xls,.xlsx"
                @change="validateFile"
                :max-size="10"
                placeholder="Choose a file or drag and drop"
              />
              <p class="text-xs text-muted-foreground">
                Supported formats: Xls, Xlsx, Csv (Max Size 10MB)
              </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 pt-4">
              <Button type="submit" :disabled="form.processing" class="flex items-center gap-2">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{ form.processing ? 'Submitting...' : 'Submit Form' }}
              </Button>
              <Button type="button" variant="outline" :disabled="form.processing" @click="$inertia.visit(route('forms.index'))">
                Cancel
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
